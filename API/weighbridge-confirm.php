<?php
//include Configurations
include './config/db.php';

//declare requred parameters & methods

$_PARAMS = array(
  'name' => ['gatePassNo','vehicleNo',
            'purchaseHub','purchaseType',
            'tareWt','wastage','netWt','imgUrl','acnote_qty','acnote_no'],
  'type' => ['alphanumeric', 'alphanumeric','alphanumeric',
             'alphanumeric', 'alphanumeric',
              'alphanumeric','alphanumeric', 'alphanumeric','alphanumeric', 'alphanumeric'],
  'maxLength' => [ '130','255','255','255','255','255','255','255','255','255'],
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
  $tareWt = (int)$_DATA['tareWt'];
  $wastage = (int)$_DATA['wastage'];
  $netWt = (int)$_DATA['netWt'];
  $imgUrl = $_DATA['imgUrl'];

  $acnote_no = $_DATA['acnote_no'];
  $acnote_qty = $_DATA['acnote_qty'];
  $grossWt = $netWt - ($tareWt + $wastage);



    if($con->query("UPDATE weighbridge set  gatepassno = '".$gatePassNo."',vehicleno = '".$vehicleNo."',purchasehub = '".$purchaseHub."',purchasetype = '".$purchaseType."',grosswt = '".$grossWt."',tarewt = '".$tareWt."',wastage = '".$wastage."',netwt = '".$netWt."',imgUrl = '".$imgUrl."', acnote_no = '".$acnote_no."' , acnote_qty ='".$acnote_qty."',status = 1 where slno = $slNo")){
      $returnArr = returnData('Update successfully', 200);
    } else{
        $returnArr = returnData('Something went wrong', 401);
    }
  return $returnArr;
}
