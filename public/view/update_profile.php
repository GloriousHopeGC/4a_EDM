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
    <script src="/4a_edma/public/lib/js/swal.js"></script>
    <link rel="stylesheet" href="/4a_edma/public/lib/css/sweetalert2.min.css">
    <script defer src="/4a_edma/public/lib/js/sweetalert2.all.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>
<nav id="userData"></nav>

<div class="container mt-5">
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
                    <div class="text-center">
                    </div>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item"><strong>Name:</strong> <?php echo $userName; ?></li>
                        <li class="list-group-item"><strong>Gender:</strong> <?php echo $userGender; ?></li>
                        <li class="list-group-item"><strong>Birthday:</strong> <?php echo $userBirthday; ?></li>
                        <li class="list-group-item"><strong>Age:</strong> <?php echo $age; ?> years old</li> <!-- Added age -->
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-8">
        <div id="postsContainer"></div>

        </div>
    </div>
</div>
<script>
  $(document).ready(function () {
    // Extract the user ID from the URL
    const params = new URLSearchParams(window.location.search);
    const userId = params.get('id');

    // Function to format the date
    function formatDateTo12Hour(dateString) {
        const date = new Date(dateString);
        const options = { 
            year: 'numeric', 
            month: 'long', 
            day: 'numeric', 
            hour: 'numeric', 
            minute: '2-digit',  
            hour12: true 
        };
        return date.toLocaleString('en-US', options);
    }

    if (userId) {
        // Fetch posts for the user
        $.ajax({
            url: "/edma/src/controller/fetch_profilepost.php", // Update with your PHP file path
            type: "GET",
            data: { id: userId },
            dataType: "json",
            success: function (response) {
                if (response.success) {
                    const posts = response.posts;
                    let postsHTML = "";

                    posts.forEach(post => {
                        // Format the date here inside the loop
                        const formattedDate = formatDateTo12Hour(post.created_at);

                        // Check for the file type and display accordingly
                        let fileDisplay = "";
                        if (post.file_name) {
                            const fileExtension = post.file_type.split('/')[1]; // Extract file extension (e.g., 'jpg', 'mp4', 'pdf')

                            // Display images
                            if (['gif', 'png', 'jpeg', 'jpg'].includes(fileExtension)) {
                                fileDisplay = `
                                    <img src="../../public/lib/images/posts/${post.file_name}" alt="Image" class="img-fluid rounded mb-3 full-width-media" style="max-width: 100%; height: auto;">
                                `;
                            }
                            // Display videos (MP4)
                            else if (fileExtension === 'mp4') {
                                fileDisplay = `
                                    <video controls class="w-100 rounded mb-3 full-width-media">
                                        <source src="../../public/lib/images/posts/${post.file_name}" type="video/mp4">
                                        Your browser does not support the video tag.
                                    </video>
                                `;
                            }
                            // Display audio (MP3)
                            else if (fileExtension === 'audio/mpeg', 'audio/wav') {
                                fileDisplay = `
                                    <audio controls class="w-100 rounded mb-3 full-width-media">
                                        <source src="../../public/lib/images/posts/${post.file_name}" type="audio/mpeg">
                                        Your browser does not support the audio element.
                                    </audio>
                                `;
                            }
                            // Display documents (PDF, DOCX, PPTX)
                            else if (['pdf', 'docx', 'pptx'].includes(fileExtension)) {
                                fileDisplay = `
                                    <a href="../../public/lib/images/posts/${post.file_name}" target="_blank" class="btn btn-link">
                                        Download ${fileExtension.toUpperCase()} file
                                    </a>
                                `;
                            }
                        }

                        // Create post HTML with file display
                        postsHTML += `
                            <div class="card mb-3" style="max-width: 540px; margin: auto;">
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-3">
                                        <img src="../../public/lib/images/user_profile/${post.image_name}" alt="${post.name}" class="img-fluid rounded-circle" style="width: 40px; height: 40px; margin-right: 10px;">
                                        <h6 class="card-title text-truncate user-name" data-user-id="${post.u_id}" style="cursor: pointer;">
                                            ${post.name || 'Unknown'}
                                        </h6>
                                    </div>
                                    <p class="card-text text-truncate">${post.content}</p>
                                    <small class="text-muted d-block mb-3">Posted on ${formattedDate}</small>
                                    ${fileDisplay} <!-- Insert file display here -->
                                </div>
                            </div>
                        `;
                    });

                    // Append posts to the container
                    $('#postsContainer').html(postsHTML);
                } else {
                    $('#postsContainer').html(`<p>${response.message}</p>`);
                }
            },
            error: function (error) {
                console.error("Error fetching posts:", error);
                $('#postsContainer').html(`<p>Failed to load posts. Please try again later.</p>`);
            }
        });
    } else {
        $('#postsContainer').html(`<p>No user ID found in the URL.</p>`);
    }
});

    </script>
</body>
</html>
