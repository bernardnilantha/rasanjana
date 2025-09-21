<?php error_reporting (E_ALL ^ E_NOTICE);

 if (isset($_POST['tag']) && $_POST['tag'] != '') {
	//{mobile=0715870870, imei=99594c535e7588bd, tag=otp_request}
     $tag = $_POST['tag'];

 	 include("otpapi.php");

	 $app_key = "7b63ec57cbeebe598ef80b1443eaa2f7";

	 $otp = new Otp(API);	 

     //$db = new DB_Functions();

     $response = array("tag" => $tag, "success" => 0, "error" => 0);

    if ($tag == 'paid_verify') {

		 

		 $res = $otp->VerfyPaid("VerfyPaid",$app_key); 

 		 $json =	json_decode($res);

		 

		 $active = $json->APP_OTP[0]->active; 

		 $response["paid"] 		= $active; 

		 $response["success"] 	= 1;

         $response["message"] 	= "success";

 		 echo json_encode($response);

	 } else if ($tag == 'otp_verify') {

		 $pin	 	 = $_POST['pin'];

 		 $imei	     = $_POST['imei']; 

		 $refNo 	 = $_POST['refNo'];

		 $res = $otp->VerfyOtp("otp_verify",$refNo,$pin,$app_key); 

		 //$json =  json_decode('{ "APP_OTP": [ { "statusDetail": "Could not find OTP", "version": "1.0", "statusCode": "E1854" } ] }');//

		 $json =	json_decode($res);

		 

		 $statusDetail = $json->APP_OTP[0]->statusDetail;

		 $statusCode = $json->APP_OTP[0]->statusCode;

		  

		 $response["referenceNo"] 	= $refNo;

		 $response["statusCode"] 	= $statusCode;

		 $response["success"] 	= 1;

         $response["message"] 	= $statusDetail;

 		 echo json_encode($response);

	 } else if ($tag == 'otp_request') {

		 $mobile 	 = $_POST['mobile'];

 		 $imei	     = $_POST['imei']; 

		 $res = $otp->RegOtp("otp_request",$mobile,$app_key, "vSa5kD9Xgf+"); //"eYQ208cuf3a"

 		 $json = json_decode($res);

		 

		 $statusDetail = $json->APP_OTP[0]->statusDetail;

		 $statusCode = $json->APP_OTP[0]->statusCode;

		 if($statusCode=="S1000"){

			 $referenceNo = $json->APP_OTP[0]->referenceNo;

		 } else {

			 $referenceNo = "";

		 }

		 $response["referenceNo"] 	= $referenceNo;

		 $response["statusCode"] 	= $statusCode;

		 $response["success"] 	= 1;

         $response["message"] 	= $statusDetail;

 		 echo json_encode($response);

	 }    else {

         echo "Invalid Request";

     }

 } else {

     echo "Access Denied";

 }



?>



