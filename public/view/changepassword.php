<?php
session_start();
// Check if the user is allowed to access the page
if (!isset($_SESSION['reset_verified']) || $_SESSION['reset_verified'] !== true) {
    header('Location: process_forgot_password.php');
    exit();
}

require_once '../../src/controller/userController.php';

$alertScript = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newPassword = isset($_POST['new_password']) ? $_POST['new_password'] : '';
    $confirmPassword = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';
    $email = $_SESSION['email']; // Get email from session

    if (!empty($newPassword) && !empty($confirmPassword)) {
        if ($newPassword === $confirmPassword) {
            $controller = new userController();
            $response = $controller->changeForgotPassword($email, $newPassword);
            if ($response['status'] === 'success') {
                // SweetAlert for success
                $alertScript = "
                    <script>
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: 'Password successfully changed.',
                            confirmButtonText: 'OK'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location = 'login.php';
                            }
                        });
                    </script>
                ";
                unset($_SESSION['reset_verified']);
            } else {
                // SweetAlert for error
                $alertScript = "
                    <script>
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Error changing password: {$response['message']}'
                        });
                    </script>
                ";
            }
        } else {
            // SweetAlert for mismatched passwords
            $alertScript = "
                <script>
                    Swal.fire({
                        icon: 'warning',
                        title: 'Warning!',
                        text: 'Passwords do not match.'
                    });
                </script>
            ";
        }
    } else {
        // SweetAlert for empty fields
        $alertScript = "
            <script>
                Swal.fire({
                    icon: 'info',
                    title: 'Notice',
                    text: 'Please fill in all fields.'
                });
            </script>
        ";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- SweetAlert2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="card shadow-sm w-100" style="max-width: 400px;">
            <div class="card-body">
                <h1 class="text-center text-primary">Change Password</h1>
                <form method="POST" class="mt-4">
                    <div class="mb-3">
                        <label for="new_password" class="form-label">New Password:</label>
                        <input type="password" id="new_password" name="new_password" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">Confirm Password:</label>
                        <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Change Password</button>
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


