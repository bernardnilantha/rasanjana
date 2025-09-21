<?php
declare(strict_types=1);

/**
 * Utility Functions
 */

 

/**
 * Establish database connection
 */
function db_connect(string $database = null) {
    $db_name = $database ?? DatabaseConfig::DEFAULT_DB;
    
    $connection = mysqli_connect(
        DatabaseConfig::HOST,
        DatabaseConfig::USER,
        DatabaseConfig::PASSWORD,
        $db_name
    );
    
    if (!$connection) {
        throw new Exception('Unable to establish a DB connection: ' . mysqli_connect_error());
    }
    
    mysqli_set_charset($connection, "utf8");
    return $connection;
}

/**
 * Check if app key exists
 */
function app_key_exists(string $key): int {
    $connection = db_connect();
    $escaped_key = mysqli_real_escape_string($connection, $key);
    
    $sql = "SELECT * FROM app_table WHERE app_key = '$escaped_key'";
    $result = mysqli_query($connection, $sql);
    
    if (!$result) {
        throw new Exception('Database query error: ' . mysqli_error($connection));
    }
    
    return mysqli_num_rows($result);
}

/**
 * Check if username exists
 */
function username_exists(string $username): int {
    $connection = db_connect();
    $escaped_username = mysqli_real_escape_string($connection, $username);
    
    $sql = "SELECT * FROM user_table WHERE username = '$escaped_username'";
    $result = mysqli_query($connection, $sql);
    
    if (!$result) {
        throw new Exception('Database query error: ' . mysqli_error($connection));
    }
    
    return mysqli_num_rows($result);
}

/**
 * Sanitize input data
 */
function clean_input(string $input): string {
    $input = trim($input);
    $connection = db_connect();
    return mysqli_real_escape_string($connection, $input);
}

/**
 * Encryption function
 */
function encode_string(string $string, string $key): string {
    $key = sha1($key);
    $strLen = strlen($string);
    $keyLen = strlen($key);
    $hash = '';
    $j = 0;
    
    for ($i = 0; $i < $strLen; $i++) {
        $ordStr = ord(substr($string, $i, 1));
        if ($j == $keyLen) { 
            $j = 0; 
        }
        $ordKey = ord(substr($key, $j, 1));
        $j++;
        $hash .= strrev(base_convert(dechex($ordStr + $ordKey), 16, 36));
    }
    
    return $hash;
}

/**
 * Decryption function
 */
function decode_string(string $string, string $key): string {
    $key = sha1($key);
    $strLen = strlen($string);
    $keyLen = strlen($key);
    $hash = '';
    $j = 0;
    
    for ($i = 0; $i < $strLen; $i += 2) {
        $ordStr = hexdec(base_convert(strrev(substr($string, $i, 2)), 36, 16));
        if ($j == $keyLen) { 
            $j = 0; 
        }
        $ordKey = ord(substr($key, $j, 1));
        $j++;
        $hash .= chr($ordStr - $ordKey);
    }
    
    return $hash;
}

/**
 * Base API call function
 */
function make_base_api_call(string $url, string $appid, string $password): string {
    $payload = [
        "applicationId" => $appid,
        "password" => $password
    ];
    
    $jsonData = json_encode($payload);
    
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
        CURLOPT_POSTFIELDS => $jsonData,
        CURLOPT_RETURNTRANSFER => true
    ]);
    
    $response = curl_exec($ch);
    $error = curl_error($ch);
    curl_close($ch);
    
    if ($error) {
        throw new Exception("cURL Error: " . $error);
    }
    
    return $response;
}

/**
 * Get due payment information for a user
 */
function get_due_payment_info($user_id) {
    $connection = db_connect();
            
    if (!$connection) {
        throw new Exception("Failed to connect to database");
    }
    
    $sql = "SELECT MAX(due_date) AS last_due_date, SUM(billed - paid) AS due 
            FROM payment_ledger  
            WHERE user_id = ? AND approved = '1'";
    
    $stmt = mysqli_prepare($connection, $sql);
    if (!$stmt) {
        throw new Exception("Prepare failed: " . mysqli_error($connection));
    }
    
    $bind_result = mysqli_stmt_bind_param($stmt, "i", $user_id);
    if (!$bind_result) {
        throw new Exception("Bind param failed: " . mysqli_error($connection));
    }
    
    $execute_result = mysqli_stmt_execute($stmt);
    if (!$execute_result) {
        throw new Exception("Execute failed: " . mysqli_error($connection));
    }
    
    // Get the result set from the prepared statement
    $result = mysqli_stmt_get_result($stmt);
    if (!$result) {
        throw new Exception("Get result failed: " . mysqli_error($connection));
    }
    
    // Fetch the associative array from the result set
    $row = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
    
    $due_info = [
        'last_due_date' => $row['last_due_date'] ?? null,
        'due' => $row['due'] ?? 0,
        'days_overdue' => 0
    ];
    
    if ($due_info['last_due_date']) {
        $due_date = new DateTime($due_info['last_due_date']);
        $today = new DateTime();
        $interval = $today->diff($due_date);
        $due_info['days_overdue'] = $interval->days;
        
        // If due date is in the past, make days overdue positive
        if ($due_date < $today) {
            $due_info['days_overdue'] = $interval->days;
        }
    }
    
    return $due_info;
}

// Application platform and type arrays (should match your original definitions)
$platform = ["", "Ideamart", "mSpace"];
$apps_list = ["", "Content", "Chatting", "Proposal"];