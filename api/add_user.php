<?php
header('Content-Type: application/json');

// Verify reCAPTCHA
if (!isset($_POST['g-recaptcha-response'])) {
    echo json_encode(['success' => false, 'message' => 'CAPTCHA not completed.']);
    exit;
}

$recaptchaResponse = $_POST['g-recaptcha-response'];
$secretKey = '6LeK9oIqAAAAAHGWBhQvZ90QvEe4y7Kc4chgc62h'; // Your reCAPTCHA secret key

// Send POST request to Google's reCAPTCHA verification API
$verifyURL = 'https://www.google.com/recaptcha/api/siteverify';
$response = file_get_contents($verifyURL . '?secret=' . $secretKey . '&response=' . $recaptchaResponse);
$responseKeys = json_decode($response, true);

if (!$responseKeys['success']) {
    echo json_encode(['success' => false, 'message' => 'CAPTCHA verification failed.']);
    exit;
}

// Connect to the database
try {
    $db = new SQLite3('../waiting_list.db');
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed: ' . $e->getMessage()]);
    exit;
}

// Check for required POST data
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['name'])) {
    $name = filter_var($_POST['name'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $email = isset($_POST['email']) ? filter_var($_POST['email'], FILTER_SANITIZE_EMAIL) : null;
    $comment = isset($_POST['comment']) ? filter_var($_POST['comment'], FILTER_SANITIZE_FULL_SPECIAL_CHARS) : null;
    $time = time();

    try {
        // Get the highest current position in the waiting list
        $result = $db->query('SELECT MAX(position) AS max_position FROM waiting_list');
        $row = $result->fetchArray(SQLITE3_ASSOC);
        $position = $row['max_position'] + 1;  // Set the new position to be one greater than the current max

        // Insert the new user at the bottom (new position)
        $stmt = $db->prepare('INSERT INTO waiting_list (name, email_or_phone, comment, time, confirmed, position) VALUES (:name, :email, :comment, :time, :confirmed, :position)');
        $stmt->bindValue(':name', $name, SQLITE3_TEXT);
        $stmt->bindValue(':email', $email, SQLITE3_TEXT);
        $stmt->bindValue(':comment', $comment, SQLITE3_TEXT);
        $stmt->bindValue(':time', $time, SQLITE3_INTEGER);
        $stmt->bindValue(':confirmed', 1, SQLITE3_INTEGER); // Assume confirmed for admin entry
        $stmt->bindValue(':position', $position, SQLITE3_INTEGER);
        $stmt->execute();

        // Return the response with name, email, and position
        echo json_encode([
            'success' => true,
            'message' => 'User added successfully at position ' . $position,
            'name' => $name,
            'email' => $email,
            'position' => $position
        ]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Failed to add user: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request or missing data.']);
}
