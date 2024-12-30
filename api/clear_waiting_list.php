<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

try {
    $db = new SQLite3('../waiting_list.db');
    $db->exec('DELETE FROM waiting_list');

    echo json_encode(['success' => true, 'message' => 'Waiting list cleared successfully']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}