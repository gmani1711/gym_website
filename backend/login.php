<?php
// Include database configuration
require_once 'config.php';
require_once 'database.php';

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Process login form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get database connection
    $dbManager = DatabaseManager::getInstance();
    $conn = $dbManager->getConnection();
    
    // Collect form data
    $email = isset($_POST['email']) ? $conn->real_escape_string($_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $rememberMe = isset($_POST['rememberMe']) ? true : false;
    
    // Validate required fields
    if (empty($email) || empty($password)) {
        $_SESSION['error'] = "Email and password are required.";
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit();
    }
    
    // Check if user exists
    $query = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        
        // Verify password
        if (password_verify($password, $user['password'])) {
            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['is_admin'] = $user['is_admin'];
            
            // Set remember me cookie if checked
            if ($rememberMe) {
                $token = bin2hex(random_bytes(32));
                $expires = time() + (86400 * 30); // 30 days
                
                // Store token in database
                $query = "INSERT INTO user_tokens (user_id, token, expires_at) VALUES (?, ?, FROM_UNIXTIME(?))";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("isi", $user['id'], $token, $expires);
                $stmt->execute();
                
                // Set cookie
                setcookie('remember_token', $token, $expires, '/');
            }
            
            // Log the login activity
            $ipAddress = $_SERVER['REMOTE_ADDR'];
            $userAgent = $_SERVER['HTTP_USER_AGENT'];
            $activityType = 'login';
            $description = 'User logged in';
            
            $query = "INSERT INTO user_activity_log (user_id, activity_type, description, ip_address, user_agent, created_at) 
                      VALUES (?, ?, ?, ?, ?, NOW())";
            
            $stmt = $conn->prepare($query);
            $stmt->bind_param("issss", $user['id'], $activityType, $description, $ipAddress, $userAgent);
            $stmt->execute();
            
            // Redirect based on user type
            if ($user['is_admin'] == 1) {
                header("Location: ../admin.php");
            } else {
                header("Location: ../dashboard.php");
            }
            exit();
        } else {
            $_SESSION['error'] = "Invalid password. Please try again.";
        }
    } else {
        $_SESSION['error'] = "Email not found. Please register or try again.";
    }
    
    // Redirect back to the referring page
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
} else {
    // If not a POST request, redirect to home page
    header("Location: ../index.html");
    exit();
}

// Check for remember me token
function checkRememberMeToken() {
    if (isset($_COOKIE['remember_token'])) {
        $token = $_COOKIE['remember_token'];
        
        // Get database connection
        $dbManager = DatabaseManager::getInstance();
        $conn = $dbManager->getConnection();
        
        // Check if token exists and is valid
        $query = "SELECT u.* FROM users u 
                  JOIN user_tokens t ON u.id = t.user_id 
                  WHERE t.token = ? AND t.expires_at > NOW()";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows == 1) {
            $user = $result->fetch_assoc();
            
            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['is_admin'] = $user['is_admin'];
            
            // Redirect based on user type
            if ($user['is_admin'] == 1) {
                header("Location: ../admin.php");
            } else {
                header("Location: ../dashboard.php");
            }
            exit();
        }
    }
}

// Check for remember me token on page load
checkRememberMeToken();
?>