<?php
// Database connection
$host = 'localhost';
$dbname = '4a-pro';
$username = 'root'; // Adjust according to your MySQL username
$password = ''; // Adjust according to your MySQL password

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Could not connect to the database: " . $e->getMessage());
}

// Check if the 'query' parameter is set
if (isset($_GET['query'])) {
    $query = $_GET['query'];

    // Search query using LIKE for the 'name' column
    $sql = "SELECT * FROM user_info WHERE name LIKE :query";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':query', '%' . $query . '%', PDO::PARAM_STR);
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Return the results as JSON
    echo json_encode(['data' => $results]);
} else {
    echo json_encode(['error' => 'No query provided']);
}
