<?php
// Start session
session_start();

// Database connection (assuming you have a database.php file for the connection)
require_once '../../src/controller/database.php';

// Initialize response array
$response = ['status' => 'error', 'message' => ''];

// Check if the file is uploaded via POST
if (isset($_FILES['profileImage'])) {
    $image = $_FILES['profileImage'];
    $userId = $_POST['user_id'];

    // Validate file type (allow JPG, PNG, GIF)
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    if (!in_array($image['type'], $allowedTypes)) {
        $response['icon'] = 'error';
        $response['message'] = 'Invalid file type. Only JPG, PNG, or GIF are allowed.';
    } elseif ($image['size'] > 2 * 1024 * 1024) { // 2MB file size limit
        $response['icon'] = 'error';
        $response['message'] = 'File size exceeds the 2MB limit.';
    } else {
        // Define upload directory
        $uploadDir = '../../public/lib/images/user_profile/';

        // Check if upload directory is writable
        if (!is_dir($uploadDir) || !is_writable($uploadDir)) {
            $response['icon'] = 'error';
            $response['message'] = 'Upload directory is not writable or does not exist.';
        } else {
            // Generate unique file name by adding userId and timestamp for uniqueness
            $timestamp = time();
            $originalFileName = basename($image['name']);
            $imageName = $userId . '_' . $timestamp . '_' . $originalFileName;  // Keep the original file name
            $targetFilePath = $uploadDir . $imageName;

            // Move the uploaded file to the target directory
            if (move_uploaded_file($image['tmp_name'], $targetFilePath)) {
                // Extract only the original file name to store in the database
                $finalImageName = $imageName; // The unique file name with user ID and timestamp
                
                // Proceed with database update for the profile image
                try {
                    // Initialize database connection
                    $db = new database();
                    $con = $db->initDatabase();

                    // Update the user profile image in the database
                    $stmt = $con->prepare("UPDATE user_info SET updated = NOW(), image_name = :image_name WHERE u_id = :user_id");
                    $stmt->bindParam(':image_name', $finalImageName);  // Store only the original file name
                    $stmt->bindParam(':user_id', $userId);
                    $stmt->execute();

                    // Return success response
                    $response['icon'] = 'success';
                    $response['status'] = 'success';
                    $response['message'] = 'Profile image updated successfully.';
                    $response['imageName'] = $finalImageName; // Return only the original file name
                } catch (Exception $e) {
                    $response['message'] = 'Failed to update profile image in the database: ' . $e->getMessage();
                    error_log("Error updating profile image: " . $e->getMessage());
                }
            } else {
                $response['icon'] = 'error';
                $response['message'] = 'Failed to upload the file.';
                error_log("Failed to move uploaded file to target location.");
            }
        }
    }
} else {
    $response['icon'] = 'error';
    $response['message'] = 'No file uploaded or an error occurred.';
    error_log("No file uploaded or an error occurred");
}

// Return the response as JSON
echo json_encode($response);

?>
