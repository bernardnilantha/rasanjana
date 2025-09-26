<?php error_reporting (E_ALL ^ E_NOTICE); 

include_once '../mlib/sms/SmsReceiver.php';



include_once '../mlib/sms/SmsSender.php';



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



    $responseMsg = LogicHere($address,$content);



    // Create the sender object server url



    



    //sending a one message



 	
    include_once("udb.php"); // important 


    $applicationId = APP_ID;
	
    $password = APP_PASSWORD;

    
	
    $sourceAddress = SMS_SENDER_ADDRESS;

    

    $deliveryStatusRequest = "0";



    $charging_amount = "0";



    $destinationAddresses = array($address);



    $binary_header = "";



	//if($responseMsg!="200"){

     $sender = new SmsSender(SMS_SENDER_URL);	

    $res = $sender->sms($responseMsg, $destinationAddresses, $password, $applicationId, $sourceAddress, $deliveryStatusRequest, $charging_amount, $encoding, $version, $binary_header);



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



?>