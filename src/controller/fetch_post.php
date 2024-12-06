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

// Fetch posts with user status and flag check
try {
    // Adjust the query to include the user comments
    $stmt = $pdo->prepare("SELECT posts.*, user_info.name AS admin_name, user_info.image_name 
        FROM posts
        JOIN user_info ON posts.ui_id = user_info.id
        JOIN user ON user.id = user_info.u_id  -- Join with the 'users' table to check the 'flag' column
        WHERE user.flag != 0 AND user_info.status != 0  -- Check the status and flag conditions
        ORDER BY posts.created_at DESC");

    $stmt->execute();
    $posts = $stmt->fetchAll();

    // Loop through posts and fetch comments for each post
    foreach ($posts as &$post) {
        $postId = $post['post_id']; // Assuming 'post_id' is the primary key of the posts table

        // Fetch comments for the current post
        $commentStmt = $pdo->prepare("SELECT comment.comment, comment.created_at, user_info.name, user_info.image_name 
FROM comment 
JOIN user_info ON comment.ui_id = user_info.id 
WHERE comment.p_id = ? 
ORDER BY comment.created_at;
ASC");
        $commentStmt->execute([$postId]);
        $comments = $commentStmt->fetchAll();

        // Add comments to the post data
        $post['comments'] = $comments;
    }

    // Check if no posts were found
    if (empty($posts)) {
        echo json_encode(['status' => 'error', 'message' => 'No posts found.']);
    } else {
        echo json_encode(['status' => 'success', 'posts' => $posts]);
    }
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
