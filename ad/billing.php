<?php include 'db.php';



if(!isSet($_SESSION['username']) || $_SESSION['username']=="Geust"){



	header("Location: login.php");
	exit;



}

$user_name=$_SESSION['username'];

$menu = 5;

//$sub_menu = 2;

?><!DOCTYPE html>

<html>



<head>



    <meta charset="utf-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">



    <title>AMS | Billing</title>



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
                        <th>Date</th>
                         <th>Description</th>
                         <th>App Name</th>
                         <th>Amount Billed</th>
                         <th>Amount Paid</th>
                       </tr>

                    </thead>

                    <tbody>

                    <?php 

					

					$sqle		=	"SELECT * FROM payment_ledger WHERE user_id='$user_id'";
				
					$ereply		=	mysqli_query(db_connecti(),$sqle);
					$billed = 0;
					$paid = 0;
					while($rowe 		= 	mysqli_fetch_array($ereply)){ 
 					$app_id			=	$rowe['app_id'];
 					$billed			=	$rowe['billed'];
 					$paid			=	$rowe['paid'];
 					$edate			=	$rowe['edate'];
 					$due_date		=	$rowe['due_date'];
 					$desc			=	$rowe['desc'];
 					if($billed>0){ 
						$tbilled = $tbilled+$billed;
					} 
 					 if($paid>0){ 
						$tpaid = $tpaid+$paid;
					} 
					
					$sqlu 		= 	"SELECT  * FROM app_table WHERE  id='$app_id'";
					$replyu		=	mysqli_query(db_connecti(),$sqlu) ; 
					$rowu    	= 	mysqli_fetch_array($replyu);
					$app_name 	=	$rowu['app_name'];
					$app_category =	$rowu['app_category'];
				?>

                    <tr class="gradeX">
                     
                        <td class="right"><?php echo $edate;?></td>
                        <td class="left"><?php echo $desc;?></td>
                        <td class="left"><?php echo $app_name." ".$platform[$app_category];?></td>
                        <td class="right"><?php echo number_format($billed,2);?></td>
                        <td class="right"><?php echo number_format($paid,2);?></td>
                       </tr>

                    <?php }?>

                    <tfoot>

                      <tr>
                        <th colspan="4">Balance Due</th>
                         <th class="right"><?php echo number_format($tbilled-$tpaid,2);?></th>
                       </tr>

                    </tfoot>

                    </table>



               </div>
               </div>
               <br>
               <div class="row">

        	 	<div class="ibox-content">
				<h1>Payment Details Update</h1>
                <?php
					
					$ref_no				=	$_POST['ref_no'];
					$amount				=	$_POST['amount'];
					$dep_date			=	$_POST['dep_date'];
					$submit				=	$_POST['submit'];
					if($dep_date==""){
						$dep_date = date("Y-m-d");
					}
					
					switch ($submit) {	 
						case "Submit";
						
						if( $ref_no==""){
							$form_msg=$form_msg."<br>"."Enter reference number.";		
						} 
						if( $amount==""){
							$form_msg=$form_msg."<br>"."Enter amount.";		
						} else if(!is_numeric($amount)){
							$form_msg=$form_msg."<br>"."Enter valid amount.";
						}
						if( $dep_date==""){
							$form_msg=$form_msg."<br>"."Enter date.";		
						}
						
						
						
						if ($form_msg=="" ){
							$depo_date = date("Y-m-d", strtotime($dep_date));
								
							$sql = "insert into payment_ledger ( user_id, app_id, billed, paid, edate, due_date, description ) " .
							   "values ('$user_id', '', '0', '$amount', '$depo_date', '', 'Pending')";
							mysqli_query(db_connecti(),$sql) or die (mysqli_error(db_connecti()));
							//send_email($email,base64_encode($email_token));
							//header("Location: billing.php");
							echo "<meta http-equiv=\"refresh\" content=\"2;URL=billing.php\">";
						}
						break;
						 
					}
					?>		<?php if(!empty($form_msg)){?>   
                            <div class="alert alert-danger">
                              <?php echo $form_msg;?>                 
                            </div>
                            <?php }?> 
							<form id="form" action="" method="post" class="wizard-big" >
                                <fieldset>
                                     <div class="row">

                                    	<div class="col-lg-6">

                                            <div class="form-group">

                                                <label><FONT color="#FF0000">*</FONT>Reference/Mobile Number </label>

                                                <INPUT id="ref_no" name="ref_no" type="text" class="form-control"  value="<?php echo $ref_no;?>" size="45" >

                                            </div>

                                        </div>

                                        

                                        <div class="col-lg-6">

                                            <div class="form-group">

                                                <label><FONT color="#FF0000">*</FONT>Amount</label>

                                                <INPUT id="amount" name="amount" type="text" class="form-control required"  value="<?php echo $amount;?>" maxlength="6" size="30">

                                            </div>

                                            </div>

                                             <div class="col-lg-6">

                                            <div class="form-group">

                                                <label><FONT color="#FF0000">*</FONT>Date</label>

                                                <INPUT id="dep_date" name="dep_date" type="date" class="form-control required"  value="<?php echo $dep_date;?>"  size="30">

                                            </div>

                                        </div>
                                        <div class="col-lg-6">

                                            <div class="form-group">

                                                 <label>.</label>
                                                <INPUT id="submit" name="submit" type="submit" class="btn btn-primary block full-width m-b"  value="Submit" size="45" >

                                            </div>

                                        </div>

                                    </div>

                                </fieldset>
                                </form>
                </div>
                </div>
                <br>
                <div class="row">

        	 	<div class="ibox-content">                
        		<h1>Payment Methods</h1><br>
                <div>eZCash - 0725 870 870</div>
                <br>
                <div>Bank Payments - 
                  <table>
                    <tbody>
                      <tr>
                        <td>Beneficiary Name</td>
                        <td>:</td>
                        <td>WB Nilantha</td>
                      </tr>
                      <tr>
                        <td>Account Number</td>
                        <td>:</td>
                        <td>111657334011</td>
                      </tr>
                       
                      <tr>
                        <td>Bank Name</td>
                        <td>:</td>
                        <td>SAMPATH BANK</td>
                      </tr>
                      <tr>
                        <td>Branch Name</td>
                        <td>:</td>
                        <td>TANGALLE</td>
                      </tr>
                    </tbody>
                  </table>
                </div>
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

