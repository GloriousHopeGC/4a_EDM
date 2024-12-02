<?php
header('Content-Type: application/json');

$dsn = "mysql:host=localhost;dbname=4a-pro;charset=utf8mb4";
$username = "root";
$password = "";

try {
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed: ' . $e->getMessage()]);
    exit;
}

$userId = isset($_GET['id']) ? intval($_GET['id']) : null;

if ($userId) {
    // Modified query to include file_name and file_type
    $sql = "SELECT p.post_id, p.content, p.created_at, p.file_name, p.file_type, u.name, u.image_name
            FROM posts p
            JOIN user_info u ON p.ui_id = u.id
            WHERE p.u_id = ?
            ORDER BY p.created_at DESC"; // Fetch related user data and files along with posts
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$userId]);
    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($posts) {
        echo json_encode(['success' => true, 'posts' => $posts]);
    } else {
        echo json_encode(['success' => false, 'message' => 'No posts found for this user!']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'No user ID provided!']);
}
?>
