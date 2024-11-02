<?php
// Include the necessary files to access your controller
require_once '../../src/controller/userController.php'; 
header('Content-Type: application/json');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller = new userController();

    // Get the posted data
    $user_id = trim($_POST['user_id']);
    $name = trim($_POST['name']);
    $gender = trim($_POST['gender']);
    $birthday = trim($_POST['birthday']);
    $address = trim($_POST['address']);

    if (!empty($user_id) && !empty($name) && !empty($gender) && !empty($birthday) && !empty($address)) {
        $response = $controller->update_user($user_id, $name, $gender, $birthday, $address);
        echo json_encode($response); // Make sure this is in a proper JSON format
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Please fill in all fields.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}

?>
