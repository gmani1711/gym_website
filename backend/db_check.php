<?php
// Include database configuration
require_once 'config.php';
require_once 'database.php';

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Get database connection
$dbManager = DatabaseManager::getInstance();
$conn = $dbManager->getConnection();

// Function to check if a table exists
function tableExists($tableName, $conn) {
    $result = $conn->query("SHOW TABLES LIKE '$tableName'");
    return $result->num_rows > 0;
}

// Function to check if a column exists in a table
function columnExists($tableName, $columnName, $conn) {
    $result = $conn->query("SHOW COLUMNS FROM `$tableName` LIKE '$columnName'");
    return $result->num_rows > 0;
}

// Check and create users table if it doesn't exist
if (!tableExists('users', $conn)) {
    $conn->query("
        CREATE TABLE users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            first_name VARCHAR(50) NOT NULL,
            last_name VARCHAR(50) NOT NULL,
            email VARCHAR(100) NOT NULL UNIQUE,
            phone VARCHAR(20) NOT NULL,
            password VARCHAR(255) NOT NULL,
            dob DATE NOT NULL,
            gender ENUM('male', 'female', 'other') NOT NULL,
            address TEXT NOT NULL,
            city VARCHAR(50) NOT NULL,
            pincode VARCHAR(10) NOT NULL,
            emergency_contact_name VARCHAR(100) NOT NULL,
            emergency_contact_phone VARCHAR(20) NOT NULL,
            is_admin TINYINT(1) DEFAULT 0,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");
    echo "Created users table.<br>";
}

// Check and create health_metrics table if it doesn't exist
if (!tableExists('health_metrics', $conn)) {
    $conn->query("
        CREATE TABLE health_metrics (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            height DECIMAL(5,2) NOT NULL,
            weight DECIMAL(5,2) NOT NULL,
            bmi DECIMAL(5,2) NOT NULL,
            body_fat DECIMAL(5,2) DEFAULT NULL,
            muscle_mass DECIMAL(5,2) DEFAULT NULL,
            fitness_level ENUM('beginner', 'intermediate', 'advanced') NOT NULL,
            medical_condition TINYINT(1) DEFAULT 0,
            medical_details TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");
    echo "Created health_metrics table.<br>";
}

// Check and create memberships table if it doesn't exist
if (!tableExists('memberships', $conn)) {
    $conn->query("
        CREATE TABLE memberships (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            plan_name VARCHAR(50) NOT NULL,
            price DECIMAL(10,2) NOT NULL,
            start_date DATE NOT NULL,
            end_date DATE NOT NULL,
            preferred_time ENUM('morning', 'afternoon', 'evening') NOT NULL,
            status ENUM('active', 'expired', 'cancelled', 'pending') NOT NULL DEFAULT 'active',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");
    echo "Created memberships table.<br>";
}

// Check and create user_classes table if it doesn't exist
if (!tableExists('user_classes', $conn)) {
    $conn->query("
        CREATE TABLE user_classes (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            class_name VARCHAR(50) NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");
    echo "Created user_classes table.<br>";
}

// Check and create registration_codes table if it doesn't exist
if (!tableExists('registration_codes', $conn)) {
    $conn->query("
        CREATE TABLE registration_codes (
            id INT AUTO_INCREMENT PRIMARY KEY,
            code VARCHAR(15) NOT NULL UNIQUE,
            discount_percentage INT DEFAULT 0,
            discount_amount DECIMAL(10,2) DEFAULT 0.00,
            is_used TINYINT(1) DEFAULT 0,
            used_by INT,
            used_at DATETIME,
            expiry_date DATE,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (used_by) REFERENCES users(id) ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");
    echo "Created registration_codes table.<br>";
    
    // Insert some sample registration codes
    $conn->query("
        INSERT INTO registration_codes (code, discount_percentage, expiry_date)
        VALUES 
        ('FIT-1234-5678', 10, DATE_ADD(NOW(), INTERVAL 3 MONTH)),
        ('FIT-2345-6789', 15, DATE_ADD(NOW(), INTERVAL 3 MONTH)),
        ('FIT-3456-7890', 20, DATE_ADD(NOW(), INTERVAL 3 MONTH))
    ");
    echo "Added sample registration codes.<br>";
}

// Check and create user_activity_log table if it doesn't exist
if (!tableExists('user_activity_log', $conn)) {
    $conn->query("
        CREATE TABLE user_activity_log (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            activity_type VARCHAR(50) NOT NULL,
            description TEXT,
            ip_address VARCHAR(45),
            user_agent TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");
    echo "Created user_activity_log table.<br>";
}

// Check and create diet_plans table if it doesn't exist
if (!tableExists('diet_plans', $conn)) {
    $conn->query("
        CREATE TABLE diet_plans (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            plan_name VARCHAR(100) NOT NULL,
            calorie_goal INT NOT NULL,
            protein_goal INT NOT NULL,
            carbs_goal INT NOT NULL,
            fat_goal INT NOT NULL,
            is_active TINYINT(1) DEFAULT 1,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");
    echo "Created diet_plans table.<br>";
}

// Check and create food_log table if it doesn't exist
if (!tableExists('food_log', $conn)) {
    $conn->query("
        CREATE TABLE food_log (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            food_name VARCHAR(100) NOT NULL,
            calories INT NOT NULL,
            protein DECIMAL(5,2) NOT NULL,
            carbs DECIMAL(5,2) NOT NULL,
            fat DECIMAL(5,2) NOT NULL,
            meal_type ENUM('breakfast', 'lunch', 'dinner', 'snack') NOT NULL,
            log_date DATE NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");
    echo "Created food_log table.<br>";
}

// Check and create workout_log table if it doesn't exist
if (!tableExists('workout_log', $conn)) {
    $conn->query("
        CREATE TABLE workout_log (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            exercise VARCHAR(100) NOT NULL,
            sets INT NOT NULL,
            reps INT NOT NULL,
            weight DECIMAL(5,2) NOT NULL,
            workout_date DATE NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");
    echo "Created workout_log table.<br>";
}

// Check if admin user exists, if not create one
$query = "SELECT * FROM users WHERE is_admin = 1";
$result = $conn->query($query);

if ($result->num_rows == 0) {
    // Create admin user
    $adminFirstName = "Admin";
    $adminLastName = "User";
    $adminEmail = "admin@fitlifegym.com";
    $adminPhone = "9876543210";
    $adminPassword = password_hash("admin123", PASSWORD_DEFAULT); // Default password, should be changed
    $adminDob = "1990-01-01";
    $adminGender = "male";
    $adminAddress = "123 Admin Street";
    $adminCity = "Mumbai";
    $adminPincode = "400001";
    $adminEmergencyName = "Emergency Contact";
    $adminEmergencyPhone = "9876543211";
    
    $query = "INSERT INTO users (first_name, last_name, email, phone, password, dob, gender, address, city, pincode, 
              emergency_contact_name, emergency_contact_phone, is_admin, created_at) 
              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1, NOW())";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssssssssssss", $adminFirstName, $adminLastName, $adminEmail, $adminPhone, $adminPassword, 
                      $adminDob, $adminGender, $adminAddress, $adminCity, $adminPincode, 
                      $adminEmergencyName, $adminEmergencyPhone);
    $stmt->execute();
    
    echo "Created admin user (Email: admin@fitlifegym.com, Password: admin123).<br>";
}

echo "Database check completed successfully!";
?>