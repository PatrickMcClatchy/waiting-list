<?php
header('Content-Type: application/json');
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Use __DIR__ to get the directory of this file, then navigate to the backups folder
$backupDir = __DIR__ . '/../backups/';
// Debug: Uncomment the line below to output the backup directory path
// error_log("Backup Directory: " . $backupDir);

$backups = glob($backupDir . 'waiting_list_backup_*.db');

// Debug: Uncomment the line below to see what glob() returned
// error_log("Backups found: " . print_r($backups, true));

if ($backups && count($backups) > 0) {
    $latestBackup = max($backups);
    $lastBackupDate = date("Y-m-d H:i:s", filemtime($latestBackup));
    echo json_encode(['success' => true, 'backup_date' => $lastBackupDate]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'No backup files found in directory: ' . $backupDir,
        'backups' => $backups // This will be an empty array if nothing is found
    ]);
}
?>
