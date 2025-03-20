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

    // Create the settings table if it doesn't exist
    $createSettingsTable = $db->exec("CREATE TABLE IF NOT EXISTS settings (
        key TEXT PRIMARY KEY,
        value TEXT NOT NULL
    )");

    if (!$createSettingsTable) {
        echo "Error creating settings table: " . $db->lastErrorMsg();
    } else {
        echo "Settings table created or already exists.<br>";
    }

    // Insert the initial waiting list state if it doesn't exist
    $stmt = $db->prepare("INSERT OR IGNORE INTO settings (key, value) VALUES (:key, :value)");
    $stmt->bindValue(':key', 'waiting_list_open', SQLITE3_TEXT);
    $stmt->bindValue(':value', '1', SQLITE3_TEXT); // Default state is open ('1')
    
    if ($stmt->execute()) {
        echo "Initial settings inserted successfully.<br>";
    } else {
        echo "Failed to insert initial settings: " . $db->lastErrorMsg() . "<br>";
    }

    // Insert the scheduled open times if it doesn't exist
    $stmt = $db->prepare("INSERT OR IGNORE INTO settings (key, value) VALUES (:key, :value)");
    $stmt->bindValue(':key', 'scheduled_open_times', SQLITE3_TEXT);
    $stmt->bindValue(':value', 'Monday 09:00,Thursday 14:00', SQLITE3_TEXT); // Example times
    $stmt->execute();

    // Insert the default closed message if it doesn't exist
    $stmt = $db->prepare("INSERT OR IGNORE INTO settings (key, value) VALUES (:key, :value)");
    $stmt->bindValue(':key', 'closed_message', SQLITE3_TEXT);
    $stmt->bindValue(':value', 'The waiting list is currently closed.', SQLITE3_TEXT);
    $stmt->execute();

    // Insert the manual close date if it doesn't exist
    $stmt = $db->prepare("INSERT OR IGNORE INTO settings (key, value) VALUES (:key, :value)");
    $stmt->bindValue(':key', 'manual_close_date', SQLITE3_TEXT);
    $stmt->bindValue(':value', '', SQLITE3_TEXT);
    $stmt->execute();

    echo "Database and tables setup complete!<br>";

} catch (Exception $e) {
    echo "Failed to create database or tables: " . $e->getMessage();
}
?>