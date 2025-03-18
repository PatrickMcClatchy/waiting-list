<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

try {
    // Define the main database and backup directory
    $dbPath = '../waiting_list.db';
    $backupDir = '../backups/';
    
    // Step 1: Generate a timestamped backup filename
    $timestamp = date('Y-m-d_H-i-s');
    $backupFile = $backupDir . 'waiting_list_backup_' . $timestamp . '.db'; // Backup with timestamp

    // Step 2: Check if the backup directory exists, create it if not
    if (!file_exists($backupDir)) {
        mkdir($backupDir, 0777, true); // Create backup directory if it doesn't exist
    }

    // Step 3: Delete the previous backup (if exists)
    // Get the list of backup files and delete the oldest one
    $existingBackups = glob($backupDir . 'waiting_list_backup_*.db');
    foreach ($existingBackups as $backup) {
        unlink($backup); // Delete the existing backup
    }

    // Step 4: Copy the current database to the new backup file
    if (!copy($dbPath, $backupFile)) {
        throw new Exception('Failed to create backup.');
    }

    // Step 5: Open the database and clear the waiting list
    $db = new SQLite3($dbPath);
    $db->exec('DELETE FROM waiting_list');

    // Step 6: Send the response
    echo json_encode(['success' => true, 'message' => 'Waiting list cleared successfully', 'backup_file' => $backupFile]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>
