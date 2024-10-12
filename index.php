<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Waiting List</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Periodically refresh the waiting list
            setInterval(function() {
                $('#waiting-list').load('get_list.php');
            }, 5000);
            
            // Submit form via AJAX
            $('#addForm').submit(function(event) {
                event.preventDefault();
                console.log("Form submitted!"); // Debug: Check if form submission is triggered
                $.post('add_user.php', $(this).serialize(), function(response) {
                    console.log(response); // Debug: Log server response
                    alert(response.message); 
                    $('#waiting-list').load('get_list.php');
                }, 'json').fail(function() {
                    alert('Failed to connect to the server.');
                });
            });
        });
    </script>
</head>
<body>
    <h1>Join the Waiting List</h1>
    <form id="addForm">
        <input type="text" name="name" placeholder="Your Name" required>
        <button type="submit">Add Me to the List</button>
    </form>
    
    <h2>Current Waiting List</h2>
    <div id="waiting-list">
        <!-- Waiting list will be loaded here dynamically -->
    </div>
    <div style="margin-top: 20px;">
        <a href="admin.php">Go to Admin Panel</a>
    </div>
</body>
</html>
