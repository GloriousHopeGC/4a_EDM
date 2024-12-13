<?php
require_once '../../src/controller/userController.php';
$controller = new userController();
$controller->sessionAdmin();
$controller->handleLogoutAction();
$currentUserId = $_SESSION['user_id'] ?? null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="/edma/public/lib/css/bootstrap.min.css">
    <script defer src="/edma/public/lib/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <link rel="stylesheet" href="/edma/public/lib/css/admin.css">
    <script src="/edma/public/lib/js/admin.js"></script>
    <script src="/edma/public/lib/js/swal.js"></script>
    <link rel="stylesheet" href="/edma/public/lib/css/sweetalert2.min.css">
    <script defer src="/edma/public/lib/js/sweetalert2.all.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script defer src="/edma/public/lib/js/admin.js"></script> <!-- Include the admin.js -->
</head>
<body>
    <div id="admin-container"></div>
</body>
</html>


