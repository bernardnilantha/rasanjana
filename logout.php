<?php require_once("db.php");
 	  session_regenerate_id();
	  //$member = mysqli_fetch_assoc($result);
	  unset($_SESSION['username']);
	  unset($_SESSION['user_id']); 
		unset($_SESSION['userlevel']);
	  session_write_close();
	  header("location: login.php");
?>

