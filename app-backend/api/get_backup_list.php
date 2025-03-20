<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

try {
    // Get the list of backup files
    $backupFiles = glob(__DIR__ . '/../backups/waiting_list_backup*.db');
    
    // Check if we found any files
    if (empty($backupFiles)) {
        echo json_encode(['success' => false, 'message' => 'No backup files found']);
        exit;
    }
    
    // Sort the files by last modified date (most recent first)
    usort($backupFiles, function($a, $b) {
        return filemtime($b) - filemtime($a); // Compare timestamps
    });

    // Use the most recent file
    $mostRecentFile = $backupFiles[0];

    // Open the most recent SQLite file
    $db = new SQLite3($mostRecentFile);
    $results = $db->query('SELECT * FROM waiting_list ORDER BY position ASC');
    
    $waitingList = [];
    while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
        $waitingList[] = $row;
    }

    echo json_encode(['success' => true, 'data' => $waitingList]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
