<?php
try {
    // Create or open the SQLite database
    $db = new SQLite3('waiting_list.db');

    // Check if the database opened successfully
    if (!$db) {
        echo "Failed to open database: " . $db->lastErrorMsg();
        exit;
    }

    // Create the waiting_list table if it doesn't exist
    $createWaitingListTable = $db->exec("CREATE TABLE IF NOT EXISTS waiting_list (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        email_or_phone TEXT,
        comment TEXT,
        language TEXT,
        time INTEGER NOT NULL,
        confirmed INTEGER DEFAULT 1,
        position INTEGER NOT NULL
    )");

    if (!$createWaitingListTable) {
        echo "Error creating waiting_list table: " . $db->lastErrorMsg();
    } else {
        echo "Waiting list table created or already exists.<br>";
    }

    // Create the settings table to store days and times
    $createSettingsTable = $db->exec("CREATE TABLE IF NOT EXISTS settings (
        key TEXT PRIMARY KEY,
        value TEXT NOT NULL
    )");

    if (!$createSettingsTable) {
        echo "Error creating settings table: " . $db->lastErrorMsg();
    } else {
        echo "Settings table created or already exists.<br>";
    }

    // Insert initial data for waiting list state and days/times
    $stmt = $db->prepare("INSERT OR IGNORE INTO settings (key, value) VALUES (:key, :value)");

    // Set the initial waiting list state to "open"
    $stmt->bindValue(':key', 'waiting_list_state', SQLITE3_TEXT);
    $stmt->bindValue(':value', 'open', SQLITE3_TEXT); // Default state is 'open'
    $stmt->execute();

    // Insert open days and times (example: open on Saturday and Thursday from 9 AM to 12 PM)
    $stmt = $db->prepare("INSERT OR IGNORE INTO settings (key, value) VALUES (:key, :value)");
    $stmt->bindValue(':key', 'open_days_times', SQLITE3_TEXT);
    $stmt->bindValue(':value', 'Saturday,09:00-12:00,Thursday,09:00-12:00', SQLITE3_TEXT); // Format: Day,StartTime-EndTime
    $stmt->execute();

    // Insert consultation days and times (example: Wednesday 3 PM to 9 PM, Friday 2 PM to 9 PM)
    $stmt = $db->prepare("INSERT OR IGNORE INTO settings (key, value) VALUES (:key, :value)");
    $stmt->bindValue(':key', 'consultation_days_times', SQLITE3_TEXT);
    $stmt->bindValue(':value', 'Wednesday,15:00-21:00,Friday,14:00-21:00', SQLITE3_TEXT); // Format: Day,StartTime-EndTime
    $stmt->execute();

    // Insert closed times (optional, could be inferred from open/consultation times)
    $stmt = $db->prepare("INSERT OR IGNORE INTO settings (key, value) VALUES (:key, :value)");
    $stmt->bindValue(':key', 'closed_times', SQLITE3_TEXT);
    $stmt->bindValue(':value', '', SQLITE3_TEXT); // You can calculate the closed times based on the open and consultation times
    $stmt->execute();

    echo "Database and tables setup complete!<br>";

} catch (Exception $e) {
    echo "Failed to create database or tables: " . $e->getMessage();
}
?>
