<?php
session_start();
header('Content-Type: application/json');

try {
    // Store the hashed password securely, e.g., in an environment variable or configuration file
    $config = include('config.php'); // Adjust the path based on your structure
    $hashed_password = $config['admin_password_hash']; // Assume this is securely set in an environment variable

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $password = $_POST['password'];

        // Check if password is correct
        if (password_verify($password, $hashed_password)) {
            // Set session variables
            $_SESSION['loggedin'] = true;
            $_SESSION['LAST_ACTIVITY'] = time(); // Update last activity time
            $_SESSION['CREATED'] = time();       // Record session creation time
            
            // Regenerate session ID on successful login
            session_regenerate_id(true);

            echo json_encode(['success' => true, 'message' => 'Logged in successfully.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid password.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    }
} catch (Exception $e) {
    // Log the error to a secure server log and send a generic error message to the client
    error_log('Login error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Server error. Please try again later.']);
}
exit;
