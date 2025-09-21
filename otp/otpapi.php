<?php 
define('API', 'http://smart-apps.space/otp/api.php');
class Otp {

	var $server;

	

    public function __construct($server){

		$this->server = $server;

    }
 

	private function sendOTPRequest($jsonObjectFields)

    {
//logfile($jsonObjectFields);
        $ch = curl_init($this->server);

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        curl_setopt($ch, CURLOPT_POST, 1);

        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));

        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonObjectFields);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $res = curl_exec($ch);

        curl_close($ch);

//logfile($res);


		$this->handleResponse($res);

        return $this->handleResponse($res);

    }
	private function verifyOTPRequest($jsonObjectFields)

    {    

        $ch = curl_init($this->server);

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        curl_setopt($ch, CURLOPT_POST, 1);

        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));

        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonObjectFields);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $res = curl_exec($ch);

        curl_close($ch);

		$this->handleResponse($res);

        return $this->handleResponse($res);

    }
	
	private function VerfyPaidRequest($jsonObjectFields)

    {    

        $ch = curl_init($this->server);

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        curl_setopt($ch, CURLOPT_POST, 1);

        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));

        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonObjectFields);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $res = curl_exec($ch);

        curl_close($ch);

		$this->handleResponse($res);

        return $this->handleResponse($res);

    }

    private function handleResponse($resp){

        if ($resp == "") {

            return "";

        } else {

            return $resp;

        }

    }

     	
 
	public function RegOtp( $method, $mobile, $app_key, $applicationHash) {
		
			$arrayField = array(
            "method_name" => $method,
            "mobile" => $mobile,
            "app_key" => $app_key,
			"applicationHash" => $applicationHash);
        $jsonObjectFields = json_encode($arrayField);
        return $this->sendOTPRequest($jsonObjectFields);
    }
	
	public function VerfyOtp($method, $referenceNo, $otp, $app_key) {
        $arrayField = array(
            "method_name" => $method,
            "referenceNo" => $referenceNo,
			"otp" => $otp,
            "app_key" => $app_key);
        $jsonObjectFields = json_encode($arrayField);
        return $this->verifyOTPRequest($jsonObjectFields);

    }
	
	public function VerfyPaid($method, $app_key) {
        $arrayField = array(
            "method_name" => $method, 
            "app_key" => $app_key);
        $jsonObjectFields = json_encode($arrayField);
        return $this->VerfyPaidRequest($jsonObjectFields);

    }
 
} 