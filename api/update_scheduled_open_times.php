<?php
session_start();
header('Content-Type: application/json');

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Check if the user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize input
    $scheduledOpenTimes = filter_input(INPUT_POST, 'scheduled_open_times', FILTER_SANITIZE_STRING);

    if (!$scheduledOpenTimes) {
        echo json_encode(['success' => false, 'message' => 'Invalid or missing scheduled open times.']);
        exit;
    }

    try {
        // Connect to the SQLite database
        $db = new SQLite3('../waiting_list.db');
        if (!$db) {
            throw new Exception('Failed to connect to the database');
        }

        // Prepare the SQL query to update the setting
        $stmt = $db->prepare("UPDATE settings SET value = :value WHERE key = 'scheduled_open_times'");
        if (!$stmt) {
            throw new Exception('Failed to prepare the SQL statement');
        }

        // Bind the parameter and execute the statement
        $stmt->bindValue(':value', $scheduledOpenTimes, SQLITE3_TEXT);
        if (!$stmt->execute()) {
            throw new Exception('Failed to execute the SQL statement');
        }

        // Return success response
        echo json_encode(['success' => true, 'message' => 'Scheduled open times updated successfully.']);
    } catch (Exception $e) {
        // Log error details and return a failure response
        error_log('Error: ' . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()]);
    } finally {
        // Ensure the database connection is closed
        if (isset($db)) {
            $db->close();
        }
    }
} else {
    // Handle invalid request method
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>
