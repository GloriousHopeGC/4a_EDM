<?php
// Database connection using PDO
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

// Handle POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $u_id = $_POST['u_id'] ?? null;
    $ui_id = $_POST['ui_id'] ?? null;
    $content = $_POST['postContent'] ?? null;
    $file = $_FILES['postFile'] ?? null;

    // Validate input
    if (!$u_id || !$ui_id) {
        echo json_encode(['status' => 'error', 'message' => 'All fields are required except file.']);
        exit;
    }

    // Allowed file types
    $allowedTypes = [
        'image/jpeg', 'image/png', 'image/gif', 'application/pdf',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.openxmlformats-officedocument.presentationml.presentation', 
        'audio/mpeg', 'audio/wav', 'video/mp4'
    ];

    $maxFileSize = 1 * 1024 * 1024 * 1024; // 1GB in bytes
    $fileName = null;
    $fileType = null;

    // If a file is uploaded, validate and save the file
    if ($file && !empty($file['name'])) {
        // Check file type
        if (!in_array($file['type'], $allowedTypes)) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid file type.']);
            exit;
        }

        // Check file size (1GB max)
        if ($file['size'] > $maxFileSize) {
            echo json_encode(['status' => 'error', 'message' => 'File is too large. Maximum allowed size is 1GB.']);
            exit;
        }

        // Save the file
        $uploadDir = '../../public/lib/images/posts/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $fileName = time() . '_' . basename($file['name']);
        $filePath = $uploadDir . $fileName;

        if (!move_uploaded_file($file['tmp_name'], $filePath)) {
            echo json_encode(['status' => 'error', 'message' => 'File upload failed.']);
            exit;
        }

        $fileType = $file['type'];
    }

    // Insert into the database
    try {
        $query = "INSERT INTO posts (u_id, ui_id, content, created_at";
        $params = [
            ':u_id' => $u_id,
            ':ui_id' => $ui_id,
            ':content' => $content,
        ];

        // If a file was uploaded, include file data in the insert query
        if ($fileName && $fileType) {
            $query .= ", file_name, file_type";
            $params[':file_name'] = $fileName;
            $params[':file_type'] = $fileType;
        }

        $query .= ") VALUES (:u_id, :ui_id, :content, NOW()";

        // Add file data if necessary
        if ($fileName && $fileType) {
            $query .= ", :file_name, :file_type";
        }

        $query .= ")";

        // Prepare and execute the query
        $stmt = $pdo->prepare($query);
        $stmt->execute($params);

        echo json_encode(['status' => 'success', 'message' => 'Post created successfully.']);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
    }
}

