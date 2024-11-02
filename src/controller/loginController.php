<?php
require_once '../../src/controller/userController.php';

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Instantiate the controller
$controller = new userController();

// Handle login only if this is a POST request (typically from an AJAX call)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Process login by calling the handleLogin method in userController
    $controller->handleLogin();
} else {
    // If accessed directly without POST, return a JSON error response
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
}
?>
