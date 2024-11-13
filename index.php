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

    <!-- Load jQuery library for AJAX functionality -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        $(document).ready(function() {
            // Handle form submission for signup
            $('#signupForm').submit(function(event) {
                event.preventDefault();  // Prevent the default form submission

                // Use jQuery's post method to send form data to 'add_user.php'
                $.post('add_user.php', $(this).serialize(), function(response) {
                    $('#signupResponse').html(response);  // Display the response message
                });
            });
        });
    </script>

    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        h1 {
            color: #2C3E50;
            text-align: left;
            margin-bottom: 10px;
        }
        .container {
            width: 90%;
            max-width: 500px;
            margin-top: 20px;
        }
        .login-link {
            margin-top: 20px;
            text-align: center;
        }
        form {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            margin-bottom: 10px;
        }
        input, textarea {
            padding: 10px;
            margin-bottom: 10px;
            width: 100%;
            border-radius: 5px;
            border: 1px solid #ccc;
            box-sizing: border-box;
        }
        button {
            padding: 10px 15px;
            background-color: #3498db;
            border: none;
            color: white;
            border-radius: 5px;
            cursor: pointer;
            align-self: flex-start;
        }
        #signupResponse {
            margin-top: 10px;
            color: green;
            text-align: left;
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Sign-up Section -->
        <section id="signup-section">
            <header>
                <h1>Join Our Waiting List</h1>
            </header>
            <form id="signupForm">
                <input type="text" name="name" placeholder="Enter your name" required>
                <input type="email" name="email" placeholder="Enter your email" required>
                <textarea name="comment" placeholder="Enter a comment (optional)"></textarea>
                <button type="submit">Sign Up</button>
            </form>

            <div id="signupResponse"></div>
        </section>

        <!-- Login Link -->
        <div class="login-link">
            <p><a href="login.php" target="_blank">Log in</a></p>
        </div>
    </div>
</body>
</html>
