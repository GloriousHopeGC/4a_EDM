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

// Check if name parameter is passed
if (isset($_GET['name'])) {
    $name = '%' . trim($_GET['name']) . '%';

    try {
        // Query to search user by name
        $stmt = $pdo->prepare("SELECT ui.* FROM user_info ui
            JOIN user u ON ui.user_id = u.id
            WHERE u.flag = 1 AND ui.name LIKE :query");
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Fetch posts for the user
            $postsStmt = $pdo->prepare("SELECT * FROM posts WHERE user_id = :user_id");
            $postsStmt->bindParam(':user_id', $user['u_id'], PDO::PARAM_INT);
            $postsStmt->execute();
            $posts = $postsStmt->fetchAll(PDO::FETCH_ASSOC);

            // Return the user and posts data as JSON
            echo json_encode(['success' => true, 'user_info' => $user, 'posts' => $posts]);
        } else {
            echo json_encode(['success' => false, 'message' => 'No user found.']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
}
?>
