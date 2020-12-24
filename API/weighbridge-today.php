<?php
//include Configurations
include './config/db.php';

//declare requred parameters & methods
$_PARAMS = array(
  'name' => ['date'],
  'type' => ['alphanumeric'],
  'maxLength' => ['10'],
);
$_METHOD = 'GET';


// Call API, init API calls execute_main()
echo json_encode(init_api($_METHOD,$_DATA,$_PARAMS,$_ERROR=''),JSON_UNESCAPED_SLASHES);


// API Main
/*-----------main()---------*/
function execute_main()
{
  global $_DATA, $con;
  $date = $_DATA['date'];
  $weighList = $con->query("SELECT * from weighbridge where date = '$date'");
  $weigh = [];
  while($data = $weighList->fetch_assoc()){
    array_push($weigh,$data);
  }
  $returnArr = returnData('Today list', 200,$weigh);
  return $returnArr;
}
