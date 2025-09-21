<?php include '../db.php'; 
$ad_id		=	$_SESSION['adid'];
$otp_time		=	$_SESSION['otp_time'];
$referenceNo	=	$_SESSION['ref_no'];
$mobile			=	$_SESSION['mobile'] ;
if($ad_id==""){header("Location: index.php");}
$sqlu 		= 	"SELECT  * FROM ads WHERE  ad_id='$ad_id'";
$replyu		=	mysqli_query(db_connect(),$sqlu) ; 
$rowu    	= 	mysqli_fetch_array($replyu);
$ad_heading	=	$rowu['ad_heading'];
$ad_image   =	$rowu['ad_image'];
$ad_charging=	$rowu['ad_charging'];
$otp_id 	=	$rowu['otp_id'];
$thank_url 	=	$rowu['thank_url'];

if($ad_image==""){$ad_image="img-01.png";}

$sql 		= 	"SELECT  * FROM otp_apps WHERE  id='$otp_id'";
$reply		=	mysqli_query(db_connect(),$sql) ; 
$row    	= 	mysqli_fetch_array($reply);
$app_key	=	$row['app_key'];

include("otpapi.php");
$otp_model = new Otp(API);
					$otp				=	$_POST['otp']; 
					$submit				=	$_POST['submit'];
					 
					
					switch ($submit) {	 
						case "Verify";
						
						if( $otp==""){
							$form_msg=$form_msg."Enter pin number.";		
						} else if(!is_numeric($otp)){
							$form_msg=$form_msg."Enter valid pin number.";
						} else if(strlen($otp)<>6){ 
							$form_msg=$form_msg."Enter valid pin number.";
						}
						 
						
						
						
						if ($form_msg=="" ){
							//echo 
							$res = $otp_model-> VerfyOtp("otp_verify",$referenceNo,$otp,$app_key);  
							$json =  json_decode($res);
							if (isset($json->APP_OTP[0]->statusCode)) {
								$statusCode = $json->APP_OTP[0]->statusCode; 
								$statusDetail = $json->APP_OTP[0]->statusDetail;
								if($statusCode=="S1000" || $statusCode=="E1351" || $statusCode=="E1854") { 
									$subscriberId = $json->APP_OTP[0]->subscriberId;
									if($subscriberId!=""){
										
if($otp_id ==262){
	echo "<meta http-equiv=\"refresh\" content=\"2;URL=https://wingames.live/\">";

} else if($otp_id ==273){
	echo "<meta http-equiv=\"refresh\" content=\"2;URL=http://agfuturepro.com/kootipathiyo/Web/\">";

} else{
	if($thank_url!=""){
		echo "<meta http-equiv=\"refresh\" content=\"2;URL=".$thank_url."/\">";
	} else {
		header("Location: thank.php");
	}
}
									} else {
										$form_msg = "PIN Number Error!! Please try again.";
									}
									 
								} else {
									$form_msg = $statusDetail;
								}
							} else {
									$form_msg = "System Error!! Please try again.";
									 
							}
 							$sql = "insert into ad_click ( ad_id, mobile, billed, paid, edate, due_date, description ) " .
							   "values ('$user_id', '', '0', '$amount', '$depo_date', '', 'Pending')";
							
							//header("Location: billing.php");
							//echo "<meta http-equiv=\"refresh\" content=\"2;URL=billing.php\">";
						}
						break;
						 
					}
 ?>
<!DOCTYPE html>
<html>
<head>
	<title>Ad</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
<!--===============================================================================================-->	
	<link rel="icon" type="image/png" href="images/icons/favicon.ico"/>
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="vendor/bootstrap/css/bootstrap.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="fonts/font-awesome-4.7.0/css/font-awesome.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="vendor/animate/animate.css">
<!--===============================================================================================-->	
	<link rel="stylesheet" type="text/css" href="vendor/css-hamburgers/hamburgers.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="vendor/select2/select2.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="css/util.css">
	<link rel="stylesheet" type="text/css" href="css/main.css">
<!--===============================================================================================-->
</head>
<body>
	
	<div class="limiter">
		<div class="container-login100">
			<div class="wrap-login100" align="center">
				<div class="login100-pic js-tilt" data-tilt align="center">
                	<div  class="login100-form-title" align="center">
						<img src="images/<?php echo $ad_image;?>" alt="IMG">
                    </div>
				</div>
				
				<form class="login100-form validate-form" action="" method="post">
					<span class="login100-form-title">
						දුරකතන අංකය තහවුරු කිරීම
					</span>
					<span class="txt2">
						ඔබගේ <?php echo $_SESSION['mobile'];?> දුරකතන අංකය වෙත ලැබුනු PIN අංකය ඇතුළත් කරන්න.
					</span>
                    <?php if(!empty($form_msg)){?>   
                        <div class="alert alert-danger">
                     <?php echo $form_msg;?>                 
                        </div>
                     <?php }?>
					<div class="wrap-input100 validate-input" data-validate = "Valid mobile number is required.">
						<input class="input100" type="tel" name="otp" placeholder="123456" value="<?php echo $otp;?>" maxlength="6">
						<span class="focus-input100"></span>
						<span class="symbol-input100">
							<i class="fa fa-key" aria-hidden="true"></i>
						</span>
					</div>

					 
					
					<div class="container-login100-form-btn">
						<INPUT class="login100-form-btn" type="submit" value="Verify" name="submit">
						</INPUT>
					</div>

					<div class="text-center p-t-12">
						<span class="txt1">
							නැවත PIN අංකය ඉල්ලීමට
						</span>
						<a class="txt2" href="index.php?adid=<?php echo $_SESSION['adid'];?>">
							මෙතන ඔබන්න.
						</a>
					</div>
					<!--
					<div class="text-center p-t-136">
						<a class="txt2" href="#">
							**
 						</a></div>
                       -->
					
				</form>
			</div>
		</div>
	</div>
	
	

	
<!--===============================================================================================-->	
	<script src="vendor/jquery/jquery-3.2.1.min.js"></script>
<!--===============================================================================================-->
	<script src="vendor/bootstrap/js/popper.js"></script>
	<script src="vendor/bootstrap/js/bootstrap.min.js"></script>
<!--===============================================================================================-->
	<script src="vendor/select2/select2.min.js"></script>
<!--===============================================================================================-->
	<script src="vendor/tilt/tilt.jquery.min.js"></script>
	<script >
		$('.js-tilt').tilt({
			scale: 1.1
		})
	</script>
<!--===============================================================================================-->
	<script src="js/main.js"></script>

</body>
</html>