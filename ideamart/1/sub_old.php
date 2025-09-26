<?php error_reporting (E_ALL ^ E_NOTICE); 
require_once '../lib/subscription.php';

include_once '../lib/sms/SmsSender.php';

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

if(strlen($address)>0) {

    $dateText= date("Y-m-d H:i:s");

    logFile("address :$address");

    logFile("frequency    :$frequency");

    logFile("Time         :$dateText");

    logFile("status       :$status");

    //$db = new database();

    $regState="Status :$status";

        //If registering

        if($status=="REGISTERED") {

            createUser($address,$status,1,"");

        }else if($status=="UNREGISTERED"){

			removeUser($address);

        } 

		 pending($address,$status);

}

?>

