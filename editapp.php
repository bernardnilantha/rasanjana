<?php 
include 'db.php';
require_once 'functions.php';

// Check if user is logged in
if (!isset($_SESSION['username']) || $_SESSION['username'] == "Guest") {
    header("Location: login.php");
    exit;
}

$menu = 2;
$sub_menu = 1;
$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['username'];

// Decode app ID
$id = isset($_GET['appid']) ? decode_string($_GET['appid'], $user_id) : '';

// Initialize variables
$app_name = $app_sms = $app_port = $app_key = $sms_sender_address = $ussd_code = '';
$ussd_keyword = $key_gen = $app_id = $app_pswd = $intmsg = $sms_sender_aliases = '';
$app_category = $app_sub_category = '';
$form_msg = '';

// Fetch app data if ID is provided
if (!empty($id)) {
    try {
        $connection = db_connect();
        $sqle = "SELECT * FROM app_table WHERE id = ?";
        $stmt = mysqli_prepare($connection, $sqle);
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if ($rowe = mysqli_fetch_assoc($result)) {
            $app_name = $rowe['app_name'];
            $app_category = $rowe['app_category'];
            $app_sub_category = $rowe['app_sub_category'];
            $app_sms = $rowe['app_sms'];
            $app_port = $rowe['app_port'];
            $app_key = $rowe['app_key'];
            $sms_sender_address = $rowe['sms_sender_address'];
            $ussd_code = $rowe['ussd_code'];
            $ussd_keyword = $rowe['ussd_keyword']; 
            $sms_sender_aliases = $rowe['sms_sender_aliass'] ?? '';
            $app_id = $rowe['app_id'];
            $app_pswd = $rowe['app_password'];
            $intmsg = $rowe['intmsg'] ?? '';
        }
        mysqli_stmt_close($stmt);
    } catch (Exception $e) {
        error_log("Database error: " . $e->getMessage());
        $form_msg = "<div class='alert alert-danger'>Error loading app data. Please try again.</div>";
    }
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $app_id = $_POST['app_id'] ?? '';
    $app_pswd = $_POST['app_pswd'] ?? '';
    $intmsg = $_POST['intmsg'] ?? '';
    
    // Validate inputs
    if (empty($app_id)) {
        $form_msg .= "<div class='alert alert-warning'>Enter App ID.</div>";
    }
    
    if (empty($app_pswd)) {
        $form_msg .= "<div class='alert alert-warning'>Enter App Password / Key.</div>";
    }
    
    // Update database if no errors
    if (empty($form_msg)) {
        try {
            $connection = db_connect();
            $sql = "UPDATE app_table SET app_id = ?, app_password = ?, intmsg = ? WHERE id = ?";
            $stmt = mysqli_prepare($connection, $sql);
            mysqli_stmt_bind_param($stmt, "sssi", $app_id, $app_pswd, $intmsg, $id);
            
            if (mysqli_stmt_execute($stmt)) {
                $form_msg = "<div class='alert alert-success'>App updated successfully! Redirecting...</div>";
                echo "<meta http-equiv='refresh' content='2;URL=dashboard.php'>";
            } else {
                $form_msg = "<div class='alert alert-danger'>Error updating app: " . mysqli_error($connection) . "</div>";
            }
            mysqli_stmt_close($stmt);
        } catch (Exception $e) {
            error_log("Database error: " . $e->getMessage());
            $form_msg = "<div class='alert alert-danger'>Error updating app. Please try again.</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SMART Apps | Edit Application</title>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="css/theme.css" rel="stylesheet">
    <style>
         
         
        
        /* Edit App Content */
        .edit-app-container {
            padding: 25px;
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
        
        .app-card {
            background-color: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            overflow: hidden;
            margin-bottom: 20px;
        }
        
        .app-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            padding: 20px;
            position: relative;
        }
        
        .app-title {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 5px;
        }
        
        .app-subtitle {
            opacity: 0.9;
            font-size: 0.9rem;
        }
        
        .app-content {
            padding: 25px;
        }
        
        .section-title {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 20px;
            color: var(--dark);
            padding-bottom: 10px;
            border-bottom: 2px solid var(--light);
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
        
        .form-control:disabled {
            background-color: #f8f9fa;
            color: #6c757d;
        }
        
        .required::after {
            content: " *";
            color: var(--danger);
        }
        
        .url-display {
            background-color: #f8f9fa;
            padding: 12px 15px;
            border-radius: 8px;
            border: 1px dashed #dee2e6;
            font-family: monospace;
            word-break: break-all;
        }
        
        .btn {
            padding: 12px 20px;
            border-radius: 8px;
            font-weight: 500;
            cursor: pointer;
            transition: var(--transition);
            border: none;
        }
        
        .btn-primary {
            background-color: var(--primary);
            color: white;
        }
        
        .btn-primary:hover {
            background-color: var(--secondary);
            transform: translateY(-2px);
        }
        
        .btn-secondary {
            background-color: #6c757d;
            color: white;
        }
        
        .btn-secondary:hover {
            background-color: #5a6268;
        }
        
        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
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
        
        .alert-warning {
            background-color: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }
        
        .info-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 500;
            margin-left: 10px;
        }
        
        .badge-primary {
            background-color: rgba(67, 97, 238, 0.1);
            color: var(--primary);
        }
        
        .badge-success {
            background-color: rgba(76, 201, 240, 0.1);
            color: var(--success);
        }
        
        .tab-container {
            display: flex;
            border-bottom: 1px solid #e2e8f0;
            margin-bottom: 20px;
        }
        
        .tab {
            padding: 12px 20px;
            cursor: pointer;
            border-bottom: 3px solid transparent;
            transition: var(--transition);
        }
        
        .tab.active {
            border-bottom-color: var(--primary);
            color: var(--primary);
            font-weight: 500;
        }
        
        .tab-content {
            display: none;
        }
        
        .tab-content.active {
            display: block;
        }
        
        .platform-icon {
            width: 24px;
            height: 24px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-right: 10px;
            border-radius: 50%;
            background-color: rgba(255, 255, 255, 0.2);
        }
        
        @media (max-width: 992px) {
            .sidebar {
                transform: translateX(-100%);
            }
            
            .sidebar.show {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0;
            }
            
            .search-box {
                width: 200px;
            }
        }
        
        @media (max-width: 768px) {
            .edit-app-container {
                padding: 15px;
            }
            
            .app-content {
                padding: 15px;
            }
            
            .tab {
                padding: 10px 15px;
                font-size: 0.9rem;
            }
            
            .search-box {
                display: none;
            }
        }
    </style>
</head>

<body>
    <!-- Sidebar Navigation -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <img src="img/icon.png" alt="SMART Apps Logo">
        </div>
        
        <div class="sidebar-profile">
            <div class="profile-name"><?php echo $user_name; ?></div>
            <div class="profile-role"><?php echo ($_SESSION['userlevel'] < 10) ? 'Administrator' : 'User'; ?></div>
        </div>
        
        <nav class="sidebar-menu">
            <div class="menu-label">Main Navigation</div>
            <a href="dashboard.php" class="menu-item">
                <div class="menu-icon"><i class="fas fa-th-large"></i></div>
                <div class="menu-text">Dashboard</div>
            </a>
            
            <?php if($_SESSION['userlevel'] < 10): ?>
            <div class="menu-label">Applications</div>
            <a href="#" class="menu-item" id="appsMenu">
                <div class="menu-icon"><i class="fas fa-mobile-alt"></i></div>
                <div class="menu-text">Applications</div>
                <div class="menu-icon"><i class="fas fa-chevron-down"></i></div>
            </a>
            <div class="menu-dropdown" id="appsDropdown">
                <a href="appcreate.php" class="submenu-item">
                    <i class="fas fa-plus-circle me-2"></i> Create New App
                </a>
                <a href="my_apps.php" class="submenu-item">
                    <i class="fas fa-list me-2"></i> My Apps
                </a>
            </div>
            
            <a href="reports.php" class="menu-item">
                <div class="menu-icon"><i class="fas fa-chart-bar"></i></div>
                <div class="menu-text">Reports</div>
            </a>
            <?php endif; ?>
            
            <div class="menu-label">Content</div>
            <a href="#" class="menu-item" id="contentMenu">
                <div class="menu-icon"><i class="fas fa-file-alt"></i></div>
                <div class="menu-text">Contents</div>
                <div class="menu-icon"><i class="fas fa-chevron-down"></i></div>
            </a>
            <div class="menu-dropdown" id="contentDropdown">
                <a href="category.php" class="submenu-item">
                    <i class="fas fa-tag me-2"></i> Category
                </a>
                <a href="content.php" class="submenu-item">
                    <i class="fas fa-table me-2"></i> Content
                </a>
            </div>
            
            <?php if($_SESSION['userlevel'] < 10): ?>
            <a href="billing.php" class="menu-item">
                <div class="menu-icon"><i class="fas fa-credit-card"></i></div>
                <div class="menu-text">Billing</div>
                <span class="menu-badge">Due</span>
            </a>
            
            <a href="otp_apps.php" class="menu-item">
                <div class="menu-icon"><i class="fas fa-key"></i></div>
                <div class="menu-text">OTP API Key</div>
            </a>
            
            <a href="create_ad.php" class="menu-item">
                <div class="menu-icon"><i class="fas fa-ad"></i></div>
                <div class="menu-text">Create AD</div>
            </a>
            
            <a href="#" class="menu-item" id="managersMenu">
                <div class="menu-icon"><i class="fas fa-users"></i></div>
                <div class="menu-text">Content Managers</div>
                <div class="menu-icon"><i class="fas fa-chevron-down"></i></div>
            </a>
            <div class="menu-dropdown" id="managersDropdown">
                <a href="add_manager.php" class="submenu-item">
                    <i class="fas fa-user-plus me-2"></i> Add Managers
                </a>
                <a href="add_manager_apps.php" class="submenu-item">
                    <i class="fas fa-mobile-alt me-2"></i> Add Apps
                </a>
            </div>
            <?php endif; ?>
            
            <a href="profile.php" class="menu-item">
                <div class="menu-icon"><i class="fas fa-user"></i></div>
                <div class="menu-text">Profile</div>
            </a>
            
            <a href="logout.php" class="menu-item">
                <div class="menu-icon"><i class="fas fa-sign-out-alt"></i></div>
                <div class="menu-text">Logout</div>
            </a>
        </nav>
    </aside>

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

        <!-- Edit App Content -->
        <div class="edit-app-container">
            <?php echo $form_msg; ?>
            
            <h1 class="page-title">Edit Application</h1>
            <p class="page-description">Update your application settings and configuration</p>
            
            <div class="app-card">
                <div class="app-header">
                    <h2 class="app-title"><?php echo htmlspecialchars($app_name); ?></h2>
                    <p class="app-subtitle"><?php echo $apps_list[$app_sub_category] ?? 'Not set'; ?></p>
                </div>
                
                <div class="app-content">
                    <div class="tab-container">
                        <div class="tab active" data-tab="basic">Basic Info</div>
                        <div class="tab" data-tab="sms">SMS Configuration</div>
                        <div class="tab" data-tab="ussd">USSD Configuration</div>
                        <div class="tab" data-tab="subscription">Subscription</div>
                    </div>
                    
                    <form method="post" action="">
                        <!-- Basic Info Tab -->
                        <div class="tab-content active" id="basic-tab">
                            <h3 class="section-title">Basic Information</h3>
                            
                            <div class="form-group">
                                <label class="form-label">Platform</label>
                                <div>
                                    <span class="info-badge badge-primary">
                                        <span class="platform-icon"><i class="fas fa-mobile-alt"></i></span>
                                        <?php echo $platform[$app_category] ?? 'Not set'; ?>
                                    </span>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Application Type</label>
                                <div>
                                    <span class="info-badge badge-success">
                                        <i class="fas fa-cog"></i> <?php echo $apps_list[$app_sub_category] ?? 'Not set'; ?>
                                    </span>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Application Name</label>
                                <input type="text" class="form-control" value="<?php echo htmlspecialchars($app_name); ?>" disabled>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Allowed Host Address(es)</label>
                                <input type="text" class="form-control" value="50.87.144.12" disabled>
                            </div>
                        </div>
                        
                        <!-- SMS Tab -->
                        <div class="tab-content" id="sms-tab">
                            <h3 class="section-title">
                                <i class="fas fa-sms"></i> SMS Configuration
                            </h3>
                            
                            <div class="form-group">
                                <label class="form-label">Message Receiving URL</label>
                                <div class="url-display" id="sms-url">
                                    https://ideaapps.space/<?php echo $app_category == 1 ? 'ideamart' : 'mspace'; ?>/<?php echo $app_sub_category; ?>/sms.php
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Default Sender Address</label>
                                <input type="text" class="form-control" value="<?php echo htmlspecialchars($sms_sender_address); ?>" disabled>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">SMS Short Code</label>
                                <input type="text" class="form-control" value="<?php echo htmlspecialchars($app_port); ?>" disabled>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Send Address Aliases</label>
                                <input type="text" class="form-control" value="<?php echo htmlspecialchars($sms_sender_aliases); ?>" disabled>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">SMS Keyword</label>
                                <input type="text" class="form-control" value="<?php echo htmlspecialchars($app_sms); ?>" disabled>
                            </div>
                        </div>
                        
                        <!-- USSD Tab -->
                        <div class="tab-content" id="ussd-tab">
                            <h3 class="section-title">
                                <i class="fas fa-comment"></i> USSD Configuration
                            </h3>
                            
                            <div class="form-group">
                                <label class="form-label">Connection URL</label>
                                <div class="url-display" id="ussd-url">
                                    https://ideaapps.space/<?php echo $app_category == 1 ? 'ideamart' : 'mspace'; ?>/<?php echo $app_sub_category; ?>/ussd.php
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Service Code</label>
                                <input type="text" class="form-control" value="<?php echo htmlspecialchars($ussd_code); ?>" disabled>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Keyword</label>
                                <input type="text" class="form-control" value="<?php echo htmlspecialchars($ussd_keyword); ?>" disabled>
                            </div>
                        </div>
                        
                        <!-- Subscription Tab -->
                        <div class="tab-content" id="subscription-tab">
                            <h3 class="section-title">
                                <i class="fas fa-bell"></i> Subscription Configuration
                            </h3>
                            
                            <div class="form-group">
                                <label class="form-label">Subscription Notification URL</label>
                                <div class="url-display" id="sub-url">
                                    https://ideaapps.space/<?php echo $app_category == 1 ? 'ideamart' : 'mspace'; ?>/<?php echo $app_sub_category; ?>/sub.php
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label required">APP ID</label>
                                <input type="text" name="app_id" class="form-control" value="<?php echo htmlspecialchars($app_id); ?>" 
                                       placeholder="APP_001110" maxlength="15" required>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label required">APP Password / Key</label>
                                <input type="text" name="app_pswd" class="form-control" value="<?php echo htmlspecialchars($app_pswd); ?>" 
                                       placeholder="Enter your app password" maxlength="55" required>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Subscription Message</label>
                                <input type="text" name="intmsg" class="form-control" value="<?php echo htmlspecialchars($intmsg); ?>" 
                                       placeholder="Enter subscription message" maxlength="160">
                                <small style="color: #6c757d; display: block; margin-top: 5px;">Maximum 160 characters</small>
                            </div>
                            
                            <div class="form-group">
                                <button type="submit" name="Submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Update Application
                                </button>
                                <a href="my_apps.php" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Back to My Apps
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <!-- JavaScript -->
    <script src="js/jquery-2.1.1.js"></script>
    <script src="js/bootstrap.min.js"></script>
    
    <script>
        $(document).ready(function() {
            // Toggle sidebar on mobile
            document.getElementById('toggleSidebar').addEventListener('click', function() {
                document.getElementById('sidebar').classList.toggle('show');
            });
            
            // Toggle dropdown menus
            document.getElementById('appsMenu').addEventListener('click', function(e) {
                e.preventDefault();
                document.getElementById('appsDropdown').classList.toggle('show');
            });
            
            document.getElementById('contentMenu').addEventListener('click', function(e) {
                e.preventDefault();
                document.getElementById('contentDropdown').classList.toggle('show');
            });
            
            document.getElementById('managersMenu').addEventListener('click', function(e) {
                e.preventDefault();
                document.getElementById('managersDropdown').classList.toggle('show');
            });
            
            // User dropdown
            document.getElementById('userDropdown').addEventListener('click', function() {
                document.getElementById('dropdownMenu').classList.toggle('show');
            });
            
            // Close dropdown when clicking outside
            document.addEventListener('click', function(event) {
                const dropdown = document.getElementById('dropdownMenu');
                const userDropdown = document.getElementById('userDropdown');
                
                if (!userDropdown.contains(event.target) && !dropdown.contains(event.target)) {
                    dropdown.classList.remove('show');
                }
            });
            
            // Tab functionality
            $('.tab').click(function() {
                $('.tab').removeClass('active');
                $(this).addClass('active');
                
                var tabId = $(this).data('tab');
                $('.tab-content').removeClass('active');
                $('#' + tabId + '-tab').addClass('active');
            });
            
            // Form validation
            $('form').submit(function() {
                var valid = true;
                $('#subscription-tab input[required]').each(function() {
                    if ($(this).val().trim() === '') {
                        valid = false;
                        $(this).css('border-color', 'var(--danger)');
                    } else {
                        $(this).css('border-color', '');
                    }
                });
                
                if (!valid) {
                    // Switch to subscription tab if there are errors
                    $('.tab').removeClass('active');
                    $('[data-tab="subscription"]').addClass('active');
                    $('.tab-content').removeClass('active');
                    $('#subscription-tab').addClass('active');
                    
                    // Show alert
                    alert('Please fill in all required fields in the Subscription tab.');
                }
                
                return valid;
            });
        });
    </script>
</body>
</html>