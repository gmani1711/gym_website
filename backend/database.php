<?php
/**
 * Database Connection Manager
 * 
 * This class provides a centralized way to manage database connections
 * using the mysqli extension, compatible with the existing config.php
 */
class DatabaseManager {
    private static $instance = null;
    private $conn;
    
    /**
     * Private constructor to prevent direct instantiation
     * Uses the connection parameters from config.php
     */
    private function __construct() {
        require_once 'config.php';
        global $conn;
        
        if ($conn instanceof mysqli && !$conn->connect_error) {
            $this->conn = $conn;
        } else {
            // If the global connection isn't available, create a new one
            global $host, $username, $password, $database;
            $this->conn = new mysqli($host, $username, $password, $database);
            
            if ($this->conn->connect_error) {
                die("Database connection failed: " . $this->conn->connect_error);
            }
            
            $this->conn->set_charset("utf8mb4");
        }
    }
    
    /**
     * Get the singleton instance of the DatabaseManager
     * 
     * @return DatabaseManager The singleton instance
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Get the database connection
     * 
     * @return mysqli The database connection
     */
    public function getConnection() {
        return $this->conn;
    }
    
    /**
     * Execute a prepared statement with parameters
     * 
     * @param string $query The SQL query with placeholders
     * @param string $types The types of the parameters
     * @param array $params The parameters to bind
     * @return mysqli_stmt|false The prepared statement or false on failure
     */
    public function executeQuery($query, $types = "", $params = []) {
        $stmt = $this->conn->prepare($query);
        
        if ($stmt === false) {
            return false;
        }
        
        if (!empty($types) && !empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        
        $stmt->execute();
        return $stmt;
    }
    
    /**
     * Begin a transaction
     * 
     * @return bool True on success, false on failure
     */
    public function beginTransaction() {
        return $this->conn->begin_transaction();
    }
    
    /**
     * Commit a transaction
     * 
     * @return bool True on success, false on failure
     */
    public function commitTransaction() {
        return $this->conn->commit();
    }
    
    /**
     * Rollback a transaction
     * 
     * @return bool True on success, false on failure
     */
    public function rollbackTransaction() {
        return $this->conn->rollback();
    }
    
    /**
     * Close the database connection
     */
    public function closeConnection() {
        if ($this->conn instanceof mysqli) {
            $this->conn->close();
        }
    }
    
    /**
     * Create necessary tables for the application if they don't exist
     */
    public function createTables() {
        // Contact messages table
        $query = "CREATE TABLE IF NOT EXISTS contact_messages (
            id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            email VARCHAR(100) NOT NULL,
            subject VARCHAR(100) NOT NULL,
            message TEXT NOT NULL,
            ip_address VARCHAR(45),
            submission_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        $this->conn->query($query);
        
        // Admin users table
        $query = "CREATE TABLE IF NOT EXISTS admin_users (
            id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            email VARCHAR(100) NOT NULL UNIQUE,
            last_login TIMESTAMP NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        $this->conn->query($query);
        
        // User activity log table
        $query = "CREATE TABLE IF NOT EXISTS user_activity_log (
            id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            user_id INT(11) UNSIGNED,
            activity_type ENUM('registration', 'login', 'logout', 'profile_update', 'workout_log', 'health_update', 'class_booking', 'other') NOT NULL,
            description TEXT,
            ip_address VARCHAR(45),
            user_agent TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
        )";
        $this->conn->query($query);
    }
    
    /**
     * Create a default admin user if none exists
     * 
     * @param string $username The admin username
     * @param string $password The admin password
     * @param string $email The admin email
     * @return bool True on success, false on failure
     */
    public function createDefaultAdmin($username = 'admin', $password = 'admin123', $email = 'admin@fitlifegym.com') {
        // Check if admin user already exists
        $query = "SELECT id FROM admin_users LIMIT 1";
        $result = $this->conn->query($query);
        
        if ($result->num_rows > 0) {
            return false; // Admin already exists
        }
        
        // Create default admin user
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $query = "INSERT INTO admin_users (username, password, email) VALUES (?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("sss", $username, $hashedPassword, $email);
        
        return $stmt->execute();
    }
}

// Initialize the database manager and create necessary tables
$dbManager = DatabaseManager::getInstance();
$dbManager->createTables();
$dbManager->createDefaultAdmin();
?>