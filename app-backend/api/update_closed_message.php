<?php
session_start();
header('Content-Type: application/json');

// Display errors for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Log errors to a file
ini_set('log_errors', 1);
ini_set('error_log', 'error_log.txt');

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $message = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    if (!$message) {
        echo json_encode(['success' => false, 'message' => 'Invalid message.']);
        exit;
    }

    try {
        $db = new SQLite3('../waiting_list.db');
        $stmt = $db->prepare("UPDATE settings SET value = :message WHERE key = 'closed_message'");
        $stmt->bindValue(':message', $message, SQLITE3_TEXT);
        $stmt->execute();

        echo json_encode(['success' => true, 'message' => 'Closed message updated successfully.']);
    } catch (Exception $e) {
        error_log($e->getMessage());
        echo json_encode(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>