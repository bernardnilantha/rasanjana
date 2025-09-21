<?php error_reporting (E_ALL ^ E_NOTICE); 
		session_start(); 
date_default_timezone_set('Asia/Colombo'); 
if(isSet($_SESSION['username'])){
	$user_name = $_SESSION['username'];
	$user_id = $_SESSION['user_id'];	
}
$today_full=date("Y-m-d H:i:s"); 

		$today=date("Y-m-d"); 

		$month=date("m"); 
		$day=date("d"); 
	  function db_connecti() { 			
			$host_name = 'localhost'; 
			$database = 'apps'; 
			$user_name = 'root'; 
			$password = ''; 
			$db_connection = mysqli_connect($host_name, $user_name, $password, $database) or die('Unable to establish a DB connection'); 
			return $db_connection; 
	  	}  
		function db_connecti_new($database) { 			
			$host_name = 'localhost'; 
			//$database = 'apps'; 
			$user_name = 'root'; 
			$password = ''; 
			$db_connection = mysqli_connect($host_name, $user_name, $password, $database) or die('Unable to establish a DB connection'); 
			return $db_connection; 
	  	}  
		function app_key_exist ($key){
			$sql 	= 	"SELECT  * FROM app_table WHERE  app_key='$key'";
			$reply	=	mysqli_query(db_connecti(),$sql) or die (mysqli_error(db_connecti())); 
			//$row    = mysqli_fetch_array($reply);
			$num_km = 	mysqli_num_rows($reply);
			return $num_km;
		}
		$platform   = array("","Ideamart", "mSpace");
		$apps 	    = array("","Content", "Chat", "Chat & Dating");
		$app_folder = array("","contentx", "chatx", "datingx");
		$platform_folder   = array("","ideamart/", "mspace/");
		

 ?>