<?php
//include Configurations
include './config/db.php';

function execute_main()
{
  global $_DATA, $con;
  $where = '';
  if(isset($_DATA['purchaseHub'])){
    $where = "WHERE purchase_hub = ".$_DATA['purchaseHub'];
  }

  $hubDetails = $con->query("SELECT * from hub ".$where."");

  $hub = [];
  while($data = $hubDetails->fetch_assoc()){
    array_push($hub,$data);
  }
  $returnArr = returnData('Hub data', 200,$hub);
  return $returnArr;
}
execute_main();
