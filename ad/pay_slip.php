<?php  error_reporting (E_ALL ^ E_NOTICE); //include 'db.php';
 	function db_connecti() { 			
			$host_name = 'localhost'; 
			$database = 'bernard_smspal'; 
			$user_name = 'bernard_root'; 
			$password = 'gigabyte'; 
			$db_connection = mysqli_connect($host_name, $user_name, $password, $database) or die('Unable to establish a DB connection'); 
			return $db_connection; 
	  	}   
		$db_connection = db_connecti();
		
		$emp_no				=	$_POST['emp_no']; 
		$row_id				=	$_POST['row_id'];
		$slip_month			=	$_POST['slip_month']; 
		$submit				=	$_POST['submit'];
		switch ($submit) {	 
			case "Show";
				if( $emp_no==""){
					$form_msg=$form_msg."Enter employee number.";		
				} else if(!is_numeric($emp_no)){
					$form_msg=$form_msg."Enter valid employee number.";
				}  
				if( $slip_month==""){
					$form_msg=$form_msg."Select month.";		
				} 
						 
 				if ($form_msg=="" ){
					
					$sqls		=	"SELECT * FROM slips WHERE  slip_month='$slip_month' AND emp_id='$emp_no'";
                    $rss		=	mysqli_query($db_connection,$sqls) or die (mysqli_error());
                    $rows 		= 	mysqli_fetch_array($rss);
					$num_row	= 	mysqli_num_rows($rss); 
					if($num_row>0){
                    	$emp_slip	=	$rows['emp_slip'];
						$row_id		=	$rows['row_id'];
						$file		=	$rows['row_id'].str_replace(" ","_",$rows['slip_month']).".pdf";
					} else {
						$form_msg=$form_msg."Invaliad employee number or month.";	
					}
					
					
				}
			break;
			case "Download";
			
					
					 $sqls		=	"SELECT * FROM slips WHERE  row_id='$row_id'";
					 $rss		=	mysqli_query($db_connection,$sqls) or die (mysqli_error());
					 $rows 		= 	mysqli_fetch_array($rss);
					 $emp_slip	=	$rows['emp_slip'];
					 $file		=	$rows['row_id'].str_replace(" ","_",$rows['slip_month']).".jpg";

					header('Content-type: image/jpeg');
					header("Cache-Control: no-store, no-cache");  
							header('Content-Disposition: attachment; filename="'.$file.'"');
					// Load And Create Image From Source
					$our_image = imagecreatefromjpeg('blanck.jpg');
					
					// Allocate A Color For The Text Enter RGB Value
					$white_color = imagecolorallocate($our_image, 0, 0, 0);
					
					// Set Path to Font File
					$font_path = 'CONSOLA.TTF';
					
					// Set Text to Be Printed On Image
					$text = str_replace(array("<pre>","</pre>","<br>"),array("","","\n"),$emp_slip);//"Welcome To Talkerscode";
					
					$size=20;
					$angle=0;
					$left=125;
					$top=200;
						
					// Print Text On Image
					imagettftext($our_image, $size,$angle,$left,$top, $white_color, $font_path, $text);
					
					// Send Image to Browser
					imagejpeg($our_image);
					
					// Clear Memory
					imagedestroy($our_image);
			break;
 		}
		?>
<!DOCTYPE html>
<html>
<head>
	<title>Ad</title>
	<meta charset="utf-8">
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
				 <form class="login100-form validate-form" action="" method="post">
				<?php if($num_row>0){
					echo  $emp_slip;
					?>
                    <!--<button class="btn btn-dark mt-2" style="width:75%" title="Download Slip" onClick="window.open('download_pdf.php?id=<?php //echo $id;?>','_blank','resizable=yes');"><i class="fa fa-download"></i> Download</button>--><input class="input100" type="hidden" name="row_id"  value="<?php echo $row_id;?>" >
                    <INPUT class="btn btn-dark mt-2" style="width:75%"  type="submit" value="Download" name="submit"> </INPUT>
                    <?php
				} else {?>
				
					<span class="login100-form-title">
						<?php //echo "ad_heading";?>
					</span>
                    <?php if(!empty($form_msg)){?>   
                        <div class="alert alert-danger">
                     <?php echo $form_msg;?>                 
                        </div>
                     <?php }?>
					<span class="txt2">
						ඔබගේ සේවක අංකය ඇතුළත් කරන්න.
					</span>
                     
					<div class="wrap-input100 validate-input" data-validate = "Valid employee number is required.">
						<input class="input100" type="tel" name="emp_no" placeholder="සේවක අංකය" value="<?php echo $emp_no;?>" maxlength="7">
						<span class="focus-input100"></span>
						<span class="symbol-input100">
							<i class="fa fa-male" aria-hidden="true"></i>
						</span>
					</div>
                    <span class="txt2">
						මාසය තෝරන්න.
					</span>
					<div class="wrap-input100 validate-input" data-validate = "Valid month is required.">
						 <select name="slip_month" class="form-control" data-live-search="true" id="su_agent_id">

                       <option <?php if ($slip_month==$row['slip_month']){echo "selected=selected";} ?>  value="" ><span class="main">Select Month</span></option>     
                       <?php  $sql="SELECT * FROM slips GROUP BY  slip_month";
                              $rs=mysqli_query(db_connecti(),$sql) or die (mysqli_error());
                              while($row = mysqli_fetch_array($rs)){?>
                       <option <?php if ($slip_month==$row['slip_month']){echo "selected=selected";} ?>  
                       		value="<?php echo $row['slip_month'];?>" >
                            <span class="main"><?php echo  $row['slip_month'];?></span>
                      </option>
                       <?php }?>
                     </select>
 					</div>
					 
					
					<div class="container-login100-form-btn">
						<INPUT class="login100-form-btn" type="submit" value="Show" name="submit">
						</INPUT>
					</div>
 				
                <?php }?>
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