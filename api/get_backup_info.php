<?php
date_default_timezone_set('Europe/Paris'); // UTC+1 (or adjust as needed)
header('Content-Type: application/json');
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Use __DIR__ to get the directory of this file, then navigate to the backups folder
$backupDir = __DIR__ . '/../backups/';

$backups = glob($backupDir . 'waiting_list_backup_*.db');

if ($backups && count($backups) > 0) {
    $latestBackup = max($backups);
    $lastBackupDate = date("Y-m-d H:i:s", filemtime($latestBackup));
    echo json_encode(['success' => true, 'backup_date' => $lastBackupDate]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'No backup files found in directory: ' . $backupDir,
        'backups' => $backups
    ]);
}
?>
