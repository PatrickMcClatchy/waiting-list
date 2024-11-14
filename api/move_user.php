<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'], $_POST['direction'])) {
    $id = intval($_POST['id']);
    $direction = $_POST['direction'];

    try {
        $db = new SQLite3('../waiting_list.db');
        $currentUser = $db->querySingle("SELECT id, position FROM waiting_list WHERE id = $id", true);

        if ($currentUser) {
            $currentPosition = $currentUser['position'];

            if ($direction === 'up') {
                $swapUser = $db->querySingle("SELECT id, position FROM waiting_list WHERE position = " . ($currentPosition - 1), true);
            } else if ($direction === 'down') {
                $swapUser = $db->querySingle("SELECT id, position FROM waiting_list WHERE position = " . ($currentPosition + 1), true);
            }

            if ($swapUser) {
                $db->exec("UPDATE waiting_list SET position = $currentPosition WHERE id = " . $swapUser['id']);
                $db->exec("UPDATE waiting_list SET position = " . $swapUser['position'] . " WHERE id = $id");
                echo json_encode(['success' => true, 'message' => 'User moved successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Cannot move user']);
            }
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
}
