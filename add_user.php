<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize inputs
    $name = filter_var($_POST['name'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $comment = filter_var($_POST['comment'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $ip_address = $_SERVER['REMOTE_ADDR'];

    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        try {
            $db = new SQLite3('waiting_list.db');

            // Check if the email has already been used to sign up
            $checkEmail = $db->prepare("SELECT has_signed_up FROM waiting_list WHERE email_or_phone = :email LIMIT 1");
            $checkEmail->bindValue(':email', $email, SQLITE3_TEXT);
            $emailResult = $checkEmail->execute()->fetchArray(SQLITE3_ASSOC);

            // Check if the IP has already signed up in the last 24 hours
            $checkIP = $db->prepare("SELECT COUNT(*) as count FROM waiting_list WHERE ip_address = :ip AND time > :time_limit");
            $checkIP->bindValue(':ip', $ip_address, SQLITE3_TEXT);
            $checkIP->bindValue(':time_limit', time() - 86400, SQLITE3_INTEGER); // 24 hours ago
            $ipResult = $checkIP->execute()->fetchArray(SQLITE3_ASSOC);

            // Unified error message for either email or IP restriction
            if (($emailResult && $emailResult['has_signed_up'] == 1) || $ipResult['count'] > 0) {
                echo "<p style='color: red;'>Only 1 signup per person.</p>";
            } else {
                // Insert the new user into the waiting list
                $stmt = $db->prepare('INSERT INTO waiting_list (name, email_or_phone, comment, time, confirmed, position, ip_address, has_signed_up) VALUES (:name, :email, :comment, :time, :confirmed, :position, :ip, :has_signed_up)');
                $stmt->bindValue(':name', $name, SQLITE3_TEXT);
                $stmt->bindValue(':email', $email, SQLITE3_TEXT);
                $stmt->bindValue(':comment', $comment, SQLITE3_TEXT);
                $stmt->bindValue(':time', time(), SQLITE3_INTEGER);
                $stmt->bindValue(':confirmed', 0, SQLITE3_INTEGER);
                $stmt->bindValue(':position', 1, SQLITE3_INTEGER); // Adjust as needed
                $stmt->bindValue(':ip', $ip_address, SQLITE3_TEXT);
                $stmt->bindValue(':has_signed_up', 1, SQLITE3_INTEGER);  // Mark as signed up
                $stmt->execute();

                echo "<p style='color: green;'>Your signup is successful! You have been added to the waiting list.</p>";
            }
        } catch (Exception $e) {
            echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
        }
    } else {
        echo "<p style='color: red;'>Invalid email address. Please try again.</p>";
    }
}
?>
