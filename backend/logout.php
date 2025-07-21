<?php
require '../db/config.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $session_token = $_POST['session_token'] ?? '';
    
    if (!empty($session_token) && $redis) {
        // Delete session from Redis
        $result = redis_del($session_token);
        if ($result) {
            echo json_encode(['status' => 'success', 'message' => 'Logged out successfully']);
        } else {
            echo json_encode(['status' => 'success', 'message' => 'Logged out (session may have expired)']);
        }
    } else {
        echo json_encode(['status' => 'success', 'message' => 'Logged out']);
    }
}
?> 