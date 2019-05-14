

<?PHP
function sendMessage(){
    $xrpChangePercent = 0.025;
    $btcChangePercent = 0.01;
    $bchChangePercent = 0.019;
    $ethChangePercent = 0.02;
    $url = "http://nammabagalkot.in/cryptboard/callExchanges.php";
    $responseData = file_get_contents($url);
    $responseData = json_decode($responseData);
    $lastFetchData = getLastPrice();
    // var_dump($lastFetchData);
    echo "<br/>";
    // var_dump($responseData);
    $message = "";
    foreach ($responseData as $key => $value) {
        $name = $value->name;
        if($name=="koinex"){
            // $message.="Koinex\n";
            /*
            *Coin wise spike handling
            */
            /* 1. XRP */
            $oldValue = $lastFetchData['koinex']['xrp'];
            $newValue = $value->xrp->buy;
            $changeValue = $newValue-$oldValue;
            if($changeValue<0){
                $change = "Fall";
            }else{
                $change = "Rise";
            }
            $percentageChangeValue = abs(($changeValue/$oldValue));
            if($percentageChangeValue>$xrpChangePercent){
                setlocale(LC_MONETARY, 'en_IN');
                $oldValue = money_format('%!i', $oldValue);
                $newValue = money_format('%!i', $newValue);
                $message.=(round($percentageChangeValue*100))."% ".$change." for XRP\n"
                .$oldValue."->".$newValue. "\n";
            }

            /* 2. BTC */
            $oldValue = $lastFetchData['koinex']['btc'];
            $newValue = $value->buy;
            $changeValue = $newValue-$oldValue;
            if($changeValue<0){
                $change = "Fall";
            }else{
                $change = "Rise";
            }
            $percentageChangeValue = abs(($changeValue/$oldValue));
            if($percentageChangeValue>$btcChangePercent){
                setlocale(LC_MONETARY, 'en_IN');
                $oldValue = money_format('%!i', $oldValue);
                $newValue = money_format('%!i', $newValue);
                $message.=(round($percentageChangeValue*100))."% ".$change." for BTC\n"
                .$oldValue."->".$newValue. "\n";
            }
            /* 3. ETH */
            $oldValue = $lastFetchData['koinex']['eth'];
            $newValue = $value->eth->buy;
            $changeValue = $newValue-$oldValue;
            if($changeValue<0){
                $change = "Fall";
            }else{
                $change = "Rise";
            }
            $percentageChangeValue = abs(($changeValue/$oldValue));
            if($percentageChangeValue>$ethChangePercent){
                setlocale(LC_MONETARY, 'en_IN');
                $oldValue = money_format('%!i', $oldValue);
                $newValue = money_format('%!i', $newValue);
                $message.=(round($percentageChangeValue*100))."% ".$change." for ETH\n"
                .$oldValue."->".$newValue. "\n";
            }
            /* 4. BCH */
            $oldValue = $lastFetchData['koinex']['bch'];
            $newValue = $value->bch->buy;
            $changeValue = $newValue-$oldValue;
            if($changeValue<0){
                $change = "Fall";
            }else{
                $change = "Rise";
            }
            $percentageChangeValue = abs(($changeValue/$oldValue));
            if($percentageChangeValue>$bchChangePercent){
                setlocale(LC_MONETARY, 'en_IN');
                $oldValue = money_format('%!i', $oldValue);
                $newValue = money_format('%!i', $newValue);
               $message.=(round($percentageChangeValue*100))."% ".$change." for BCH\n"
               .$oldValue."->".$newValue. "\n";
           }
           /* 5. LTC */
            /*$oldValue = $lastFetchData['koinex']['eth'];
            $newValue = $value->ltc->buy;
            $changeValue = $newValue-$oldValue;
            if($changeValue<0){
                $change = "Fall";
            }else{
                $change = "Rise";
            }
            $percentageChangeValue = abs(($changeValue/$oldValue));
            if($percentageChangeValue>$xrpChangePercent){
                $message.=(round($percentageChangeValue*100))."% ".$change." in LTC value to make it ".$newValue. "\n";
            }*/
            /*
            End spike handling
            */

        }else{
            // $message.=$name.": ".$value->buy."\n";
        }
    }
    
    echo "$message";
    if($message==""){
        echo "No spikes!";
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
            //btc,bch,eth,xrp
            $lastLineArray = explode(",", $lastLine);
            $lastPrice['btc'] = $lastLineArray[1];
            if(isset($lastLineArray[2])){
                $lastPrice['bch'] = $lastLineArray[2];    
            }
            if(isset($lastLineArray[3])){
                $lastPrice['eth'] = $lastLineArray[3];    
            }
            if(isset($lastLineArray[4])){
                $lastPrice['xrp'] = $lastLineArray[4];    
            }
            $lastFetchData[$exchange] = $lastPrice;
        }
    }
    return $lastFetchData;
}
?>

