<?php 
include 'db.php';
require_once 'functions.php';

// Check if user is logged in
if (!isset($_SESSION['username']) || $_SESSION['username'] == "Guest") {
    header("Location: login.php");
    exit;
}

$menu = 3;
$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['username'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SMART Apps | Reports</title>
    
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
        
        
        /* Reports Content */
        .reports-content {
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
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background-color: white;
            border-radius: var(--border-radius);
            padding: 20px;
            box-shadow: var(--box-shadow);
            transition: var(--transition);
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }
        
        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 15px;
        }
        
        .bg-primary-light {
            background-color: rgba(67, 97, 238, 0.1);
            color: var(--primary);
        }
        
        .bg-success-light {
            background-color: rgba(76, 201, 240, 0.1);
            color: var(--success);
        }
        
        .bg-warning-light {
            background-color: rgba(247, 37, 133, 0.1);
            color: var(--warning);
        }
        
        .bg-info-light {
            background-color: rgba(72, 149, 239, 0.1);
            color: var(--info);
        }
        
        .stat-value {
            font-size: 1.75rem;
            font-weight: 600;
            margin-bottom: 5px;
        }
        
        .stat-label {
            color: #6c757d;
            font-size: 0.9rem;
        }
        
        .data-table-container {
            background-color: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            overflow: hidden;
            margin-bottom: 30px;
        }
        
        .table-header {
            padding: 20px;
            border-bottom: 1px solid #e2e8f0;
        }
        
        .table-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--dark);
        }
        
        .dataTables_wrapper {
            padding: 0 20px 20px;
        }
        
        table.dataTable thead th {
            border-bottom: 1px solid #e2e8f0;
            padding: 15px 10px;
            font-weight: 600;
            color: var(--dark);
        }
        
        table.dataTable tbody td {
            padding: 12px 10px;
            border-top: 1px solid #e2e8f0;
        }
        
        table.dataTable tbody tr:hover {
            background-color: #f8f9fa;
        }
        
        .badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 500;
        }
        
        .badge-primary {
            background-color: rgba(67, 97, 238, 0.1);
            color: var(--primary);
        }
        
        .badge-success {
            background-color: rgba(76, 201, 240, 0.1);
            color: var(--success);
        }
        
        .loading-cell {
            color: #6c757d;
            font-style: italic;
        }
        
        .report-link {
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
            transition: var(--transition);
        }
        
        .report-link:hover {
            color: var(--secondary);
            text-decoration: underline;
        }
        
        .footer {
            padding: 20px;
            background-color: white;
            border-top: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
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
            .reports-content {
                padding: 15px;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .search-box {
                display: none;
            }
            
            .footer {
                flex-direction: column;
                text-align: center;
                gap: 10px;
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
    
    <?php require_once 'nav.php';  ?>  
    <!-- Main Content -->
    <main class="main-content">
        <!-- Header -->
        <?php require_once 'head.php';  ?>  

        <!-- Reports Content -->
        <div class="reports-content">
            <h1 class="page-title">Reports</h1>
            <p class="page-description">View detailed reports and analytics for your applications</p>
            
            <!-- Stats Overview -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon bg-primary-light">
                        <i class="fas fa-mobile-alt"></i>
                    </div>
                    <div class="stat-value" id="total-apps">0</div>
                    <div class="stat-label">Total Applications</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon bg-success-light">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-value" id="total-active">0</div>
                    <div class="stat-label">Total Active Users</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon bg-warning-light">
                        <i class="fas fa-user-times"></i>
                    </div>
                    <div class="stat-value" id="total-inactive">0</div>
                    <div class="stat-label">Total Inactive Users</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon bg-info-light">
                        <i class="fas fa-database"></i>
                    </div>
                    <div class="stat-value" id="total-base"></div>
                    <div class="stat-label">Total Base Size</div>
                </div>
            </div>
            
            <!-- Applications Table -->
            <div class="data-table-container">
                <div class="table-header">
                    <h2 class="table-title">Application Reports</h2>
                </div>
                
                <table class="table table-striped table-bordered table-hover dataTables-example">
                    <thead>
                        <tr>
                             
                            <th>Platform</th>
                            <th>App Name</th>
                            <th>App USSD</th>
                            <th>Total Active</th>
                            <th>Total Inactive</th>
                            <th>Base Size</th>
                            <th>Pending</th>
                            <th>Report</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        // Get user's apps
                       
                        $sqle = "SELECT * FROM app_table WHERE user_id='$user_id' AND  active=1 "; //
                        
                        
                        $ereply = mysqli_query(db_connect(), $sqle);
                        $no_of_apps = mysqli_num_rows($ereply);
                        $i = 0;
                        $total_active = 0;
                        $total_inactive = 0;
                        $total_base = 0;
                        
                        while($rowe = mysqli_fetch_array($ereply)) {
                            $i++;
                            $app_name = $rowe['app_name'];
                            $app_user = $rowe['user_id'];
                            $app_category = $rowe['app_category'];
                            $app_key = $rowe['app_key'];
                            $app_database = $rowe['app_database'];
                            $id = $rowe['id'];
                            $app_ussd = $rowe['app_ussd'];
                            $app_id = $rowe['app_id'];
                            $app_password = $rowe['app_password'];
                            
                            $trsub = 0;
                            $tusub = 0;
                            $base = 0;
                            
                            $sql_cat = "SELECT 
                                COUNT(CASE WHEN statustext = 'REGISTERED' THEN 1 END) AS registered_count,
                                COUNT(CASE WHEN statustext = 'UNREGISTERED' THEN 1 END) AS unregistered_count,
                                COUNT(*) AS total_count
                            FROM ".$app_key."_subcribers";
                            $reply_cat = mysqli_query(db_connect(), $sql_cat);
                            $row_cat   = mysqli_fetch_array($reply_cat);
                            $trsub = $row_cat['registered_count']??0;
                            $tusub = $row_cat['unregistered_count']??0;
                            
                            $total_active += $trsub;
                            $total_inactive += $tusub;
                            $total_base += $base;
                        ?>
                        <tr class="gradeX">
                             
                            <td class="right">
                                <span class="badge badge-<?php echo $app_category == 1 ? 'primary' : 'success'; ?>">
                                    <?php echo $platform[$app_category]; ?>
                                </span>
                            </td>
                            <td class="left"><?php echo ($app_name); ?></td>
                            <td class="left"><?php echo ($app_ussd)??''; ?></td>
                            <td class="right" id="active-<?php echo $i; ?>"><?php echo $trsub; ?></td>
                            <td class="right" id="inactive-<?php echo $i; ?>"><?php echo $tusub; ?></td>
                            <td class="center" id="base-<?php echo $i; ?>">
                                <div class="loading-cell">Loading...</div>
                            </td>
                            <td class="center" id="pending-<?php echo $i; ?>">
                                <input id="app_id_<?php echo $i;?>" type="hidden" value="<?php echo $app_id;?>"/>
                                <input id="app_pw_<?php echo $i;?>" type="hidden" value="<?php echo $app_password;?>"/>
                                <input id="app_category_<?php echo $i;?>" type="hidden" value="<?php echo $app_category;?>"/>
                                <div class="loading-cell">Loading...</div>
                            </td>
                            <td class="left">
                                <a href="ireports.php?app_id=<?php echo md5($id); ?>" class="report-link">Daily</a> | 
                                <a href="mreports.php?app_id=<?php echo md5($id); ?>" class="report-link">Range</a>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                    <tfoot>
                        <tr>
                             
                            <th>Platform</th>
                            <th>App Name</th>
                            <th>App USSD</th>
                            <th>Total Active</th>
                            <th>Total Inactive</th>
                            <th>Base Size</th>
                            <th>Pending</th>
                            <th>Report</th>
                        </tr>
                    </tfoot>
                </table>
                <input id="no_of_apps" type="hidden" value="<?php echo $no_of_apps; ?>"/>
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
                "pageLength": 50, // Set default to 50 records per page
                "dom": 'T<"clear">lfrtip',
                "tableTools": {
                    "sSwfPath": "js/plugins/dataTables/swf/copy_csv_xls_pdf.swf"
                }
            });
            
            // Update stats
            $('#total-apps').text(<?php echo $no_of_apps; ?>);
            $('#total-active').text(<?php echo $total_active; ?>);
            $('#total-inactive').text(<?php echo $total_inactive; ?>);
            $('#total-base').text(<?php echo $total_base; ?>);
            
            // Toggle sidebar on mobile
            $('#toggleSidebar').click(function() {
                $('#sidebar').toggleClass('show');
            });
            
            // Toggle dropdown menus
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
            
            // User dropdown
            $('#userDropdown').click(function() {
                $('#dropdownMenu').toggleClass('show');
            });
            
            // Close dropdown when clicking outside
            $(document).click(function(event) {
                if (!$(event.target).closest('#userDropdown, #dropdownMenu').length) {
                    $('#dropdownMenu').removeClass('show');
                }
            });
            
            // Load base data
            var no_of_apps = $("#no_of_apps").val();
            if(no_of_apps > 0) {
                setTimeout(function() {
                    loadBaseData(1);
                }, 1000);
            }
        });
        
        function loadBaseData(i) {
            var no_of_apps = $("#no_of_apps").val();
            var appId = $("#app_id_"+i).val(); 
			var passwd = $("#app_pw_"+i).val();
			var category = $("#app_category_"+i).val();
            if(i > no_of_apps) return;
            
            // Simulate loading base data (replace with actual API call)
            setTimeout(function() {
                var active = parseInt($('#active-' + i).text()); 
                //var pending = base - active;
                
                
                
                // Update totals
                var currentTotalBase = parseInt($('#total-base').text());
                var currentTotalPending = parseInt($('#total-pending').text() || '0');
                
                
                $.ajax
                ({
                     beforeSend: function()
                    {
                        $("#base_"+i).html('loading...');
                    }, 
                    type: "POST",
                    url: 'base.php',
                    data: {applicationId: appId, password: passwd,app_category:category},
                    //dataType: 'json',
                    success: function(result)
                    {
                        //response(result);
						var base    = result;
						var pending = active-base;
                        //$("#base_"+i).html(base);
						//$("#panding_"+i).html(total-base);
                        $('#base-' + i).html(base);
                        $('#pending-' + i).html(pending);
                        $('#total-base').text(currentTotalBase + base);
						if(no_of_apps>i){
							
							loadBaseData(i+1);
						}
                        //window.location.href = "viewPrintAdmissionCards?ExamType=" + CourseExamType + "&CourseCode=" + CourseCode;
                    }
                });
                // Load next app
                //loadBaseData(i + 1);
            }, 500);
        }
    </script>
</body>
</html>