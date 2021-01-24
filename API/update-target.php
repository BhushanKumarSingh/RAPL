<?php
//include Configurations
include './config/db.php';

//declare requred parameters & methods
$_PARAMS = array(
  'name' => ['hub','target'],
  'type' => ['alphanumeric','alphanumeric'],
  'maxLength' => ['255', '255'],
);
$_METHOD = 'GET';

// Call API, init API calls execute_main()
echo json_encode(init_api($_METHOD,$_DATA,$_PARAMS,$_ERROR=''),JSON_UNESCAPED_SLASHES);

/*-----------main()---------*/
function execute_main()
{
  global $_DATA, $con;
  $hub = $_DATA['hub'];
  $target = $_DATA['target'];
    if($con->query("UPDATE hub SET purchase_target ='$target' where purchase_hub = '$hub'")){
        $returnArr = returnData('Updated successfully', 200);
    } else{
        $returnArr = returnData('Something went wrong', 401);
    }
  return $returnArr;
}
