<?php
session_start();
require_once '../../src/controller/view_profile.php';
require_once '../../src/controller/userController.php';
$controller = new userController();
$userData = $controller->getUserData();
if (isset($userData['error'])) {
    // Handle the error (e.g., user not found or not logged in)
    echo "<p>" . htmlspecialchars($userData['error']) . "</p>";
    header('Location: login.php'); // Redirect to login if not logged in
    exit();
}
$user = $userData['user'];
$user_info = $userData['user_info'];
$controller->handleLogoutAction(); 
$currentUserId = $_SESSION['user_id'] ?? null;

// Calculate age based on birthday
$birthdate = new DateTime($userBirthday);  // Assuming $userBirthday is in 'Y-m-d' format
$today = new DateTime();
$age = $today->diff($birthdate)->y;
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link rel="stylesheet" href="/edma/public/lib/css/bootstrap.min.css">
    <link rel="stylesheet" href="/edma/public/lib/css/bootstrap.min.css">
    <script defer src="/edma/public/lib/js/bootstrap.bundle.min.js"></script>
    <link rel="icon" href="/edma/public/lib/images/AGCOWC TRANSPARENT.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="/edma/public/lib/css/edit.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="/edma/public/lib/js/my.js"></script>
    <script src="/edma/public/lib/js/my2.js"></script>
    <script src="/4a_edma/public/lib/js/swal.js"></script>
    <link rel="stylesheet" href="/4a_edma/public/lib/css/sweetalert2.min.css">
    <script defer src="/4a_edma/public/lib/js/sweetalert2.all.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
<nav id="userData" data-user-name="<?php echo $userName; ?>" data-user-gender="<?php echo $userGender; ?>" data-user-birthday="<?php echo $userBirthday; ?>" data-user-age="<?php echo $age; ?>"></nav>

<div class="container" style="margin-top:80px;">
    <div class="row">
        <div class="col-md-6 col-lg-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mx-auto">User Details</h5>
                    </div>
                    <div class="text-center mb-3">
                        <img src="../../public/lib/images/user_profile/<?php echo $userProfileImage; ?>" alt="User Image" class="rounded-circle" style="width: 150px; height: 150px; object-fit: cover;" data-bs-toggle="modal" data-bs-target="#changeImageModal">
                    </div>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item" id="userName"><strong>Name:</strong></li>
                        <li class="list-group-item" id="userGender"><strong>Gender:</strong></li>
                        <li class="list-group-item" id="userBirthday"><strong>Birthday:</strong></li>
                        <li class="list-group-item" id="userAge"><strong>Age:</strong></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-8 mt-2">
            <div id="postsContainer"></div>
        </div>
    </div>
</div>

</body>
</html>

