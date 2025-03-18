<?php
session_start();
header('Content-Type: application/json');

// Define a session timeout period in seconds (e.g., 900 seconds = 15 minutes)
$session_timeout = 900;

// Check if the user is logged in
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    // Check if the session has timed out
    if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > $session_timeout)) {
        // If session has expired, unset and destroy it
        session_unset();
        session_destroy();
        echo json_encode(['loggedIn' => false, 'message' => 'Session expired. Please log in again.']);
        exit;
    } else {
        // Update last activity time if session is still valid
        $_SESSION['LAST_ACTIVITY'] = time();
        echo json_encode(['loggedIn' => true]);
    }
} else {
    // If the user is not logged in, return loggedIn as false
    echo json_encode(['loggedIn' => false, 'message' => 'Unauthorized access.']);
}
