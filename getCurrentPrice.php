<?php 
/**
* Calls specifies apis and returns data for frontend
*/
if(isset($_SERVER['SERVER_NAME']) && $_SERVER['SERVER_NAME']=="localhost"){
    $url = "http://localhost/cryptboard/callExchanges.php"; 
}else{
    $url = "http://nammabagalkot.in/cryptboard/callExchanges.php";
}


$responseData = file_get_contents($url);
$responseData = json_decode($responseData);
$lastFetchData = getLastPrice();
foreach ($responseData as $key => $value) {
    $name = $value->name;
    if(array_key_exists($name, $lastFetchData)){
        $difference = $value->buy - $lastFetchData[$name];
       //  echo $value->buy;
       // echo  "-".$lastFetchData[$name]."<br/>";
        $value->lastPrice = $lastFetchData[$name];
        $value->difference = $difference;
    }
}

print json_encode($responseData);


function getLastPrice(){
    $properties = parse_ini_file("./php.ini", "true") or die("Could not find ini file");
    $exhangesPropertyFile = $properties['exchanges'];
    $exchanges = [];
    foreach ($exhangesPropertyFile as $e) {
        $exchanges[] = explode(",", $e)[0];
    }
    date_default_timezone_set('Asia/Kolkata');
    $date = date('m_d_Y', time());
    $time_stamp = date('m/d/Y H:i:s', time());
    $lastFetchData = [];
    foreach ($exchanges as $exchange) {
        $fileToday = "data/" . $date ."_". $exchange."_price.txt";
        if (file_exists($fileToday)) {
            $fileData = file_get_contents($fileToday);
            $fileDataLines = explode("\n", $fileData);
            $lastLine = $fileDataLines[count($fileDataLines)-2];
            $lastPrice = explode(",", $lastLine)[1];
            $lastFetchData[$exchange] = $lastPrice;
        }
    }
    return $lastFetchData;
}
?>