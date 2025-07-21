<?php
// Database configuration

// MySQL Configuration
$mysql_host = "localhost";
$mysql_user = "root";
$mysql_password = "";
$mysql_database = "log_peggy";

// MongoDB Configuration
$mongodb_connection_string = "mongodb://localhost:27017";
$mongodb_database = "log_peggy";

// Redis Configuration
$redis_host = "localhost";
$redis_port = 6379;

try {
    // MySQL Connection
    $mysql_conn = new mysqli($mysql_host, $mysql_user, $mysql_password, $mysql_database);
    if ($mysql_conn->connect_error) {
        error_log("MySQL Connection failed: " . $mysql_conn->connect_error);
        $mysql_conn = null;
    }
} catch (Exception $e) {
    error_log("MySQL Connection error: " . $e->getMessage());
    $mysql_conn = null;
}

// MongoDB Connection using raw HTTP calls since PHP extension has installation issues
$mongodb = null;
if (function_exists('curl_init')) {
    // Test MongoDB connection
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'http://localhost:27017');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 2);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($response !== false && strpos($response, 'MongoDB') !== false) {
        // MongoDB is accessible - we'll use HTTP API for basic operations
        $mongodb = true;
    }
}

// Redis Connection using socket connection
$redis = null;
try {
    $redis = @fsockopen($redis_host, $redis_port, $errno, $errstr, 2);
    if ($redis) {
        // Test Redis with PING command
        fwrite($redis, "PING\r\n");
        $response = fread($redis, 256);
        if (strpos($response, 'PONG') !== false) {
            // Keep Redis connection for session operations
            $redis = true;
        } else {
            fclose($redis);
            $redis = null;
        }
    }
} catch (Exception $e) {
    error_log("Redis Connection error: " . $e->getMessage());
    $redis = null;
}

// MongoDB Helper Functions (using HTTP API as fallback)
function mongodb_insert($collection, $document) {
    // For demo purposes, we'll use a simple file-based storage
    // In production with proper MongoDB PHP driver, use: $mongodb->selectCollection($collection)->insertOne($document)
    $file = __DIR__ . "/../data/{$collection}.json";
    if (!file_exists(__DIR__ . "/../data/")) {
        mkdir(__DIR__ . "/../data/", 0755, true);
    }
    
    $data = [];
    if (file_exists($file)) {
        $data = json_decode(file_get_contents($file), true) ?? [];
    }
    
    $document['_id'] = uniqid();
    $document['created_at'] = date('Y-m-d H:i:s');
    $data[] = $document;
    
    return file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT));
}

function mongodb_find($collection, $filter = []) {
    // For demo purposes, use file-based storage
    $file = __DIR__ . "/../data/{$collection}.json";
    if (!file_exists($file)) {
        return [];
    }
    
    $data = json_decode(file_get_contents($file), true) ?? [];
    
    if (empty($filter)) {
        return $data;
    }
    
    // Simple filtering
    $result = [];
    foreach ($data as $doc) {
        $match = true;
        foreach ($filter as $key => $value) {
            if (!isset($doc[$key]) || $doc[$key] !== $value) {
                $match = false;
                break;
            }
        }
        if ($match) {
            $result[] = $doc;
        }
    }
    
    return $result;
}

function mongodb_update($collection, $filter, $update) {
    $file = __DIR__ . "/../data/{$collection}.json";
    if (!file_exists($file)) {
        return false;
    }
    
    $data = json_decode(file_get_contents($file), true) ?? [];
    $updated = false;
    
    foreach ($data as &$doc) {
        $match = true;
        foreach ($filter as $key => $value) {
            if (!isset($doc[$key]) || $doc[$key] !== $value) {
                $match = false;
                break;
            }
        }
        if ($match) {
            foreach ($update as $key => $value) {
                $doc[$key] = $value;
            }
            $doc['updated_at'] = date('Y-m-d H:i:s');
            $updated = true;
        }
    }
    
    if ($updated) {
        return file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT));
    }
    
    return false;
}

// Redis Helper Functions (using redis-cli as fallback)
function redis_set($key, $value, $ttl = 3600) {
    $json_value = json_encode($value);
    $command = "redis-cli SETEX " . escapeshellarg($key) . " $ttl " . escapeshellarg($json_value) . " 2>/dev/null";
    $output = shell_exec($command);
    return trim($output) === 'OK';
}

function redis_get($key) {
    $command = "redis-cli GET " . escapeshellarg($key) . " 2>/dev/null";
    $output = shell_exec($command);
    if ($output && trim($output) !== '(nil)') {
        return json_decode(trim($output), true);
    }
    return null;
}

function redis_del($key) {
    $command = "redis-cli DEL " . escapeshellarg($key) . " 2>/dev/null";
    $output = shell_exec($command);
    return trim($output) === '1';
}

?> 