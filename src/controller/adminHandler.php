<?php
require_once '../../src/controller/adminController.php'; // Adjust the path if needed

class adminHandler {
    public function __construct() {
        // Initialization code if necessary
    }

    public function handleRequest() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';
            $controller = new adminController();

            switch ($action) {
                case 'getUsers':
                    $controller->getUsers();
                    break;
                case 'updateUserStatus':
                    $controller->updateUserStatus();
                    break;
                case 'getDashboardStats':
                    $controller->getDashboardStats();
                    break;
                case 'backupDatabase':
                    $controller->backupDatabase();
                    break;
                default:
                    echo json_encode(['error' => 'Invalid action']);
            }
        }
    }
}

$adminController = new adminHandler();
$adminController->handleRequest();
?>
