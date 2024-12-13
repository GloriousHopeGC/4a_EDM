<?php
require_once '../../src/controller/database.php';

class adminController {
    public function getUsers()
    {
        try {
            // Get page and limit from POST request
            $page = isset($_POST['page']) ? (int)$_POST['page'] : 1;
            $limit = 5;  // Fixed limit of 5 users per page
            $offset = ($page - 1) * $limit; // Calculate the offset
    
            $db = new database();
            $con = $db->initDatabase();
    
            // Prepare and execute the query with LIMIT, OFFSET, and role exclusion (excluding admins)
            $sql = "SELECT 
                        u.id, 
                        u.email, 
                        u.role, 
                        ui.name, 
                        ui.gender, 
                        ui.birthday, 
                        ui.address,
                        ui.status,  -- Make sure 'status' is included here
                        ui.created,
                        ui.updated
                    FROM user u
                    JOIN user_info ui ON u.id = ui.u_id
                    WHERE u.role != 2  -- Exclude admins (role != 2)
                    AND u.flag != 0    -- Exclude users where the flag is 0
                    LIMIT :limit OFFSET :offset";
            $stmt = $con->prepare($sql);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
    
            // Fetch the results as an associative array
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
            // Get the total number of users for pagination, excluding admins
            $sqlCount = "SELECT COUNT(*) FROM user u JOIN user_info ui ON u.id = ui.u_id WHERE u.role != 2";
            $stmtCount = $con->query($sqlCount);
            $totalUsers = $stmtCount->fetchColumn();
    
            // Return the data as JSON, including pagination info
            echo json_encode([
                'users' => $users,
                'totalUsers' => $totalUsers,
                'pages' => ceil($totalUsers / $limit),
                'currentPage' => $page
            ]);
    
        } catch (Exception $e) {
            echo json_encode(["error" => "Failed to fetch users: " . $e->getMessage()]);
        }
    }    
    public function updateUserStatus() {
        if (isset($_POST['userId']) && isset($_POST['status'])) {
            try {
                $userId = $_POST['userId'];
                $status = $_POST['status']; // 0 for disable, 1 for enable
    
                $db = new database();
                $con = $db->initDatabase();
    
                // Fetch the current status of the user
                $sql = "SELECT status FROM user_info WHERE u_id = :user_id"; // Ensure this is the correct column
                $stmt = $con->prepare($sql);
                $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
                $stmt->execute();
                $currentStatus = $stmt->fetchColumn();
    
                // If the status is already the same, don't update it
                if ($currentStatus == $status) {
                    echo json_encode(['success' => false, 'message' => 'User is already ' . ($status == 1 ? 'enabled' : 'disabled')]);
                    return;
                }
    
                // Update the user status
                $sql = "UPDATE user_info SET status = :status WHERE u_id = :user_id"; // Ensure status column is correct here
                $stmt = $con->prepare($sql);
                $stmt->bindParam(':status', $status, PDO::PARAM_INT);
                $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT); 
                $stmt->execute();
    
                echo json_encode(['success' => true]);
            } catch (Exception $e) {
                echo json_encode(["error" => "Failed to update status: " . $e->getMessage()]);
            }
        } else {
            echo json_encode(['error' => 'Invalid parameters']);
        }
    }    
    public function getDashboardStats() {
        try {
            $db = new database();
            $con = $db->initDatabase();

            // Count the number of registered users
            $sqlUsers = "SELECT COUNT(*) FROM user WHERE flag = 1 AND role != 2"; // Exclude admins (role 2)
            $stmtUsers = $con->query($sqlUsers);
            $userCount = $stmtUsers->fetchColumn();

            // Count the number of posts
            $sqlPosts = "SELECT COUNT(*) FROM posts"; // Assuming the posts table exists
            $stmtPosts = $con->query($sqlPosts);
            $postCount = $stmtPosts->fetchColumn();

            // Count the number of comments
            $sqlComments = "SELECT COUNT(*) FROM comment"; // Assuming the comments table exists
            $stmtComments = $con->query($sqlComments);
            $commentCount = $stmtComments->fetchColumn();

            // Return the data as JSON
            echo json_encode([
                'userCount' => $userCount,
                'postCount' => $postCount,
                'commentCount' => $commentCount
            ]);

        } catch (Exception $e) {
            echo json_encode(["error" => "Failed to fetch dashboard stats: " . $e->getMessage()]);
        }
    }
    public function backupDatabase() {
        // Database connection settings
        $dbHost = 'localhost';
        $dbUsername = 'root'; // Your database username
        $dbPassword = '';     // Your database password (empty if none)
        $dbName = '4a-pro';   // Replace with your database name
    
        // Backup file path and name
        $date = date('Y-m-d_H-i-s');
        $backupFile1 = "../../public/lib/backups/4a-pro_backup_$date.sql"; // Primary backup location
        $backupFile2 = "C:/xampp/htdocs/edma/backups/4a-pro_backup_$date.sql"; // Secondary backup location
    
        // Full path to mysqldump
        $mysqldumpPath = 'C:/xampp/mysql/bin/mysqldump.exe';
    
        // Command to run mysqldump for the primary location
        $command1 = "\"$mysqldumpPath\" --opt -h$dbHost -u$dbUsername --password=\"$dbPassword\" --skip-lock-tables $dbName > \"$backupFile1\"";
    
        // Command to run mysqldump for the secondary location
        $command2 = "\"$mysqldumpPath\" --opt -h$dbHost -u$dbUsername --password=\"$dbPassword\" --skip-lock-tables $dbName > \"$backupFile2\"";
    
        // Execute the commands
        exec($command1, $output1, $resultCode1);
        exec($command2, $output2, $resultCode2);
    
        // Check if both commands were successful
        if ($resultCode1 === 0 && $resultCode2 === 0) {
            // Verify both files are created and have data
            if (file_exists($backupFile1) && filesize($backupFile1) > 0 &&
                file_exists($backupFile2) && filesize($backupFile2) > 0) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Database backup completed successfully in both locations!',
                    'files' => [$backupFile1, $backupFile2]
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'One or both backup files are empty. Please try again.',
                    'error' => implode("\n", array_merge($output1, $output2))
                ]);
            }
        } else {
            // If any backup fails, return the failure message and error details
            echo json_encode([
                'success' => false,
                'message' => 'Failed to backup the database in one or both locations.',
                'errors' => [
                    'Primary' => implode("\n", $output1),
                    'Secondary' => implode("\n", $output2)
                ]
            ]);
        }
    }
    
    
    
}    
?>
