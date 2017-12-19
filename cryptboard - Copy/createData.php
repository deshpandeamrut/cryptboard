<?php 

if(isset($_SERVER['SERVER_NAME']) && $_SERVER['SERVER_NAME']=="localhost"){
	$url = "http://localhost/cryptboard/callExchanges.php";	
}else{
	$url = "http://nammabagalkot.in/cryptboard/callExchanges.php";
}

date_default_timezone_set('Asia/Kolkata');
$date = date('m_d_Y', time());
$time_stamp = date('m/d/Y H:i:s', time());

$responseData = file_get_contents($url);
$responseData = json_decode($responseData);
// var_dump($responseData);
foreach ($responseData as $key => $value) {
	$fileToday = "data/" . $date ."_". $value->name."_price.txt";
	$writeData = $time_stamp . "," . $value->buy . "\n";
	if (file_exists($fileToday)) {
		file_put_contents($fileToday, $writeData, FILE_APPEND);
	} else {
		$myfile = fopen($fileToday, "w") or die("Unable to open file!");
		fwrite($myfile, $writeData);
		fclose($myfile);
	}
}
?>