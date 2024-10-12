<?php
$db = new SQLite3('waiting_list.db');  // This will create the file if it doesn't exist
$db->exec("CREATE TABLE IF NOT EXISTS waiting_list (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    time INTEGER NOT NULL
);");

echo "Database and table created successfully.";
?>
