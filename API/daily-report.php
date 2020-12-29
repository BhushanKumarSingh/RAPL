<?php
//include Configurations
require './library/phpmailer/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require './library/jsontocsv.php';
include './config/db.php';
require './simplexlsx/src/SimpleXLSXGen.php';

function execute_main(){
  global $_DATA, $con;
  $BASE_URL ='https://ritikaagencies.in/API/';
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

    $weighList = $con->query("SELECT slno As SL_NO , timestamp as Datetime, vehicleno As Vehicle_No,
                              purchasehub as PACS,acnote_no as AC_Note_No ,
                              acnote_date as AC_Note_Date,acnote_bags as AC_Note_Bags,
                              grosswt as Gross_Wt,tarewt as Tare_Wt,
                              wastage as Wastage,netwt as Net_Wt from weighbridge ".$where." ");
    $weigh = [];
$temp = ['SL_NO', 'Vehicle_No', 'PACS', 'AC_Note_No','AC_Note_Date','AC_Note_Bags','Gross_Wt','Tare_Wt','Wastage','Net_Wt'];
array_push($weigh,$temp);

    while($data = $weighList->fetch_assoc()){
      $row1 = array();
      $row1[] = $data['SL_NO'];
      $row1[] = $data['Vehicle_No'];
      $row1[] = $data['PACS'];
      $row1[] = $data['AC_Note_No'];
      $row1[] = $data['AC_Note_Date'];
      $row1[] = $data['AC_Note_Bags'];
      $row1[] = $data['Gross_Wt'];
      $row1[] = $data['Tare_Wt'];
      $row1[] = $data['Wastage'];
      $row1[] = $data['Net_Wt'];
      array_push($weigh,$row1);
    }
    $reportName = 'assets/'.time().'.'.'xlsx';
  $xlsx = SimpleXLSXGen::fromArray( $weigh );
  $xlsx->saveAs($reportName);

  return $BASE_URL.$reportName;
}
echo json_encode(array('url'=>execute_main()));