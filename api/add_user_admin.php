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

    if ($name) {
        try {
            $db = new SQLite3('../waiting_list.db');

            // Get the current maximum position
            $result = $db->query('SELECT COALESCE(MAX(position), 0) AS max_position FROM waiting_list');
            $row = $result->fetchArray(SQLITE3_ASSOC);
            $position = $row['max_position'] + 1;

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
        echo json_encode(['success' => false, 'message' => 'Invalid name']);
    }
}
