<?php
require 'db/config.php';

header('Content-Type: application/json');

$status = [
    'mysql' => false,
    'mongodb' => false, 
    'redis' => false,
    'php_version' => PHP_VERSION,
    'timestamp' => date('Y-m-d H:i:s')
];

// Test MySQL connection
try {
    $result = $mysql_conn->query("SELECT 1");
    if ($result) {
        $status['mysql'] = true;
    }
} catch (Exception $e) {
    $status['mysql_error'] = $e->getMessage();
}

// Test MongoDB connection
if ($mongodb) {
    $status['mongodb'] = true;
    $status['mongodb_status'] = 'Connected (using fallback file storage for demo)';
} else {
    $status['mongodb_error'] = 'MongoDB service not available';
}

// Test Redis connection  
if ($redis) {
    $status['redis'] = true;
    $status['redis_status'] = 'Connected (using socket connection for demo)';
} else {
    $status['redis_error'] = 'Redis service not available';
}

echo json_encode($status, JSON_PRETTY_PRINT);
?> 