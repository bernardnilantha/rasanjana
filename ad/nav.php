<nav class="navbar-default navbar-static-side" role="navigation">
            <div class="sidebar-collapse">
                <ul class="nav" id="side-menu">
                    <li class="nav-header">
                        <div class="dropdown profile-element"> <span>
                            <img alt="image" class="img-responsive" src="img/logo-white.png" />
                             </span>
                            <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                            <span class="clear"> <span class="block m-t-xs"> <strong class="font-bold"></strong>
                             </span> <span class="text-muted text-xs block"> <b class="caret"></b></span> </span> </a>
                            <ul class="dropdown-menu animated fadeInRight m-t-xs">
                                <li><a href="#">Profile</a></li>
                                <li class="divider"></li>
                                <li><a href="logout.php">Logout</a></li>
                            </ul>
                        </div>
                        <div class="logo-element">
                            SMART Apps
                        </div>
                    </li>
                    <li <?php if($menu==1){?>class="active"<?php }?>>
                        <a href="dashboard.php"><i class="fa fa-th-large"></i> <span class="nav-label">Dashboard</span> </a>
                        
                    </li>
                    <li <?php if($menu==2){?>class="active"<?php }?>>
                        <a href="#"><i class="fa"><img src="img/ic_apps.png" height="20"/></i> <span class="nav-label">Application</span> <span class="fa arrow"></span></a>
                        <ul class="nav nav-second-level">
                            <li <?php if($sub_menu==1){?>class="active"<?php }?>> <a href="appcreate.php"><i class="fa fa-cog"></i>Create New App</a></li>
                            <li <?php if($sub_menu==2){?>class="active"<?php }?>><a href="my_apps.php"><i class="fa fa-list"></i>My Apps</a></li>
                        </ul>
                    </li>
                    <li <?php if($menu==3){?>class="active"<?php }?>>
                        <a href="reports.php"><i class="fa"><img src="img/ic_reports.png" height="20"/></i> <span class="nav-label">Reports</span> <span class="fa arrow"></span></a>
                    </li>
                    <li <?php if($menu==4){?>class="active"<?php }?>>
                        <a href="#"><i class="fa"><img src="img/ic_contents.png" height="20"/></i> <span class="nav-label">Contents</span> <span class="fa arrow"></span></a>
                        <ul class="nav nav-second-level">
                            <li <?php if($sub_menu==3){?>class="active"<?php }?>> <a href="category.php"><i class="fa fa-tag"></i>Category</a></li>
                            <li <?php if($sub_menu==4){?>class="active"<?php }?>><a href="content.php"><i class="fa fa-table"></i>Content</a></li>
                        </ul>
                    </li>
                    <li <?php if($menu==5){?>class="active"<?php }?>>
                        <a href="billing.php"><i class="fa"><img src="img/ic_reports.png" height="20"/></i> <span class="nav-label">Billing</span> <span class="fa arrow"></span></a>
                    </li>
                </ul>

            </div>
        </nav>