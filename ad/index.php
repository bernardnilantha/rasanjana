<?php
session_start();
include '../db.php';
require_once '../functions.php';

// Validate and sanitize input
$ad_id = isset($_GET['adid']) ? intval($_GET['adid']) : 1;
if ($ad_id <= 0) $ad_id = 1;

// Set charset for database connection
mysqli_set_charset(db_connect(), "utf8");

// Fetch ad information
$sql = "SELECT * FROM ads WHERE ad_id='$ad_id' AND active=1";
$result = mysqli_query(db_connect(), $sql);
$ad = mysqli_fetch_assoc($result);

if (!$ad) {
    die("Invalid advertisement or advertisement not active.");
}

$ad_heading = htmlspecialchars($ad['ad_heading']);
$ad_image = !empty($ad['ad_image']) ? $ad['ad_image'] : "img-01.png";
$ad_charging = htmlspecialchars($ad['ad_charging']);
$otp_id = intval($ad['otp_id']);

// Check if OTP is enabled for this ad
if ($otp_id <= 0) {
    die("OTP service not configured for this advertisement.");
}

// Fetch OTP app information
$sql = "SELECT * FROM otp_apps WHERE id='$otp_id'";
$result = mysqli_query(db_connect(), $sql);
$otp_app = mysqli_fetch_assoc($result);

if (!$otp_app) {
    die("OTP application configuration not found.");
}

$app_key = $otp_app['app_key'];

// Initialize session variables if not set
if (!isset($_SESSION['adid'])) $_SESSION['adid'] = "";
if (!isset($_SESSION['otp_time'])) $_SESSION['otp_time'] = "";
if (!isset($_SESSION['ref_no'])) $_SESSION['ref_no'] = "";
if (!isset($_SESSION['mobile'])) $_SESSION['mobile'] = "";

// Process form submission
$form_msg = "";
$mobile = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    $mobile = trim($_POST['mobile']);
    $submit = $_POST['submit'];
    
    if ($submit === "Register") {
        // Validate mobile number
        if (empty($mobile)) {
            $form_msg = "Enter mobile number.";
        } elseif (!is_numeric($mobile)) {
            $form_msg = "Enter valid mobile number.";
        } elseif (strlen($mobile) !== 10) {
            $form_msg = "Enter valid mobile number.";
        }
        
        // If validation passed, process registration
        if (empty($form_msg)) {
            include("otpapi.php");
            $otp = new Otp(API);
            
            $dbi = db_connect();
            $timestamp = time();
            
            // Insert mobile number into database
            $sql_insert = "INSERT INTO ad_mobile (mobile, ad_id, datetime, otp) 
                          VALUES (?, ?, ?, '')";
            $stmt = mysqli_prepare($dbi, $sql_insert);
            mysqli_stmt_bind_param($stmt, "sii", $mobile, $ad_id, $timestamp);
            mysqli_stmt_execute($stmt);
            $rowid_last = mysqli_insert_id($dbi);
            mysqli_stmt_close($stmt);
            
            // Request OTP
            $res = $otp->RegOtp("otp_request", $mobile, $app_key);
            $json = json_decode($res);
            
            if (isset($json->APP_OTP[0]->statusCode)) {
                // Update response time
                $sql_update = "UPDATE ad_mobile SET resdatetime=? WHERE rowid=?";
                $stmt = mysqli_prepare($dbi, $sql_update);
                mysqli_stmt_bind_param($stmt, "ii", $timestamp, $rowid_last);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
                
                $statusCode = $json->APP_OTP[0]->statusCode;
                $statusDetail = $json->APP_OTP[0]->statusDetail;
                
                if ($statusCode === "S1000") {
                    $referenceNo = $json->APP_OTP[0]->referenceNo;
                    
                    if (!empty($referenceNo)) {
                        // Set session variables and redirect
                        $_SESSION['adid'] = $ad_id;
                        $_SESSION['otp_time'] = time();
                        $_SESSION['ref_no'] = $referenceNo;
                        $_SESSION['mobile'] = $mobile;
                        
                        header("Location: otp.php?fadid=" . md5($ad_id));
                        exit();
                    } else {
                        $form_msg = "Number Error!! Please try again.";
                    }
                } else {
                    $form_msg = $statusDetail;
                }
            } else {
                $form_msg = "System Error!! Please try again.";
            }
            
            mysqli_close($dbi);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title><?php echo $ad_heading; ?></title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="images/icons/favicon.ico"/>
    <link rel="stylesheet" type="text/css" href="vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="fonts/font-awesome-4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="vendor/animate/animate.css">
    <link rel="stylesheet" type="text/css" href="vendor/css-hamburgers/hamburgers.min.css">
    <link rel="stylesheet" type="text/css" href="vendor/select2/select2.min.css">
    <link rel="stylesheet" type="text/css" href="css/util.css">
    <link rel="stylesheet" type="text/css" href="css/main.css">
</head>
<body>
    <div class="limiter">
        <div class="container-login100">
            <div class="wrap-login100" align="center">
                <div class="login100-pic js-tilt" data-tilt align="center">
                    <div class="login100-form-title" align="center">
                        <img src="images/<?php echo htmlspecialchars($ad_image); ?>" alt="Advertisement Image">
                    </div>
                </div>

                <form class="login100-form validate-form" method="post">
                    <span class="login100-form-title">
                        <?php echo $ad_heading; ?>
                    </span>
                    <span class="txt2">
                        ඔබගේ දුරකතන අංකය ඇතුළත් කරන්න.
                    </span>
                    
                    <?php if (!empty($form_msg)): ?>
                        <div class="alert alert-danger">
                            <?php echo htmlspecialchars($form_msg); ?>
                        </div>
                    <?php endif; ?>
                    
                    <div class="wrap-input100 validate-input" data-validate="Valid mobile number is required.">
                        <input class="input100" type="tel" name="mobile" placeholder="07*2345678" 
                               value="<?php echo htmlspecialchars($mobile); ?>" maxlength="10" required>
                        <span class="focus-input100"></span>
                        <span class="symbol-input100">
                            <i class="fa fa-mobile" aria-hidden="true"></i>
                        </span>
                    </div>

                    <div class="container-login100-form-btn">
                        <button class="login100-form-btn" type="submit" name="submit" value="Register">
                            Register
                        </button>
                    </div>

                    <div class="text-center p-t-3">
                        <span class="txt2">**<?php echo $ad_charging; ?></span>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="vendor/jquery/jquery-3.2.1.min.js"></script>
    <script src="vendor/bootstrap/js/popper.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.min.js"></script>
    <script src="vendor/select2/select2.min.js"></script>
    <script src="vendor/tilt/tilt.jquery.min.js"></script>
    <script>
        $('.js-tilt').tilt({
            scale: 1.1
        });
    </script>
    <script src="js/main.js"></script>
</body>
</html>