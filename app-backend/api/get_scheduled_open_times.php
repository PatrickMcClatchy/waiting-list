<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

try {
    $db = new SQLite3(__DIR__ . '/../waiting_list.db');
    $stmt = $db->prepare("SELECT value FROM settings WHERE key = :key");
    $stmt->bindValue(':key', 'scheduled_open_times', SQLITE3_TEXT);
    $result = $stmt->execute();
    
    if (!$result) {
        throw new Exception('Error executing the query.');
    }
    
    $row = $result->fetchArray(SQLITE3_ASSOC);
    $scheduledOpenTimes = $row ? $row['value'] : '';

    echo json_encode(['success' => true, 'scheduled_open_times' => $scheduledOpenTimes]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>