<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

try {
    // Connect to SQLite database
    $db = new SQLite3('waiting_list.db');

    // Fetch all users from the waiting list
    $results = $db->query('SELECT * FROM waiting_list ORDER BY id ASC');

    // Check if the results are empty
    if ($results) {
        $position = 1;
        while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
            $estimatedWaitTime = $position * 15; // Assume 15 minutes per person
            echo "<p>{$row['name']} - Position: $position, Estimated wait: $estimatedWaitTime minutes</p>";
            $position++;
        }
        if ($position === 1) {
            echo "<p>No one is on the waiting list yet.</p>";
        }
    } else {
        echo "<p>Error fetching data from the database.</p>";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
