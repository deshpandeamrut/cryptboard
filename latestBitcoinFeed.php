<?php



$responseData = doCurl("https://newsapi.org/v2/everything?q=bitcoin&sortBy=publishedAt&apiKey=d7ae5b652f4d481d8cb7ded898d9d43f");
print json_encode($responseData);

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