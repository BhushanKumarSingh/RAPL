<?php
//include Configurations
include './config/db.php';

//declare requred parameters & methods
$_PARAMS = array(
  'name' => ['slNo','acnote_qty','acnote_no'],
  'type' => ['alphanumeric','alphanumeric', 'alphanumeric'],
  'maxLength' => ['8','255','255'],
);
$_METHOD = 'GET';


// Call API, init API calls execute_main()
echo json_encode(init_api($_METHOD,$_DATA,$_PARAMS,$_ERROR=''),JSON_UNESCAPED_SLASHES);


// API Main
/*-----------main()---------*/
function execute_main()
{
  global $_DATA, $con;
  $id = $_DATA['id'];
  $acnote_no = $_DATA['acnote_no'];
  $acnote_qty = $_DATA['acnote_qty'];


    if($con->query("UPDATE weighbridge set acnote_no = '$acnote_no' , acnote_qty ='$acnote_qty',status = 1 where id = $id")){
      $returnArr = returnData('Update successfully', 200);
    } else{
        $returnArr = returnData('Something went wrong', 401);
    }
  return $returnArr;
}
