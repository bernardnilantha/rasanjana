<?php
// ==========================================
// Ideamart : PHP SMS API Core Class
// ==========================================
// ==========================================
// Author : Pasindu De Silva
// Licence : MIT License
// http://opensource.org/licenses/MIT
// ==========================================

class Core
{
	
	public function sendRequest($jsonStream,$url){

		$this->WriteLog($jsonStream);

		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        	curl_setopt($ch, CURLOPT_POST, 1);
        	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        	curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonStream);
        	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        	$res = curl_exec($ch);
        	curl_close($ch);

		return json_decode($res);

	}
	public function WriteLog($logStream){
		date_default_timezone_set('Asia/Colombo');
		$_LOGFILE = 'LogData.log';
		
		$file = fopen($_LOGFILE, 'a');
		fwrite($file, '['.date('D M j G:i:s T Y').'] '.$logStream.'\n');
		fclose($file);
	}

}

?>
