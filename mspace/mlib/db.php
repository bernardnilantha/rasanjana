<?php error_reporting (E_ALL ^ E_NOTICE);

		date_default_timezone_set('Asia/Colombo');

		$today_full=date("Y-m-d H:i:s");

		$today=date("Y-m-d");

		$month=date("m");

		$day=date("d");

		

		$db_connection = db_connect();

		mysql_select_db (db_name(),$db_connection) or exit();

		function db_name() {

			  return ("bernard_vlocate");

		  }

  		

	  	function db_connect() {

			//gigabyte

			$db_connection = @mysql_connect("localhost", "bernard_root", "gigabyte") or die('Unable to establish a DB connection');

			return $db_connection;

	  	}  

define('APP_PASSWORD', '719f78fde3e28a0fae3670f5e645f865');

define('APP_ID', 'APP_026759');

define('SMS_SENDER_ADDRESS', 'vLocate');

define('SMS_DELIVERY_REPORT', 0);



define('SMS_SENDER_URL', 'http://api.dialog.lk:8080/sms/send');

define('USSD_SENDER_URL', 'http://api.dialog.lk:8080/ussd/send/');

define('SUBSCRIPTION_URL', 'http://api.dialog.lk:8080/subscription/send/');



/*define('USSD_SENDER_URL', 'http://localhost:7000/ussd/send/');

define('SMS_SENDER_URL', 'http://localhost:7000/sms/send');

define('SUBSCRIPTION_URL', 'http://localhost:7000/subscription/send/');*/

$SERVICE_TYPE = "IMMEDIATE";

$RESPONSE_TIME ="DELAY_TOLERANCE";

$FRESHNESS = "LOW";

$HORIZONTAL_ACCURACY = "1000";

define('LBS_QUERY_SERVER_URL' , 'http://api.dialog.lk:8080/lbs/locate');
function userPinExist($id){
	$sql_dialog		=	"SELECT * FROM subcribers WHERE pinno = '$id'";
	$reply_dialog	=	mysql_query($sql_dialog) or die (mysql_error()); 
	$row_dialog 	= 	mysql_fetch_array($reply_dialog);
	$dialog_num		=	mysql_num_rows($reply_dialog);
	return false;
	if($dialog_num>0){
		return true;
	}
}
function userExist($address){
	$sql_dialog		=	"SELECT * FROM subcribers WHERE masknumber = '$address' AND active=1";
	$reply_dialog	=	mysql_query($sql_dialog) or die (mysql_error()); 
	$row_dialog 	= 	mysql_fetch_array($reply_dialog);
	$dialog_num		=	mysql_num_rows($reply_dialog);
	//return false;
	if($dialog_num>0){
		return true;
	}
}
function myPin($address){
	$sql_dialog		=	"SELECT * FROM subcribers WHERE masknumber = '$address' AND active=1";
	$reply_dialog	=	mysql_query($sql_dialog) or die (mysql_error()); 
	$row_dialog 	= 	mysql_fetch_array($reply_dialog);
	$dialog_num		=	mysql_num_rows($reply_dialog);
	return $row_dialog ['pinno'];
}
function userAddress($id){
	$sql_dialog		=	"SELECT * FROM subcribers WHERE pinno = '$id'";
	$reply_dialog	=	mysql_query($sql_dialog) or die (mysql_error()); 
	$row_dialog 	= 	mysql_fetch_array($reply_dialog);
	$dialog_num		=	mysql_num_rows($reply_dialog);
	return false;
	if($dialog_num>0){
		return $row_dialog['masknumber'];
	}
	
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

    logFile(">> Time    : $time");
    logFile(">> Status  : $statusDetail($statusCode)");
    logFile(">> Request : $requestId");

    print "SMS :$statusDetail($statusCode)<br>";
}
function locate($address,$pin){
	//logFile(time());
	$SERVICE_TYPE = "IMMEDIATE";
	$RESPONSE_TIME ="DELAY_TOLERANCE";
	$FRESHNESS = "LOW";
	$HORIZONTAL_ACCURACY = "1000";
	$request = new LbsRequest(LBS_QUERY_SERVER_URL);
	$request->setAppId(APP_ID);
	$request->setAppPassword(APP_PASSWORD);
	$request->setSubscriberId($address);
	$request->setServiceType($SERVICE_TYPE);
	$request->setFreshness($FRESHNESS);
	$request->setHorizontalAccuracy($HORIZONTAL_ACCURACY);
	$request->setResponseTime($RESPONSE_TIME);
	
	//logFile("[ $request ]");
	$lbsClient = new LbsClient();
	$lbsResponse = new LbsResponse($lbsClient->getResponse($request));
	$lbsResponse->setTimeStamp(getModifiedTimeStamp($lbsResponse->getTimeStamp()));
	//$log->LogDebug("Lbs response:".$lbsResponse->toJson());
	$data = $lbsResponse->toJson();
	$jsondata = json_decode($data);
	$statuscode=$jsondata->{'statusCode'};
	if($statuscode=="S1000"){
		$lat	=$jsondata->{'latitude'};
		$lng	=$jsondata->{'longitude'};
		//logFile(time());
		$addr=locationAddress($lat,$lng);
		

		//update_locate($pin,$lat,$lng,$addr);
		//$addr="Test";
	} else {
		$addr="Unable Locate.";
	}
	$message = $pin. " at \n".$addr;
	//logFile(time());
	sendSMS($address, $message);
	return  $message;
	//sendSMS($address, $message);								
}
function locateMap($address,$pin){
	$SERVICE_TYPE = "IMMEDIATE";
	$RESPONSE_TIME ="DELAY_TOLERANCE";
	$FRESHNESS = "LOW";
	$HORIZONTAL_ACCURACY = "1000";
	$request = new LbsRequest(LBS_QUERY_SERVER_URL);
	$request->setAppId(APP_ID);
	$request->setAppPassword(APP_PASSWORD);
	$request->setSubscriberId($address);
	$request->setServiceType($SERVICE_TYPE);
	$request->setFreshness($FRESHNESS);
	$request->setHorizontalAccuracy($HORIZONTAL_ACCURACY);
	$request->setResponseTime($RESPONSE_TIME);
	
	//logFile("[ $request ]");
	$lbsClient = new LbsClient();
	$lbsResponse = new LbsResponse($lbsClient->getResponse($request));
	$lbsResponse->setTimeStamp(getModifiedTimeStamp($lbsResponse->getTimeStamp()));
	//$log->LogDebug("Lbs response:".$lbsResponse->toJson());
	$data = $lbsResponse->toJson();
	$jsondata = json_decode($data);
	$statuscode=$jsondata->{'statusCode'};
	if($statuscode=="S1000"){
		$lat	=$jsondata->{'latitude'};
		$lng	=$jsondata->{'longitude'};
		
		$message = "http://maps.google.com?q=".$lat.",".$lng;
	} else {
		$message="Unable Locate.";
	}
	
	return  $message;
	//sendSMS($address, $message);								
}
function locationAddress($lat,$lng){
			$api_key = "AIzaSyBnhMyRyENAmcLFGteCnqGEmbabApzQgME";
			// format this string with the appropriate latitude longitude
			//$url = 'http://maps.googleapis.com/maps/api/geocode/json?latlng='.$lat.','.$lng.'&output=json&sensor=false';
			$url = 'https://maps.googleapis.com/maps/api/geocode/json?latlng='.$lat.','.$lng.'&output=json&sensor=false&key=' . $api_key;
			// make the HTTP request
			$data = @file_get_contents($url);
			// parse the json response
			$jsondata = json_decode($data,true);
			//logFile("[ $jsondata ]");
			// if we get a placemark array and the status was good, get the addres
			if($jsondata ['status']=="OK") {
				$gresult = mysql_real_escape_string($data);
				$addr1 = $jsondata ['results'][0]['address_components'][0]['long_name'];
				$addr2 = $jsondata ['results'][0]['address_components'][1]['long_name'];  
				$addr3 = $jsondata ['results'][0]['address_components'][2]['long_name'];  
				$addr=$addr1.",".$addr2.",".$addr3;
			} else {
				$addr="Unable Locate.";
			}
				return $addr;
}
function update_locate($pin,$lat,$lng,$address){
	$sql3 = "insert locate (pin, lat, lng, address, dtime) " .
			"values ('$pin',  '$lat', '$lng' , '$address',  '".time()."' )";
	mysql_query($sql3) or die (mysql_error());
}
 
function removeUser($address){
	 $sql3 = "UPDATE subcribers SET dtime= '".time()."', active=0 WHERE masknumber = '$address'";
	 mysql_query($sql3) or die (mysql_error());
}
function createUser($address,$statustext,$statusnum,$agent){
	$sql_dialog		=	"SELECT * FROM subcribers WHERE masknumber = '$address'";
	$reply_dialog	=	mysql_query($sql_dialog) or die (mysql_error()); 
	$row_dialog 	= 	mysql_fetch_array($reply_dialog);
	$dialog_num		=	mysql_num_rows($reply_dialog);
	if($dialog_num>0){
		$id 	= $row_dialog['pinno'];
		$rowid  = $row_dialog['rowid'];
		$sql3 = "UPDATE subcribers SET utime= '".time()."', active=1 WHERE rowid='$rowid'";
		mysql_query($sql3) or die (mysql_error());
	} else {
		$id = rand(10000,99999);
		while(userPinExist($id)){
			$id = rand(10000,99999);
		}
		$sql3 = "insert subcribers ( masknumber, statustext, statusnum, pinno, atime,agent_id) " .
						"values ('$address',  '$statustext', '$statusnum' , '$id',  '".time()."' ,'$agent')";
					mysql_query($sql3) or die (mysql_error());
	}//Hello! Welcome to OnlineCabs. Your OPT is
	$msg ="Your Pin Number : $id \n\n";
    $msg1 .="Get pin number of the person you want to Locate.\n";
    $msg1 .="(Ask to Dial #775*105# to Get Pin.)";
    $msg1 .="To get location Type,  vLocate<space>Friends's Pin and Send to 77105 \nEx:vLocate 12345\n\n";
    $msg1 .="To Locate in map Type, vLocate<space>MAP<space>Friends's Pin and Send to 77105 \nEx:vLocate map  12345\n\n";
	//echo $msg1;
	sendSMS($address,$msg);
	sendSMS($address,$msg1);
}
function update_to_agent($address,$agent_id){
	
	$sql3 = "insert agent_registration ( dmobile, agent_id, atime) " .
	"values ('$address',  '$agent_id',  '".time()."' )";
	mysql_query($sql3) or die (mysql_error());
}
function get_agent($address){
	$sql_dialog		=	"SELECT * FROM agent_registration WHERE dmobile = '$address' ";
	$reply_dialog	=	mysql_query($sql_dialog) or die (mysql_error()); 
	$row_dialog 	= 	mysql_fetch_array($reply_dialog);
	//$dialog_num		=	mysql_num_rows($reply_dialog);
	$agent_id 		=	$row_dialog ['agent_id'];
	if($agent_id==""){
		$agent_id = 0;
	}
	return $agent_id;
}
function pending($address,$statustext){
	$sql3 = "insert subcribers_pending ( masknumber, statustext, atime) " .
	"values ('$address',  '$statustext',  '".time()."' )";
	mysql_query($sql3) or die (mysql_error());
}

function help($address){
	$id = myPin($address);
	//$msg1 ="Your Pin Number $id\n\n";
	$msg ="Your Pin Number : $id \n\n";
    $msg1 .="Get pin number of the person you want to Locate.\n";
    $msg1 .="(Ask to Dial #775*105# to Get Pin.)";
    $msg1 .="To get location Type,  vLocate<space>Friends's Pin and Send to 77105 \nEx:vLocate 12345\n\n";
    $msg1 .="To Locate in map Type, vLocate<space>MAP<space>Friends's Pin and Send to 77105 \nEx:vLocate map  12345\n\n";
	//echo $msg1;
	sendSMS($address,$msg);
	sendSMS($address,$msg1);
}
function getModifiedTimeStamp($timeStamp){
    try {
        $date= new DateTime($timeStamp,new DateTimeZone('Asia/Colombo'));
    } catch (Exception $e) {
        echo $e->getMessage();
        exit(1);
    }
    return $date->format('Y-m-d H:i:s');
}
?>