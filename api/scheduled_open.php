<?php
header('Content-Type: application/json');

// Set the time zone to Germany (Europe/Berlin)
date_default_timezone_set('Europe/Berlin');

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
    $currentDateTime = new DateTime();
    $currentDay = $currentDateTime->format('l'); // Full day name (e.g., 'Monday')
    $currentTimestamp = $currentDateTime->getTimestamp();

    error_log("Current Day: {$currentDay}");
    error_log("Current Time: {$currentDateTime->format('Y-m-d H:i:s')}");
    error_log("Current Timestamp: {$currentTimestamp}");

    $isOpen = false;

    foreach ($openTimes as $openTime) {
        list($day, $time) = explode(' ', $openTime);

        // Skip if the day does not match
        if ($currentDay !== $day) {
            error_log("Day mismatch - Current: {$currentDay}, Scheduled: {$day}");
            continue;
        }

        // Create DateTime object for scheduled time
        $scheduledDateTime = DateTime::createFromFormat('H:i', $time);
        if ($scheduledDateTime === false) {
            error_log("Invalid time format: $time");
            continue;
        }

        // Set the scheduled time to today's date
        $scheduledDateTime->setDate(
            $currentDateTime->format('Y'),
            $currentDateTime->format('m'),
            $currentDateTime->format('d')
        );

        error_log("Scheduled Time: {$scheduledDateTime->format('Y-m-d H:i:s')}");
        error_log("Scheduled Timestamp: {$scheduledDateTime->getTimestamp()}");

        // Compare timestamps - open list if the current time is exactly the same or later
        if (($currentTimestamp -1) >= $scheduledDateTime->getTimestamp()) {
            error_log("Match found - Opening waiting list");
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
    error_log("Error: " . $e->getMessage());
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
?>
