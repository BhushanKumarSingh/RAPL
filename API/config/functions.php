<?php


//Filters Special Characters
/**
 * This fuction is used to filter the parameter and escapes all the special characters in a string
 * filter
 *
 * @param  mixed $con
 * @param  mixed $data
 * @param  mixed $value
 * @return void
 */
function filter($con, $data, $value)
{
  return strip_tags(mysqli_real_escape_string($con, $data[$value]));
}

//Initialise Server Data Variable
/**
 * This function is cheked which type of request is coming
 * check_method
 *
 * @return 'POST'/'GET'
 */
function check_method()
{
  if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    return 'POST';
  } else {
    return 'GET';
  }
}

//Initialise Server Data Variable
/**
 * This function is for parameter cheking
 * check_param_errors
 *
 * @param  mixed $f_DATA
 * @param  mixed $f_PARAM
 * @return void
 */
function check_param_errors($f_DATA, $f_PARAM)
{
  $f_PM = '';
  $f_PV = '';
  $f_PM_count = $f_PV_count = 0;
  $param = $f_PARAM['name'];
  for ($i = 0; $i < count($param); $i++) {
    if (isset($f_DATA[$param[$i]])) {
      $f_PV .= $param[$i] . ',';
      $f_PV_count++;
    } else {
      $f_PM .= $param[$i] . ',';
      $f_PM_count++;
    }
  }
  if (count($f_PARAM['name']) == $f_PV_count) {
    return 0;
  } else {
    return array('Params Missing or Invalid' => $f_PM, 'Params Valid' => $f_PV);
  }
}


//API Response Encode
/**
 * This function is for formating the responce message
 * returnData
 *
 * @param  mixed $responseMsg
 * @param  mixed $responseCode
 * @param  mixed $data
 * @return void
 */
function returnData($responseMsg = 'Some Error Occured', $responseCode = 401, $data = '')
{
  return array("ResponseCode" => $responseCode, "ResponseMsg" => $responseMsg, "response" => $data);
}

//init API
/**
 * This function is for validating all required paramter
 * init_api
 *
 * @param  mixed $f_METHOD
 * @param  mixed $f_DATA
 * @param  mixed $f_PARAMS
 * @param  mixed $f_ERROR
 * @return void
 */
function init_api($f_METHOD, $f_DATA, $f_PARAMS, $f_ERROR = '')
{
  //check if API call satisfies required Method and contains valid parameters
  if (check_method() != $f_METHOD) {
    //print which method accepted
    $returnArr = returnData($f_METHOD . ' Method Accepted Only');
  } else {
    if ($f_ERROR = check_param_errors($f_DATA, $f_PARAMS)) {
      //print param errors if occured
      $returnArr = returnData($f_ERROR);
    } else {
      //execure main work
      $returnArr = execute_main();
    }
  }
  return $returnArr;
}



function cors() {

  // Allow from any origin
  if (isset($_SERVER['HTTP_ORIGIN'])) {
      // Decide if the origin in $_SERVER['HTTP_ORIGIN'] is one
      // you want to allow, and if so:
      header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
      header('Access-Control-Allow-Credentials: true');
      header('Access-Control-Max-Age: 86400');    // cache for 1 day
  }

  // Access-Control headers are received during OPTIONS requests
  if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

      if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
          // may also be using PUT, PATCH, HEAD etc
          header("Access-Control-Allow-Methods: GET, POST, OPTIONS");

      if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
          header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

      exit(0);
  }

}
