$(document).ready(function () {
    // Define the HTML structure for the admin dashboard
    const navbar = `
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <div class="container-fluid">
                <a class="navbar-brand" href="#">Admin Dashboard</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="#">Home</a>
                        </li>
                        <li class="nav-item">
                              <a class="nav-link" href="#" onclick="confirmLogout()">Logout</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>`;

    const sidebar = `
        <div class="col-md-2 sidebar">
            <h5>Admin Menu</h5>
            <a href="#" id="manageDashboardLink"><i class="bi bi-speedometer2"></i> Dashboard</a>
            <a href="#" id="manageUsersLink"><i class="bi bi-people"></i> Manage Users</a>
             <a href="#" id="postLink"><i class="bi bi-files"></i>  Manage Post</a>
            <a href="#" id="settingsLink"><i class="bi bi-gear"></i> Settings</a>
            <a href="#" onclick="confirmLogout()"><i class="bi bi-door-open"></i> Log-out</a>
        </div>`;

        const mainContent = ` 
        <div class="col-md-10" id="mainContent">
        <div class="p-4">
            <h1>Welcome, Admin</h1>
            <p>This is your admin dashboard. Use the menu to navigate through your administrative tasks.</p>
            <div class="row">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">
                                <i class="bi bi-person-lines-fill"></i> Registered Users
                            </h5>
                            <p id="userCount" class="card-text">Loading...</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">
                                <i class="bi bi-file-earmark-post"></i> Total Posts
                            </h5>
                            <p id="postCount" class="card-text">Loading...</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">
                                <i class="bi bi-chat-left-dots"></i> Total Comments
                            </h5>
                            <p id="commentCount" class="card-text">Loading...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    `;    
    const adminLayout = `
        <div class="container-fluid">
            <div class="row">
                ${sidebar}
                ${mainContent}
            </div>
        </div>`;

    // Append the full layout to the container
    $('#admin-container').html(navbar + adminLayout);

    const manageUsersContent = `
    <div class="container">
        <h2>Manage Users</h2>
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Status</th>
                    <th>Gender</th>
                    <th>Birthday</th>
                    <th>Address</th>
                    <th>Date Created</th>
                    <th>Last Updated</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="userTableBody">
                <!-- User data will be populated here -->
            </tbody>
        </table>
        <div id="pagination" class="d-flex justify-content-center">
            <!-- Pagination buttons will be populated here -->
        </div>
    </div>`;
    const managePostContent = `
    <div class="container">
    <div id="postContainer">
        <!-- User posts will be populated here as cards -->
    </div>
</div>

    `;
    const settingsContent = `
    <div class="container">
        <h2>Settings</h2>
        <button id="backupButton" class="btn btn-danger">
            <i class="bi bi-cloud-download"></i> Backup Database
        </button>
    </div>`;
    function fetchDashboardStats() {
        $.ajax({
            url: '/edma/src/controller/adminHandler.php',
            method: 'POST',
            data: { action: 'getDashboardStats' },
            dataType: 'json',
            success: function (response) {
                if (response.error) {
                    Swal.fire('Error', response.error, 'error');
                } else {
                    $('#userCount').text(response.userCount);
                    $('#postCount').text(response.postCount);
                    $('#commentCount').text(response.commentCount);
                }
            },
            error: function () {
                Swal.fire('Error', 'Failed to fetch dashboard stats. Please try again later.', 'error');
            }
        });
    }
    // Call the function to fetch the stats when the page loads
    fetchDashboardStats();

    function fetchUsers(page = 1) {
    $.ajax({
        url: '/edma/src/controller/adminHandler.php',
        method: 'POST',
        data: { action: 'getUsers', page: page },
        dataType: 'json',
        success: function (response) {
            if (response.error) {
                Swal.fire('Error', response.error, 'error');
            } else {
                // Populate the table with users
                let userTable = '';
                response.users.forEach((user, index) => {
                    let statusButton;
                    // Confirm that status is being correctly compared
                    console.log(`User ID: ${user.id}, Status: ${user.status}`);
                    if (user.status == 1) { // User is enabled
                        statusButton = `<button class="btn btn-warning disableBtn" data-id="${user.id}">Disable</button>`;
                    } else { // User is disabled
                        statusButton = `<button class="btn btn-success enableBtn" data-id="${user.id}">Enable</button>`;
                    }

                    userTable += `
                        <tr>
                            <td>${(index + 1) + (page - 1) * 5}</td>
                            <td>${user.name}</td>
                            <td>${user.email}</td>
                            <td>${user.status === 0 ? 'Disabled' : 'Enabled'}</td>
                            <td>${user.gender == 'Male' ? 'M' : (user.gender == 'Female' ? 'F' : 'U')}</td>
                            <td>${user.birthday}</td>
                            <td>${user.address}</td>
                            <td>${user.created}</td>
                            <td>${user.updated}</td>
                            <td>${statusButton}</td>
                        </tr>`;
                });
                $('#userTableBody').html(userTable);

                // Generate pagination buttons
                let pagination = '';
                for (let i = 1; i <= response.pages; i++) {
                    pagination += `<button class="btn btn-primary ${i === response.currentPage ? 'active' : ''}" data-page="${i}">${i}</button> `;
                }
                $('#pagination').html(pagination);

                // Add click events for pagination buttons
                $('#pagination button').on('click', function () {
                    const page = $(this).data('page');
                    fetchUsers(page); // Fetch users for the clicked page
                });

                // Enable and Disable button click event handlers
                $('.disableBtn').on('click', function () {
                    const userId = $(this).data('id');
                    updateUserStatus(userId, 0); // Disable the user
                });

                $('.enableBtn').on('click', function () {
                    const userId = $(this).data('id');
                    updateUserStatus(userId, 1); // Enable the user
                });
            }
        },
        error: function () {
            Swal.fire('Error', 'Failed to fetch users. Please try again later.', 'error');
        }
    });
}

function updateUserStatus(userId, status) {
    $.ajax({
        url: '/edma/src/controller/adminHandler.php',
        method: 'POST',
        data: { action: 'updateUserStatus', userId: userId, status: status },
        dataType: 'json',
        success: function (response) {
            if (response.success) {
                // Update the button text and classes after successful status change
                const button = $(`button[data-id="${userId}"]`);
                if (status === 1) {
                    button.text('Disable').removeClass('btn-success').addClass('btn-warning');
                } else {
                    button.text('Enable').removeClass('btn-warning').addClass('btn-success');
                }
                Swal.fire('Success', `User has been ${status === 1 ? 'enabled' : 'disabled'}.`, 'success')
                .then((result) => {
                    if (result.isConfirmed) {
                        location.reload(); // Reload the page after clicking "OK"
                    }
                });
            } else {
                Swal.fire('Info', response.message, 'info');
            }
        },
        error: function () {
            Swal.fire('Error', 'Failed to update user status. Please try again later.', 'error');
        }
    });
}
function fetchUserPosts(userId) {
    $.ajax({
        url: '/edma/src/controller/adminHandler.php',
        method: 'POST',
        data: { action: 'getUserPosts', userId: userId },
        dataType: 'json',
        success: function (response) {
            if (response.error) {
                Swal.fire('Error', response.error, 'error');
            } else {
                let postCards = '';
                response.posts.forEach((post) => {
                    postCards += `
                        <div class="card mb-3 mt-4" style="max-width: 540px; margin: auto;" data-post-id="${post.post_id}">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <img src="../../public/lib/images/user_profile/${post.image_name}" alt="${post.user_name}" class="img-fluid rounded-circle" style="width: 40px; height: 40px; margin-right: 10px;">
                                    <h6 class="card-title text-truncate">${post.user_name || 'Unknown'}</h6>
                                </div>
                                <p class="card-text text-truncate" style="cursor: pointer;" data-post-id="${post.post_id}" data-ui-id="${post.ui_id}">${post.content}</p>
                                <small class="text-muted d-block mb-3">Posted on ${post.created_at}</small>
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
                                    `<a href="../../public/lib/images/posts/${post.file_name}" target="_blank" class="btn btn-link" style="text-decoration: none;">
                                        <i class="fas fa-download"></i> ${post.file_name.replace(/^\d+_/, '')}
                                    </a>`
                                : post.file_name ? 
                                    `<p class="mb-0">
                                        <a href="../../public/lib/images/posts/${post.file_name}" target="_blank" class="btn btn-link" style="text-decoration: none;">
                                        <i class="fas fa-download"></i> ${post.file_name.replace(/^\d+_/, '')}</a>
                                    </p>`
                                : ''}
                                <div class="dropdown position-absolute top-0 end-0 p-2">
                                    <i class="bi bi-three-dots" style="font-size: 20px;" data-bs-toggle="dropdown" aria-expanded="false"></i>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item delete-post" href="#" data-post-id="${post.post_id}">Delete</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>`;
                });
                $('#postContainer').html(postCards); // Assuming you have a container to show posts

                // Add click event for the delete button
                $('.delete-post').on('click', function (e) {
                    e.preventDefault();
                    const postId = $(this).data('post-id');
                    deletePost(postId);
                });
            }
        },
        error: function () {
            Swal.fire('Error', 'Failed to fetch posts. Please try again later.', 'error');
        }
    });
}

// Function to delete a post
function deletePost(postId) {
    Swal.fire({
        title: 'Are you sure?',
        text: "This action cannot be undone!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '/edma/src/controller/adminHandler.php',
                method: 'POST',
                data: { action: 'deletePost', postId: postId },
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        // Remove the post card from the DOM
                        $(`div[data-post-id="${postId}"]`).remove();
                        Swal.fire('Deleted!', 'The post has been deleted.', 'success');
                    } else {
                        Swal.fire('Error', response.message, 'error');
                    }
                },
                error: function () {
                    Swal.fire('Error', 'Failed to delete the post. Please try again later.', 'error');
                }
            });
        }
    });
}

$('#postLink').on('click', function (e) {
    e.preventDefault();
    $('#mainContent').html(managePostContent); // Replace main content with Manage Post content
    // Call fetchUserPosts with the appropriate user ID when needed
    const userId = 1;  // Example userId, you should dynamically pass the user ID
    fetchUserPosts(userId); // Fetch posts for the user
});

function backupDatabase() {
    $.ajax({
        url: '/edma/src/controller/adminHandler.php',  // Adjust the path if necessary
        method: 'POST',
        data: { action: 'backupDatabase' },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                Swal.fire('Success', response.message, 'success');
                console.log("Backup file created: " + response.file); // Optional: log the file path
            } else {
                Swal.fire('Error', response.message, 'error');
                console.log("Error details: " + response.error); // Optional: log the error
            }
        },
        error: function() {
            Swal.fire('Error', 'Failed to backup the database. Please try again later.', 'error');
        }
    });
}

$('#settingsLink').on('click', function (e) {
    e.preventDefault();
    $('#mainContent').html(settingsContent); // Replace main content with Settings content
    $('#backupButton').on('click', function () {
        Swal.fire({
            title: 'Are you sure?',
            text: "This will back up all the tables and the entire database!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, Backup',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                backupDatabase(); // Call the backup function
            }
        });
    });
});
$('#postLink').on('click', function (e) {
    e.preventDefault();
    $('#mainContent').html(managePostContent); // Replace main content with Manage Users content
    fetchUsers(); // Fetch users and populate the table (initial page is 1)
});
// Add click event for "Manage Users" menu link
$('#manageUsersLink').on('click', function (e) {
    e.preventDefault();
    $('#mainContent').html(manageUsersContent); // Replace main content with Manage Users content
    fetchUsers(); // Fetch users and populate the table (initial page is 1)
});
$('#manageDashboardLink').on('click', function (e) {
    e.preventDefault();
    $('#mainContent').html(mainContent); // Replace main content with Manage Users content
    fetchDashboardStats(); // Fetch users and populate the table (initial page is 1)
});
});
