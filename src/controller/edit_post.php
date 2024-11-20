<?php
// Database credentials
$servername = "localhost";  // Replace with your server if different
$username = "root"; // Replace with your database username
$password = ""; // Replace with your database password
$dbname = "4a-pro";  // Your database name

try {
    // Create a PDO connection
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    
    // Set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Get post id and content from the request
        $postId = $_POST['post_id'];
        $content = $_POST['content'];

        // Sanitize the content
        $content = htmlspecialchars($content, ENT_QUOTES, 'UTF-8');  // Convert special chars to HTML entities

        // Default file name and type (set to null if no file uploaded)
        $fileName = null;
        $fileType = null;

        // Check if a file was uploaded
        if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
            // Handle file upload
            $fileTmpPath = $_FILES['file']['tmp_name'];
            $fileName = basename($_FILES['file']['name']);
            $fileType = $_FILES['file']['type'];
            $uploadDir = '../../public/lib/images/posts/';  // Directory to save the uploaded file
            $destPath = $uploadDir . $fileName;

            // Move the uploaded file to the destination folder
            if (move_uploaded_file($fileTmpPath, $destPath)) {
                // File upload successful
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to upload file.']);
                exit;
            }
        } else {
            // If no file is uploaded, retain the current file (we need to get the existing file name and type)
            // Fetch the existing post details from the database to check if there was an old file
            $stmt = $conn->prepare("SELECT file_name, file_type FROM posts WHERE post_id = :post_id");
            $stmt->bindParam(':post_id', $postId, PDO::PARAM_INT);
            $stmt->execute();
            $post = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($post && $post['file_name'] && $post['file_type']) {
                // Retain the old file details if no new file is uploaded
                $fileName = $post['file_name'];
                $fileType = $post['file_type'];
            }
        }

        // Update post query
        $sql = "UPDATE posts SET content = :content, file_name = :file_name, file_type = :file_type WHERE post_id = :post_id";
        $stmt = $conn->prepare($sql);

        // Bind parameters
        $stmt->bindParam(':content', $content, PDO::PARAM_STR);
        $stmt->bindParam(':post_id', $postId, PDO::PARAM_INT);
        $stmt->bindParam(':file_name', $fileName, PDO::PARAM_STR);
        $stmt->bindParam(':file_type', $fileType, PDO::PARAM_STR);

        // Execute the query
        if ($stmt->execute()) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to update post.']);
        }
    }

} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Database connection failed: ' . $e->getMessage()]);
}

// Close the connection (optional as PDO will automatically close when the script ends)
$conn = null;
?>
