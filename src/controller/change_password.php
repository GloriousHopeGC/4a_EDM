<?php
require_once '../../src/controller/userController.php'; 
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-cache, must-revalidate');

try {
    // Check if all required fields are provided
    if (!isset($_POST['user_id'], $_POST['currentPassword'], $_POST['newPassword'], $_POST['confirmPassword'])) {
        echo json_encode(['error' => 'All fields are required.']);
        exit;
    }
    $controller = new userController();
    // Collect POST data
    $user_id = trim($_POST['user_id']);
    $currentPassword = trim($_POST['currentPassword']);
    $newPassword = trim($_POST['newPassword']);
    $confirmPassword = trim($_POST['confirmPassword']);

    // Validate that no field is empty
    if (empty($user_id) || empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
        echo json_encode(['error' => 'All fields are required.']);
        exit;
    }

    // Check if new password and confirm password match
    if ($newPassword !== $confirmPassword) {
        echo json_encode(['error' => 'New passwords do not match.']);
        exit;
    }

    // Initialize database connection (update with your specific implementation)
    $pdo = new PDO('mysql:host=localhost;dbname=4a-pro', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch the user's current password from the database
    $query = $pdo->prepare("SELECT password FROM user WHERE id = :id");
    $query->execute(['id' => $user_id]);
    $user = $query->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo json_encode(['error' => 'User not found.']);
        exit;
    }

    // Verify the current password
    if (!password_verify($currentPassword, $user['password'])) {
        echo json_encode(['error' => 'Incorrect current password.']);
        exit;
    }

    // Hash the new password
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

    // Update the password in the database
    $updateQuery = $pdo->prepare("UPDATE user SET password = :password WHERE id = :id");
    $updateQuery->execute([
        'password' => $hashedPassword,
        'id' => $user_id
    ]);

    // Success response
    echo json_encode(['success' => true, 'message' => 'Password changed successfully.']);
} catch (PDOException $e) {
    // Database-related errors
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
} catch (Exception $e) {
    // General errors
    echo json_encode(['error' => 'Error: ' . $e->getMessage()]);
}
?>
