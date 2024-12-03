<?php
require_once '../../src/controller/database.php';
// require_once $_SERVER['DOCUMENT_ROOT'] . '/edma/src/controller/database.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../../vendor/autoload.php';
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
    
        // Prepare a statement to fetch the user and their status
        $stmt = $con->prepare("
            SELECT u.*, ui.status 
            FROM user u 
            LEFT JOIN user_info ui ON u.id = ui.u_id 
            WHERE u.email = :email
        ");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
    
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
        // Check if user exists and verify the password
        if ($user) {
            if ($user['flag'] == 0) {
                // Return an error response if the account is deleted
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Account deleted. Please contact support for assistance.',
                    'icon' => 'error'
                ]);
            } elseif ($user['status'] == 0) {
                // Return an error response if the account is disabled
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Account disabled. Please contact the admin.',
                    'icon' => 'error'
                ]);
            } elseif (password_verify($password, $user['password'])) {
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
                // If password verification fails, return an error response
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Invalid email or password.',
                    'icon' => 'error'
                ]);
            }
        } else {
            // If user does not exist, return an error response
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
public function getData() {
    $db = new database();
    $con = $db->initDatabase();

    // Get the user ID from the URL (assuming the ID is passed in the URL like 'id=32')
    if (isset($_GET['id'])) {
        $user_id = $_GET['id']; // Fetch user ID from URL query parameters

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
        return ['error' => 'User ID not specified in the URL.'];
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
public function sendResetCode($email) {
    $db = new database();
    $con = $db->initDatabase();

    // Check if the email exists
    $stmt = $con->prepare("SELECT id FROM user WHERE email = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Generate a unique reset code
        $resetCode = bin2hex(random_bytes(2));

        // Save the reset code in the database with a timestamp
        $stmt = $con->prepare("UPDATE user SET reset_code = :reset_code, reset_expires = NOW() + INTERVAL 15 MINUTE WHERE email = :email");
        $stmt->execute(['reset_code' => $resetCode, 'email' => $email]);

        // Send the code using PHPMailer
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com'; // Replace with your SMTP server
            $mail->SMTPAuth = true;
            $mail->Username = 'useragcowc@gmail.com'; // Replace with your email
            $mail->Password = 'qkwpcqqcxkvzokbh'; // Replace with your password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // Recipient and sender
            $mail->setFrom('useragcowc@gmail.com', 'AGCOWC TEAM');
            $mail->addAddress($email);

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Password Reset Code';
            $mail->Body = "  <div style=\"font-family: Arial, sans-serif; margin: 0 auto; max-width: 600px; border: 1px solid #ddd; border-radius: 10px; background-color: #ffffff; padding: 20px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);\">
                                <div style=\"text-align: center; padding-bottom: 20px;\">
                                    <h2 style=\"color: #007bff; font-size: 1.5em; margin: 0;\">Password Reset</h2>
                                        <p style=\"color: #555; margin-top: 5px;\">Secure your account</p>
                                </div>
                                    <hr style=\"border: none; border-top: 1px solid #ddd; margin: 20px 0;\" />
        <p style=\"color: #333; font-size: 1em; line-height: 1.6; margin-bottom: 20px;\">
            Hi there,
        </p>
        <p style=\"color: #333; font-size: 1em; line-height: 1.6; margin-bottom: 20px;\">
            You recently requested to reset your password. Please use the code below to complete the process:
        </p>
        <div style=\"background-color: #f7f7f7; border: 1px dashed #ccc; padding: 15px; border-radius: 8px; text-align: center; margin: 20px 0;\">
            <span style=\"font-size: 1.5em; font-weight: bold; color: #ff5722;\">$resetCode</span>
        </div>
        <p style=\"color: #333; font-size: 1em; line-height: 1.6;\">
            If you did not request a password reset, please ignore this email or contact our support team if you have any concerns.
        </p>
        <p style=\"color: #333; font-size: 1em; line-height: 1.6; margin-top: 30px;\">
            Best regards,<br />
            <strong>The Support Team</strong>
        </p>
        <hr style=\"border: none; border-top: 1px solid #ddd; margin: 20px 0;\" />
        <p style=\"color: #aaa; font-size: 0.9em; text-align: center;\">
            This is an automated email, please do not reply.
        </p>
    </div>";

            $mail->send();

            return ['status' => 'success', 'message' => 'Verification code sent to your email.'];
        } catch (Exception $e) {
            return ['status' => 'error', 'message' => 'Error sending email: ' . $mail->ErrorInfo];
        }
    } else {
        return ['status' => 'error', 'message' => 'Email not found.'];
    }
}
public function verifyResetCode($email, $resetCode) {
    $db = new database();
    $con = $db->initDatabase();

    try {
        // Query to check if the reset code exists, is valid, and not expired
        $stmt = $con->prepare("
            SELECT id 
            FROM user 
            WHERE email = :email 
            AND reset_code = :reset_code 
            AND reset_expires > NOW()
        ");
        $stmt->execute([
            'email' => $email, 
            'reset_code' => $resetCode
        ]);

        if ($stmt->rowCount() > 0) {
            // Invalidate the reset code immediately to prevent reuse
            $invalidateStmt = $con->prepare("
                UPDATE user 
                SET reset_code = NULL, reset_expires = NULL 
                WHERE email = :email
            ");
            $invalidateStmt->execute(['email' => $email]);

            return [
                'status' => 'success', 
                'message' => 'Reset code verified.'
            ];
        } else {
            // Handle case where code is invalid or expired
            return [
                'status' => 'error', 
                'message' => 'Invalid or expired reset code. Please request a new one.'
            ];
        }
    } catch (PDOException $e) {
        // Log error for debugging purposes
        error_log("Error verifying reset code for email $email: " . $e->getMessage());

        // Return a user-friendly error message
        return [
            'status' => 'error', 
            'message' => 'An error occurred while verifying the reset code. Please try again later.'
        ];
    }
}

public function changeForgotPassword($email, $newPassword) {
    // Make sure the password is strong enough
    if (strlen($newPassword) < 8) {
        return ['status' => 'error', 'message' => 'Password must be at least 8 characters long.'];
    }

    // Hash the password before saving
    $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);

    // Update the password in the database
    $query = "UPDATE user SET password = :password WHERE email = :email";
    $params = [
        ':password' => $hashedPassword,
        ':email' => $email
    ];

    // Create a new database instance and execute the query
    $db = new database();
    $result = $db->execute($query, $params);

    if ($result) {
        return ['status' => 'success', 'message' => 'Password changed successfully.'];
    } else {
        return ['status' => 'error', 'message' => 'Failed to update password.'];
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
