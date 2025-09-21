<?php
declare(strict_types=1);

/**
 * Application Bootstrap File
 * Modernized version with improved security, organization and maintainability
 */

// Error reporting configuration
error_reporting(E_ALL ^ E_NOTICE);

// Session management
session_start();

// Timezone configuration
date_default_timezone_set('Asia/Colombo');

// Load configuration and utilities
require_once 'config/database.php';
require_once 'lib/functions.php';
require_once 'lib/subscription.php';

// Initialize user session if available
$user_name = $_SESSION['username'] ?? null;
$user_id = $_SESSION['user_id'] ?? null;

// Current date/time values
$today_full = date("Y-m-d H:i:s");
$today = date("Y-m-d");
$month = date("m");
$day = date("d");

// Application constants
define('PLATFORMS', ['', 'Ideamart', 'mSpace']);
define('APPS', ['', 'Content', 'Chatting', 'Proposal']);
define('APP_FOLDERS', ['', 'content', 'chat', 'dating']);
define('PLATFORM_FOLDERS', ['', 'ideamart/', 'mspace/']);
define('SUBSCRIPTION_I_BASE_URL', 'https://api.dialog.lk/subscription/query-base/');
define('SUBSCRIPTION_M_BASE_URL', 'https://api.mspace.lk/subscription/query-base/');

// Initialize subscription services
$isub = new Subscription(SUBSCRIPTION_I_BASE_URL);
$msub = new Subscription(SUBSCRIPTION_M_BASE_URL);

function basen($url,$appid,$pw){

 

    $arrayField = array("applicationId" => $appid,

        "password" => $pw

    );

 

    $jsonStream = json_encode($arrayField);

 

    $ch = curl_init($url);

    curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, false);

    curl_setopt($ch,CURLOPT_POST, 1);

    curl_setopt($ch,CURLOPT_HTTPHEADER, array('Content-Type: application/json'));

    curl_setopt($ch,CURLOPT_POSTFIELDS, $jsonStream);

    curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);

    $res = curl_exec($ch);

    curl_close($ch);

    //echo $res; // Debug

    return $res;

}
