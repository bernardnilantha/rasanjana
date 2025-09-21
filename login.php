<?php
declare(strict_types=1);
require_once("db.php");

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Initialize variables
$username = $password = $form_msg = '';
$login_error = false;

// Process login if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['Login'])) {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    // Validate inputs
    if (empty($username)) {
        $form_msg .= "<br>Enter user name.";
        $login_error = true;
    }
    
    if (strlen($password) < 4) {
        $form_msg .= "<br>Password must be at least 4 characters.";
        $login_error = true;
    }
    
    // Proceed with authentication if no validation errors
    if (!$login_error) {
        try {
            // Get database connection
            $connection = db_connect();
            
            if (!$connection) {
                throw new Exception("Failed to connect to database");
            }
            
            // Use prepared statement to prevent SQL injection
            $qry = "SELECT * FROM user_table WHERE username = ?";
            $stmt = mysqli_prepare($connection, $qry);
            
            if (!$stmt) {
                throw new Exception("Prepare failed: " . mysqli_error($connection));
            }
            
            $bind_result = mysqli_stmt_bind_param($stmt, "s", $username);
            
            if (!$bind_result) {
                throw new Exception("Bind param failed: " . mysqli_error($connection));
            }
            
            $execute_result = mysqli_stmt_execute($stmt);
            
            if (!$execute_result) {
                throw new Exception("Execute failed: " . mysqli_error($connection));
            }
            
            $result = mysqli_stmt_get_result($stmt);
            
            if ($result && mysqli_num_rows($result) === 1) {
                $member = mysqli_fetch_assoc($result);
                
                // For debugging - check what hash we're comparing
                $input_password_hash = md5(($password));
                
                // Verify password (using md5 as in original code)
                if ($input_password_hash === $member['passwd']) {
                    if ($member['active'] == 0) {
                        $form_msg .= "<br>Account deactivated or email not verified.";
                    } else if ($member['email_verified'] == 1) {
                        // Successful login
                        session_regenerate_id(true);
                        
                        $_SESSION['username'] = $member['username'];
                        $_SESSION['user_id'] = $member['user_id'];
                        $_SESSION['email'] = $member['email'];
                        $_SESSION['userlevel'] = $member['user_level'];
                        
                        session_write_close();
                        header("Location: dashboard.php");
                        exit();
                    } else {
                        // Email not verified
                        session_regenerate_id(true);
                        $_SESSION['temp_username'] = $member['username'];
                        $_SESSION['temp_user_id'] = $member['user_id'];
                        $_SESSION['email'] = $member['email'];
                        session_write_close();
                        header("Location: confirm_email.php");
                        exit();
                    }
                } else {
                    error_log("Password mismatch for user: $username");
                    error_log("Input hash: $input_password_hash");
                    error_log("Stored hash: " . $member['passwd']);
                    $form_msg .= "<br>Invalid username or password.";
                }
            } else {
                error_log("No user found or multiple users with username: $username");
                $form_msg .= "<br>Invalid username or password.";
            }
            
            mysqli_stmt_close($stmt);
        } catch (Exception $e) {
            error_log("Login error: " . $e->getMessage());
            error_log("Error trace: " . $e->getTraceAsString());
            $form_msg .= "<br>An error occurred during login. Please try again. Debug info: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SMART Apps | Login</title>

    <!-- CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="font-awesome/css/font-awesome.css" rel="stylesheet">
    <link href="css/animate.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #1ab394;
            --secondary-color: #2f4050;
            --accent-color: #ed5565;
        }
        
        body.black-bg {
            background: linear-gradient(135deg, var(--secondary-color) 0%, #283949 100%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .login-container {
            max-width: 400px;
            width: 100%;
            animation: fadeIn 0.8s ease-in-out;
        }
        
        .login-card {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            padding: 30px;
            position: relative;
            overflow: hidden;
        }
        
        .login-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: var(--primary-color);
        }
        
        .logo-container {
            text-align: center;
            margin-bottom: 20px;
        }
        
        .logo-container img {
            max-width: 120px;
            height: auto;
        }
        
        .form-control {
            padding: 12px 15px;
            height: auto;
            border-radius: 4px;
            border: 1px solid #ddd;
            transition: all 0.3s;
        }
        
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(26, 179, 148, 0.2);
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            padding: 12px;
            font-weight: 600;
            border-radius: 4px;
            transition: all 0.3s;
        }
        
        .btn-primary:hover {
            background-color: #18a689;
            border-color: #18a689;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        
        .alert {
            border-radius: 4px;
            padding: 12px 15px;
        }
        
        .divider {
            display: flex;
            align-items: center;
            margin: 20px 0;
        }
        
        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid #ddd;
        }
        
        .divider span {
            padding: 0 10px;
            color: #777;
            font-size: 14px;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }
        
        .shake {
            animation: shake 0.3s linear;
        }
    </style>
</head>

<body class="black-bg">
    <div class="login-container">
        <div class="login-card">
            <div class="logo-container">
                <h1><img src="img/icon.png" alt="SMART Apps Logo"></h1>
            </div>
            
            <h3 class="text-center" style="margin-bottom: 20px; color: var(--secondary-color);">Welcome Back</h3>
            <p class="text-center text-muted">Sign in to continue to your account</p>
            
            <?php if(!empty($form_msg)): ?>   
            <div class="alert alert-danger">
                <i class="fa fa-exclamation-triangle"></i> 
                <?php echo $form_msg; ?>                 
            </div>
            <?php endif; ?>  
            
            <form method="post" action="">
                <div class="form-group">
                    <input type="text" class="form-control" placeholder="Username" name="username" value="<?php echo htmlspecialchars($username); ?>" required>
                </div>
                
                <div class="form-group">
                    <input type="password" class="form-control" placeholder="Password" name="password" required>
                </div>
                
                <div class="form-group">
                    <input type="submit" class="btn btn-primary btn-block" name="Login" value="Login">
                </div>
                
                
            </form>
        </div>
        
        <div class="text-center" style="margin-top: 20px; color: #fff;">
            <p>&copy; <?php echo date('Y'); ?> SMART Apps. All rights reserved.</p>
        </div>
    </div>

    <!-- JavaScript -->
    <script src="js/jquery-2.1.1.js"></script>
    <script src="js/bootstrap.min.js"></script>
    
    <script>
        $(document).ready(function() {
            // Add shake animation to form on error
            <?php if($login_error): ?>
                $('.login-card').addClass('shake');
                setTimeout(function() {
                    $('.login-card').removeClass('shake');
                }, 300);
            <?php endif; ?>
            
            // Focus on username field
            $('input[name="username"]').focus();
        });
    </script>
</body>
</html>