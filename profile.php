<?php 
include 'db.php';

if(!isset($_SESSION['username']) || $_SESSION['username'] == "Guest"){
    header("Location: login.php");
    exit;
}

$user_name = $_SESSION['username'];
$user_id = $_SESSION['user_id'];
$menu = 9;

// Initialize variables
$cpassword = $npassword1 = $npassword2 = $form_msg = '';
$submit = $_POST['submit'] ?? '';

// Process form submission
if($submit == "Submit"){
    $cpassword = $_POST['cpassword'] ?? '';
    $npassword1 = $_POST['npassword1'] ?? '';
    $npassword2 = $_POST['npassword2'] ?? '';
    $connection = db_connect();
    if (!$connection) {
        throw new Exception("Failed to connect to database");
    }
    if(empty($cpassword)){
        $form_msg .= "Enter current password.<br>";
    } else {
        $qry = "SELECT * FROM user_table WHERE user_id = ? AND passwd = ?";
        $stmt = mysqli_prepare($connection, $qry);
        $hashed_password = md5(clean($cpassword));
        mysqli_stmt_bind_param($stmt, "is", $user_id, $hashed_password);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if(mysqli_num_rows($result) != 1) {
            $form_msg .= "Current password invalid.<br>";
        }
    }
    
    if(empty($npassword1)){
        $form_msg .= "Enter new password.<br>";
    }
    
    if(empty($npassword2)){
        $form_msg .= "Enter confirm password.<br>";
    }
    
    if(!empty($npassword1) && !empty($npassword2)){
        if($npassword1 != $npassword2){
            $form_msg .= "New password and confirm password do not match.<br>";
        }
    }
    
    if(empty($form_msg)){
        $sql = "UPDATE user_table SET passwd = ? WHERE user_id = ?";
        $stmt = mysqli_prepare($connection, $sql);
        $hashed_new_password = md5(clean($npassword1));
        mysqli_stmt_bind_param($stmt, "si", $hashed_new_password, $user_id);
        
        if(mysqli_stmt_execute($stmt)){
            echo "<meta http-equiv=\"refresh\" content=\"2;URL=dashboard.php\">";
            $form_msg = "Password changed successfully!";
        } else {
            $form_msg = "Error changing password: " . mysqli_error(db_connect());
        }
    }
}

function clean($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SMART Apps | Change Password</title>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Data Tables -->
    <link href="css/plugins/dataTables/dataTables.bootstrap.css" rel="stylesheet">
    <link href="css/plugins/dataTables/dataTables.responsive.css" rel="stylesheet">
    <link href="css/plugins/dataTables/dataTables.tableTools.min.css" rel="stylesheet">
    <link href="css/theme.css" rel="stylesheet">
    <style>
         
        /* Content Styles */
        .content {
            padding: 25px;
            flex: 1;
        }
        
        .page-title {
            font-size: 1.75rem;
            font-weight: 600;
            margin-bottom: 5px;
            color: var(--dark);
        }
        
        .page-description {
            color: #6c757d;
            margin-bottom: 30px;
        }
        
        .content-card {
            background-color: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            overflow: hidden;
            margin-bottom: 20px;
            max-width: 600px;
            margin: 0 auto;
        }
        
        .content-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            padding: 20px;
            position: relative;
        }
        
        .content-title {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 5px;
        }
        
        .content-subtitle {
            opacity: 0.9;
            font-size: 0.9rem;
        }
        
        .content-body {
            padding: 25px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-label {
            font-weight: 500;
            margin-bottom: 8px;
            display: block;
            color: var(--dark);
        }
        
        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            font-size: 1rem;
            transition: var(--transition);
        }
        
        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.15);
        }
        
        .password-toggle {
            position: relative;
        }
        
        .password-toggle-icon {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #6c757d;
        }
        
        .btn {
            padding: 12px 20px;
            border-radius: 8px;
            font-weight: 500;
            cursor: pointer;
            transition: var(--transition);
            border: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        
        .btn i {
            margin-right: 8px;
        }
        
        .btn-primary {
            background-color: var(--primary);
            color: white;
        }
        
        .btn-primary:hover {
            background-color: var(--secondary);
            transform: translateY(-2px);
        }
        
        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
        }
        
        .alert i {
            margin-right: 10px;
            font-size: 1.2rem;
        }
        
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .password-strength {
            margin-top: 5px;
            height: 5px;
            border-radius: 3px;
            background-color: #e9ecef;
            overflow: hidden;
        }
        
        .password-strength-bar {
            height: 100%;
            width: 0;
            transition: width 0.3s ease;
            border-radius: 3px;
        }
        
        .password-strength-weak {
            background-color: var(--danger);
        }
        
        .password-strength-medium {
            background-color: var(--warning);
        }
        
        .password-strength-strong {
            background-color: var(--success);
        }
        
        .password-strength-text {
            font-size: 0.8rem;
            margin-top: 5px;
            color: #6c757d;
        }
        
        .footer {
            padding: 20px;
            background-color: white;
            border-top: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        /* Responsive Design */
        @media (max-width: 992px) {
            .main-content {
                margin-left: 0;
            }
            
            .search-box {
                width: 200px;
            }
        }
        
        @media (max-width: 768px) {
            .content {
                padding: 15px;
            }
            
            .search-box {
                display: none;
            }
            
            .footer {
                flex-direction: column;
                text-align: center;
                gap: 10px;
            }
            
            .content-card {
                margin: 0 15px;
            }
        }
    </style>
</head>

<body>
    <!-- Sidebar Navigation -->
    <?php require_once 'nav.php'; ?>  
    
    <!-- Main Content -->
    <main class="main-content">
        <!-- Header -->
        <header class="header">
            <div class="toggle-sidebar" id="toggleSidebar">
                <i class="fas fa-bars"></i>
            </div>
            
            <div class="search-box">
                <i class="fas fa-search search-icon"></i>
                <input type="text" class="search-input" placeholder="Search for something...">
            </div>
            
            <div class="header-actions">
                <div class="header-action">
                    <i class="far fa-bell"></i>
                    <span class="notification-badge">3</span>
                </div>
                
                <div class="header-action">
                    <i class="far fa-envelope"></i>
                    <span class="notification-badge">5</span>
                </div>
                
                <div class="user-dropdown">
                    <img src="img/profile_small.jpg" alt="User" class="user-avatar" id="userDropdown">
                    <div class="dropdown-menu" id="dropdownMenu">
                        <a href="profile.php" class="dropdown-item">
                            <div class="dropdown-icon"><i class="fas fa-user"></i></div>
                            <div>Profile</div>
                        </a>
                        <a href="billing.php" class="dropdown-item">
                            <div class="dropdown-icon"><i class="fas fa-credit-card"></i></div>
                            <div>Billing</div>
                        </a>
                        <a href="logout.php" class="dropdown-item">
                            <div class="dropdown-icon"><i class="fas fa-sign-out-alt"></i></div>
                            <div>Logout</div>
                        </a>
                    </div>
                </div>
            </div>
        </header>
        
        <!-- Content Section -->
        <div class="content">
            <h1 class="page-title">Account Security</h1>
            <p class="page-description">Change your password to keep your account secure</p>
            
            <div class="content-card">
                <div class="content-header">
                    <h2 class="content-title">Change Password</h2>
                    <p class="content-subtitle">Update your account password</p>
                </div>
                
                <div class="content-body">
                    <?php if(!empty($form_msg)): ?>   
                    <div class="alert <?php echo strpos($form_msg, 'Error') !== false || strpos($form_msg, 'invalid') !== false ? 'alert-danger' : 'alert-success'; ?>">
                        <i class="fa <?php echo strpos($form_msg, 'Error') !== false || strpos($form_msg, 'invalid') !== false ? 'fa-exclamation-triangle' : 'fa-check-circle'; ?>"></i>
                        <?php echo $form_msg; ?>                 
                    </div>
                    <?php endif; ?>
                    
                    <form action="" method="post">
                        <div class="form-group">
                            <label class="form-label">Current Password *</label>
                            <div class="password-toggle">
                                <input type="password" name="cpassword" class="form-control" value="<?php echo htmlspecialchars($cpassword); ?>" required>
                                <span class="password-toggle-icon" onclick="togglePassword('cpassword')">
                                    <i class="fa fa-eye"></i>
                                </span>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">New Password *</label>
                            <div class="password-toggle">
                                <input type="password" name="npassword1" id="npassword1" class="form-control" value="<?php echo htmlspecialchars($npassword1); ?>" required onkeyup="checkPasswordStrength(this.value)">
                                <span class="password-toggle-icon" onclick="togglePassword('npassword1')">
                                    <i class="fa fa-eye"></i>
                                </span>
                            </div>
                            <div class="password-strength">
                                <div class="password-strength-bar" id="passwordStrengthBar"></div>
                            </div>
                            <div class="password-strength-text" id="passwordStrengthText"></div>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Confirm Password *</label>
                            <div class="password-toggle">
                                <input type="password" name="npassword2" id="npassword2" class="form-control" value="<?php echo htmlspecialchars($npassword2); ?>" required onkeyup="checkPasswordMatch()">
                                <span class="password-toggle-icon" onclick="togglePassword('npassword2')">
                                    <i class="fa fa-eye"></i>
                                </span>
                            </div>
                            <div id="passwordMatchText" class="password-strength-text"></div>
                        </div>
                        
                        <div class="form-group">
                            <button type="submit" name="submit" value="Submit" class="btn btn-primary">
                                <i class="fa fa-key"></i> Change Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="footer">
            <div>
                <strong>Copyright</strong> SMART Apps &copy; <?php echo date('Y'); ?>
            </div>
            <div>
                <strong>Alpha</strong> Version
            </div>
        </div>
    </main>

    <!-- JavaScript -->
    <script src="js/jquery-2.1.1.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/plugins/metisMenu/jquery.metisMenu.js"></script>
    <script src="js/plugins/slimscroll/jquery.slimscroll.min.js"></script>

    <script>
        $(document).ready(function() {
            // Toggle sidebar on mobile
            $('#toggleSidebar').click(function() {
                $('.sidebar').toggleClass('show');
                $('.main-content').toggleClass('show');
            });
            
            // Toggle dropdown menus in sidebar
            $('#appsMenu').click(function(e) {
                e.preventDefault();
                $('#appsDropdown').toggleClass('show');
            });
            
            $('#contentMenu').click(function(e) {
                e.preventDefault();
                $('#contentDropdown').toggleClass('show');
            });
            
            $('#managersMenu').click(function(e) {
                e.preventDefault();
                $('#managersDropdown').toggleClass('show');
            });
            
            // User dropdown in header
            $('#userDropdown').click(function() {
                $('#dropdownMenu').toggleClass('show');
            });
            
            // Close dropdowns when clicking outside
            $(document).click(function(event) {
                // Close user dropdown
                if (!$(event.target).closest('#userDropdown, #dropdownMenu').length) {
                    $('#dropdownMenu').removeClass('show');
                }
                
                // Close sidebar dropdowns
                if (!$(event.target).closest('#appsMenu, #appsDropdown').length) {
                    $('#appsDropdown').removeClass('show');
                }
                
                if (!$(event.target).closest('#contentMenu, #contentDropdown').length) {
                    $('#contentDropdown').removeClass('show');
                }
                
                if (!$(event.target).closest('#managersMenu, #managersDropdown').length) {
                    $('#managersDropdown').removeClass('show');
                }
            });
        });
        
        function togglePassword(inputId) {
            const passwordInput = document.getElementById(inputId);
            const icon = passwordInput.nextElementSibling.querySelector('i');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
        
        function checkPasswordStrength(password) {
            const strengthBar = document.getElementById('passwordStrengthBar');
            const strengthText = document.getElementById('passwordStrengthText');
            
            // Reset classes
            strengthBar.className = 'password-strength-bar';
            
            // Calculate strength
            let strength = 0;
            if (password.length > 5) strength += 1;
            if (password.length > 7) strength += 1;
            if (/[A-Z]/.test(password)) strength += 1;
            if (/[0-9]/.test(password)) strength += 1;
            if (/[^A-Za-z0-9]/.test(password)) strength += 1;
            
            // Update strength bar
            const width = (strength / 5) * 100;
            strengthBar.style.width = width + '%';
            
            // Update strength text and color
            if (password.length === 0) {
                strengthText.textContent = '';
            } else if (strength <= 2) {
                strengthBar.classList.add('password-strength-weak');
                strengthText.textContent = 'Weak password';
                strengthText.style.color = '#e63946';
            } else if (strength <= 4) {
                strengthBar.classList.add('password-strength-medium');
                strengthText.textContent = 'Medium strength password';
                strengthText.style.color = '#f72585';
            } else {
                strengthBar.classList.add('password-strength-strong');
                strengthText.textContent = 'Strong password';
                strengthText.style.color = '#4cc9f0';
            }
        }
        
        function checkPasswordMatch() {
            const password1 = document.getElementById('npassword1').value;
            const password2 = document.getElementById('npassword2').value;
            const matchText = document.getElementById('passwordMatchText');
            
            if (password2.length === 0) {
                matchText.textContent = '';
            } else if (password1 === password2) {
                matchText.textContent = 'Passwords match';
                matchText.style.color = '#4cc9f0';
            } else {
                matchText.textContent = 'Passwords do not match';
                matchText.style.color = '#e63946';
            }
        }
    </script>
</body>
</html>