<?php
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

$userId = isset($_GET['id']) ? $_GET['id'] : null;

// Check if user ID is valid
if ($userId) {
    // Query the database to fetch user details from `user_info` table
    $sql = "SELECT u.id, ui.name, ui.image_name, u.email
            FROM user u
            INNER JOIN user_info ui ON u.id = ui.u_id
            WHERE u.id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$userId]);
    $user = $stmt->fetch();

    // If user exists, display profile info
    if ($user) {
        $userName = htmlspecialchars($user['name']);
        $userEmail = htmlspecialchars($user['email']);
        $userProfileImage = $user['image_name'] ? $user['image_name'] : 'default.jpg';
    } else {
        // If user not found
        echo "User not found!";
        exit;
    }
} else {
    // If no user ID provided
    echo "No user ID provided!";
    exit;
}
?>