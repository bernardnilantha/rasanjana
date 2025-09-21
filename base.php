<?php include 'db.php';
$app_id = $_POST['applicationId'];
$app_password = $_POST['password'];
$app_category = $_POST['app_category'];
if($app_category==1){
	$base_url = SUBSCRIPTION_I_BASE_URL;
} else {
	$base_url = SUBSCRIPTION_M_BASE_URL;
}
$baseresponse 	= basen($base_url,$app_id,$app_password);
$basejson 		= json_decode($baseresponse, true);
if (isset($basejson['baseSize'])) {
    $base = $basejson['baseSize'];
    echo $base;
} else {
	echo "0";
}
 
?>