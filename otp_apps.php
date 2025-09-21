<?php 
include 'db.php';

if(!isset($_SESSION['username']) || $_SESSION['username'] == "Guest"){
    header("Location: login.php");
    exit;
}

$user_name = $_SESSION['username'];
$user_id = $_SESSION['user_id'];
$menu = 6;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SMART Apps | OTP API Key Management</title>
    
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
        
        .btn-success {
            background-color: var(--success);
            color: white;
        }
        
        .btn-success:hover {
            background-color: #3aa5c9;
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
        
        .api-key {
            font-family: monospace;
            background-color: #f8f9fa;
            padding: 8px 12px;
            border-radius: 6px;
            border: 1px solid #e9ecef;
            word-break: break-all;
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
            <h1 class="page-title">OTP API Key Management</h1>
            <p class="page-description">Manage your OTP API keys for Ideamart and mSpace applications</p>
            
            <!-- API Keys Table -->
            <div class="content-card">
                <div class="content-header">
                    <h2 class="content-title">Your API Keys</h2>
                    <p class="content-subtitle">All your OTP API keys in one place</p>
                </div>
                
                <div class="content-body">
                    <div class="table-container">
                        <div class="table-header">
                            <h5 class="table-title">OTP API Keys</h5>
                        </div>
                        <table class="table table-striped table-bordered table-hover dataTables-example">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>App Name</th>
                                    <th>Ideamart AppID</th>
                                    <th>mSpace App ID</th>
                                    <th>OTP API Key</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                 $connection = db_connect();
            
                                if (!$connection) {
                                    throw new Exception("Failed to connect to database");
                                }
                                $sqle = "SELECT * FROM otp_apps WHERE user_id = ?";
                                $stmt = mysqli_prepare($connection, $sqle);
                                mysqli_stmt_bind_param($stmt, "i", $user_id);
                                mysqli_stmt_execute($stmt);
                                $result = mysqli_stmt_get_result($stmt);
                                
                                while($rowe = mysqli_fetch_assoc($result)):
                                    $id = $rowe['id'];
                                    $iApp_Id = $rowe['iApp_Id'];
                                    $iApp_password = $rowe['iApp_password'];
                                    $mApp_Id = $rowe['mApp_Id'];
                                    $mApp_password = $rowe['mApp_password'];
                                    $app_key = $rowe['app_key'];
                                    $app_name = $rowe['app_name'];
                                    $iHide = $rowe['iHide'];
                                    $mHide = $rowe['mHide'];
                                    
                                    if($mHide == 1) $mApp_Id = "";
                                    if($iHide == 1) $iApp_Id = "";
                                ?>
                                <tr class="gradeX">
                                    <td class="right"><?php echo $id; ?></td>
                                    <td class="left"><?php echo htmlspecialchars($app_name); ?></td>
                                    <td class="left"><?php echo htmlspecialchars($iApp_Id); ?></td>
                                    <td class="right"><?php echo htmlspecialchars($mApp_Id); ?></td>
                                    <td class="right">
                                        <span class="api-key"><?php echo htmlspecialchars($app_key); ?></span>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Create New API Key Form -->
            <div class="content-card">
                <div class="content-header">
                    <h2 class="content-title">Create New OTP API Key</h2>
                    <p class="content-subtitle">Generate a new API key for your applications</p>
                </div>
                
                <div class="content-body">
                    <?php
                    $iApp = $_POST['iApp'] ?? '';
                    $mApp = $_POST['mApp'] ?? '';
                    $submit = $_POST['submit'] ?? '';
                    $form_msg = '';
                    
                    if($submit == "Submit"){
                        if(empty($iApp) && empty($mApp)){
                            $form_msg = "Select at least one app";
                        } else {
                            $ids = [];
                            if(!empty($iApp)) $ids[] = $iApp;
                            if(!empty($mApp)) $ids[] = $mApp;
                            
                            $id_list = implode(",", $ids);
                            $sql_app = "SELECT * FROM app_table WHERE id IN ($id_list)";
                            $rs_app = mysqli_query(db_connect(), $sql_app);
                            
                            $iAppId = $iAppPswd = $mAppId = $mAppPswd = "";
                            $appName = "";
                            
                            while($row_app = mysqli_fetch_array($rs_app)){
                                if($row_app['app_category'] == 1){
                                    $iAppId = $row_app['app_id'];
                                    $iAppPswd = $row_app['app_password'];
                                } 
                                if($row_app['app_category'] == 2){
                                    $mAppId = $row_app['app_id'];
                                    $mAppPswd = $row_app['app_password'];
                                } 
                                $appName .= $row_app['app_name'] . " ";
                            }
                            
                            if(empty($form_msg)){
                                $appKey = $iAppPswd . $mAppPswd . time(); 
                                $appKeymd5 = md5($appKey);	
                                $sql = "INSERT INTO otp_apps (iApp_Id, iApp_password, mApp_Id, mApp_password, active, app_key, app_name, user_id) 
                                        VALUES (?, ?, ?, ?, '1', ?, ?, ?)";
                                
                                $stmt = mysqli_prepare($connection, $sql);
                                mysqli_stmt_bind_param($stmt, "ssssssi", $iAppId, $iAppPswd, $mAppId, $mAppPswd, $appKeymd5, $appName, $user_id);
                                
                                if(mysqli_stmt_execute($stmt)){
                                    echo "<meta http-equiv=\"refresh\" content=\"2;URL=otp_apps.php\">";
                                    $form_msg = "API key created successfully!";
                                } else {
                                    $form_msg = "Error creating API key: " . mysqli_error(db_connect());
                                }
                            }
                        }
                    }
                    ?>
                    
                    <?php if(!empty($form_msg)): ?>   
                    <div class="alert <?php echo strpos($form_msg, 'Error') !== false ? 'alert-danger' : 'alert-success'; ?>">
                        <i class="fa <?php echo strpos($form_msg, 'Error') !== false ? 'fa-exclamation-triangle' : 'fa-check-circle'; ?>"></i>
                        <?php echo $form_msg; ?>                 
                    </div>
                    <?php endif; ?>
                    
                    <form action="" method="post">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Ideamart App</label>
                                    <select name="iApp" class="form-control">
                                        <option value="">Select Ideamart App</option>
                                        <?php
                                        $sql_app = "SELECT * FROM app_table WHERE app_category = 1 AND user_id = ?";
                                        $stmt = mysqli_prepare($connection, $sql_app);
                                        mysqli_stmt_bind_param($stmt, "i", $user_id);
                                        mysqli_stmt_execute($stmt);
                                        $result = mysqli_stmt_get_result($stmt);
                                        
                                        while($row_app = mysqli_fetch_array($result)):
                                        ?>
                                        <option value="<?php echo $row_app['id']; ?>" <?php echo $iApp == $row_app['id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($row_app['app_name']); ?>
                                        </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">mSpace App</label>
                                    <select name="mApp" class="form-control">
                                        <option value="">Select mSpace App</option>
                                        <?php
                                        $sql_app = "SELECT * FROM app_table WHERE app_category = 2 AND user_id = ?";
                                        $stmt = mysqli_prepare($connection, $sql_app);
                                        mysqli_stmt_bind_param($stmt, "i", $user_id);
                                        mysqli_stmt_execute($stmt);
                                        $result = mysqli_stmt_get_result($stmt);
                                        
                                        while($row_app = mysqli_fetch_array($result)):
                                        ?>
                                        <option value="<?php echo $row_app['id']; ?>" <?php echo $mApp == $row_app['id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($row_app['app_name']); ?>
                                        </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group text-center">
                            <button type="submit" name="submit" value="Submit" class="btn btn-primary">
                                <i class="fa fa-key"></i> Generate API Key
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