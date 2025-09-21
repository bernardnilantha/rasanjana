<?php 
include 'db.php';

if(!isset($_SESSION['username']) || $_SESSION['username'] == "Guest"){
    header("Location: login.php");
    exit;
}

$user_name = $_SESSION['username'];
$user_id = $_SESSION['user_id'];
$menu = 7;

// Initialize variables
$appName = $image_name = $adHeading = $adCharging = $form_msg = '';
$submit = $_POST['submit'] ?? '';
$connection = db_connect();
// Process form submission
if($submit == "Submit"){
    $appName = $_POST['appName'] ?? '';
    $image_name = $_POST['image_name'] ?? '';
    $adHeading = $_POST['adHeading'] ?? '';
    $adCharging = $_POST['adCharging'] ?? '';
    
    if(empty($appName)){
        $form_msg .= "Select App Name.<br>";
    }
    
    if(empty($adCharging)){
        $form_msg .= "Enter App Charging details.<br>";
    }
    
    if(empty($form_msg)){
        $sql = "INSERT INTO ads (otp_id, ad_heading, ad_charging, active, ad_image, user_id) 
                VALUES (?, ?, ?, '1', ?, ?)";
        
        $stmt = mysqli_prepare($connection, $sql);
        mysqli_stmt_bind_param($stmt, "isssi", $appName, $adHeading, $adCharging, $image_name, $user_id);
        
        if(mysqli_stmt_execute($stmt)){
            echo "<meta http-equiv=\"refresh\" content=\"2;URL=create_ad.php\">";
            $form_msg = "Landing page created successfully!";
        } else {
            $form_msg = "Error creating landing page: " . mysqli_error(db_connect());
        }
    }
}

            
if (!$connection) {
    throw new Exception("Failed to connect to database");
}
// Get existing ads
$ads = [];
$sqle = "SELECT ads.*, otp_apps.app_name, otp_apps.adurl 
         FROM ads 
         LEFT JOIN otp_apps ON ads.otp_id = otp_apps.id 
         WHERE ads.user_id = ?";
$stmt = mysqli_prepare($connection, $sqle);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

while($row = mysqli_fetch_assoc($result)){
    $ads[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SMART Apps | Landing Page Management</title>
    
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
        
        .ad-image {
            max-width: 100px;
            height: auto;
            border-radius: 6px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
        
        .ad-url {
            font-size: 0.85rem;
            color: var(--primary);
            word-break: break-all;
        }
        
        .image-upload-container {
            border: 2px dashed #e2e8f0;
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            margin-bottom: 20px;
            background-color: #fafbfc;
            transition: var(--transition);
        }
        
        .image-upload-container:hover {
            border-color: var(--primary);
            background-color: #f8f9fa;
        }
        
        .image-preview {
            max-width: 200px;
            max-height: 200px;
            margin: 0 auto 15px;
            display: block;
            border-radius: 8px;
        }
        
        .progressBar {
            display: none;
            margin: 15px 0;
            background: #f1f1f1;
            border-radius: 20px;
            overflow: hidden;
        }
        
        .bar {
            height: 20px;
            background: var(--primary);
            width: 0%;
            transition: width 0.3s ease;
        }
        
        .percent {
            text-align: center;
            color: white;
            font-weight: 600;
            position: relative;
            top: -20px;
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
            <h1 class="page-title">Landing Page Management</h1>
            <p class="page-description">Create and manage landing pages for your applications</p>
            
            <!-- Existing Landing Pages -->
            <div class="content-card">
                <div class="content-header">
                    <h2 class="content-title">Your Landing Pages</h2>
                    <p class="content-subtitle">All your landing pages in one place</p>
                </div>
                
                <div class="content-body">
                    <div class="table-container">
                        <div class="table-header">
                            <h5 class="table-title">Landing Pages</h5>
                        </div>
                        <table class="table table-striped table-bordered table-hover dataTables-example">
                            <thead>
                                <tr>
                                    <th>OTP APP ID</th>
                                    <th>App Name</th>
                                    <th>Heading</th>
                                    <th>Charging</th>
                                    <th>Image</th>
                                    <th>Ad URL</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($ads as $ad): ?>
                                <tr class="gradeX">
                                    <td class="right"><?php echo $ad['otp_id']; ?></td>
                                    <td class="left"><?php echo htmlspecialchars($ad['app_name']); ?></td>
                                    <td class="left"><?php echo htmlspecialchars($ad['ad_heading']); ?></td>
                                    <td class="right"><?php echo htmlspecialchars($ad['ad_charging']); ?></td>
                                    <td class="center">
                                        <img src="ad/images/<?php echo $ad['ad_image']; ?>" alt="Ad Image" class="ad-image">
                                    </td>
                                    <td class="right">
                                        <a href="<?php echo $ad['adurl']; ?>?adid=<?php echo $ad['ad_id']; ?>" target="_blank" class="ad-url">
                                            <?php echo $ad['adurl']; ?>?adid=<?php echo $ad['ad_id']; ?>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Create New Landing Page -->
            <div class="content-card">
                <div class="content-header">
                    <h2 class="content-title">Create New Landing Page</h2>
                    <p class="content-subtitle">Design a new landing page for your application</p>
                </div>
                
                <div class="content-body">
                    <?php if(!empty($form_msg)): ?>   
                    <div class="alert <?php echo strpos($form_msg, 'Error') !== false ? 'alert-danger' : 'alert-success'; ?>">
                        <i class="fa <?php echo strpos($form_msg, 'Error') !== false ? 'fa-exclamation-triangle' : 'fa-check-circle'; ?>"></i>
                        <?php echo $form_msg; ?>                 
                    </div>
                    <?php endif; ?>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <!-- Image Upload Section -->
                            <div class="image-upload-container">
                                <form enctype="multipart/form-data" action="image_upload.php" method="post" name="image_upload_form" id="image_upload_form">
                                    <img src="<?php echo !empty($image_name) ? 'ad/images/' . $image_name : 'https://via.placeholder.com/200x150?text=Upload+Image'; ?>" 
                                         alt="Image Preview" class="image-preview" id="imagePreview">
                                    
                                    <div class="progressBar">
                                        <div class="bar"></div>
                                        <div class="percent">0%</div>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label class="btn btn-primary">
                                            <i class="fa fa-upload"></i> Choose Image
                                            <input type="file" accept="image/*" name="image_upload_file" id="image_upload_file" style="display: none;">
                                        </label>
                                    </div>
                                </form>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <form action="" method="post">
                                <input type="hidden" id="image_name" name="image_name" value="<?php echo $image_name; ?>">
                                
                                <div class="form-group">
                                    <label class="form-label">App Name *</label>
                                    <select name="appName" class="form-control" required>
                                        <option value="">Select App Name</option>
                                        <?php
                                        $sql_app = "SELECT * FROM otp_apps WHERE user_id = ?";
                                        $stmt = mysqli_prepare($connection, $sql_app);
                                        mysqli_stmt_bind_param($stmt, "i", $user_id);
                                        mysqli_stmt_execute($stmt);
                                        $result = mysqli_stmt_get_result($stmt);
                                        
                                        while($row_app = mysqli_fetch_array($result)):
                                        ?>
                                        <option value="<?php echo $row_app['id']; ?>" <?php echo $appName == $row_app['id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($row_app['app_name']); ?>
                                        </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                                
                                <div class="form-group">
                                    <label class="form-label">Ad Title *</label>
                                    <input type="text" name="adHeading" class="form-control" value="<?php echo htmlspecialchars($adHeading); ?>" required>
                                </div>
                                
                                <div class="form-group">
                                    <label class="form-label">Ad Charging *</label>
                                    <input type="text" name="adCharging" class="form-control" value="<?php echo htmlspecialchars($adCharging); ?>" required>
                                </div>
                                
                                <div class="form-group">
                                    <button type="submit" name="submit" value="Submit" class="btn btn-primary">
                                        <i class="fa fa-plus"></i> Create Landing Page
                                    </button>
                                </div>
                            </form>
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
    <script src="./js/jquery.form.js"></script>

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
            
            // Image upload functionality
            $('#image_upload_file').change(function() {
                var progressBar = $('.progressBar'),
                    bar = $('.progressBar .bar'),
                    percent = $('.progressBar .percent');
                
                $('#image_upload_form').ajaxForm({
                    beforeSend: function() {
                        progressBar.fadeIn();
                        var percentVal = '0%';
                        bar.width(percentVal);
                        percent.html(percentVal);
                    },
                    uploadProgress: function(event, position, total, percentComplete) {
                        var percentVal = percentComplete + '%';
                        bar.width(percentVal);
                        percent.html(percentVal);
                    },
                    success: function(html, statusText, xhr, $form) {
                        try {
                            var obj = JSON.parse(html);
                            if(obj.status) {
                                var percentVal = '100%';
                                bar.width(percentVal);
                                percent.html(percentVal);
                                $('#imagePreview').attr('src', obj.image_medium);
                                $('#image_name').val(obj.image_name);
                            } else {
                                alert(obj.error);
                            }
                        } catch(e) {
                            alert('Error parsing server response');
                        }
                    },
                    complete: function(xhr) {
                        progressBar.fadeOut();
                    },
                    error: function(xhr, status, error) {
                        alert('Upload error: ' + error);
                    }
                }).submit();
            });
        });
    </script>
</body>
</html>