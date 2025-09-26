<?php error_reporting (E_ALL ^ E_NOTICE); 

include_once 'log.php'; 

include_once '../mlib/ussd/MoUssdReceiver.php'; 

include_once '../mlib/ussd/MtUssdSender.php'; 

include_once '../mlib/sms/SmsSender.php'; 

include_once '../mlib/subscription.php'; 
logfile("Line 1");

$receiver = new MoUssdReceiver(); 

$receiverSessionId = $receiver->getSessionId(); 

session_id($receiverSessionId); 

  session_start(); 

  $content = $receiver->getMessage(); 

  $address = $receiver->getAddress(); 

  $requestId = $receiver->getRequestID(); 

  $applicationId = $receiver->getApplicationId(); 

  include_once("udb.php");

  $password = APP_PASSWORD; 

  $applicationId = APP_ID; 

  $sub = new Subscription(SUBSCRIPTION_URL); 

  $encoding = $receiver->getEncoding(); 

  $version = $receiver->getVersion(); 

  $sessionId = $receiver->getSessionId(); 

  $ussdOperation = $receiver->getUssdOperation(); 
	
logfile($applicationId.$address);
  $responseMsg = array(    "main" => "Do you want to Subscribe to ".APP_NAME." and Get updates \n1.Yes \n0.No", 	"mainError" => "Invaliad Input ".APP_NAME." \n1.Subscribe \n0.Exit", 	"exit" => "000",     "register" => "999" ); 

if ($ussdOperation == "mo-init") {  	

	if(userExist($address)===true){ 		

		$_SESSION['menu-Opt'] = "jobcat"; 

		loadUssdSender($sessionId, "Select Category ".jobCat(),$address); 

	} else { 		

		loadUssdSender($sessionId, $responseMsg["main"],$address); 

		logfile($responseMsg["main"]);

		if (!(isset($_SESSION['menu-Opt']))) { 			

			$_SESSION['menu-Opt'] = "main"; 

 		} 	

	} 

} 

	if ($ussdOperation == "mo-cont") {     

		$menuName = null; 

		$responseReply = null; 

    	switch ($_SESSION['menu-Opt']) {         

			case "mainError":             

			switch ($receiver->getMessage()) {                 

			case "1": 					                      	

				$menuName = "register"; 

				$res = $sub->RegUser($applicationId,$password,"1.0",$address); 

				$json = json_decode($res, true); 

				createUser($address,$json['statusDetail'],$json['statusCode'],0); 

                break; 

                case "0":                     

					$menuName = "exit"; 

                    break; 

				default: 					  					

					

                    //$menuName = "mainError"; 

					$code = $receiver->getMessage();

					//update_to_agent($address,$code);

					$response = loadUssdSender($sessionId, "Invaliad Input ".APP_NAME." \n1.Subscribe \n0.Exit",$address); 

                    $menuName = "mainError";

                break; 

            }             $_SESSION['menu-Opt'] = $menuName; 

             break; 

			

			case "main":             

			switch ($receiver->getMessage()) {                 

						case "1": 					                      

						$menuName = "register"; 

					  	$res = $sub->RegUser($applicationId,$password,"1.0",$address); 

						$json = json_decode($res, true); 

						if($json['statusCode']=="E1351"){ 							

							//logFile("Register := " . $json['statusDetail']); 

							createUser($address,$json['statusDetail'],$json['statusCode'],0); 

						} 					

						$response = loadUssdSender($sessionId, $responseMsg[$menuName],$address); 

						break; 

						case "0":                     

							$menuName = "exit"; 

					    break; 

                        default:                      					

							

							$code = $receiver->getMessage();

							update_to_agent($address,$code);

							$menuName = "mainError"; 

							$response = loadUssdSender($sessionId, "Invaliad Input ".APP_NAME." \n1.Subscribe \n0.Exit",$address); 

                    	break; 

            }             $_SESSION['menu-Opt'] = $menuName; 

              break; 

	   	  case "jobcat":             switch ($receiver->getMessage()) {                                                   default:  $jobmessage=job($receiver->getMessage());

sendSMS($address, $jobmessage); 

                      $menuName = "job"; 

						$_SESSION['menu-Opt'] = "job"; 

						$response = loadUssdSender($sessionId, $jobmessage."\n1. Back to Category \n0. Exit",$address); 

                    break; 

            }       break; 

	   	  	case "job":             

			switch ($receiver->getMessage()) {               	

				case "0":                     

				$menuName = "exit"; 

					                    

										break; 

				case "1":                     $_SESSION['menu-Opt'] = "jobcat"; 

					loadUssdSender($sessionId, "Select Category ".jobCat(),$address); 

					                 break; 

                

default:                         $menuName = "job"; 

						$_SESSION['menu-Opt'] = "job"; 

						$response = loadUssdSender($sessionId, "Invaliad Input.\n1. Back to Category \n0. Exit",$address); 

                    break; 

            }       break; 

	   	       }     if ($receiver->getMessage() == "000") {         $responseExitMsg = "000"; 

        $response = loadUssdSender($sessionId, $responseExitMsg,$address); 

        session_destroy(); 

    } else {              } } 

	function loadUssdSender($sessionId, $responseMessage,$destinationAddress) {    

	global $password; 

    global $applicationId; 

     	$ussdOperation = "mt-fin"; 

   		$chargingAmount = "1"; 

   		$encoding = "440"; 

   		$version = "1.0"; 

	if ($responseMessage == "000") {        

		 $ussdOperation = "mt-fin"; 

		$responseMessage = "Thank you for using ".APP_NAME.". Exit Program!"; 

    } else if ($responseMessage == "999") {         $ussdOperation = "mt-fin"; 

		$responseMessage = "Thank you for Subscribe to ".APP_NAME."!"; 

    } else {        

		 $ussdOperation = "mt-cont"; 

   	}    

	 try {      	

	 	  $sender = new MtUssdSender(USSD_SENDER_URL); 

          $response = $sender->ussd($applicationId, $password, $version, $responseMessage,         	$sessionId, $ussdOperation, 						$destinationAddress, $encoding, $chargingAmount);

		  //logfile($response); 

        return $response; 

    } catch (UssdException $ex) {                 

		 error_log("USSD ERROR: {$ex->getStatusCode()} | {$ex->getStatusMessage()}"); 

        return null; 

    } } ?>