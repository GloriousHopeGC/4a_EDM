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
</head>

<body>
    <div class="container mt-5">
        <div class="card shadow-sm mx-auto" style="max-width: 400px;">
            <div class="card-body">
                <h1 class="text-center text-primary">Forgot Password?</h1>
                <p class="text-center text-muted">Enter your email to receive a password reset code.</p>

                <form method="POST">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address:</label>
                        <input type="email" id="email" name="email" class="form-control" required placeholder="Enter your email">
                    </div>
                    <button type="submit" name="send_code" class="btn btn-primary w-100">Send Reset Code</button>
                </form>
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

