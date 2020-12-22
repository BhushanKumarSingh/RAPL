<?php
if(!isset($_SESSION)) { session_start(); }


// Allow from any origin
if (isset($_SERVER['HTTP_ORIGIN'])) {
    // should do a check here to match $_SERVER['HTTP_ORIGIN'] to a
    // whitelist of safe domains
    header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Max-Age: 86400');    // cache for 1 day
}
// Access-Control headers are received during OPTIONS requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
        header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");

    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
        header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

}

//set timezone
date_default_timezone_set('Asia/Kolkata');
setlocale(LC_MONETARY, 'en_US');
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
  $con = new mysqli("localhost","ritikaag_RAPLus","RApl@1234567890","ritikaag_RAPL");
  $con->set_charset("utf8mb4");
} catch(Exception $e) {
  error_log($e->getMessage());
}



switch ($_SERVER['REQUEST_METHOD']) {
  case 'POST':
  $_DATA = $_POST;
    break;

  default:
  $_DATA = $_GET;
  break;
}

if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
  $ip_address = $_SERVER['HTTP_CLIENT_IP'];
} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
  $ip_address = $_SERVER['HTTP_X_FORWARDED_FOR'];
} else {
  $ip_address = $_SERVER['REMOTE_ADDR'];
}

if (!isset($_DATA['imei']) or $_DATA['imei']=='' or $_DATA['imei']==='') {
  $_DATA['imei']=$ip_address;
}

//include functions
require 'functions.php';

cors();
