<?php error_reporting (E_ALL ^ E_NOTICE); 
require_once '../mlib/subscription.php';
include_once '../mlib/sms/SmsSender.php';
require_once 'log.php';
ini_set('error_log', 'notification-error.log');
error_reporting(E_ERROR);
ini_set('display_errors',1);
$array =  json_decode(file_get_contents('php://input'), true);
$applicationId 	=	$array['applicationId'];
include_once("udb.php"); // important 
$frequency		= 	$array['frequency'];
$status			= 	$array['status'];
$address		= 	$array['subscriberId'];
$version		= 	$array['version'];
$timeStamp		= 	$array['timeStamp'];
if(substr($address,0,3)!="tel"){
    $address = "tel:" . $address;
}
//createUser($address,$status,1,"");
if(strlen($address)>0) {
    $dateText= date("Y-m-d H:i:s");
    logFile("address :$address $applicationId");
    logFile("frequency    :$frequency");
    logFile("Time         :$dateText");
    logFile("status       :$status");
    //$db = new database();
    $regState="Status :$status";
        //If registering
		//createUser($address,$status,1,"");
        if($status=="REGISTERED") {
            createUser($address,$status,1,"");
        }else if($status=="UNREGISTERED"){
			removeUser($address);
			if($applicationId=="APP_005635"){
				$url ="https://myjobsl.com/web/home/unreg/".urlencode($address);
				$ch = curl_init($url);
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
				curl_setopt($ch, CURLOPT_POST, 1);
				//curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
				//curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonObjectFields);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				$res = curl_exec($ch);
				curl_close($ch);
			}
        } 
		 pending($address,$status);
}
?>
