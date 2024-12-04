<?php
session_start();

require_once '../../src/controller/userController.php';

$alertScript = ''; // Placeholder for SweetAlert script

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = isset($_POST['email']) ? filter_var($_POST['email'], FILTER_SANITIZE_EMAIL) : '';
    $controller = new userController();

    if (isset($_POST['send_code'])) {
        // Step 1: Validate email format
        if (!empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
            // Step 2: Send Reset Code
            $response = $controller->sendResetCode($email);

            // Generate SweetAlert based on response
            if ($response['status'] === 'success') {
                $alertScript = "
                    <script>
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: '{$response['message']}'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location = 'resetcode.php';
                            }
                        });
                    </script>
                ";
                $_SESSION['email'] = $email; // Store email in session
            } else {
                $alertScript = "
                    <script>
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: '{$response['message']}'
                        });
                    </script>
                ";
            }
        } else {
            $alertScript = "
                <script>
                    Swal.fire({
                        icon: 'warning',
                        title: 'Invalid Email',
                        text: 'Please provide a valid email address.'
                    });
                </script>
            ";
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
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- SweetAlert2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f8f9fa;
        }
        .card {
            margin: 20px;
            border-radius: 10px;
        }
        .card-body h2 {
            font-size: 1.8rem;
            font-weight: 700;
            color: #333;
        }
        .card-body p {
            font-size: 0.95rem;
            color: #6c757d;
        }
        .btn-primary {
            font-weight: 500;
        }
        a {
            font-size: 0.9rem;
        }
    </style>
</head>

<body>
    <div class="d-flex justify-content-center align-items-center vh-100">
        <div class="card shadow-sm" style="width: 360px;">
            <div class="card-body">
                <h2 class="text-center mb-3">Forgot Password?</h2>
                <p class="text-center mb-4">Enter your email to receive a password reset code.</p>

                <form method="POST">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address:</label>
                        <input type="email" id="email" name="email" class="form-control" required placeholder="Enter your email">
                    </div>
                    <button type="submit" name="send_code" class="btn btn-primary w-100">Send Reset Code</button>
                </form>
                <div class="text-center mt-3">
                    <a href="login.php" class="text-decoration-none">Remembered your password? <strong>Login here</strong></a>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- SweetAlert Script -->
    <?php if (!empty($alertScript)) echo $alertScript; ?>
</body>
</html>

