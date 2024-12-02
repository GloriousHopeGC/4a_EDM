<?php
session_start(); // Ensure this is at the top

require_once '../../src/controller/userController.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = isset($_POST['email']) ? filter_var($_POST['email'], FILTER_SANITIZE_EMAIL) : '';
    $controller = new userController();

    if (isset($_POST['send_code'])) {
        // Step 1: Validate email format
        if (!empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
            // Step 2: Send Reset Code
            $response = $controller->sendResetCode($email);
            echo "<script>alert('{$response['message']}');</script>";

            if ($response['status'] === 'success') {
                // Step 3: Redirect to resetcode.php
                $_SESSION['email'] = $email; // Store email in session
                header('Location: resetcode.php');
                exit();
            }
        } else {
            echo "<script>alert('Please provide a valid email address.');</script>";
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Process Forgot Password</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f7fc;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 400px;
            margin: 50px auto;
            padding: 30px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        h1 {
            font-size: 24px;
            color: #333;
            text-align: center;
            margin-bottom: 20px;
        }

        label {
            font-size: 16px;
            color: #555;
            display: block;
            margin-bottom: 10px;
        }

        input[type="email"],
        input[type="submit"] {
            width: 100%;
            padding: 15px;
            font-size: 16px;
            border: 1px solid #ddd;
            border-radius: 4px;
            margin-bottom: 20px;
            box-sizing: border-box;
        }

        input[type="email"] {
            margin-bottom: 10px;
        }

        input[type="submit"] {
            background-color: #007BFF;
            color: white;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        input[type="submit"]:hover {
            background-color: #0056b3;
        }

        .alert {
            text-align: center;
            color: red;
            font-size: 14px;
            margin-top: 10px;
        }

        .message {
            text-align: center;
            color: green;
            font-size: 14px;
            margin-top: 10px;
        }

        .form-footer {
            text-align: center;
            font-size: 14px;
            color: #777;
        }

        .form-footer a {
            color: #007BFF;
            text-decoration: none;
        }

        .form-footer a:hover {
            text-decoration: underline;
        }

        /* Mobile-friendly */
        @media (max-width: 500px) {
            .container {
                padding: 20px;
            }

            h1 {
                font-size: 20px;
            }

            input[type="email"], input[type="submit"] {
                padding: 12px;
                font-size: 14px;
            }
        }
    </style>
</head>

<body>

    <div class="container">
        <h1>Forgot Password?</h1>
        <p class="form-footer">Enter your email to receive a password reset code.</p>

        <form method="POST">
            <label for="email">Email Address:</label>
            <input type="email" id="email" name="email" required placeholder="Enter your email" />

            <input type="submit" name="send_code" value="Send Reset Code" />
        </form>

        <!-- Display message if there was an error or success -->
        <div class="message">
            <?php
            if (isset($response)) {
                echo $response['message'];
            }
            ?>
        </div>

        <div class="form-footer">
            <p>Remembered your password? <a href="login.php">Login here</a></p>
        </div>
    </div>

</body>
</html>
