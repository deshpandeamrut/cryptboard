<?php 
/*$properties = parse_ini_file("./php.ini", "true") or die("Could not find ini file");
$exhangesPropertyFile = $properties['exchanges'];
$exchanges = [];
foreach ($exhangesPropertyFile as $e) {
    $exchange['name'] = explode(",", $e)[0];
    $exchange['url'] = explode(",", $e)[1];
    $exchanges[] = $exchange;
}*/

include 'corsHeader.php';
$coinType = 'btc';
if(isset($_GET['coinType'])){
    $coinType = $_GET['coinType'];
}
getHourlyGraphData($coinType);


function getHourlyGraphData($coinType){
 date_default_timezone_set('Asia/Kolkata');
 $date = date('m_d_Y', time());
 $dir = "data/";
 if($coinType=="btctwo"){
     $fileName1 =$dir . $date . "_"."zebpay_price.txt";
     $fileName2=$dir . $date . "_"."koinex_price.txt";

     $visitsData = [];

     if (file_exists($fileName1)) {
        $fileData1 = file_get_contents($fileName1);
        $fileData1 = explode("\n", $fileData1);
        if (file_exists($fileName2)) {
            $fileData2 = file_get_contents($fileName2);
        }
        $fileData2 = explode("\n", $fileData2);
        for ($index = 0; $index < count($fileData1); $index++) {
            $line = $fileData1[$index];
            $line2 = $fileData2[$index];
            if (strlen(trim($line)) < 1) {
                continue;
            }
            $time = explode(",", $line)[0];
            $time = explode(" ", $time)[1];
            $price = explode(",", $line)[1];

            $time2 = explode(",", $line2)[0];
            $time2 = explode(" ", $time2)[1];
            if(trim($price)!="" && $price!=0){
                if($time==$time2){
                    $price2 = explode(",", $line2)[1];
                    if($price2!=0){
                        $price= $price.",".$price2;
                        $visitsData[$time] = $price;
                    }
                }

            }

        }
    }
// $time_stamp = date('m/d/Y h:i:s a', time());
//var_dump($visitsData);
    $data = array(
        array("date", "zebpay_price,koinex_price")
    );
    ksort($visitsData);
}else if($coinType=="btc"){
    /* GLOBAL PRICE */
    $fileName1 =$dir . $date . "_"."zebpay_price.txt";
    $fileName2=$dir . $date . "_"."koinex_price.txt";
    $globalFile=$dir . $date . "_"."global_price.txt";
    $visitsData = [];

    if (file_exists($fileName1)) {
        $fileData1 = file_get_contents($fileName1);
        $fileData1 = explode("\n", $fileData1);
        if (file_exists($fileName2)) {
            $fileData2 = file_get_contents($fileName2);
        }
        if(file_exists($globalFile)){
            $globalFileData = file_get_contents($globalFile);       
        }
        $fileData2 = explode("\n", $fileData2);
        $globalFileData = explode("\n", $globalFileData);
        $price3cache =0;
        for ($index = 0; $index < count($fileData1); $index++) {
            $line = $fileData1[$index];
            
            
            if (strlen(trim($line)) < 1) {
                continue;
            }
            $time = explode(",", $line)[0];
            $time = explode(" ", $time)[1];
            $price = explode(",", $line)[1];
            if(trim($price)!="" && $price!=0){
                $visitsData[$time] = $price;
            }
            /* Handle Koinex data */
            $line2 = $fileData2[$index];
            $time2 = explode(",", $line2)[0];
            $time2 = explode(" ", $time2)[1];
            $price2 = explode(",", $line2)[1];
            if(array_key_exists($time2, $visitsData)){
                if(trim($price2)=="" || $price2==0){
                    $visitsData[$time2].=",".$price; 
                }else{
                    $visitsData[$time2].=",".$price2; 
                }
            }else{
                 $visitsData[$time2].=",".$price2; 
            }
            /* Handle global price */
            if(isset($globalFileData[$index])){
                $line3 = $globalFileData[$index];
                $time3 = explode(",", $line3)[0];
                $time3 = explode(" ", $time3)[1];
                $price3 = explode(",", $line3)[2];
/*
                $line3Old ="";
                $time3Old="";
                $price3Old=0;
                if(isset($globalFileData[$index-1])){
                    $line3Old = $globalFileData[$index-1];
                    $time3Old = explode(",", $line3Old)[0];
                    $time3Old = explode(" ", $time3Old)[1];
                    $price3Old = explode(",", $line3Old)[2];
                }*/
                if(array_key_exists($time3, $visitsData)){
                    if((trim($price3)=="" || $price3==0 || $price3<9999) && $price3cache!=0){
                        $visitsData[$time2].=",".$price3cache; 
                    }else{
                        $visitsData[$time2].=",".$price3; 
                        $price3cache = $price3;
                    }
                }else{
                     if((trim($price3)=="" || $price3==0) && $price3cache!=0){
                        $visitsData[$time2].=",".$price3cache; 
                    }
                }
            }
        }
    }
    $data = array(
        array("date", "zebpay_price,koinex_price,global_price")
    );
    ksort($visitsData);
}else{
    $koinex_file=$dir . $date . "_"."koinex_price.txt";
    $visitsData = [];

    if (file_exists($koinex_file)) {
        $fileData1 = file_get_contents($koinex_file);
        $fileData1 = explode("\n", $fileData1);
        //btc,bch,eth,xrp
        $coinIndex = 2;
        if($coinType=="bch"){
            $coinIndex = 2;
        }else if($coinType=="eth"){
            $coinIndex = 3;
        }else if($coinType=="xrp"){
            $coinIndex = 4;
        }else{
            echo "Coin Type Not supported.";
            return;
        }
        for ($index = 0; $index < count($fileData1); $index++) {
            $line = $fileData1[$index];
            if (strlen(trim($line)) < 1) {
                continue;
            }
            // echo $line;
            $lineFields = explode(",", $line);
            $time = $lineFields[0];
            // echo $time;
            $time = explode(" ", $time)[1];

            $price = $lineFields[$coinIndex];
            // if(trim($price)!=""{
            if($price!=0 && $price!=""){
                $visitsData[$time] = $price;
            }
        }
    }
    $data = array(
        array("date", $coinType."_Price")
    );
    ksort($visitsData);
}
if (count($visitsData) > 0) {
    foreach ($visitsData as $key => $value) {
        $tempArray = array($key, $value);
        array_push($data, $tempArray);
    }
    echo json_encode($data);
}
}

function getTodaysVists() {
    date_default_timezone_set('Asia/Kolkata');
    $date = date('m_d_Y', time());
    $dir = "/data/";
    $todayFileName = $dir."_" . $date . "_"."koinex_price.txt";
    if (!file_exists($todayFileName)) {
        // do nothing
    } else {
        $recordsArray = [];
        $todaysFileData = file_get_contents($todayFileName);
        $todaysFileData = explode("\n", $todaysFileData);
        foreach ($todaysFileData as $value) {
            $client_id = explode(",", $value)[0];
            array_push($recordsArray, $client_id);
        }
        $visits['uniquevists'] = count(array_unique($recordsArray)) - 1;
        $visits['totalvists'] = count($recordsArray) - 1;
    }
    return $visits;
}
?>