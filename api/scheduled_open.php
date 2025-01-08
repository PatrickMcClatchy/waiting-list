<?php
header('Content-Type: application/json');

$response = ['success' => false, 'message' => 'The waiting list remains closed.'];

try {
    // Connect to the database
    $db = new SQLite3('../waiting_list.db');

    // Fetch the scheduled open times from the database
    $stmt = $db->prepare("SELECT value FROM settings WHERE key = :key");
    $stmt->bindValue(':key', 'scheduled_open_times', SQLITE3_TEXT);
    $result = $stmt->execute();

    if (!$result) {
        throw new Exception('Error executing the query.');
    }

    $row = $result->fetchArray(SQLITE3_ASSOC);
    $scheduledOpenTimes = $row ? $row['value'] : '';

    if (empty($scheduledOpenTimes)) {
        throw new Exception('No scheduled open times found.');
    }

    // Parse the scheduled open times
    $openTimes = explode(',', $scheduledOpenTimes);

    // Get the current day and time
    $currentDay = date('l'); // Full day name (e.g., 'Monday')
    $currentTime = date('H:i'); // Current time in HH:MM format

    // Check if the current day and time match any of the scheduled open times
    $isOpen = false;
    foreach ($openTimes as $openTime) {
        list($day, $time) = explode(' ', $openTime);
        if ($currentDay === $day && $currentTime >= $time) {
            $isOpen = true;
            break;
        }
    }

    // Toggle the waiting list if it should be open
    if ($isOpen) {
        $stmt = $db->prepare("UPDATE settings SET value = :value WHERE key = 'waiting_list_open'");
        $stmt->bindValue(':value', '1', SQLITE3_TEXT);
        if ($stmt->execute()) {
            $response['success'] = true;
            $response['message'] = 'The waiting list is now open.';
        } else {
            throw new Exception('Failed to update the waiting list state.');
        }
    }
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
?>