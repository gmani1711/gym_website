<?php
// Include database manager
require_once 'database.php';

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Function to generate CSRF token
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Function to validate CSRF token
function validateCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// Function to log user activity
function logUserActivity($userId, $activityType, $description = '') {
    $dbManager = DatabaseManager::getInstance();
    $conn = $dbManager->getConnection();
    
    $ipAddress = $_SERVER['REMOTE_ADDR'];
    $userAgent = $_SERVER['HTTP_USER_AGENT'];
    
    $query = "INSERT INTO user_activity_log (user_id, activity_type, description, ip_address, user_agent) 
              VALUES (?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("issss", $userId, $activityType, $description, $ipAddress, $userAgent);
    $stmt->execute();
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate CSRF token
    if (!isset($_POST['csrf_token']) || !validateCSRFToken($_POST['csrf_token'])) {
        $_SESSION['error'] = "Invalid form submission. Please try again.";
        header("Location: ../feedback.php");
        exit();
    }
    
    // Get database connection
    $dbManager = DatabaseManager::getInstance();
    $conn = $dbManager->getConnection();
    
    // Get form data and sanitize
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $subject = filter_input(INPUT_POST, 'subject', FILTER_SANITIZE_STRING);
    $message = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_STRING);
    $rating = filter_input(INPUT_POST, 'rating', FILTER_VALIDATE_INT);
    
    // Validate input
    $errors = [];
    
    if (empty($name)) {
        $errors[] = "Name is required";
    }
    
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Valid email is required";
    }
    
    if (empty($subject)) {
        $errors[] = "Subject is required";
    }
    
    if (empty($message)) {
        $errors[] = "Message is required";
    }
    
    if ($rating === false || $rating < 1 || $rating > 5) {
        $errors[] = "Rating must be between 1 and 5";
    }
    
    // If there are errors, redirect back with error messages
    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        $_SESSION['form_data'] = $_POST;
        header("Location: ../feedback.php");
        exit();
    }
    
    // Get user ID if logged in
    $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
    
    // Prepare and execute the query
    $query = "INSERT INTO feedback (user_id, name, email, subject, message, rating) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("issssi", $userId, $name, $email, $subject, $message, $rating);
    
    if ($stmt->execute()) {
        // Log the activity
        if ($userId) {
            logUserActivity($userId, 'other', 'Submitted feedback');
        }
        
        // Set success message and redirect
        $_SESSION['success'] = "Thank you for your feedback! We appreciate your input.";
        header("Location: ../feedback.php");
    } else {
        // Set error message and redirect
        $_SESSION['error'] = "Failed to submit feedback. Please try again later.";
        $_SESSION['form_data'] = $_POST;
        header("Location: ../feedback.php");
    }
    
    $stmt->close();
    exit();
}

// Generate CSRF token for the form
$csrfToken = generateCSRFToken();
?>