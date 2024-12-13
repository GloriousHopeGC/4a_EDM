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
                                <h5 class="card-title">Registered Users</h5>
                                <p id="userCount" class="card-text">Loading...</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Total Posts</h5>
                                <p id="postCount" class="card-text">Loading...</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Total Comments</h5>
                                <p id="commentCount" class="card-text">Loading...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>`;    
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
    const settingsContent = `
    <div class="container">
        <h2>Settings</h2>
        <button id="backupButton" class="btn btn-danger">Backup Database</button>
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
