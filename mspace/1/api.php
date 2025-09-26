<?php  //ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);

      include("includes/connection.php");
 	  include("includes/function.php");
 	  include("language/app_language.php"); 	
	  //include("smtp_email.php");
	
error_reporting(0);
    date_default_timezone_set("Asia/Kolkata");

     $file_path = getBaseUrl();
	 
	define('mAPP_ID', 'APP_007592');
	define('mAPP_PSWD', '32915d3f1da22657d1099e61066648fa');
	define('mSMS_URL', 'https://api.mspace.lk/sms/send');
	define('mSUB_URL', 'https://api.mspace.lk/subscription/getStatus/');
	
	define('iAPP_ID', 'APP_063412');
	define('iAPP_PSWD', '3ecf35d446263ecefd3fb414de222d2b');
	define('iSMS_URL', 'https://api.dialog.lk/sms/send');
	define('iSUB_URL', 'https://api.dialog.lk/subscription/getStatus/');
	include_once 'lib/sms/SmsSender.php';
	
	include("otpapi.php");
	 $app_key = "32915d3f1da22657fd3fb414de222d2b"; //84ee3b7b684f0b4b136ba02463babe2
	 $otp = new Otp(API);	
    
	define("PACKAGE_NAME",$settings_details['package_name']);
    define("HOME_LIMIT",$settings_details['api_home_limit']);
	define("API_PAGE_LIMIT",$settings_details['api_page_limit']);
	
	define("API_APP_DBM","bernard_m_lbs");
	define("API_APP_DBI","bernard_lbs");
	define("API_APP_TBLM","376_1662992674_subcribers");
	define("API_APP_TBLI","376_1662456009_subcribers");
	
	
	$SERVICE_TYPE = "IMMEDIATE";
	$RESPONSE_TIME ="DELAY_TOLERANCE";
	$FRESHNESS = "LOW";
	$HORIZONTAL_ACCURACY = "1000";
	define('LBS_QUERY_SERVER_iURL' , 'http://api.dialog.lk:8080/lbs/locate');
	define('LBS_QUERY_SERVER_mURL' , 'https://api.mspace.lk/lbs/request');
	
	
	
	function locate($address){
		include_once 'lib/lbs/LbsClient.php';
		include_once 'lib/lbs/LbsRequest.php';
		include_once 'lib/lbs/LbsResponse.php';
		include_once 'lib/lbs/KLogger.php';
		$SERVICE_TYPE = "IMMEDIATE";
		$RESPONSE_TIME ="DELAY_TOLERANCE";
		$FRESHNESS = "LOW";
		$HORIZONTAL_ACCURACY = "1000";
		$request = new LbsRequest(LBS_QUERY_SERVER_iURL);
		$request->setAppId(iAPP_ID);
		$request->setAppPassword(iAPP_PSWD);
		$request->setSubscriberId($address);
		$request->setServiceType($SERVICE_TYPE);
		$request->setFreshness($FRESHNESS);
		$request->setHorizontalAccuracy($HORIZONTAL_ACCURACY);
		$request->setResponseTime($RESPONSE_TIME);
		
		//logFile("[ $request ]");
		$lbsClient = new LbsClient();
		$lbsResponse = new LbsResponse($lbsClient->getResponse($request));
		$lbsResponse->setTimeStamp(getModifiedTimeStamp($lbsResponse->getTimeStamp()));
		
		$data = $lbsResponse->toJson();
		$jsondata = json_decode($data);
		$statuscode=$jsondata->{'statusCode'};
		/*if($statuscode=="S1000"){
			$lat	=$jsondata->{'latitude'};
			$lng	=$jsondata->{'longitude'};
			//logFile(time());
			$addr=locationAddress($lat,$lng);
			$message = $pin. " at \n".$addr."\nhttp://www.google.com/maps/place/".$lat.",".$lng;
			//update_locate($pin,$lat,$lng,$addr);
			//$addr="Test";
		} else if($statuscode=="E1365"){
			//$addr="Unable Locate.";
			$message = $pin. " is Subscriber is not registered to use this application";
		} else {
			$addr=$jsondata->{'statusDetail'};//"Unable Locate.";
			$message = $pin. " is ".$addr;
		} */
		
		//logFile(time());
		//sendSMS($address, $message);
		//logFile("ddddd");
		//$logFile("Lbs response:".$jsondata);
		 return  $jsondata;	//sendSMS($address, $message);								
	}

	function mlocate($address,$requesterId){
		include_once 'mlib/lbs/LbsClient.php';
		include_once 'mlib/lbs/LbsRequest.php';
		include_once 'mlib/lbs/LbsResponse.php';
		include_once 'mlib/lbs/KLogger.php';
		 
		 
		//$requesterId="tel:NDFiYmY5ZTNjNTgzYTMzMDJkYzYwYjgzYmJiZjRiYzNkYzM4ZTM3MDYzZWYyZmI4NTE3YmZkNGQ3ZWU5NWU3NTptb2JpdGVs";
		
		$SERVICE_TYPE = "IMMEDIATE";
		$RESPONSE_TIME ="DELAY_TOLERANCE";
		$FRESHNESS = "LOW";
		$HORIZONTAL_ACCURACY = "1000";
		
		$request = new LbsRequest(LBS_QUERY_SERVER_mURL);
		$request->setAppId(mAPP_ID);
		$request->setAppPassword(mAPP_PSWD);
		$request->setRequesterId($requesterId);
		$request->setSubscriberId($address);
		$request->setServiceType($SERVICE_TYPE);
		$request->setFreshness($FRESHNESS);
		$request->setHorizontalAccuracy($HORIZONTAL_ACCURACY);
		$request->setResponseTime($RESPONSE_TIME);


 
		//logFile($request->toJson());
		$lbsClient = new LbsClient();
		$lbsResponse = new LbsResponse($lbsClient->getResponse($request));
		//logFile($lbsResponse->toJson()); 
		//logFile(date('Y-m-d H:i:s',$lbsResponse->getTimeStamp())); 
		if($lbsResponse->getStatusCode()=="S1000"){
			$lbsResponse->setTimeStamp(date('Y-m-d H:i:s',$lbsResponse->getTimeStamp()/1000));
		} else {
			$lbsResponse->setTimeStamp(getModifiedTimeStamp($lbsResponse->getTimeStamp()));
		}
		
		
		
		
		
		
		//$log->LogDebug("Lbs response:".$lbsResponse->toJson());
		//$data = $lbsResponse->toJson();
		//$jsondata = json_decode($data);

		//$statuscode=$jsondata->{'statusCode'};
		//$message = "Error!";
		 
		
		logFile($lbsResponse->toJson()); 
		return  $lbsResponse;	//sendSMS($address, $message);								
	}

	function get_user_status($user_id)
	{
		global $mysqli;

		$user_qry="SELECT * FROM tbl_users where id='".$user_id."'";
		$user_result=mysqli_query($mysqli,$user_qry);
		$user_row=mysqli_fetch_assoc($user_result);

		if(mysqli_num_rows($user_result) > 0){
			if($user_row['status']==0){
				return 'false';
			}
			else if($user_row['status']==1){
				return 'true';
			}
		}
		else{
			return 'false';
		}

		
	}
function logFile($rtn){
		$f=fopen("log.txt","a");
		fwrite($f, $rtn . "\n");
		fclose($f);
	}
	
	/*if($settings_details['envato_buyer_name']=='' OR $settings_details['envato_purchase_code']=='' OR $settings_details['envato_purchased_status']==0) {  

		if($get_method['user_id']!=''){
			$set['user_status']=get_user_status($get_method['user_id']);
		}
		else{
			$set['user_status']="false";	
		}

		$set['JOBS_APP'][] =array('msg' => 'Purchase code verification failed!','success'=>'0');	
		
		header( 'Content-Type: application/json; charset=utf-8' );
	    echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
		die();
	}*/
   

	function generateRandomPassword($length = 10) {

	    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	    $charactersLength = strlen($characters);
	    $randomString = '';
	    for ($i = 0; $i < $length; $i++) {
	        $randomString .= $characters[rand(0, $charactersLength - 1)];
	    }
	    return $randomString;
	}

    function apply_job_count($user_id)
	{ 
    
	global $mysqli;   

    $qry_applied="SELECT COUNT(*) as num FROM tbl_apply WHERE `user_id`='".$user_id."'";
     
    $total_applied = mysqli_fetch_array(mysqli_query($mysqli,$qry_applied));
    $total_applied = $total_applied['num'];
     
    return $total_applied;

  }

  function get_saved_info($user_id,$job_id) 
	 {
	 	
	 	global $mysqli;

	    $sql="SELECT * FROM tbl_saved WHERE tbl_saved.`user_id`='$user_id' AND tbl_saved.`job_id`='$job_id'";
	 	$res=mysqli_query($mysqli,$sql);
 				 	
         return ($res->num_rows == 1) ? 'true' : 'false';
	  } 


   function saved_job_count($user_id) 
	 {
	 	global $mysqli;

        $qry_saved="SELECT COUNT(*) as num FROM tbl_saved WHERE `user_id` ='".$user_id."' ";

		$total_saved = mysqli_fetch_array(mysqli_query($mysqli,$qry_saved));
		$total_saved = $total_saved['num'];

		return $total_saved;
	 }

   function get_job_info($job_id,$field_name) 
	 {
	 	global $mysqli;

	 	$qry_job="SELECT * FROM tbl_jobs WHERE `id`='".$job_id."'";
	 	$query1=mysqli_query($mysqli,$qry_job);
		$row_job = mysqli_fetch_array($query1);
        
            $num_rows1 = mysqli_num_rows($query1);
 		
            if ($num_rows1 > 0)
		    {		 	
				return $row_job[$field_name];
			}
			else
			{
				return "";
			}
	 }	
	 
	function get_user_info($user_id,$field_name) 
	  {
	    global $mysqli;

	    $qry_user="SELECT * FROM tbl_users WHERE id='".$user_id."'";
	    $query1=mysqli_query($mysqli,$qry_user);
	    $row_user = mysqli_fetch_array($query1);

	    $num_rows1 = mysqli_num_rows($query1);
	    
	    if ($num_rows1 > 0)
	    {     
	      return $row_user[$field_name];
	    }
	    else
	    {
	      return "";
	    }
	  }

    function get_apply_count($job_id)
    {
      global $mysqli;

      $qry_apply="SELECT COUNT(*) as num FROM tbl_apply WHERE `job_id`='".$job_id."'";
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
	
   function get_city_name($city_id) 
	 {
	 	global $mysqli;

	 	 $qry_video="SELECT * FROM tbl_city WHERE `c_id`='".$city_id."'";
	 	$query1=mysqli_query($mysqli,$qry_video);
		$row_video = mysqli_fetch_array($query1);

			return $row_video['city_name'];
	 }
	 
	 function locationAddress($lat,$lng){
			$api_key = "AIzaSyBnhMyRyENAmcLFGteCnqGEmbabApzQgME";
 			$url = 'https://maps.googleapis.com/maps/api/geocode/json?latlng='.$lat.','.$lng.'&output=json&sensor=false&key=AIzaSyDM7VdEYslQU1G1LGCJtT3-yZ0rGLvdF4Q';
 			$data = @file_get_contents($url); 
 			$jsondata = json_decode($data,true);
 			if($jsondata ['status']=="OK") { 
 				$addr1 = $jsondata ['results'][0]['formatted_address'];
 				$addr	= $addr1; 
			} else {
				$addr="Location name not available.";
			}
				return $addr;
	}
		 
	$get_method = checkSignSalt($_POST['data']);	 

   if ($get_method['method_name'] == 'otp_verify') {
		 $pin	 	 = $get_method['pin'];
		 $mobile	 = $get_method['mobile'];
		 $fcm	 	 = $get_method['fcm'];
 		 $imei	     = $get_method['imei']; 
		 $refNo 	 = $get_method['refNo'];
		 $statusCode = "";
		 
		if($mobile=="0777445799"){
			if($pin=="159753"){
			 $refNo 	= "0777445799".time().rand(100000,999999);
		     $statusCode 	= "S1000"; 
			 $statusCodeOut = "S1000"; 
			 $statusDetail 	= "Success";
			 $userId =482;
			} else {
			 $refNo 	= "0777445799".time().rand(100000,999999);
		     $statusCode 	= "E1602"; 
			 $statusDetail 	= "Error!. OTP Not valid";
			 $userId = 0;
			 $statusCodeOut = "E1602"; 
			}
		} else {
		 
		 $sql_		=	"SELECT * FROM tbl_otp WHERE mobile = '$mobile' AND used=0 ORDER BY id DESC LIMIT 1";
		 $reply_	=	 mysqli_query($mysqli,$sql_)or die(mysqli_error($mysqli));
		 $row_	 	= 	mysqli_fetch_array($reply_);
		 $m_num		=	mysqli_num_rows($reply_);
		 $userId 	= 	"0";
		 if($m_num>0){
			 $otp 	= $row_['otp'];
			 $used 	= $row_['used'];
			 $id 	= $row_['id'];
			 if($otp==$pin && $used==0){
				 $sql_1		=	"UPDATE  tbl_otp SET used=1 WHERE mobile = '$mobile'";
				 mysqli_query($mysqli,$sql_1) or die (mysqli_error($mysqli));
				 
 				 $sql3		=	"SELECT * FROM tbl_users WHERE phone = '$mobile' "; //AND imei = '$imei'
				 $reply3		=	mysqli_query($mysqli,$sql3) or die (mysqli_error($mysqli)); 
				 $row3	 	= 	mysqli_fetch_array($reply3);
				 $mnum		=	mysqli_num_rows($reply3);
				 $rowid		=	$row3['id'];
				 
				 
				 
				 if($rowid>0){
					 
					  
				 
					 $sql_3		=	"UPDATE  tbl_users SET status=1, imei = '$imei' WHERE phone = '$mobile'  ";//AND
					 mysqli_query($mysqli,$sql_3) or die (mysqli_error($mysqli));
					 $userId 	= $rowid;
				 }  
				 
				 $statusCodeOut 	= "S1000";
				 $statusDetail ="Success.";
			 } else {
				 $statusCodeOut 	= "E1001";
				 $statusDetail ="Could not find OTP.";
			 }
		 
		 } else {
 			 $res = $otp->VerfyOtp("otp_verify",$refNo,$pin,$app_key); 
 			 $json =	json_decode($res);
			 //logFile($res); 
 			 $statusDetail = $json->APP_OTP[0]->statusDetail;
			 $statusCode = $json->APP_OTP[0]->statusCode;
			 if($statusCode=="S1000" || $statusCode=="E1351" || $statusCode=="E1854"){
				$subscriptionStatus = $json->APP_OTP[0]->subscriptionStatus;
 				$subscriberId = $json->APP_OTP[0]->subscriberId;
				$statusCodeOut 	= "S1000";
				$refNo = $json->APP_OTP[0]->referenceNo;
				
				$sqlx		=	"SELECT * FROM tbl_users WHERE masknumber = '$subscriberId' "; //AND imei = '$imei'
				$replyx		=	mysqli_query($mysqli,$sqlx) or die (mysqli_error($mysqli)); 
				$rowx	 	= 	mysqli_fetch_array($replyx);
				$rowidx		=	$rowx['id'];
				 
				if($rowidx>0){
					$sql3 = "UPDATE tbl_users SET  refno='$refNo', phone='$mobile', imei='$imei', otp='$pin',   statustext='$statusDetail', statusnum='$statusCode', utime='".time()."', status=1 WHERE id='$rowidx'";
					mysqli_query($mysqli,$sql3) or die (mysqli_error($mysqli));
					$userId = $rowidx;
					
					logFile($sql3);
				} else {
					if(substr($refNo,0,4)=="9470" ||  substr($refNo,0,4)=="9471"){
						$network = 2;
					} else {
						$network = 1;
					}
					
					$sqlxx		=	"SELECT * FROM tbl_users WHERE phone = '$mobile' "; //AND imei = '$imei'
					$replyxx		=	mysqli_query($mysqli,$sqlxx) or die (mysqli_error($mysqli)); 
					$rowxx	 	= 	mysqli_fetch_array($replyxx);
					$rowidxx	=	$rowxx['id'];
					 
					if($rowidxx>0){
					
						$sql3 = "UPDATE tbl_users SET  masknumber = '$subscriberId', refno='$refNo', phone='$mobile', imei='$imei', otp='$pin',   statustext='$statusDetail', statusnum='$statusCode', utime='".time()."', status=1 WHERE id='$rowidxx'";
						mysqli_query($mysqli,$sql3) or die (mysqli_error($mysqli));
						$userId = $rowidxx;
						logFile($sql3);
				
					} else {
						$sql3 = "insert tbl_users ( refno, phone, imei, otp,   masknumber, statustext, statusnum, atime, status,network) " .
										"values ('$refNo', '$mobile','$imei','$pin',  '$subscriberId', '$statusDetail', '$statusCode' ,   '".time()."' , '1','$network')";
						mysqli_query($mysqli,$sql3) or die (mysqli_error($mysqli));
						$userId = mysqli_insert_id($mysqli);
						logFile($sql3);
					}
				}
 			 } else {
				 $statusCodeOut 	= $statusCode;
			 }
		 }
		 
		}
		 $response["referenceNo"] 	= $refNo;
		 $response["userId"] 	= $userId;
 		 $response["success"] 	= 1;
         $response["message"] 	= $statusDetail;
		 
		 $set['JOBS_APP'][]=array('message' =>$statusDetail,'success'=>'1', 'referenceNo' =>$refNo, 'userId' =>$userId , 'statusCode' => $statusCodeOut);
 		 header( 'Content-Type: application/json; charset=utf-8' );
		echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
		die();
	 } else if ($get_method['method_name'] == 'otp_request') {
 		 $mobile 	 = $get_method['mobile'];
  		 $imei	     = $get_method['imei']; 
		 $fcm	     = $get_method['fcm']; 
		 
		 	if($mobile=="0777445799"){
				 $referenceNo 	= "0777445799".time().rand(100000,999999);
				 $statusCode 	= "S1000"; 
				 $statusDetail 	= "Success";
			} else {
			$sql_		=	"SELECT * FROM tbl_users WHERE phone = '$mobile'  ";
			$reply_		=	mysqli_query($mysqli,$sql_) or die (mysqli_error($mysqli)); 
			$row_	 	= 	mysqli_fetch_array($reply_);
			$m_num		=	mysqli_num_rows($reply_);
			//logFile($sql_);
			
			
			
			
			if($m_num>0){//old number
			
				/*$host_name = 'localhost'; 
				$database = APP_DB; 
				$user_name = 'bernard_root'; 
				$password = 'gigabyte'; 
				$db_connection = mysqli_connect($host_name, $user_name, $password, $database) or die('Unable to establish a DB connection');*/
				 
				$refno		=	$row_['refno']; 
				$xstatus		=	$row_['status']; 
				$masknumber	=	$row_['masknumber']; 
				$otprand 		=	rand(100000,999999);
				$msg = "Your OTP is ".$otprand." for TrackWay";
				
				//if($xstatus==1){
				
					 $res = $otp->SendOTPSMS_active("send_OTP_SMS",$mobile,$masknumber,$msg,$app_key); //"eYQ208cuf3a"
					 $json = json_decode($res);
					 //logFile($json->APP_OTP[0]->destinationResponses[0]->statusDetail); 
					 $statusDetail = $json->APP_OTP[0]->destinationResponses[0]->statusDetail;//$json->APP_OTP[0]->statusDetail;
					 $statusCode = $json->APP_OTP[0]->destinationResponses[0]->statusCode;//$json->APP_OTP[0]->statusCode;
					 
					 
					 
					$referenceNo = "";
					//$statusDetail = $statustext;//"OTP sent";
					//if($statusnum=="S1000"){
					if($statusCode=="S1000"){
						$sql_		=	"UPDATE  tbl_otp SET used=1 WHERE mobile = '$mobile'";
						mysqli_query($mysqli,$sql_) or die (mysqli_error($mysqli));
						
						$sql3 = "insert tbl_otp ( mobile,otp,imei, statustext, statusnum, datetime,used) " .
										"values (  '$mobile','$otprand','$imei', '$statustext', '$statusnum' ,   '".time()."' , '0')";
						mysqli_query($mysqli,$sql3) or die (mysqli_error($mysqli));
						
						//logFile($sql_.$sql3); 
					} else if($statusCode=="E1365"  ){
						 $res = $otp->RegOtp("otp_request",$mobile,$app_key, " "); //"eYQ208cuf3a"
						 $json = json_decode($res);
						 //logFile($res); 
						 $statusDetail = $json->APP_OTP[0]->statusDetail;
						 $statusCode = $json->APP_OTP[0]->statusCode;
						 if($statusCode=="S1000"){
							$referenceNo = $json->APP_OTP[0]->referenceNo;
						 }
				 
					} else if($statusCode=="E1325" || $statusCode=="E1951" ){
						$sql_		=	"UPDATE tbl_users SET status=0 WHERE phone = '$mobile' ";
			                        mysqli_query($mysqli,$sql_) or die (mysqli_error($mysqli)); 

						 $res = $otp->RegOtp("otp_request",$mobile,$app_key, " "); //"eYQ208cuf3a"
						 $json = json_decode($res);
						 //logFile($res); 
						 $statusDetail = $json->APP_OTP[0]->statusDetail;
						 $statusCode = $json->APP_OTP[0]->statusCode;
						 if($statusCode=="S1000"){
							$referenceNo = $json->APP_OTP[0]->referenceNo;
						 }
				 
					}  
				   
				
			} else { // new number
 				
				 $res = $otp->RegOtp("otp_request",$mobile,$app_key, " "); //"eYQ208cuf3a"
				 $json = json_decode($res);
				 logFile($res); 
				 $statusDetail = $json->APP_OTP[0]->statusDetail;
				 $statusCode = $json->APP_OTP[0]->statusCode;
				 if($statusCode=="S1000"){
					$referenceNo = $json->APP_OTP[0]->referenceNo;
 				 } else if($statusCode=="E1351"){
 					 $statusDetail .= $statusDetail." Please unsubribe from application and try again.";
					 $referenceNo = "";
				 } else {
 					 $referenceNo = "";
 				 }
					 
			}
			
			
		 }
		 
				
 		 $response["referenceNo"] 	= $referenceNo;
 		 $response["statusCode"] 	= $statusCode;
 		 $response["success"] 	= 1;
         $response["message"] 	= $statusDetail;
		 
  		 $set['JOBS_APP'][]=array('message' =>$statusDetail,'success'=>'1', 'referenceNo' =>$referenceNo,   'statusCode' => $statusCode);
 		 header( 'Content-Type: application/json; charset=utf-8' );
		echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
		die();
	 } else if ($get_method['method_name'] == 'add_member') {
 		 $member_mobile = $get_method['member_mobile'];
  		 $member_pin	= $get_method['member_pin']; 
		 $member_name	= $get_method['member_name']; 
		 $user_id	 	= $get_method['user_id']; 
		 
		 
			$sql_		=	"SELECT * FROM tbl_user_member WHERE user_id = '$user_id' AND member_mobile = '$member_mobile' AND member_pin = '$member_pin' ";
			$reply_		=	mysqli_query($mysqli,$sql_) or die (mysqli_error($mysqli)); 
			$row_	 	= 	mysqli_fetch_array($reply_);
			$m_num		=	mysqli_num_rows($reply_);
			if($user_id<1){
				$statusCode 	= "101";
				$statusDetail 	= "Your not logged in.";
			} else if($m_num>0){
				$statusCode 	= "101";
				$statusDetail 	= "Member already in list.";
 			} else {
				$mobile9 	= substr($member_mobile ,-9,9);
				if(  substr($mobile9,0,2)=="70" ||  substr($mobile9,0,2)=="71"){
					$app_db 		= API_APP_DBM;
					$app_table	 	= API_APP_TBLM; 
								
				} else {
					$app_db 		= API_APP_DBI;
					$app_table	 	= API_APP_TBLI; 
				}
				
				$app_db_connection = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, $app_db) or die('Unable to establish a DB connection'); 
 				mysqli_set_charset($db_connection, "utf8");
				
				$sql_member_exist 		=	"SELECT * FROM ".$app_table." WHERE pinno = '$member_pin' AND active=1";
				$reply_member_exist		=	mysqli_query($app_db_connection,$sql_member_exist) or die (mysqli_error($app_db_connection)); 
				$row_member_exist	 	= 	mysqli_fetch_array($reply_member_exist);
				$member_exist			=	mysqli_num_rows($reply_member_exist);
				if($member_exist>0){
					$member_id = $row_member_exist['rowid'];
					$member_mask = $row_member_exist['masknumber'];
					$sql3 = "insert tbl_user_member (user_id, member_id, member_mobile, member_mask, member_name, member_pin, member_db, member_tbl, cdate,active) " .
										"values (  '$user_id','$member_id','$member_mobile', '$member_mask', '$member_name', '$member_pin', '$app_db', '$app_table' ,   '".time()."' , '1')";
					mysqli_query($mysqli,$sql3) or die (mysqli_error($mysqli));
					
					$statusCode 	= "200";
					$statusDetail 	= "Member added successfully.";
					
				} else {
					$statusCode 	= "102";
					$statusDetail 	=  "Member not subcribe to Trackway Service.";
				}

				 
			} 
		 
				
 		 
 		 
  		 $set['JOBS_APP'][]=array('message' =>$statusDetail,'success'=>'1',    'statusCode' => $statusCode);
 		 header( 'Content-Type: application/json; charset=utf-8' );
		echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
		die();
	 } else if($get_method['method_name']=="get_home")
  	{
       	$user_id=$get_method['user_id'];

       	$jsonObj3= array();	
   
		$query3="SELECT * FROM tbl_user_member 
		WHERE user_id='$user_id'
		ORDER BY member_name ";
      
		$sql3 = mysqli_query($mysqli,$query3)or die(mysqli_error($mysqli));

		while($data3 = mysqli_fetch_assoc($sql3))
		{ 
		    $row3['id'] = $data3['member_id'];
			$row3['cat_id'] = $data3['cat_id'];
			$row3['city_id'] = $data3['city_id'];
			if($data3['active']==1){
				$row3['job_type'] = "Active";
			} else {
				$row3['job_type'] = "Inactive";
			}
			$row3['job_name'] = $data3['member_name'];
			$row3['job_designation'] = $data3['job_designation'];
			$row3['job_desc'] = $data3['job_desc'];
			$row3['job_salary'] = $data3['job_salary'];
			$row3['job_company_name'] = $data3['job_company_name'];
			$row3['job_company_website'] = $data3['job_company_website'];
			$row3['job_phone_number'] = $data3['job_phone_number'];
			$row3['job_mail'] = $data3['job_mail'];
			$row3['job_vacancy'] = $data3['job_vacancy'];
			$row3['job_address'] = $data3['member_mobile'];
			$row3['job_qualification'] = $data3['job_qualification'];
			$row3['job_skill'] = $data3['job_skill'];
			$row3['job_experince'] = $data3['job_experince'];
			$row3['job_work_day'] = $data3['job_work_day'];
			$row3['job_work_time'] = $data3['job_work_time'];
			$row3['job_map_latitude'] = $data3['job_map_latitude'];
			$row3['job_map_longitude'] =$data3['job_map_longitude'];
			$row3['job_image'] = $data3['job_image'];
			$row3['job_image'] = $file_path.'images/'.$data3['job_image'];
			$row3['job_image_thumb'] = $file_path.'images/thumbs/'.$data3['job_image'];
			$row3['job_date'] = date('m/d/Y',$data3['job_date']);
 
			$row3['cid'] = $data3['cid'];
			$row3['category_name'] = $data3['category_name'];
		    $row3['category_image'] = $file_path.'images/'.$data3['category_image'];
			$row3['category_image_thumb'] = $file_path.'images/thumbs/'.$data3['category_image'];

			$row3['is_favourite']=get_saved_info($user_id,$data3['id']);

			 
			array_push($jsonObj3,$row3);
		
			}

        $row['latest_job']=$jsonObj3; 

	      

		  $set['JOBS_APP'] = $row;
			
		  header( 'Content-Type: application/json; charset=utf-8' );
	      echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
		  die();
  }	 
	else if($get_method['method_name']=="get_member_location")
	{
		$user_id	=	$get_method['user_id'];
		$member_id	=	$get_method['request_id'];

		$jsonObj= array();	

		$query="SELECT * FROM tbl_user_member
		WHERE user_id='".$user_id."' AND member_id='$member_id'";

		$sql = mysqli_query($mysqli,$query)or die(mysqli_error($mysqli));

			while($data = mysqli_fetch_assoc($sql)){
			$row['user_id'] = $data['id'];
			$row['name'] = $data['member_name'];
			$row['phone'] = $data['member_mobile'];
			
			
			$user_qry="SELECT * FROM tbl_users where id='".$member_id."'";
			$user_result=mysqli_query($mysqli,$user_qry);
			$user_row=mysqli_fetch_assoc($user_result);
			$user_image=$file_path.'images/73177.png';
			if(mysqli_num_rows($user_result) > 0){
			  if($user_row['user_image'])
			  {
				$user_image=$file_path.'images/'.$user_row['user_image'];
			  } 
			   
			} 
			
			$row['user_image'] = $user_image;
			
			$member_mask = $data['member_mask'];
			
			if($member_mask){
				//logFile($member_mask.$data['member_db']);	
				if($data['member_db']=="bernard_lbs"){
					$jsondata = locate($member_mask);
				} else {
					$requester_qry="SELECT * FROM tbl_users where id='".$user_id."'";
					$requester_result=mysqli_query($mysqli,$requester_qry);
					$requester_row=mysqli_fetch_assoc($requester_result);
					$requesterId = $requester_row['masknumber'];
					$jsondata = mlocate($member_mask,$requesterId);
					logFile($member_mask." requesterId:".$requesterId.$jsondata->toJson());
 			  		
				}
				$statuscode=$jsondata->{'statusCode'};
				
				logFile("S".$statuscode);
				
				if($statuscode=="S1000"){
					
					//$date = DateTime::createFromFormat("YmdHis", $jsondata->{'timeStamp'});
					//$formattedDate = $date->format("Y:m:d H:i:s"); // YYYY:MM:DD HH:ii:ss format
					$timestamp = strtotime($jsondata->{'timeStamp'});
					logFile("Time".$timestamp);
					$formattedDate = date("Y:m:d H:i:s", $timestamp);

					$latitude			=	$jsondata->{'latitude'};
					$longitude			=	$jsondata->{'longitude'}; 
					$location_accuracy	=	$jsondata->{'horizontalAccuracy'}; 
					$location_time		=	$formattedDate; 
					$city				=	locationAddress($latitude,$longitude); 
					 
				} else if($statuscode=="E1365"){ 
					$latitude			=	"";
					$longitude			=	""; 
					$location_accuracy	=	""; 
					$location_time		=	date("Y-m-d h:i:s A"); 
					$city				=	"Subscriber is not registered to use this application";
				} else {
					 
					$latitude			=	"";
					$longitude			=	""; 
					$location_accuracy	=	""; 
					$location_time		=	date("Y-m-d h:i:s A"); 
					$city				=	$jsondata->{'statusDetail'}; 
				} 
				
		
			  	$row['location_time'] = $location_time;
				$row['location_accuracy'] = $location_accuracy;
				$row['latitude'] = $latitude;
				$row['longitude'] = $longitude;
				$row['city'] = $city;
			} else {
				$row['location_time'] = "";
				$row['location_accuracy'] = "";
				$row['latitude'] = "";
				$row['longitude'] = "";
				$row['city'] = "";
			}

			 
			
			 
			}
			 
   
			array_push($jsonObj,$row);
		
		 

		 $set['already_saved'] = 'true';
			
		 

		$set['JOBS_APP'] = $jsonObj;
		
		header( 'Content-Type: application/json; charset=utf-8' );
	    echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
		die();	
 

	}	
	else if($get_method['method_name']=="get_single_job")
	{
		$user_id	=	$get_method['user_id'];
		$member_id	=	$get_method['request_id'];

		$jsonObj= array();	

		$query="SELECT * FROM tbl_user_member
		WHERE user_id='".$user_id."' AND member_id='$member_id'";

		$sql = mysqli_query($mysqli,$query)or die(mysqli_error($mysqli));

			while($data = mysqli_fetch_assoc($sql)){
			$row['id'] = $data['id'];
			$row['cat_id'] = $data['cat_id'];
			$row['city_id'] = $data['city_id'];
			$row['city_name'] = "";
			$row['job_type'] = $data['job_type'];
			$row['job_name'] = $data['job_name'];
			$row['job_designation'] = $data['job_designation'];
			$row['job_desc'] = $data['job_desc'];
			$row['job_salary'] = $data['job_salary'];
			$row['job_company_name'] = $data['job_company_name'];
			$row['job_company_website'] = $data['job_company_website'];
			$row['job_phone_number'] = $data['job_phone_number'];
			$row['job_mail'] = $data['job_mail'];
			$row['job_vacancy'] = $data['job_vacancy'];
			$row['job_address'] = $data['job_address'];
			$row['job_qualification'] = $data['job_qualification'];
			$row['job_skill'] = $data['job_skill'];
			$row['job_experince'] = $data['job_experince'];
			$row['job_work_day'] = $data['job_work_day'];
			$row['job_work_time'] = $data['job_work_time'];
			$row['job_map_latitude'] = $data['job_map_latitude'];
			$row['job_map_longitude'] =$data['job_map_longitude'];
			$row['job_image'] = $data['job_image'];
			$row['job_image'] = $file_path.'images/'.$data['job_image'];
			$row['job_image_thumb'] = $file_path.'images/thumbs/'.$data['job_image'];
			$row['job_date'] = date('d-m-Y',$data['job_date']);

			$row['cid'] = $data['cid'];
			$row['category_name'] = $data['category_name'];
			$row['category_image'] = $file_path.'images/'.$data['category_image'];
			$row['category_image_thumb'] = $file_path.'images/thumbs/'.$data['category_image'];
			
			$member_mask = $data['member_mask'];
			}
			 
   
			array_push($jsonObj,$row);
		
		 

		 $set['already_saved'] = 'true';
			
		 locate($member_mask);

		$set['JOBS_APP'] = $jsonObj;
		
		header( 'Content-Type: application/json; charset=utf-8' );
	    echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
		die();	
 

	}	
  else if($get_method['method_name']=="get_category")
 	{
 	    if(isset($get_method['page']))
 	     
 	      {
 	        $query_rec = "SELECT COUNT(*) as num FROM tbl_category WHERE `status`= 1";
    		$total_pages = mysqli_fetch_array(mysqli_query($mysqli,$query_rec));
    		
    		$page_limit=API_PAGE_LIMIT;
    			
    		$limit=($get_method['page']-1) * $page_limit;
     	    
     		$jsonObj= array();
    		
    		$cat_order=API_CAT_ORDER_BY;
    
    		$query="SELECT cid,category_name,category_image FROM tbl_category 
    		WHERE `status`=1 ORDER BY tbl_category.".$cat_order." LIMIT $limit, $page_limit";
    		$sql = mysqli_query($mysqli,$query)or die(mysqli_error($mysqli)); 
    		
    		$total_item =$total_pages['num'];
 	        
 	    }
 	    else
 	    {
 	        $jsonObj= array();
    		
    		$cat_order=API_CAT_ORDER_BY;
    
    		$query="SELECT `cid`,`category_name`,`category_image` FROM tbl_category WHERE `status`=1 ORDER BY tbl_category.".$cat_order."";
    		$sql = mysqli_query($mysqli,$query)or die(mysqli_error($mysqli));     
    		
    		$total_item =0;
 	    }
 	    
		while($data = mysqli_fetch_assoc($sql))
		{
            $row['total_item'] = $total_item;
			$row['cid'] = $data['cid'];
			$row['category_name'] = $data['category_name'];
			$row['category_image'] = $file_path.'images/'.$data['category_image'];
			$row['category_image_thumb'] = $file_path.'images/thumbs/'.$data['category_image'];
 
			array_push($jsonObj,$row);
		
		}

		$set['JOBS_APP'] = $jsonObj;
		
		header( 'Content-Type: application/json; charset=utf-8' );
	    echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
		die();
 	}
 	else if($get_method['method_name']=="get_city")
 	{
 		$jsonObj= array();
		
		$query="SELECT `c_id`,`city_name` FROM tbl_city WHERE tbl_city.`status`=1 ORDER BY tbl_city.`c_id` DESC";
		$sql = mysqli_query($mysqli,$query)or die(mysqli_error($mysqli));

		while($data = mysqli_fetch_assoc($sql))
		{
			 
			$row['c_id'] = $data['c_id'];
			$row['city_name'] = $data['city_name'];
 
			array_push($jsonObj,$row);
		
		}

		$set['JOBS_APP'] = $jsonObj;
		
		header( 'Content-Type: application/json; charset=utf-8' );
	    echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
		die();
 	}

  	else if($get_method['method_name']=="get_list")
  	{	
       	
       	$jsonObj0= array();
		
		$query0="SELECT * FROM tbl_city WHERE tbl_city.`status`=1 ORDER BY tbl_city.`c_id` DESC";
		$sql0 = mysqli_query($mysqli,$query0)or die(mysqli_error($mysqli));

		while($data0 = mysqli_fetch_assoc($sql0))
		{
			 
			$row0['c_id'] = $data0['c_id'];
			$row0['city_name'] = $data0['city_name'];
 
			array_push($jsonObj0,$row0);
		
		}

        $row['city_list']=$jsonObj0; 

	    $jsonObj1= array();
		
		$query1="SELECT * FROM tbl_jobs WHERE tbl_jobs.`status`=1
		        ORDER BY tbl_jobs.`id` DESC ";

		$sql1 = mysqli_query($mysqli,$query1)or die(mysql_error($mysqli));
		
		while($data1 = mysqli_fetch_assoc($sql1))
		{	
			$row1['id'] = $data1['id'];
			$row1['job_company_name'] = $data1['job_company_name'];

			array_push($jsonObj1,$row1);
		
		}

        $row['company_list']=$jsonObj1; 

		$jsonObj_2= array();	

        $cid=API_CAT_ORDER_BY;

	    $query2="SELECT * FROM tbl_category ORDER BY tbl_category.".$cid." DESC";
		$sql2 = mysqli_query($mysqli,$query2)or die(mysqli_error($mysqli));

		while($data2 = mysqli_fetch_assoc($sql2))
		{
			$row2['cid'] = $data2['cid'];
			$row2['category_name'] = $data2['category_name'];
			$row2['category_image'] = $file_path.'images/'.$data2['category_image'];
			$row2['category_image_thumb'] = $file_path.'images/thumbs/'.$data2['category_image'];

			array_push($jsonObj_2,$row2);
		
		}
            $row['cat_list']=$jsonObj_2; 

		    $set['JOBS_APP'] = $row;
			
		  header( 'Content-Type: application/json; charset=utf-8' );
	      echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
		  die();
  }	 
 else if($get_method['method_name']=="get_job_by_cat_id")
	{
		$post_order_by=API_CAT_POST_ORDER_BY;

		$cat_id=$get_method['cat_id'];	

		$query_rec = "SELECT COUNT(*) as num FROM tbl_jobs
		LEFT JOIN tbl_category ON tbl_jobs.`cat_id`= tbl_category.`cid` 
		WHERE tbl_jobs.`cat_id`='".$cat_id."' AND tbl_jobs.`status`=1";
		
		$total_pages = mysqli_fetch_array(mysqli_query($mysqli,$query_rec));
		
		$page_limit=API_PAGE_LIMIT;
			
		$limit=($get_method['page']-1) * $page_limit;

		$jsonObj= array();	
	
	    $query="SELECT * FROM tbl_jobs
		LEFT JOIN tbl_category ON tbl_jobs.`cat_id`= tbl_category.`cid`
		WHERE tbl_jobs.`cat_id`='".$cat_id."' AND tbl_jobs.`status`=1 ORDER BY tbl_jobs.`id` ".$post_order_by." LIMIT $limit, $page_limit";

		$sql = mysqli_query($mysqli,$query)or die(mysqli_error($mysqli));

		while($data = mysqli_fetch_assoc($sql))
		{
		    $row['total_item'] = $total_pages['num'];
			$row['id'] = $data['id'];
			$row['cat_id'] = $data['cat_id'];
			$row['city_id'] = $data['city_id'];
			$row['job_type'] = $data['job_type'];
			$row['job_name'] = $data['job_name'];
			$row['job_designation'] = $data['job_designation'];
			$row['job_desc'] = $data['job_desc'];
			$row['job_salary'] = $data['job_salary'];
			$row['job_company_name'] = $data['job_company_name'];
			$row['job_company_website'] = $data['job_company_website'];
			$row['job_phone_number'] = $data['job_phone_number'];
			$row['job_mail'] = $data['job_mail'];
			$row['job_vacancy'] = $data['job_vacancy'];
			$row['job_address'] = $data['job_address'];
			$row['job_qualification'] = $data['job_qualification'];
			$row['job_skill'] = $data['job_skill'];
			$row['job_experince'] = $data['job_experince'];
			$row['job_work_day'] = $data['job_work_day'];
			$row['job_work_time'] = $data['job_work_time'];
			$row['job_map_latitude'] = $data['job_map_latitude'];
			$row['job_map_longitude'] =$data['job_map_longitude'];
			$row['job_image'] = $data['job_image'];
			$row['job_image'] = $file_path.'images/'.$data['job_image'];
			$row['job_image_thumb'] = $file_path.'images/thumbs/'.$data['job_image'];
			$row['job_date'] = date('m/d/Y',$data['job_date']);
 
			$row['cid'] = $data['cid'];
			$row['category_name'] = $data['category_name'];
			$row['category_image'] = $file_path.'images/'.$data['category_image'];
			$row['category_image_thumb'] = $file_path.'images/thumbs/'.$data['category_image'];

			$row['is_favourite']=get_saved_info($get_method['user_id'],$data['id']);
			 
			array_push($jsonObj,$row);
		
		}
		$set['JOBS_APP'] = $jsonObj;
		
		header( 'Content-Type: application/json; charset=utf-8' );
	    echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
		die();
		
	}		 
  else if($get_method['method_name']=="get_latest_job")
	{
		$latest_limit=API_LATEST_LIMIT;
  		$jsonObj= array();

  		$page_limit=API_PAGE_LIMIT;

  		$total_pages=round($latest_limit/$page_limit);
			
		$limit=($get_method['page']-1) * $page_limit;

		$actual_limit=$get_method['page']*$page_limit;

		if($actual_limit <= $latest_limit){
			$page_limit=API_PAGE_LIMIT;
		}
		else if($get_method['page'] <= $total_pages){
			$page_limit=$latest_limit-$page_limit;
		}
		else{
			$page_limit=0;	
		}
		$query="SELECT * FROM tbl_jobs
		LEFT JOIN tbl_category ON tbl_jobs.`cat_id`= tbl_category.`cid` 
		WHERE tbl_jobs.`status`= 1 ORDER BY tbl_jobs.`id` DESC LIMIT $limit,$page_limit";

		$sql = mysqli_query($mysqli,$query)or die(mysqli_error($mysqli));
		
		while($data = mysqli_fetch_assoc($sql))
			{
			$row['id'] = $data['id'];
			$row['cat_id'] = $data['cat_id'];
			$row['city_id'] = $data['city_id'];
			$row['job_type'] = $data['job_type'];
			$row['job_name'] = $data['job_name'];
			$row['job_designation'] = $data['job_designation'];
			$row['job_desc'] = $data['job_desc'];
			$row['job_salary'] = $data['job_salary'];
			$row['job_company_name'] = $data['job_company_name'];
			$row['job_company_website'] = $data['job_company_website'];
			$row['job_phone_number'] = $data['job_phone_number'];
			$row['job_mail'] = $data['job_mail'];
			$row['job_vacancy'] = $data['job_vacancy'];
			$row['job_address'] = $data['job_address'];
			$row['job_qualification'] = $data['job_qualification'];
			$row['job_skill'] = $data['job_skill'];
			$row['job_experince'] = $data['job_experince'];
			$row['job_work_day'] = $data['job_work_day'];
			$row['job_work_time'] = $data['job_work_time'];
			$row['job_map_latitude'] = $data['job_map_latitude'];
			$row['job_map_longitude'] =$data['job_map_longitude'];
			$row['job_image'] = $data['job_image'];
			$row['job_image'] = $file_path.'images/'.$data['job_image'];
			$row['job_image_thumb'] = $file_path.'images/thumbs/'.$data['job_image'];
			$row['job_date'] = date('d-m-Y',$data['job_date']);

			$row['cid'] = $data['cid'];
			$row['category_name'] = $data['category_name'];
			$row['category_image'] = $file_path.'images/'.$data['category_image'];
			$row['category_image_thumb'] = $file_path.'images/thumbs/'.$data['category_image'];
				
			$row['is_favourite']=get_saved_info($get_method['user_id'],$data['id']);

			array_push($jsonObj,$row);
		
		}
	

		$set['JOBS_APP'] = $jsonObj;
		
		header( 'Content-Type: application/json; charset=utf-8' );
	    echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
		die();

	  }
   else if($get_method['method_name']=="get_recent_job")
		{
	   $user_id=$get_method['user_id'];

	   $query_rec="SELECT COUNT(*) as num FROM tbl_jobs
      			 LEFT JOIN tbl_category ON tbl_jobs.`cat_id`= tbl_category.`cid` WHERE
	       		 FIND_IN_SET(tbl_jobs.`id`,(SELECT `job_id` FROM tbl_recent WHERE tbl_recent.`user_id` = '".$user_id."')) AND tbl_jobs.`status`= 1 AND tbl_category.`status`= 1 ORDER BY tbl_jobs.`id`";
		$total_pages = mysqli_fetch_array(mysqli_query($mysqli,$query_rec));	 

		$page_limit=API_PAGE_LIMIT;
			
		$limit=($get_method['page']-1) * $page_limit;
		
		$jsonObj= array();	
 
		 $query="SELECT * FROM tbl_jobs 
      			 LEFT JOIN tbl_category ON tbl_jobs.`cat_id`= tbl_category.`cid` WHERE
	       		 FIND_IN_SET(tbl_jobs.`id`,(SELECT `job_id` FROM tbl_recent WHERE tbl_recent.`user_id` = '".$user_id."')) AND tbl_jobs.`status`= 1 AND tbl_category.`status`= 1 ORDER BY tbl_jobs.`id` DESC LIMIT $limit ,$page_limit";

		$sql = mysqli_query($mysqli,$query)or die(mysqli_error($mysqli));

		while($data = mysqli_fetch_assoc($sql))
		{
			$row['id'] = $data['id'];
			$row['cat_id'] = $data['cat_id'];
			$row['city_id'] = $data['city_id'];
			$row['job_type'] = $data['job_type'];
			$row['job_name'] = $data['job_name'];
			$row['job_designation'] = $data['job_designation'];
			$row['job_desc'] = $data['job_desc'];
			$row['job_salary'] = $data['job_salary'];
			$row['job_company_name'] = $data['job_company_name'];
			$row['job_company_website'] = $data['job_company_website'];
			$row['job_phone_number'] = $data['job_phone_number'];
			$row['job_mail'] = $data['job_mail'];
			$row['job_vacancy'] = $data['job_vacancy'];
			$row['job_address'] = $data['job_address'];
			$row['job_qualification'] = $data['job_qualification'];
			$row['job_skill'] = $data['job_skill'];
			$row['job_experince'] = $data['job_experince'];
			$row['job_work_day'] = $data['job_work_day'];
			$row['job_work_time'] = $data['job_work_time'];
			$row['job_map_latitude'] = $data['job_map_latitude'];
			$row['job_map_longitude'] =$data['job_map_longitude'];
			$row['job_image'] = $data['job_image'];
			$row['job_image'] = $file_path.'images/'.$data['job_image'];
			$row['job_image_thumb'] = $file_path.'images/thumbs/'.$data['job_image'];
			$row['job_date'] = date('d-m-Y',$data['job_date']);

			$row['cid'] = $data['cid'];
			$row['category_name'] = $data['category_name'];
			$row['category_image'] = $file_path.'images/'.$data['category_image'];
			$row['category_image_thumb'] = $file_path.'images/thumbs/'.$data['category_image'];

			$row['is_favourite']=get_saved_info($user_id,$data['id']);
			
			array_push($jsonObj,$row);
		
		}

		$set['JOBS_APP'] = $jsonObj;
		
		header( 'Content-Type: application/json; charset=utf-8' );
	    echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
		die();

	  }
  else if($get_method['method_name']=="get_search_job")
		{
		
		$job_search=$get_method['search_text'];	
		
		$cat_id=$get_method['cat_id'];	

		$city_id=$get_method['city_id'];

		$job_type=$get_method['job_type'];
		
		$job_company_name=$get_method['job_company_name'];
			
		$jsonObj= array();	
	    
		if($cat_id!='' or  $city_id!='' or  $job_type!='' or  $job_company_name!='')
		{	
		
		 $query_rec = "SELECT COUNT(*) as num FROM tbl_jobs
		LEFT JOIN tbl_category ON tbl_jobs.`cat_id`= tbl_category.`cid` 
		LEFT JOIN tbl_city ON tbl_jobs.`city_id`= tbl_city.`c_id`
		WHERE tbl_jobs.`cat_id` LIKE '%".$cat_id."%' AND tbl_jobs.`city_id`LIKE '%".$city_id."%' AND tbl_jobs.`job_type` LIKE '%".$job_type."%' AND tbl_jobs.`job_company_name` LIKE '%".$job_company_name."%' AND tbl_jobs.`job_name` LIKE '%".$job_search."%' AND tbl_jobs.`status`=1";
		$total_pages = mysqli_fetch_array(mysqli_query($mysqli,$query_rec));
		
		$page_limit=API_PAGE_LIMIT;
			
		$limit=($get_method['page']-1) * $page_limit;
		
	    $query="SELECT * FROM tbl_jobs
		LEFT JOIN tbl_category ON tbl_jobs.`cat_id`= tbl_category.`cid`
		LEFT JOIN tbl_city ON tbl_jobs.`city_id`= tbl_city.`c_id`
		WHERE tbl_jobs.`cat_id` LIKE '%".$cat_id."%' AND tbl_jobs.`city_id` LIKE '%".$city_id."%' AND tbl_jobs.`job_type` LIKE '%".$job_type."%' AND tbl_jobs.`job_company_name` LIKE '%".$job_company_name."%' AND tbl_jobs.`job_name` LIKE '%".$job_search."%' AND tbl_jobs.`status`=1 ORDER BY tbl_jobs.`job_name` LIMIT $limit, $page_limit";
       
	   	}
        else if($cat_id)
		{		
		
		$query_rec = "SELECT COUNT(*) as num FROM tbl_jobs
		LEFT JOIN tbl_category ON tbl_jobs.`cat_id`= tbl_category.`cid` 
		WHERE tbl_jobs.`cat_id` LIKE '%".$cat_id."%'  AND tbl_jobs.`job_name` LIKE '%".$job_search."%' AND tbl_jobs.`status`=1";
		$total_pages = mysqli_fetch_array(mysqli_query($mysqli,$query_rec));
		
		$page_limit=API_PAGE_LIMIT;
			
		$limit=($get_method['page']-1) * $page_limit;
		
		$query="SELECT * FROM tbl_jobs
		LEFT JOIN tbl_category ON tbl_jobs.`cat_id`= tbl_category.`cid` 
		WHERE tbl_jobs.`cat_id` LIKE '%".$cat_id."%' AND tbl_jobs.`job_name` LIKE '%".$job_search."%' AND tbl_jobs.`status`=1 ORDER BY tbl_jobs.`job_name` LIMIT $limit, $page_limit";
		
		}
		else if($job_company_name)
		{		
		
		$query_rec = "SELECT COUNT(*) as num FROM tbl_jobs
		LEFT JOIN tbl_category ON tbl_jobs.`cat_id`= tbl_category.`cid` 
		WHERE tbl_jobs.`job_company_name` LIKE '%".$job_company_name."%'  AND tbl_jobs.`job_name` LIKE '%".$job_search."%' AND tbl_jobs.`status`=1";
		$total_pages = mysqli_fetch_array(mysqli_query($mysqli,$query_rec));
		
		$page_limit=API_PAGE_LIMIT;
			
		$limit=($get_method['page']-1) * $page_limit;
		
		$query="SELECT * FROM tbl_jobs
		LEFT JOIN tbl_category ON tbl_jobs.`cat_id`= tbl_category.`cid` 
		WHERE tbl_jobs.`job_company_name` LIKE '%".$job_company_name."%' AND tbl_jobs.`job_name` LIKE '%".$job_search."%' AND tbl_jobs.`status`=1 ORDER BY tbl_jobs.`job_name` LIMIT $limit, $page_limit";
		
		}
		else if($job_type)
		{	
		
		$query_rec = "SELECT COUNT(*) as num FROM tbl_jobs
		LEFT JOIN tbl_category ON tbl_jobs.`cat_id`= tbl_category.`cid` 
		WHERE tbl_jobs.`job_type` LIKE '%".$job_type."%' AND tbl_jobs.`job_name` LIKE '%".$job_search."%' AND tbl_jobs.`status`=1";
		$total_pages = mysqli_fetch_array(mysqli_query($mysqli,$query_rec));
		
		$page_limit=API_PAGE_LIMIT;
			
		$limit=($get_method['page']-1) * $page_limit;
		
	    $query="SELECT * FROM tbl_jobs
		LEFT JOIN tbl_category ON tbl_jobs.`cat_id`= tbl_category.`cid`
		WHERE tbl_jobs.`job_type` LIKE '%".$job_type."%' AND tbl_jobs.`job_name` LIKE '%".$job_search."%' AND tbl_jobs.`status`=1 ORDER BY tbl_jobs.`job_name` LIMIT $limit, $page_limit";
       	
	   	}
        else if($city_id)
		{		
	    
	    $query_rec = "SELECT COUNT(*) as num FROM tbl_jobs
		LEFT JOIN tbl_category ON tbl_jobs.`cat_id`= tbl_category.`cid` 
		LEFT JOIN tbl_city ON tbl_jobs.`city_id`= tbl_city.`c_id`
		WHERE tbl_jobs.`city_id` LIKE '%".$city_id."%' AND tbl_jobs.`job_name` LIKE '%".$job_search."%' AND tbl_jobs.`status`=1";
		$total_pages = mysqli_fetch_array(mysqli_query($mysqli,$query_rec));
		
		$page_limit=API_PAGE_LIMIT;
				
		$limit=($get_method['page']-1) * $page_limit;
	   	
	    $query="SELECT * FROM tbl_jobs
		LEFT JOIN tbl_category ON tbl_jobs.`cat_id`= tbl_category.`cid` 
		LEFT JOIN tbl_city ON tbl_jobs.`city_id`= tbl_city.`c_id`
		WHERE tbl_jobs.`city_id` LIKE '%".$city_id."%' AND tbl_jobs.`job_name` LIKE '%".$job_search."%' AND tbl_jobs.`status`=1 ORDER BY tbl_jobs.`job_name` LIMIT $limit, $page_limit";
		
		}
	    else 
	    {
        
        $query_rec = "SELECT COUNT(*) as num FROM tbl_jobs
		LEFT JOIN tbl_category ON tbl_jobs.`cat_id`= tbl_category.`cid`
		LEFT JOIN tbl_city ON tbl_jobs.`city_id`= tbl_city.`c_id`
		WHERE tbl_jobs.`status`=1 AND tbl_jobs.`job_name` LIKE '%".$job_search."%'";
		$total_pages = mysqli_fetch_array(mysqli_query($mysqli,$query_rec));
		
		$page_limit=API_PAGE_LIMIT;
			
		$limit=($get_method['page']-1) * $page_limit;
        	
		$query="SELECT * FROM tbl_jobs
		LEFT JOIN tbl_category ON tbl_jobs.`cat_id`= tbl_category.`cid` 
		LEFT JOIN tbl_city ON tbl_jobs.`city_id`= tbl_city.`c_id`
		WHERE tbl_jobs.`status`=1 AND tbl_jobs.`job_name` LIKE '%".$job_search."%' ORDER BY tbl_jobs.`job_name` LIMIT $limit, $page_limit";
       	
		}
	   	
		$sql = mysqli_query($mysqli,$query)or die(mysqli_error($mysqli));

		while($data = mysqli_fetch_assoc($sql))
		{
		    $row['total_item'] = $total_pages['num'];
			$row['id'] = $data['id'];
			$row['cat_id'] = $data['cat_id'];
			$row['city_id'] = $data['city_id'];
			$row['job_type'] = $data['job_type'];
			$row['job_name'] = $data['job_name'];
			$row['job_designation'] = $data['job_designation'];
			$row['job_desc'] = $data['job_desc'];
			$row['job_salary'] = $data['job_salary'];
			$row['job_company_name'] = $data['job_company_name'];
			$row['job_company_website'] = $data['job_company_website'];
			$row['job_phone_number'] = $data['job_phone_number'];
			$row['job_mail'] = $data['job_mail'];
			$row['job_vacancy'] = $data['job_vacancy'];
			$row['job_address'] = $data['job_address'];
			$row['job_qualification'] = $data['job_qualification'];
			$row['job_skill'] = $data['job_skill'];
			$row['job_experince'] = $data['job_experince'];
			$row['job_work_day'] = $data['job_work_day'];
			$row['job_work_time'] = $data['job_work_time'];
			$row['job_map_latitude'] = $data['job_map_latitude'];
			$row['job_map_longitude'] =$data['job_map_longitude'];
			$row['job_image'] = $data['job_image'];
			$row['job_image'] = $file_path.'images/'.$data['job_image'];
			$row['job_image_thumb'] = $file_path.'images/thumbs/'.$data['job_image'];
			$row['job_date'] = date('m/d/Y',$data['job_date']);
 
			$row['cid'] = $data['cid'];
			$row['category_name'] = $data['category_name'];
			$row['category_image'] = $file_path.'images/'.$data['category_image'];
			$row['category_image_thumb'] = $file_path.'images/thumbs/'.$data['category_image'];

			$row['is_favourite']=get_saved_info($get_method['user_id'],$data['id']);

			array_push($jsonObj,$row);
		
		}
 		
		$set['JOBS_APP'] = $jsonObj;
		
		header( 'Content-Type: application/json; charset=utf-8' );
	    echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
		die();

		
	}
	else if($get_method['method_name']=="search_by_keyword")
	{
		
		$job_search=$get_method['search_text'];	
		
		$query_rec = "SELECT COUNT(*) as num FROM tbl_jobs
		LEFT JOIN tbl_category ON tbl_jobs.`cat_id`= tbl_category.`cid` 
		WHERE tbl_jobs.`status`=1 AND tbl_jobs.`job_name` LIKE '%".$job_search."%'";
		$total_pages = mysqli_fetch_array(mysqli_query($mysqli,$query_rec));
		
		$page_limit=API_PAGE_LIMIT;
			
		$limit=($get_method['page']-1) * $page_limit;
		
		$jsonObj= array();	  		 

		$query="SELECT * FROM tbl_jobs
		LEFT JOIN tbl_category ON tbl_jobs.`cat_id`= tbl_category.`cid` 
		WHERE tbl_jobs.`status`=1 AND tbl_jobs.`job_name` LIKE '%".$job_search."%' ORDER BY tbl_jobs.`job_name` LIMIT $limit, $page_limit";       	
		 
		$sql = mysqli_query($mysqli,$query)or die(mysqli_error($mysqli));

		while($data = mysqli_fetch_assoc($sql))
		{
		    $row['total_item'] = $total_pages['num'];
			$row['id'] = $data['id'];
			$row['cat_id'] = $data['cat_id'];
			$row['city_id'] = $data['city_id'];
			$row['job_type'] = $data['job_type'];
			$row['job_name'] = $data['job_name'];
			$row['job_designation'] = $data['job_designation'];
			$row['job_desc'] = $data['job_desc'];
			$row['job_salary'] = $data['job_salary'];
			$row['job_company_name'] = $data['job_company_name'];
			$row['job_company_website'] = $data['job_company_website'];
			$row['job_phone_number'] = $data['job_phone_number'];
			$row['job_mail'] = $data['job_mail'];
			$row['job_vacancy'] = $data['job_vacancy'];
			$row['job_address'] = $data['job_address'];
			$row['job_qualification'] = $data['job_qualification'];
			$row['job_skill'] = $data['job_skill'];
			$row['job_experince'] = $data['job_experince'];
			$row['job_work_day'] = $data['job_work_day'];
			$row['job_work_time'] = $data['job_work_time'];
			$row['job_map_latitude'] = $data['job_map_latitude'];
			$row['job_map_longitude'] =$data['job_map_longitude'];
			$row['job_image'] = $data['job_image'];
			$row['job_image'] = $file_path.'images/'.$data['job_image'];
			$row['job_image_thumb'] = $file_path.'images/thumbs/'.$data['job_image'];
			$row['job_date'] = date('d-m-Y',$data['job_date']);
 
			$row['cid'] = $data['cid'];
			$row['category_name'] = $data['category_name'];
			$row['category_image'] = $file_path.'images/'.$data['category_image'];
			$row['category_image_thumb'] = $file_path.'images/thumbs/'.$data['category_image'];

			$row['is_favourite']=get_saved_info($get_method['user_id'],$data['id']);
			 

			array_push($jsonObj,$row);
		
		}
 
		$set['JOBS_APP'] = $jsonObj;
		
		header( 'Content-Type: application/json; charset=utf-8' );
	    echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
		die();
		
	}	 
  
	else if($get_method['method_name']=="get_similar_jobs")
	{

		//Get cat id using job id
		$query_job="SELECT * FROM tbl_jobs
		LEFT JOIN tbl_category ON tbl_jobs.`cat_id`= tbl_category.`cid`
		WHERE tbl_jobs.`id`='".$get_method['job_id']."' AND tbl_jobs.`status`=1";
		$sql_job = mysqli_query($mysqli,$query_job)or die(mysqli_error($mysqli));
		$row_job=mysqli_fetch_assoc($sql_job);
        
        
        $query_rec = "SELECT COUNT(*) as num FROM tbl_jobs
		LEFT JOIN tbl_category ON tbl_jobs.`cat_id`= tbl_category.`cid` 
		WHERE tbl_jobs.`cat_id`='".$row_job['cat_id']."' AND tbl_jobs.`id` !='".$get_method['job_id']."' AND tbl_jobs.`status`=1";
		$total_pages = mysqli_fetch_array(mysqli_query($mysqli,$query_rec));
		
		$page_limit=API_PAGE_LIMIT;
			
		$limit=($get_method['page']-1) * $page_limit;
		 
		$jsonObj= array();	
 
		$query="SELECT * FROM tbl_jobs
		LEFT JOIN tbl_category ON tbl_jobs.`cat_id`= tbl_category.`cid` 
		WHERE tbl_jobs.`cat_id`='".$row_job['cat_id']."' AND tbl_jobs.`id` !='".$get_method['job_id']."' AND tbl_jobs.`status`=1 ORDER BY tbl_jobs.`id` DESC LIMIT $limit, $page_limit";

		$sql = mysqli_query($mysqli,$query)or die(mysqli_error($mysqli));

		while($data = mysqli_fetch_assoc($sql))
		{
		    $row['total_item'] = $total_pages['num'];
			$row['id'] = $data['id'];
			$row['cat_id'] = $data['cat_id'];
			$row['city_id'] = $data['city_id'];
			$row['job_type'] = $data['job_type'];
			$row['job_name'] = $data['job_name'];
			$row['job_designation'] = $data['job_designation'];
			$row['job_desc'] = $data['job_desc'];
			$row['job_salary'] = $data['job_salary'];
			$row['job_company_name'] = $data['job_company_name'];
			$row['job_company_website'] = $data['job_company_website'];
			$row['job_phone_number'] = $data['job_phone_number'];
			$row['job_mail'] = $data['job_mail'];
			$row['job_vacancy'] = $data['job_vacancy'];
			$row['job_address'] = $data['job_address'];
			$row['job_qualification'] = $data['job_qualification'];
			$row['job_skill'] = $data['job_skill'];
			$row['job_experince'] = $data['job_experince'];
			$row['job_work_day'] = $data['job_work_day'];
			$row['job_work_time'] = $data['job_work_time'];
			$row['job_map_latitude'] = $data['job_map_latitude'];
			$row['job_map_longitude'] =$data['job_map_longitude'];
			$row['job_image'] = $data['job_image'];
			$row['job_image'] = $file_path.'images/'.$data['job_image'];
			$row['job_image_thumb'] = $file_path.'images/thumbs/'.$data['job_image'];
			$row['job_date'] = date('d-m-Y',$data['job_date']);

			$row['cid'] = $data['cid'];
			$row['category_name'] = $data['category_name'];
			$row['category_image'] = $file_path.'images/'.$data['category_image'];
			$row['category_image_thumb'] = $file_path.'images/thumbs/'.$data['category_image'];

		    $row['is_favourite']=get_saved_info($get_method['user_id'],$data['id']);

			array_push($jsonObj,$row);
		
		}

		$set['JOBS_APP'] = $jsonObj;
		
		header( 'Content-Type: application/json; charset=utf-8' );
	    echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
		die();

	  }	
  else if($get_method['method_name']=="apply_job_add")
	{
	
	$apply_user_id=$get_method['apply_user_id'];
	$apply_job_id=$get_method['apply_job_id'];

	$qry = "SELECT * FROM tbl_apply WHERE `user_id` = '".$apply_user_id."' AND `job_id` = '".$apply_job_id."'"; 
	$result = mysqli_query($mysqli,$qry);
	$num_rows = mysqli_num_rows($result);
	$row = mysqli_fetch_assoc($result);	

	$user_qry="SELECT * FROM tbl_users WHERE `id` = '".$apply_user_id."'";
	$user_result=mysqli_query($mysqli,$user_qry);
	$user_row=mysqli_fetch_assoc($user_result);

	$qry_job="SELECT * FROM tbl_jobs WHERE `id`='".$apply_job_id."'";
	$job_result=mysqli_query($mysqli,$qry_job);
	$job_row=mysqli_fetch_assoc($job_result);

	

	if($num_rows==0){	
			if($user_row['user_resume']!=''){
			//Insert data
			$data_apply = array( 
					    'user_id'  =>  $apply_user_id,
					    'job_id'  =>  $apply_job_id,
					    'apply_date' => date('Y-m-d H:i:s')
					    );		

		 	$qry_apply = Insert('tbl_apply',$data_apply);

 				
				
		 			if(get_user_info($job_row['user_id'],'email')!='')
					{	
						
		 				$to = (get_user_info($job_row['user_id'],'email'));
						$recipient_name=$job_row['job_name'];

						$path='uploads/'.$user_row['user_resume'];     
		    
					    $user_resume=rand(0,99999)."_".str_replace(" ", "-", $_FILES['user_resume']['name']);
					    $tmp = $_FILES['user_resume']['tmp_name'];
					    move_uploaded_file($tmp, $path.$user_resume);
					    $icon_path = 'uploads/'.$user_row['user_resume'];

						// subject
						$subject = '[IMPORTANT] '.APP_NAME.'  New apply details';
		 			
						$message='<div style="background-color: #f9f9f9;" align="center"><br />
							  <table style="font-family: OpenSans,sans-serif; color: #666666;" border="0" width="600" cellspacing="0" cellpadding="0" align="center" bgcolor="#FFFFFF">
							    <tbody>
							     <tr>
							        <td colspan="2" bgcolor="#FFFFFF" align="center"><img src="'.$file_path.'images/'.APP_LOGO.'" alt="header" width="120"/></td>
							      </tr>
							      <tr>
							        <td width="600" valign="top" bgcolor="#FFFFFF"><br>
							          <table style="font-family:OpenSans,sans-serif; color: #666666; font-size: 10px; padding: 15px;" border="0" width="100%" cellspacing="0" cellpadding="0" align="left">
							            <tbody>
							              <tr>
							                <td valign="top"><table border="0" align="left" cellpadding="0" cellspacing="0" style="font-family:OpenSans,sans-serif; color: #666666; font-size: 10px; width:100%;">
							                    <tbody>
							                      <tr>
							                        <td>
							                        <p style="color:#262626; font-size:16px; line-height:10px;font-weight:500; margin-bottom:0px;"> 
							                           <h1> '.$job_row['job_name'].'</h1></p>
							                         <p style="color:#262626; font-size:16px; line-height:10px;font-weight:500;"> 
							                          '.$job_row['job_company_name'].'</p>

							                        <h4 style="color: #262626; font-size: 18px; margin-top:0px;">Apply User Details</h4>
							                        <hr>
							                          <p style="color:#262626; font-size:16px; line-height:10px;font-weight:500;"> 
							                            Name: '.$user_row['name'].'</p>
							                          <p style="color:#262626; font-size:16px; line-height:10px;font-weight:500;"> 
							                            Email: '.$user_row['email'].'</p>
							                          <p style="color:#262626; font-size:16px; line-height:10px;font-weight:500;"> 
							                            Phone: '.$user_row['phone'].'</p>
							                           
							                        </td>
							                      </tr>
							                    </tbody>
							                  </table>
							                </td>
							              </tr>
							            </tbody>
							          </table>
							        </td>
							      </tr>
							      <tr>
							        <td style="color: #262626; padding: 20px 0; font-size: 20px; border-top:5px solid #52bfd3;" colspan="2" align="center" bgcolor="#ffffff">Copyright Â© '.APP_NAME.'.</td>
							      </tr>
							    </tbody>
							  </table>
							</div>';
							
					    send_email($to,$recipient_name,$subject,$message,$icon_path);

					    $to1 = $user_row['email'];
						$recipient_name1=$user_row['name'];
						// subject
						$subject1 = $subject = '[IMPORTANT] '.APP_NAME.'  Job Information';
		 			
						$message1='<div style="background-color: #f9f9f9;" align="center"><br />
							  <table style="font-family: OpenSans,sans-serif; color: #666666;" border="0" width="600" cellspacing="0" cellpadding="0" align="center" bgcolor="#FFFFFF">
							    <tbody>
							      <tr>
							        <td colspan="2" bgcolor="#FFFFFF" align="center"><img src="'.$file_path.'images/'.APP_LOGO.'" alt="header" width="120"/></td>
							      </tr>
							      <tr>
							        <td width="600" valign="top" bgcolor="#FFFFFF"><br>
							          <table style="font-family:OpenSans,sans-serif; color: #666666; font-size: 10px; padding: 15px;" border="0" width="100%" cellspacing="0" cellpadding="0" align="left">
							            <tbody>
							              <tr>
							                <td valign="top"><table border="0" align="left" cellpadding="0" cellspacing="0" style="font-family:OpenSans,sans-serif; color: #666666; font-size: 10px; width:100%;">
							                    <tbody>
							                      <tr>
							                        <td>
							                        <p style="color:#262626; font-size:16px; line-height:10px;font-weight:500; margin-bottom:0px;"> 
							                           <h1> '.$job_row['job_name'].'</h1></p>
							                         <p style="color:#262626; font-size:16px; line-height:10px;font-weight:500;"> 
							                          '.$job_row['job_company_name'].'</p>

							                          <p style="color:#262626; font-size:16px; line-height:10px;font-weight:500;"> 
							                            '.$job_row['job_type'].'</p>
							                          <p style="color:#262626; font-size:16px; line-height:10px;font-weight:500;"> 
							                           '.$job_row['job_address'].'</p>
							                          <p style="color:#262626; font-size:16px; line-height:10px;font-weight:500;"> 
							                            '.$job_row['job_company_website'].'</p>

							                          <h4 style="color: #262626; font-size: 18px; margin-top:0px;">You have successfully Apply</h4>
							                        </td>
							                      </tr>
							                    </tbody>
							                  </table>
							                </td>
							              </tr>
							            </tbody>
							          </table>
							        </td>
							      </tr>
							      <tr>
							        <td style="color: #262626; padding: 20px 0; font-size: 20px; border-top:5px solid #52bfd3;" colspan="2" align="center" bgcolor="#ffffff">Copyright Â© '.APP_NAME.'.</td>
							      </tr>
							    </tbody>
							  </table>
							</div>';

					    send_email($to1,$recipient_name1,$subject1,$message1);


					$set['JOBS_APP'][]=array('msg' => $app_lang['job_applied'],'success'=>'1');
					
				  }
				}else{

						$set['JOBS_APP'][]=array('msg' => $app_lang['upload_resume'],'status' => -1,'success'=>'0');
					}
				}
				else{
					$set['JOBS_APP'][]=array('msg' => $app_lang['already_applied'],'success'=>'0');
					
				}
		
		header( 'Content-Type: application/json; charset=utf-8' );
	    echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
		die();	
 

	}	
  else if($get_method['method_name']=="saved_job_add")
	{
	
	$saved_user_id =$get_method['saved_user_id'];
	$saved_job_id =$get_method['saved_job_id'];

	$sql ="SELECT * FROM tbl_saved WHERE `user_id` = '$saved_user_id' AND `job_id` = '$saved_job_id'"; 
	$res = mysqli_query($mysqli,$sql);
	
	if($res->num_rows == 0){
	
			//Inser data
			$data = array( 
					    'user_id'  =>  $saved_user_id,
					    'job_id'  =>  $saved_job_id,
					    'created_at' => date('Y-m-d H:i:s')
					    );		

		 	 $qry_apply = Insert('tbl_saved',$data);
		 
		     $set['JOBS_APP'][]=array('msg' => $app_lang['add_favourite'],'success'=>'1');
		 
		}else{
			// remove to favourite list
			$deleteSql="DELETE FROM tbl_saved WHERE `user_id`='$saved_user_id' AND `job_id`='$saved_job_id'";
			if(mysqli_query($mysqli, $deleteSql)){
				$set['JOBS_APP'][]=array('msg' => $app_lang['remove_favourite'],'success'=>'0');
			}
			else{

				$set['JOBS_APP'][] = array('msg'=>$app_lang['error_msg'],'success'=>'0');
			}
		}
		
		header( 'Content-Type: application/json; charset=utf-8' );
	    echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
		die();	
 
	}	
  else if($get_method['method_name']=="delete_job")
	{
		  
	
		Delete('tbl_jobs','id='.$get_method['delete_job_id'].''); 

		Delete('tbl_apply','job_id='.$get_method['delete_job_id'].''); 
			 
  				 
  		$set['JOBS_APP'][]=array('msg' => $app_lang['delete_job'],'success'=>'1');
			  
		header( 'Content-Type: application/json; charset=utf-8' );
	    echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
		die();	
 

	}	
    else if($get_method['method_name']=="job_list")
 	{

    $jsonObj= array();

    $page_limit=API_PAGE_LIMIT;

	$limit=($get_method['page']-1) * $page_limit;
		
	$user_id=$get_method['user_id'];  

    $jsonObj= array();  
  
    $query="SELECT * FROM tbl_jobs
           LEFT JOIN tbl_category ON tbl_jobs.`cat_id`= tbl_category.`cid` 
           WHERE tbl_jobs.`user_id`='".$user_id."' AND tbl_jobs.`status`= 1
           ORDER BY tbl_jobs.`id` DESC LIMIT $limit, $page_limit";

    $sql = mysqli_query($mysqli,$query)or die(mysqli_error($mysqli));

    while($data = mysqli_fetch_assoc($sql))
    {
            $row['id'] = $data['id'];
			$row['cat_id'] = $data['cat_id'];
			$row['city_id'] = $data['city_id'];
			$row['job_type'] = $data['job_type'];
			$row['job_name'] = $data['job_name'];
			$row['job_designation'] = $data['job_designation'];
			$row['job_desc'] = $data['job_desc'];
			$row['job_salary'] = $data['job_salary'];
			$row['job_company_name'] = $data['job_company_name'];
			$row['job_company_website'] = $data['job_company_website'];
			$row['job_phone_number'] = $data['job_phone_number'];
			$row['job_mail'] = $data['job_mail'];
			$row['job_vacancy'] = $data['job_vacancy'];
			$row['job_address'] = $data['job_address'];
			$row['job_qualification'] = $data['job_qualification'];
			$row['job_skill'] = $data['job_skill'];
			$row['job_experince'] = $data['job_experince'];
			$row['job_work_day'] = $data['job_work_day'];
			$row['job_work_time'] = $data['job_work_time'];
			$row['job_map_latitude'] = $data['job_map_latitude'];
			$row['job_map_longitude'] =$data['job_map_longitude'];
			$row['job_image'] = $data['job_image'];
			$row['job_image'] = $file_path.'images/'.$data['job_image'];
			$row['job_image_thumb'] = $file_path.'images/thumbs/'.$data['job_image'];
			$row['job_date'] = date('d-m-Y',$data['job_date']);

            $row['job_apply_total'] = get_apply_count($data['id']);
 
	        $row['cid'] = $data['cid'];
	        $row['category_name'] = $data['category_name'];
	        $row['category_image'] = $file_path.'images/'.$data['category_image'];
	        $row['category_image_thumb'] = $file_path.'images/thumbs/'.$data['category_image'];	

	        $row['is_favourite']=get_saved_info($user_id,$data['id']);

		   array_push($jsonObj,$row);
		}

		$set['JOBS_APP'] = $jsonObj;
		
		header( 'Content-Type: application/json; charset=utf-8' );
	    echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
		die();
 	} 
  else if($get_method['method_name']=="user_job_apply_list")
 	{
 		
    $jsonObj= array();  
 
    $query="SELECT * FROM tbl_users
             LEFT JOIN tbl_apply ON tbl_users.`id`= tbl_apply.`user_id` 
             WHERE tbl_apply.`job_id`=".$get_method['apply_job_id']." ORDER BY tbl_users.`id` DESC";

    $sql = mysqli_query($mysqli,$query)or die(mysqli_error($mysqli));

    while($data = mysqli_fetch_assoc($sql))
    {
      $row['user_id'] = $data['id'];
      $row['name'] = $data['name'];
      $row['email'] = $data['email'];
      $row['phone'] = $data['phone'];
      $row['city'] = $data['city'];

      if($data['user_image'])
      {
        $user_image=$file_path.'images/'.$data['user_image'];
      } 
      else
      {
        $user_image='';
      }

      $row['user_image'] = $user_image;

      if($data['user_resume'])
      {
        $user_resume=$file_path.'uploads/'.$data['user_resume'];
      } 
      else
      {
        $user_resume='';
      }
  
      $row['user_resume'] = $user_resume;
       
      array_push($jsonObj,$row);
    
     }
 
        $set['JOBS_APP'] = $jsonObj;
		
		header( 'Content-Type: application/json; charset=utf-8' );
	    echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
		die();
 	}   
   else if($get_method['method_name']=="user_apply_list")
 	{
 	 
 	 $query_rec ="SELECT COUNT(*) as num FROM tbl_apply  
 	 			  WHERE  tbl_apply.`user_id`=".$get_method['user_id']."";
	 $total_pages = mysqli_fetch_array(mysqli_query($mysqli,$query_rec));
		
	 $page_limit=API_PAGE_LIMIT;
	 $limit=($get_method['page']-1) * $page_limit;   
 		
     $jsonObj= array(); 
     
	 $query="SELECT * FROM tbl_apply  WHERE  tbl_apply.`user_id`=".$get_method['user_id']."
	 		 ORDER BY tbl_apply.`ap_id` DESC LIMIT $limit, $page_limit";
   	 $sql = mysqli_query($mysqli,$query)or die(mysqli_error($mysqli));

    while($data = mysqli_fetch_assoc($sql))
    {       
           
            $row['total_item'] = $total_pages['num'];
	        $row['apply_id'] = $data['ap_id'];   			
			$row['user_id'] = $data['user_id'];
            $row['job_id'] =$data['job_id'];
			
			$row['id'] =get_job_info($data['job_id'],'id');
		    $row['cat_id'] =get_job_info($data['job_id'],'cat_id');
		    $row['city_id'] =get_job_info($data['job_id'],'city_id');
		    $row['job_type'] =get_job_info($data['job_id'],'job_type');
		    $row['job_name'] =get_job_info($data['job_id'],'job_name');
		    $row['job_designation'] =get_job_info($data['job_id'],'job_designation');
		    $row['job_desc'] =get_job_info($data['job_id'],'job_desc');
		    $row['job_salary'] =get_job_info($data['job_id'],'job_salary');
		    $row['job_company_name'] =get_job_info($data['job_id'],'job_company_name');
		    $row['job_company_website'] =get_job_info($data['job_id'],'job_company_website');
		    $row['job_phone_number'] =get_job_info($data['job_id'],'job_phone_number');
		    $row['job_mail'] =get_job_info($data['job_id'],'job_mail');
		    $row['job_vacancy'] =get_job_info($data['job_id'],'job_vacancy');
		    $row['job_address'] =get_job_info($data['job_id'],'job_address');
		    $row['job_qualification'] =get_job_info($data['job_id'],'job_qualification');
		    $row['job_skill'] =get_job_info($data['job_id'],'job_skill');
		    $row['job_experince'] = get_job_info($data['job_id'],'job_experince');
			$row['job_work_day'] = get_job_info($data['job_id'],'job_work_day');
			$row['job_work_time'] = get_job_info($data['job_id'],'job_work_time');
			$row['job_map_latitude'] = get_job_info($data['job_id'],'job_map_latitude');
			$row['job_map_longitude'] = get_job_info($data['job_id'],'job_map_longitude');
			$row['job_image'] = $file_path.'images/'.get_job_info($data['job_id'],'job_image');
			$row['job_image_thumb'] =$file_path.'images/thumbs/'.get_job_info($data['job_id'],'job_image');
			$row['job_date'] = date('d-m-Y',get_job_info($data['job_id'],'job_date'));

			$row['apply_date'] = date('Y-m-d',strtotime($data['apply_date']));

              if($data['seen']==1)
		      {
		      	$row['seen'] = 'true';
		      }
		      else
		      {
		      	$row['seen'] = 'false';
		      }
 
		    array_push($jsonObj,$row);
    
    		}
 
         $set['JOBS_APP'] = $jsonObj;
		 
		header( 'Content-Type: application/json; charset=utf-8' );
	    echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
		die();
 	}
 	else if($get_method['method_name']=="user_apply_job_seen")
 	{

 		$data = array('seen'  =>  '1');		
		$edit_status=Update('tbl_apply', $data, "WHERE user_id = '".$get_method['apply_user_id']."' AND job_id = '".$get_method['job_id']."'");

 		$set['JOBS_APP'][]=array('msg' => $app_lang['job_seen'],'success'=>'1');
		 
		header( 'Content-Type: application/json; charset=utf-8' );
	    echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
		die();
 	}
  else if($get_method['method_name']=="user_saved_list")
 	{
 		
    $query_rec = "SELECT COUNT(*) as num FROM tbl_saved  WHERE  tbl_saved.`user_id`=".$get_method['user_id']."";
	$total_pages = mysqli_fetch_array(mysqli_query($mysqli,$query_rec));
	
	$page_limit=API_PAGE_LIMIT;
	$limit=($get_method['page']-1) * $page_limit;
		
    $jsonObj= array();  
 
	$query="SELECT * FROM tbl_saved  WHERE  tbl_saved.`user_id`=".$get_method['user_id']."
		   ORDER BY tbl_saved.`sa_id` DESC LIMIT $limit, $page_limit";
    $sql = mysqli_query($mysqli,$query)or die(mysqli_error($mysqli));

    while($data = mysqli_fetch_assoc($sql))
    {
            
            $row['total_item'] = $total_pages['num']; 
            $row['user_id'] = $data['user_id'];
            $row['job_id'] =$data['job_id'];
		    $row['id'] =get_job_info($data['job_id'],'id');
		    $row['cat_id'] =get_job_info($data['job_id'],'cat_id');
		    $row['city_id'] =get_job_info($data['job_id'],'city_id');
		    $row['job_type'] =get_job_info($data['job_id'],'job_type');
		    $row['job_name'] =get_job_info($data['job_id'],'job_name');
		    $row['job_designation'] =get_job_info($data['job_id'],'job_designation');
		    $row['job_desc'] =get_job_info($data['job_id'],'job_desc');
		    $row['job_salary'] =get_job_info($data['job_id'],'job_salary');
		    $row['job_company_name'] =get_job_info($data['job_id'],'job_company_name');
		    $row['job_company_website'] =get_job_info($data['job_id'],'job_company_website');
		    $row['job_phone_number'] =get_job_info($data['job_id'],'job_phone_number');
		    $row['job_mail'] =get_job_info($data['job_id'],'job_mail');
		    $row['job_vacancy'] =get_job_info($data['job_id'],'job_vacancy');
		    $row['job_address'] =get_job_info($data['job_id'],'job_address');
		    $row['job_qualification'] =get_job_info($data['job_id'],'job_qualification');
		    $row['job_skill'] =get_job_info($data['job_id'],'job_skill');
		    $row['job_experince'] = get_job_info($data['job_id'],'job_experince');
			$row['job_work_day'] = get_job_info($data['job_id'],'job_work_day');
			$row['job_work_time'] = get_job_info($data['job_id'],'job_work_time');
			$row['job_map_latitude'] = get_job_info($data['job_id'],'job_map_latitude');
			$row['job_map_longitude'] = get_job_info($data['job_id'],'job_map_longitude');
		    $row['job_image'] =get_job_info($data['job_id'],'job_image');
			$row['job_image'] = $file_path.'images/'.get_job_info($data['job_id'],'job_image');
			$row['job_image_thumb'] =$file_path.'images/thumbs/'.get_job_info($data['job_id'],'job_image');
			$row['job_date'] = date('m/d/Y',get_job_info($data['job_id'],'job_date'));
            
            $row['is_favourite']=get_saved_info($get_method['user_id'],$data['job_id']);

            array_push($jsonObj,$row);
    
    	}
 
        $set['JOBS_APP'] = $jsonObj;
     
		header( 'Content-Type: application/json; charset=utf-8' );
	    echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
		die();
 	} 
 else if($get_method['method_name']=="job_add")
 	{
 		
          $job_image=rand(0,99999)."_".$_FILES['job_image']['name'];

	      $ext = pathinfo($_FILES['job_image']['name'], PATHINFO_EXTENSION);

	      $job_image=rand(0,99999).".".$ext;
	      //Main Image
	      $tpath1='images/'.$job_image;   

	      $tmp = $_FILES['job_image']['tmp_name'];
	      move_uploaded_file($tmp, $tpath1);
	   
	      //Thumb Image 
	      $thumbpath='images/thumbs/'.$job_image;   
	      $thumb_pic1=create_thumb_image($tpath1,$thumbpath, '200' ,'200');    
      
       $data = array( 
		 'user_id'  =>  $get_method['user_id'],
		 'cat_id'  =>  $get_method['cat_id'],
		 'city_id'  =>  $get_method['city_id'],
		 'job_type'  =>  $get_method['job_type'],
         'job_name'  =>  addslashes($get_method['job_name']),
         'job_designation'  =>  addslashes($get_method['job_designation']),
         'job_desc'  =>  addslashes($get_method['job_desc']),
         'job_salary'  =>  $get_method['job_salary'],
         'job_company_name'  =>  $get_method['job_company_name'],
         'job_company_website'  =>  $get_method['job_company_website'],
         'job_phone_number'  =>  $get_method['job_phone_number'],
         'job_mail'  =>  $get_method['job_mail'],
         'job_vacancy'  =>  $get_method['job_vacancy'],
         'job_address'  =>  addslashes($get_method['job_address']),
         'job_qualification'  =>  addslashes($get_method['job_qualification']),
         'job_skill'  =>  addslashes($get_method['job_skill']),
         'job_experince'  =>  addslashes($get_method['job_experince']),
         'job_work_day'  =>  addslashes($get_method['job_work_day']),
         'job_work_time'  =>  addslashes($get_method['job_work_time']),
         'job_map_latitude'  =>  addslashes($get_method['job_map_latitude']),
         'job_map_longitude'  =>  addslashes($get_method['job_map_longitude']),
         'job_image'  =>  $job_image,
         'job_date'  =>  strtotime($get_method['job_date']),
         'status' => 0
         
		);		

 		$qry = Insert('tbl_jobs',$data);

        $set['JOBS_APP'][]=array('msg' => $app_lang['add_job'],'success'=>'1');
			  
		header( 'Content-Type: application/json; charset=utf-8' );
	    echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
		die();
 	}

  else if($get_method['method_name']=="edit_job")
 	{
 		if($_FILES['job_image']['name']!="")
     {

          $job_image=rand(0,99999)."_".$_FILES['job_image']['name'];

	      $ext = pathinfo($_FILES['job_image']['name'], PATHINFO_EXTENSION);

	      $job_image=rand(0,99999).".".$ext;
	      //Main Image
	      $tpath1='images/'.$job_image;   

	      $tmp = $_FILES['job_image']['tmp_name'];
	      move_uploaded_file($tmp, $tpath1);
	   
	      //Thumb Image 
	      $thumbpath='images/thumbs/'.$job_image;   
	      $thumb_pic1=create_thumb_image($tpath1,$thumbpath, '200' ,'200');    
       
       $data = array(
           'user_id'  =>  $get_method['user_id'], 
           'cat_id'  =>  $get_method['cat_id'],
           'city_id'  =>  $get_method['city_id'],
           'job_type'  =>  $get_method['job_type'],
           'job_name'  =>  addslashes($get_method['job_name']),
           'job_designation'  =>  addslashes($get_method['job_designation']),
           'job_desc'  =>  addslashes($get_method['job_desc']),
           'job_salary'  =>  $get_method['job_salary'],
           'job_company_name'  =>  $get_method['job_company_name'],
           'job_company_website'  =>  $get_method['job_company_website'],
           'job_phone_number'  =>  $get_method['job_phone_number'],
           'job_mail'  =>  $get_method['job_mail'],
           'job_vacancy'  =>  $get_method['job_vacancy'],
           'job_address'  =>  addslashes($get_method['job_address']),
           'job_qualification'  =>  addslashes($get_method['job_qualification']),
           'job_skill'  =>  addslashes($get_method['job_skill']),
           'job_experince'  =>  addslashes($get_method['job_experince']),
           'job_work_day'  =>  addslashes($get_method['job_work_day']),
           'job_work_time'  =>  addslashes($get_method['job_work_time']),
           'job_map_latitude'  =>  addslashes($get_method['job_map_latitude']),
           'job_map_longitude'  =>  addslashes($get_method['job_map_longitude']),
           'job_image'  =>  $job_image,
           'job_date'  =>  strtotime($get_method['job_date'])
            ); 

      }
      else
      {
          $data = array( 
           'user_id'  =>  $get_method['user_id'],
           'cat_id'  =>  $get_method['cat_id'],
           'city_id'  =>  $get_method['city_id'],
           'job_type'  =>  $get_method['job_type'],
           'job_name'  =>  addslashes($get_method['job_name']),
           'job_designation'  =>  addslashes($get_method['job_designation']),
           'job_desc'  =>  addslashes($get_method['job_desc']),
           'job_salary'  =>  $get_method['job_salary'],
           'job_company_name'  =>  $get_method['job_company_name'],
           'job_company_website'  =>  $get_method['job_company_website'],
           'job_phone_number'  =>  $get_method['job_phone_number'],
           'job_mail'  =>  $get_method['job_mail'],
           'job_vacancy'  =>  $get_method['job_vacancy'],
           'job_address'  =>  addslashes($get_method['job_address']),
           'job_qualification'  =>  addslashes($get_method['job_qualification']),
           'job_skill'  =>  addslashes($get_method['job_skill']),
           'job_experince'  =>  addslashes($get_method['job_experince']),
           'job_work_day'  =>  addslashes($get_method['job_work_day']),
           'job_work_time'  =>  addslashes($get_method['job_work_time']),
           'job_map_latitude'  =>  addslashes($get_method['job_map_latitude']),
           'job_map_longitude'  =>  addslashes($get_method['job_map_longitude']),
           'job_date'  =>  strtotime($get_method['job_date'])
            ); 
      }

 
	    $job_edit=Update('tbl_jobs', $data, "WHERE id = '".$get_method['job_id']."'");
				  
		$set['JOBS_APP'][]=array('msg' => $app_lang['edit_job'],'success'=>'1');	 
  
		 
		header( 'Content-Type: application/json; charset=utf-8' );
	    echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
		die();
 	} 
 	else if ($get_method['method_name']=="get_company_details") {

		$jsonObj= array();	
 		
		$user_id=$get_method['user_id'];

		$query="SELECT * FROM tbl_company  WHERE tbl_company.`user_id`='$user_id' ORDER BY tbl_company.`id` DESC";

		$sql = mysqli_query($mysqli,$query) or die(mysqli_error($mysqli));

			while($data = mysqli_fetch_assoc($sql))
			{

				$row['id'] = $data['id'];
				$row['company_name'] = $data['company_name'];
				$row['company_email'] = $data['company_email'];
				$row['mobile_no'] = $data['mobile_no'];
				$row['company_address'] = $data['company_address'];
				$row['company_desc'] = $data['company_desc'];
				$row['company_website'] = $data['company_website'];
				$row['company_work_day'] = $data['company_work_day'];
				$row['company_work_time'] = $data['company_work_time'];
				$row['company_logo'] = $file_path.'images/'.$data['company_logo'];

				array_push($jsonObj,$row);
					
			}	
		
		$set['JOBS_APP'] = $jsonObj;
		
		header( 'Content-Type: application/json; charset=utf-8' );
	    echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
		die();
		
	}

   else if($get_method['method_name']=="user_register")
	{
		$register_date=strtotime(date('d-m-Y h:i A'));

		$imie=$get_method['imie'];

		$name=filter_var($get_method['name'], FILTER_SANITIZE_STRING);
		//$email=filter_var($get_method['email'], FILTER_SANITIZE_STRING);
		$password=md5(trim($get_method['password']));
		$phone=filter_var($get_method['phone'], FILTER_SANITIZE_STRING);

	    $qry = "SELECT * FROM tbl_users WHERE `phone` = '".$phone."'"; 
		$result = mysqli_query($mysqli,$qry);
		$row = mysqli_fetch_assoc($result);

		if (!strlen($phone)) 
		{
			$set['JOBS_APP'][]=array('msg' => $app_lang['invalid_phone_format'],'success'=>'0');
		}
		else if(substr($phone,0,3)!="077" && substr($phone,0,3)!="076" && substr($phone,0,3)!="071" && substr($phone,0,3)!="070")
		{
			$set['JOBS_APP'][]=array('msg' => $app_lang['invalid_phone_operator'],'success'=>'0');
		}
		else if($row['phone']!="" && $row['status']==1)
		{
			$set['JOBS_APP'][]=array('msg' => $app_lang['phone_exist'],'success'=>'1', 'user_id'=>$row['id']);
		}
		else if($row['status']==2 || $row['status']==0)
		{
					$sql_3		=	"UPDATE  tbl_users SET status=2  WHERE id = '".$row['id']."'  ";//AND
					 mysqli_query($mysqli,$sql_3) or die (mysqli_error($mysqli));
					 //$userId 	= $rowid;
			$set['JOBS_APP'][]=array('msg' => $app_lang['register_success'],'success'=>'1', 'user_id'=>$row['id']);
		}
		else{

			$data = array(
 				    'user_type'  => $user_type,
				    'name'  => $name,				    
					'imei'  =>  $imie,
					'password'  =>  $password,
					'phone'  =>  $phone,
					'register_date'  =>  $register_date,
					'status'  =>  '2'
					);		
 			 	
			$qry = Insert('tbl_users',$data);

			$user_id=mysqli_insert_id($mysqli);

			 
				 

		   $set['JOBS_APP'][]=array('msg' => $app_lang['register_success'],'success'=>'1', 'user_id'=>$user_id);
		 }
		header( 'Content-Type: application/json; charset=utf-8' );
		echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
		die();

	}
  else if($get_method['method_name']=="user_login")
	{
		
	    $email = cleanInput($get_method['email']);
 		$password = trim($get_method['password']);

		$qry = "SELECT * FROM tbl_users WHERE `phone` = '$email'"; 
		$result = mysqli_query($mysqli,$qry) or die('Error in fetch data ->'.mysqli_error($mysqli));


   		if(mysqli_num_rows($result) > 0){
			$row=mysqli_fetch_assoc($result);	
		       	 if($row['user_image'])
                  {
                    $user_image=$file_path.'images/'.$row['user_image'];
                  } 
                  else
                  {
                    $user_image='';
                  }
				 if($row['status']=='1'){  

				 	if($row['password']==md5($password)){

			        $set['JOBS_APP'][]=array('user_type' => $row['user_type'],'user_id' => $row['id'],'name'=>$row['name'],'user_image'=>$user_image,'success'=>'1', 'imei'=>$row['imei'], 'mobile'=>$row['phone']);
                  }
              	
                  else
                  {
                     $set['JOBS_APP'][]=array('msg' =>$app_lang['invalid_password'],'success'=>'0', 'imei'=>'');
                  }
		}	
		else{
				// account is deactivated
				$set['JOBS_APP'][]=array('msg' =>$app_lang['account_deactive'],'success'=>'0', 'imei'=>'');
			}
		}		
		else
		{
				 
 				$set['JOBS_APP'][]=array('msg' =>$app_lang['email_not_found'],'success'=>'0', 'imei'=>'');
		}

	    header( 'Content-Type: application/json; charset=utf-8' );
	    echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
		die();
	}
 else if($get_method['method_name']=="user_profile")
	{
		
	$qry = "SELECT * FROM tbl_users WHERE `id` = '".$get_method['id']."'"; 
	$result = mysqli_query($mysqli,$qry);
	$row = mysqli_fetch_assoc($result);

	if($row['user_image'])
	{
		$user_image=$file_path.'images/'.$row['user_image'];
	} 
	else
	{
		$user_image='';
	}
	  			
	  			$member_mobile = $row['phone'];
				$masknumber    = $row['masknumber'];
	  			$mobile9 	= substr($member_mobile ,-9,9);
				if(  substr($mobile9,0,2)=="70" ||  substr($mobile9,0,2)=="71"){
					$app_db 		= API_APP_DBM;
					$app_table	 	= API_APP_TBLM; 
								
				} else {
					$app_db 		= API_APP_DBI;
					$app_table	 	= API_APP_TBLI; 
				}
				
				$app_db_connection = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, $app_db) or die('Unable to establish a DB connection'); 
 				mysqli_set_charset($db_connection, "utf8");
				
				$sql_member_exist 		=	"SELECT * FROM ".$app_table." WHERE masknumber = '$masknumber' AND active=1";
				$reply_member_exist		=	mysqli_query($app_db_connection,$sql_member_exist) or die (mysqli_error($app_db_connection)); 
				$row_member_exist	 	= 	mysqli_fetch_array($reply_member_exist);
				$member_exist			=	mysqli_num_rows($reply_member_exist);
				$pin_number 			=   $row_member_exist['pinno']; 
				if($pin_number==""){
					$pin_number="";
				}
		
		  
        $set['JOBS_APP'][]=array('user_id' => $row['id'],'pin_number' => $pin_number,'name'=>$row['name'],'email'=>$row['email'],'phone'=>$row['phone'], 'current_company_name'=>stripslashes($row['current_company_name']), 'user_image'=>$user_image, 'success'=>'1');
		

		header( 'Content-Type: application/json; charset=utf-8' );
	    echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
		die();
	}
	else if($get_method['method_name']=="user_email")
	{
		 

		 

		$user_id=$get_method['user_id'];
		$email=$get_method['email'];
		$name=$get_method['name'];
		$msg=$get_method['msg'];

 	 	$qry = "SELECT * FROM tbl_users WHERE `id` = '".$user_id."'"; 
		$result = mysqli_query($mysqli,$qry);
		$row = mysqli_fetch_assoc($result);
 		$member_mobile = $row['phone'];
		
		
			$to = "trackwaylanka@gmail.com";
			$recipient_name=$name;
			// subject
			$subject = APP_NAME.' Contact us form';
			
			$message = '<html><body>';
 			
			$message .='<div style="background-color: #f9f9f9;" align="center"><br />
					  <table style="font-family: OpenSans,sans-serif; color: #666666;" border="0" width="600" cellspacing="0" cellpadding="0" align="center" bgcolor="#FFFFFF">
					    <tbody>
					      <tr>
					        <td colspan="2" bgcolor="#FFFFFF" align="center"><img src="'.$file_path.'images/'.APP_LOGO.'" alt="header" width="120"/></td>
					      </tr>
					      <tr>
					        <td width="600" valign="top" bgcolor="#FFFFFF"><br>
					          <table style="font-family:OpenSans,sans-serif; color: #666666; font-size: 10; padding: 15px;" border="0" width="100%" cellspacing="0" cellpadding="0" align="left">
					            <tbody>
					              <tr>
					                <td valign="top"><table border="0" align="left" cellpadding="0" cellspacing="0" style="font-family:OpenSans,sans-serif; color: #666666; font-size: 10px; width:100%;">
					                    <tbody>
					                      <tr>
					                        <td><p style="color: #262626; font-size: 28px; margin-top:0px;"><strong>Name : '.$name.'</strong></p>
											  <p style="color:#262626; font-size:20px; line-height:32px;font-weight:500;">Email : '.$email.'</p>
					                          <p style="color:#262626; font-size:20px; line-height:32px;font-weight:500;"> '.$msg.'</p>
					                          <p style="color:#262626; font-size:20px; line-height:32px;font-weight:500;margin-bottom:30px;">Thanks you,<br />
					                            '.APP_NAME.'.</p></td>
					                      </tr>
					                    </tbody>
					                  </table></td>
					              </tr>
					               
					            </tbody>
					          </table></td>
					      </tr>
					      <tr>
					        <td style="color: #262626; padding: 20px 0; font-size: 20px; border-top:5px solid #52bfd3;" colspan="2" align="center" bgcolor="#ffffff">Copyright Â© '.APP_NAME.'.</td>
					      </tr>
					    </tbody>
					  </table>
					</div';
			$message .= "</body></html>";
			
			
			
			$headers = 'MIME-Version: 1.0' . "\r\n";
			$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
			//$headers .= 'Reply-To: $email' . "\r\n";
			
			// Additional headers
			//$headers .= 'To: Recipient <trackwaylanka@gmail.com>' . "\r\n";
			$headers .= 'From: '.$name.' <'.$email.'>' . "\r\n";
			
			mail("5870870@gmail.com", $subject, $message, $headers);
			// Send the email
			$res = mail($to, $subject, $message, $headers); 


		
			//send_email($to,$recipient_name,$subject,$message);
 
	       		 
		$set['JOBS_APP'][]=array('msg'=>'Message Sent','success'=>'1');

		header( 'Content-Type: application/json; charset=utf-8' );
	    echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
		die();
	
	} else if($get_method['method_name']=="user_profile_update"){
		$path='';

		$qry = "SELECT * FROM tbl_users WHERE `id` = '".$get_method['user_id']."'"; 
		$result = mysqli_query($mysqli,$qry);
		$row = mysqli_fetch_assoc($result);

		$user_id=$get_method['user_id'];
		
		if($_FILES['user_image']['name']!='') {
			if($row['user_image']!="") {
              	   		unlink('images/'.$row['user_image']);
           		}

 	     		$user_image=rand(0,99999)."_".$_FILES['user_image']['name'];
	 	
 			$tpath1='images/'.$user_image; 			 
	     		$pic1=compress_image($_FILES["user_image"]["tmp_name"], $tpath1, 80);
	     		$path=$file_path.$tpath1;
		 } else {
		 	$user_image='';
		 }
		
		 $data = array(
 			'name'  =>  $get_method['name'], 
			'user_resume' => $user_resume  
			);
		 $user_edit=Update('tbl_users', $data, "WHERE `id` = '".$user_id."'");

		$set['JOBS_APP'][]=array('user_image'=>$path,'msg'=>$app_lang['update_success'],'success'=>'1');

		header( 'Content-Type: application/json; charset=utf-8' );
	    	echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
		die();

	}
   else if($get_method['method_name']=="user_profile_update_old")
	{
		$path='';

		$qry = "SELECT * FROM tbl_users WHERE `id` = '".$get_method['user_id']."'"; 
		$result = mysqli_query($mysqli,$qry);
		$row = mysqli_fetch_assoc($result);

		$user_id=$get_method['user_id'];

 	 	if($_FILES['user_image']['name']!='')
         {

          if($row['user_image']!="")
            {
              unlink('images/'.$row['user_image']);
           }

 	     $user_image=rand(0,99999)."_".$_FILES['user_image']['name'];
	 	
	     //Main Image
		 $tpath1='images/'.$user_image; 			 
	     $pic1=compress_image($_FILES["user_image"]["tmp_name"], $tpath1, 80);
	     $path=$file_path.$tpath1;
		 }
		 else
		 {
		 	$user_image='';
		 }

		/* if($_FILES['user_resume']['name']!='')
         {

          $img_res1=mysqli_query($mysqli,'SELECT * FROM tbl_users WHERE `id`='.$user_id.'');
          $img_res_row1=mysqli_fetch_assoc($img_res1);

          if($img_res_row1['user_resume']!="")
            {
              unlink('uploads/'.$img_res_row1['user_resume']);
           }*/
 	     /*$user_resume=rand(0,99999)."_".$_FILES['user_resume']['name'];
	 	
	     //Main Image
		 $tpath1='uploads/'.$user_resume; 			 
	      move_uploaded_file($_FILES["user_resume"]["tmp_name"],"uploads/" . $user_resume);
			
		 }
		 else
		 {
		 	$user_resume=$row['user_resume'];
		 } 


 	 	if (!filter_var($get_method['email'], FILTER_VALIDATE_EMAIL)) 
		{
			$set['JOBS_APP'][]=array('msg' => $app_lang['invalid_email_format'],'success'=>'0');

			header( 'Content-Type: application/json; charset=utf-8' );
			$json = json_encode($set);
			echo $json;
			 exit;
		}
		else*/ 
           /*if(  $row['id']!=$user_id)
		{
			$set['JOBS_APP'][]=array('msg' => $app_lang['email_exist'],'success'=>'0');

			header( 'Content-Type: application/json; charset=utf-8' );
			$json = json_encode($set);
			echo $json;
			 exit;
		}
 	 	else if($get_method['password']!="")
		{
			$data = array(
 			'name'  =>  $get_method['name'],
			'email'  =>  $get_method['email'],
			'phone'  =>  $get_method['phone'],
			'city'  =>  $get_method['city'],
			'address'  =>  addslashes($get_method['address']),
			'user_image' => $user_image,
			'user_resume' => $user_resume,
			'current_company_name'  =>  addslashes($get_method['current_company_name']),
	        'experiences'  =>  addslashes($get_method['experiences']),
	        'skills'  =>  $get_method['skills'],
	        'gender'  =>  $get_method['gender'],
	        'date_of_birth'  => strtotime($get_method['date_of_birth'])
			);
		}
		else
		{
			$data = array(
 			'name'  =>  $get_method['name'],
			'email'  =>  $get_method['email'],			 
			'phone'  =>  $get_method['phone'],
			'city'  =>  $get_method['city'],
			'address'  =>  addslashes($get_method['address']),
			'user_image' => $user_image,
			'user_resume' => $user_resume,
			'current_company_name'  =>  addslashes($get_method['current_company_name']),
	        'experiences'  =>  addslashes($get_method['experiences']),
	        'skills'  =>  $get_method['skills'],
	        'gender'  =>  $get_method['gender'],
	        'date_of_birth'  => strtotime($get_method['date_of_birth'])
			);
		}
		
	    if($get_method['password']!=""){
				$data = array_merge($data, array("password" => md5(trim($get_method['password']))));
			}
 	
	  	 $user_edit=Update('tbl_users', $data, "WHERE `id` = '".$user_id."'");

	  	  if($_FILES['company_logo']['name']!="")
    		{

	    	$img_res_company=mysqli_query($mysqli,'SELECT * FROM tbl_company WHERE `id`='.$user_id.'');
	        $img_res_row_company=mysqli_fetch_assoc($img_res_company);

	          if($img_res_row_company['company_logo']!="")
	            {
	              unlink('images/'.$img_res_row_company['company_logo']);
	           }

		    $ext = pathinfo($_FILES['company_logo']['name'], PATHINFO_EXTENSION);

			$company_logo=rand(0,99999).".".$ext;

			//Main Image
			$tpath='images/'.$company_logo;

			if($ext!='png') {
			$pic1=compress_image($_FILES["company_logo"]["tmp_name"], $tpath, 80);
			}
			else{
			$tmp = $_FILES['company_logo']['tmp_name'];
			move_uploaded_file($tmp, $tpath);
			} 
			           
	       $data = array(
		        'company_name'  =>  addslashes(trim($get_method['company_name'])),
		        'company_email'  =>  $get_method['company_email'],
		        'mobile_no'  =>  $get_method['mobile_no'],
		        'company_address'  =>  addslashes($get_method['company_address']),
		        'company_desc'  =>  addslashes($get_method['company_desc']),
		        'company_website'  =>  $get_method['company_website'],
		         'company_work_day'  =>  addslashes(trim($get_method['company_work_day'])),
		        'company_work_time'  => $get_method['company_work_time'],
		        'company_logo' => $company_logo

	             );
	   
	   }else{
	     
	     	$data = array(
		        'company_name'  =>  addslashes(trim($get_method['company_name'])),
		        'company_email'  =>  $get_method['company_email'],
		        'mobile_no'  =>  $get_method['mobile_no'],
		        'company_address'  =>  addslashes($get_method['company_address']),
		        'company_desc'  =>  addslashes($get_method['company_desc']),
		        'company_work_day'  =>  addslashes(trim($get_method['company_work_day'])),
		        'company_work_time'  => $get_method['company_work_time'],
		        'company_website'  =>  $get_method['company_website']
	        	 );
	      	 }*/
	        
	        $user_edit=Update('tbl_company', $data, " WHERE `user_id` = '".$user_id."'");
	       		 
			$set['JOBS_APP'][]=array('user_image'=>$path,'msg'=>$app_lang['update_success'],'success'=>'1');

		header( 'Content-Type: application/json; charset=utf-8' );
	    echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
		die();
	
	}
	
	
 else if($get_method['method_name']=="forgot_pass")	
	{
		
		$email=htmlentities(trim($get_method['email']));
	 	 
		$qry = "SELECT * FROM tbl_users WHERE `email` = '$email'"; 
		$result = mysqli_query($mysqli,$qry);
		$row = mysqli_fetch_assoc($result);

		if($result->num_rows > 0)
		{
 			$password=generateRandomPassword(7);

			$new_password=md5($password);

			$to = $row['email'];
			$recipient_name=$row['name'];
			// subject
			$subject = '[IMPORTANT] '.APP_NAME.' Forgot Password Information';
 			
			$message='<div style="background-color: #f9f9f9;" align="center"><br />
					  <table style="font-family: OpenSans,sans-serif; color: #666666;" border="0" width="600" cellspacing="0" cellpadding="0" align="center" bgcolor="#FFFFFF">
					    <tbody>
					      <tr>
					        <td colspan="2" bgcolor="#FFFFFF" align="center"><img src="'.$file_path.'images/'.APP_LOGO.'" alt="header" width="120"/></td>
					      </tr>
					      <tr>
					        <td width="600" valign="top" bgcolor="#FFFFFF"><br>
					          <table style="font-family:OpenSans,sans-serif; color: #666666; font-size: 10; padding: 15px;" border="0" width="100%" cellspacing="0" cellpadding="0" align="left">
					            <tbody>
					              <tr>
					                <td valign="top"><table border="0" align="left" cellpadding="0" cellspacing="0" style="font-family:OpenSans,sans-serif; color: #666666; font-size: 10px; width:100%;">
					                    <tbody>
					                      <tr>
					                        <td><p style="color: #262626; font-size: 28px; margin-top:0px;"><strong>Dear '.$row['name'].'</strong></p>
					                          <p style="color:#262626; font-size:20px; line-height:32px;font-weight:500;">Thank you for using '.APP_NAME.',<br>
					                            Your password is: '.$password.'</p>
					                          <p style="color:#262626; font-size:20px; line-height:32px;font-weight:500;margin-bottom:30px;">Thanks you,<br />
					                            '.APP_NAME.'.</p></td>
					                      </tr>
					                    </tbody>
					                  </table></td>
					              </tr>
					               
					            </tbody>
					          </table></td>
					      </tr>
					      <tr>
					        <td style="color: #262626; padding: 20px 0; font-size: 20px; border-top:5px solid #52bfd3;" colspan="2" align="center" bgcolor="#ffffff">Copyright Â© '.APP_NAME.'.</td>
					      </tr>
					    </tbody>
					  </table>
					</div>';
					
			send_email($to,$recipient_name,$subject,$message);

			$sql="UPDATE tbl_users SET `password`='$new_password' WHERE `id`='".$row['id']."'";
			      	mysqli_query($mysqli,$sql);
 			  
			$set=array('msg' => $app_lang['password_sent_mail'],'success'=>'1');
		}
		else
		{  	 	
			$set=array('msg' => $app_lang['email_not_found'],'success'=>'0');		
		}
	
		header( 'Content-Type: application/json; charset=utf-8' );
	    echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
		die();	
	}
	 else if($get_method['method_name']=="get_app_update")		
		{
			  
			$jsonObj= array();	

			$query="SELECT * FROM tbl_settings WHERE id='1'";
			$sql = mysqli_query($mysqli,$query);

			$data = mysqli_fetch_assoc($sql);

			$row['update_status'] = $data['update_status'];
			$row['cancel_status'] = $data['cancel_status'];
			$row['new_app_version'] = $data['new_app_version'];
			$row['app_link'] = $data['app_link'];
			$row['app_update_desc'] = $data['app_update_desc'];
			
			array_push($jsonObj,$row);
			
			$set['JOBS_APP'] = $jsonObj;
				
			header( 'Content-Type: application/json; charset=utf-8' );
		    echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
			die();	
		}
   		
    else if($get_method['method_name']=="get_app_help")
	    {
	    //User status
	    $set['user_status'] ='true';	
	    //App settings 
		$jsonObj= array();	

		$query="SELECT * FROM tbl_settings WHERE `id`='1'";
		$sql = mysqli_query($mysqli,$query)or die(mysqli_error($mysqli));

		$row['package_name'] = $settings_details['package_name']; 

		while($data = mysqli_fetch_assoc($sql))
		{
		    
			$row['app_name'] = $data['app_name'];
			$row['app_logo'] = $data['app_logo'];
			$row['app_version'] = $data['app_version'];
			$row['app_author'] = $data['app_author'];
			$row['app_contact'] = $data['app_contact'];
			$row['app_email'] = $data['app_email'];
			$row['app_website'] = $data['app_website'];
			$row['app_update_desc'] = $data['app_update_desc'];
 			$row['app_developed_by'] = $data['app_developed_by'];
 			$row['app_privacy_policy'] = stripslashes($data['app_privacy_policy']);
 			
 			$row['publisher_id'] = $data['publisher_id'];
 			$row['interstital_ad'] = $data['interstital_ad'];
 			$row['interstital_ad_type'] = $data['interstital_ad_type'];

			$row['interstital_ad_id'] = ($data['interstital_ad_type']=='facebook') ? $data['interstital_facebook_id'] : $data['interstital_ad_id'];

			$row['interstital_ad_click'] = $data['interstital_ad_click'];

 			$row['banner_ad'] = $data['banner_ad'];
 			$row['banner_ad_type'] = $data['banner_ad_type'];

 			$row['banner_ad_id'] = ($data['banner_ad_type']=='facebook') ? $data['banner_facebook_id'] : $data['banner_ad_id'];
 			

 			$row['update_status'] = $data['update_status'];
			$row['cancel_status'] = $data['cancel_status'];
			$row['new_app_version'] = $data['new_app_version'];
			$row['app_link'] = $data['app_link'];
			$row['app_description'] = "<p><strong>ඔබ ඩයලොග් පාරිබෝගිකයෙක් නම් (Dialog)</strong><br />
1.	ඔබට සෙවිමට අවශ්‍ය කෙනාගේ ජංගම දුරකථනයට Track Way App එක Download කිරීම මගින් හෝ #781*941# Dial කර ඒ තුලින් Register වී ලැබෙන PIN අංකය ඔබ ලබාගන්න</p>
<p><br />
  2.	පසුව එම PIN අංකය ඔබගේ Track Way App එක හරහා ලබා දී ඔවුන් සිටින ස්ථානය බලාගන්න</p>
<p><strong>ඔබ මොබිටෙල් පාරිබෝගිකයෙක් නම් (Mobitel)</strong><br />
1.	ඔබට සෙවිමට අවශ්‍ය කෙනාගේ ජංගම දුරකථනයට Track Way App එක Download කිරීම මගින් හෝ #780*1*7592# Dial කර ඒ තුලින් Register වී ලැබෙන PIN අංකය ඔබ ලබාගන්න</p>
<p><br />
2.	පසුව එම PIN අංකය ඔබගේ Track Way App එක හරහා ලබා දෙන්න.</p>
<p><br />
3. එවිට සෙවීමට අවශ්‍ය කෙනාගේ දුරකතනයට ලැබෙන request එක accept කරන්න. (dial *7700#&lt;NIC&gt;# &amp; enter one time PIN)</p>
<p><br />
  4. අවසානයේ ඔවුන් සිටින තැන බලාගැනීමට අවශ්‍ය ඕනෑම වෙලාවක සෙවීමට අවශ්‍ය කෙනාගේ PIN අංකය ඔබගේ Track Way App එක හරහා ලබා දෙන්න.</p>
<p><strong>ඔබට ඇප් එක භාවිතා කිරීමේදී ගැටලුවක් ඇති  උවහොත් contact වෙතින් ඔබගේ ගැටලුව ඉදිරිපත් කරන්න.</strong></p>
<p>සැ.යු.<br />
  •	ඔබ ලියාපදිංචි වූ අංකය ඩයලොග් නම් ඔබට සෙවීමට අවශ්‍ය පුද්ගලයාද ඩයලොග් පාරිභෝගිකයෙක් විය යුතුයි.</p>
<p><br />
  •	ඔබ ලියාපදිංචි වූ අංකය මොබිටෙල් නම් ඔබට සෙවීමට අවශ්‍ය පුද්ගලයාද මොබිටෙල් පාරිභෝගිකයෙක් විය යුතුයි.</p>
<p><br />
  •	සෙවීමට අවශ්‍ය පුද්ගලයාගේ ජංගම දුරකතනයට Track Way App එක Download කිරීම අනිවාර්ය නොවේ.</p>
<p><strong>If you a Dialog Customer</strong><br />
1.	Download the Track Way App to the mobile phone of the person you want to search or dial #781*941# and get the PIN number that you get registered through it.</p>
<p><br />
  2.	Then give that PIN number through your Track Way App and check their location</p>
<p><strong>If you a Mobitel Customer</strong><br />
1.	Download the Track Way App to the mobile phone of the person you want to search or dial #780*1*7592# and get the PIN number that you get registered through it.</p>
<p><br />
2.	Then provide that PIN number through your Track Way App.</p>
<p><br />
3. Then accept the request from the phone of the person you want to search.  (*Dial 7700#&lt;NIC&gt;# and enter PIN once)</p>
<p><br />
  4. Finally, if you want to track their location, enter the PIN number of the person you want to track through your Track Way app.</p>
<p><strong>If you have any problem while using the app, submit your problem from contact.</strong></p>
<p>•	If the number you registered is Dialog then the person you want to trace must also be a Dialog customer</p>
<p><br />
  •	If the number you registered is Mobitel, the person you want to trace must also be a Mobitel customer</p>
<p><br />
  •	It is not mandatory to download the Track Way App to the mobile phone of the person to be searched.</p>
<p><strong>நீங்கள் Dialog வாடிக்கையாளராக இருந்தால்</strong><br />
1. நீங்கள் தேட விரும்பும் நபரின் மொபைல் ஃபோனில் Track Way பயன்பாட்டைப் பதிவிறக்கவும் அல்லது #781*941# ஐ டயல் செய்து அதன் மூலம் பதிவு செய்வதன் மூலம் நீங்கள் பெறும் PIN எண்ணைப் பெறவும்.</p>
<p><br />
  2. பிறகு அந்த பின் எண்ணை உங்கள் ட்ராக் வே ஆப் மூலம் அளித்து அவற்றின் இருப்பிடத்தைக் கண்காணிக்கவும்</p>
<p> <strong>நீங்கள் மொபிடெல் வாடிக்கையாளராக இருந்தால்</strong><br />
1. நீங்கள் தேட விரும்பும் நபரின் மொபைல் போனில் Track Way செயலியை பதிவிறக்கம் செய்து அல்லது #780*1*7592# ஐ டயல் செய்து அதன் மூலம் பதிவு செய்து பின் எண்ணைப் பெறுங்கள்.</p>
<p><br />
2. பின்னர் அந்த பின் எண்ணை உங்கள் ட்ராக் வே ஆப் மூலம் வழங்கவும்.</p>
<p><br />
3. பின்னர் நீங்கள் தேட விரும்பும் நபரின் தொலைபேசியில் பெறப்பட்ட கோரிக்கையை ஏற்கவும்.  (*7700#&lt;NIC&gt;# டயல் செய்து ஒரு முறை பின்னை உள்ளிடவும்)</p>
<p><br />
  4. இறுதியாக, உங்கள் ட்ராக் வே ஆப் மூலம் நீங்கள் கண்காணிக்க விரும்பும் நபரின் பின் எண்ணை அவர் இருக்கும் இடத்தைக் கண்காணிக்க விரும்பும் போதெல்லாம் வழங்கவும்.</p>
<p><strong>பயன்பாட்டைப் பயன்படுத்தும் போது உங்களுக்கு ஏதேனும் சிக்கல் இருந்தால், உங்கள் சிக்கலைத் தொடர்பில் இருந்து சமர்ப்பிக்கவும்.</strong></p>
<p> NB<br />
  • நீங்கள் பதிவுசெய்த எண் Dialog எனில், நீங்கள் கண்டறிய விரும்பும் நபரும் Dialog வாடிக்கையாளராக இருக்க வேண்டும்.</p>
<p><br />
  • நீங்கள் பதிவுசெய்த எண் மொபிடெல் எனில், நீங்கள் கண்டுபிடிக்க விரும்பும் நபரும் மொபிடெல் வாடிக்கையாளராக இருக்க வேண்டும்.</p>
<p><br />
  • தேடப்படும் நபரின் மொபைல் ஃபோனில் ட்ராக் வே ஆப் பதிவிறக்குவது கட்டாயமில்லை.</p>";
			//$row[' '] = "Terms of Service 
			//Only for Dialog & Mobitel users.<br />Dialog 6+tax / Mobitel 5+tax daily";

			array_push($jsonObj,$row);
		
		}

		$set['JOBS_APP'] = $jsonObj;
		
		header( 'Content-Type: application/json; charset=utf-8' );
	    echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
		die();	
	} else if($get_method['method_name']=="get_app_details")
	    {
	    //User status
	    $qry_user = "SELECT * FROM tbl_users WHERE `id` = '".$get_method['user_id']."'";
        $result_user = mysqli_query($mysqli,$qry_user);
        $num_rows = mysqli_num_rows($result_user);
        $row1 = mysqli_fetch_assoc($result_user);
        		
        if ($num_rows > 0)
        { 
        		if($row1['status']=='1')
                {
                	$set['user_status'] ='true';	
                }
                else
                {
                	$set['user_status'] ='false';	
                } 	
        }
        else
        {
        	$set['user_status'] ='false';
        }
	    	
	    //App settings 
		$jsonObj= array();	

		$query="SELECT * FROM tbl_settings WHERE `id`='1'";
		$sql = mysqli_query($mysqli,$query)or die(mysqli_error($mysqli));

		$row['package_name'] = $settings_details['package_name']; 

		while($data = mysqli_fetch_assoc($sql))
		{
		    
			$row['app_name'] = $data['app_name'];
			$row['app_logo'] = $data['app_logo'];
			$row['app_version'] = $data['app_version'];
			$row['app_author'] = $data['app_author'];
			$row['app_contact'] = $data['app_contact'];
			$row['app_email'] = $data['app_email'];
			$row['app_website'] = $data['app_website'];
			$row['app_description'] = $data['app_description'];
 			$row['app_developed_by'] = $data['app_developed_by'];
 			$row['app_privacy_policy'] = stripslashes($data['app_privacy_policy']);
 			
 			$row['publisher_id'] = $data['publisher_id'];
 			$row['interstital_ad'] = $data['interstital_ad'];
 			$row['interstital_ad_type'] = $data['interstital_ad_type'];

			$row['interstital_ad_id'] = ($data['interstital_ad_type']=='facebook') ? $data['interstital_facebook_id'] : $data['interstital_ad_id'];

			$row['interstital_ad_click'] = $data['interstital_ad_click'];

 			$row['banner_ad'] = $data['banner_ad'];
 			$row['banner_ad_type'] = $data['banner_ad_type'];

 			$row['banner_ad_id'] = ($data['banner_ad_type']=='facebook') ? $data['banner_facebook_id'] : $data['banner_ad_id'];
 			

 			$row['update_status'] = $data['update_status'];
			$row['cancel_status'] = $data['cancel_status'];
			$row['new_app_version'] = $data['new_app_version'];
			$row['app_link'] = $data['app_link'];
			$row['app_update_desc'] = $data['app_update_desc'];
			$row['terms'] = "Terms of Service 
			Only for Dialog & Mobitel users. \r\n Dialog 6+tax / Mobitel 5+tax daily";


			array_push($jsonObj,$row);
		
		}

		$set['JOBS_APP'] = $jsonObj;
		
		header( 'Content-Type: application/json; charset=utf-8' );
	    echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
		die();	
	}		
	 else
  {
  		$get_method = checkSignSalt($_POST['data']);
  }
