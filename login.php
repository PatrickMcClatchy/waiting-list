<?php
session_start();

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Define the hashed password (you can replace this with a securely hashed password)
$hashed_password = '$2y$06$So/CYnud39L2W43MPmSTcOr8WosL1drKI9B2ktEKk.J0gkXO52FFK';

// Check if the form has been submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'];

    // Verify the entered password against the hashed password
    if (password_verify($password, $hashed_password)) {
        $_SESSION['loggedin'] = true;  // Set session variable for logged in state
        header('Location: admin.php');  // Redirect to admin page
        exit;
    } else {
        $error = "Invalid password. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        form {
            background-color: #f4f4f4;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        input[type="password"] {
            padding: 10px;
            margin-bottom: 10px;
            width: 100%;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        button {
            padding: 10px;
            width: 100%;
            background-color: #3498db;
            border: none;
            color: white;
            border-radius: 5px;
            cursor: pointer;
        }
        p {
            color: red;
        }
    </style>
</head>
<body>
    <form method="POST">
        <h1>Admin Login</h1>
        <input type="password" name="password" placeholder="Enter password" required>
        <button type="submit">Login</button>
        <?php if (isset($error)) { echo "<p>$error</p>"; } ?>
    </form>
</body>
</html>
