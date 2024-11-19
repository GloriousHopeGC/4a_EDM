<?php
header('Content-Type: application/json');
// Database connection setup
$dsn = 'mysql:host=localhost;dbname=4a-pro;charset=utf8mb4'; // Database details
$username = 'root'; // Database username
$password = ''; // Database password
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    // Create PDO instance for database connection
    $pdo = new PDO($dsn, $username, $password, $options);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Database connection failed: ' . $e->getMessage()]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $postId = intval($_POST['id']); // Ensure ID is an integer

    try {
        // Check if the post exists
        $checkSql = "SELECT 1 FROM posts WHERE post_id = ?";
        $checkStmt = $pdo->prepare($checkSql);
        $checkStmt->execute([$postId]);

        if ($checkStmt->fetch()) {
            // Post exists, proceed to delete
            $sql = "DELETE FROM posts WHERE post_id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$postId]);

            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Post not found']);
        }
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Post ID missing']);
}

?>
