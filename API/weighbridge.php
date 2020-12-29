<?php
//include Configurations
include './config/db.php';

//declare requred parameters & methods
$_PARAMS = array(
  'name' => ['gatePassNo','vehicleNo',
            'purchaseHub','purchaseType',
            'tareWt','wastage','grossWt','imgUrl','purchaseTarget'],
  'type' => ['alphanumeric','alphanumeric',
             'alphanumeric','alphanumeric', 'alphanumeric',
              'alphanumeric','alphanumeric', 'alphanumeric','alphanumeric'],
  'maxLength' => ['130', '255','255','255','255','255','255','255','255'],
);
$_METHOD = 'GET';

// Call API, init API calls execute_main()
echo json_encode(init_api($_METHOD,$_DATA,$_PARAMS,$_ERROR=''),JSON_UNESCAPED_SLASHES);


// API Main
/*-----------main()---------*/
function execute_main()
{
  global $_DATA, $con;
  $gatePassNo = $_DATA['gatePassNo'];
  $vehicleNo = $_DATA['vehicleNo'];
  $purchaseHub = $_DATA['purchaseHub'];
  $purchaseType = $_DATA['purchaseType'];
  $tareWt = (float)$_DATA['tareWt'];
  $wastage = (float)$_DATA['wastage'];
  $grossWt = (float)$_DATA['grossWt'];
  $imgUrl = $_DATA['imgUrl'];
  $purchase_target = $_DATA['purchaseTarget'];

  $netWt = $grossWt - ($tareWt + $wastage);


// $upload_dir = '';
// $server_url = 'https://api.dailymoo.in/test';

// if($_FILES['avatar'])
// {
//     $avatar_name = $_FILES["avatar"]["name"];
//     $avatar_tmp_name = $_FILES["avatar"]["tmp_name"];

//         $random_name = rand(1000,1000000)."-".$avatar_name;
//         $upload_name = $upload_dir.strtolower($random_name);
//         $upload_name = preg_replace('/\s+/', '-', $upload_name);
    
//         if(move_uploaded_file($avatar_tmp_name , $upload_name)) {
//           $imgUrl = $server_url."/".$upload_name;
//         }
//   }


    if($con->query("INSERT into weighbridge(gatepassno,vehicleno,purchasehub,purchasetype,grosswt,tarewt,wastage,netwt,imgUrl,purchase_target,timestamp)
                    values('".$gatePassNo."','".$vehicleNo."','".$purchaseHub."',
                            '".$purchaseType."','".$grossWt."','".$tareWt."','".$wastage."','".$netWt."','".$imgUrl."','".$purchase_target."','".date('Y-m-d H:i:s')."')")){
        $returnArr = returnData('Save successfully', 200);

    } else{

        $returnArr = returnData('Something went wrong', 401);
    }

  return $returnArr;
}
