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
    $date = $_DATA['date'];
    $weighList = $con->query("SELECT slno As SL_NO , date as Date, vehicleno As Vehicle_No,
                              purchasehub as PACS,acnote_no as AC_Note_No ,
                              acnote_date as AC_Note_Date,acnote_bags as AC_Note_Bags,
                              grosswt as Gross_Wt,tarewt as Tare_Wt,
                              wastage as Wastage,netwt as Net_Wt from weighbridge");
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
  $xlsx = SimpleXLSXGen::fromArray( $weigh );
  $xlsx->saveAs('assets/'.date('Y-m-d').'.'.'xlsx');

  return 'yes';
}
echo execute_main();