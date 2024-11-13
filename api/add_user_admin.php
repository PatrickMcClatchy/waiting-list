<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = filter_var($_POST['name'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $comment = filter_var($_POST['comment'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $position = intval($_POST['position']);

    if ($name && $position > 0) {
        try {
            $db = new SQLite3('../waiting_list.db');

            // Shift other users down to make space for the new user at the desired position
            $db->exec("UPDATE waiting_list SET position = position + 1 WHERE position >= $position");

            // Insert the new user
            $stmt = $db->prepare('INSERT INTO waiting_list (name, email_or_phone, comment, time, confirmed, position) VALUES (:name, :email, :comment, :time, :confirmed, :position)');
            $stmt->bindValue(':name', $name, SQLITE3_TEXT);
            $stmt->bindValue(':email', $email, SQLITE3_TEXT);
            $stmt->bindValue(':comment', $comment, SQLITE3_TEXT);
            $stmt->bindValue(':time', time(), SQLITE3_INTEGER);
            $stmt->bindValue(':confirmed', 1, SQLITE3_INTEGER);
            $stmt->bindValue(':position', $position, SQLITE3_INTEGER);
            $stmt->execute();

            echo json_encode(['success' => true, 'message' => 'User added successfully at position ' . $position]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid name or position']);
    }
}
