<?php
include './config/db.php';
$_PARAMS = array(
    'name' => ['id'],
    'type' => ['alphanumeric'],
    'maxLength' => ['8'],
  );
  $_METHOD = 'GET';
  echo json_encode(init_api($_METHOD, $_DATA, $_PARAMS, $_ERROR = ''));
  
  function execute_main(){
    echo 'hello bhuahn';
  }