<?php
header('Content-Type: application/json');

// Display errors for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

try {
    $db = new SQLite3(__DIR__ .'/../waiting_list.db');
    if (!$db) {
        throw new Exception('Failed to open database: ' . $db->lastErrorMsg());
    }

    $stmt = $db->prepare("SELECT value FROM settings WHERE key = :key");
    if (!$stmt) {
        throw new Exception('Failed to prepare statement: ' . $db->lastErrorMsg());
    }

    $stmt->bindValue(':key', 'closed_message', SQLITE3_TEXT);
    $result = $stmt->execute();
    if (!$result) {
        throw new Exception('Error executing the query: ' . $db->lastErrorMsg());
    }
    
    $row = $result->fetchArray(SQLITE3_ASSOC);
    if (!$row) {
        throw new Exception('No row found for key "closed_message"');
    }

    $message = $row['value'] ? $row['value'] : 'The waiting list is currently closed.';

    echo json_encode(['success' => true, 'message' => $message]);
} catch (Exception $e) {
    error_log($e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>