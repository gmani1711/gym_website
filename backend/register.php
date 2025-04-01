<?php
// Include database configuration
require_once 'config.php';
require_once 'database.php';

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Function to validate registration code
function validateRegistrationCode($code, $conn) {
    if (empty($code)) {
        return true; // No code provided, so no validation needed
    }
    
    // Check if code matches format FIT-XXXX-XXXX
    if (!preg_match('/^FIT-\d{4}-\d{4}$/', $code)) {
        return false;
    }
    
    // Check if code exists in database and is not used
    $query = "SELECT * FROM registration_codes WHERE code = ? AND is_used = 0";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $code);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        return true;
    }
    
    return false;
}

// Function to mark registration code as used
function markCodeAsUsed($code, $userId, $conn) {
    if (empty($code)) {
        return;
    }
    
    $query = "UPDATE registration_codes SET is_used = 1, used_by = ?, used_at = NOW() WHERE code = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("is", $userId, $code);
    $stmt->execute();
}

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get database connection
    $dbManager = DatabaseManager::getInstance();
    $conn = $dbManager->getConnection();
    
    // Validate registration code if provided
    $registrationCode = isset($_POST['registrationCode']) ? trim($_POST['registrationCode']) : '';
    if (!empty($registrationCode) && !validateRegistrationCode($registrationCode, $conn)) {
        $_SESSION['error'] = "Invalid registration code. Please try again.";
        header("Location: ../membership.html");
        exit();
    }
    
    // Collect form data
    $firstName = isset($_POST['firstName']) ? $conn->real_escape_string($_POST['firstName']) : '';
    $lastName = isset($_POST['lastName']) ? $conn->real_escape_string($_POST['lastName']) : '';
    $email = isset($_POST['email']) ? $conn->real_escape_string($_POST['email']) : '';
    $phone = isset($_POST['phone']) ? $conn->real_escape_string($_POST['phone']) : '';
    $password = isset($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : ''; // Hash password
    $dob = isset($_POST['dob']) ? $conn->real_escape_string($_POST['dob']) : '';
    $gender = isset($_POST['gender']) ? $conn->real_escape_string($_POST['gender']) : '';
    $address = isset($_POST['address']) ? $conn->real_escape_string($_POST['address']) : '';
    $city = isset($_POST['city']) ? $conn->real_escape_string($_POST['city']) : '';
    $pincode = isset($_POST['pincode']) ? $conn->real_escape_string($_POST['pincode']) : '';
    $emergencyName = isset($_POST['emergencyName']) ? $conn->real_escape_string($_POST['emergencyName']) : '';
    $emergencyPhone = isset($_POST['emergencyPhone']) ? $conn->real_escape_string($_POST['emergencyPhone']) : '';
    $height = isset($_POST['height']) ? floatval($_POST['height']) : 0;
    $weight = isset($_POST['weight']) ? floatval($_POST['weight']) : 0;
    $fitnessLevel = isset($_POST['fitnessLevel']) ? $conn->real_escape_string($_POST['fitnessLevel']) : '';
    $medicalCondition = isset($_POST['medicalCondition']) ? $conn->real_escape_string($_POST['medicalCondition']) : 'no';
    $medicalDetails = ($medicalCondition === 'yes' && isset($_POST['medicalDetails'])) ? $conn->real_escape_string($_POST['medicalDetails']) : '';
    $preferredTime = isset($_POST['preferredTime']) ? $conn->real_escape_string($_POST['preferredTime']) : '';
    $plan = isset($_POST['plan']) ? $conn->real_escape_string($_POST['plan']) : '';
    $price = isset($_POST['price']) ? floatval($_POST['price']) : 0;
    
    // Validate required fields
    if (empty($firstName) || empty($lastName) || empty($email) || empty($phone) || empty($password) || 
        empty($dob) || empty($gender) || empty($address) || empty($city) || empty($pincode) || 
        empty($emergencyName) || empty($emergencyPhone) || empty($height) || empty($weight) || 
        empty($fitnessLevel) || empty($preferredTime) || empty($plan) || empty($price)) {
        $_SESSION['error'] = "All required fields must be filled out.";
        header("Location: ../membership.html");
        exit();
    }
    
    // Check if email already exists
    $query = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $_SESSION['error'] = "Email already registered. Please use a different email or login.";
        header("Location: ../membership.html");
        exit();
    }
    
    // Begin transaction
    $conn->begin_transaction();
    
    try {
        // Insert user data
        $query = "INSERT INTO users (first_name, last_name, email, phone, password, dob, gender, address, city, pincode, 
                  emergency_contact_name, emergency_contact_phone, created_at) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
        
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssssssssssss", $firstName, $lastName, $email, $phone, $password, $dob, $gender, 
                          $address, $city, $pincode, $emergencyName, $emergencyPhone);
        $stmt->execute();
        
        // Get the user ID
        $userId = $conn->insert_id;
        
        // Insert health data
        $query = "INSERT INTO health_metrics (user_id, height, weight, bmi, fitness_level, medical_condition, medical_details, created_at) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";
        
        $bmi = $weight / (($height / 100) * ($height / 100)); // Calculate BMI
        $hasMedicalCondition = $medicalCondition === 'yes' ? 1 : 0;
        
        $stmt = $conn->prepare($query);
        $stmt->bind_param("iddssis", $userId, $height, $weight, $bmi, $fitnessLevel, $hasMedicalCondition, $medicalDetails);
        $stmt->execute();
        
        // Insert membership data
        $query = "INSERT INTO memberships (user_id, plan_name, price, start_date, end_date, preferred_time, status, created_at) 
                  VALUES (?, ?, ?, NOW(), DATE_ADD(NOW(), INTERVAL 1 MONTH), ?, 'active', NOW())";
        
        $stmt = $conn->prepare($query);
        $stmt->bind_param("isds", $userId, $plan, $price, $preferredTime);
        $stmt->execute();
        
        // Insert class preferences if selected
        if (isset($_POST['classes']) && is_array($_POST['classes'])) {
            $classes = $_POST['classes'];
            
            foreach ($classes as $class) {
                $class = $conn->real_escape_string($class);
                
                $query = "INSERT INTO user_classes (user_id, class_name, created_at) VALUES (?, ?, NOW())";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("is", $userId, $class);
                $stmt->execute();
            }
        }
        
        // Mark registration code as used if provided
        if (!empty($registrationCode)) {
            markCodeAsUsed($registrationCode, $userId, $conn);
        }
        
        // Log the registration activity
        $ipAddress = $_SERVER['REMOTE_ADDR'];
        $userAgent = $_SERVER['HTTP_USER_AGENT'];
        $activityType = 'registration';
        $description = 'User registered with ' . $plan . ' plan';
        
        $query = "INSERT INTO user_activity_log (user_id, activity_type, description, ip_address, user_agent, created_at) 
                  VALUES (?, ?, ?, ?, ?, NOW())";
        
        $stmt = $conn->prepare($query);
        $stmt->bind_param("issss", $userId, $activityType, $description, $ipAddress, $userAgent);
        $stmt->execute();
        
        // Commit transaction
        $conn->commit();
        
        // Set success message
        $_SESSION['success'] = "Registration successful! You can now login with your email and password.";
        
        // Redirect to login page
        header("Location: ../index.html");
        exit();
        
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        
        $_SESSION['error'] = "Registration failed: " . $e->getMessage();
        header("Location: ../membership.html");
        exit();
    }
} else {
    // If not a POST request, redirect to membership page
    header("Location: ../membership.html");
    exit();
}
?>