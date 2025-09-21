<?php 
include 'db.php';

if(!isset($_SESSION['username']) || $_SESSION['username'] == "Guest"){
    header("Location: login.php");
    exit;
}

$user_name = $_SESSION['username'];
$user_id = $_SESSION['user_id'];
$menu = 3;

// Get app details
$iapp_id = $_GET['app_id'] ?? '';
$app_name = $app_user = $app_category = $app_key = $app_database = $app_ussd = $app_id = $app_password = '';
$has_last_used = false;

if(!empty($iapp_id)){
    $connection = db_connect();
    if (!$connection) {
        throw new Exception("Failed to connect to database");
    }
    $sqle = "SELECT * FROM app_table WHERE MD5(id) = ?";
    $stmt = mysqli_prepare($connection, $sqle);
    mysqli_stmt_bind_param($stmt, "s", $iapp_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if($rowe = mysqli_fetch_assoc($result)){
        $app_name = $rowe['app_name'];
        $app_user = $rowe['user_id'];
        $app_category = $rowe['app_category'];
        $app_key = $rowe['app_key'];
        $app_database = $rowe['app_database'];
        $id = $rowe['id'];
        $app_ussd = $rowe['app_ussd'];
        $app_id = $rowe['app_id'];
        $app_password = $rowe['app_password'];
        
        if(!empty($app_key)){
            $app_key = $app_key . "_";
        }
        
        // Check if last_used column exists
        $sql_c = "SHOW COLUMNS FROM " . $app_key . "subcribers LIKE 'last_used'";
        $stmt_c = mysqli_prepare($connection, $sql_c);
        mysqli_stmt_execute($stmt_c);
        $result_c = mysqli_stmt_get_result($stmt_c);
        $has_last_used = (mysqli_num_rows($result_c) > 0);
    }
}

// Get activated users data
$activated_data = [];
if(!empty($app_id)  && $has_last_used){
    $sql_act = "SELECT CONCAT(21*FLOOR(a.diff/21), '-', 21*FLOOR(a.diff/21) + 20) AS `range`,
                SUM(a.no_of_users) AS `num_users` 
                FROM (SELECT TIMESTAMPDIFF(DAY, FROM_UNIXTIME(atime, '%Y-%m-%d'), CURDATE()) AS diff, 
                             COUNT(*) AS no_of_users
                      FROM " . $app_key . "subcribers 
                      WHERE active = 1   
                      GROUP BY diff) a
                GROUP BY 1 ORDER BY a.diff";
    
    $stmt_act = mysqli_prepare($connection, $sql_act);
    mysqli_stmt_execute($stmt_act);
    $result_act = mysqli_stmt_get_result($stmt_act);
    
    while($row_act = mysqli_fetch_assoc($result_act)){
        $activated_data[] = [
            'range' => $row_act['range'],
            'count' => $row_act['num_users']
        ];
    }
}

// Get last used data
$last_used_data = [];
if(!empty($app_id) &&     $has_last_used){
    $sql_su = "SELECT CONCAT(21*FLOOR(a.diff/21), '-', 21*FLOOR(a.diff/21) + 20) AS `range`,
               SUM(a.no_of_users) AS `num_users` 
               FROM (SELECT TIMESTAMPDIFF(DAY, FROM_UNIXTIME(last_used, '%Y-%m-%d'), CURDATE()) AS diff, 
                            COUNT(*) AS no_of_users
                     FROM " . $app_key . "subcribers 
                     WHERE active = 1 AND last_used > 0
                     GROUP BY diff) a
               GROUP BY 1 ORDER BY a.diff";
    
    $stmt_su = mysqli_prepare($connection, $sql_su);
    mysqli_stmt_execute($stmt_su);
    $result_su = mysqli_stmt_get_result($stmt_su);
    
    while($row_su = mysqli_fetch_assoc($result_su)){
        $last_used_data[] = [
            'range' => $row_su['range'],
            'count' => $row_su['num_users']
        ];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SMART Apps | User Activity Report</title>
    
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
        
        .table-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            margin-bottom: 20px;
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
        
        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .alert-info {
            background-color: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
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
            <h1 class="page-title">User Activity Report</h1>
            <p class="page-description">Analyze user activation and usage patterns</p>
            
            <?php if(!empty($app_name)): ?>
            <div class="content-card">
                <div class="content-header">
                    <h2 class="content-title"><?php echo htmlspecialchars($app_name); ?></h2>
                    <p class="content-subtitle">User activity analysis</p>
                </div>
                
                <div class="content-body">
                    <?php if($has_last_used): ?>
                    <div class="row">
                        <!-- Activated Users Table -->
                        <div class="col-lg-6">
                            <div class="table-container">
                                <div class="table-header">
                                    <h5 class="table-title">Activated Users (Days before)</h5>
                                </div>
                                <table class="table table-striped table-bordered table-hover dataTables-example">
                                    <thead>
                                        <tr>
                                            <th>Days Range</th>
                                            <th>User Count</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($activated_data as $row): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($row['range']); ?> days</td>
                                            <td class="text-right"><?php echo $row['count']; ?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        
                        <!-- Last Used Table -->
                        <div class="col-lg-6">
                            <div class="table-container">
                                <div class="table-header">
                                    <h5 class="table-title">Last Used (Days before)</h5>
                                </div>
                                <table class="table table-striped table-bordered table-hover dataTables-example">
                                    <thead>
                                        <tr>
                                            <th>Days Range</th>
                                            <th>User Count</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($last_used_data as $row): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($row['range']); ?> days</td>
                                            <td class="text-right"><?php echo $row['count']; ?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <?php else: ?>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> Contact us to activate the user activity tracking feature.
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php else: ?>
            <div class="content-card">
                <div class="content-header">
                    <h2 class="content-title">Application Not Found</h2>
                    <p class="content-subtitle">Please select a valid application</p>
                </div>
                
                <div class="content-body">
                    <div class="empty-state">
                        <i class="fas fa-exclamation-circle empty-icon"></i>
                        <p>The requested application could not be found or you don't have access to it.</p>
                        <a href="dashboard.php" class="btn btn-primary mt-3">
                            <i class="fas fa-arrow-left"></i> Back to Dashboard
                        </a>
                    </div>
                </div>
            </div>
            <?php endif; ?>
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
                ordering: false,
                "dom": 'T<"clear">lfrtip',
                "pageLength": 50,
                "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
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