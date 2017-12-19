
<?php 

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
        $response['buy'] = $data->buy;
        $response['sell'] = $data->sell;
        $response['volume'] = floor($data->volume);
        $response['min_24hrs'] = 'NA';
       $response['max_24hrs'] = 'NA';
    }elseif(strtolower($response['name']) == "koinex"){
      try{
       $response['buy'] = floor($data->prices->BTC);
       $response['sell'] = floor($data->prices->BTC);
       $response['volume'] = floor($data->stats->BTC->vol_24hrs);
       $response['min_24hrs'] = floor($data->stats->BTC->min_24hrs);
       $response['max_24hrs'] = floor($data->stats->BTC->max_24hrs);
       $btc['buy'] = floor($data->prices->BTC);
       $btc['sell'] = floor($data->prices->BTC);
       $ethereum['buy'] = floor($data->prices->ETH);
       $ethereum['sell'] = floor($data->prices->ETH);
       $bch['buy']= floor($data->prices->BCH);
       $bch['sell']= floor($data->prices->BCH);
        $ltc['buy']= floor($data->prices->LTC);
       $ltc['sell']= floor($data->prices->LTC);
        $xrp['buy']= floor($data->prices->XRP);
       $xrp['sell']= floor($data->prices->XRP);
        $response['btc'] = $btc;
       $response['eth'] = $ethereum;
       $response['bch'] = $bch;
       $response['ltc'] = $ltc;
       $response['xrp'] = $xrp;
     }catch(Exception $e){
        echo "koinex issue";
     }
   }elseif(strtolower($response['name']) == "coinsecure"){
       $buy = $data->message->lastPrice;
       $response['buy'] = floor(substr($buy,0,strlen($buy)-2));
       $response['sell'] = $response['buy'] ;
       $response['volume'] = 'NA';
       $low = $data->message->low;
       $response['min_24hrs'] = floor(substr($low,0,strlen($low)-2));
       $high = $data->message->high;
       $response['max_24hrs'] = floor(substr($high,0,strlen($high)-2));
   }
   $reponseData[] = $response;
}

print json_encode($reponseData);
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