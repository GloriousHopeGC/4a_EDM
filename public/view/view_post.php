<?php
require_once '../../src/controller/userController.php';
$controller = new userController();
$controller->sessionhome(); 
$controller->handleLogoutAction(); 
$currentUserId = $_SESSION['user_id'] ?? null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Post</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/edma/public/lib/js/my.js"></script>
    <script src="/edma/public/lib/js/swal.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="/4a_edma/public/lib/css/sweetalert2.min.css">
    <script defer src="/4a_edma/public/lib/js/sweetalert2.all.min.js"></script>
</head>
<body>
<nav id="userData"></nav>
<div class="container" style="margin-top:80px;">
        <!-- This is the container where the post content will be dynamically inserted -->
        <div id="viewPost"></div>
    </div>
</body>
</html>
