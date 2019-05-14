

<?PHP
function sendMessage(){
    $url = "http://nammabagalkot.in/cryptboard/callExchanges.php";
    $responseData = file_get_contents($url);
    $responseData = json_decode($responseData);
// $lastFetchData = getLastPrice();
    $message = "";
    foreach ($responseData as $key => $value) {
        $name = $value->name;
        if($name=="koinex"){
            // $message.="Koinex\n";
            $message.="BTC".": ".$value->btc->buy."\n";
            $message.="BCH".": ".$value->bch->buy."\n";
            $message.="ETH".": ".$value->eth->buy."\n";
            $message.="XRP".": ".$value->xrp->buy."\n";
            // $message.="LTC".":".$value->ltc->buy."\n";
        }else{
            // $message.=$name.": ".$value->buy."\n";
        }
    // if(array_key_exists($name, $lastFetchData)){
    //     $difference = $value->buy - $lastFetchData[$name];
    //    //  echo $value->buy;
    //    // echo  "-".$lastFetchData[$name]."<br/>";
    //     $value->lastPrice = $lastFetchData[$name];
    //     $value->difference = $difference;
    //     $message.=$name.":".$value->buy."";
    // }
    }
    var_dump($message);
    $content = array(
        "en" => $message
    );

    $fields = array(
        'app_id' => "7334a009-9ce6-491b-9e3b-223d839d6c65",
        'included_segments' => array('All'),
        'filters' => array(array("field" => "tag", "key" => "periodic", "relation" => "not_exists")),
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


$response = sendMessage();
$return["allresponses"] = $response;
$return = json_encode( $return);
print("\n\nJSON received:\n");
print($return);
print("\n");




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

