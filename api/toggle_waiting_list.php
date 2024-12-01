<?php
try {
    // Create or open the SQLite database
    $db = new SQLite3('../waiting_list.db');

    // Check if the database connection is valid
    if (!$db) {
        throw new Exception('Failed to connect to the database.');
    }

    // Retrieve the state of the waiting list from the POST request
    $isOpen = isset($_POST['isOpen']) ? $_POST['isOpen'] : null;

    // Validate if the isOpen parameter is provided
    if ($isOpen === null) {
        throw new Exception('The state of the waiting list (isOpen) is required.');
    }

    // Prepare the SQL statement to update the state of the waiting list
    $stmt = $db->prepare("UPDATE settings SET value = :value WHERE key = 'waiting_list_open'");
    
    // Check if the statement preparation is successful
    if (!$stmt) {
        throw new Exception('Failed to prepare the SQL statement.');
    }

    // Bind the value to the prepared statement
    $stmt->bindValue(':value', (int)$isOpen, SQLITE3_INTEGER);

    // Execute the statement
    $result = $stmt->execute();

    // Check if the execution was successful
    if (!$result) {
        throw new Exception('Failed to execute the SQL statement.');
    }

    // Return a success message
    echo json_encode(['success' => true, 'message' => 'Waiting list state updated successfully.']);

} catch (Exception $e) {
    // Return error message
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
