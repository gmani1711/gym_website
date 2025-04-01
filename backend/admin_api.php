<?php
// Include database configuration
require_once 'config.php';
require_once 'database.php';

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header('Content-Type: application/json');
    echo json_encode(['error' => '  != 1) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Unauthorized access']);
    exit();
}

// Get database connection
$dbManager = DatabaseManager::getInstance();
$conn = $dbManager->getConnection();

// Get action from request
$action = isset($_GET['action']) ? $_GET['action'] : '';

// Process API requests
switch ($action) {
    case 'getUsers':
        // Get all users
        $query = "SELECT * FROM users ORDER BY id DESC";
        $result = $conn->query($query);
        
        $users = [];
        while ($row = $result->fetch_assoc()) {
            // Remove password for security
            unset($row['password']);
            $users[] = $row;
        }
        
        header('Content-Type: application/json');
        echo json_encode($users);
        break;
        
    case 'getMemberships':
        // Get all memberships with user details
        $query = "SELECT m.*, u.first_name, u.last_name 
                  FROM memberships m 
                  JOIN users u ON m.user_id = u.id 
                  ORDER BY m.id DESC";
        $result = $conn->query($query);
        
        $memberships = [];
        while ($row = $result->fetch_assoc()) {
            $memberships[] = $row;
        }
        
        header('Content-Type: application/json');
        echo json_encode($memberships);
        break;
        
    case 'getClasses':
        // Get class popularity
        $query = "SELECT class_name, COUNT(user_id) as user_count 
                  FROM user_classes 
                  GROUP BY class_name 
                  ORDER BY user_count DESC";
        $result = $conn->query($query);
        
        $classes = [];
        while ($row = $result->fetch_assoc()) {
            $classes[] = $row;
        }
        
        header('Content-Type: application/json');
        echo json_encode($classes);
        break;
        
    case 'getRegistrationCodes':
        // Get all registration codes
        $query = "SELECT rc.*, u.first_name, u.last_name 
                  FROM registration_codes rc 
                  LEFT JOIN users u ON rc.used_by = u.id 
                  ORDER BY rc.id DESC";
        $result = $conn->query($query);
        
        $codes = [];
        while ($row = $result->fetch_assoc()) {
            $codes[] = $row;
        }
        
        header('Content-Type: application/json');
        echo json_encode($codes);
        break;
        
    case 'addRegistrationCode':
        // Add new registration code
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $code = isset($_POST['code']) ? $conn->real_escape_string($_POST['code']) : '';
            $discountPercentage = isset($_POST['discount_percentage']) ? intval($_POST['discount_percentage']) : 0;
            $discountAmount = isset($_POST['discount_amount']) ? floatval($_POST['discount_amount']) : 0;
            $expiryDate = isset($_POST['expiry_date']) ? $conn->real_escape_string($_POST['expiry_date']) : '';
            
            if (empty($code) || empty($expiryDate)) {
                header('Content-Type: application/json');
                echo json_encode(['error' => 'Code and expiry date are required']);
                exit();
            }
            
            // Check if code already exists
            $query = "SELECT * FROM registration_codes WHERE code = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("s", $code);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                header('Content-Type: application/json');
                echo json_encode(['error' => 'Code already exists']);
                exit();
            }
            
            // Insert new code
            $query = "INSERT INTO registration_codes (code, discount_percentage, discount_amount, expiry_date) 
                      VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("sisd", $code, $discountPercentage, $discountAmount, $expiryDate);
            
            if ($stmt->execute()) {
                header('Content-Type: application/json');
                echo json_encode(['success' => true, 'id' => $conn->insert_id]);
            } else {
                header('Content-Type: application/json');
                echo json_encode(['error' => 'Failed to add registration code']);
            }
        } else {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Invalid request method']);
        }
        break;
        
    case 'deleteUser':
        // Delete user
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = isset($_POST['id']) ? intval($_POST['id']) : 0;
            
            if ($userId <= 0) {
                header('Content-Type: application/json');
                echo json_encode(['error' => 'Invalid user ID']);
                exit();
            }
            
            // Check if user is an admin
            $query = "SELECT is_admin FROM users WHERE id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $user = $result->fetch_assoc();
                
                if ($user['is_admin'] == 1) {
                    header('Content-Type: application/json');
                    echo json_encode(['error' => 'Cannot delete admin user']);
                    exit();
                }
            }
            
            // Delete user
            $query = "DELETE FROM users WHERE id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("i", $userId);
            
            if ($stmt->execute()) {
                header('Content-Type: application/json');
                echo json_encode(['success' => true]);
            } else {
                header('Content-Type: application/json');
                echo json_encode(['error' => 'Failed to delete user']);
            }
        } else {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Invalid request method']);
        }
        break;
        
    case 'updateSettings':
        // Update system settings
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $siteName = isset($_POST['site_name']) ? $conn->real_escape_string($_POST['site_name']) : '';
            $contactEmail = isset($_POST['contact_email']) ? $conn->real_escape_string($_POST['contact_email']) : '';
            $contactPhone = isset($_POST['contact_phone']) ? $conn->real_escape_string($_POST['contact_phone']) : '';
            $address = isset($_POST['address']) ? $conn->real_escape_string($_POST['address']) : '';
            
            // Check if settings table exists
            $result = $conn->query("SHOW TABLES LIKE 'settings'");
            
            if ($result->num_rows == 0) {
                // Create settings table
                $conn->query("
                    CREATE TABLE settings (
                        id INT AUTO_INCREMENT PRIMARY KEY,
                        setting_key VARCHAR(50) NOT NULL UNIQUE,
                        setting_value TEXT NOT NULL,
                        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
                ");
            }
            
            // Update or insert settings
            $settings = [
                'site_name' => $siteName,
                'contact_email' => $contactEmail,
                'contact_phone' => $contactPhone,
                'address' => $address
            ];
            
            foreach ($settings as $key => $value) {
                // Check if setting exists
                $query = "SELECT * FROM settings WHERE setting_key = ?";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("s", $key);
                $stmt->execute();
                $result = $stmt->get_result();
                
                if ($result->num_rows > 0) {
                    // Update setting
                    $query = "UPDATE settings SET setting_value = ? WHERE setting_key = ?";
                    $stmt = $conn->prepare($query);
                    $stmt->bind_param("ss", $value, $key);
                    $stmt->execute();
                } else {
                    // Insert setting
                    $query = "INSERT INTO settings (setting_key, setting_value) VALUES (?, ?)";
                    $stmt = $conn->prepare($query);
                    $stmt->bind_param("ss", $key, $value);
                    $stmt->execute();
                }
            }
            
            header('Content-Type: application/json');
            echo json_encode(['success' => true]);
        } else {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Invalid request method']);
        }
        break;
        
    default:
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Invalid action']);
        break;
}
?>