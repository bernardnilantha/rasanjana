<?php 
require_once 'db.php';
require_once 'functions.php';

// Check if user is logged in
if (!isset($_SESSION['username']) || $_SESSION['username'] === "Guest") {
    header("Location: login.php");
    exit;
}

$menu = 1;
$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['username'];
$connection = db_connect();
            
if (!$connection) {
    throw new Exception("Failed to connect to database");
}
// Get user's apps
$apps = [];
$sql = "SELECT * FROM app_table WHERE user_id = ? AND active = 1";
$stmt = mysqli_prepare($connection, $sql);

mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$icount = $mcount = 0;
while ($row = mysqli_fetch_assoc($result)) {
    $apps[] = $row;
    $app_key      = $row['app_key'];
    $app_category = $row['app_category'];
    $sql_cat = "SELECT COUNT(rowid) AS sub_count FROM ".$app_key."_subcribers  
                WHERE ".$app_key."_subcribers.statustext = 'REGISTERED'";
    $reply_cat = mysqli_query(db_connect(), $sql_cat);
    $row_cat   = mysqli_fetch_array($reply_cat);
    $sub_count = $row_cat['sub_count']??0;
    if($app_category==1){$icount = $icount+$sub_count; }
    if($app_category==2){$mcount = $mcount+$sub_count; }
    
}


// Check for due payments
$due_info = get_due_payment_info($user_id);
if ($due_info['due'] > 0 && $menu != 5 && $due_info['days_overdue'] > 0) {
    header("Location: billing.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SMART Apps | Dashboard</title>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <link href="css/theme.css" rel="stylesheet">
</head>

<body>
    <!-- Sidebar Navigation -->
      <?php require_once 'nav.php';  ?>  

    <!-- Main Content -->
    <main class="main-content">
        <!-- Header -->
        <?php require_once 'head.php';  ?>  

        <!-- Dashboard Content -->
        <div class="dashboard-content">
            <h1 class="page-title">Dashboard</h1>
            <p class="page-description">Welcome back, <?php echo $user_name; ?>. Here's what's happening with your apps today.</p>
            
            <!-- Stats Overview -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon bg-primary-light">
                        <i class="fas fa-mobile-alt"></i>
                    </div>
                    <div class="stat-value"><?php echo count($apps);?></div>
                    <div class="stat-label">Active Applications</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon bg-success-light">
                        <i class="fas fa-mobile-alt"></i>
                    </div>
                    <div class="stat-value"><?php echo number_format($icount,0);?></div>
                    <div class="stat-label">Ideamart Registrations</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon bg-warning-light">
                        <i class="fas fa-mobile-alt"></i>
                    </div>
                    <div class="stat-value"><?php echo number_format($mcount,0);?></div>
                    <div class="stat-label">mSpace Registrations</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon bg-info-light">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-value"><?php echo number_format($icount+$mcount,0);?></div>
                    <div class="stat-label">Total Registrations</div>
                </div>
            </div>
            
            <!-- Applications Section -->
            <h2 class="section-title">My Applications</h2>
            
            <?php if (count($apps) > 0): ?>
            <div class="apps-grid">
                <?php foreach ($apps as $app): ?>
                <div class="app-card">
                    <div class="app-header">
                        <span class="app-badge <?php echo $app['app_category'] == 1 ? 'badge-primary' : 'badge-success'; ?>">
                            <?php echo $platform[$app['app_category']]; ?>
                        </span>
                        
                        <div class="app-icon <?php echo $app['app_category'] == 1 ? 'bg-primary' : 'bg-success'; ?>">
                            <i class="fas fa-mobile-alt"></i>
                        </div>
                        
                        <h3 class="app-title"><?php echo htmlspecialchars($app['app_name']); ?></h3>
                        <p class="app-description"><?php echo $apps_list[$app['app_sub_category']]; ?></p>
                    </div>
                    
                    <div class="app-stats">
                        <div class="app-stat">
                            <div class="stat-number"><?php echo $app['app_sms']; ?></div>
                            <div class="stat-name">SMS REG</div>
                        </div>
                        
                        <div class="app-stat">
                            <div class="stat-number"><?php echo $app['app_ussd']; ?></div>
                            <div class="stat-name">USSD</div>
                        </div>
                    </div>
                    
                    <div class="app-actions">
                        <a href="editapp.php?appid=<?php echo encode_string($app['id'], $user_id); ?>" class="app-action">
                            <i class="fas fa-edit me-1"></i> Edit App
                        </a>
                        <a href="#" class="app-action">
                            <i class="fas fa-chart-bar me-1"></i> View Stats
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <div class="empty-state">
                <div class="empty-icon">
                    <i class="fas fa-cubes"></i>
                </div>
                <h3 class="empty-title">No Applications Yet</h3>
                <p class="empty-description">You haven't created any applications yet. Get started by creating your first app to manage your services.</p>
                <a href="appcreate.php" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i> Create Your First App
                </a>
            </div>
            <?php endif; ?>
        </div>
    </main>

    <script>
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
    </script>
</body>
</html>