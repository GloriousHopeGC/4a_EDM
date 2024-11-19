<?php
// Start session
session_start();

// Include the userController class
require_once '../../src/controller/userController.php';

$response = ['status' => 'error', 'message' => ''];

// Check if the file is uploaded via POST
if (isset($_FILES['profileImage'])) {
    $userId = $_POST['user_id'];
    $image = $_FILES['profileImage'];

    // Instantiate the userController class
    $controller = new userController();

    // Call the updateProfileImage method and get the response
    $response = $controller->updateProfileImage($userId, $image);

    // Return the response as JSON
    echo json_encode($response);
} else {
    // If no file is uploaded, return an error response
    $response['icon'] = 'error';
    $response['message'] = 'No file uploaded or an error occurred.';
    echo json_encode($response);
}
?>
