<?php
session_start();
require_once '../../src/controller/userController.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-cache, must-revalidate');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $userController = new userController();

        $user_id = $_POST['user_id'];
        $new_email = $_POST['newEmail'];

        $response = $userController->updateEmail($user_id, $new_email);

        echo json_encode($response);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}
?>
