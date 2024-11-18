<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-cache, must-revalidate');

try {
    // Initialize database connection
    $pdo = new PDO('mysql:host=localhost;dbname=4a-pro', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $user_id = $_POST['user_id'];
        $new_email = $_POST['newEmail'];

        // Check if email is valid
        if (!filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid email format.']);
            exit;
        }

        // Check if the email is already in use by another user
        $checkEmailStmt = $pdo->prepare("SELECT id FROM user WHERE email = :new_email AND id != :user_id");
        $checkEmailStmt->bindParam(':new_email', $new_email, PDO::PARAM_STR);
        $checkEmailStmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $checkEmailStmt->execute();

        if ($checkEmailStmt->rowCount() > 0) {
            echo json_encode(['status' => 'error', 'message' => 'Email is already in use by another user.']);
            exit;
        }

        // Prepare SQL query to update the email
        $stmt = $pdo->prepare("UPDATE user SET email = :new_email WHERE id = :user_id");
        $stmt->bindParam(':new_email', $new_email, PDO::PARAM_STR);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);

        // Execute the query
        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Email updated successfully!']);
        } else {
            // Get database error info
            $errorInfo = $stmt->errorInfo();
            echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $errorInfo[2]]);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
    }
} catch (PDOException $e) {
    // Database-related errors
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
} catch (Exception $e) {
    // General errors
    echo json_encode(['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]);
}
?>
