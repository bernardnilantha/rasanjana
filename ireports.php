<?php 
include 'db.php';

if(!isset($_SESSION['username']) || $_SESSION['username'] == "Guest"){
    header("Location: login.php");
    exit;
}

$user_name = $_SESSION['username'];
$user_id = $_SESSION['user_id'];
$menu = 3;

// Initialize variables
$f = $_POST['from'] ?? date("Y-m-01");
$t = $_POST['to'] ?? date("Y-m-d");
$iapp_id = $_GET['app_id'] ?? '';

// Get app details
$app_name = $app_user = $app_category = $app_key = $app_database = $app_ussd = $app_id = $app_password = '';
$tr = $tu = $tttcount = $tttucount = 0;

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
    }
}

// Get report data
$report_data = [];
$tr = $tu = $tttcount = $tttucount = 0;

if(!empty($app_id)  ){
    $sql_su = "SELECT FROM_UNIXTIME(atime+(630*60),'%Y-%m-%d') AS idate, 
               COUNT(CASE WHEN active = '1' THEN active END) 'R', 
               COUNT(CASE WHEN active = '0' THEN active END) 'U'
               FROM " . $app_key . "subcribers 
               WHERE FROM_UNIXTIME(atime+(630*60),'%Y-%m-%d') 
               BETWEEN ? AND ? 
               GROUP BY FROM_UNIXTIME(atime+(630*60),'%Y-%m-%d') 
               ORDER BY idate ASC";
    
    $stmt = mysqli_prepare($connection, $sql_su);
    mysqli_stmt_bind_param($stmt, "ss", $f, $t);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    while($row_su = mysqli_fetch_assoc($result)){
        $idate = $row_su['idate'];
        $r = $row_su['R'];
        $u = $row_su['U'];
        $tr += $r;
        $tu += $u;
        
        // Get registered count
        $sql_tt = "SELECT COUNT(DISTINCT(masknumber)) AS tt 
                   FROM " . $app_key . "subcribers_pending 
                   WHERE DATE_FORMAT(FROM_UNIXTIME(atime+(10.5*60*60)), '%Y-%m-%d') = ? 
                   AND statustext = 'REGISTERED'";
        $stmt_tt = mysqli_prepare($connection, $sql_tt);
        mysqli_stmt_bind_param($stmt_tt, "s", $idate);
        mysqli_stmt_execute($stmt_tt);
        $result_tt = mysqli_stmt_get_result($stmt_tt);
        $row_tt = mysqli_fetch_assoc($result_tt);
        $ttcount = $row_tt['tt'] ?? 0;
        $tttcount += $ttcount;
        
        // Get unregistered count
        $sql_ttu = "SELECT COUNT(DISTINCT(masknumber)) AS ttu 
                    FROM " . $app_key . "subcribers_pending 
                    WHERE DATE_FORMAT(FROM_UNIXTIME(atime+(10.5*60*60)), '%Y-%m-%d') = ? 
                    AND statustext = 'UNREGISTERED'";
        $stmt_ttu = mysqli_prepare($connection, $sql_ttu);
        mysqli_stmt_bind_param($stmt_ttu, "s", $idate);
        mysqli_stmt_execute($stmt_ttu);
        $result_ttu = mysqli_stmt_get_result($stmt_ttu);
        $row_ttu = mysqli_fetch_assoc($result_ttu);
        $ttucount = $row_ttu['ttu'] ?? 0;
        $tttucount += $ttucount;
        
        $report_data[] = [
            'date' => $idate,
            'subscribed' => $r,
            'unsubscribed' => $u,
            'registered' => $ttcount,
            'unregistered' => $ttucount
        ];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SMART Apps | Dashboard Reports</title>
    
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
        
        .filter-form {
            background-color: #f8f9fa;
            border-radius: var(--border-radius);
            padding: 20px;
            margin-bottom: 20px;
        }
        
        .form-group {
            margin-bottom: 15px;
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
        
        .btn {
            margin-top: 2rem;
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
        
        .btn-info {
            background-color: var(--info);
            color: white;
        }
        
        .btn-info:hover {
            background-color: #3a7de8;
            transform: translateY(-2px);
        }
        
        .stats-card {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            padding: 20px;
            margin-bottom: 20px;
            text-align: center;
        }
        
        .stats-number {
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 5px;
        }
        
        .stats-label {
            color: #6c757d;
            font-size: 0.9rem;
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

        .filter-form .row {
            display: flex;
            flex-wrap: wrap;
            margin-right: -15px;
            margin-left: -15px;
        }
        
        .filter-form .col-md-3 {
            position: relative;
            width: 100%;
            padding-right: 15px;
            padding-left: 15px;
            flex: 0 0 25%;
            max-width: 25%;
        }
        
        /* Fix for stats cards layout */
        .stats-grid {
            display: flex;
            flex-wrap: wrap;
            margin-right: -15px;
            margin-left: -15px;
        }
        
        .stats-grid .col-md-3 {
            position: relative;
            width: 100%;
            padding-right: 15px;
            padding-left: 15px;
            flex: 0 0 25%;
            max-width: 23%;
            margin-bottom: 20px;
        }
        
        @media (max-width: 992px) {
            .filter-form .col-md-3,
            .stats-grid .col-md-3 {
                flex: 0 0 50%;
                max-width: 50%;
            }
        }
        
        @media (max-width: 768px) {
            .filter-form .col-md-3,
            .stats-grid .col-md-3 {
                flex: 0 0 100%;
                max-width: 100%;
            }
        }
        
        /* Additional fixes for form elements */
        .form-control {
            display: block;
            width: 100%;
            height: calc(1.5em + 0.75rem + 2px);
            padding: 0.375rem 0.75rem;
            font-size: 1rem;
            font-weight: 400;
            line-height: 1.5;
            color: #495057;
            background-color: #fff;
            background-clip: padding-box;
            border: 1px solid #ced4da;
            border-radius: 0.25rem;
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        }
        
         
        
        /* Ensure proper alignment in form */
        .filter-form .form-group {
            margin-bottom: 1rem;
        }
        
        .filter-form .btn {
            margin-bottom: 1rem;
            height: calc(1.5em + 0.75rem + 2px);
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
            <h1 class="page-title">Dashboard Reports</h1>
            <p class="page-description">View detailed reports for your application</p>
            
            <?php if(!empty($app_name)): ?>
            <div class="content-card">
                <div class="content-header">
                    <h2 class="content-title"><?php echo htmlspecialchars($app_name); ?></h2>
                    <p class="content-subtitle">Subscription and registration statistics</p>
                </div>
                
                <div class="content-body">
                    <!-- Date Filter Form -->
                    <div class="filter-form">
                        <form action="" method="post" class="row align-items-end">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-label">Date From</label>
                                    <input type="date" name="from" class="form-control" value="<?php echo htmlspecialchars($f); ?>">
                                </div>
                            </div>
                            
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-label">Date To</label>
                                    <input type="date" name="to" class="form-control" value="<?php echo htmlspecialchars($t); ?>">
                                </div>
                            </div>
                            
                            <div class="col-md-3">
                                <div class="form-group">
                                     
                                    <button type="submit" class="btn btn-primary mt-10">
                                        <i class="fas fa-search"></i> Filter Results
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                    
                    <!-- Stats Summary -->
                    <div class="stats-grid row">
                        <div class="col-md-3">
                            <div class="stats-card">
                                <div class="stats-number"><?php echo $tr; ?></div>
                                <div class="stats-label">Total Subscribed</div>
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="stats-card">
                                <div class="stats-number"><?php echo $tu; ?></div>
                                <div class="stats-label">Total Unsubscribed</div>
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="stats-card">
                                <div class="stats-number"><?php echo $tttcount; ?></div>
                                <div class="stats-label">Total Registered</div>
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="stats-card">
                                <div class="stats-number"><?php echo $tttucount; ?></div>
                                <div class="stats-label">Total Unregistered</div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Data Table -->
                    <div class="table-container">
                        <div class="table-header">
                            <h5 class="table-title">Daily Report Details</h5>
                        </div>
                        
                        <?php if(!empty($report_data)): ?>
                        <table class="table table-striped table-bordered table-hover dataTables-example">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Subscribed</th>
                                    <th>Unsubscribed</th>
                                    <th>Registered</th>
                                    <th>Unregistered</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($report_data as $row): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['date']); ?></td>
                                    <td class="text-right"><?php echo $row['subscribed']; ?></td>
                                    <td class="text-right"><?php echo $row['unsubscribed']; ?></td>
                                    <td class="text-right"><?php echo $row['registered']; ?></td>
                                    <td class="text-right"><?php echo $row['unregistered']; ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>Total</th>
                                    <th class="text-right"><?php echo $tr; ?></th>
                                    <th class="text-right"><?php echo $tu; ?></th>
                                    <th class="text-right"><?php echo $tttcount; ?></th>
                                    <th class="text-right"><?php echo $tttucount; ?></th>
                                </tr>
                            </tfoot>
                        </table>
                        <?php else: ?>
                        <div class="empty-state">
                            <i class="fas fa-chart-line empty-icon"></i>
                            <p>No data available for the selected date range</p>
                        </div>
                        <?php endif; ?>
                    </div>
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
            // Initialize DataTables with 50 records per page
            $('.dataTables-example').dataTable({
                responsive: true,
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