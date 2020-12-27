<?php
//include Configurations
include './config/db.php';

function execute_main()
{
  global $_DATA, $con;
  $where = '';
  if(isset($_DATA['purchaseHub'])){
    $purchaseHub = $_DATA['purchaseHub'];
    $where = "WHERE purchase_hub = '$purchaseHub'";
    $hubDetails = $con->query("SELECT * from hub ".$where."");
  } else {
    $hubDetails = $con->query("SELECT * from hub");
  }


  $hub = [];
  while($data = $hubDetails->fetch_assoc()){
    array_push($hub,$data);
  }
  $returnArr = returnData('Hub data', 200,$hub);
  return $returnArr;
}
execute_main();
