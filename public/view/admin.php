<?php
require_once '../../src/controller/userController.php';
$controller = new userController();
$controller->sessionhome(); 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome</title>
    <link rel="stylesheet" href="/4a_edma/public/lib/css/bootstrap.min.css">
    <script defer src="/4a_edma/public/lib/js/bootstrap.bundle.min.js"></script>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="/edma/public/lib/js/my.js"></script> <!-- Adjust the path to your my.js file -->
</head>
<body>
    <nav id="userAdmin"></nav>
</body>
</html>