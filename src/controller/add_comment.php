<?php
$dsn = "mysql:host=localhost;dbname=4a-pro;charset=utf8mb4";
$username = "root"; // Your database username
$password = ""; // Your database password

try {
    $pdo = new PDO($dsn, $username, $password);
    // Set error mode to exceptions for better error handling
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die(json_encode(['status' => 'error', 'message' => 'Database connection failed: ' . $e->getMessage()])); 
}

header('Content-Type: application/json');

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the POST data
    $postId = isset($_POST['post_id']) ? $_POST['post_id'] : null;
    $userId = isset($_POST['user_id']) ? $_POST['user_id'] : null;
    $commentText = isset($_POST['comment_text']) ? $_POST['comment_text'] : null;

    // Validate input
    if (!empty($postId) && !empty($userId) && !empty($commentText)) {
        try {
            // Fetch the `ui_id` dynamically using the provided `user_id`
            $stmt = $pdo->prepare("SELECT id, image_name FROM user_info WHERE u_id = ?");
            $stmt->execute([$userId]);
            $userInfo = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$userInfo) {
                echo json_encode(['status' => 'error', 'message' => 'User info not found']);
                exit;
            }

            $uiId = $userInfo['id'];
            $imageName = $userInfo['image_name'] ?? 'default.jpg'; // Use default image if not provided

            // Insert the comment into the database
            $stmt = $pdo->prepare("INSERT INTO comment (u_id, ui_id, p_id, comment, created_at) VALUES (?, ?, ?, ?, NOW())");
            if ($stmt->execute([$userId, $uiId, $postId, $commentText])) {
                // Get the last inserted comment id
                $commentId = $pdo->lastInsertId();

                // Prepare the response with the new comment details
                $response = [
                    'status' => 'success',
                    'message' => 'Comment added successfully',
                    'comment' => [
                        'id' => $commentId,
                        'u_id' => $userId,
                        'ui_id' => $uiId,
                        'p_id' => $postId,
                        'comment' => $commentText,
                        'created_at' => date('Y-m-d H:i:s'),
                        'image_name' => $imageName // Include the user's profile image
                    ]
                ];
                echo json_encode($response);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to add comment']);
            }
        } catch (PDOException $e) {
            echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid input']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>
