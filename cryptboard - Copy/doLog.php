<?php
/*
 * Input: Text to be logged
 * Invoked by AJAX calls and writes the details about the user and actions in userdetails.txt
 * Returns nothing
 */
// include './corsHeader.php';
session_start();
client_cookie();

if (isset($_POST['userData'])) {
    $userData = ($_POST['userData']);
//    var_dump($userData["browswername"]);
    $client_id = "NA";
    
    $userData = json_encode($userData);
    $userData = json_decode($userData);
    $user = "Anonymous";
    $userData->browswername = str_replace(",", "_", $userData->browswername);
    $userData->version = str_replace(",", "_", $userData->version);
    $userData->appName = str_replace(",", "_", $userData->appName);
    $userData->userAgent = str_replace(",", "_", $userData->userAgent);
    $userData->OS = str_replace(",", "_", $userData->OS);
    $userData->userText = str_replace(",", "_", $userData->userText);
    $op = $userData->browswername . ","
    . $userData->version . ","
    . $userData->appName . "," .
    $userData->userAgent . "," .
    $userData->OS . "," .
    $userData->userText . "," .
    $user . "," .
    $userData->timestamp . ",". 
    $client_id."\n";
    file_put_contents("data/userdetails.txt", $op, FILE_APPEND);
} else {
    //if included in php script
    //doPhpLog($userText);
}

function doPhpLog($userText) {
    $user = "Anonymous";
    if (isset($_SESSION['intranetid'])) {
        $user = $_SESSION['intranetid'];
    }
    date_default_timezone_set('Asia/Kolkata');
    $time_stamp = date('m/d/Y H:i:s', time());
    $op = 'browswername' . "," . 'version' . "," . 'appName' . "," . 'userAgent' . "," . 'OS' . "," . $userText . "," .
    $user . "," .
    $time_stamp . "\n";
    file_put_contents("userdetails.txt", $op, FILE_APPEND);
}
function client_cookie() {
    $cookie_name = "client_id";
    $cookie_value = time();
    $client_id = "";
    if (isset($_COOKIE['client_id'])) {
        $client_id = $_COOKIE['client_id'];
    } else {
        setcookie($cookie_name, $cookie_value, time() + (86400 * 90), "/"); // 86400 = 1 day
    }
}
?>