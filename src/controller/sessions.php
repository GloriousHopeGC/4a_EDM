<?php
// Start the session
session_start();

// Check if the user is logged in
if (isset($_SESSION['user_id'])) {
    // If logged in, return the session data as a JSON response
    echo json_encode([
        'logged_in' => true,
        'user_id' => $_SESSION['user_id'],
        'email' => $_SESSION['email'],
        'role' => $_SESSION['role']
    ]);
} else {
    // If not logged in, return a JSON response indicating the user is not logged in
    echo json_encode([
        'logged_in' => false
    ]);
}
exit();
?>
