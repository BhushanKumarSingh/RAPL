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
  $todate = $todate.' '.'23:59:00';


  $where = "WHERE (timestamp BETWEEN '".$fromdate."' and '".$todate."')";

  $date = $_DATA['date'];
  $weighList = $con->query("SELECT slno As SL_NO , timestamp as Datetime, vehicleno As Vehicle_No,purchasehub as PACS,acnote_no as AC_Note_No ,acnote_date as AC_Note_Date,acnote_bags as AC_Note_Bags,grosswt as Gross_Wt,tarewt as Tare_Wt, wastage as Wastage,netwt as Net_Wt  from weighbridge ".$where."");
  $weigh = [];
  while($data = $weighList->fetch_assoc()){
    array_push($weigh,$data);
  }
  $returnArr = returnData('Today list', 200,$weigh);
  return $returnArr;
}
