<?php
//include Configurations
include './config/db.php';

//declare requred parameters & methods
$_PARAMS = array(
  'name' => ['fromdate','todate'],
  'type' => ['alphanumeric','alphanumeric'],
  'maxLength' => ['10','10'],
);
$_METHOD = 'GET';


// Call API, init API calls execute_main()
echo json_encode(init_api($_METHOD,$_DATA,$_PARAMS,$_ERROR=''),JSON_UNESCAPED_SLASHES);


// API Main
/*-----------main()---------*/
function execute_main()
{
  global $_DATA, $con;
  if(isset($_GET['fromdate'])){
    $fromdate=date('Y-m-d',strtotime($_GET['fromdate']));
  } else {
    $fromdate=date('Y-m-d');
  }

  if(isset($_GET['todate'])){
    $todate=date('Y-m-d',strtotime($_GET['todate']));
  } else {
    $todate=date('Y-m-d');
  }


  $where = "WHERE (timestamp BETWEEN '".$fromdate."' and '".$todate."')";

  $date = $_DATA['date'];
  $weighList = $con->query("SELECT *  from weighbridge ".$where."");
  $weigh = [];
  while($data = $weighList->fetch_assoc()){
    array_push($weigh,$data);
  }
  $returnArr = returnData('Today list', 200,$weigh);
  return $returnArr;
}
