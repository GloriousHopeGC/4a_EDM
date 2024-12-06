<?php
require_once '../../src/controller/userController.php';
$controller = new userController();
$controller->sessionhome(); 
$controller->handleLogoutAction(); 
$currentUserId = $_SESSION['user_id'] ?? null;
$currentUserIdInfo = $_SESSION['user_info'] ?? null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome</title>
    <link rel="stylesheet" href="/4a_edma/public/lib/css/bootstrap.min.css">
    <script defer src="/4a_edma/public/lib/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="/edma/public/lib/js/my.js"></script> <!-- Adjust the path to your my.js file -->
    <script src="/4a_edma/public/lib/js/swal.js"></script>
    <link rel="stylesheet" href="/4a_edma/public/lib/css/sweetalert2.min.css">
    <script defer src="/4a_edma/public/lib/js/sweetalert2.all.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <meta name="current-user-id" content="<?php echo htmlspecialchars($currentUserId, ENT_QUOTES, 'UTF-8'); ?>">
    <meta name="current-user-info" content="<?php echo htmlspecialchars($currentUserIdInfo, ENT_QUOTES, 'UTF-8'); ?>">
</head>
<body>
    <nav id="userData"></nav>
    <div id="searchBar"></div>
    <div id="searchResults"></div>
    <div id="adminPost"></div>
    <div id="adminPostlist"></div>
</body>
</html>
