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
$fileName = "./data/".$name."_investments.txt";
if(!file_exists($fileName)){
    echo "Invalid Credentials";
    exit();
}

$investment_details = file_get_contents($fileName, "true") or die("Could not find txt file");
if(strlen(trim($investment_details))==0){
    echo "No Data";
    exit();
}
$investment_details = explode("\n", $investment_details);
$profile_details = $investment_details[0];
$profile_details = explode(",", $profile_details);
$profile_email = $profile_details[1];
$profile_pin = $profile_details[2];
// var_dump($profile_details);
if(trim($profile_pin)!=$pin){
    echo "Invalid Credentials";
    exit();
}
/*
* Call exchanges
*/
$responseData = file_get_contents($url);
$responseData = json_decode($responseData);

/* End call */
$outputData= [];
$outputData['zebpay']['total_investments']=0;
$outputData['zebpay']['difference']=0;
$outputData['zebpay']['currentValue']=0;
$outputData['koinex']['total_investments']=0;
$outputData['koinex']['difference']=0;
$outputData['koinex']['currentValue']=0;
$outputData['koinex']['btc_total_investments']=0;
$outputData['koinex']['btc_difference']=0;
$outputData['koinex']['btc_currentValue']=0;
$outputData['koinex']['bch_total_investments']=0;
$outputData['koinex']['bch_difference']=0;
$outputData['koinex']['bch_currentValue']=0;
$outputData['koinex']['eth_total_investments']=0;
$outputData['koinex']['eth_difference']=0;
$outputData['koinex']['eth_currentValue']=0;
$outputData['koinex']['ltc_total_investments']=0;
$outputData['koinex']['ltc_difference']=0;
$outputData['koinex']['ltc_currentValue']=0;
$outputData['koinex']['xrp_total_investments']=0;
$outputData['koinex']['xrp_difference']=0;
$outputData['koinex']['xrp_currentValue']=0;
for ($i=1; $i<count($investment_details) ; $i++) {
    if(trim($investment_details[$i])==""){
        continue;
    }
    $investment = explode(",",$investment_details[$i]);
    $row['name'] = $investment[0];
    $row['investment'] = $investment[1];
    $row['price'] = $investment[2];
    if(isset($investment[3])){
        $row['doi'] = $investment[3];
    }else{
        $row['doi'] = "NA";
    }
    $row['bits'] = $row['investment']/$row['price'];
    // $row=[];
    if(stripos($row['name'], "zebpay")>-1){
        $row['exchange_name'] = "ZebPay";
        foreach ($responseData as $key => $value) {
            $name = $value->name;
            if(strtolower($name)=="zebpay"){
                $row['currentPrice'] = $value->buy;
                $currentValue = $value->sell * $row['bits'];
                $row['currentValue'] = floor($currentValue);
                $difference = $currentValue - $row['investment'];
                $row['difference'] = floor($difference);
                /* Add to output data */
                $outputData['zebpay']['total_investments']+=$row['investment'];
                $outputData['zebpay']['difference']+=$row['difference'];
                $outputData['zebpay']['currentValue']+=$row['currentValue'];
                $outputData['zebpay']['investments'][] = $row;
                /* end */
                break;
            }
        }
    }else if(stripos($row['name'], "koinex")>-1){
        $coins=['btc','bch','eth','xrp','ltc'];
        $row['exchange_name'] = "Koinex";
        $coinType = explode("-", $row['name'])[1];
        if(in_array($coinType, $coins)){
            foreach ($responseData as $key => $value) {
            $name = $value->name;
            if(strtolower($name)=="koinex"){

                $row['currentPrice'] = $value->$coinType->buy;
                $currentValue = $value->$coinType->buy * $row['bits'];
                $row['currentValue'] = floor($currentValue);
                $difference = $currentValue - $row['investment'];
                $row['difference'] = floor($difference);
                /* Add to output data */
                $outputData['koinex']['total_investments']+=$row['investment'];
                $outputData['koinex']['difference']+=$row['difference'];
                $outputData['koinex']['currentValue']+=$row['currentValue'];
                /* coin specific */
                $outputData['koinex'][$coinType.'_total_investments']+=$row['investment'];
                $outputData['koinex'][$coinType.'_difference']+=$row['difference'];
                $outputData['koinex'][$coinType.'_currentValue']+=$row['currentValue'];

                $outputData['koinex']['investments'][$coinType][] = $row;
                /* end */
                break;
            }
        }


        }
        
        
    }
}

print json_encode($outputData);





/*$profileDetails =  $investment_details['profile'];
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
$coins=['btc','bch','eth'];
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
print json_encode($outputData);*/

?>