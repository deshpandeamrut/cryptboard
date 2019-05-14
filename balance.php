<?php

if(isset($_SERVER['SERVER_NAME']) && $_SERVER['SERVER_NAME']=="localhost"){
    $url = "http://localhost/cryptboard/callExchanges.php"; 
}else{
    $url = "http://nammabagalkot.in/cryptboard/callExchanges.php";
}
$name = "";
$pin="";
if(!isset($_GET['name']) || !isset($_GET['pin'])){
    exit();
}else{
    $name = $_GET['name'];
    $pin =$_GET['pin'];

}
$responseData = file_get_contents($url);
$responseData = json_decode($responseData);

$investment_details = parse_ini_file("./data/".$name."_investments.ini", "true") or die("Could not find ini file");
$profileDetails =  $investment_details['profile'];
$profilePin = explode(",",$profileDetails[1])[1];
if($profilePin!=trim($pin)){
    echo "Invalid Credentials";
    exit();
}
$zebpayInvestments = $investment_details['zebpay'];
$exchanges = [];
$investmentData = [];
$outputData = [];
foreach ($zebpayInvestments as $e) {
    $lines = explode(",", $e);
    $exchange['investment'] = $lines[0];
    $exchange['price'] = $lines[1];
    if(isset($lines[2])){
        $exchange['doi'] = $lines[2];
    }else{
        $exchange['doi'] = "NA";
    }
    $exchange['price'] = $lines[1];
    $exchange['bits'] = $exchange['investment']/$exchange['price'];
    foreach ($responseData as $key => $value) {
        $name = $value->name;
        if(strtolower($name)=="zebpay"){
            $exchange['name'] = "Zebpay";
            $exchange['currentPrice'] = $value->sell;
            $currentValue = $value->sell * $exchange['bits'];
            $exchange['currentValue'] = floor($currentValue);
            $difference = $currentValue - $exchange['investment'];
            $exchange['difference'] = floor($difference);
            $outputData[] = $exchange;
        }
    }
}

$zebpayInvestments = $investment_details['koinex'];
foreach ($zebpayInvestments as $e) {
    $lines = explode(",", $e);
    $exchange['investment'] = $lines[0];
    $exchange['price'] = $lines[1];
    if(isset($lines[2])){
        $exchange['doi'] = $lines[2];
    }else{
        $exchange['doi'] = "NA";
    }
    $exchange['bits'] = $exchange['investment']/$exchange['price'];
    foreach ($responseData as $key => $value) {
        $name = $value->name;
        if(strtolower($name)=="koinex"){

            $exchange['name'] = "Koinex";
             $exchange['currentPrice'] = $value->sell;
            $currentValue = $value->buy * $exchange['bits'];
            $exchange['currentValue'] = floor($currentValue);
            $difference = $currentValue - $exchange['investment'];
            $exchange['difference'] = floor($difference);
            $outputData[] = $exchange;
        }
    }
}

print json_encode($outputData);

?>