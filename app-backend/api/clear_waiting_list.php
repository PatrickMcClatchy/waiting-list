<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Set timezone to +1 (e.g., Paris)
date_default_timezone_set('Europe/Paris');

// Check if the user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

try {
    // Use __DIR__ to build absolute paths
    $dbPath = __DIR__ . '/../waiting_list.db';
    $backupDir = __DIR__ . '/../backups/';

    // Generate a unique timestamp (using microtime for extra uniqueness)
    $timestamp = date('Y-m-d_H-i-s') . '_' . str_replace('.', '', microtime(true));
    $backupFile = $backupDir . 'waiting_list_backup_' . $timestamp . '.db';

    // Ensure the backup directory exists
    if (!file_exists($backupDir)) {
        if (!mkdir($backupDir, 0777, true)) {
            throw new Exception("Failed to create backup directory: " . $backupDir);
        }
    }

    // Delete any existing backup files beyond the most recent three
    $existingBackups = glob($backupDir . 'waiting_list_backup_*.db');
    usort($existingBackups, function ($a, $b) {
        return filemtime($b) - filemtime($a); // Sort by last modified time (newest first)
    });

    // Keep only the three most recent backups
    if (count($existingBackups) > 3) {
        $backupsToDelete = array_slice($existingBackups, 3); // Get backups beyond the third most recent
        foreach ($backupsToDelete as $file) {
            if (!unlink($file)) {
                error_log("Failed to delete backup file: $file");
            }
        }
    }

    // Copy the main database file to create the new backup
    if (!copy($dbPath, $backupFile)) {
        $error = error_get_last();
        throw new Exception("Failed to create backup: " . $error['message']);
    }

    // Open the main database and clear the waiting list
    $db = new SQLite3($dbPath);
    $db->exec('DELETE FROM waiting_list');

    echo json_encode([
        'success' => true, 
        'message' => 'Waiting list cleared successfully', 
        'backup_file' => $backupFile
    ]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>