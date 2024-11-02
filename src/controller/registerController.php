<?php
require_once '../../src/controller/userController.php';

header('Content-Type: application/json'); // Set header to return JSON response

$controller = new userController();

// Handle registration
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Call the method to handle registration
    $response = $controller->handleRegistration();

    // If the registration method sends a response
    if ($response) {
        echo json_encode($response); // Ensure a JSON response is returned
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to register.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}
?>
