<?php
require_once '../../src/controller/userController.php';

if (isset($_GET['post_id']) && isset($_GET['user_info_id'])) {
    $post_id = intval($_GET['post_id']);
    $user_id = intval($_GET['user_info_id']); // Get user ID

    // Initialize the user controller
    $userController = new userController();

    // Optionally, you can check if the user is authorized to view the post
    // Example: if the user ID matches the post author or has the required permissions
    $response = $userController->fetch_post_by_id($post_id, $user_id);

    // Return the response as JSON
    echo json_encode($response);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Post ID or User ID is missing.']);
}
?>
