
<?php 
/**
* Calls the data that was captured in json files and recomends a coin that users are interested to buy.
*/


if(isset($_GET['notification_type'])){
	$message = "";
	if($_GET['notification_type']=="change"){
		$stats = getStats();
		setlocale(LC_MONETARY, 'en_IN');
		$price = 0;
		if(isset($_SERVER['SERVER_NAME']) && $_SERVER['SERVER_NAME']=="localhost"){
			$price = $stats['max_change_percentage']['currentPrice'];	
		}else{
			$price = money_format('%!i', $stats['max_change_percentage']['currentPrice']);
		}
		$message = "You might be interested to trade ". $stats['max_change_percentage']['coinName'] ." coin!\n"
		."It has highest change percentage of ".$stats['max_change_percentage']['change']. "% where as least changed coin is ".$stats['min_change_percentage']['coinName']." with ".$stats['min_change_percentage']['change'] ."% change\n";
		
	}else if($_GET['notification_type']=="amount"){
		$stats = getStats();
		setlocale(LC_MONETARY, 'en_IN');
		$price = 0;
		$amountTraded = 0;
		$minAmountTraded = 0;
		if(isset($_SERVER['SERVER_NAME']) && $_SERVER['SERVER_NAME']=="localhost"){
			$price = $stats['max_amount_traded']['currentPrice'];	
			$amountTraded = $stats['max_amount_traded']['amountTraded'];
			$minAmountTraded = $stats['min_amount_traded']['amountTraded'];
		}else{
			$price = money_format('%!i', $stats['max_amount_traded']['currentPrice']);
			$amountTraded = money_format('%!i', $stats['max_amount_traded']['amountTraded']);
			$minAmountTraded = money_format('%!i', $stats['min_amount_traded']['amountTraded']);
		}
		$message = "Highest Transacted Coin - You might be interested to trade ". $stats['max_amount_traded']['coinName'] ." coin!\n"
		."Total amount traded for this coin is Rs.".$amountTraded. " where as\nLeast transacted coin is ".$stats['min_amount_traded']['coinName']." with value of Rs.".$minAmountTraded ."\n";
	}
	if(isset($_SERVER['SERVER_NAME']) && $_SERVER['SERVER_NAME']=="localhost"){
		echo $message;
	}else{
		sendMessage($message);
	}
}







function getStats(){
	$properties = parse_ini_file("./php.ini", "true") or die("Could not find ini file");
	$exhangesPropertyFile = $properties['exchanges'];
	$exchanges = [];
	foreach ($exhangesPropertyFile as $e) {
		$exchange['name'] = explode(",", $e)[0];
		$exchange['url'] = explode(",", $e)[1];
		$exchanges[] = $exchange;
	}
	$reponseData = [];
	foreach ($exchanges as $exchange) {
		$data = doCurl($exchange['url']);
		$response['name'] = $exchange['name'];
		if(strtolower($response['name'])== "zebpay"){
      //do nothing as of now
		}elseif(strtolower($response['name']) == "koinex"){
			try{
				$maxChange = 0;
				$minChange = 99999999999;
				$maxVolume = 0;
				$maxAmountTraded = 0;
				$minAmountTraded = 99999999999;
				$maxChangeCoin;
				$minChangeCoin;
				$maxAmountTradedCoin;
				$minAmountTradedCoin;
				foreach ($data->stats->inr as $value) {
					$change = $value->per_change;
					$volume = $value->vol_24hrs;
					$currentPrice = $value->last_traded_price;
					$amountTraded = $volume*$currentPrice;
				if($change>$maxChange){//calculate max change for a coin
					$maxChange = $change;
					$maxChangeCoin = $value;
				}
				if($change<$minChange){//calculate min change for a coin
					$minChange = $change;
					$minChangeCoin = $value;
				}
				if($amountTraded>$maxAmountTraded){//calculate max amount traded
					$maxAmountTraded = $amountTraded;
					$maxAmountTradedCoin = $value;
				}
				if($amountTraded<$minAmountTraded){//calculate min amount traded
					$minAmountTraded = $amountTraded;
					$minAmountTradedCoin = $value;
				}

			}
			/*
			Indian Number System
				setlocale(LC_MONETARY, 'en_IN');
				$amount = money_format('%!i', $amount);
			*/
				$responses= [];
				$response = [];
				if($maxChange>0){
					$response['coinName'] = $maxChangeCoin->currency_short_form;
					$response['change'] = floor($maxChange);
					$response['currentPrice'] = $maxChangeCoin->last_traded_price;
					$responses['max_change_percentage'] = $response;
				}
				$response = [];
				if($minChange<99999999999){
					$response['coinName'] = $minChangeCoin->currency_short_form;
					$response['change'] = floor($minChange);
					$response['currentPrice'] = $minChangeCoin->last_traded_price;
					$responses['min_change_percentage'] = $response;
				}
				$response = [];
				if($maxAmountTraded>0){
					$response['coinName'] = $maxAmountTradedCoin->currency_short_form;
					$response['currentPrice'] = $maxAmountTradedCoin->last_traded_price;
					$response['amountTraded'] = $maxAmountTraded;
					$responses['max_amount_traded'] = $response;
				}
				$response = [];
				if($minAmountTraded<99999999999){
					$response['coinName'] = $minAmountTradedCoin->currency_short_form;
					$response['currentPrice'] = $minAmountTradedCoin->last_traded_price;
					$response['amountTraded'] = $minAmountTraded;
					$responses['min_amount_traded'] = $response;
				}
				return $responses;
			}catch(Exception $e){
				echo "koinex issue";
			}
		}
	}
}
function sendMessage($message){

	if($message==""){
		echo "No Message!";
		exit();
	}
    // exit();
	$content = array(
		"en" => $message
	);

	$fields = array(
		'app_id' => "7334a009-9ce6-491b-9e3b-223d839d6c65",
        // 'include_player_ids' => array("12722777-ff4b-41b4-a884-8cd3bac9d44e"),
		'included_segments' => array('All'),
		'data' => array("foo" => "bar"),
		'chrome_web_icon' =>"http://nammabagalkot.in/cryptboard/img/push_icob.png",
		'contents' => $content,
		'url' => "http://nammabagalkot.in/cryptboard"
	);


	$fields = json_encode($fields);
	print("\nJSON sent:\n");
	print($fields);

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8',
		'Authorization: Basic YzE2NTRmYTktZGE3MC00NWY2LThiYjAtYTA1MzdkNTA3NTRh'));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($ch, CURLOPT_HEADER, FALSE);
	curl_setopt($ch, CURLOPT_POST, TRUE);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);    

	$response = curl_exec($ch);
	curl_close($ch);

	return $response;
}

// create a new cURL resource
function doCurl($url){
	$ch=curl_init();
	curl_setopt($ch,CURLOPT_URL,$url);
	curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,2);
	curl_setopt($ch, CURLOPT_USERAGENT,'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.17 (KHTML, like Gecko) Chrome/24.0.1312.52 Safari/537.17');
	curl_setopt($ch, CURLOPT_AUTOREFERER, true); 

	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch, CURLOPT_VERBOSE, 1);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

	curl_setopt($ch, CURLOPT_ENCODING , "");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

	$buffer = curl_exec($ch);
	if (curl_errno($ch)) { 
		print curl_error($ch); 
	} 
	curl_close($ch); 

	if (empty($buffer)){
		print "Nothing returned from url.<p>";
	}
	else{
		return json_decode($buffer);
	}
}
?>