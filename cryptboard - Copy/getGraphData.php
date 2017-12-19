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

getHourlyGraphData();


function getHourlyGraphData(){
   date_default_timezone_set('Asia/Kolkata');
   $date = date('m_d_Y', time());
   $dir = "data/";
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