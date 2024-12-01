<?php
try {
    // Ensure the correct path to 'update_waiting_list_state.php'
    // Using require_once for including a PHP script (better than file_get_contents)
    require_once('update_waiting_list_state.php'); // Adjust path as needed
    
    // Connect to the database
    $db = new SQLite3('../waiting_list.db');
    
    if (!$db) {
        throw new Exception('Unable to connect to the database.');
    }

    // Current date and time
    $currentDateTime = new DateTime();
    $currentDay = $currentDateTime->format('l'); // Full weekday name (e.g., 'Monday')
    $currentTime = $currentDateTime->format('H:i'); // Current time in HH:mm format

    // Retrieve the open days and times from the database
    $stmt = $db->prepare("SELECT value FROM settings WHERE key = :key");
    $stmt->bindValue(':key', 'open_days_times', SQLITE3_TEXT);
    $result = $stmt->execute();
    
    if (!$result) {
        throw new Exception('Error executing the query.');
    }
    
    $row = $result->fetchArray(SQLITE3_ASSOC);
    $openDaysTimes = [];
    
    if ($row) {
        $openDaysTimes = explode(',', $row['value']);
    }

    // Check if the current time is within open hours
    $isOpen = false;
    foreach ($openDaysTimes as $openDayTime) {
        // Ensure there are at least 2 parts (day and time range)
        $parts = explode(',', $openDayTime);
        if (count($parts) >= 2) {
            list($day, $timeRange) = $parts;
            $timeParts = explode('-', $timeRange);
            if (count($timeParts) >= 2) {
                $startTime = $timeParts[0];
                $endTime = $timeParts[1];

                // If today matches the open day and the current time is within the range
                if ($currentDay === $day && $currentTime >= $startTime && $currentTime <= $endTime) {
                    $isOpen = true;
                    break;
                }
            }
        }
    }

    // Retrieve the consultation days and times from the database
    $stmt = $db->prepare("SELECT value FROM settings WHERE key = :key");
    $stmt->bindValue(':key', 'consultation_days_times', SQLITE3_TEXT);
    $result = $stmt->execute();
    
    if (!$result) {
        throw new Exception('Error executing the query.');
    }
    
    $row = $result->fetchArray(SQLITE3_ASSOC);
    $consultationDaysTimes = [];
    
    if ($row) {
        $consultationDaysTimes = explode(',', $row['value']);
    }

    // Check if the current time is within consultation hours
    $isInConsultation = false;
    foreach ($consultationDaysTimes as $consultationDayTime) {
        // Ensure there are at least 2 parts (day and time range)
        $parts = explode(',', $consultationDayTime);
        if (count($parts) >= 2) {
            list($day, $timeRange) = $parts;
            $timeParts = explode('-', $timeRange);
            if (count($timeParts) >= 2) {
                $startTime = $timeParts[0];
                $endTime = $timeParts[1];

                // If today matches the consultation day and the current time is within the range
                if ($currentDay === $day && $currentTime >= $startTime && $currentTime <= $endTime) {
                    $isInConsultation = true;
                    break;
                }
            }
        }
    }

    // Determine the waiting list state
    $newState = 0; // Default to 'closed'

    if ($isInConsultation) {
        $newState = 2; // 'in_consultation'
    } elseif ($isOpen) {
        $newState = 1; // 'open'
    }

    // Update the waiting list state in the database
    $stmt = $db->prepare("UPDATE settings SET value = :state WHERE key = :key");
    $stmt->bindValue(':key', 'waiting_list_state', SQLITE3_TEXT);
    $stmt->bindValue(':state', $newState === 1 ? 'open' : ($newState === 2 ? 'consultation' : 'closed'), SQLITE3_TEXT);
    $stmt->execute();

    // Retrieve and return the updated state
    $stmt = $db->prepare("SELECT value FROM settings WHERE key = :key");
    $stmt->bindValue(':key', 'waiting_list_state', SQLITE3_TEXT);
    $result = $stmt->execute();
    
    if (!$result) {
        throw new Exception('Error executing the query.');
    }
    
    $row = $result->fetchArray(SQLITE3_ASSOC);

    if ($row) {
        // Return the waiting list state as a string ('open', 'consultation', or 'closed')
        echo json_encode(['success' => true, 'isOpen' => $row['value']]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Setting not found']);
    }

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>
