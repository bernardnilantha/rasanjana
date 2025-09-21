<?php include '../db.php'; 
$ad_id		=	$_SESSION['adid'];
$mobile			=	$_SESSION['mobile'] ;
if($ad_id==""){header("Location: index.php");}
$sqlu 		= 	"SELECT  * FROM ads WHERE  ad_id='$ad_id'";
$replyu		=	mysqli_query(db_connecti(),$sqlu) ; 
$rowu    	= 	mysqli_fetch_array($replyu);
$ad_heading	=	$rowu['ad_heading'];
$ad_image   =	$rowu['ad_image'];
$ad_charging=	$rowu['ad_charging'];
$otp_id 	=	$rowu['otp_id'];
if($ad_image==""){$ad_image="img-01.png";}

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
                          <div class="alert alert-success">
                             <span class="login100-form-title">
                                ඔබගේ ලියාපදිංචිය තහවුරු කිරීම <?php echo $mobile;?>  අංකයට කෙටි පණිවිඩයක් මඟින් දැනුම් දෙන ලැබේ.
                            </span>
                         </div>
 					<div class="text-center p-t-12">
						<!--<span class="txt1">
							T&C Apply
						</span>
						<a class="txt2" href="#">
							Username / Password?
						</a>
					</div>

					<div class="text-center p-t-136">
						<a class="txt2" href="#">
							**
 						</a>
                       -->
					</div>
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