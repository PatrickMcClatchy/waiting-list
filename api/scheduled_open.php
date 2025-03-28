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

    // Check if the list was manually closed today
    $stmt = $db->prepare("SELECT value FROM settings WHERE key = :key");
    $stmt->bindValue(':key', 'manual_close_date', SQLITE3_TEXT);
    $result = $stmt->execute();
    $row = $result->fetchArray(SQLITE3_ASSOC);
    $manualCloseDate = $row ? $row['value'] : '';

    error_log("Manual Close Date: {$manualCloseDate}");
    error_log("Current Date: {$currentDateTime->format('Y-m-d')}");

    if ($manualCloseDate === $currentDateTime->format('Y-m-d')) {
        $response['message'] = 'The waiting list was manually closed today.';
        echo json_encode($response);
        exit;
    }

    $isOpen = false;

    foreach ($openTimes as $openTime) {
        list($day, $time) = explode(' ', $openTime);

        // Skip if the day does not match
        if ($currentDay !== $day) {
            continue;
        }

        // Create DateTime object for scheduled time
        $scheduledDateTime = DateTime::createFromFormat('H:i', $time);
        if ($scheduledDateTime === false) {
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
        if ($currentTimestamp >= $scheduledDateTime->getTimestamp()) {
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
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
?>
