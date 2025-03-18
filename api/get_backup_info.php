<?php
// Endpoint to fetch the last backup date
$backupDir = '../backups/';
$backups = glob($backupDir . 'waiting_list_backup_*.db');

if (count($backups) > 0) {
    $latestBackup = max($backups);
    $lastBackupDate = date("Y-m-d H:i:s", filemtime($latestBackup));
    echo json_encode(['success' => true, 'backup_date' => $lastBackupDate]);
} else {
    echo json_encode(['success' => false]);
}
?>
