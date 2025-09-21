<?php 
include 'db.php';
//ini_set('display_errors', '1');

include_once 'SMSSender.php';
if(!isset($_SESSION['username']) || $_SESSION['username']=="Guest"){
    header("Location: login.php");
    exit;
}

$menu = 4;
$sub_menu = 4;
$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['username'];

// Decode parameters with proper null coalescing
$id = isset($_GET['id']) ? decode_string($_GET['id'], $user_id) : 0;
$ac = isset($_GET['a']) ? decode_string($_GET['a'], $user_id) : 0;

define('SMS_SENDER_URL', 'http://api.dialog.lk:8080/sms/send');
define('MSMS_SENDER_URL', 'https://api.mspace.lk/sms/send');

// Initialize variables
$appid = $app_name = $app_category = $app_key = $app_database = $app_sid = $app_spassword = '';
$categoryfree = $sendnow = $sms = $send_date = $cat_id = $form_msg = '';
$today = date("Y-m-d");
$tomorrow = date("Y-m-d", (time() + (60 * 60 * 24)));

if($ac > 0){
    $appid = $ac;
} else {
    $appid = $_POST['appid'] ?? '';
}

if($appid > 0 || $ac > 0){
    try {
        $connection = db_connect();
        $sqle = "SELECT * FROM app_table WHERE id = ?";
        $stmt = mysqli_prepare($connection, $sqle);
        mysqli_stmt_bind_param($stmt, "i", $appid);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if($rowe = mysqli_fetch_assoc($result)){
            $app_name = $rowe['app_name'];
            $app_category = $rowe['app_category'];
            $app_key = $rowe['app_key'];
            $app_database = $rowe['app_database'];
            $app_sid = $rowe['app_id'];
            $app_spassword = $rowe['app_password'];
            $categoryfree = $rowe['categoryfree'];
            $sendnow = $rowe['sendnow'];
            
            if($app_key != "" && $id > 0){
                $db_connect = db_connect();
                $sql = "SELECT * FROM ".$app_key."_job_list WHERE srowid = ?";
                $stmt2 = mysqli_prepare($db_connect, $sql);
                mysqli_stmt_bind_param($stmt2, "i", $id);
                mysqli_stmt_execute($stmt2);
                $result2 = mysqli_stmt_get_result($stmt2);
                
                if($row = mysqli_fetch_assoc($result2)){
                    $sms = $row['sms'];
                    $cat_id = $row['cat_id'];
                    $readonly = "disabled";
                    $rows = 1;
                }
            }
        }
    } catch (Exception $e) {
        $form_msg = "Error: " . $e->getMessage();
    }
}

if($id > 0){
    $heading = "Delete Content";
    $action = "Delete";
    $class = "danger";
} else {
    $heading = "Add New Content";
    $action = "Add";
    $class = "primary";
}

$Submit = $_POST['Submit'] ?? '';

if(!empty($app_sid)){
    if($app_category == 1){
        $sender = new SMSSender(SMS_SENDER_URL, $app_sid, $app_spassword);
    } else {
        $sender = new SMSSender(MSMS_SENDER_URL, $app_sid, $app_spassword);
    }
}

if($Submit == "Add"){
    $sms = $_POST['sms'] ?? '';
    $cat_id = $_POST['cat_id'] ?? '';
    $send_date = $_POST['send_date'] ?? $today;
    
    if(empty($sms)){
        $form_msg .= "Enter SMS text.<br>";
    }
    
    if(empty($cat_id) && $categoryfree == 0){
        $form_msg .= "Select Category.<br>";
    }
    
    if(empty($form_msg) && !empty($app_key)){
        try {
            $db_connect = db_connect();
            mysqli_set_charset($db_connect, 'utf8');
            $sms_escaped = mysqli_real_escape_string($db_connect, $sms);
            
            if($categoryfree == 0){
                $sql2 = "INSERT INTO ".$app_key."_job_list (cat_id, sms, dtime, send_date) VALUES (?, ?, ?, ?)";
                $stmt = mysqli_prepare($db_connect, $sql2);
                mysqli_stmt_bind_param($stmt, "isis", $cat_id, $sms_escaped, time(), $send_date);
            } else {
                $sql2 = "INSERT INTO ".$app_key."_job_list (cat_id, sms, dtime, send_date) VALUES (?, ?, ?, ?)";
                $stmt = mysqli_prepare($db_connect, $sql2);
                mysqli_stmt_bind_param($stmt, "isis", $appid, $sms_escaped, time(), $send_date);
            }
            
            mysqli_stmt_execute($stmt);
            
            if($appid == 409){
                $sql11 = "SELECT * FROM ".$app_key."_sub_selected WHERE category = ? AND active = 1";
                $stmt11 = mysqli_prepare(db_connect(), $sql11);
                mysqli_stmt_bind_param($stmt11, "i", $cat_id);
                mysqli_stmt_execute($stmt11);
                $result11 = mysqli_stmt_get_result($stmt11);
                
                while($row11 = mysqli_fetch_assoc($result11)){
                    //$response = $sender->sms($sms, $row11['mask_address']);
                }
            } else if($sendnow == 1 && $send_date == $today){
                $sms_clean = str_replace("\r", "", $sms);
                //$response = $sender->broadcast($sms_clean);
            }
            
            $form_msg = "SMS saved.";
            $sms = "";
        } catch (Exception $e) {
            $form_msg = "Error: " . $e->getMessage();
        }
    }
} else if($Submit == "Delete" && $id > 0 && !empty($app_key)){
    try {
		$connection = db_connect();
            
        if (!$connection) {
            throw new Exception("Failed to connect to database");
        }
        $sql2 = "DELETE FROM ".$app_key."_job_list WHERE srowid = ?";
        $stmt = mysqli_prepare($connection, $sql2);
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        
        $form_msg = "Content deleted.";
        header("Location: content.php");
        exit;
    } catch (Exception $e) {
        $form_msg = "Error: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SMART Apps | Content Management</title>
    
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
        :root {
            --primary: #4361ee;
            --secondary: #3f37c9;
            --success: #4cc9f0;
            --info: #4895ef;
            --warning: #f72585;
            --danger: #e63946;
            --light: #f8f9fa;
            --dark: #212529;
            --sidebar-width: 260px;
            --header-height: 70px;
            --border-radius: 12px;
            --box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            --transition: all 0.3s ease;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f5f7fb;
            color: #495057;
            overflow-x: hidden;
            min-height: 100vh;
        }
        
        /* Sidebar Styles */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100%;
            width: var(--sidebar-width);
            background: linear-gradient(180deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            z-index: 1000;
            transition: var(--transition);
            overflow-y: auto;
        }
        
        .sidebar-header {
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .sidebar-header img {
            max-width: 120px;
        }
        
        .sidebar-profile {
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .profile-name {
            font-weight: 600;
            font-size: 1.1rem;
            margin-bottom: 5px;
        }
        
        .profile-role {
            font-size: 0.85rem;
            opacity: 0.8;
        }
        
        .sidebar-menu {
            padding: 15px 0;
        }
        
        .menu-label {
            padding: 10px 20px;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            opacity: 0.7;
            margin-top: 15px;
        }
        
        .menu-item {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            color: white;
            text-decoration: none;
            transition: var(--transition);
            position: relative;
        }
        
        .menu-item:hover, .menu-item.active {
            background-color: rgba(255, 255, 255, 0.1);
        }
        
        .menu-icon {
            width: 24px;
            text-align: center;
            margin-right: 15px;
        }
        
        .menu-text {
            flex: 1;
        }
        
        .menu-badge {
            background-color: var(--warning);
            color: white;
            font-size: 0.7rem;
            padding: 3px 8px;
            border-radius: 20px;
        }
        
        /* Sidebar Dropdown Styles */
        .menu-dropdown {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease;
            background-color: rgba(0, 0, 0, 0.1);
        }
        
        .menu-dropdown.show {
            max-height: 500px;
        }
        
        .submenu-item {
            display: flex;
            align-items: center;
            padding: 10px 20px 10px 60px;
            color: rgba(255, 255, 255, 0.9);
            text-decoration: none;
            transition: var(--transition);
            font-size: 0.9rem;
        }
        
        .submenu-item:hover {
            background-color: rgba(255, 255, 255, 0.1);
            color: white;
        }
        
        /* Main Content */
        .main-content {
            margin-left: var(--sidebar-width);
            transition: var(--transition);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        /* Header */
        .header {
            height: var(--header-height);
            background-color: white;
            box-shadow: var(--box-shadow);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 25px;
            position: sticky;
            top: 0;
            z-index: 999;
        }
        
        .toggle-sidebar {
            font-size: 24px;
            cursor: pointer;
            color: var(--dark);
        }
        
        .search-box {
            position: relative;
            width: 300px;
        }
        
        .search-input {
            width: 100%;
            padding: 10px 15px 10px 40px;
            border: 1px solid #e2e8f0;
            border-radius: 30px;
            font-size: 0.9rem;
            transition: var(--transition);
        }
        
        .search-input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.15);
        }
        
        .search-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #a0aec0;
        }
        
        .header-actions {
            display: flex;
            align-items: center;
        }
        
        .header-action {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-left: 15px;
            color: var(--dark);
            background-color: var(--light);
            cursor: pointer;
            transition: var(--transition);
            position: relative;
        }
        
        .header-action:hover {
            background-color: var(--primary);
            color: white;
        }
        
        .notification-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background-color: var(--danger);
            color: white;
            font-size: 0.7rem;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .user-dropdown {
            position: relative;
        }
        
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            cursor: pointer;
        }
        
        .dropdown-menu {
            position: absolute;
            top: 50px;
            right: 0;
            background-color: white;
            width: 200px;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            padding: 10px 0;
            z-index: 1000;
            opacity: 0;
            visibility: hidden;
            transform: translateY(10px);
            transition: var(--transition);
        }
        
        .dropdown-menu.show {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }
        
        .dropdown-item {
            padding: 10px 20px;
            display: flex;
            align-items: center;
            color: var(--dark);
            text-decoration: none;
            transition: var(--transition);
        }
        
        .dropdown-item:hover {
            background-color: var(--light);
        }
        
        .dropdown-icon {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }
        
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
        
        textarea.form-control {
            min-height: 120px;
            resize: vertical;
        }
        
        .form-control:disabled {
            background-color: #f8f9fa;
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
        
        .btn-danger {
            background-color: var(--danger);
            color: white;
        }
        
        .btn-danger:hover {
            background-color: #c53030;
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
        
        .table-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            margin-top: 20px;
        }
        
        .table-header {
            padding: 20px;
            border-bottom: 1px solid #e2e8f0;
        }
        
        .table-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--dark);
            margin: 0;
        }
        
        .empty-state {
            text-align: center;
            padding: 40px;
            color: #6c757d;
        }
        
        .empty-icon {
            font-size: 3rem;
            color: #dee2e6;
            margin-bottom: 15px;
        }
        
        .badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 500;
        }
        
        .badge-success {
            background-color: rgba(76, 201, 240, 0.1);
            color: #4cc9f0;
        }
        
        .badge-warning {
            background-color: rgba(247, 37, 133, 0.1);
            color: #f72585;
        }
        
        .footer {
            padding: 20px;
            background-color: white;
            border-top: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        /* DataTable Styles */
        .dataTables_wrapper {
            padding: 0 20px 20px;
        }
        
        table.dataTable {
            width: 100% !important;
            margin: 0 !important;
            border-collapse: collapse !important;
        }
        
        table.dataTable thead th {
            border-bottom: 2px solid #e2e8f0;
            padding: 15px 10px;
            font-weight: 600;
            color: var(--dark);
            background-color: #f8f9fa;
        }
        
        table.dataTable tbody td {
            padding: 12px 10px;
            border-top: 1px solid #e2e8f0;
            vertical-align: middle;
        }
        
        table.dataTable tbody tr:hover {
            background-color: #f8f9fa;
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
            
            table.dataTable thead th,
            table.dataTable tbody td {
                padding: 8px 5px;
                font-size: 0.9rem;
            }
        }
		/* Pagination Fix */
        .dataTables_paginate {
            margin-top: 20px !important;
            float: right !important;
            display: flex !important;
            align-items: center !important;
            gap: 4px !important;
        }

        .dataTables_paginate .paginate_button {
            padding: 8px 12px !important;
            border: 1px solid #dee2e6 !important;
            border-radius: 4px !important;
            color: var(--primary) !important;
            background: white !important;
            cursor: pointer !important;
            transition: all 0.2s ease !important;
            min-width: 38px !important;
            text-align: center !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
        }

        .dataTables_paginate .paginate_button:hover {
            background-color: var(--primary) !important;
            color: white !important;
            border-color: var(--primary) !important;
            text-decoration: none !important;
        }

        .dataTables_paginate .paginate_button.current {
            background-color: var(--primary) !important;
            color: white !important;
            border-color: var(--primary) !important;
        }

        .dataTables_paginate .paginate_button.disabled {
            color: #6c757d !important;
            cursor: not-allowed !important;
            opacity: 0.6 !important;
        }

        .dataTables_paginate .paginate_button.disabled:hover {
            background-color: white !important;
            color: #6c757d !important;
            border-color: #dee2e6 !important;
        }

        /* Info Text */
        .dataTables_info {
            padding-top: 15px !important;
            color: #6c757d !important;
            float: left !important;
            margin-top: 20px !important;
        }

        /* Clear floats */
        .dataTables_wrapper:after {
            content: "";
            display: table;
            clear: both;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .dataTables_wrapper {
                padding: 0 10px 10px;
            }
            
            .dataTables_filter {
                float: none !important;
                margin-bottom: 10px !important;
                width: 100%;
            }
            
            .dataTables_filter label {
                flex-direction: column;
                align-items: flex-start !important;
            }
            
            .dataTables_filter input {
                margin-left: 0 !important;
                margin-top: 5px;
                width: 100% !important;
                min-width: unset !important;
            }
            
            .dataTables_length {
                float: none !important;
                margin-bottom: 10px !important;
            }
            
            .dataTables_length label {
                flex-direction: column;
                align-items: flex-start !important;
            }
            
            .dataTables_length select {
                margin: 5px 0 !important;
            }
            
            .dataTables_paginate {
                float: none !important;
                justify-content: center !important;
                flex-wrap: wrap;
            }
            
            .dataTables_info {
                float: none !important;
                text-align: center !important;
                margin-bottom: 10px;
            }
            
            table.dataTable thead th,
            table.dataTable tbody td {
                padding: 8px 5px;
                font-size: 0.9rem;
            }
        }

        /* Dark mode support for form elements */
        @media (prefers-color-scheme: dark) {
            .dataTables_filter input,
            .dataTables_length select {
                background-color: #2d3748 !important;
                color: white !important;
                border-color: #4a5568 !important;
            }
            
            .dataTables_filter input:focus,
            .dataTables_length select:focus {
                border-color: var(--primary) !important;
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
        <?php require_once 'head.php'; ?>  
        
        <!-- Content Section -->
        <div class="content">
            <h1 class="page-title">Content Management</h1>
            <p class="page-description">Manage SMS content for your applications</p>
            
            <div class="content-card">
                <div class="content-header">
                    <h2 class="content-title">Content</h2>
                    <p class="content-subtitle"><?php echo htmlspecialchars($heading); ?></p>
                </div>
                
                <div class="content-body">
                    <div class="row">
                        <?php if(!empty($form_msg)): ?>   
                        <div class="col-12">
                            <div class="alert <?php echo strpos($form_msg, 'Error') !== false ? 'alert-danger' : 'alert-success'; ?>">
                                <i class="fa <?php echo strpos($form_msg, 'Error') !== false ? 'fa-exclamation-triangle' : 'fa-check-circle'; ?>"></i>
                                <?php echo $form_msg; ?>                 
                            </div>
                        </div>
                        <?php endif; ?>  
                        
                        <div class="col-md-6">  
                            <form role="form" action="" method="post">
                                <div class="form-group">
                                    <label class="form-label">Application</label> 
									<?php 
										 
                                        $connection = db_connect();
										if (!$connection) {
											throw new Exception("Failed to connect to database");
										}
                                        if($_SESSION['userlevel'] > 9){
                                            $sqle = "SELECT * FROM content_update 
                                            LEFT JOIN app_table ON app_table.id = content_update.app_id
                                            WHERE content_update.user_id = ? AND content_update.active = 1 AND app_sub_category = 1";
                                            $stmt = mysqli_prepare($connection, $sqle);
                                            $bind_result = mysqli_stmt_bind_param($stmt, "i", $user_id);
                                        } else {
                                            
                                            $sqle = "SELECT * FROM app_table WHERE user_id = ? AND app_sub_category = 1";
                                            $stmt = mysqli_prepare($connection, $sqle);
                                            $bind_result = mysqli_stmt_bind_param($stmt, "i", $user_id);
                                            
                                        }
										 
                                        if (!$stmt) {
											throw new Exception("Prepare failed: " . mysqli_error($connection));
										}
										if (!$bind_result) {
											throw new Exception("Bind param failed: " . mysqli_error($connection));
										}
                                        $execute_result = mysqli_stmt_execute($stmt);
            
										if (!$execute_result) {
											throw new Exception("Execute failed: " . mysqli_error($connection));
										}
 
                                        $result = mysqli_stmt_get_result($stmt);
										?>
                                    <select class="form-control" name="appid" id="app_sub_category" onChange="this.form.submit();">
                                        <option value="">Select Application</option>
                                        <?php
                                        
                                        while($rowe = mysqli_fetch_assoc($result)):
                                            $app_name_option = $rowe['app_name']; 
                                            $app_id_option = $rowe['id'];
                                        ?>                     
                                        <option value="<?php echo $app_id_option; ?>" <?php echo $appid == $app_id_option ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($app_name_option)." - ".$platform[$rowe['app_category']]; ?>
                                        </option>
                                        <?php endwhile; ?>                   
                                    </select>
                                </div>
                                
                                <?php if($categoryfree == 0 && $appid != ""): ?>
                                <div class="form-group">
                                    <label class="form-label">Category</label> 
                                    <select name="cat_id" class="form-control" id="cat_id">
                                        <option value="">Select Category</option>
                                        <?php    
                                        $sql = "SELECT * FROM ".$app_key."_job_categories WHERE active = 1 ORDER BY cat_text";
                                        
                                        
                                        $rs = mysqli_query(db_connect(), $sql);
                                        while($row = mysqli_fetch_array($rs)):
                                        ?>
                                        <option value="<?php echo $row['cat_id']; ?>" <?php echo $cat_id == $row['cat_id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($row['cat_text']); ?>
                                        </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                                <?php endif; ?>
                                
                                <div class="form-group">
                                    <label class="form-label">Content</label> 
                                    <textarea name="sms" id="sms" class="form-control" rows="5"><?php echo htmlspecialchars($sms); ?></textarea>
                                </div>
                                
                                <?php if($appid == 344): ?>
                                <div class="form-group">
                                    <label class="form-label">Send Date</label> 
                                    <input type="date" class="form-control" name="send_date" value="<?php echo $send_date; ?>" min="<?php echo $today; ?>">
                                </div>
                                <?php endif; ?>
                                
                                <div>
                                    <button type="submit" class="btn btn-<?php echo $class; ?>" name="Submit" value="<?php echo $action; ?>">
                                        <i class="fa fa-<?php echo $action == 'Add' ? 'plus' : 'trash'; ?>"></i> <?php echo $action; ?> Content
                                    </button>
                                </div>
                            </form>
                        </div>
                        
                        <div class="col-md-6">
                            <h4 class="section-title">Content List</h4>
                            <?php if($appid > 0): ?>
                            <div class="table-container">
                                <div class="table-header">
                                    <h5 class="table-title">Content for <?php echo htmlspecialchars($app_name); ?></h5>
                                </div>
                                <table class="table table-striped table-bordered table-hover dataTables-example">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Category</th>
                                            <th>Content</th>
                                            <th>Date</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $i = 0;
                                        if(!empty($app_key)){
                                            if($categoryfree == 0){
                                                $sql_cat = "SELECT * FROM ".$app_key."_job_list  
                                                LEFT JOIN ".$app_key."_job_categories
                                                ON ".$app_key."_job_categories.cat_id = ".$app_key."_job_list.cat_id 
                                                ORDER BY dtime DESC";
                                            } else {
                                                $sql_cat = "SELECT * FROM ".$app_key."_job_list  
                                                WHERE ".$app_key."_job_list.cat_id = '$appid' 
                                                ORDER BY dtime DESC";
                                            }
                                            
                                            $reply_cat = mysqli_query(db_connect(), $sql_cat);
                                            while($row_cat = mysqli_fetch_array($reply_cat)):
                                                $i++;
                                                $category = $row_cat['cat_text']??'';
                                                $sent_date = $row_cat['send_date'];
                                                $sent_sms = $row_cat['sms'];
                                                $rowid = $row_cat['srowid'];
                                        ?>
                                        <tr class="gradeX">
                                            <td class="right"><?php echo $i; ?></td>
                                            <td class="left"><?php echo htmlspecialchars($category); ?></td>
                                            <td class="left" style="word-break: break-all;"><?php echo htmlspecialchars($sent_sms); ?></td>
                                            <td class="center"><?php echo $sent_date; ?></td>
                                            <td class="center">
                                                <a href="content.php?id=<?php echo encode_string($rowid, $user_id); ?>&a=<?php echo encode_string($appid, $user_id); ?>" class="btn btn-xs btn-danger">
                                                    <i class="fa fa-trash"></i> Delete
                                                </a>
                                            </td>
                                        </tr>
                                        <?php endwhile; 
                                        } ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php else: ?>
                            <div class="empty-state">
                                <i class="fa fa-list-alt empty-icon"></i>
                                <p>Select an application to view content</p>
                            </div>
                            <?php endif; ?> 
                        </div>
                    </div>
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
    <script src="js/plugins/dataTables/jquery.dataTables.js"></script>
    <script src="js/plugins/dataTables/dataTables.bootstrap.js"></script>
    <script src="js/plugins/dataTables/dataTables.responsive.js"></script>
    <script src="js/plugins/dataTables/dataTables.tableTools.min.js"></script>

    <script>
        $(document).ready(function() {
            // Initialize DataTables
            $('.dataTables-example').dataTable({
                responsive: true,
                "dom": 'T<"clear">lfrtip',
                "tableTools": {
                    "sSwfPath": "js/plugins/dataTables/swf/copy_csv_xls_pdf.swf"
                }
            });
            
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
    </script>
</body>
</html>