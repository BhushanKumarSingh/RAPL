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
    if($con->query("INSERT INTO hub(purchase_hub,purchase_target)VALUES('".$hub."','".$target."')")){
        $returnArr = returnData('Save successfully', 200);
    } else{
        $returnArr = returnData('Something went wrong', 401);
    }
  return $returnArr;
}
