<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize user input
    $name = htmlspecialchars($_POST['name']);
    $currentTime = time();

    try {
        // Connect to the SQLite database
        $db = new SQLite3('waiting_list.db');
        
        // Create the table if it doesn't exist
        $db->exec("CREATE TABLE IF NOT EXISTS waiting_list (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            time INTEGER NOT NULL
        );");

        // Prepare the insert statement
        $stmt = $db->prepare('INSERT INTO waiting_list (name, time) VALUES (:name, :time)');
        $stmt->bindValue(':name', $name, SQLITE3_TEXT);
        $stmt->bindValue(':time', $currentTime, SQLITE3_INTEGER);

        // Execute the insert query
        $result = $stmt->execute();

        if ($result) {
            echo json_encode(['message' => 'Successfully added to the waiting list!']);
        } else {
            echo json_encode(['message' => 'Failed to add to the waiting list.']);
        }

    } catch (Exception $e) {
        // Output the error message if something goes wrong
        echo json_encode(['message' => 'Error: ' . $e->getMessage()]);
    }
} else {
    // Handle invalid request methods
    echo json_encode(['message' => 'Invalid request method.']);
}
?>
