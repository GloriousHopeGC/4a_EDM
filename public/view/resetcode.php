<?php
session_start(); // Ensure session is started

// Redirect if no email in session
if (!isset($_SESSION['email'])) {
    header('Location: process_forgot_password.php');
    exit();
}

require_once '../../src/controller/userController.php';

$errorMessage = ''; // Variable to store error message

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = isset($_SESSION['email']) ? $_SESSION['email'] : ''; // Get email from session
    $code = isset($_POST['code']) ? trim($_POST['code']) : ''; // Sanitize code input
    $controller = new userController();

    if (isset($_POST['verify_code'])) {
        // Verify Reset Code
        if (!empty($email) && !empty($code)) {
            $isValid = $controller->verifyResetCode($email, $code); // Call verifyResetCode
            if ($isValid['status'] === 'success') {
                // If valid, set session and redirect
                $_SESSION['reset_verified'] = true;
                if (!headers_sent()) {
                    header('Location: changepassword.php');
                    exit();
                } else {
                    echo '<script>window.location.href = "changepassword.php";</script>';
                    exit();
                }
            } else {
                // Display error message from verifyResetCode
                $errorMessage = $isValid['message'];
            }
        } else {
            // Display error if fields are empty
            $errorMessage = 'Please fill in all the required fields.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enter Reset Code</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .container {
            width: 100%;
            max-width: 400px;
            margin: 50px auto;
            padding: 20px;
            background: white;
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            color: #333;
        }
        label {
            display: block;
            margin-top: 10px;
            color: #555;
        }
        input[type="text"], input[type="submit"] {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        input[type="submit"] {
            background-color: #007BFF;
            color: white;
            cursor: pointer;
            border: none;
        }
        input[type="submit"]:hover {
            background-color: #0056b3;
        }
        .error-message {
            color: red;
            font-size: 14px;
            margin-top: 10px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Reset Password</h1>

        <!-- Display error message if the reset code is invalid -->
        <?php if ($errorMessage): ?>
            <div class="error-message"><?php echo htmlspecialchars($errorMessage); ?></div>
        <?php endif; ?>

        <form method="POST">
            <label for="code">Enter the reset code:</label>
            <input type="text" id="code" name="code" required>
            <input type="submit" name="verify_code" value="Verify Code">
        </form>
    </div>
</body>
</html>
