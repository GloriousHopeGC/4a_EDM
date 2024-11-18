<?php
require_once '../../src/controller/database.php';
// require_once $_SERVER['DOCUMENT_ROOT'] . '/edma/src/controller/database.php';

class userController
{
    // Function for user registration
    public function user_register($email, $password, $role, $name, $gender, $birthday, $address)
    {
        // Initialize the database connection
        $db = new database();
        $con = $db->initDatabase();

        // Start a transaction
        $con->beginTransaction();
        
        try {
            // Insert into the user table
            $stmt = $con->prepare("INSERT INTO user (email, password, role, flag) VALUES (:email, :password, :role, :flag)");
            
            // Hash the password before inserting
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $flag = 1; // Set default flag to 1 (active)
            
            // Bind parameters and execute
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password', $hashed_password);
            $stmt->bindParam(':role', $role);
            $stmt->bindParam(':flag', $flag);
            $stmt->execute();
            
            // Get the last inserted user's id
            $last_user_id = $con->lastInsertId();
            
            // Prepare to insert into user_info table
            $stmt2 = $con->prepare("INSERT INTO user_info (u_id, name, gender, birthday, address, status, created, updated, image_name) 
                                     VALUES (:u_id, :name, :gender, :birthday, :address, :status, NOW(), NOW(), :image_path)");
                                     
            // Bind parameters and execute
            $stmt2->bindParam(':u_id', $last_user_id);
            $stmt2->bindParam(':name', $name);
            $stmt2->bindParam(':gender', $gender);
            $stmt2->bindParam(':birthday', $birthday);
            $stmt2->bindParam(':address', $address);
            $status = 1; // Active status
            $stmt2->bindParam(':status', $status);
            $default_image_path = 'default.png';
            $stmt2->bindParam(':image_path', $default_image_path);
            $stmt2->execute();
            
            // Commit the transaction
            $con->commit();
            
            // Send success response
            return ['status' => 'success', 'icon'=>'success', 'message' => 'Registration successful! Redirecting...'];
        } catch (Exception $e) {
            // Rollback in case of an error
            $con->rollBack();
            return ['status' => 'error', 'icon'=>'error', 'message' => 'Failed to register user: ' . $e->getMessage()];
        }
    }

    // New method to handle user registration from form
    public function handleRegistration()
    {
        // Set header to return JSON response
        header('Content-Type: application/json');
    
        // Check if the form has been submitted
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Get form input values and sanitize them
            $first_name = trim($_POST['first_name']);
            $surname = trim($_POST['surname']);
            $last_name = trim($_POST['last_name']);
            $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
            $password = trim($_POST['password']);
            $confirm_password = trim($_POST['confirm_password']); // Get confirm password
            $gender = trim($_POST['gender']);
            $birthday = trim($_POST['birthday']);
            $address = trim($_POST['address']);
            $name = $first_name . ' ' . $surname . ' ' . $last_name;
    
            // Simple validation
            if (!empty($email) && !empty($password) && !empty($confirm_password) && !empty($name) && !empty($gender) && !empty($birthday) && !empty($address)) {
                // Check if password and confirm password match
                if ($password !== $confirm_password) {
                    // Send error response
                    echo json_encode(['status' => 'error', 'icon'=>'error', 'message' => 'Passwords do not match.']);
                    exit();
                }
    
                // Call the user registration function
                $response = $this->user_register($email, $password, 1, $name, $gender, $birthday, $address); // Set role to 1 automatically
                
                // Send response back
                echo json_encode($response);
                exit();
            } else {
                // Send error response
                echo json_encode(['status' => 'error', 'icon'=>'error', 'message' => 'Please fill in all fields.']);
                exit();
            }
        }
    }
    


    // Check if the login form has been submitted
    public function handleLogin()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Get form input values and sanitize them
            $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
            $password = trim($_POST['password']);
            
            // Simple validation
            if (!empty($email) && !empty($password)) {
                // Instantiate the userController and call the login function
                $controller = new userController();
                $controller->user_login($email, $password);
            } else {
                echo "Please fill in all fields.";
            }
        }
    }
    public function user_login($email, $password) {
        // Initialize the database connection
        $db = new database();
        $con = $db->initDatabase();
        
        // Start the session
        session_start();
    
        // Set header to return JSON response
        header('Content-Type: application/json');
    
        // Prepare a statement to fetch the user
        $stmt = $con->prepare("SELECT * FROM user WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
    
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
        // Check if user exists and verify the password
        if ($user && password_verify($password, $user['password'])) {
            // Set session variables after successful login
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];
    
            // Return success response with a redirect URL
            echo json_encode([
                'icon' => 'success',
                'status' => 'success',
                'message' => 'Login successful! Redirecting...',
                'redirect_url' => ($user['role'] == 2) ? 'admin.php' : 'home.php'
                
            ]);
        } else {
            // If login fails, return an error response
            echo json_encode([
                'status' => 'error',
                'message' => 'Invalid email or password.',
                'icon' => 'error'
            ]);
        }
    
        // Make sure the script stops here
        exit();
    }
    
    public function session(){
        session_start();
        if (isset($_SESSION['user_id'])){
        header('Location: home.php');
        exit();
        }
    }
    public function sessionhome(){
        session_start();
        if (!isset($_SESSION['user_id'])) {
        header('Location: login.php'); // Redirect to login if not logged in
        exit();
        }
    }
    public function getUserData(){
    $db = new database();
    $con = $db->initDatabase();

    // Check if the user is logged in
    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];

        // Prepare a statement to fetch user data
        $stmt = $con->prepare("SELECT * FROM user WHERE id = :id");
        $stmt->bindParam(':id', $user_id);
        $stmt->execute();

        // Fetch the user data
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Optionally, you could fetch related user_info data here as well
            $stmt2 = $con->prepare("SELECT * FROM user_info WHERE u_id = :u_id");
            $stmt2->bindParam(':u_id', $user_id);
            $stmt2->execute();
            $user_info = $stmt2->fetch(PDO::FETCH_ASSOC);
            
            // Return the user data and user info as an associative array
            return [
                'user' => $user,
                'user_info' => $user_info
            ];
        } else {
            return ['error' => 'User not found.'];
        }
    } else {
        return ['error' => 'User not logged in.'];
    }
}
    public function update_user($user_id, $name, $gender, $birthday, $address)
    {
    $db = new database();
    $con = $db->initDatabase();

    try {
        $stmt = $con->prepare("UPDATE user_info SET name = :name, gender = :gender, birthday = :birthday, address = :address, updated = NOW() WHERE u_id = :u_id");
        
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':gender', $gender);
        $stmt->bindParam(':birthday', $birthday);
        $stmt->bindParam(':address', $address);
        $stmt->bindParam(':u_id', $user_id);
        
            if ($stmt->execute()) {
            return ['status' => 'success', 'icon' => 'success', 'message' => 'Profile updated successfully!'];
            } else {
                return ['status' => 'error', 'icon' => 'error', 'message' => 'Failed to update profile.'];
            }
            } catch (Exception $e) {
            return ['status' => 'error', 'icon' => 'error', 'message' => 'Error: ' . $e->getMessage()];
        }
    }
    
public function logout(){
    session_start(); // Start the session
    session_unset(); // Unset all session variables
    session_destroy(); // Destroy the session
    header('Location: login.php'); // Redirect to login
    exit();
}
//calles and handle the logout function if triggered logout
public function handleLogoutAction(){
// Check if logout has been requested
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    $this->logout(); // Call the logout method
}
}



}
?>
