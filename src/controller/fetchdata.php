<?php
require_once '../../src/controller/userController.php';

$controller = new userController();
session_start();

// Fetch user data only if user is logged in
if (isset($_SESSION['user_id'])) {
    $userData = $controller->getUserData();
    echo json_encode($userData);
} else {
    echo json_encode(['error' => 'User not logged in.']);
}
?>
