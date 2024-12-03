<?php
require_once '../../src/controller/userController.php';
$controller = new userController();
$controller->session(); 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="/edma/public/lib/js/my.js"></script>
    <link rel="icon" href="/4a_edma/public/lib/images/AGCOWC TRANSPARENT.png">
    <!-- Bootstrap Libraries -->
    <link rel="stylesheet" href="/4a_edma/public/lib/css/my.css"/>
    <link rel="stylesheet" href="/4a_edma/public/lib/css/bootstrap.min.css">
    <script defer src="/4a_edma/public/lib/js/bootstrap.bundle.min.js"></script>
    <script defer src="/4a_edma/public/lib/js/sweetalert2.all.min.js"></script>
    <link rel="stylesheet" href="/4a_edma/public/lib/css/sweetalert2.min.css">
</head>
<body>
    <div class="container d-flex justify-content-center align-items-center" style="height: 100vh;">
        <div class="card" style="width: 400px;">
            <div class="card-body">
                <h2 class="card-title text-center">Login</h2>
                <form id="loginForm" method="POST">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email:</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password:</label>
                        <input type="password" class="form-control" id="password" name="password">
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Login</button>
                </form>
                <div class="register-link text-center mt-3">
                    <p>Don't have an account? <a href="register.php" class="no-underline">Register</a></p>
                </div>
                <div class="forgot-password-link text-center">
                    <p><a href="process_forgot_password.php" class="no-underline">Forgot Password?</a></p>
                </div>

            </div>
        </div>
    </div>
</body>
</html>
