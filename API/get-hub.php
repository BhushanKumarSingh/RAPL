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


  $hub['purchaseHub'] = [];
  $hub['purchaseTarget'] = [];

  while($data = $hubDetails->fetch_assoc()){
    // if(array_key_exists($data['purchase_hub'],$hub)){
    //   array_push($hub[$data['purchase_hub']],$data['purchase_target']);
    // } else{
    //   $hub[$data['purchase_hub']] = [];
    //   array_push($hub[$data['purchase_hub']],$data['purchase_target']);
    // }
      array_push($hub['purchaseTarget'],$data['purchase_target']);
      array_push($hub['purchaseHub'],$data['purchase_hub']);
  }
  echo json_encode(array("ResponseCode"=>200,"ResponseMsg"=>"Hub data","response"=>$hub));
}
execute_main();
