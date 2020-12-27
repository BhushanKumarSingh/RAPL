<?php
//include Configurations
require '../library/phpmailer/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../library/jsontocsv.php';
include 'appconfig/db.php';

//declare requred parameters & methods
$_PARAMS = array(
  'name' => ['date'],
  'type' => ['alphanumeric'],
  'maxLength' => ['8'],
);
$_METHOD = 'GET';

// Call API, init API calls execute_main()
init_api($_METHOD, $_DATA, $_PARAMS, $_ERROR = '');

// API Main
/*-----------main()---------*/
function execute_main(){
  global $_DATA, $con; $subscriptions=[];
  //check if access_token provided is valid
  if(isset($_DATA['date']) and $_DATA['date']>date('Y-m-d')) {
    //subscription active for tomorrow but low balance
    $subscriptionDetails = $con->query("SELECT
      ms.id as oid,
      mp.id as pid,mp.name as product,mp.price as price,
      sp.pid,sp.quantity as quantity,sc.quantity as cquantity, sp.frequency as frequency,sp.".strtolower(date('D',strtotime($_DATA['date']))).", (ABS(DATEDIFF(ms.startdate,'".$_DATA['date']."'))%SUBSTRING(sp.frequency,19,1)) as datedif,
      mc.id as cid,mc.name as customer,mc.cno,mc.balance as wallet_balance,mc.ltamount as last_transaction_amount,ltdatetime as last_transaction_date,
      ca.id as adid, ca.longitude as longitude, ca.latitude as latitude,
      CONCAT(ca.label,' - ',ca.landmark,', ',ca.address,', ',ca.subarea,', ',ca.area,', ',ca.region) as address,
      ca.rid as rid,mr.name as rider,mh.id as hid,mh.name as hub,
      ms.status as status,
      ms.startdate as startdate,ms.stopdate as stopdate,ms.pausedate as pausedate,ms.resumedate as resumedate
  FROM
      master_subscription ms,
      subscription_product sp LEFT OUTER JOIN subscription_change sc on (sp.sid=sc.sid and sp.pid=sc.pid and sc.startdate=DATE_FORMAT('".$_DATA['date']."', '%Y-%m-%d')),
      master_product mp,
      master_rider mr,
      master_hub mh,
      master_customer mc,
      customer_address ca
  WHERE
      (ms.id=sp.sid and sp.pid=mp.id and ms.rid=mr.id and mh.id=mr.hid and ms.cid=mc.id and ms.adid=ca.id)
      AND    (ms.status ='Active' and ((sp.".strtolower(date('D',strtotime($_DATA['date'])))." is NOT NULL and sp.".strtolower(date('D',strtotime($_DATA['date']))).">0) or (sp.quantity is NOT NULL and sp.quantity>0)))
      AND    (ms.startdate<=DATE_FORMAT('".$_DATA['date']."', '%Y-%m-%d'))
      AND    (ms.stopdate is NULL or ms.stopdate IN ('') or ms.stopdate>=DATE_FORMAT('".$_DATA['date']."', '%Y-%m-%d'))
      AND    (ms.resumedate is NULL or ms.resumedate IN ('') or ms.resumedate<=DATE_FORMAT('".$_DATA['date']."', '%Y-%m-%d'))
      AND    ((ABS(DATEDIFF(ms.startdate,'".$_DATA['date']."'))%SUBSTRING(sp.frequency,19,1)) IS NULL OR (ABS(DATEDIFF(ms.startdate,'".$_DATA['date']."'))%SUBSTRING(sp.frequency,19,1)) = 0)
      AND    ((sp.frequency='One-Time' and ms.startdate='".$_DATA['date']."') OR sp.frequency NOT IN ('One-Time'))
      ");
    while ($subscription = $subscriptionDetails->fetch_assoc()) {

      if ($subscription['frequency']=='Customize') {
        $subscription['quantity']=$subscription[strtolower(date("D",strtotime($_DATA['date'])))];
      }
      if ($subscription['cquantity']=='NULL' or $subscription['cquantity']==NULL) {} else {
        $subscription['quantity']=$subscription['cquantity'];
      }
      if ($subscription['quantity']) {
        $product = [];
        $product['name'] = $subscription['product'];
        $product['quantity'] = $subscription['quantity'];
        $product['price'] = $subscription['price'];

        $amount=$subscription['price']*$subscription['quantity'];

        unset($subscription['product']);
        unset($subscription['quantity']);
        unset($subscription['price']);

        if (array_key_exists($subscription['cid'],$subscriptions)){
          $subscriptions[$subscription['cid']]['amount']+=$amount;
          array_push($subscriptions[$subscription['cid']]['product'],$product);
        } else {
          $subscription['amount'] = $amount;

          $subscription['product'] = [];
          array_push($subscription['product'],$product);

          $subscriptions[$subscription['cid']] = $subscription;
        }
      }
    }

    foreach ($subscriptions as $cid => $subscription) {
      if ((int)$subscription['amount']<(int)$subscription['wallet_balance']) {
        unset($subscriptions[$cid]);
      } else {
        unset($subscriptions[$cid]['product']);
        $subscriptions[$cid]['order_value']=$subscriptions[$cid]['amount'];
        $subscriptions[$cid]=array_keys_set($subscriptions[$cid],['customer','address','cno','wallet_balance','order_value','rider','hub','status','last_transaction_date']);
      }
    }
    sendMailUser('biswabijaya.samal@milkmantra.com', 'Biswabijaya Samal',date('Y-m-d').'_new_dailymoo_low_credit_report',json_encode($subscriptions));

    //$returnArr = returnData('Future Order Fetch Success', 200, array('low_credit' => $subscriptions,'timestamp'=>date('Y-m-d H:i:s')));
  } else {
    //$returnArr = returnData('Fetch Not Success', 401, );
  }
  //return $returnArr;
}


function sendMailUser($to,$toname,$reportname,$strJson){

  global $_DATA, $con;

  $subject = 'Low Credit Report | '.date('Y-m-d');
  $from = 'reports@dailymoo.online';

  // To send HTML mail, the Content-type header must be set
  $headers  = 'MIME-Version: 1.0' . "\r\n";
  $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

  // Create email headers
  $headers .= 'From: '.$from."\r\n".
      'Reply-To: '.$from."\r\n" .
      'X-Mailer: PHP/' . phpversion();

  // Compose a simple HTML email message
  $message = '<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="x-apple-disable-message-reformatting">
    <meta name="format-detection" content="telephone=no,address=no,email=no,date=no,url=no">
    <meta name="color-scheme" content="light">
    <meta name="supported-color-schemes" content="light">
    <title>Thank you for supporting Project Slate.</title>
    <style>

        :root {
          color-scheme: light;
          supported-color-schemes: light;
        }
        html,
        body {
            margin: 0 auto !important;
            padding: 0 !important;
            height: 100% !important;
            width: 100% !important;
            color: #696969;
        }

        /* What it does: Stops email clients resizing small text. */
        * {
            -ms-text-size-adjust: 100%;
            -webkit-text-size-adjust: 100%;
        }

        /* What it does: Centers email on Android 4.4 */
        div[style*="margin: 16px 0"] {
            margin: 0 !important;
        }

        /* What it does: forces Samsung Android mail clients to use the entire viewport */
        #MessageViewBody, #MessageWebViewDiv{
            width: 100% !important;
        }

        /* What it does: Stops Outlook from adding extra spacing to tables. */
        table,
        td {
            mso-table-lspace: 0pt !important;
            mso-table-rspace: 0pt !important;
        }

        /* What it does: Replaces default bold style. */
        th {
        	font-weight: normal;
        }

        /* What it does: Fixes webkit padding issue. */
        table {
            border-spacing: 0 !important;
            border-collapse: collapse !important;
            table-layout: fixed !important;
            margin: 0 auto !important;
        }

        /* What it does: Prevents Windows 10 Mail from underlining links despite inline CSS. Styles for underlined links should be inline. */
        a {
            text-decoration: none;
        }

        /* What it does: Uses a better rendering method when resizing images in IE. */
        img {
            -ms-interpolation-mode:bicubic;
        }

        /* What it does: A work-around for email clients meddling in triggered links. */
        a[x-apple-data-detectors],  /* iOS */
        .unstyle-auto-detected-links a,
        .aBn {
            border-bottom: 0 !important;
            cursor: default !important;
            color: inherit !important;
            text-decoration: none !important;
            font-size: inherit !important;
            font-family: inherit !important;
            font-weight: inherit !important;
            line-height: inherit !important;
        }

        /* What it does: Prevents Gmail from changing the text color in conversation threads. */
        .im {
            color: inherit !important;
        }

        /* What it does: Prevents Gmail from displaying a download button on large, non-linked images. */
        .a6S {
           display: none !important;
           opacity: 0.01 !important;
		}
		img.g-img + div {
		   display: none !important;
		}

        /* What it does: Removes right gutter in Gmail iOS app: https://github.com/TedGoas/Cerberus/issues/89  */
        /* Create one of these media queries for each additional viewport size you would like to fix */

        /* iPhone 4, 4S, 5, 5S, 5C, and 5SE */
        @media only screen and (min-device-width: 320px) and (max-device-width: 374px) {
            u ~ div .email-container {
                min-width: 320px !important;
            }
        }
        /* iPhone 6, 6S, 7, 8, and X */
        @media only screen and (min-device-width: 375px) and (max-device-width: 413px) {
            u ~ div .email-container {
                min-width: 375px !important;
            }
        }
        /* iPhone 6+, 7+, and 8+ */
        @media only screen and (min-device-width: 414px) {
            u ~ div .email-container {
                min-width: 414px !important;
            }
        }

    </style>
    <!-- CSS Reset : END -->

    <!-- Progressive Enhancements : BEGIN -->
    <style>

        /* What it does: Hover styles for buttons */
        .button-td,
        .button-a {
            transition: all 100ms ease-in;
        }
	    .button-td-primary:hover,
	    .button-a-primary:hover {
	        background: #555555 !important;
	        border-color: #555555 !important;
	    }

        /* Media Queries */
        @media screen and (max-width: 600px) {

            .email-container {
                width: 100% !important;
                margin: auto !important;
            }

            /* What it does: Forces table cells into full-width rows. */
            .stack-column,
            .stack-column-center {
                display: block !important;
                width: 100% !important;
                max-width: 100% !important;
                direction: ltr !important;
            }
            /* And center justify these ones. */
            .stack-column-center {
                text-align: center !important;
            }

            /* What it does: Generic utility class for centering. Useful for images, buttons, and nested tables. */
            .center-on-narrow {
                text-align: center !important;
                display: block !important;
                margin-left: auto !important;
                margin-right: auto !important;
                float: none !important;
            }
            table.center-on-narrow {
                display: inline-block !important;
            }

            /* What it does: Adjust typography on small screens to improve readability */
            .email-container p {
                font-size: 17px !important;
            }
        }

    </style>
    <!-- Progressive Enhancements : END -->

</head>
<body width="100%" style="margin: 0; padding: 0 !important; mso-line-height-rule: exactly; background-color: #6abbb5;">
  <center role="article" aria-roledescription="email" lang="en" style="width: 100%; background-color: #6abbb5;">
    <!--[if mso | IE]>
    <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #222222;">
    <tr>
    <td>
    <![endif]-->

        <!-- Visually Hidden Preheader Text : BEGIN -->
        <div style="max-height:0; overflow:hidden; mso-hide:all;" aria-hidden="true">DailyMoo Admin Reports</div>
        <!-- Visually Hidden Preheader Text : END -->

        <!-- Create white space after the desired preview text so email clients don’t pull other distracting text into the inbox preview. Extend as necessary. -->
        <!-- Preview Text Spacing Hack : BEGIN -->
        <div style="display: none; font-size: 1px; line-height: 1px; max-height: 0px; max-width: 0px; opacity: 0; overflow: hidden; mso-hide: all; font-family: sans-serif;">
            &zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;
        </div>
        <!-- Preview Text Spacing Hack : END -->

        <!-- Email Body : BEGIN -->

    <!--[if mso | IE]>
    </td>
    </tr>
    </table>
    <![endif]-->
    </center>
</body>
</html>';



  $mail = new PHPMailer(true);

  try {
      //Server settings
      $mail->SMTPDebug = 0;
      $mail->isSMTP();                                            // Send using SMTP
      $mail->Host       = 'smtp.hostinger.in';                    // Set the SMTP server to send through
      $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
      $mail->Username   = $from;                     // SMTP username
      $mail->Password   = 'nji9MKO)';                               // SMTP password
      $mail->SMTPSecure = 'tls';         // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
      $mail->Port       = 587;                                    // TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above
      $mail->SMTPOptions = array(
        	'ssl' => array(
        	'verify_peer' => false,
        	'verify_peer_name' => false,
        	'allow_self_signed' => true
        	)
    		);
      //Recipients
      $mail->setFrom($from, 'Reports | Dailymoo');
      $mail->addAddress($to, $toname);     // Add a recipient
      $mail->AddCC('sachin.bishwakarma@milkmantra.com', 'Sachin Bishwakarma');
      // $mail->AddCC('amrit.visa@milkmantra.com', 'Amrit Visa');
      // $mail->AddCC('ashish.patra@milkmantra.com', 'Ashish Patra');
      $mail->AddCC('cc.milkmantra@gmail.com', 'Milkmantra Call Center');

      $mail->addBCC('ea.md@milkmantra.com');

      $strJsonFile='./output'.'/'.getToken(8).'-'.time().'.csv';
      touch($strJsonFile);
      jsonToCsv($strJson,$strJsonFile);

      // Attachments
      $mail->addAttachment($strJsonFile, $reportname.'.csv');    // Optional name

      // Content
      $mail->isHTML(true);                                  // Set email format to HTML
      $mail->Subject = $subject;
      $mail->Body    = $message;
      $mail->AltBody = $reportname;

      if(!$mail->Send()) {
        //echo 'Message was not sent.';
        echo 'Mailer error: ' . $mail->ErrorInfo;
        if (file_exists($strJsonFile)) {
          unlink($strJsonFile);
        }
      } else {
        //echo 'Message has been sent.';
        if (file_exists($strJsonFile)) {
          unlink($strJsonFile);
        }
      }
      // echo 'Message has been sent';
  } catch (Exception $e) {
      // echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
  }
}
