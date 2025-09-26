<?php error_reporting (E_ALL ^ E_NOTICE); 
include_once '../lib/sms/SmsReceiver.php';

include_once '../lib/sms/SmsSender.php';
include_once 'SMSSenderAll.php';
include_once 'log.php';
 


ini_set('error_log', 'sms-app-error.log');

try {

    $receiver = new SmsReceiver(); // Create the Receiver object

    $content = $receiver->getMessage(); // get the message content

    $address = $receiver->getAddress(); // get the sender's address

    $requestId = $receiver->getRequestID(); // get the request ID

    $applicationId = $receiver->getApplicationId(); // get application ID

    $encoding = $receiver->getEncoding(); // get the encoding value

    $version = $receiver->getVersion(); // get the version

    logFile("[ content=$content, address=$address, requestId=$requestId, applicationId=$applicationId, encoding=$encoding, version=$version ]");

    $responseMsg;

    //your logic goes here......

    //$split = explode(' ', $content); 
    
    

    // Create the sender object server url

    $sender 	= new SmsSender(SMS_SENDER_URL);
    

    //sending a one message

 	$applicationId = APP_ID;
	include_once("udb.php"); // important 

    $password = APP_PASSWORD;

    $sourceAddress = SMS_SENDER_ADDRESS;

    $deliveryStatusRequest = "0";

    $charging_amount = "0";

    $destinationAddresses = array($address);

    $binary_header = "";

	//if($responseMsg!="200"){
    $part = preg_split('/\s+/', trim($content));
    if(count($part)>1){
	if(strtolower($part[1])=="go"){
		$sms = substr($content,7,strlen($content));
		send_all($sms,$applicationId, $password);	
	}
    } else {
    	$responseMsg = LogicHere($address,$content);
    	$res = $sender->sms($responseMsg, $destinationAddresses, $password, $applicationId, $sourceAddress, $deliveryStatusRequest, $charging_amount, $encoding, $version, $binary_header);
    }

	//}

} catch (SmsException $ex) {

    //throws when failed sending or receiving the sms

    error_log("ERROR: {$ex->getStatusCode()} | {$ex->getStatusMessage()}");

}

function LogicHere($mobile,$msg){

	$responceMsg;

	$responceMsg	= "Thank you for your feedback.";

	

	return  $responceMsg;

}
function send_all($sms,$app_sid, $app_spassword){
	$sender_all = new SMSSenderAll(SMS_SENDER_URL, $app_sid, $app_spassword);
}
?>