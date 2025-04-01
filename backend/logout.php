<?php
// Include database configuration
require_once 'config.php';
require_once 'database.php';

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
    
    // Log the logout activity
    $dbManager = DatabaseManager::getInstance();
    $conn = $dbManager->getConnection();
    
    $ipAddress = $_SERVER['REMOTE_ADDR'];
    $userAgent = $_SERVER['HTTP_USER_AGENT'];
    $activityType = 'logout';
    $description = 'User logged out';
    
    $query = "INSERT INTO user_activity_log (user_id, activity_type, description, ip_address, user_agent) 
              VALUES (?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("issss", $userId, $activityType, $description, $ipAddress, $userAgent);
    $stmt->execute();
    
    // Update attendance record if there's an open check-in
    $query = "UPDATE attendance SET check_out = NOW() 
              WHERE user_id = ? AND check_out IS NULL 
              ORDER BY check_in DESC LIMIT 1";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    
    // Unset all session variables
    $_SESSION = array();
    
    // If a session cookie is used, destroy it
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    
    // Destroy the session
    session_destroy();
    
    // Set success message in a new session
    session_start();
    $_SESSION['success'] = "You have been successfully logged out.";
}

// Redirect to home page
header("Location: ../index.html");
exit();
?>