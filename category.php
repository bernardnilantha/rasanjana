<?php 
// Start session and include files at the very top
session_start();
include 'db.php';
require_once 'functions.php';

// Check if user is logged in
if (!isset($_SESSION['username']) || $_SESSION['username'] == "Guest") {
    header("Location: login.php");
    exit;
}

$menu = 4;
$sub_menu = 3;
$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['username'];

// Decode parameters with proper null coalescing
$id = isset($_GET['id']) ? decode_string($_GET['id'], $user_id) : 0;
$ac = isset($_GET['a']) ? decode_string($_GET['a'], $user_id) : 0;

// Initialize variables
$cat_text = $cat_status = $appid = $app_name = $app_key = $app_database = '';
$form_msg = $heading = $action = $readonly = '';
$rows = 0;

// Process form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $cat_text = $_POST['cat_text'] ?? '';
    $cat_status = $_POST['cat_status'] ?? '1';
    $Submit = $_POST['Submit'] ?? '';
    $appid = $_POST['appid'] ?? $ac;
    
    if ($ac > 0) {
        $appid = $ac;
    }
    
    // Validate and process
    if ($Submit == "Add" || $Submit == "Update") {
        if (empty($cat_text)) {
            $form_msg .= "Enter Category.<br>";
        }
        
        if (empty($form_msg) && $appid > 0) {
            try {
                // Get app details using prepared statement
                $connection = db_connect();
                if (!$connection) {
                    throw new Exception("Failed to connect to database");
                }
                $sqle = "SELECT * FROM app_table WHERE id = ?";
                $stmt = mysqli_prepare($connection, $sqle);
                mysqli_stmt_bind_param($stmt, "i", $appid);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                
                if ($rowe = mysqli_fetch_assoc($result)) {
                    $app_name = $rowe['app_name'];
                    $app_key = $rowe['app_key'];
                    $app_database = $rowe['app_database'];
                    
                    if ($Submit == "Add") {
                        // Insert new category using prepared statement
                        $sql2 = "INSERT INTO ".$app_key."_job_categories (cat_text, active) VALUES (?, ?)";
                        $stmt2 = mysqli_prepare($connection, $sql2);
                        mysqli_stmt_bind_param($stmt2, "si", $cat_text, $cat_status);
                        mysqli_stmt_execute($stmt2);
                        
                        // Update app table
                        $sql_cat = "UPDATE app_table SET categoryfree = 0 WHERE id = ?";
                        $stmt_cat = mysqli_prepare($connection, $sql_cat);
                        mysqli_stmt_bind_param($stmt_cat, "i", $appid);
                        mysqli_stmt_execute($stmt_cat);
                        
                        $form_msg = "Category Saved.";
                    } else if ($Submit == "Update" && $id > 0) {
                        // Update existing category using prepared statement
                        $sql2 = "UPDATE ".$app_key."_job_categories SET cat_text = ?, active = ? WHERE cat_id = ?";
                        $stmt2 = mysqli_prepare($connection, $sql2);
                        mysqli_stmt_bind_param($stmt2, "sii", $cat_text, $cat_status, $id);
                        mysqli_stmt_execute($stmt2);
                        
                        $form_msg = "Category Updated.";
                        header("Location: category.php?a=".encode_string($appid, $user_id));
                        exit;
                    }
                }
            } catch (Exception $e) {
                $form_msg = "Error: " . $e->getMessage();
            }
        }
    }
}

// Load data for editing
if ($id > 0 || $ac > 0) {
    if ($ac > 0) {
        $appid = $ac;
    }
    
    try {
        $connection = db_connect();
        if (!$connection) {
            throw new Exception("Failed to connect to database");
        }
        $sqle = "SELECT * FROM app_table WHERE id = ?";
        $stmt = mysqli_prepare($connection, $sqle);
        mysqli_stmt_bind_param($stmt, "i", $appid);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if ($rowe = mysqli_fetch_assoc($result)) {
            $app_name = $rowe['app_name'];
            $app_key = $rowe['app_key'];
            $app_database = $rowe['app_database'];
            
            if ($id > 0) {
                // Load category data using prepared statement
                $sql = "SELECT * FROM ".$app_key."_job_categories WHERE cat_id = ?";
                $stmt2 = mysqli_prepare($connection, $sql);
                mysqli_stmt_bind_param($stmt2, "i", $id);
                mysqli_stmt_execute($stmt2);
                $result2 = mysqli_stmt_get_result($stmt2);
                
                if ($row = mysqli_fetch_assoc($result2)) {
                    $cat_text = $row['cat_text'];
                    $cat_status = $row['active'];
                    $rows = 1;
                    $heading = "Edit Category: " . $row['cat_text'];
                    $action = "Update";
                    $readonly = "disabled";
                }
            }
        }
    } catch (Exception $e) {
        $form_msg = "Error loading data: " . $e->getMessage();
    }
}

// Set default heading and action if not set
if (empty($heading)) {
    $heading = "Add New Category";
    $action = "Add";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SMART Apps | Category Management</title>
    
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
        
        /* Category Content */
        .category-content {
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
        
        .category-card {
            background-color: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            overflow: hidden;
            margin-bottom: 20px;
        }
        
        .category-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            padding: 20px;
            position: relative;
        }
        
        .category-title {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 5px;
        }
        
        .category-subtitle {
            opacity: 0.9;
            font-size: 0.9rem;
        }
        
        .category-body {
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
            .category-content {
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
        /* DataTable Container */
        .dataTables_wrapper {
            padding: 0 20px 20px;
            width: 100%;
            position: relative;
        }

        /* Fixed DataTable Styles */
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
            position: relative;
        }

        table.dataTable tbody td {
            padding: 12px 10px;
            border-top: 1px solid #e2e8f0;
            vertical-align: middle;
        }

        table.dataTable tbody tr:hover {
            background-color: #f8f9fa;
        }

        /* Search Input Fix */
        .dataTables_filter {
            margin-bottom: 15px !important;
            float: right !important;
        }

        .dataTables_filter label {
            display: flex !important;
            align-items: center !important;
            margin: 0 !important;
        }

        .dataTables_filter input {
            border: 1px solid #dee2e6 !important;
            border-radius: 4px !important;
            padding: 8px 12px !important;
            margin-left: 10px !important;
            height: 38px !important;
            min-width: 200px !important;
            transition: all 0.2s ease !important;
        }

        .dataTables_filter input:focus {
            outline: none !important;
            border-color: var(--primary) !important;
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.15) !important;
        }

        /* Records Per Page Dropdown Fix */
        .dataTables_length {
            margin-bottom: 15px !important;
            float: left !important;
        }

        .dataTables_length label {
            display: flex !important;
            align-items: center !important;
            margin: 0 !important;
            font-weight: normal !important;
        }

        .dataTables_length select {
            border: 1px solid #dee2e6 !important;
            border-radius: 4px !important;
            padding: 6px 12px !important;
            margin: 0 10px !important;
            height: 38px !important;
            background-color: white !important;
            transition: all 0.2s ease !important;
        }

        .dataTables_length select:focus {
            outline: none !important;
            border-color: var(--primary) !important;
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.15) !important;
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
        
        <!-- Category Content -->
        <div class="category-content">
            <h1 class="page-title">Category Management</h1>
            <p class="page-description">Manage categories for your applications</p>
            
            <div class="category-card">
                <div class="category-header">
                    <h2 class="category-title">Categories</h2>
                    <p class="category-subtitle"><?php echo htmlspecialchars($heading); ?></p>
                </div>
                
                <div class="category-body">
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
                                    <select class="form-control" name="appid" id="app_sub_category" onChange="this.form.submit();">
                                        <option value="">Select Application</option>
                                        <?php 
                                        $connection = db_connect();
                                        $sqle = "SELECT * FROM app_table WHERE user_id = ?";
                                        $stmt = mysqli_prepare($connection, $sqle);
                                        mysqli_stmt_bind_param($stmt, "i", $user_id);
                                        mysqli_stmt_execute($stmt);
                                        $result = mysqli_stmt_get_result($stmt);
                                        
                                        while($rowe = mysqli_fetch_assoc($result)):
                                            $app_name_option = $rowe['app_name']; 
                                            $app_id_option = $rowe['id'];
                                        ?>                     
                                        <option value="<?php echo $app_id_option; ?>" <?php echo $appid == $app_id_option ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($app_name_option)." - ".$platform[$rowe['app_category']];?>
                                        </option>
                                        <?php endwhile; ?>                   
                                    </select>
                                </div>
                                
                                <div class="form-group">
                                    <label class="form-label">Category</label> 
                                    <input type="text" placeholder="Category" class="form-control" name="cat_text" value="<?php echo htmlspecialchars($cat_text); ?>" required>
                                </div>
                                
                                <div class="form-group">
                                    <label class="form-label">Status</label> 
                                    <select name="cat_status" class="form-control" id="cat_status">
                                        <option value="1" <?php echo ($cat_status == "" || $cat_status == 1) ? 'selected' : ''; ?>>Active</option>
                                        <option value="0" <?php echo ($cat_status == "0") ? 'selected' : ''; ?>>Inactive</option>
                                    </select>
                                </div>
                                
                                <div>
                                    <button type="submit" class="btn btn-primary" name="Submit" value="<?php echo $action; ?>">
                                        <i class="fa fa-save"></i> <?php echo $action; ?> Category
                                    </button>
                                </div>
                            </form>
                        </div>
                        
                        <div class="col-md-6">
                            <h4 class="section-title">Category List</h4>
                            <?php if($appid > 0):
                                $sqle = "SELECT * FROM app_table WHERE id = ?";
                                $stmt = mysqli_prepare($connection, $sqle);
                                mysqli_stmt_bind_param($stmt, "i", $appid);
                                mysqli_stmt_execute($stmt);
                                $result = mysqli_stmt_get_result($stmt);
                                
                                if ($rowe = mysqli_fetch_assoc($result)) {
                                    $app_name = $rowe['app_name'];
                                    $app_key = $rowe['app_key'];
                                    $app_database = $rowe['app_database'];
                                 } ?>
                            <div class="table-container">
                                <div class="table-header">
                                    <h5 class="table-title">Categories for  <?php echo htmlspecialchars($app_name); ?></h5>
                                </div>
                                <table class="table table-striped table-bordered table-hover dataTables-example">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Category</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>



                                    <tbody>
                                        <?php 
                                        $i = 0;
                                         
                                        if (!empty($app_key) && !empty($app_database)) {
                                            $sqle = "SELECT * FROM ".$app_key."_job_categories";
                                            $ereply = mysqli_query(db_connect(), $sqle);
                                            
                                            while($rowe = mysqli_fetch_array($ereply)):
                                                $i++;
                                                $cat_id = $rowe['cat_id'];
                                                $text_category = $rowe['cat_text'];
                                                $active = $rowe['active'];
                                                $tx = ($active == 1) ? "Active" : "Inactive";
                                                $status_class = ($active == 1) ? "badge-success" : "badge-warning";
                                        ?>
                                        <tr class="gradeX">
                                            <td class="right"><?php echo $i; ?></td>
                                            <td class="left"><?php echo htmlspecialchars($text_category); ?></td>
                                            <td class="center">
                                                <span class="badge <?php echo $status_class; ?>"><?php echo $tx; ?></span>
                                            </td>
                                            <td class="center">
                                                <a href="category.php?id=<?php echo encode_string($cat_id, $user_id); ?>&a=<?php echo encode_string($appid, $user_id); ?>&t=e" class="btn btn-xs btn-primary">
                                                    <i class="fa fa-edit"></i> Edit
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
                                <p>Select an application to view categories</p>
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