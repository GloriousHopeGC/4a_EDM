<?php
// database connection
$host = '127.0.0.1';
$dbname = '4a-pro';
$username = 'root'; // Adjust according to your MySQL username
$password = ''; // Adjust according to your MySQL password

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Could not connect to the database: " . $e->getMessage());
}

// Handling the search query
$searchQuery = '';
if (isset($_GET['query'])) {
    $searchQuery = $_GET['query'];
}

// SQL query to search in the 'user_info' table
$sql = "SELECT * FROM user_info WHERE name LIKE :query";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':query', '%' . $searchQuery . '%', PDO::PARAM_STR);
$stmt->execute();
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search User Info</title>
    <!-- Add Bootstrap for styling (optional) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Search User Info</h2>
        <!-- Search Form -->
        <form class="d-flex mb-4" action="search.php" method="GET">
            <input class="form-control me-2" type="text" name="query" placeholder="Search by name" value="<?php echo htmlspecialchars($searchQuery); ?>">
            <button class="btn btn-outline-success" type="submit">Search</button>
        </form>

        <?php if ($searchQuery): ?>
            <h4>Search Results for: <?php echo htmlspecialchars($searchQuery); ?></h4>
        <?php endif; ?>

        <?php if ($results): ?>
            <!-- Results Table -->
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Gender</th>
                        <th>Birthday</th>
                        <th>Address</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($results as $row): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['id']); ?></td>
                            <td><?php echo htmlspecialchars($row['name']); ?></td>
                            <td><?php echo htmlspecialchars($row['gender']); ?></td>
                            <td><?php echo htmlspecialchars($row['birthday']); ?></td>
                            <td><?php echo htmlspecialchars($row['address']); ?></td>
                            <td><?php echo htmlspecialchars($row['status']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No results found.</p>
        <?php endif; ?>
    </div>
</body>
</html>
