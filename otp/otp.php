<?php 

define('iOTP_URL', 'https://api.dialog.lk/subscription/otp/request');

define('iVERIFY_URL', 'https://api.dialog.lk/subscription/otp/verify');

define('mOTP_URL', 'https://api.mspace.lk/otp/request');

define('mVERIFY_URL', 'https://api.mspace.lk/otp/verify');


define('iSUB_URL', 'https://api.dialog.lk/subscription/getStatus');

define('mSUB_URL', 'https://api.mspace.lk/subscription/getStatus');



define('iSUBSCRIPTION_URL', 'https://api.dialog.lk/subscription/send');

define('mSUBSCRIPTION_URL', 'https://api.mspace.lk/subscription/send');


class Subscription {



	var $server;



	



    public function __construct($server){



		$this->server = $server;



    }







    private function sendRequest($jsonObjectFields)



    {



        $ch = curl_init($this->server);



        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);



        curl_setopt($ch, CURLOPT_POST, 1);



        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));



        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonObjectFields);



        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);



        $res = curl_exec($ch);



        curl_close($ch);



		//echo $this->handleResponse($res);



        return $this->handleResponse($res);



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



		//$this->handleResponse($res);



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



		//$this->handleResponse($res);



        return $this->handleResponse($res);



    }



    private function handleResponse($resp){



        if ($resp == "") {



            return "";



        } else {



            return $resp;



        }



    }

   public function sendQueryRequestNew($jsonObjectFields,$url){



        $ch = curl_init($url);



        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);



        curl_setopt($ch, CURLOPT_POST, 1);



        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));



        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonObjectFields);



        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);



        $res = curl_exec($ch);



        curl_close($ch);



        return $this->handleResponse($res);



    }

	
    public function sendQueryRequest($jsonObjectFields){



        $ch = curl_init(SUB_QUERY_URL);



        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);



        curl_setopt($ch, CURLOPT_POST, 1);



        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));



        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonObjectFields);



        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);



        $res = curl_exec($ch);



        curl_close($ch);



        return $this->handleResponse($res);



    }







    public function sendBaseRequest($jsonObjectFields){



        $ch = curl_init(SUB_BASE_URL);



        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);



        curl_setopt($ch, CURLOPT_POST, 1);



        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));



        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonObjectFields);



        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);



        $res = curl_exec($ch);



        curl_close($ch);



        return $this->handleResponse($res);



    }







    //**********************************************************************************************



	public function RegOtp($applicationId, $password, $version, $subscriberId, $applicationHash,$appCode)



    {

		if($applicationHash!=""){

			$arrayField = array(

	

				"applicationId" => $applicationId,

	

				"password" => $password,

	

				"version" => $version,

	

				"action" => 1,

	

				"subscriberId" => $subscriberId,

				

				"applicationHash" => $applicationHash,

	

				"applicationMetaData"=>

	

					 array(	"client"=> "MOBILEAPP",

	

						"device"=> "Samsung S10",

	

						"os"=>"android8",

	

						"appCode"=> $appCode

	

					));

		} else {

			$arrayField = array(



            "applicationId" => $applicationId,



            "password" => $password,



            "version" => $version,



            "action" => 1,



            "subscriberId" => $subscriberId,

			



			"applicationMetaData"=>



				 array(	"client"=> "WEBAPP",



					"device"=> "Samsung S10",



					"os"=>"android8",



					"appCode"=> $appCode



				));

		}







        $jsonObjectFields = json_encode($arrayField);



        return $this->sendOTPRequest($jsonObjectFields);



    }

	

	public function VerfyOtp($applicationId, $password, $referenceNo, $otp)



    {



        $arrayField = array(



            "applicationId" => $applicationId,



            "password" => $password,



            "referenceNo"=> $referenceNo,

			"otp"=> $otp

			);





        $jsonObjectFields = json_encode($arrayField);



        return $this->verifyOTPRequest($jsonObjectFields);



    }



	







    public function RegUser($applicationId, $password, $version, $subscriberId)



    {



        $arrayField = array(



            "applicationId" => $applicationId,



            "password" => $password,



            "version" => $version,



            "action" => 1,



            "subscriberId" => $subscriberId);







        $jsonObjectFields = json_encode($arrayField);



        return $this->sendRequest($jsonObjectFields);



    }



    public function UnregUser($applicationId, $password, $version, $subscriberId)



    {



        $arrayField = array(



            "applicationId" => $applicationId,



            "password" => $password,



            "version" => $version,



            "action" => 0,



            "subscriberId" => $subscriberId);







        $jsonObjectFields = json_encode($arrayField);



        return $this->sendRequest($jsonObjectFields);



    }







    public function getStatus($applicationId, $password, $subscriberId){
	 
	 
        $arrayField = array(
 
            "applicationId" => $applicationId,
 
            "password" => $password,
 
            "subscriberId" => $subscriberId);

 
        $jsonObjectFields = json_encode($arrayField);
 	logfile($jsonObjectFields);
	
        $resp=$this->sendQueryRequest($jsonObjectFields);

 
        $response = json_decode($resp, true);
  	logfile($response);

        $statusDetail = $response['statusDetail'];
 
        $statusCode = $response['statusCode'];
 
        $status =$response['subscriptionStatus'];
 
        return $status;

 
    }
	public function getStatusNew($applicationId, $password, $subscriberId,$url){
	 
	 
        $arrayField = array(
 
            "applicationId" => $applicationId,
 
            "password" => $password,
 
            "subscriberId" => $subscriberId);

 
        $jsonObjectFields = json_encode($arrayField);
 	//logfile($jsonObjectFields);
	
        $resp=$this->sendQueryRequestNew($jsonObjectFields,$url);

 
        $response = json_decode($resp, true);
  	//logfile($jsonObjectFields.$resp);

        $statusDetail = $response['statusDetail'];
 
        $statusCode = $response['statusCode'];
 
        $status =$response['subscriptionStatus'];
 
        return $resp;

 
    }







    public function getBaseSize($applicationId, $password){



        $arrayField = array(



            "applicationId" => $applicationId,



            "password" => $password);







        $jsonObjectFields = json_encode($arrayField);



        $resp=$this->sendBaseRequest($jsonObjectFields);



        $response = json_decode($resp, true);







        $statusDetail = $response['statusDetail'];



        $statusCode = $response['statusCode'];



        $status =$response['baseSize'];







        return $status;







    }











} 