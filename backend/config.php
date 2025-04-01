<?php
// Database configuration
$host = "localhost";
$username = "root";
$password = "";
$database = "gym_website";

// Create database connection
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set character set
$conn->set_charset("utf8mb4");

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Function to sanitize user input
function sanitize_input($data) {
    global $conn;
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    $data = $conn->real_escape_string($data);
    return $data;
}

// Function to check if user is logged in
function is_logged_in() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

// Function to redirect user
function redirect($url) {
    header("Location: $url");
    exit();
}

// Function to generate random string
function generate_random_string($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $random_string = '';
    for ($i = 0; $i < $length; $i++) {
        $random_string .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $random_string;
}

// Function to calculate BMI
function calculate_bmi($weight, $height) {
    // Height in meters (convert from cm)
    $height_m = $height / 100;
    // BMI formula: weight (kg) / (height (m) * height (m))
    $bmi = $weight / ($height_m * $height_m);
    return round($bmi, 2);
}

// Function to get BMI category
function get_bmi_category($bmi) {
    if ($bmi < 18.5) {
        return "Underweight";
    } elseif ($bmi >= 18.5 && $bmi < 25) {
        return "Normal weight";
    } elseif ($bmi >= 25 && $bmi < 30) {
        return "Overweight";
    } else {
        return "Obese";
    }
}

// Create database tables if they don't exist
function create_database_tables() {
    global $conn;
    
    // Users table
    $sql = "CREATE TABLE IF NOT EXISTS users (
        id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        first_name VARCHAR(50) NOT NULL,
        last_name VARCHAR(50) NOT NULL,
        email VARCHAR(100) NOT NULL UNIQUE,
        phone VARCHAR(15) NOT NULL,
        password VARCHAR(255) NOT NULL,
        height INT(3) NOT NULL,
        weight INT(3) NOT NULL,
        age INT(3) NOT NULL,
        bmi DECIMAL(4,2) NOT NULL,
        fitness_goals VARCHAR(50) NOT NULL,
        health_conditions VARCHAR(255),
        membership_plan VARCHAR(20) NOT NULL,
        registration_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $conn->query($sql);
    
    // Attendance table
    $sql = "CREATE TABLE IF NOT EXISTS attendance (
        id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        user_id INT(11) UNSIGNED NOT NULL,
        check_in DATETIME NOT NULL,
        check_out DATETIME,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )";
    $conn->query($sql);
    
    // Workouts table
    $sql = "CREATE TABLE IF NOT EXISTS workouts (
        id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        user_id INT(11) UNSIGNED NOT NULL,
        workout_date DATE NOT NULL,
        workout_type VARCHAR(50) NOT NULL,
        duration INT(3) NOT NULL,
        calories_burned INT(5),
        notes TEXT,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )";
    $conn->query($sql);
    
    // Health tracking table
    $sql = "CREATE TABLE IF NOT EXISTS health_tracking (
        id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        user_id INT(11) UNSIGNED NOT NULL,
        tracking_date DATE NOT NULL,
        weight INT(3) NOT NULL,
        bmi DECIMAL(4,2) NOT NULL,
        notes TEXT,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )";
    $conn->query($sql);
    
    // Feedback table
    $sql = "CREATE TABLE IF NOT EXISTS feedback (
        id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        user_id INT(11) UNSIGNED,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(100) NOT NULL,
        subject VARCHAR(100) NOT NULL,
        message TEXT NOT NULL,
        submission_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
    )";
    $conn->query($sql);
}

// Call the function to create tables
create_database_tables();
?>

```php project="Gym Website" file="backend/register.php" type="code"
<?php
// Include database configuration
require_once 'config.php';

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data and sanitize
    $firstName = sanitize_input($_POST['firstName']);
    $lastName = sanitize_input($_POST['lastName']);
    $email = sanitize_input($_POST['email']);
    $phone = sanitize_input($_POST['phone']);
    $height = sanitize_input($_POST['height']);
    $weight = sanitize_input($_POST['weight']);
    $age = sanitize_input($_POST['age']);
    $fitnessGoals = sanitize_input($_POST['fitnessGoals']);
    $plan = sanitize_input($_POST['plan']);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirmPassword'];
    
    // Check if passwords match
    if ($password !== $confirmPassword) {
        $_SESSION['error'] = "Passwords do not match!";
        redirect("../membership.html");
        exit();
    }
    
    // Hash password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
    // Calculate BMI
    $bmi = calculate_bmi($weight, $height);
    
    // Process health conditions
    $healthConditions = isset($_POST['healthConditions']) ? $_POST['healthConditions'] : [];
    $healthConditionsStr = implode(',', $healthConditions);
    
    // Check if email already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $_SESSION['error'] = "Email already exists. Please use a different email or login.";
        redirect("../membership.html");
        exit();
    }
    
    // Insert user data into database
    $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, email, phone, password, height, weight, age, bmi, fitness_goals, health_conditions, membership_plan) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssiidssss", $firstName, $lastName, $email, $phone, $hashedPassword, $height, $weight, $age, $bmi, $fitnessGoals, $healthConditionsStr, $plan);
    
    if ($stmt->execute()) {
        // Get the user ID
        $userId = $stmt->insert_id;
        
        // Set session variables
        $_SESSION['user_id'] = $userId;
        $_SESSION['user_name'] = $firstName . ' ' . $lastName;
        $_SESSION['user_email'] = $email;
        $_SESSION['membership_plan'] = $plan;
        
        // Create initial health tracking record
        $currentDate = date('Y-m-d');
        $stmt = $conn->prepare("INSERT INTO health_tracking (user_id, tracking_date, weight, bmi) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isid", $userId, $currentDate, $weight, $bmi);
        $stmt->execute();
        
        // Redirect to dashboard
        $_SESSION['success'] = "Registration successful! Welcome to FitLife Gym.";
        redirect("../dashboard.php");
    } else {
        $_SESSION['error'] = "Registration failed: " . $conn->error;
        redirect("../membership.html");
    }
    
    $stmt->close();
} else {
    // If not a POST request, redirect to membership page
    redirect("../membership.html");
}

$conn->close();
?>