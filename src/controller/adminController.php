<?php
require_once '../../src/controller/database.php';

class adminController {
    public function getUsers() {
        try {
            $page = isset($_POST['page']) ? (int)$_POST['page'] : 1;
            $limit = 5;
            $offset = ($page - 1) * $limit;

            $db = new database();
            $con = $db->initDatabase();

            $sql = "SELECT 
                        u.id, 
                        u.email, 
                        u.role, 
                        ui.name, 
                        ui.gender, 
                        ui.birthday, 
                        ui.address,
                        ui.status,
                        ui.created,
                        ui.updated
                    FROM user u
                    JOIN user_info ui ON u.id = ui.u_id
                    WHERE u.role != 2 
                    AND u.flag != 0
                    LIMIT :limit OFFSET :offset";
            $stmt = $con->prepare($sql);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();

            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $sqlCount = "SELECT COUNT(*) FROM user u JOIN user_info ui ON u.id = ui.u_id WHERE u.role != 2";
            $stmtCount = $con->query($sqlCount);
            $totalUsers = $stmtCount->fetchColumn();

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
                $status = $_POST['status'];

                $db = new database();
                $con = $db->initDatabase();

                $sql = "SELECT status FROM user_info WHERE u_id = :user_id";
                $stmt = $con->prepare($sql);
                $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
                $stmt->execute();
                $currentStatus = $stmt->fetchColumn();

                if ($currentStatus == $status) {
                    echo json_encode(['success' => false, 'message' => 'User is already ' . ($status == 1 ? 'enabled' : 'disabled')]);
                    return;
                }

                $sql = "UPDATE user_info SET status = :status WHERE u_id = :user_id";
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

            $sqlUsers = "SELECT COUNT(*) FROM user WHERE flag = 1 AND role != 2";
            $stmtUsers = $con->query($sqlUsers);
            $userCount = $stmtUsers->fetchColumn();

            $sqlPosts = "SELECT COUNT(*) FROM posts";
            $stmtPosts = $con->query($sqlPosts);
            $postCount = $stmtPosts->fetchColumn();

            $sqlComments = "SELECT COUNT(*) FROM comment";
            $stmtComments = $con->query($sqlComments);
            $commentCount = $stmtComments->fetchColumn();

            echo json_encode([
                'userCount' => $userCount,
                'postCount' => $postCount,
                'commentCount' => $commentCount
            ]);

        } catch (Exception $e) {
            echo json_encode(["error" => "Failed to fetch dashboard stats: " . $e->getMessage()]);
        }
    }

    public function getUserPosts() {
        if (isset($_POST['userId'])) {
            try {
                $userId = $_POST['userId'];

                $db = new database();
                $con = $db->initDatabase();

                $sql = "SELECT posts.*, user_info.name AS user_name, user_info.image_name 
                        FROM posts
                        JOIN user_info ON posts.ui_id = user_info.id
                        JOIN user ON user.id = user_info.u_id
                        WHERE user.flag != 0 AND user_info.status != 0
                        ORDER BY posts.created_at DESC";
                $stmt = $con->prepare($sql);
                $stmt->execute();

                $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

                echo json_encode([
                    'posts' => $posts
                ]);
            } catch (Exception $e) {
                echo json_encode(["error" => "Failed to fetch posts: " . $e->getMessage()]);
            }
        } else {
            echo json_encode(['error' => 'User ID is required']);
        }
    }

    public function deletePost() {
        if (isset($_POST['postId'])) {
            try {
                $postId = $_POST['postId'];

                $db = new database();
                $con = $db->initDatabase();

                $sql = "DELETE FROM posts WHERE post_id = :postId";
                $stmt = $con->prepare($sql);
                $stmt->bindParam(':postId', $postId, PDO::PARAM_INT);
                $stmt->execute();

                if ($stmt->rowCount() > 0) {
                    echo json_encode(['success' => true, 'message' => 'Post deleted successfully']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Post not found or already deleted']);
                }

            } catch (Exception $e) {
                echo json_encode(["error" => "Failed to delete post: " . $e->getMessage()]);
            }
        } else {
            echo json_encode(['error' => 'Post ID is required']);
        }
    }

    public function backupDatabase() {
        $dbHost = 'localhost';
        $dbUsername = 'root';
        $dbPassword = '';
        $dbName = '4a-pro';

        $date = date('Y-m-d_H-i-s');
        $backupFile1 = "../../public/lib/backups/4a-pro_backup_$date.sql";
        $backupFile2 = "C:/xampp/htdocs/edma/backups/4a-pro_backup_$date.sql";

        $mysqldumpPath = 'C:/xampp/mysql/bin/mysqldump.exe';

        $command1 = "\"$mysqldumpPath\" --opt -h$dbHost -u$dbUsername --password=\"$dbPassword\" --skip-lock-tables $dbName > \"$backupFile1\"";
        $command2 = "\"$mysqldumpPath\" --opt -h$dbHost -u$dbUsername --password=\"$dbPassword\" --skip-lock-tables $dbName > \"$backupFile2\"";

        exec($command1, $output1, $resultCode1);
        exec($command2, $output2, $resultCode2);

        if ($resultCode1 === 0 && $resultCode2 === 0) {
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
