<?php
// This file contains SQL statements to update the database schema
// It should be run manually by an administrator

// Include database configuration
require_once 'config.php';
require_once 'database.php';

// Get database connection
$dbManager = DatabaseManager::getInstance();
$conn = $dbManager->getConnection();

// Begin transaction
$conn->begin_transaction();

try {
    // Create registration_codes table
    $conn->query("
        CREATE TABLE IF NOT EXISTS registration_codes (
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
    
    // Create user_classes table
    $conn->query("
        CREATE TABLE IF NOT EXISTS user_classes (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            class_name VARCHAR(50) NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");
    
    // Add columns to health_metrics table if they don't exist
    $result = $conn->query("SHOW COLUMNS FROM health_metrics LIKE 'body_fat'");
    if ($result->num_rows == 0) {
        $conn->query("ALTER TABLE health_metrics ADD COLUMN body_fat DECIMAL(5,2) DEFAULT NULL");
    }
    
    $result = $conn->query("SHOW COLUMNS FROM health_metrics LIKE 'muscle_mass'");
    if ($result->num_rows == 0) {
        $conn->query("ALTER TABLE health_metrics ADD COLUMN muscle_mass DECIMAL(5,2) DEFAULT NULL");
    }
    
    // Create diet_plans table
    $conn->query("
        CREATE TABLE IF NOT EXISTS diet_plans (
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
    
    // Create food_log table
    $conn->query("
        CREATE TABLE IF NOT EXISTS food_log (
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
    
    // Create workout_log table
    $conn->query("
        CREATE TABLE IF NOT EXISTS workout_log (
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
    
    // Insert some sample registration codes
    $conn->query("
        INSERT INTO registration_codes (code, discount_percentage, expiry_date)
        VALUES 
        ('FIT-1234-5678', 10, DATE_ADD(NOW(), INTERVAL 3 MONTH)),
        ('FIT-2345-6789', 15, DATE_ADD(NOW(), INTERVAL 3 MONTH)),
        ('FIT-3456-7890', 20, DATE_ADD(NOW(), INTERVAL 3 MONTH))
    ");
    
    // Commit transaction
    $conn->commit();
    
    echo "Database schema updated successfully!";
    
} catch (Exception $e) {
    // Rollback transaction on error
    $conn->rollback();
    
    echo "Error updating database schema: " . $e->getMessage();
}
?>