<?php
// Database connection details
$host = 'localhost'; // Change this if your database is on a different host
$username = 'root'; // Your database username
$password = ''; // Your database password
$dbname = '4a-pro'; // Your database name

// Create connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

include_once 'functions.php'; // Include any utility functions (e.g., file upload handling)

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve POST data
    $postId = $_POST['post_id'];
    $content = $_POST['content'];
    $file = isset($_FILES['file']) ? $_FILES['file'] : null;
    
    // Check if the post ID exists in the database
    $stmt = $conn->prepare("SELECT * FROM posts WHERE post_id = ?");
    $stmt->bind_param("i", $postId);
    $stmt->execute();
    $result = $stmt->get_result();
    $post = $result->fetch_assoc();

    if (!$post) {
        echo json_encode(['status' => 'error', 'message' => 'Post not found']);
        exit;
    }

    // Handle file upload if a new file is provided
    if ($file) {
        // You can implement file upload handling (e.g., validate file type, move to a folder)
        $uploadDir = '../../public/lib/images/posts/';
        $newFileName = time() . '_' . basename($file['name']);
        $uploadPath = $uploadDir . $newFileName;

        if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
            $fileName = $newFileName;
        } else {
            echo json_encode(['status' => 'error', 'message' => 'File upload failed']);
            exit;
        }
    } else {
        // If no new file is uploaded, retain the original file name
        $fileName = $post['file_name'];
    }

    // Update the post content and file name in the database
    $stmt = $conn->prepare("UPDATE posts SET content = ?, file_name = ? WHERE post_id = ?");
    $stmt->bind_param("ssi", $content, $fileName, $postId);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Post updated successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to update post']);
    }
}
?>
