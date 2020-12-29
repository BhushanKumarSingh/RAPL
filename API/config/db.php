<?php
if(!isset($_SESSION)) { session_start(); }


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
