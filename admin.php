<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Set up error logging
ini_set('log_errors', 1);
ini_set('error_log', 'php_errors.log');

// Connect to the SQLite database
try {
    $db = new SQLite3('waiting_list.db');
} catch (Exception $e) {
    die('Error: Unable to connect to the database. ' . $e->getMessage());
}

// Handle form submissions (removing users)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = intval($_POST['id']); // Get the ID from the POST request

    // Prepare the delete statement
    $stmt = $db->prepare('DELETE FROM waiting_list WHERE id = :id');
    $stmt->bindValue(':id', $id, SQLITE3_INTEGER);

    // Execute the delete query
    $result = $stmt->execute();

    if ($result) {
        echo "User removed successfully.";
    } else {
        echo "Failed to remove the user: " . $db->lastErrorMsg();
    }
}

// Fetch the current waiting list
$results = $db->query('SELECT * FROM waiting_list ORDER BY id ASC');

// Check if the results are empty
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Waiting List</title>
</head>
<body>
    <h1>Admin Panel</h1>
    <h2>Current Waiting List</h2>
    <form method="POST">
        <ul>
        <?php
        $found = false;
        while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
            $found = true;
            echo "<li>{$row['name']} - <button type='submit' name='id' value='{$row['id']}'>Strike off</button></li>";
        }

        if (!$found) {
            echo "<p>No one is on the waiting list.</p>";
        }
        ?>
        </ul>
    </form>
    <div style="margin-top: 20px;">
        <a href="index.php">Back to main page</a>
    </div>
</body>
</html>
