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
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="card shadow-sm w-100" style="max-width: 400px;">
            <div class="card-body">
                <h1 class="text-center text-primary">Reset Password</h1>
                
                <!-- Display error message if the reset code is invalid -->
                <?php if ($errorMessage): ?>
                    <div class="alert alert-danger text-center" role="alert">
                        <?php echo htmlspecialchars($errorMessage); ?>
                    </div>
                <?php endif; ?>

                <form method="POST" class="mt-4">
                    <div class="mb-3">
                        <label for="code" class="form-label">Enter the reset code:</label>
                        <input type="text" id="code" name="code" class="form-control" required>
                    </div>
                    <button type="submit" name="verify_code" class="btn btn-primary w-100">Verify Code</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS (optional but recommended) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

