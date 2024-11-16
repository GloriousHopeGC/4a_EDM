<?php
session_start();
require_once '../../src/controller/userController.php';
$controller = new userController();
$userData = $controller->getUserData();
if (isset($userData['error'])) {
    // Handle the error (e.g., user not found or not logged in)
    echo "<p>" . htmlspecialchars($userData['error']) . "</p>";
    exit();
}
$user = $userData['user'];
$user_info = $userData['user_info'];
$controller->handleLogoutAction(); 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update User</title>
    <link rel="stylesheet" href="/edma/public/lib/css/bootstrap.min.css">
    <link rel="stylesheet" href="/edma/public/lib/css/bootstrap.min.css">
    <script defer src="/edma/public/lib/js/bootstrap.bundle.min.js"></script>
    <link rel="icon" href="/edma/public/lib/images/AGCOWC TRANSPARENT.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script> -->
    <link rel="stylesheet" href="/edma/public/lib/css/edit.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="/edma/public/lib/js/my.js"></script>
    <script src="/4a_edma/public/lib/js/swal.js"></script>
    <link rel="stylesheet" href="/4a_edma/public/lib/css/sweetalert2.min.css">
    <script defer src="/4a_edma/public/lib/js/sweetalert2.all.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>
    <nav id="userAdmin"></nav>
    <div id="userAdminInfo"></div>
</body>
</html>
