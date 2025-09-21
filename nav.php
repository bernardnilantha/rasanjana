<?php
// Navigation data
$current_menu = $menu ?? 1;
$current_sub_menu = $sub_menu ?? 0;
$user_level = $_SESSION['userlevel'] ?? 0;

 
?>
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
            <a href="dashboard.php" class="menu-item <?php if($current_menu==1){echo "active";}?>">
                <div class="menu-icon"><i class="fas fa-th-large"></i></div>
                <div class="menu-text">Dashboard</div>
            </a>
            
             
            <a href="reports.php" class="menu-item <?php if($current_menu==3){echo "active";}?>">
                <div class="menu-icon"><i class="fas fa-chart-bar"></i></div>
                <div class="menu-text">Reports</div>
            </a>
            
            
            <div class="menu-label">Content</div>
            <a href="#" class="menu-item" id="contentMenu">
                <div class="menu-icon"><i class="fas fa-file-alt "></i></div>
                <div class="menu-text">Contents</div>
                <div class="menu-icon"><i class="fas fa-chevron-down"></i></div>
            </a>
            <div class="menu-dropdown <?php if($current_menu==4){echo "show";}?>" id="contentDropdown">
                <a href="category.php" class="submenu-item <?php if($current_sub_menu==3){echo "active";}?>">
                    <i class="fas fa-tag me-2"></i> Category
                </a>
                <a href="content.php" class="submenu-item <?php if($current_sub_menu==4){echo "active";}?>">
                    <i class="fas fa-table me-2"></i> Content
                </a>
            </div>
            
             
            
            <a href="otp_apps.php" class="menu-item <?php if($current_menu==6){echo "active";}?>">
                <div class="menu-icon"><i class="fas fa-key"></i></div>
                <div class="menu-text">OTP API Key</div>
            </a>
            
            <a href="create_ad.php" class="menu-item <?php if($current_menu==7){echo "active";}?>">
                <div class="menu-icon"><i class="fas fa-ad"></i></div>
                <div class="menu-text">Create AD</div>
            </a>
            
            
            
            
            <a href="profile.php" class="menu-item <?php if($current_menu==9){echo "active";}?>">
                <div class="menu-icon"><i class="fas fa-user"></i></div>
                <div class="menu-text">Profile</div>
            </a>
            
            <a href="logout.php" class="menu-item">
                <div class="menu-icon"><i class="fas fa-sign-out-alt"></i></div>
                <div class="menu-text">Logout</div>
            </a>
        </nav>
    </aside>