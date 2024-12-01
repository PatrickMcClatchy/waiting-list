<?php
try {
    // Trigger the update for the waiting list state by calling the update script
    $updateResult = file_get_contents(__DIR__ . '/update_waiting_list_state.php');
    if ($updateResult === false) {
        throw new Exception("Failed to trigger update_waiting_list_state.php");
    }

    // Connect to the database
    $db = new SQLite3('../waiting_list.db');
    if (!$db) {
        throw new Exception('Unable to connect to the database.');
    }

    // Current date and time
    $currentDateTime = new DateTime();
    $currentDay = $currentDateTime->format('l'); // Full weekday name (e.g., 'Monday')
    $currentTime = $currentDateTime->format('H:i'); // Current time in HH:mm format

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

    // Find the next available consultation time
    $nextConsultation = null;
    foreach ($consultationDaysTimes as $consultationDayTime) {
        $parts = explode(',', $consultationDayTime);
        if (count($parts) >= 2) {
            list($day, $timeRange) = $parts;
            $timeParts = explode('-', $timeRange);
            if (count($timeParts) >= 2) {
                $startTime = $timeParts[0];
                $endTime = $timeParts[1];
    
                // Debugging outputs
                echo "Consultation Day: $day, Start Time: $startTime, End Time: $endTime<br>";
    
                $consultationStart = new DateTime("next $day $startTime");
                $consultationEnd = new DateTime("next $day $endTime");
    
                echo "Consultation Start: " . $consultationStart->format('Y-m-d H:i') . "<br>";
                echo "Consultation End: " . $consultationEnd->format('Y-m-d H:i') . "<br>";
    
                if ($currentDateTime < $consultationStart) {
                    $nextConsultation = [
                        'day' => $day,
                        'startTime' => $startTime,
                        'endTime' => $endTime
                    ];
                    break;
                }
            }
        }
    }
    
    if (!$nextConsultation) {
        echo "No upcoming consultations found.<br>";
    }
    

    // Determine the waiting list state
    $isOpen = false;
    $isInConsultation = false;
    foreach ($consultationDaysTimes as $consultationDayTime) {
        $parts = explode(',', $consultationDayTime);
        if (count($parts) >= 2) {
            list($day, $timeRange) = $parts;
            $timeParts = explode('-', $timeRange);
            if (count($timeParts) >= 2) {
                $startTime = $timeParts[0];
                $endTime = $timeParts[1];

                $consultationStart = new DateTime("next $day $startTime");
                $consultationEnd = new DateTime("next $day $endTime");

                if ($currentDateTime >= $consultationStart && $currentDateTime <= $consultationEnd) {
                    $isInConsultation = true;
                    break;
                }
            }
        }
    }

    // Set the state of the waiting list
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

    // Return the result
    $stmt = $db->prepare("SELECT value FROM settings WHERE key = :key");
    $stmt->bindValue(':key', 'waiting_list_state', SQLITE3_TEXT);
    $result = $stmt->execute();

    if (!$result) {
        throw new Exception('Error executing the query.');
    }

    $row = $result->fetchArray(SQLITE3_ASSOC);
    $waitingListState = $row ? $row['value'] : 'unknown';

    // Return the response
    echo json_encode([
        'success' => true,
        'isOpen' => $waitingListState,
        'nextConsultation' => $nextConsultation ? $nextConsultation : null
    ]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}