<?php
// Database connection setup
$dsn = "mysql:host=localhost;dbname=4a-pro;charset=utf8mb4";
$username = "root"; // Your database username
$password = ""; // Your database password

try {
    $pdo = new PDO($dsn, $username, $password);
    // Set error mode to exceptions for better error handling
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die(json_encode(['success' => false, 'message' => 'Database connection failed: ' . $e->getMessage()]));
}

// Check if user ID is passed
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'])) {
    $user_id = intval($_POST['user_id']);

    try {
        // Update query to mark the user as deleted
        $sql = "UPDATE user SET flag = 0 WHERE id = :user_id";
        $stmt = $pdo->prepare($sql);

        // Bind the user_id parameter
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);

        // Execute the query
        if ($stmt->execute()) {
            session_start(); // Start the session
            session_unset(); // Unset all session variables
            session_destroy(); 
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to execute query.']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
}
?>
