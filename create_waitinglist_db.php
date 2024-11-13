<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

try {
    // Create (or open if it exists) the SQLite database
    $db = new SQLite3('waiting_list.db');

    // Create the waiting_list table if it doesn't exist
    $createTableQuery = "
        CREATE TABLE IF NOT EXISTS waiting_list (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            email_or_phone TEXT NOT NULL,    -- Email or phone for signup
            comment TEXT,                    -- Optional comment
            time INTEGER NOT NULL,           -- Timestamp for signup
            confirmed INTEGER DEFAULT 0,     -- Indicates if signup is confirmed
            position INTEGER DEFAULT 1,      -- User's position in the list
            ip_address TEXT,                 -- Stores user's IP address
            has_signed_up BOOLEAN DEFAULT 0  -- Indicates if this user has signed up before
        );
    ";
    $db->exec($createTableQuery);

    echo "Database and table created successfully.";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
