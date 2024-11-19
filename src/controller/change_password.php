<?php
require_once '../../src/controller/userController.php'; 
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-cache, must-revalidate');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller = new userController();

    // Collect POST data
    $user_id = trim($_POST['user_id'] ?? '');
    $currentPassword = trim($_POST['currentPassword'] ?? '');
    $newPassword = trim($_POST['newPassword'] ?? '');
    $confirmPassword = trim($_POST['confirmPassword'] ?? '');

    // Call the controller's changePassword method
    $response = $controller->changePassword($user_id, $currentPassword, $newPassword, $confirmPassword);

    // Return the response as JSON
    echo json_encode($response);
}
?>
