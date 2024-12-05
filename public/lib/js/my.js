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
                <nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top">
                    <div class="container">
                        <a class="navbar-brand" href="update_user.php">${response.user_info.name}</a>
                            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                                <span class="navbar-toggler-icon"></span>
                            </button>
                                <div class="collapse navbar-collapse" id="navbarNav">
                                    <ul class="navbar-nav ms-auto"> <!-- Right-aligned items -->
                                        <li class="nav-item">
                                            <a class="nav-link active" aria-current="page" href="home.php">Home</a>
                                        </li>
                                            <li class="nav-item d-block d-lg-none">
                                                <a class="nav-link" href="update_user.php">Profile</a>
                                            </li>
                                            <li class="nav-item d-block d-lg-none">
                                                <a class="nav-link" href="#" onclick="confirmLogout()">Logout</a>
                                            </li>
                                            <li class="nav-item dropdown d-none d-lg-block">
                                                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <img src="../lib/images/user_profile/${response.user_info.image_name}" alt="Profile" class="rounded-circle" style="width: 30px; height: 30px;" />
                                                </a>
                                                <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                                            <li>
                                                <a class="dropdown-item" href="update_user.php">
                                                    <i class="bi-person-circle"></i> Profile
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item" href="#" onclick="confirmLogout()">
                                                    <i class="bi bi-door-open"></i> Log-out
                                                </a>
                                            </li>
                                        </ul>
                                    </li>
                                </ul>
                        </div>
                    </div>
                </nav>

                `);
                $('#searchBar').html(`
                    <div class="d-flex justify-content-center" style="margin-top:80px;">
                        <form class="d-flex" id="searchForm" method="GET" style="max-width: 542px; width: 100%;">
                            <input class="form-control me-2" type="search" placeholder="Search User" aria-label="Search" name="query" style="min-width: 280px; width: 100%;"/>
                             <!-- <button class="btn btn-outline-success" type="submit">Search</button> -->
                        </form>
                    </div>

                `);
                $('#searchForm input[name="query"]').on('input', function(e) {
                    var query = $(this).val().trim();  // Get the value of the input and remove any extra spaces
                
                    // Log the query to debug
                    console.log("Search Query: ", query);
                
                    if (!query) {
                        $('#searchResults').html('');  // Clear results when there's no query
                        $('#adminPostlist').show();  // Show the admin post list if search box is empty
                        $('#adminPost').show();      // Show the admin post content if search box is empty
                        return;
                    }
                
                    // Hide the admin post list when searching
                    $('#adminPostlist').hide();  
                    $('#adminPost').hide(); 
                    $('#userInfo').hide();  
                
                    $.ajax({
                        url: '/edma/src/controller/searchdata.php',  // Ensure this file returns JSON correctly
                        type: 'GET',
                        dataType: 'json',
                        data: { query: query },
                        success: function(response) {
                            let resultsHtml = '';
                            if (response.data && response.data.length > 0) {
                                response.data.forEach(function(row) {
                                    resultsHtml += `
                                      <tr data-user-id="${row.u_id}" class="user-row">
                                        <td>
                                            <div class="d-flex align-items-center user-name" data-user-id="${row.id}" style="cursor:pointer;">  <!-- use u_id here -->
                                                <img src="../lib/images/user_profile/${row.image_name}" alt="${row.name}" class="rounded-circle" style="width: 50px; height: 50px; margin-right: 10px; margin-top:20px; cursor:pointer;">
                                                <span style="color: #333;">${row.name}</span>
                                            </div>
                                        </td>
                                      </tr>
                                    `;
                                });
                                resultsHtml += '</tbody></table>';
                            } else {
                                resultsHtml = '<p>No Users Found.</p>';
                            }
                            $('#searchResults').html(resultsHtml);
                        },
                        error: function(xhr, status, error) {
                            console.error("AJAX Error: ", status, error);
                            $('#searchResults').html('<div class="alert alert-danger">There was an error with the search request.</div>');
                        }
                    });
                    $(document).on('click', '.user-row', function() {
                        const userId = $(this).data('user-id'); // Extract user ID from the clicked row
                        const currentUserId = $('meta[name="current-user-id"]').attr('content'); // Current user's ID from a meta tag
                        
                        if (userId === parseInt(currentUserId)) {
                            // Redirect to update_user.php if the IDs match
                            window.location.href = '../view/update_user.php';
                        } else {
                            // Otherwise, redirect to the profile page
                            window.location.href = `../view/update_profile.php?id=${userId}`;
                        }
                    });
                });
                
                
                
                $('#userInfo').html(`
                   <div class="container" style="margin-top:80px;">
                        <div class="row">
                            <!-- Left Column for User Info Card -->
                                <div class="col-md-6 col-lg-4">
                                    <div class="card">
                                        <div class="card-body">
                                            <!-- Flex container to center the title and position the dropdown to the right -->
                                                <div class="d-flex justify-content-between align-items-center">
                                            <!-- Centered title with flex-grow to take remaining space -->
                                                <h5 class="card-title mx-auto">User Details</h5>
                                                    <div class="dropdown">
                                                        <a class="nav-link dropdown-toggle" href="#" id="settingsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                            <i class="bi bi-gear"></i>
                                                        </a>
                                                        <ul class="dropdown-menu" aria-labelledby="settingsDropdown" style="cursor: pointer;">
                                                            <li><a class="dropdown-item" data-bs-toggle="modal" data-bs-target="#editModal"><i class="bi bi-pencil"></i> Edit Profile</a></li>
                                                            <li><a class="dropdown-item" data-bs-toggle="modal" data-bs-target="#changePassword"><i class="bi bi-key"></i> Change Password</a></li>
                                                            <li><a class="dropdown-item" data-bs-toggle="modal" data-bs-target="#changeEmailModal"><i class="bi bi-envelope"></i> Change Email</a></li>
                                                            <li><a class="dropdown-item" data-action="delete-account"><i class="bi bi-trash"></i> Delete Account</a></li>
                                                        </ul>
                                            </div>
                        </div>
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
                            <li class="list-group-item"><strong>Age:</strong> ${calculateAge(response.user_info.birthday)}</li>
                            <li class="list-group-item"><strong>Address:</strong> ${response.user_info.address}</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Right Column for User Posts -->
            <div class="col-md-6 col-lg-8">
            <div id="adminPostprofile"></div>
                <div id="postsContainer" class="mt-3">
                    <div id="postsLists"></div>
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
                    $(document).ready(function () {
                        // Event listener for the delete account option
                        $(document).on('click', '.dropdown-item[data-action="delete-account"]', function () {
                            Swal.fire({
                                title: 'Are you sure?',
                                text: "You won't be able to revert this action!",
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonColor: '#3085d6',
                                cancelButtonColor: '#d33',
                                confirmButtonText: 'Yes, delete it!',
                                cancelButtonText: 'Cancel'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    // AJAX request to delete the account
                                    $.ajax({
                                        url: '/edma/src/controller/delete_account.php', // The PHP script handling the deletion
                                        type: 'POST',
                                        data: { user_id: response.user_info.u_id }, // Send the user ID to the PHP script
                                        success: function (response) {
                                            // Parse the response
                                            const result = JSON.parse(response);
                                            if (result.success) {
                                                Swal.fire({
                                                    title: 'Deleted!',
                                                    text: 'Your account has been successfully deleted.',
                                                    icon: 'success',
                                                    confirmButtonText: 'OK'
                                                }).then(() => {
                                                    // Redirect to login page
                                                    window.location.href = "login.php";
                                                });
                                            } else {
                                                Swal.fire({
                                                    title: 'Error!',
                                                    text: result.message || 'An error occurred while deleting your account.',
                                                    icon: 'error',
                                                    confirmButtonText: 'OK'
                                                });
                                            }
                                        },
                                        error: function () {
                                            Swal.fire({
                                                title: 'Error!',
                                                text: 'Failed to delete the account. Please try again later.',
                                                icon: 'error',
                                                confirmButtonText: 'OK'
                                            });
                                        }
                                    });
                                }
                            });
                        });
                    });
                    
                    $('#adminPostprofile').html(`
                        <div class="d-flex justify-content-center">
                          <div class="card shadow-sm  mt-3" style="width: 100%; max-width: 540px; border-radius: 10px;">
                              <div class="card-body d-flex align-items-center">
                                  <!-- Profile Picture -->
                                      <img src="../lib/images/user_profile/${response.user_info.image_name}" alt="Profile Picture" class="rounded-circle" style="width: 40px; height: 40px; margin-right: 10px;">
                                  <!-- Input-like Button -->
                                      <button id="openModalBtn" data-bs-toggle="modal" data-bs-target="#postModal" class="btn btn-outline-white flex-grow-1 text-start d-flex align-items-center" style="border-radius: 20px; padding: 10px 15px; ; border-color: #444;">Post Now, ${response.user_info.name}</button>
                              </div>
                          </div>
                      </div>
                      <!-- Modal HTML -->
                      <div id="postModal" class="modal fade" tabindex="-1" aria-labelledby="postModalLabel" aria-hidden="true">
                          <div class="modal-dialog">
                              <div class="modal-content">
                                  <div class="modal-header">
                                      <h5 class="modal-title" id="postModalLabel">Create Post</h5>
                                          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                  </div>
                                  <div class="modal-body">
                                      <form id="postForm" method="POST" enctype="multipart/form-data">
                                          <input type="hidden" name="u_id" value="${response.user_info.u_id}">
                                          <input type="hidden" name="ui_id" value="${response.user_info.id}">
                                      <div class="mb-3">
                                          <label for="postContent" class="form-label">What Do You Want To Post?</label>
                                          <textarea class="form-control" id="postContent" name="postContent" rows="4"></textarea>
                                      </div>
                                      <div class="mb-3 d-flex align-items-center">
                                      <!-- Icon-Based Upload Button -->
                                          <label for="postFile" class="btn btn-outline-white d-flex align-items-center" style="cursor: pointer;">
                                          <i class="bi bi-paperclip" style="font-size: 1.2rem;"></i>
                                      </label>
                                          <label for="postFile" class="btn btn-outline-white d-flex align-items-center" style="cursor: pointer;">
                                          <i class="bi bi-image" style="font-size: 1.2rem;"></i>
                                      </label>
                                          <input type="file" id="postFile" name="postFile" style="display: none;">
                                          <span id="fileName" class="ms-3 text-muted"></span>
                                      </div>
                                      <button type="submit" class="btn btn-primary">Create Post</button>
                                      </form>
                                  </div>
                              </div>
                          </div>
                      </div>`); 

                      handleFileInputChange();
                      function handleFileInputChange() {
                          const fileInput = document.getElementById('postFile');
                          if (fileInput) {
                              fileInput.addEventListener('change', function () {
                                  const fileNameSpan = document.getElementById('fileName');
                                  fileNameSpan.textContent = this.files.length > 0 ? this.files[0].name : 'No file chosen';
                              });
                          }
                      }
                      
                    $('#adminPostlists').html(`
                        <div id="postsContainer" class="mt-2">
                             <div id="postsLists"></div>
                         </div>
                     `);
                     $(document).ready(function () {
                        // Fetch posts for the current user
                        function loadAdminPosts() {
                            const currentUserId = $('meta[name="current-user-id"]').attr('content'); // Get user ID from meta tag
                            
                            $.ajax({
                                url: '/edma/src/controller/fetch_post.php', // Backend endpoint
                                method: 'GET',
                                dataType: 'json',
                                success: function (response) {
                                    if (response.error) {
                                        $('#adminPostlists').html(`<p class="text-danger">${response.error}</p>`);
                                    } else {
                                        const posts = response.posts;
                                        let postsHtml = '';
                    
                                        if (posts.length > 0) {
                                            posts.forEach(post => {
                                                const formattedDate = formatDateTo12Hour(post.created_at); // Assume `formatDateTo12Hour` is implemented
                    
                                                postsHtml += `
                                                   ${post.u_id == currentUserId ? `  <div class="card mb-3" style="max-width: 540px; margin: auto;">
                                                                <div class="card-body">
                                                                    <div class="d-flex align-items-center mb-3">
                                                                        <img src="../../public/lib/images/user_profile/${post.image_name}" alt="${post.admin_name}" class="img-fluid rounded-circle" style="width: 40px; height: 40px; margin-right: 10px;">
                                                                        <h6 class="card-title text-truncate">${post.admin_name || 'Unknown'}</h5>
                                                                    </div>
                            
                                                                   <p class="card-text text-truncate" style=" cursor:pointer;" data-post-id="${post.post_id}" data-ui-id="${post.ui_id}">${post.content}</p>
                                                            <small class="text-muted d-block mb-3">Posted on ${formattedDate}</small>
                        
                                                            <!-- Media Display -->
                                                            ${post.file_name && post.file_type.startsWith('image/') ? 
                                                                `<img src="../../public/lib/images/posts/${post.file_name}" alt="${post.title}" class="img-fluid rounded mb-3 full-width-media" style="max-width: 100%; height: auto;">`
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
                            
                                                                    <!-- Dropdown for delete and edit actions -->
                                                                    ${post.u_id == currentUserId ? `
                                                                        <div class="dropdown position-absolute top-0 end-0 p-2">
                                                                            <i class="bi bi-three-dots mr-3" style="font-size: 20px;" data-bs-toggle="dropdown" aria-expanded="false"></i>
                                                                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                                                <li><a class="dropdown-item edit-post" href="#" data-post-id="${post.post_id}" data-post-content="${post.content}" data-post-file="${post.file_name}">Edit</a></li>
                                                                                <li><a class="dropdown-item delete-profile-post" href="#" data-post-id="${post.post_id}">Delete</a></li>
                                                                            </ul>
                                                                        </div>
                                                                    ` : ''}
                                                                </div>
                                                            </div>
                                                        ` : ''}
                                                        </div>
                                                    </div>
                                                `;
                                            });
                                        } else {
                                            postsHtml = '<p>No posts available.</p>';
                                        }
                    
                                        $('#postsLists').html(postsHtml);
                                    }
                                },
                                error: function () {
                                    $('#adminPostlists').html('<p class="text-danger">Error fetching posts. Please try again later.</p>');
                                }
                            });
                        }
                    
                        // Initialize admin posts
                        loadAdminPosts();
                    
                        // Helper function to format date
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
                        
                        function deleteProfilePost(postId) {
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
                        
                                                loadAdminPosts(); // Refresh the posts list after deletion
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
                        $(document).on('click', '.delete-profile-post', function(e) {
                            e.preventDefault(); // Prevent default anchor behavior
                            const postId = $(this).data('post-id'); // Retrieve post ID
                            deleteProfilePost(postId);
                        });

                        loadAdminPosts(); 
                    });
                    

                        $('#adminPost').html(`
                          <div class="d-flex justify-content-center mt-4">
                            <div class="card shadow-sm" style="width: 100%; max-width: 540px; border-radius: 10px;">
                                <div class="card-body d-flex align-items-center">
                                    <!-- Profile Picture -->
                                        <img src="../lib/images/user_profile/${response.user_info.image_name}" alt="Profile Picture" class="rounded-circle" style="width: 40px; height: 40px; margin-right: 10px;">
                                    <!-- Input-like Button -->
                                        <button id="openModalBtn" data-bs-toggle="modal" data-bs-target="#postModal" class="btn btn-outline-white flex-grow-1 text-start d-flex align-items-center" style="border-radius: 20px; padding: 10px 15px; ; border-color: #444;">Post Now, ${response.user_info.name}</button>
                                </div>
                            </div>
                        </div>
                        <!-- Modal HTML -->
                        <div id="postModal" class="modal fade" tabindex="-1" aria-labelledby="postModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="postModalLabel">Create Post</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form id="postForm" method="POST" enctype="multipart/form-data">
                                            <input type="hidden" name="u_id" value="${response.user_info.u_id}">
                                            <input type="hidden" name="ui_id" value="${response.user_info.id}">
                                        <div class="mb-3">
                                            <label for="postContent" class="form-label">What Do You Want To Post?</label>
                                            <textarea class="form-control" id="postContent" name="postContent" rows="4"></textarea>
                                        </div>
                                        <div class="mb-3 d-flex align-items-center">
                                        <!-- Icon-Based Upload Button -->
                                            <label for="postFile" class="btn btn-outline-white d-flex align-items-center" style="cursor: pointer;">
                                            <i class="bi bi-paperclip" style="font-size: 1.2rem;"></i>
                                        </label>
                                            <label for="postFile" class="btn btn-outline-white d-flex align-items-center" style="cursor: pointer;">
                                            <i class="bi bi-image" style="font-size: 1.2rem;"></i>
                                        </label>
                                            <input type="file" id="postFile" name="postFile" style="display: none;">
                                            <span id="fileName" class="ms-3 text-muted"></span>
                                        </div>
                                        <button type="submit" class="btn btn-primary">Create Post</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>`); 
                        handleFileInputChange();
                        function handleFileInputChange() {
                            const fileInput = document.getElementById('postFile');
                            if (fileInput) {
                                fileInput.addEventListener('change', function () {
                                    const fileNameSpan = document.getElementById('fileName');
                                    fileNameSpan.textContent = this.files.length > 0 ? this.files[0].name : 'No file chosen';
                                });
                            }
                        }
                        
                        
                          
                        $('#adminPostlist').html(`
                           <div id="postsContainer" class="mt-4">
                                <div id="postsList"></div>
                            </div>
                        `);
                            // Fetch posts
                            $(document).ready(function() {
                                // Fetch posts
                                function fetchPosts() {
                                    // Dynamically add the modal HTML to the page (only once)
                                    if ($('#editPostModal').length === 0) { // Check if modal already exists
                                        const modalHtml = `
                                            <div class="modal fade" id="editPostModal" tabindex="-1" aria-labelledby="editPostModalLabel" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="editPostModalLabel">Edit Post</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <textarea class="form-control" rows="5"></textarea>
                                                            <div class="mt-3">
                                                                <label for="postFile" class="form-label">Upload a file (optional)</label>
                                                                <input type="file" class="form-control" id="postFile" accept="image/*,video/mp4">
                                                                <div id="existingFileContainer" class="mt-3"></div> <!-- Display existing file if any -->
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                            <button type="button" class="btn btn-primary" id="savePostChanges">Save Changes</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        `;
                                        $('body').append(modalHtml); // Append modal HTML to body
                                    }
                            
                                    $.ajax({
                                        url: '/edma/src/controller/fetch_post.php',
                                        type: 'GET',
                                        dataType: 'json',
                                        success: function(response) {
                                            if (response.status === 'success') {
                                                const posts = response.posts;
                                                let postsHtml = '';
                                                const currentUserId = $('meta[name="current-user-id"]').attr('content');
                                    
                                                if (posts.length > 0) {
                                                    posts.forEach(post => {
                                                        const formattedDate = formatDateTo12Hour(post.created_at);
                                    
                                                        postsHtml += `
                                                            <div class="card mb-3 post-card" style="max-width: 540px; margin: auto;">
                                                                <div class="card-body">
                                                                    <div class="d-flex align-items-center mb-3">
                                                                        <img src="../../public/lib/images/user_profile/${post.image_name}" alt="${post.admin_name}" class="img-fluid rounded-circle" style="width: 40px; height: 40px; margin-right: 10px;">
                                                                        <h6 class="card-title text-truncate user-name" data-user-id="${post.u_id}" style="cursor: pointer;">${post.admin_name || 'Unknown'}</h6>
                                                                    </div>
                                                                    <p class="card-text text-truncate" style=" cursor:pointer;" data-post-id="${post.post_id}" data-ui-id="${post.ui_id}">${post.content}</p>
                                                                    <small class="text-muted d-block mb-3">Posted on ${formattedDate}</small>
                                    
                                                                    ${post.file_name && post.file_type.startsWith('image/') ? 
                                                                        `<img src="../../public/lib/images/posts/${post.file_name}" alt="${post.title}" class="img-fluid rounded mb-2 full-width-media" style="width: 550px; height: 550px; object-fit: cover; border-radius: 8px;">`
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
                                                                        `<a href="../../public/lib/images/posts/${post.file_name}"target="_blank" class="btn btn-link">${post.file_name.replace(/^\d+_/, '')}</a>`
                                                                    : post.file_name ? 
                                                                        `<p class="mb-0">
                                                                            <a href="../../public/lib/images/posts/${post.file_name}" target="_blank" class="btn btn-link">Download File</a>
                                                                        </p>`
                                                                    : ''}
                                                                    ${post.u_id == currentUserId ? `
                                                                        <div class="dropdown position-absolute top-0 end-0 p-2">
                                                                            <i class="bi bi-three-dots mr-3" style="font-size: 20px;" data-bs-toggle="dropdown" aria-expanded="false"></i>
                                                                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                                                <li><a class="dropdown-item edit-post" href="#" data-post-id="${post.post_id}" data-post-content="${post.content}" data-post-file="${post.file_name}">Edit</a></li>
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
                                    
                                                // Attach click event to redirect to view_post.php with the post ID
                                                $('.card-text').on('click', function() {
                                                    const postId = $(this).data('post-id');
                                                    const userId = $(this).data('ui-id');  // Get user ID
                                                    window.location.href = `view_post.php?post_id=${postId}&user_info_id=${userId}`;  // Pass both IDs in the URL
                                                });
                                            } else {
                                                console.error('Error fetching posts:', response.message);
                                            }
                                        },
                                        error: function(jqXHR, textStatus, errorThrown) {
                                            console.error("AJAX error: ", textStatus, errorThrown);
                                        }
                                    });                                    
                                }
                                
                                $(document).ready(function () {
                                    // Get the `post_id` from the URL
                                    const urlParams = new URLSearchParams(window.location.search);
                                    const postId = urlParams.get('post_id');
                                    const userId = urlParams.get('user_info_id');
                                
                                    if (!postId) {
                                        $('#postContainer').html('<p class="text-danger">Invalid Post ID.</p>');
                                        return;
                                    }
                                
                                    // Fetch post data using AJAX
                                    $.ajax({
                                        url: '/edma/src/controller/fetch_single_post.php',
                                        type: 'GET',
                                        data: { post_id: postId, user_info_id: userId },
                                        dataType: 'json',
                                        success: function (response) {
                                            if (response.status === 'success') {
                                                const post = response.post;
                                                
                                                $('#viewPost').html(`
                                                    <div class="card">
                                                        <div class="card-body">
                                                            <div class="d-flex align-items-center mb-3">
                                                                <img src="../../public/lib/images/user_profile/${post.user_image || 'default.jpg'}" class="img-fluid rounded-circle" style="width: 40px; height: 40px; margin-right: 10px;"/>
                                                                <h5 class="card-title">${post.user_name || 'Untitled'}</h5>
                                                            </div>
                                                            <p class="card-text"  style=" cursor:pointer;" data-post-id="${post.post_id}" data-ui-id="${post.ui_id}">${post.content || 'No content available.'}</p>
                                                            <small class="text-muted">Posted on: ${new Date(post.created_at).toLocaleString()}</small>
                                                            <div id="postMedia" class="mt-3"></div>
                                                        </div>
                                                    </div>
                                                `);
                                                
                                            
                                                // Display post media
                                                if (post.file_name && post.file_type.startsWith('image/')) {
                                                    $('#postMedia').html(
                                                        `<img src="../../public/lib/images/posts/${post.file_name}" alt="${post.title}"  class="img-fluid rounded mb-2 full-width-media" style="width: 550px; height: 550px; object-fit: cover; border-radius: 8px;">`
                                                    );
                                                } else if (post.file_type === 'video/mp4') {
                                                    $('#postMedia').html(
                                                        `<video controls class="w-100">
                                                            <source src="../../public/lib/images/posts/${post.file_name}" type="video/mp4">
                                                            Your browser does not support the video tag.
                                                        </video>`
                                                    );
                                                } else if (post.file_type === 'audio/mpeg' || post.file_type === 'audio/wav') {
                                                    $('#postMedia').html(
                                                        `<audio controls class="w-100">
                                                            <source src="../../public/lib/images/posts/${post.file_name}" type="${post.file_type}">
                                                            Your browser does not support the audio element.
                                                        </audio>`
                                                    );
                                                } else if (post.file_name) {
                                                    $('#postMedia').html(
                                                        `<a href="../../public/lib/images/posts/${post.file_name}" target="_blank" class="btn btn-link">Download File</a>`
                                                    );
                                                }
                                            } else {
                                                $('#postContainer').html(`<p class="text-danger">${response.message}</p>`);
                                            }
                                        },
                                        error: function (jqXHR, textStatus, errorThrown) {
                                            console.error("AJAX error: ", textStatus, errorThrown);
                                            $('#postContainer').html('<p class="text-danger">Failed to load post.</p>');
                                        }
                                    });
                                });
                                
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
                                
                                
                                $(document).on('click', '.user-name', function () {
                                    const userId = $(this).data('user-id'); // Get the user ID from the clicked name
                                    const currentUserId = $('meta[name="current-user-id"]').attr('content'); // Get the currently logged-in user's ID from the meta tag
                                
                                    if (userId == currentUserId) {
                                        // Redirect to update_user.php if the IDs match
                                        window.location.href = '../view/update_user.php';
                                    } else {
                                        // Otherwise, redirect to the profile page
                                        window.location.href = `../view/update_profile.php?id=${userId}`;
                                    }
                                });
                                
                                
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
                                
                                // Event listener for opening the edit modal
                                $(document).on('click', '.edit-post', function(e) {
                                    e.preventDefault();
                                    const postId = $(this).data('post-id');
                                    const postContent = $(this).data('post-content');
                                    const postFile = $(this).data('post-file'); // Get file name if available
                            
                                    // Populate the modal with post content
                                    $('#editPostModal textarea').val(postContent);
                                    $('#savePostChanges').data('post-id', postId); // Set the postId to the Save Changes button
                            
                                    // Check if there's an existing file and display it
                                    if (postFile) {
                                        const fileExtension = postFile.split('.').pop().toLowerCase();
                                        const fileContainer = $('#editPostModal #existingFileContainer');
                                        fileContainer.empty(); // Clear previous file
                            
                                        if (fileExtension === 'jpg' || fileExtension === 'jpeg' || fileExtension === 'png' || fileExtension === 'gif') {
                                            fileContainer.append(`<img src="../../public/lib/images/posts/${postFile}" alt="Post file" class="img-fluid rounded mb-3">`);
                                        } else if (fileExtension === 'mp4') {
                                            fileContainer.append(`
                                                <video controls class="w-100 rounded mb-3">
                                                    <source src="../../public/lib/images/posts/${postFile}" type="video/mp4">
                                                    Your browser does not support the video tag.
                                                </video>
                                            `);
                                        }
                                        else if (fileExtension === 'mp3') {
                                            fileContainer.append(`
                                                <audio controls class="w-100 rounded mb-3 full-width-media">
                                                                    <source src="../../public/lib/images/posts/${postFile}" type="audio/mpeg">
                                                                    Your browser does not support the audio element.
                                                                </audio>
                                            `);
                                        }
                                    }
                            
                                    $('#editPostModal').modal('show');
                                });
                            
                                $(document).on('click', '#savePostChanges', function() {
                                    const updatedContent = $('#editPostModal textarea').val();
                                    const postId = $(this).data('post-id');
                                    const formData = new FormData();
                                    formData.append('post_id', postId);
                                    formData.append('content', updatedContent);
                                    formData.append('file', $('#editPostModal #postFile')[0].files[0]); // Append the file if selected
                            
                                    $.ajax({
                                        url: '/edma/src/controller/edit_post.php',
                                        type: 'POST',
                                        data: formData,
                                        processData: false, // Do not process the data
                                        contentType: false, // Set content type to false for file upload
                                        dataType: 'json',
                                        success: function(response) {
                                            if (response.status === 'success') {
                                                Swal.fire({
                                                    title: 'Post Updated!',
                                                    text: 'Your post has been updated successfully.',
                                                    icon: 'success',
                                                    confirmButtonText: 'OK',
                                                });
                            
                                                fetchPosts(); // Refresh posts after update
                                                $('#editPostModal').modal('hide'); // Close modal
                                            } else {
                                                Swal.fire({
                                                    title: 'Error',
                                                    text: 'Error updating post.',
                                                    icon: 'error',
                                                    confirmButtonText: 'OK',
                                                });
                                            }
                                        },
                                        error: function(jqXHR, textStatus, errorThrown) {
                                            console.error("AJAX error while updating post: ", textStatus, errorThrown);
                                            Swal.fire({
                                                title: 'Error',
                                                text: 'AJAX error: ' + textStatus,
                                                icon: 'error',
                                                confirmButtonText: 'OK',
                                            });
                                        }
                                    });
                                });
                            
                            
                                // Fetch posts on page load
                                fetchPosts();
                            });
                            
                        function calculateAge(birthday) {
                            const birthDate = new Date(birthday);
                            const today = new Date();
                            let age = today.getFullYear() - birthDate.getFullYear();
                            const monthDifference = today.getMonth() - birthDate.getMonth();
                        
                            // Adjust if the birthday hasn't occurred yet this year
                            if (monthDifference < 0 || (monthDifference === 0 && today.getDate() < birthDate.getDate())) {
                                age--;
                            }
                        
                            return age;
                        }
                        
                        
                        
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
                                            window.location.href = 'home.php'; // Redirect to admin dashboard after success
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
