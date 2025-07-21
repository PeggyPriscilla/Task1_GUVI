<?php
require '../db/config.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $session_token = $_POST['session_token'] ?? '';
    $user_id = $_POST['user_id'] ?? '';
    
    if (empty($session_token) || empty($user_id)) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid session']);
        exit;
    }
    
    // Verify session in Redis
    $session_valid = false;
    if ($redis) {
        $session_data = redis_get($session_token);
        if ($session_data && isset($session_data['user_id']) && $session_data['user_id'] == $user_id) {
            $session_valid = true;
        }
    } else {
        // Fallback: Basic session token validation when Redis is not available
        if (!empty($session_token) && strlen($session_token) > 10) {
            // Verify user exists in database
            $user_check = $mysql_conn->prepare("SELECT id FROM users WHERE id = ?");
            $user_check->bind_param("i", $user_id);
            $user_check->execute();
            $user_result = $user_check->get_result();
            if ($user_result && $user_result->num_rows > 0) {
                $session_valid = true;
            }
            $user_check->close();
        }
    }
    
    if (!$session_valid) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid or expired session']);
        exit;
    }
    
    // Get profile data from MongoDB
    if ($mongodb) {
        $profiles = mongodb_find('user_profiles', ['user_id' => intval($user_id)]);
        
        if (!empty($profiles)) {
            $profile = $profiles[0]; // Get the first match
            $profile_data = [
                'age' => $profile['age'] ?? '',
                'dob' => $profile['dob'] ?? '',
                'contact' => $profile['contact'] ?? '',
                'city' => $profile['city'] ?? '',
                'address' => $profile['address'] ?? '',
                'occupation' => $profile['occupation'] ?? '',
                'company' => $profile['company'] ?? ''
            ];
            
            echo json_encode([
                'status' => 'success', 
                'profile' => $profile_data,
                'message' => 'Profile loaded from MongoDB'
            ]);
        } else {
            echo json_encode([
                'status' => 'success', 
                'profile' => null,
                'message' => 'No profile data found'
            ]);
        }
    } else {
        // Fallback: Get profile data from MySQL
        try {
            $stmt = $mysql_conn->prepare("SELECT age, dob, contact, city, address, occupation, company FROM profiles WHERE user_id = ?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result && $result->num_rows > 0) {
                $profile = $result->fetch_assoc();
                $profile_data = [
                    'age' => $profile['age'] ?? '',
                    'dob' => $profile['dob'] ?? '',
                    'contact' => $profile['contact'] ?? '',
                    'city' => $profile['city'] ?? '',
                    'address' => $profile['address'] ?? '',
                    'occupation' => $profile['occupation'] ?? '',
                    'company' => $profile['company'] ?? ''
                ];
                
                echo json_encode([
                    'status' => 'success', 
                    'profile' => $profile_data,
                    'message' => 'Profile loaded from MySQL fallback'
                ]);
            } else {
                echo json_encode([
                    'status' => 'success', 
                    'profile' => null,
                    'message' => 'No profile data found'
                ]);
            }
            $stmt->close();
            
        } catch (Exception $e) {
            error_log('MySQL get profile error: ' . $e->getMessage());
            echo json_encode(['status' => 'error', 'message' => 'Database error']);
        }
    }
}
?> 