<?php
require_once '../../src/controller/userController.php';
$controller = new userController();
$controller->sessionAdmin(); 
$controller->handleLogoutAction(); 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome</title>
    <link rel="stylesheet" href="/4a_edma/public/lib/css/bootstrap.min.css">
    <script defer src="/4a_edma/public/lib/js/bootstrap.bundle.min.js"></script>
    <!-- <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet"> -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="/edma/public/lib/js/my.js"></script> <!-- Adjust the path to your my.js file -->
    <script src="/4a_edma/public/lib/js/swal.js"></script>
    <link rel="stylesheet" href="/4a_edma/public/lib/css/sweetalert2.min.css">
    <script defer src="/4a_edma/public/lib/js/sweetalert2.all.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>
    <nav id="userData"></nav>
    <div id="adminPost"></div>
    <div id="adminPostlist"></div>
</body>
</html>
