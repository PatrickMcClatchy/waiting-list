<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

try {
    $db = new SQLite3('../waiting_list.db');
    $results = $db->query('SELECT * FROM waiting_list ORDER BY position ASC');

    $exportData = [];
    while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
        $exportData[] = $row;
    }

    echo json_encode(['success' => true, 'data' => $exportData]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
