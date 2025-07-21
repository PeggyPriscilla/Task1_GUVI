<?php
require '../db/config.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if ($email && $password) {
        // Use prepared statement to get user
        $stmt = $mysql_conn->prepare("SELECT id, username, email, password FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            $user = $result->fetch_assoc();
            
            // Verify password (support both hashed and plain text for migration)
            $password_valid = false;
            if (password_verify($password, $user['password'])) {
                $password_valid = true; // Hashed password
            } elseif ($user['password'] === $password) {
                $password_valid = true; // Plain text (legacy support)
                
                // Update to hashed password for future use
                $hashed = password_hash($password, PASSWORD_DEFAULT);
                $update_stmt = $mysql_conn->prepare("UPDATE users SET password = ? WHERE id = ?");
                $update_stmt->bind_param("si", $hashed, $user['id']);
                $update_stmt->execute();
                $update_stmt->close();
            }
            
            if ($password_valid) {
                // Generate session token
                $session_token = bin2hex(random_bytes(32));
                
                // Store session in Redis if available
                if ($redis) {
                    $session_data = [
                        'user_id' => $user['id'],
                        'email' => $user['email'],
                        'username' => $user['username'],
                        'login_time' => time()
                    ];
                    redis_set($session_token, $session_data, 86400); // 24 hours expiry
                }
                
                echo json_encode([
                    'status' => 'success', 
                    'session_token' => $session_token,
                    'user' => [
                        'id' => $user['id'],
                        'username' => $user['username'],
                        'email' => $user['email']
                    ]
                ]);
                exit;
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Invalid password']);
                exit;
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'User not found']);
            exit;
        }
        $stmt->close();
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Missing email or password']);
        exit;
    }
}
?>
