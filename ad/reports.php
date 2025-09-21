<?php include 'db.php';
if(!isSet($_SESSION['username']) || $_SESSION['username']=="Geust"){
	header("Location: login.php");
	exit;
}
$user_name=$_SESSION['username2'];
$menu = 3;
//$sub_menu = 2;
?><!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AMS | Dashboard</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="font-awesome/css/font-awesome.css" rel="stylesheet">
    <!-- Data Tables -->
    <link href="css/plugins/dataTables/dataTables.bootstrap.css" rel="stylesheet">
    <link href="css/plugins/dataTables/dataTables.responsive.css" rel="stylesheet">
    <link href="css/plugins/dataTables/dataTables.tableTools.min.css" rel="stylesheet">
    <link href="css/animate.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>
<body>
    <div id="wrapper">
        <?php include_once("nav.php");?>
        <div id="page-wrapper" class="gray-bg dashbard-1">
        <div class="row border-bottom">
        <nav class="navbar navbar-static-top" role="navigation" style="margin-bottom: 0">
        <div class="navbar-header">
            <a class="navbar-minimalize minimalize-styl-2 btn btn-primary " href="#"><i class="fa fa-bars"></i> </a>
            <form role="search" class="navbar-form-custom" method="post" action="search_results.html">
                <div class="form-group">
                    <input type="text" placeholder="Search for something..." class="form-control" name="top-search" id="top-search">
                </div>
            </form>
        </div>
            <ul class="nav navbar-top-links navbar-right">
                <li>
                    <span class="m-r-sm text-muted welcome-message">Welcome. <?php echo $user_name;?></span>
                </li>
                 
                
                <li>
                    <a href="login.php">
                        <i class="fa fa-sign-out"></i> Log out
                    </a>
                </li>
            </ul>
        </nav>
        </div>
                <div class="row  border-bottom white-bg dashboard-header"></div>
        <div class="row">
            <div class="col-lg-12">
                <div class="wrapper wrapper-content">
        <div class="row">
        	 <div class="ibox-content">
        		<table class="table table-striped table-bordered table-hover dataTables-example" >
                    <thead>
                    <tr>
                    	<?php if($user_id==38){?>
                      <th>User Name</th>
                      <?php }?>
                        <th>Plarform</th>
                        <th>App Name</th>
                        <th>App USSD</th>
                        <th>Total Active</th>
                        <th>Total Deact</th>
                        <th>Base Size</th>
                        <th>Total Pending</th>
                        <th>Report</th>
                      </tr>
                    </thead>
                    <tbody>
                    <?php 
					
					/*if($user_id==38){
						$sqle		=	"SELECT * FROM app_table WHERE user_id IN (38,39,347) AND active=1 ORDER BY user_id";

					}  else {*/
						$sqle		=	"SELECT * FROM app_table WHERE user_id='$user_id' AND  active=1";
					//}
					
					$ereply		=	mysqli_query(db_connecti(),$sqle);
					$no_of_apps = mysqli_num_rows($ereply); 
					$i=0;
					while($rowe 		= 	mysqli_fetch_array($ereply)){
					$i=$i+1;	
 					$app_name			=	$rowe['app_name'];
					
					$app_user			=	$rowe['user_id'];
					$app_category		=	$rowe['app_category'];
					$app_key			=	$rowe['app_key'];
					$app_database		=	$rowe['app_database'];
					$id					=	$rowe['id'];
					$app_ussd				=	$rowe['app_ussd'];
					
					$app_id				=	$rowe['app_id'];
					$app_password		=	$rowe['app_password'];
					 $trsub = 0;
					  $tusub = 0;
					  
					  $base  = 0;
					if($app_id!="" && $app_key!=""){
					
					 $sqlt		 = "SELECT active, COUNT(rowid) AS t 
						FROM ".$app_key."_subcribers 
						GROUP BY active" ;
					  $replyt	 = mysqli_query(db_connecti_new($app_database),$sqlt) or die (mysqli_error(db_connecti_new($app_database)));
//echo db_connect();
					  $num_rowsc = mysqli_num_rows($replyt); 
					 
					  while($rowt = mysqli_fetch_array($replyt)){
						$tcount			=	$rowt['t'];
						$tstatus		=	$rowt['active'];
                        if($tstatus=="0"){
							$tusub = $tcount;
						} else if($tstatus=="1"){
							$trsub = $tcount;
						} 
					  }
					  	if($app_category==1){
							$base_url = SUBSCRIPTION_I_BASE_URL;
						} else {
							$base_url = SUBSCRIPTION_M_BASE_URL;
						}
					  	//$baseresponse 	= basen($base_url,$app_id,$app_password);
						//$basejson 		= json_decode($baseresponse, true);
						//$base 			= $basejson['baseSize'];
					} else  if($app_key==""){
					
					    $sqlt		 = "SELECT active, COUNT(rowid) AS t 
						FROM  subcribers 
						GROUP BY active" ;
					  $replyt	 = mysqli_query(db_connecti_new($app_database),$sqlt) or die (mysqli_error(db_connecti_new($app_database)));
//echo db_connect();
					  $num_rowsc = mysqli_num_rows($replyt); 
					 
					  while($rowt = mysqli_fetch_array($replyt)){
						$tcount			=	$rowt['t'];
						$tstatus		=	$rowt['active'];
                        if($tstatus=="0"){
							$tusub = $tcount;
						} else if($tstatus=="1"){
							$trsub = $tcount;
						} 
					  }
					  if($app_category==1){
							$base_url = SUBSCRIPTION_I_BASE_URL;
						} else {
							$base_url = SUBSCRIPTION_M_BASE_URL;
						}
					  	//$baseresponse 	= basen($base_url,$app_id,$app_password);
						//$basejson 		= json_decode($baseresponse, true);
						//$base 			= $basejson['baseSize'];
					}
					
					$sqlu 	= 	"SELECT  * FROM user_table WHERE  user_id='$app_user'";
					$replyu	=	mysqli_query(db_connecti(),$sqlu) ; 
					$rowu    = mysqli_fetch_array($replyu);
				?>
                    <tr class="gradeX">
                    <?php if($user_id==38){?>
                      <td class="right"><?php echo $rowu['username'];?></td>
					<?php }?>
                        <td class="right"><?php echo $platform[$app_category];?></td>
                        <td class="left"><?php echo $app_name;?></td>
                        <td class="left"><?php echo $app_ussd;?></td>
                        <td class="right"><?php echo $trsub;?></td>
                        <td><?php echo $tusub;?></td>
                        <td class="center"><input id="total_<?php echo $i;?>" type="hidden" value="<?php echo $trsub;?>"/>
                        	<input id="app_id_<?php echo $i;?>" type="hidden" value="<?php echo $app_id;?>"/>
                            <input id="app_pw_<?php echo $i;?>" type="hidden" value="<?php echo $app_password;?>"/>
                            <input id="app_category_<?php echo $i;?>" type="hidden" value="<?php echo $app_category;?>"/>
							<div id="base_<?php echo $i;?>"></div></td>
                        <td class="left"><div id="panding_<?php echo $i;?>"></div></td>
                        <td class="left"><a href="ireports.php?app_id=<?php echo md5($id);?>" > Daily</a> | <a href="mreports.php?app_id=<?php echo md5($id);?>" > Range</a></td>
                      </tr>
                    <?php }?>
                    <tfoot>
                      <tr>
                      <?php if($user_id==38){?>
                        <th>User Name</th>
					  <?php }?>
                        <th>Plarform</th>
                        <th>App Name</th>
                        <th>App USSD</th>
                        <th>Total Active</th>
                        <th>Total Deact</th>
                        <th>Base Size</th>
                        <th>Total Pending</th>
                        <th>Report</th>
                      </tr>
                    </tfoot>
                    </table>
                    <input id="no_of_apps" type="hidden" value="<?php echo $no_of_apps;?>"/>
               </div>
        		
                   
            </div>
        </div>
              </div>
                <div class="footer">
                    <div class="pull-right">
                        Create App <strong>Free.</strong> 
                    </div>
                    <div>
                        <strong>Copyright</strong> SMART Apps &copy; 2020
                    </div>
                </div>
            </div>
        </div>
        </div>
    </div>
    <!-- Mainly scripts -->
    <script src="js/jquery-2.1.1.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/plugins/metisMenu/jquery.metisMenu.js"></script>
    <script src="js/plugins/slimscroll/jquery.slimscroll.min.js"></script>
    <script src="js/plugins/jeditable/jquery.jeditable.js"></script>
    <!-- Data Tables -->
    <script src="js/plugins/dataTables/jquery.dataTables.js"></script>
    <script src="js/plugins/dataTables/dataTables.bootstrap.js"></script>
    <script src="js/plugins/dataTables/dataTables.responsive.js"></script>
    <script src="js/plugins/dataTables/dataTables.tableTools.min.js"></script>
    <!-- Custom and plugin javascript -->
    <script src="js/inspinia.js"></script>
    <script src="js/plugins/pace/pace.min.js"></script>
    <!-- Page-Level Scripts -->
    <script>
        $(document).ready(function() {
            $('.dataTables-example').dataTable({
                responsive: true,
                "dom": 'T<"clear">lfrtip',
                "tableTools": {
                    "sSwfPath": "js/plugins/dataTables/swf/copy_csv_xls_pdf.swf"
                }
            });
            /* Init DataTables */
            var oTable = $('#editable').dataTable();
            /* Apply the jEditable handlers to the table */
            oTable.$('td').editable( '../example_ajax.php', {
                "callback": function( sValue, y ) {
                    var aPos = oTable.fnGetPosition( this );
                    oTable.fnUpdate( sValue, aPos[0], aPos[1] );
                },
                "submitdata": function ( value, settings ) {
                    return {
                        "row_id": this.parentNode.getAttribute('id'),
                        "column": oTable.fnGetPosition( this )[2]
                    };
                },
                "width": "90%",
                "height": "100%"
            } );
        });
        function fnClickAddRow() {
            $('#editable').dataTable().fnAddData( [
                "Custom row",
                "New row",
                "New row",
                "New row",
                "New row" ] );
        }
		$(document).ready(function() {
            
			var no_of_apps = $("#no_of_apps").val();
				if(no_of_apps>0){
				setTimeout(function(){
				   loadeBase(1);
				 },1000);
			}
				
         });
        function loadeBase(i){
			var appId = $("#app_id_"+i).val();
			var total = $("#total_"+i).val();
			var passwd = $("#app_pw_"+i).val();
			var category = $("#app_category_"+i).val();
			var no_of_apps = $("#no_of_apps").val();
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
						//var panding = total-base;
                        $("#base_"+i).html(base);
						$("#panding_"+i).html(total-base);
						if(no_of_apps>i){
							
							loadeBase(i+1);
						}
                        //window.location.href = "viewPrintAdmissionCards?ExamType=" + CourseExamType + "&CourseCode=" + CourseCode;
                    }
                });
		}
    </script>
<style>
    body.DTTT_Print {
        background: #fff;
    }
    .DTTT_Print #page-wrapper {
        margin: 0;
        background:#fff;
    }
    button.DTTT_button, div.DTTT_button, a.DTTT_button {
        border: 1px solid #e7eaec;
        background: #fff;
        color: #676a6c;
        box-shadow: none;
        padding: 6px 8px;
    }
    button.DTTT_button:hover, div.DTTT_button:hover, a.DTTT_button:hover {
        border: 1px solid #d2d2d2;
        background: #fff;
        color: #676a6c;
        box-shadow: none;
        padding: 6px 8px;
    }
    .dataTables_filter label {
        margin-right: 5px;
    }
</style>
    
</body>
</html>
