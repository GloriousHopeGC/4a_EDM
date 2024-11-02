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
    <title>Register</title>
    <link rel="stylesheet" href="/4a_edma/public/lib/css/my.css"/>
    <link rel="icon" href="/4a_edma/public/lib/images/AGCOWC TRANSPARENT.png">
    <!-- Bootstrap Libraries-->
    <link rel="stylesheet" href="/4a_edma/public/lib/css/bootstrap.min.css">
    <script defer src="/4a_edma/public/lib/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> <!-- Include jQuery -->
    <script src="/edma/public/lib/js/my.js"></script><!-- Include my.js -->
</head>
<body>
<div class="container mt-5">
    <h2 class="text-center">Register</h2>
        <div class="row justify-content-center">
            <div class="col-md-6">
                <form id="registerForm" method="POST" class="border p-4 rounded shadow">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="first_name" class="form-label">First Name:</label>
                            <input type="text" id="first_name" name="first_name" class="form-control">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="surname" class="form-label">Surname:</label>
                            <input type="text" id="surname" name="surname" class="form-control">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="last_name" class="form-label">Last Name:</label>
                            <input type="text" id="last_name" name="last_name" class="form-control">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="gender" class="form-label">Gender:</label>
                                <select id="gender" name="gender" class="form-control">
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                    <option value="Unknown">Prefer not to say</option>
                                </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="birthday" class="form-label">Birthday:</label>
                            <input type="date" id="birthday" name="birthday" class="form-control">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="address" class="form-label">Address:</label>
                        <input type="text" id="address" name="address" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email:</label>
                        <input type="email" id="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password:</label>
                        <input type="password" id="password" name="password" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">Confirm Password:</label>
                        <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Register</button>
                    <a href="login.php" class="d-block text-center mt-3 no-underline">Already Have An Account?</a>
                </form>
            </div>
        </div>
</div>
</body>
</html>
