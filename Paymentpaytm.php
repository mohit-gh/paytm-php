<?php

if(!defined('BASEPATH')) exit('No direct script access allowed');

class Paymentpaytm extends CI_Controller {

public $data = array();

function index()
{
  $data['page_name'] = 'checkout/payment/paytm/TxnTest';
  $this->load->view('index.php',$data);
}

function paytm_status() {
	$this->load->view('checkout/payment/paytm/TxnStatus.php');
}

function paytmpost()
{
 header("Pragma: no-cache");
 header("Cache-Control: no-cache");
 header("Expires: 0");

 // following files need to be included
 require_once(APPPATH . "third_party/paytm_lib/config_paytm.php");
 require_once(APPPATH . "third_party/paytm_lib/encdec_paytm.php");

 $checkSum = "";
 $paramList = array();

 $ORDER_ID = 'ORDS88554847';
 $CUST_ID = 'CUST001';
 $INDUSTRY_TYPE_ID = 'Retail';
 $CHANNEL_ID = 'WEB';
 $TXN_AMOUNT = 1;

// Create an array having all required parameters for creating checksum.
 $paramList["MID"] = PAYTM_MERCHANT_MID;
 $paramList["ORDER_ID"] = $ORDER_ID;
 $paramList["CUST_ID"] = $CUST_ID;
 $paramList["INDUSTRY_TYPE_ID"] = $INDUSTRY_TYPE_ID;
 $paramList["CHANNEL_ID"] = $CHANNEL_ID;
 $paramList["TXN_AMOUNT"] = $TXN_AMOUNT;
 $paramList["WEBSITE"] = PAYTM_MERCHANT_WEBSITE;

 /*
 $paramList["MSISDN"] = $MSISDN; //Mobile number of customer
 $paramList["EMAIL"] = $EMAIL; //Email ID of customer
 $paramList["VERIFIED_BY"] = "EMAIL"; //
 $paramList["IS_USER_VERIFIED"] = "YES"; //

 */

//Here checksum string will return by getChecksumFromArray() function.
 $checkSum = getChecksumFromArray($paramList,PAYTM_MERCHANT_KEY);
 echo "<html>
<head>
<title>Merchant Check Out Page</title>
</head>
<body>
    <center><h1>Please do not refresh this page...</h1></center>
        <form method='post' action='".PAYTM_TXN_URL."' name='f1'>
<table border='1'>
 <tbody>";

 foreach($paramList as $name => $value) {
 echo '<input type="hidden" name="' . $name .'" value="' . $value .         '">';
 }

 echo "<input type='hidden' name='CHECKSUMHASH' value='". $checkSum . "'>
 </tbody>
</table>
<script type='text/javascript'>
 document.f1.submit();
</script>
</form>
</body>
</html>";
 }

 function paytm_callback() {
 	header("Pragma: no-cache");
	header("Cache-Control: no-cache");
	header("Expires: 0");

	// following files need to be included
	require_once(APPPATH . "third_party/paytm_lib/config_paytm.php");
	require_once(APPPATH . "third_party/paytm_lib/encdec_paytm.php");

	$paytmChecksum = "";
	$paramList = array();
	$isValidChecksum = "FALSE";

	$paramList = $_POST;
	$paytmChecksum = isset($_POST["CHECKSUMHASH"]) ? $_POST["CHECKSUMHASH"] : ""; //Sent by Paytm pg

	//Verify all parameters received from Paytm pg to your application. Like MID received from paytm pg is same as your application’s MID, TXN_AMOUNT and ORDER_ID are same as what was sent by you to Paytm PG for initiating transaction etc.
	$isValidChecksum = verifychecksum_e($paramList, PAYTM_MERCHANT_KEY, $paytmChecksum); //will return TRUE or FALSE string.


	if($isValidChecksum == "TRUE") {
		echo "<b>Checksum matched and following are the transaction details:</b>" . "<br/>";
		if ($_POST["STATUS"] == "TXN_SUCCESS") {
			echo "<b>Transaction status is success</b>" . "<br/>";
			//Process your transaction here as success transaction.
			//Verify amount & order id received from Payment gateway with your application's order id and amount.
		}
		else {
			echo "<b>Transaction status is failure</b>" . "<br/>";
		}

		if (isset($_POST) && count($_POST)>0 )
		{ 
			foreach($_POST as $paramName => $paramValue) {
					echo "<br/>" . $paramName . " = " . $paramValue;
			}
		}
		

	}
	else {
		echo "<b>Checksum mismatched.</b>";
		//Process transaction as suspicious.
	}
 } 

}

?>