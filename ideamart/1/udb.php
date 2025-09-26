<?php 
date_default_timezone_set('Asia/Colombo'); 
			$sqle		=	"SELECT * FROM app_table WHERE app_id='$applicationId'";
			$ereply		=	mysqli_query(mysqli_connect('localhost', 'bernard_root', 'gigabyte', 'bernard_smartapps'),$sqle);
			$rowe 		= 	mysqli_fetch_array($ereply);
			$app_name			=	$rowe['app_name'];
			$app_category		=	$rowe['app_category'];
			$app_sub_category	=	$rowe['app_sub_category'];
			$app_sms			=	$rowe['app_sms'];
			$app_port			=	$rowe['app_port'];
			$app_key			=	$rowe['app_key'];
			$sms_sender_address	=	$rowe['sms_sender_address'];
			$ussd_code			=	$rowe['ussd_code'];
			$ussd_keyword		=	$rowe['ussd_keyword'];
			$key_gen			=	$rowe['key_gen'];
			$app_id				=	$rowe['app_id'];
			$app_pswd			=	$rowe['app_password'];
			$app_ussd			=	$rowe['app_ussd'];
			$app_database		=	$rowe['app_database'];
			$intmsg			=       $rowe['intmsg'];
			$sendintmsg			=       $rowe['sendintmsg'];
			
define('APP_ID', $app_id); 
define('INT_MSG', $intmsg);
define('SEND_INT_MSG', $sendintmsg);
	
define('APP_PASSWORD', $app_pswd); 
define('SMS_SENDER_ADDRESS', $sms_sender_address); 
define('SMS_DELIVERY_REPORT', 0); 
define('APP_NAME', $app_name); 
define('APP_USSD', $app_ussd); 
define('APP_SMS', $app_sms); 
define('APP_PORT', $app_port); 
define('APP_DB', $app_database); 
define('APP_KEY', $app_key);
define('SUBSCRIPTION_BASE_URL', 'http://api.dialog.lk:8080/subscription/query-base/'); 
define('SMS_SENDER_URL', 'http://api.dialog.lk:8080/sms/send');
define('USSD_SENDER_URL', 'http://api.dialog.lk:8080/ussd/send/');
define('SUBSCRIPTION_URL', 'http://api.dialog.lk:8080/subscription/send/');
define('SUBSCRIPTION_BASE_URL', 'http://api.dialog.lk:8080/subscription/query-base/');
define('SUBSCRIPTION__BASE_URL', 'http://api.dialog.lk:8080/subscription/query-base/');
$today_full=date("Y-m-d H:i:s"); 
		$today=date("Y-m-d"); 
		$month=date("m"); 
		$day=date("d"); 
		function db_connecti() { 			
			$host_name = 'localhost'; 
			$database = APP_DB; 
			$user_name = 'bernard_root'; 
			$password = 'gigabyte'; 
			$db_connection = mysqli_connect($host_name, $user_name, $password, $database) or die('Unable to establish a DB connection'); 
			return $db_connection; 
	  	}  
function sendSMS($address, $message){     
$sender = new SmsSender(SMS_SENDER_URL); 
    
$encoding = "0"; 
    $version =  "1.0"; 
    
$res = $sender->sms($message, $address, APP_PASSWORD, APP_ID, SMS_SENDER_ADDRESS, SMS_DELIVERY_REPORT, 0, $encoding, $version, ""); 
    $response = json_decode($res, true); 
    
$requestId = $response['requestId']; 
    
$response = $response['destinationResponses']; 
    
$response =$response[0]; 
    
$statusDetail = $response['statusDetail']; 
    
$statusCode = $response['statusCode']; 
    
$time = date("y-m-d H:i",time()); 
    
logFile(">> Time    : $time Addreess : $address msg : $message"); 
    
; 
} 
function removeUser($address){ 	
$key =APP_KEY; 
$sql3 = "UPDATE ".$key."_subcribers SET dtime= '".time()."', active=0 WHERE masknumber = '$address'"; 
	 mysqli_query(db_connecti(),$sql3); 
} 
function  createUser($address,$statustext,$statusnum,$agent){ 	
		$key =APP_KEY; 
		$dbcon = db_connecti();
		$sql_cat		=	"SELECT * FROM ".$key."_subcribers WHERE masknumber='$address' "; 
 		$reply_cat		=	mysqli_query($dbcon,$sql_cat) ; 
 		$row_cat 		= 	mysqli_fetch_array($reply_cat); 
		$rowid			=	$row_cat['rowid'];
		if($rowid>0){
			$sql4 = "UPDATE ".$key."_subcribers SET active=1 WHERE masknumber='$address' "; 
				mysqli_query($dbcon,$sql4); 
 		} else {
			$sql3 = "insert ".$key."_subcribers ( masknumber, statustext, statusnum,  atime) " . 				
			"values ('$address',  '$statustext', '$statusnum' ,   '".time()."' )"; 
				mysqli_query($dbcon,$sql3); 
		}
		/*$sql_cat		=	"SELECT * FROM app_table WHERE app_key='$key' "; 
 		$reply_cat		=	mysqli_query(db_connecti(),$sql_cat) ; 
 		$row_cat 		= 	mysqli_fetch_array($reply_cat); 
		$category		=	$row_cat['intmsg'];*/
		$msg1 =INT_MSG.". Await for the New updates."; 
		
	 	//logFile($msg1); 
	if(SEND_INT_MSG==1){
		sendSMS($address,$msg1); 
	}
} function jobCat(){ 	
	$key =APP_KEY;
	$sql_cat		=	"SELECT * FROM ".$key."_job_categories WHERE active=1 ORDER BY cat_text ASC "; 
	$reply_cat		=	mysqli_query(db_connecti(),$sql_cat) ; 
	$i=0; 
	while($row_cat 	= 	mysqli_fetch_array($reply_cat)){ 		$i=$i+1; 
		$category		=	$row_cat['cat_text']; 
		$active			=	$row_cat['active']; 
		$cat_id			=	$row_cat['cat_id']; 
		$jobCat .= "\n".$i.". ".$category; 
	} 	return $jobCat; 
} 
function job($id){ 	
$key =APP_KEY;
$sql_cat		=	"SELECT * FROM ".$key."_job_categories WHERE active=1 ORDER BY cat_text ASC "; 
	
$reply_cat		=	mysqli_query(db_connecti(),$sql_cat) ; 
	
$cat_rows 	= 	mysqli_num_rows($reply_cat); 
	
$i=0; 
	
if(!is_numeric($id) || $cat_rows <$id){ 		
	$jobCat = "Invaliad input"; 
	
} else { 		
while($row_cat 	= 	mysqli_fetch_array($reply_cat)){ 			
$i=$i+1; 
			
if($id==$i){ 				
$category		=	$row_cat['cat_text']; 
				
$active			=	$row_cat['active']; 
				
$cat_id			=	$row_cat['cat_id']; 
				
$sql_job		=	"SELECT * FROM ".$key."_job_list WHERE cat_id='$cat_id' ORDER BY dtime DESC LIMIT 4 "; 
				$reply_job		=	mysqli_query(db_connecti(),$sql_job) ; 
				
$job_rows 	= 	mysqli_num_rows($reply_job); 
				
if($job_rows>0){ 					
$jobCat ="";
$no=0;
while( $row_job 	= 	mysqli_fetch_array($reply_job)){
$no=$no+1;
					
$jobCat .= $no.". ".$row_job['sms']."\n\n"; 
}
				
}  else { 					
$jobCat = "No Content under this category."; 
				
} 			} 		} 	} 	
return $jobCat; 
} 
function pending($address,$statustext){ 	
$key =APP_KEY;
$sql3 = "insert ".$key."_subcribers_pending ( masknumber, statustext, atime) " . 	"values ('$address',  '$statustext',  '".time()."' )"; 
	//logFile($sql3);
	mysqli_query(db_connecti(),$sql3) ; 
} 
function getModifiedTimeStamp($timeStamp){     try {         $date= new DateTime($timeStamp,new DateTimeZone('Asia/Colombo')); 
    } catch (Exception $e) {         echo $e->getMessage(); 
        exit(1); 
    }     return $date->format('Y-mcreateUserd H:i:s'); 
} function userExist($address){ 	
	$key =APP_KEY;
	$sql_dialog		=	"SELECT * FROM ".$key."_subcribers WHERE masknumber = '$address' AND active=1"; 
	 
	$reply_dialog	=	mysqli_query(db_connecti(),$sql_dialog) or die (mysqli_error()); 
	$row_dialog 	= 	mysqli_fetch_array($reply_dialog); 
	$dialog_num		=	mysqli_num_rows($reply_dialog); 
	if($dialog_num>0 ){ 		return true; 
	} } function base($url,$appid,$pw){      $arrayField = array("applicationId" => $appid,         "password" => $pw     ); 
     $jsonStream = json_encode($arrayField); 
     $ch = curl_init($url); 
    curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, false); 
    curl_setopt($ch,CURLOPT_POST, 1); 
    curl_setopt($ch,CURLOPT_HTTPHEADER, array('Content-Type: application/json')); 
    curl_setopt($ch,CURLOPT_POSTFIELDS, $jsonStream); 
    curl_setopt($ch,CURLOPT_RETURNTRANSFER, true); 
    $res = curl_exec($ch); 
    curl_close($ch); 
    return $res; 
}
function update_to_agent($address,$agent_id){
	$key =APP_KEY;
	$sql3 = "insert ".$key."_agent_registration ( dmobile, agent_id, atime) " .
	"values ('$address',  '$agent_id',  '".time()."' )";
	mysqli_query(db_connecti(),$sql3) or die (mysqli_error());
}
function get_agent($address){
	$key =APP_KEY;
	$sql_dialog		=	"SELECT * FROM ".$key."_agent_registration WHERE dmobile = '$address' ";
	$reply_dialog	=	mysqli_query(db_connecti(),$sql_dialog) or die (mysqli_error()); 
	$row_dialog 	= 	mysqli_fetch_array($reply_dialog);
	$agent_id 		=	$row_dialog ['agent_id'];
	if($agent_id==""){
		$agent_id = 0;
	}
	return $agent_id;
}
function agent_name($username){
		$key =APP_KEY;
		$sql	=	"SELECT * FROM ".$key."_agents WHERE agent_id='$username'";
		$reply	=	mysqli_query(db_connecti(),$sql) or die (mysqli_error());
		$rows 	= 	mysqli_fetch_array($reply);
		$name	=	$rows['name'];
		return $name;
}
function count_pending($agent_id,$date,$status){
		$key =APP_KEY;
		$sql	=	"SELECT COUNT(".$key."_subcribers.rowid) AS t FROM 
								".$key."_subcribers, ".$key."_charging 
								WHERE ".$key."_charging.subid=".$key."_subcribers.rowid AND ".$key."_charging.charging='$status' AND 
								".$key."_charging.date = '$date'  AND  ".$key."_subcribers.agent_id='$agent_id'";
		$reply	=	mysqli_query(db_connecti(),$sql) or die (mysqli_error());
		$rows 	= 	mysqli_fetch_array($reply);
		$t		=	$rows['t'];
		return $t;
}
function count_sub($agent_id,$f,$t,$status){
	$key =APP_KEY;
	if($status==4){
		$sql	=	"SELECT COUNT(rid) AS t
					FROM ".$key."_agent_registration
					WHERE  ".$key."_agent_registration.agent_id ='$agent_id'  AND FROM_UNIXTIME(".$key."_agent_registration.atime,'%Y-%m-%d') 
					BETWEEN '$f'  AND '$t'"; 
		$reply	=	mysqli_query(db_connecti(),$sql) or die (mysqli_error());
		$rows 	= 	mysqli_fetch_array($reply);
		$t		=	$rows['t'];
	} else {
		$sql	=	"SELECT COUNT(rowid) AS t
					FROM ".$key."_subcribers ,".$key."_agent_registration
					WHERE ".$key."_agent_registration.dmobile=".$key."_subcribers.masknumber
					AND ".$key."_agent_registration.agent_id ='$agent_id'  AND ".$key."_subcribers.active='$status' AND 			
					FROM_UNIXTIME(".$key."_subcribers.atime,'%Y-%m-%d') BETWEEN '$f'  AND '$t'"; 
		$reply	=	mysqli_query(db_connecti(),$sql) or die (mysqli_error());
		$rows 	= 	mysqli_fetch_array($reply);
		$t		=	$rows['t'];
	}
	return $t;
}
 ?>