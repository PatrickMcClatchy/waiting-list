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
        /* Reset default styles */
        body, html {
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
            font-family: Arial, sans-serif;
        }

        .container {
            width: 100%;
            max-width: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px;
            box-sizing: border-box;
        }

        #signup-section {
            width: 100%;
            max-width: 500px;
            background-color: #f4f4f4;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        #signup-section header h1 {
            text-align: center;
            color: #2C3E50;
            margin-bottom: 15px;
        }

        /* Form Styles */
        form {
            display: flex;
            flex-direction: column;
            align-items: stretch;
        }

        input, textarea, button {
            padding: 12px;
            margin-bottom: 15px;
            width: 100%;
            border-radius: 5px;
            border: 1px solid #ccc;
            box-sizing: border-box;
        }

        button {
            background-color: #3498db;
            border: none;
            color: white;
            cursor: pointer;
        }

        button:hover {
            background-color: #2980b9;
        }

        /* Response Message */
        #signupResponse {
            margin-top: 10px;
            color: green;
            text-align: center;
        }

        /* Login Link */
        .login-link p {
            text-align: center;
            margin-top: 20px;
        }

        .login-link p a {
            color: #3498db;
            text-decoration: none;
        }

        .login-link p a:hover {
            text-decoration: underline;
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
