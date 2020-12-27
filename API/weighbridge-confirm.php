<?php
//include Configurations
include './config/db.php';

//declare requred parameters & methods

$_PARAMS = array(
  'name' => ['gatePassNo','vehicleNo',
            'purchaseHub','purchaseType',
            'tareWt','wastage','grossWt','imgUrl','acnoteDate','acnote_no','acnote_bags','acnote_fig'],
  'type' => ['alphanumeric', 'alphanumeric','alphanumeric',
             'alphanumeric', 'alphanumeric',
              'alphanumeric','alphanumeric', 'alphanumeric','alphanumeric', 'alphanumeric','alphanumeric','alphanumeric'],
  'maxLength' => [ '130','255','255','255','255','255','255','255','255','255','255','255'],
);
$_METHOD = 'GET';
// Call API, init API calls execute_main()
echo json_encode(init_api($_METHOD,$_DATA,$_PARAMS,$_ERROR=''),JSON_UNESCAPED_SLASHES);
function execute_main()
{
  global $_DATA, $con;
  $slNo = $_DATA['slNo'];
  $gatePassNo = $_DATA['gatePassNo'];
  $vehicleNo = $_DATA['vehicleNo'];
  $purchaseHub = $_DATA['purchaseHub'];
  $purchaseType = $_DATA['purchaseType'];
  $tareWt = (float)$_DATA['tareWt'];
  $wastage = (float)$_DATA['wastage'];
  $grossWt = (float)$_DATA['grossWt'];
  $imgUrl = $_DATA['imgUrl'];

  $acnote_no = $_DATA['acnote_no'];
  $acnote_date = $_DATA['acnoteDate'];
  $acnote_bags = $_DATA['acnote_bags'];
  $acnote_fig = $_DATA['acnote_fig'];



  $netWt = $grossWt - ($tareWt + $wastage);



    if($con->query("UPDATE weighbridge set  gatepassno = '".$gatePassNo."',vehicleno = '".$vehicleNo."',purchasehub = '".$purchaseHub."',
    purchasetype = '".$purchaseType."',grosswt = '".$grossWt."',tarewt = '".$tareWt."',wastage = '".$wastage."',
    netwt = '".$netWt."',imgUrl = '".$imgUrl."', acnote_no = '".$acnote_no."' , acnote_date ='".$acnote_date."',acnote_bags='".$acnote_bags."',acnote_fig='".$acnote_fig."'
    status = 1 where slno = $slNo")){
      $returnArr = returnData('Update successfully', 200);
    } else{
        $returnArr = returnData('Something went wrong', 401);
    }
  return $returnArr;
}
