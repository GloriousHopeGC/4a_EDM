<?php
// Database connection using PDO
try {
    $dsn = 'mysql:host=localhost;dbname=4a-pro;charset=utf8mb4';
    $username = 'root'; // Replace with your database username
    $password = ''; // Replace with your database password
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ];

    $pdo = new PDO($dsn, $username, $password, $options);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Database connection failed: ' . $e->getMessage()]);
    exit;
}

// Fetch posts
try {
    $stmt = $pdo->prepare("SELECT posts.*, user_info.name AS admin_name, user_info.image_name 
        FROM posts
        JOIN user_info ON posts.ui_id = user_info.id
        ORDER BY posts.created_at DESC");
    $stmt->execute();
    $posts = $stmt->fetchAll();

    echo json_encode(['status' => 'success', 'posts' => $posts]);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
}
