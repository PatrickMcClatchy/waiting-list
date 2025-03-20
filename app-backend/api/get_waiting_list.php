<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

try {
    $db = new SQLite3(__DIR__ . '/../waiting_list.db');
    $results = $db->query('SELECT * FROM waiting_list ORDER BY position ASC');
    
    $waitingList = [];
    while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
        $waitingList[] = $row;
    }

    echo json_encode(['success' => true, 'data' => $waitingList]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
