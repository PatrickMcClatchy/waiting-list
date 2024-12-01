<?php
try {
    $db = new SQLite3('../waiting_list.db');
    
    if (!$db) {
        throw new Exception('Unable to connect to the database.');
    }

    $stmt = $db->prepare("SELECT value FROM settings WHERE key = :key");
    $stmt->bindValue(':key', 'waiting_list_open', SQLITE3_TEXT);
    $result = $stmt->execute();
    
    if (!$result) {
        throw new Exception('Error executing the query.');
    }
    
    $row = $result->fetchArray(SQLITE3_ASSOC);

    if ($row) {
        echo json_encode(['success' => true, 'isOpen' => (int)$row['value']]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Setting not found']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}

?>
