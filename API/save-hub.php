<?php
//include Configurations
include './config/db.php';

//declare requred parameters & methods
$_PARAMS = array(
  'name' => ['purchaseHub','purchaseTarget'],
  'type' => [ 'alphanumeric','alphanumeric'],
  'maxLength' => ['255','255'],
);
$_METHOD = 'GET';

echo json_encode(init_api($_METHOD,$_DATA,$_PARAMS,$_ERROR=''),JSON_UNESCAPED_SLASHES);

function execute_main()
{
  global $_DATA, $con;
  $purchase_hub = $_DATA['purchaseHub'];
  $purchase_target = $_DATA['purchaseTarget'];

    if($con->query("INSERT into hub(purchase_hub,purchase_target)values('".$purchase_hub."','".$purchase_target."')")){
        $returnArr = returnData('Save successfully', 200);
    } else{
        $returnArr = returnData('Something went wrong', 401);
    }
  return $returnArr;
}
