<?php
session_start();
header('Content-Type: application/json');

try {
    // Hardcoded hashed password (replace with your actual hash)
    $hashed_password = '$2y$06$So/CYnud39L2W43MPmSTcOr8WosL1drKI9B2ktEKk.J0gkXO52FFK';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $password = $_POST['password'];

        // Check password
        if (password_verify($password, $hashed_password)) {
            $_SESSION['loggedin'] = true;
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid password.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    }
} catch (Exception $e) {
    // In case of any errors, send a JSON error response
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}
exit;
