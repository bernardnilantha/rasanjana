<?php include("connection.php");
 	  include("otp.php");
	  include_once '../lib/sms/SmsSender.php';
	  define('iSMS_URL', 'https://api.dialog.lk/sms/send');
	  define('mSMS_URL', 'https://api.mspace.lk/sms/send');//https://api.mspace.lk/sms/send
     $protocol = strtolower( substr( $_SERVER[ 'SERVER_PROTOCOL' ], 0, 5 ) ) == 'https' ? 'https' : 'http'; 
	 $file_path = $protocol.'://'.$_SERVER['SERVER_NAME'] . dirname($_SERVER['REQUEST_URI']).'/';
 
	  
   
   
	
  
function logFile($rtn){
		$f=fopen("log.txt","a");
		fwrite($f, $rtn . "\n");
		fclose($f);
	}
	
	 
    function get_apply_count($job_id)
    {
      global $mysqli;
      $qry_apply="SELECT COUNT(*) as num FROM tbl_apply WHERE job_id='".$job_id."'";
      $total_apply= mysqli_fetch_array(mysqli_query($mysqli,$qry_apply));
      $total_apply = $total_apply['num'];
      if($total_apply)
      {
        return $total_apply;
      }
      else
      {
        return 0;
      }
      
    }
	
 // {"salt":"127","sign":"734b1b49acdea2aad71d202fee1e5ae5","method_name":"user_register","email":"0777125125","phone":"0777125125"}
	$get_method =  json_decode(file_get_contents('php://input'), true);
	//$data_arr = json_decode(urldecode(base64_decode($data_json)),true);
	 if($get_method['method_name']=="otp_request") {	
	 
	 
	 
	 	
					
					
  		$mobile 	= substr($get_method['mobile'],-9,9);
		$app_key 	= $get_method['app_key'];
		$applicationHash= $get_method['applicationHash'];
		$app_url		= $get_method['app_url'];
	    $qry = "SELECT * FROM otp_apps WHERE app_key = '".$app_key."'"; 
		$result = mysqli_query($mysqli,$qry);
		$row = mysqli_fetch_assoc($result);
		//logfile($qry );
		$iapp_id  		= $row ['iApp_Id'];
		$iapp_password 	= $row ['iApp_password'];
		$mapp_id  		= $row ['mApp_Id'];
		$mapp_password 	= $row ['mApp_password'];
		
		$user_id  		= $row ['user_id'];
		$category  		= $row ['category'];
		
		$sqled		=	"SELECT MAX(due_date) AS last_due_date, SUM(billed-paid) AS due FROM payment_ledger  
					WHERE user_id='$user_id' AND approved='1'";
				
		$ereplyd		=	mysqli_query($mysqli,$sqled);
		$rowed 		= 	mysqli_fetch_array($ereplyd); 
 		$last_due_date			=	$rowed['last_due_date'];
 		$current_due			=	$rowed['due'];
		//logfile(date("Y-m-d h:i A")." ".$sqled.$current_due);

		if($current_due>0  && ((time()-strtotime($last_due_date))/(60*60*24))>0){
			if($category==1){
				$iapp_id  		= "APP_058900";
	
				$iapp_password 	= "9e96f4755927e38a76b615dccccb37b2";
		
				$mapp_id  		= "APP_005650";
				
				$mapp_password 	= "b76b6d6e1e02ae0e660c6349ec252ae8";

			} else {
				$iapp_id  		= "";
				$iapp_password 	= "";
				$mapp_id  		= "";
				$mapp_password 	= "";
			}
		}
		//$dbi = db_connecti();
		
		//$rowid_last = mysqli_insert_id($dbi);
		if(  substr($mobile,0,2)=="70" ||  substr($mobile,0,2)=="71"){
			$app_id 		= $mapp_id;
			$app_password 	= $mapp_password;
			$otp_url 		= mOTP_URL;
	
		} else {
			$app_id 		= $iapp_id;
			$app_password 	= $iapp_password;
			$otp_url 		= iOTP_URL;	
		}
		 $fullmobile ="0".$mobile;
		 $sql_insert = "insert into otp_mobile ( mobile, ad_id, datetime, otp, apikey ) " .
			"values ('$fullmobile', '$app_id', '".time()."', '','$app_key')";
		mysqli_query($mysqli,$sql_insert) ;


		//logfile(date("Y-m-d h:i A")." ".$otp_url.$mobile.$app_id.$app_password);

		$otp = new Subscription($otp_url);
		$res = $otp->RegOtp($app_id,$app_password,"1.0","tel:94".$mobile,$applicationHash,"https://topjob.today/"); 
		logfile(date("Y-m-d h:i A")." ".$mobile.$res);

		$response = json_decode($res, true);
		$set['APP_OTP'][]=$response;
		
		
		header( 'Content-Type: application/json; charset=utf-8' );
	    echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
		die();
		} else if($get_method['method_name']=="otp_verify") {	
			$referenceNo 	= $get_method['referenceNo'];
			$otp 			= $get_method['otp'];
			$app_key 		= $get_method['app_key'];
				
			
			$qry = "SELECT * FROM otp_apps WHERE app_key = '".$app_key."'"; 
			$result = mysqli_query($mysqli,$qry);
			$row = mysqli_fetch_assoc($result);
			
			$iapp_id  		= $row ['iApp_Id'];
			$iapp_password 	= $row ['iApp_password'];
			$mapp_id  		= $row ['mApp_Id'];
			$mapp_password 	= $row ['mApp_password'];
			$user_id  		= $row ['user_id'];
			
			$category  		= $row ['category'];
		
		$sqled		=	"SELECT MAX(due_date) AS last_due_date, SUM(billed-paid) AS due FROM payment_ledger  
					WHERE user_id='$user_id' AND approved='1'";
				
		$ereplyd		=	mysqli_query($mysqli,$sqled);
		$rowed 		= 	mysqli_fetch_array($ereplyd); 
 		$last_due_date			=	$rowed['last_due_date'];
 		$current_due			=	$rowed['due'];
		if($current_due>0  && ((time()-strtotime($last_due_date))/(60*60*24))>0){
			if($category==1){
				$iapp_id  		= "APP_058900";
	
				$iapp_password 	= "9e96f4755927e38a76b615dccccb37b2";
		
				$mapp_id  		= "APP_005650";
		
				$mapp_password 	= "b76b6d6e1e02ae0e660c6349ec252ae8";
			} else {
				$iapp_id  		= "";
				$iapp_password 	= "";
				$mapp_id  		= "";
				$mapp_password 	= "";
			}
		}
			
			if(  substr($referenceNo,0,4)=="9470" ||  substr($referenceNo,0,4)=="9471"){
				$app_id 		= $mapp_id;
				$app_password 	= $mapp_password;
				$verify_url 	= mVERIFY_URL;	
			} else {
				$app_id 		= $iapp_id;
				$app_password 	= $iapp_password;
				$verify_url 	= iVERIFY_URL;	
			}
			$verify = new Subscription($verify_url);
			$res = $verify->VerfyOtp($app_id,$app_password,$referenceNo,$otp);
//logFile($app_id.$app_password.$referenceNo.$otp.$res);
			//$otp = new Subscription($otp_url);
			//$res = $otp->RegOtp($app_id,$app_password,"1.0","tel:94".$mobile,$applicationHash,"https://topjob.today/"); 
			
			$response = json_decode($res, true);
			$set['APP_OTP'][]=$response;
			 
			
			header( 'Content-Type: application/json; charset=utf-8' );
			echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
			die();
		} else if($get_method['method_name']=="send_OTP_SMS") {	//iSUBSCRIPTION
			$referenceNo 	= $get_method['referenceNo'];
			$mobile	 		= $get_method['mobile'];
			$msg 			= $get_method['msg'];
			$app_key 		= $get_method['app_key'];
				
			
			$qry = "SELECT * FROM otp_apps WHERE app_key = '".$app_key."'"; 
			$result = mysqli_query($mysqli,$qry);
			$row = mysqli_fetch_assoc($result);
			
			$iapp_id  		= $row ['iApp_Id'];
			$iapp_password 	= $row ['iApp_password'];
			$iSMS  		= $row ['iSMS'];
			$mSMS  		= $row ['mSMS'];
			$mapp_id  		= $row ['mApp_Id'];
			$mapp_password 	= $row ['mApp_password'];
			$user_id  		= $row ['user_id'];
			
			$category  		= $row ['category'];
		
		 
			
			if(  substr($mobile,1,2)=="70" ||  substr($mobile,1,2)=="71"){
				$app_id 		= $mapp_id;
				$app_password 	= $mapp_password;
				$verify_url 	= mVERIFY_URL;	
				$sms_url 		= mSMS_URL;
				$sub_url 		= mSUB_URL;


				$sms_name = $mSMS;	
			} else {
				$app_id 		= $iapp_id;
				$app_password 	= $iapp_password;
				$verify_url 	= iVERIFY_URL;	
				$sms_url 	= iSMS_URL;
				$sms_name = $iSMS;
				$sub_url 		= iSUB_URL;

			}
			$sender = new SmsSender($sms_url); 
//logfile("send_OTP_SMS".$sms_url.$referenceNo);

			$encoding = "0"; 
			$version =  "1.0"; 
			$destinationAddresses =array($referenceNo);
			$res = $sender->sms($msg,$destinationAddresses ,$app_password,$app_id,$sms_name,0, 0, $encoding, $version, "");  
			
			$response = json_decode($res, true);
                        //logfile("tel".$destinationAddresses."send_OTP_SMS".$response.$res);

			//$sub = new Subscription($sub_url); 

			//$subres = $sub->getStatus($app_id, $app_password, $referenceNo);
			//$subresponse = json_decode($subres, true);
			//logfile("SMS".$referenceNo.$res);



			$set['APP_OTP'][]=$response;
			 
			
			header( 'Content-Type: application/json; charset=utf-8' );
			echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
			die();
		} else if($get_method['method_name']=="send_unreg") {	//iSUBSCRIPTION
			$subscriberid		= $get_method['subscriber_id'];
			$mobile	 		= $get_method['mobile'];
			$app_key 		= $get_method['app_key'];
				
			
			$qry = "SELECT * FROM otp_apps WHERE app_key = '".$app_key."'"; 
			$result = mysqli_query($mysqli,$qry);
			$row = mysqli_fetch_assoc($result);
			
			$iapp_id  		= $row ['iApp_Id'];
			$iapp_password 	= $row ['iApp_password'];
			$iSMS  		= $row ['iSMS'];
			$mSMS  		= $row ['mSMS'];
			$mapp_id  		= $row ['mApp_Id'];
			$mapp_password 	= $row ['mApp_password'];
			$user_id  		= $row ['user_id'];
			
			$category  		= $row ['category'];
		
		 
			
			if(  substr($mobile,1,2)=="70" ||  substr($mobile,1,2)=="71"){
				$app_id 		= $mapp_id;
				$app_password 	= $mapp_password;
				$verify_url 	= mVERIFY_URL;	
				$sms_url 		= mSMS_URL;
				$sub_url 		= mSUBSCRIPTION_URL;


				$sms_name = $mSMS;	
			} else {
				$app_id 		= $iapp_id;
				$app_password 	= $iapp_password;
				$verify_url 	= iVERIFY_URL;	
				$sms_url 	= iSMS_URL;
				$sms_name = $iSMS;
				$sub_url 		= iSUBSCRIPTION_URL;

			}
			 
			logfile("send_unreg".$mobile.$subscriberid);

			 

			$unsub 	= new Subscription($sub_url); 

			$unsub_res 	= $unsub->UnregUser($app_id, $app_password,"1.0", $subscriberid);
			$response	= json_decode($unsub_res, true);
			logfile("UnregUser".$response);



			$set['APP_OTP'][]=$response;
			 
			
			header( 'Content-Type: application/json; charset=utf-8' );
			echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
			die();
		} else if($get_method['method_name']=="get_status") {	 
			$mobile	 		= $get_method['mobile']; 
			$mask_number	= $get_method['mask'];
			$app_key 		= $get_method['app_key'];
				
			//logfile("get_status".$mask_number);

			$qry = "SELECT * FROM otp_apps WHERE app_key = '".$app_key."'"; 
			$result = mysqli_query($mysqli,$qry);
			$row = mysqli_fetch_assoc($result);
			
			$iapp_id  		= $row ['iApp_Id'];
			$iapp_password 	= $row ['iApp_password'];
			$iSMS  		= $row ['iSMS'];
			$mSMS  		= $row ['mSMS'];
			$mapp_id  		= $row ['mApp_Id'];
			$mapp_password 	= $row ['mApp_password'];
			$user_id  		= $row ['user_id'];
			
			$category  		= $row ['category'];
		
		 
			
			if(  substr($mobile,1,2)=="70" ||  substr($mobile,1,2)=="71"){
				$app_id 		= $mapp_id;
				$app_password 	= $mapp_password; 
				$sub_url 		= mSUB_URL; 
			} else {
				$app_id 		= $iapp_id;
				$app_password 	= $iapp_password;  
				$sub_url 		= iSUB_URL;

			} 

			$sub = new Subscription($sub_url); 

			$subres = $sub->getStatusNew($app_id, $app_password, $mask_number,$sub_url);
			$subresponse = json_decode($subres, true);
			//logfile("Status".$app_id.$app_password.$mask_number.$qry);



			$set['APP_OTP'][]=$subresponse;
			 
			
			header( 'Content-Type: application/json; charset=utf-8' );
			echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
			die();
		}

	 
?>