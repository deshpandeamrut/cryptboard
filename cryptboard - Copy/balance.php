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
            $exchange['currentPrice'] = $value->buy;
            $currentValue = $value->sell * $exchange['bits'];
            $exchange['currentValue'] = floor($currentValue);
            $difference = $currentValue - $exchange['investment'];
            $exchange['difference'] = floor($difference);
            $outputData['zebpay'][] = $exchange;
        }
    }
}
$coins=['btc','bch','eth','xrp','ltc'];
foreach ($coins as $coin) {
    if(!isset($investment_details['koinex-'.$coin])){
        // echo "No $coin";
        continue;
    }
    $zebpayInvestments = $investment_details['koinex-'.$coin];
    foreach ($zebpayInvestments as $e) {
        // var_dump($e);
       
        $lines = explode(",", $e);
        $coinType['investment'] = $lines[0];
        $coinType['price'] = $lines[1];
         if(array_key_exists('koinex', $outputData)){
         $exchange = $outputData['koinex'];
        }else{
            $exchange['investment_sum']=0;
            $exchange['name'] = "Koinex";    
        }
        $exchange['investment_sum']+=$coinType['investment'];
        if(isset($lines[2])){
            $coinType['doi'] = $lines[2];
        }else{
            $coinType['doi'] = "NA";
        }
        $coinType['bits'] = $coinType['investment']/$coinType['price'];
        foreach ($responseData as $key => $value) {
            $name = $value->name;
            if(strtolower($name)=="koinex"){
                $coinType['name'] = $coin;
                $coinType['currentPrice'] = $value->sell;
                $currentValue = $value->$coin->buy * $coinType['bits'];
                $coinType['currentValue'] = floor($currentValue);
                $difference = $currentValue - $coinType['investment'];                
                $coinType['difference'] = floor($difference);
                $exchange['coins'][] = $coinType; 
                
                // $outputData[] = $exchange;
            }
        }
    }
    $outputData['koinex'] = $exchange;
}
print json_encode($outputData);

?>