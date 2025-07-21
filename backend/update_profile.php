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
    
    // Collect profile data
    $profile_data = [
        'user_id' => intval($user_id),
        'age' => !empty($_POST['age']) ? intval($_POST['age']) : null,
        'dob' => $_POST['dob'] ?? null,
        'contact' => $_POST['contact'] ?? null,
        'city' => $_POST['city'] ?? null,
        'address' => $_POST['address'] ?? null,
        'occupation' => $_POST['occupation'] ?? null,
        'company' => $_POST['company'] ?? null
    ];
    
    // Add timestamp
    $profile_data['updated_at'] = date('Y-m-d H:i:s');
    
    // Remove null values to keep document clean
    $profile_data = array_filter($profile_data, function($value) {
        return $value !== null && $value !== '';
    });
    
    // Always include user_id and updated_at
    $profile_data['user_id'] = intval($user_id);
    $profile_data['updated_at'] = date('Y-m-d H:i:s');
    
    // Update profile data in MongoDB
    if ($mongodb) {
        // First check if profile exists
        $existing = mongodb_find('user_profiles', ['user_id' => intval($user_id)]);
        
        if ($existing) {
            // Update existing profile
            $result = mongodb_update('user_profiles', ['user_id' => intval($user_id)], $profile_data);
            if ($result) {
                echo json_encode([
                    'status' => 'success', 
                    'message' => 'Profile updated successfully in MongoDB'
                ]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to update profile']);
            }
        } else {
            // Insert new profile
            $result = mongodb_insert('user_profiles', $profile_data);
            if ($result) {
                echo json_encode([
                    'status' => 'success', 
                    'message' => 'Profile created successfully in MongoDB'
                ]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to create profile']);
            }
        }
    } else {
        // Fallback: Store profile data in MySQL
        try {
            // Create profiles table if it doesn't exist
            $create_table = "CREATE TABLE IF NOT EXISTS profiles (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                age INT,
                dob DATE,
                contact VARCHAR(20),
                city VARCHAR(100),
                address TEXT,
                occupation VARCHAR(100),
                company VARCHAR(100),
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                UNIQUE KEY unique_user_profile (user_id)
            )";
            $mysql_conn->query($create_table);
            
            // Insert or update profile data
            $stmt = $mysql_conn->prepare("
                INSERT INTO profiles (user_id, age, dob, contact, city, address, occupation, company)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE
                age = VALUES(age), dob = VALUES(dob), contact = VALUES(contact),
                city = VALUES(city), address = VALUES(address), occupation = VALUES(occupation),
                company = VALUES(company), updated_at = CURRENT_TIMESTAMP
            ");
            
            $dob = !empty($profile_data['dob']) ? $profile_data['dob'] : null;
            $age = !empty($profile_data['age']) ? $profile_data['age'] : null;
            
            $stmt->bind_param("iissssss", 
                $user_id, 
                $age,
                $dob,
                $profile_data['contact'],
                $profile_data['city'],
                $profile_data['address'],
                $profile_data['occupation'],
                $profile_data['company']
            );
            
            if ($stmt->execute()) {
                echo json_encode([
                    'status' => 'success', 
                    'message' => 'Profile updated successfully (stored in MySQL as fallback)'
                ]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to update profile']);
            }
            $stmt->close();
            
        } catch (Exception $e) {
            error_log('MySQL profile update error: ' . $e->getMessage());
            echo json_encode(['status' => 'error', 'message' => 'Database error occurred']);
        }
    }
}
?> 