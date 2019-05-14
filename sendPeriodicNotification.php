

<?PHP
function sendMessage(){
    $allowedTimeSlots = [0,2,15,30,45];
    date_default_timezone_set('Asia/Kolkata');
    $currentTime = date('i', time());
    if($currentTime=="00"){
        $currentTime="0";
    }
    if(in_array($currentTime, $allowedTimeSlots)){
        echo "Current time is in allocation slot.";    
    }else{
        exit();
    }
    echo "Current Time: ".$currentTime;    
    $url = "http://nammabagalkot.in/cryptboard/callExchanges.php";
    $responseData = file_get_contents($url);
    $responseData = json_decode($responseData);
    $message = "";
    foreach ($responseData as $key => $value) {
        $name = $value->name;
        if($name=="koinex"){
            $message.="BTC".": ".$value->btc->buy."\n";
            $message.="BCH".": ".$value->bch->buy."\n";
            $message.="ETH".": ".$value->eth->buy."\n";
            $message.="XRP".": ".$value->xrp->buy."\n";
            // $message.="LTC".":".$value->ltc->buy."\n";
        }
    }
    var_dump($message);
    $content = array(
        "en" => $message
    );
    $fields = array(
        'app_id' => "7334a009-9ce6-491b-9e3b-223d839d6c65",
        'filters' => array(array("field" => "tag", "key" => $currentTime, "relation" => "=", "value" => "true")),
        'chrome_web_icon' =>"http://nammabagalkot.in/cryptboard/img/push_icob.png",
        'contents' => $content,
        'url' => "http://nammabagalkot.in/cryptboard",
        'web_buttons' => array(array("id" => "manage-notifications-button", "text" => "Manage Notifications", "icon" => "http://nammabagalkot.in/cryptboard/img/settings-icon.png", "url" => "http://nammabagalkot.in/cryptboard/#/notifications"))
    );
    $fields = json_encode($fields);
    echo $fields;
    print("\nJSON sent:\n");
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

?>

