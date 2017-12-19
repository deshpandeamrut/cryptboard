
<?php 

$properties = parse_ini_file("./php_new.ini", "true") or die("Could not find ini file");
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
      // var_dump($data);
    if(trim($data)!=""){
      file_put_contents("zebpay_api_data.json",($data));
    }
  }elseif(strtolower($response['name']) == "koinex"){
    if(trim($data)!=""){
      file_put_contents("koinex_api_data.json",($data));
    }
  }elseif(strtolower($response['name']) == "coinsecure"){
    if(trim($data)!=""){
      file_put_contents("coinsecure_api_data.json",($data));
    }
  }
   // $reponseData[] = $response;
}

// print json_encode($reponseData);
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
  return $buffer;
}
}
?>