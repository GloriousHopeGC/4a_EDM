$(document).ready(function() {
    console.log("my.js is loaded and ready!");

    // Handle registration form submission
    $('#registerForm').on('submit', function(e) {
        e.preventDefault(); // Prevent the form from submitting the traditional way

        // Gather form data
        var formData = $(this).serialize(); // Serialize form data for AJAX

        // Send the form data via AJAX
        $.ajax({
            url: '/edma/src/controller/registerController.php', // Target the new controller for processing
            type: 'POST',
            dataType: 'json', // Expect a JSON response
            data: formData,
            success: function(response) {
                if (response.status === 'success') {
                    Swal.fire({
                        title: 'Success',
                        text: response.message,
                        icon: response.icon // Use the icon from the response
                    }).then(() => {
                        window.location.href = 'login.php';
                    });
                    // If registration is successful, show message and redirect
                    // alert(response.message);
                    // window.location.href = 'login.php'; // Redirect to login page
                } else {
                    // If registration fails, show the error message
                    Swal.fire({
                        title: 'Error',
                        text: response.message,
                        icon: response.icon // Use the icon from the response
                    });
                    // alert(response.message);
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                // Debugging: Show the full response in the console
                console.log("AJAX error: ", textStatus, errorThrown);
                alert('There was an error processing your request. Please try again.');
            }
        });
    });

   $('#loginForm').on('submit', function(e) {
    e.preventDefault(); // Prevent the form from submitting the traditional way

    console.log("Form submission intercepted!");

    // Gather form data
    var email = $('#email').val();
    var password = $('#password').val();

    // Send the form data via AJAX
    $.ajax({
        url: '/edma/src/controller/loginController.php', // Target the new backend file
        type: 'POST',
        dataType: 'json', // Expect a JSON response
        data: {
            email: email,
            password: password
        },
        success: function(response) {
            console.log("Response received: ", response);

            if (response.status === 'success') {
                Swal.fire({
                    title: 'Success',
                    text: response.message,
                    icon: response.icon // Use the icon from the response
                }).then(() => {
                    window.location.href = response.redirect_url;
                });
            } else {
                Swal.fire({
                    title: 'Error',
                    text: response.message,
                    icon: response.icon // Use the icon from the response
                });
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.log("AJAX error: ", textStatus, errorThrown);
            console.log("Response Text: ", jqXHR.responseText);
            Swal.fire({
                title: 'Error',
                text: 'There was an error processing your request. Please try again.',
                icon: 'error'
            });
        }
    });
});


    // Fetch user data via AJAX
    $.ajax({
        url: '/edma/src/controller/fetchdata.php',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.error) {
                $('#userData').html('<div class="alert alert-danger">' + response.error + '</div>');
            } else {
                $('#userData').html(`
                <nav class="navbar navbar-expand-lg navbar-light bg-light">
                <div class="container">
                    <a class="navbar-brand" href="update_user.php">${response.user_info.name}</a>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarNav">
                        <ul class="navbar-nav ms-auto">
                            <li class="nav-item">
                                <a class="nav-link active" aria-current="page" href="home.php">Home</a>
                            </li>
                            <li class="nav-item d-block d-lg-none">
                                <a class="nav-link" href="update_user.php">Profile</a>
                            </li>
                            <li class="nav-item d-block d-lg-none">
                                <a class="nav-link" href="#" onclick="confirmLogout()">Logout</a>
                            </li>
                            <!-- Dropdown Menu -->
                            <li class="nav-item dropdown d-none d-lg-block">
                                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <img src="../lib/images/user_profile/${response.user_info.image_name}" alt="Profile" class="rounded-circle" style="width: 30px; height: 30px;">
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                                    <li><a class="dropdown-item" href="update_user.php"><i class="bi-person-circle"></i> Profile</a></li>
                                    <li><a class="dropdown-item" href="#" onclick="confirmLogout()"><i class="bi bi-door-open"></i> Log-out</a></li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>
                `);
                $('#userInfo').html(`
                   <div class="container mt-5">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title text-center">User Details</h5>
                                    <div class="text-center mb-3">
                                        <img src="../lib/images/user_profile/${response.user_info.image_name}" alt="User Image" class="rounded-circle" style="width: 150px; height: 150px; object-fit: cover;" data-bs-toggle="modal" data-bs-target="#changeImageModal">
                                            <i class="fas fa-camera fa-2x" data-bs-toggle="modal" data-bs-target="#changeImageModal" style="cursor: pointer;"></i>
                                    </div>
                                    <div class="text-center">
                                            <p id="dummyText">Id: ${(response.user_info.u_id).toString().padStart(5, '0')}-${(new Date(response.user_info.created).getMonth() + 1).toString().padStart(3, '0')}-${new Date(response.user_info.created).getFullYear()}</p>
                                          </div>
                                        <ul class="list-group list-group-flush">
                                            <li class="list-group-item"><strong>Name:</strong> ${response.user_info.name}</li>
                                            <li class="list-group-item"><strong>Email:</strong>  ${response.user.email}</li>
                                            <li class="list-group-item"><strong>Gender:</strong> ${response.user_info.gender}</li>
                                            <li class="list-group-item"><strong>Birthday:</strong> ${response.user_info.birthday}</li>
                                            <li class="list-group-item"><strong>Address:</strong> ${response.user_info.address}</li>
                                        </ul>
                                        <div class="text-center mt-4">
                                        <li class="nav-item dropdown d-block d-lg-block">
                                            <a class="nav-link dropdown-toggle" href="#" id="settingsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                               <i class="bi bi-gear"></i> Account Settings
                                            </a>
                                            <ul class="dropdown-menu" aria-labelledby="settingsDropdown"  style="cursor: pointer;">
                                                <li><a class="dropdown-item" data-bs-toggle="modal" data-bs-target="#editModal"><i class="bi bi-pencil"></i> Edit Profile</a></li>
                                                <li><a class="dropdown-item" data-bs-toggle="modal" data-bs-target="#changePassword"><i class="bi bi-key"></i> Change Password</a></li>
                                                 <li><a class="dropdown-item" data-bs-toggle="modal" data-bs-target="#changeEmailModal"><i class="bi bi-envelope"></i> Change Email</a></li>
                                            </ul>
                                        </li>
                                           <!-- <button class="btn btn-custom" data-bs-toggle="modal" data-bs-target="#editModal">Edit Profile</button>
                                            <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#changePassword">Change Password</button>
                                            <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#changeEmailModal">Change Email</button>  -->
                                        </div>
                            </div>
                        </div>
                    </div>
                    <!-- Change Password Modal -->
                    <div class="modal fade" id="changePassword" tabindex="-1" aria-labelledby="changePasswordLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="changePasswordLabel">Change Password</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                    <div class="modal-body">
                                        <form id="changePasswordForm" method="POST">
                                            <input type="hidden" name="user_id" value="${response.user_info.u_id}">
                                             <div class="mb-3">
                                                <label for="currentPassword" class="form-label">Current Password</label>
                                                <input type="password" class="form-control" id="currentPassword" name="currentPassword" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="newPassword" class="form-label">New Password</label>
                                                <input type="password" class="form-control" id="newPassword" name="newPassword" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="confirmPassword" class="form-label">Confirm New Password</label>
                                                <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" required>
                                            </div>
                                                    <button type="submit" class="btn btn-primary">Change Password</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <!-- Change Email Modal -->
<div class="modal fade" id="changeEmailModal" tabindex="-1" aria-labelledby="changeEmailLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="changeEmailLabel">Change Email</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="changeEmailForm" method="POST">
                    <input type="hidden" name="user_id" id="user_id" value="${response.user_info.u_id}">
                    <div class="mb-3">
                        <label for="newEmail" class="form-label">New Email</label>
                        <input type="email" class="form-control" id="newEmail" name="newEmail" >
                    </div>
                    <button type="submit" class="btn btn-primary">Update Email</button>
                </form>
            </div>
        </div>
    </div>
</div>

                    <!-- Change Image Modal -->
                    <div class="modal fade" id="changeImageModal" tabindex="-1" aria-labelledby="changeImageModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="changeImageModalLabel">Change Profile Image</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form id="changeImageForm" method="POST" enctype="multipart/form-data">
                                        <input type="hidden" name="user_id" value="${response.user_info.u_id}">
                                        <div class="mb-3">
                                            <label for="profileImage" class="form-label">Choose New Profile Image</label>
                                            <input type="file" class="form-control" name="profileImage" id="profileImage">
                                        </div>
                                    <button type="submit" class="btn btn-primary">Upload Image</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Edit Profile -->
                     <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="editModalLabel">Edit Profile</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form id="editForm" method="POST">
                                        <input type="hidden" name="user_id" value="${response.user_info.u_id}"> <!-- Hidden user_id -->
                                            <div class="mb-3">
                                                <label for="editName" class="form-label">Name</label>
                                                <input type="text" class="form-control" id="name" name="name" value="${response.user_info.name}" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="editGender" class="form-label">Gender</label>
                                                <select class="form-select" id="gender" name="gender" required>
                                                    <option value="Male" ${response.user_info.gender === 'Male' ? 'selected' : ''}>Male</option>
                                                    <option value="Female" ${response.user_info.gender === 'Female' ? 'selected' : ''}>Female</option>
                                                    <option value="Other" ${response.user_info.gender === 'Other' ? 'selected' : ''}>Other</option>
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label for="editBirthday" class="form-label">Birthday</label>
                                                <input type="date" class="form-control" id="birthday" name="birthday" value="${response.user_info.birthday}" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="editAddress" class="form-label">Address</label>
                                                <input type="text" class="form-control" id="address" name="address" value="${response.user_info.address}">
                                            </div>
                                            <button type="submit" class="btn btn-primary">Save Changes</button>
                                    </form>

                                </div>
                            </div>
                        </div>
                    </div>
                    `);

                        $('#adminPost').html(`
                            <div class="container mt-5">
                                <div class="card">
                                    <div class="card-body">
                                        <form id="postForm" method="POST" enctype="multipart/form-data">
                                            <input type="hidden" name="u_id" value="${response.user_info.u_id}">
                                            <input type="hidden" name="ui_id" value="${response.user_info.id}">
                                            <div class="mb-3">
                                                <label for="postContent" class="form-label">Create Post</label>
                                                <textarea class="form-control" id="postContent" name="postContent" rows="4"></textarea>
                                            </div>
                                            <div class="mb-3">
                                                <label for="postFile" class="form-label">Upload File</label>
                                                <input type="file" class="form-control" id="postFile" name="postFile">
                                            </div>
                                            <button type="submit" class="btn btn-primary">Create Post</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                           
                        `);
                        $('#adminPostlist').html(`
                           <div id="postsContainer" class="mt-5">
                                <div id="postsList"></div>
                            </div>
                        `);
                            // Fetch posts
                            $(document).ready(function() {
                                // Fetch posts
                                function fetchPosts() {
                                    $.ajax({
                                        url: '/edma/src/controller/fetch_post.php',
                                        type: 'GET',
                                        dataType: 'json',
                                        success: function(response) {
                                            if (response.status === 'success') {
                                                const posts = response.posts;
                                                let postsHtml = '';
                                                const currentUserId = $('meta[name="current-user-id"]').attr('content'); // Assuming a meta tag for user ID
                                                console.log('Current User ID:', currentUserId);
                            
                                                if (posts.length > 0) {
                                                    posts.forEach(post => {
                                                        const formattedDate = formatDateTo12Hour(post.created_at);
                            
                                                        postsHtml += `
                                                       <div class="card mb-3" style="max-width: 540px; margin: auto;">
                                                        <div class="card-body">
                                                            <!-- Admin Info -->
                                                            <div class="d-flex align-items-center mb-3">
                                                                <img src="../../public/lib/images/user_profile/${post.image_name}" alt="${post.admin_name}" class="img-fluid rounded-circle" style="width: 40px; height: 40px; margin-right: 10px;">
                                                                <h5 class="card-title text-truncate">${post.admin_name || 'Unknown'}</h5>
                                                            </div>
                        
                                                            <!-- Post Content -->
                                                            <p class="card-text text-truncate">${post.content}</p>
                                                            <small class="text-muted d-block mb-3">Posted on ${formattedDate}</small>
                        
                                                            <!-- Media Display -->
                                                            ${post.file_name && post.file_type.startsWith('image/') ? 
                                                                `<img src="../../public/lib/images/posts/${post.file_name}" alt="${post.title}" class="img-fluid rounded mb-3 full-width-media">`
                                                            : post.file_type === 'video/mp4' ? 
                                                                `<video controls class="w-100 rounded mb-3 full-width-media">
                                                                    <source src="../../public/lib/images/posts/${post.file_name}" type="video/mp4">
                                                                    Your browser does not support the video tag.
                                                                </video>`
                                                            : post.file_type === 'audio/mpeg' || post.file_type === 'audio/wav' ? 
                                                                `<audio controls class="w-100 rounded mb-3 full-width-media">
                                                                    <source src="../../public/lib/images/posts/${post.file_name}" type="${post.file_type}">
                                                                    Your browser does not support the audio element.
                                                                </audio>`
                                                                : post.file_name && 
                                                                (post.file_type === 'application/pdf' || 
                                                                post.file_type === 'application/vnd.openxmlformats-officedocument.wordprocessingml.document' || 
                                                                post.file_type === 'application/vnd.openxmlformats-officedocument.presentationml.presentation') ?
                                                                // Check file type and display its name instead of "Download File"
                                                                `<a href="../../public/lib/images/posts/${post.file_name}"target="_blank" class="btn btn-link">${post.file_name.replace(/^\d+_/, '')}</a>`
                                                            : post.file_name ? 
                                                                `<p class="mb-0">
                                                                    <a href="../../public/lib/images/posts/${post.file_name}" target="_blank" class="btn btn-link">Download File</a>
                                                                </p>`
                                                            : ''}
                                                                    <input type="hidden" name="user_id" value="${post.u_id}">
                                                                   
                            
                                                                <!-- Dropdown for delete action (visible only to matching user) -->
                                                                ${post.u_id == currentUserId ? `
                                                                    <div class="dropdown position-absolute top-0 end-0 p-2">
                                                                   <i class="bi bi-three-dots mr-3" style="font-size: 20px;" dropdown-toggle="true" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false"></i>
                                                                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                                            <li><a class="dropdown-item delete-post" href="#" data-post-id="${post.post_id}">Delete</a></li>
                                                                        </ul>
                                                                    </div>
                                                                ` : ''}
                                                            </div>
                                                        </div>
                                                        `;
                                                    });
                                                } else {
                                                    postsHtml = '<p>No posts available.</p>';
                                                }
                            
                                                $('#postsList').html(postsHtml);
                                            } else {
                                                console.error('Error fetching posts:', response.message);
                                            }
                                        },
                                        error: function(jqXHR, textStatus, errorThrown) {
                                            console.error("AJAX error: ", textStatus, errorThrown);
                                        }
                                    });
                            }
                        
                            // Function to format date to 12-hour format
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
                        // Function to delete a post
                        function deletePost(postId) {
                            console.log("Deleting post with ID:", postId); // Log the ID before making the AJAX call
                        
                            // Confirm deletion using SweetAlert
                            Swal.fire({
                                title: 'Are you sure?',
                                text: 'Do you really want to delete this post? This action cannot be undone.',
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonText: 'Yes, delete it!',
                                cancelButtonText: 'Cancel',
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    // Proceed with deletion if confirmed
                                    $.ajax({
                                        url: '/edma/src/controller/delete_post.php',
                                        type: 'POST',
                                        data: { id: postId },
                                        dataType: 'json', // Ensures the response is parsed as JSON
                                        success: function(response) {
                                            console.log("Response from server:", response); // Debug response
                        
                                            if (response.status === 'success') {
                                                Swal.fire({
                                                    title: 'Deleted!',
                                                    text: 'Post deleted successfully!',
                                                    icon: 'success',
                                                    confirmButtonText: 'OK',
                                                });
                        
                                                fetchPosts(); // Refresh the posts list after deletion
                                            } else {
                                                const errorMessage = response.message || 'Unknown error';
                                                Swal.fire({
                                                    title: 'Error',
                                                    text: 'Error deleting post: ' + errorMessage,
                                                    icon: 'error',
                                                    confirmButtonText: 'OK',
                                                });
                                            }
                                        },
                                        error: function(jqXHR, textStatus, errorThrown) {
                                            console.error("AJAX error while deleting post: ", textStatus, errorThrown);
                                            Swal.fire({
                                                title: 'Error',
                                                text: 'AJAX error: ' + textStatus,
                                                icon: 'error',
                                                confirmButtonText: 'OK',
                                            });
                                        },
                                    });
                                }
                            });
                        }
                        
                        // Use event delegation to handle clicks for dynamic elements
                        $(document).on('click', '.delete-post', function(e) {
                            e.preventDefault(); // Prevent default anchor behavior
                            const postId = $(this).data('post-id'); // Retrieve post ID
                            deletePost(postId);
                        });
                        

                            fetchPosts();
                        });
                        
                        
                        
                        $('#postForm').on('submit', function(e) {
                            e.preventDefault(); // Prevent the form from submitting traditionally
                    
                            // Gather form data
                            var formData = new FormData(this); // Use FormData to handle file uploads
                    
                            // Validate file types (images, pdf, docx, ppt, audio)
                            var fileInput = $('#postFile')[0];
                            var file = fileInput.files[0];
                            var allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'application/pdf', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',  'application/vnd.openxmlformats-officedocument.presentationml.presentation', 'audio/mpeg', 'audio/wav', 'video/mp4'];
                            
                            if (file && !allowedTypes.includes(file.type)) {
                                Swal.fire({
                                    title: 'Error',
                                    text: 'Invalid file type. Please upload an image, PDF, DOCX, PowerPoint, or audio file.',
                                    icon: 'error'
                                });
                                return;
                            }
                            
                            
                            // Send the form data via AJAX
                            $.ajax({
                                url: '/edma/src/controller/post.php', // Target the controller for processing the post
                                type: 'POST',
                                dataType: 'json',
                                data: formData,
                                processData: false,
                                contentType: false,
                                success: function(response) {
                                    if (response.status === 'success') {
                                        Swal.fire({
                                            title: 'Success',
                                            text: response.message,
                                            icon: response.icon
                                        }).then(() => {
                                            window.location.href = 'admin.php'; // Redirect to admin dashboard after success
                                        });
                                    } else {
                                        Swal.fire({
                                            title: 'Error',
                                            text: response.message,
                                            icon: response.icon
                                        });
                                    }
                                },
                                error: function(jqXHR, textStatus, errorThrown) {
                                    console.log("AJAX error: ", textStatus, errorThrown);
                                    Swal.fire({
                                        title: 'Error',
                                        text: 'There was an error processing your request. Please try again.',
                                        icon: 'error'
                                    });
                                }
                            });
                        });
                    $('#editForm').on('submit', function(e) {
                        e.preventDefault(); // Prevent the default form submission
                        console.log("Edit form submitted!"); // Check if this line is reached
                        var editFormData = $(this).serialize();
                        $.ajax({
                            url: '/edma/src/controller/updateuser.php', 
                            type: 'POST',
                            dataType: 'json', // Ensure this is set to 'json'
                            data: editFormData, 
                            success: function(response) {
                                console.log("Response received: ", response); // Check what response you are getting
                                if (response && response.status === 'success') { // Check if response is defined
                                    Swal.fire({
                                        title: 'Success',
                                        text: response.message,
                                        icon: response.icon // Use the icon from the response
                                    }).then(() => {
                                        location.reload();
                                    });
                                    // alert(response.message);
                                    // location.reload(); // Refresh the page after a successful update
                                } else {
                                    Swal.fire({
                                        title: 'Error',
                                        text: response.message,
                                        icon: response.icon // Use the icon from the response
                                    });
                                    // alert(response.message); // Handle any error message returned
                                }
                            },
                            error: function(jqXHR, textStatus, errorThrown) {
                                console.log("AJAX error: ", textStatus, errorThrown);
                                console.log("Response Text: ", jqXHR.responseText); // Log the full response text
                                alert('There was an error processing your request. Please try again.');
                            }
                        });
                        
                        
                    });
                }
            },
        error: function(xhr, status, error) {
            $('#userData').html('<div class="alert alert-danger">Failed to fetch user data.</div>');
        }
    });
    
    
    $(document).on('submit', '#changePasswordForm', function(e)
    /* $('#changePasswordForm').on('submit', function(e)*/ {
        e.preventDefault(); // Prevent the default form submission
        console.log('Form submitted, method is POST');
        const formData = $(this).serialize(); // Serialize the form data
        console.log('Form Data:', formData); // Check what is being sent
        
        $.ajax({
            url: 'http://localhost/edma/src/controller/change_password.php',// Correct path
            type: 'POST', // Ensure POST method is used
            dataType: 'json', // Expect JSON response
            data: formData, // Send serialized form data
            success: function(response) {
                console.log('Response:', response);
                if (response.success) {
                    Swal.fire({
                        title: 'Success',
                        text: 'Password changed successfully!',
                        icon: 'success'
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        title: 'Error',
                        text: response.error || response.message || 'An error occurred.',
                        icon: 'error'
                    });
                }
            },
            error: function(xhr, status, error) {
                // Handle AJAX errors
                console.log('AJAX Error:', status, error);
                Swal.fire({
                    title: 'Error',
                    text: 'An error occurred while processing your request.',
                    icon: 'error'
                });
            }
        });
    });
   
    $(document).on('submit', '#changeEmailForm', function (e) {
        e.preventDefault(); // Prevent default form submission
        const formData = $(this).serialize(); // Serialize form data for AJAX
    
        console.log('Email Change Request:', formData); // Debugging: Check data being sent
    
        $.ajax({
            url: '/edma/src/controller/change_email.php', // Backend controller for email change
            type: 'POST', // Use POST to securely send data
            dataType: 'json', // Expect JSON response
            data: formData, // Send serialized form data
            success: function (response) {
                console.log('Response:', response); // Debugging: Check the response
                if (response.status === 'success') {
                    Swal.fire({
                        title: 'Success',
                        text: response.message,
                        icon: 'success'
                    }).then(() => {
                        location.reload(); // Reload to update UI or fetch updated data
                    });
                } else {
                    Swal.fire({
                        title: 'Error',
                        text: response.message || 'An error occurred.',
                        icon: 'error'
                    });
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.log('AJAX Error:', textStatus, errorThrown);
                Swal.fire({
                    title: 'Error',
                    text: 'An error occurred while processing your request.',
                    icon: 'error'
                });
            }
        });
    });
    
    $.ajax({
        url: '/edma/src/controller/fetchdata.php',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.error) {
                $('#userAdmin').html('<div class="alert alert-danger">' + response.error + '</div>');
            } else {
                $('#userAdmin').html(`
                <nav class="navbar navbar-expand-lg navbar-light bg-light">
                <div class="container">
                    <a class="navbar-brand" href="update_user.php">${response.user_info.name}</a>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarNav">
                        <ul class="navbar-nav ms-auto">
                            <li class="nav-item">
                                <a class="nav-link active" aria-current="page" href="home.php">Home</a>
                            </li>
                            <li class="nav-item d-block d-lg-none">
                                <a class="nav-link" href="update_user.php">Profile</a>
                            </li>
                            <li class="nav-item d-block d-lg-none">
                                <a class="nav-link" href="#" onclick="confirmLogout()">Logout</a>
                            </li>
                            <!-- Dropdown Menu -->
                            <li class="nav-item dropdown d-none d-lg-block">
                                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <img src="../lib/images/user_profile/${response.user_info.image_name}" alt="Profile" class="rounded-circle" style="width: 30px; height: 30px;">
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                                    <li><a class="dropdown-item" href="update_user.php"><i class="bi-person-circle"></i> Profile</a></li>
                                    <li><a class="dropdown-item" href="#" onclick="confirmLogout()"><i class="bi bi-door-open"></i> Log-out</a></li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>
                `);
                $('#userAdminInfo').html(`
                   <div class="container mt-5">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title text-center">User Details</h5>
                                    <div class="text-center mb-3">
                                        <img src="../lib/images/user_profile/${response.user_info.image_name}" alt="User Image" class="rounded-circle" style="width: 150px; height: 150px; object-fit: cover;" data-bs-toggle="modal" data-bs-target="#changeImageModal">
                                            <i class="fas fa-camera fa-2x" data-bs-toggle="modal" data-bs-target="#changeImageModal" style="cursor: pointer;"></i>
                                    </div>
                                        <ul class="list-group list-group-flush">
                                            <li class="list-group-item"><strong>Name:</strong> ${response.user_info.name}</li>
                                            <li class="list-group-item"><strong>Email:</strong>  ${response.user.email}</li>
                                            <li class="list-group-item"><strong>Gender:</strong> ${response.user_info.gender}</li>
                                            <li class="list-group-item"><strong>Birthday:</strong> ${response.user_info.birthday}</li>
                                            <li class="list-group-item"><strong>Address:</strong> ${response.user_info.address}</li>
                                        </ul>
                                        <div class="text-center mt-4">
                                            <button class="btn btn-custom" data-bs-toggle="modal" data-bs-target="#editModal">Edit Profile</button>
                                            <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#changePassword">Change Password</button>
                                            <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#changeEmail">Change Email</button>
                                        </div>
                             </div>
                        </div>
                    </div>
                     <!-- Change Image Modal -->
                    <div class="modal fade" id="changeImageModal" tabindex="-1" aria-labelledby="changeImageModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="changeImageModalLabel">Change Profile Image</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form id="changeImageForm" method="POST" enctype="multipart/form-data">
                                        <input type="hidden" name="user_id" value="${response.user_info.u_id}">
                                        <div class="mb-3">
                                            <label for="profileImage" class="form-label">Choose New Profile Image</label>
                                            <input type="file" class="form-control" name="profileImage" id="profileImage">
                                        </div>
                                    <button type="submit" class="btn btn-primary">Upload Image</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Change Password Modal -->
                    <div class="modal fade" id="changePassword" tabindex="-1" aria-labelledby="changePasswordLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="changePasswordLabel">Change Password</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                    <div class="modal-body">
                                        <form id="changePasswordForm" method="POST">
                                            <input type="hidden" name="user_id" value="${response.user_info.u_id}">
                                             <div class="mb-3">
                                                <label for="currentPassword" class="form-label">Current Password</label>
                                                <input type="password" class="form-control" id="currentPassword" name="currentPassword" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="newPassword" class="form-label">New Password</label>
                                                <input type="password" class="form-control" id="newPassword" name="newPassword" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="confirmPassword" class="form-label">Confirm New Password</label>
                                                <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" required>
                                            </div>
                                                    <button type="submit" class="btn btn-primary">Change Password</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="editModalLabel">Edit Profile</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                    <div class="modal-body">
                                        <form id="editAdminForm" method="POST">
                                            <input type="hidden" name="user_id" value="${response.user_info.u_id}"> <!-- Hidden user_id -->
                                            <div class="mb-3">
                                                <label for="editName" class="form-label">Name</label>
                                                <input type="text" class="form-control" id="name" name="name" value="${response.user_info.name}" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="editGender" class="form-label">Gender</label>
                                                <select class="form-select" id="gender" name="gender" required>
                                                    <option value="Male" ${response.user_info.gender === 'Male' ? 'selected' : ''}>Male</option>
                                                    <option value="Female" ${response.user_info.gender === 'Female' ? 'selected' : ''}>Female</option>
                                                    <option value="Other" ${response.user_info.gender === 'Other' ? 'selected' : ''}>Other</option>
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label for="editBirthday" class="form-label">Birthday</label>
                                                <input type="date" class="form-control" id="birthday" name="birthday" value="${response.user_info.birthday}" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="editAddress" class="form-label">Address</label>
                                                <input type="text" class="form-control" id="address" name="address" value="${response.user_info.address}">
                                            </div>
                                            <button type="submit" class="btn btn-primary">Save Changes</button>
                                        </form>
                                    </div>
                            </div>
                        </div>
                    </div>
                    `);
                    $('#changeImageForm').on('submit', function(e) {
                        e.preventDefault(); // Prevent the default form submission
                    
                        // Prepare form data including the file
                        var formData = new FormData(this); // 'this' includes profileImage and user_id from the form
                    
                        $.ajax({
                            url: '/edma/src/controller/updateProfileImage.php',
                            type: 'POST',
                            dataType: 'json', // Expecting JSON response
                            data: formData,
                            contentType: false,  // Let the browser set the content type (important for file uploads)
                            processData: false,  // Don't process the data (important for file uploads)
                            success: function(response) {
                                console.log("Response from server:", response); // Log response for debugging
                                if (response.status === 'success') {
                                    Swal.fire({
                                        title: 'Success',
                                        text: response.message,
                                        icon: response.icon // Use the icon from the response
                                    }).then(() => {
                                        location.reload();
                                    });
                                    // alert(response.message);  // Alert the success message
                                    // location.reload();  // Reload the page to reflect the changes
                                } else {
                                    Swal.fire({
                                        title: 'Error',
                                        text: response.message,
                                        icon: response.icon // Use the icon from the response
                                    });
                                    // alert(response.message);  // Show error message if upload fails
                                }
                            },
                            error: function(jqXHR, textStatus, errorThrown) {
                                console.log("AJAX error: ", textStatus, errorThrown);
                                alert('There was an error processing your request. Please try again.');
                            }
                        });
                    });
                    

                    $('#editAdminForm').on('submit', function(e) {
                        e.preventDefault(); // Prevent the default form submission
                        console.log("Edit form submitted!"); // Check if this line is reached
                        var editFormData = $(this).serialize();
                        $.ajax({
                            url: '/edma/src/controller/updateuser.php', 
                            type: 'POST',
                            dataType: 'json', // Ensure this is set to 'json'
                            data: editFormData, 
                            success: function(response) {
                                console.log("Response received: ", response); // Check what response you are getting
                                if (response && response.status === 'success') { // Check if response is defined
                                    Swal.fire({
                                        title: 'Success',
                                        text: response.message,
                                        icon: response.icon // Use the icon from the response
                                    }).then(() => {
                                        location.reload();
                                    });
                                    // alert(response.message);
                                    // location.reload(); // Refresh the page after a successful update
                                } else {
                                    Swal.fire({
                                        title: 'Error',
                                        text: response.message,
                                        icon: response.icon // Use the icon from the response
                                    });
                                    // alert(response.message); // Handle any error message returned
                                }
                            },
                            error: function(jqXHR, textStatus, errorThrown) {
                                console.log("AJAX error: ", textStatus, errorThrown);
                                console.log("Response Text: ", jqXHR.responseText); // Log the full response text
                                alert('There was an error processing your request. Please try again.');
                            }
                        });
                        
                        
                    });
                }
            },
        error: function(xhr, status, error) {
            $('#userData').html('<div class="alert alert-danger">Failed to fetch user data.</div>');
        }
    });
    
});
