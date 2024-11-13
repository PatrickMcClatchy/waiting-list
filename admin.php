<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}

ini_set('log_errors', 1);
ini_set('error_log', 'php_errors.log');

try {
    $db = new SQLite3('waiting_list.db');

    // Clear database if the waiting list is empty
    $result = $db->querySingle("SELECT COUNT(*) as count FROM waiting_list");
    if ($result === 0) {
        $db->exec("DELETE FROM waiting_list");
        $db->exec("VACUUM");  // Reset the database to reclaim space and reset IDs
    } else {
        // Normalize positions to ensure sequential values without gaps
        $results = $db->query("SELECT id FROM waiting_list ORDER BY position ASC");
        $position = 1;
        while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
            $db->exec("UPDATE waiting_list SET position = $position WHERE id = " . $row['id']);
            $position++;
        }
    }

} catch (Exception $e) {
    die('Error: Unable to connect to the database. ' . $e->getMessage());
}

// Handle removing users from the list
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'remove') {
    $id = intval($_POST['id']);
    $db->exec("DELETE FROM waiting_list WHERE id = $id");

    header("Location: admin.php");
    exit;
}

// Handle moving users up and down
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'], $_POST['action'])) {
    $id = intval($_POST['id']);
    $action = $_POST['action'];

    $currentUser = $db->querySingle("SELECT id, position FROM waiting_list WHERE id = $id", true);

    if ($currentUser) {
        $currentPosition = $currentUser['position'];

        if ($action === 'move_up') {
            $previousUser = $db->querySingle("SELECT id, position FROM waiting_list WHERE position = " . ($currentPosition - 1), true);
            if ($previousUser) {
                $db->exec("UPDATE waiting_list SET position = $currentPosition WHERE id = " . $previousUser['id']);
                $db->exec("UPDATE waiting_list SET position = " . ($currentPosition - 1) . " WHERE id = $id");
            }
        } elseif ($action === 'move_down') {
            $nextUser = $db->querySingle("SELECT id, position FROM waiting_list WHERE position = " . ($currentPosition + 1), true);
            if ($nextUser) {
                $db->exec("UPDATE waiting_list SET position = $currentPosition WHERE id = " . $nextUser['id']);
                $db->exec("UPDATE waiting_list SET position = " . ($currentPosition + 1) . " WHERE id = $id");
            }
        }

        header("Location: admin.php");
        exit;
    }
}

// Handle admin adding a new user to a specific position
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_user') {
    $name = filter_var($_POST['name'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $comment = filter_var($_POST['comment'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $position = intval($_POST['position']);

    if ($name && $position > 0) {
        $db->exec("UPDATE waiting_list SET position = position + 1 WHERE position >= $position");

        $stmt = $db->prepare('INSERT INTO waiting_list (name, email_or_phone, comment, time, confirmed, position) VALUES (:name, :email, :comment, :time, :confirmed, :position)');
        $stmt->bindValue(':name', $name, SQLITE3_TEXT);
        $stmt->bindValue(':email', $email, SQLITE3_TEXT);
        $stmt->bindValue(':comment', $comment, SQLITE3_TEXT);
        $stmt->bindValue(':time', time(), SQLITE3_INTEGER);
        $stmt->bindValue(':confirmed', 1, SQLITE3_INTEGER);
        $stmt->bindValue(':position', $position, SQLITE3_INTEGER);
        $stmt->execute();

        echo "<p style='color: green;'>User added successfully at position $position.</p>";
    } else {
        echo "<p style='color: red;'>Please enter a valid name and position.</p>";
    }
}

// Export the list in a nicely formatted, compact printable view
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'export') {
    $results = $db->query('SELECT * FROM waiting_list ORDER BY position ASC');
    echo "<!DOCTYPE html><html lang='en'><head><meta charset='UTF-8'><title>Exported Waiting List</title>";
    echo "<style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
        }
        h1 {
            font-size: 20px;
            text-align: left;
            margin-bottom: 20px;
            color: #2C3E50;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f4f4f4;
            font-weight: bold;
            font-size: 14px;
        }
        td {
            vertical-align: top;
        }
        button {
            margin-top: 10px;
            padding: 6px 12px;
            background-color: #3498db;
            border: none;
            color: white;
            border-radius: 3px;
            cursor: pointer;
            font-size: 14px;
        }
    </style></head><body>";

    echo "<h1>Waiting List - Printable Version</h1>";
    echo "<table><tr><th>Position</th><th>Name</th><th>Email/Phone</th><th>Comment</th><th>Signup Time</th></tr>";

    while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
        echo "<tr>";
        echo "<td>{$row['position']}</td>";
        echo "<td>" . htmlspecialchars($row['name']) . "</td>";
        echo "<td>" . htmlspecialchars($row['email_or_phone']) . "</td>";
        echo "<td>" . htmlspecialchars($row['comment']) . "</td>";
        echo "<td>" . date('Y-m-d H:i', $row['time']) . "</td>";
        echo "</tr>";
    }

    echo "</table>";
    echo "<button onclick='window.print()'>Print</button>";
    echo "</body></html>";
    exit;
}

// Fetch the current waiting list ordered by position
$results = $db->query('SELECT * FROM waiting_list ORDER BY position ASC');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Waiting List</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            margin: 0;
            box-sizing: border-box;
        }
        h1, h2 {
            color: #2C3E50;
            text-align: center;
            margin: 20px 0;
        }
        #logout {
            text-align: right;
            margin-bottom: 20px;
        }
        #logout a {
            color: #3498db;
            text-decoration: none;
            font-size: 1rem;
        }
        #logout a:hover {
            text-decoration: underline;
        }
        #controls, .table-container {
            text-align: center;
            margin-bottom: 20px;
        }
        #controls form, .table-container {
            display: inline-block;
            width: 100%;
            max-width: 600px;
        }
        form {
            display: inline-block;
            width: 100%;
        }
        button, input[type="text"], input[type="email"], input[type="number"] {
            padding: 8px 12px;
            margin: 5px 0;
            font-size: 1rem;
            width: 100%;
            max-width: 100%;
            box-sizing: border-box;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        button {
            background-color: #3498db;
            color: white;
            cursor: pointer;
            border: none;
            border-radius: 4px;
        }
        button.remove {
            background-color: #e74c3c;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 0.9rem;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f4f4f4;
        }
        .action-buttons form {
            display: flex;
            flex-direction: column;
        }
        .action-buttons button {
            margin: 2px 0;
            font-size: 0.9rem;
        }
        .responsive-hide {
            display: none;
        }
        
        @media (min-width: 600px) {
            button, input[type="text"], input[type="email"], input[type="number"] {
                width: auto;
            }
            .action-buttons form {
                flex-direction: row;
            }
            .responsive-hide {
                display: table-cell;
            }
        }
    </style>
</head>
<body>
    <div id="logout">
        <a href="logout.php">Logout</a>
    </div>

    <h1>Admin Panel - Waiting List</h1>

    <div id="controls">
        <form method="POST">
            <button type="submit" name="action" value="export">Export to Print</button>
            <button type="button" onclick="window.location.reload()">Refresh</button>
        </form>
    </div>

    <!-- Form for admin to add a new user -->
    <div class="form-container">
        <h2>Add a New User</h2>
        <form method="POST">
            <input type="hidden" name="action" value="add_user">
            <label for="name">Name:</label>
            <input type="text" name="name" required>
            <label for="email">Email:</label>
            <input type="email" name="email">
            <label for="comment">Comment:</label>
            <input type="text" name="comment">
            <label for="position">Position:</label>
            <input type="number" name="position" min="1" required>
            <button type="submit">Add User</button>
        </form>
    </div>

    <div class="table-container">
        <table>
            <tr>
                <th>Position</th>
                <th>Name</th>
                <th class="responsive-hide">Email/Phone</th>
                <th>Comment</th>
                <th class="responsive-hide">Signup Time</th>
                <th>Actions</th>
            </tr>
            <?php while ($row = $results->fetchArray(SQLITE3_ASSOC)) { ?>
                <tr>
                    <td><?php echo $row['position']; ?></td>
                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                    <td class="responsive-hide"><?php echo htmlspecialchars($row['email_or_phone']); ?></td>
                    <td><?php echo htmlspecialchars($row['comment']); ?></td>
                    <td class="responsive-hide"><?php echo date('Y-m-d H:i', $row['time']); ?></td>
                    <td class="action-buttons">
                        <form method="POST">
                            <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                            <button type="submit" name="action" value="move_up">Move Up</button>
                            <button type="submit" name="action" value="move_down">Move Down</button>
                            <button type="submit" name="action" value="remove" class="remove">Remove</button>
                        </form>
                    </td>
                </tr>
            <?php } ?>
        </table>
    </div>

    <?php if ($results->numColumns() === 0): ?>
        <p>No users on the waiting list.</p>
    <?php endif; ?>
</body>
</html>
