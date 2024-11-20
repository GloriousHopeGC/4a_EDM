<?php
require_once '../../src/controller/database.php';
// require_once $_SERVER['DOCUMENT_ROOT'] . '/edma/src/controller/database.php';

class userController
{
    // Function for user registration
    public function user_register($email, $password, $role, $name, $gender, $birthday, $address){
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
            
            // Determine the default image path based on gender
            $default_image_path = 'default.png'; // Default fallback
            if (strtolower($gender) === 'male') {
                $default_image_path = 'male.jpg';
            } elseif (strtolower($gender) === 'female') {
                $default_image_path = 'female.jpg';
            }
            
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
    public function handleRegistration(){
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
    public function handleLogin(){
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
    
    public function session() {
        session_start();
        if (isset($_SESSION['user_id'])) {
            header('Location: home.php');
            exit();
        }
    }
    
    public function sessionhome() {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            header('Location: login.php'); // Redirect to login if not logged in
            exit();
        } elseif ($_SESSION['role'] == 2) {
            header('Location: admin.php'); // Redirect admins to the admin dashboard
            exit();
        }
    }
    
    public function sessionAdmin() {
        session_start();
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 2) {
            header('Location: home.php'); // Redirect to home page if not an admin (role = 2)
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
    public function updateEmail($user_id, $new_email)
    {
    $db = new database();
    $con = $db->initDatabase();

    try {
        // Check if the email is valid
        if (!filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
            return ['status' => 'error', 'message' => 'Invalid email format.'];
        }

        // Check if the email is already in use by another user
        $checkEmailStmt = $con->prepare("SELECT id FROM user WHERE email = :new_email AND id != :user_id");
        $checkEmailStmt->bindParam(':new_email', $new_email, PDO::PARAM_STR);
        $checkEmailStmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $checkEmailStmt->execute();

        if ($checkEmailStmt->rowCount() > 0) {
            return ['status' => 'error', 'message' => 'Email is already in use by another user.'];
        }

        // Update the user's email
        $stmt = $con->prepare("UPDATE user SET email = :new_email WHERE id = :user_id");
        $stmt->bindParam(':new_email', $new_email, PDO::PARAM_STR);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            return ['status' => 'success', 'message' => 'Email updated successfully!'];
        } else {
            $errorInfo = $stmt->errorInfo();
            return ['status' => 'error', 'message' => 'Database error: ' . $errorInfo[2]];
        }
    } catch (PDOException $e) {
        return ['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()];
    } catch (Exception $e) {
        return ['status' => 'error', 'message' => 'Error: ' . $e->getMessage()];
    }
}
public function changePassword($user_id, $currentPassword, $newPassword, $confirmPassword)
{
    try {
        // Validate input
        if (empty($user_id) || empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
            return ['error' => 'All fields are required.'];
        }

        if ($newPassword !== $confirmPassword) {
            return ['error' => 'New passwords do not match.'];
        }

        // Initialize the database connection
        $db = new database();
        $con = $db->initDatabase();

        // Fetch the user's current password
        $stmt = $con->prepare("SELECT password FROM user WHERE id = :id");
        $stmt->execute(['id' => $user_id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            return ['error' => 'User not found.'];
        }

        // Verify the current password
        if (!password_verify($currentPassword, $user['password'])) {
            return ['error' => 'Incorrect current password.'];
        }

        // Hash the new password
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        // Update the password in the database
        $updateStmt = $con->prepare("UPDATE user SET password = :password WHERE id = :id");
        $updateStmt->execute([
            'password' => $hashedPassword,
            'id' => $user_id
        ]);

        // Return success response
        return ['success' => true, 'message' => 'Password changed successfully.'];
    } catch (PDOException $e) {
        return ['error' => 'Database error: ' . $e->getMessage()];
    } catch (Exception $e) {
        return ['error' => 'Error: ' . $e->getMessage()];
    }
}
public function updateProfileImage($userId, $image)
{
    // Initialize response array
    $response = ['status' => 'error', 'message' => ''];

    // Validate file type (allow JPG, PNG, GIF)
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    if (!in_array($image['type'], $allowedTypes)) {
        $response['icon'] = 'error';
        $response['message'] = 'Invalid file type. Only JPG, PNG, or GIF are allowed.';
        return $response;
    } elseif ($image['size'] > 2 * 1024 * 1024) { // 2MB file size limit
        $response['icon'] = 'error';
        $response['message'] = 'File size exceeds the 2MB limit.';
        return $response;
    }

    // Define upload directory
    $uploadDir = '../../public/lib/images/user_profile/';

    // Check if upload directory is writable
    if (!is_dir($uploadDir) || !is_writable($uploadDir)) {
        $response['icon'] = 'error';
        $response['message'] = 'Upload directory is not writable or does not exist.';
        return $response;
    }

    // Initialize database connection
    try {
        $db = new database();
        $con = $db->initDatabase();

        // Retrieve the current image name from the database
        $stmt = $con->prepare("SELECT image_name FROM user_info WHERE u_id = :user_id");
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        $user = $stmt->fetch();

        // If the user already has a profile image, delete it
        if ($user && $user['image_name']) {
            $existingImage = $user['image_name'];
            $existingImagePath = 'C:\\xampp\\htdocs\\edma\\public\\lib\\images\\user_profile\\' . $existingImage;

            // Check if the file exists and delete it
            if (file_exists($existingImagePath)) {
                unlink($existingImagePath); // Delete the old profile image
            }
        }

        // Generate unique file name by adding userId and timestamp for uniqueness
        $timestamp = time();
        $originalFileName = basename($image['name']);
        $imageName = $userId . '_' . $timestamp . '_' . $originalFileName;
        $targetFilePath = $uploadDir . $imageName;

        // Move the uploaded file to the target directory
        if (move_uploaded_file($image['tmp_name'], $targetFilePath)) {
            // Update the user profile image in the database
            $finalImageName = $imageName;
            $stmt = $con->prepare("UPDATE user_info SET updated = NOW(), image_name = :image_name WHERE u_id = :user_id");
            $stmt->bindParam(':image_name', $finalImageName);
            $stmt->bindParam(':user_id', $userId);
            $stmt->execute();

            // Return success response
            $response['icon'] = 'success';
            $response['status'] = 'success';
            $response['message'] = 'Profile image updated successfully.';
            $response['imageName'] = $finalImageName;
        } else {
            $response['icon'] = 'error';
            $response['message'] = 'Failed to upload the file.';
            error_log("Failed to move uploaded file to target location.");
        }
    } catch (Exception $e) {
        $response['message'] = 'Failed to update profile image in the database: ' . $e->getMessage();
        error_log("Error updating profile image: " . $e->getMessage());
    }

    return $response;
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
