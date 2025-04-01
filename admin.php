<?php
// Include database configuration
require_once 'backend/config.php';
require_once 'backend/database.php';

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in and is an admin
$isAdmin = false;
if (isset($_SESSION['user_id']) && isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1) {
    $isAdmin = true;
} else {
    // If not logged in as admin, redirect to login page
    header("Location: index.html");
    exit();
}

// Get database connection
$dbManager = DatabaseManager::getInstance();
$conn = $dbManager->getConnection();

// Get total user count
$query = "SELECT COUNT(*) as total FROM users";
$result = $conn->query($query);
$row = $result->fetch_assoc();
$totalUsers = $row['total'];

// Get active memberships count
$query = "SELECT COUNT(*) as total FROM memberships WHERE status = 'active'";
$result = $conn->query($query);
$row = $result->fetch_assoc();
$activeMembers = $row['total'];

// Get today's registrations
$query = "SELECT COUNT(*) as total FROM users WHERE DATE(created_at) = CURDATE()";
$result = $conn->query($query);
$row = $result->fetch_assoc();
$todayRegistrations = $row['total'];

// Get today's logins
$query = "SELECT COUNT(*) as total FROM user_activity_log WHERE activity_type = 'login' AND DATE(created_at) = CURDATE()";
$result = $conn->query($query);
$row = $result->fetch_assoc();
$todayLogins = $row['total'];

// Get membership plan distribution
$query = "SELECT plan_name, COUNT(*) as count FROM memberships GROUP BY plan_name";
$planDistribution = $conn->query($query);

// Get class popularity
$query = "SELECT class_name, COUNT(*) as count FROM user_classes GROUP BY class_name ORDER BY count DESC";
$classPopularity = $conn->query($query);

// Get recent user registrations
$query = "SELECT u.id, u.first_name, u.last_name, u.email, u.created_at, m.plan_name 
          FROM users u 
          LEFT JOIN memberships m ON u.id = m.user_id 
          ORDER BY u.created_at DESC 
          LIMIT 10";
$recentUsers = $conn->query($query);

// Get recent activity logs
$query = "SELECT l.id, l.user_id, u.first_name, u.last_name, l.activity_type, l.description, l.ip_address, l.created_at 
          FROM user_activity_log l 
          JOIN users u ON l.user_id = u.id 
          ORDER BY l.created_at DESC 
          LIMIT 20";
$recentActivity = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - FitLife Gym</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/admin.css">
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container">
            <a class="navbar-brand" href="index.html">
                <span class="fw-bold">FitLife</span> Gym
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.html" title="Home">
                            <i class="fas fa-home"></i>
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="classesDropdown" role="button" data-bs-toggle="dropdown" title="Classes">
                            <i class="fas fa-dumbbell"></i>
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="classesDropdown">
                            <li><a class="dropdown-item" href="classes/yoga.html">Yoga</a></li>
                            <li><a class="dropdown-item" href="classes/zumba.html">Zumba</a></li>
                            <li><a class="dropdown-item" href="classes/strength-training.html">Strength Training</a></li>
                            <li><a class="dropdown-item" href="classes/cardio.html">Cardio</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="membership.html" title="Membership">
                            <i class="fas fa-id-card"></i>
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="timeSlotDropdown" role="button" data-bs-toggle="dropdown" title="Time Slots">
                            <i class="far fa-clock"></i>
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="timeSlotDropdown">
                            <li><a class="dropdown-item" href="#">Morning (6AM - 10AM)</a></li>
                            <li><a class="dropdown-item" href="#">Afternoon (12PM - 4PM)</a></li>
                            <li><a class="dropdown-item" href="#">Evening (5PM - 9PM)</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="about.html" title="About Us">
                            <i class="fas fa-info-circle"></i>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="contact.html" title="Contact Us">
                            <i class="fas fa-phone"></i>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="feedback.html" title="Feedback">
                            <i class="fas fa-comment"></i>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="admin.php" title="Admin">
                            <i class="fas fa-user-shield"></i>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="backend/logout.php" title="Logout">
                            <i class="fas fa-sign-out-alt"></i>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Admin Dashboard -->
    <div class="admin-dashboard">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="sidebar-header">
                <h3>Admin Panel</h3>
            </div>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link active" href="#dashboard" data-bs-toggle="tab">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#users" data-bs-toggle="tab">
                        <i class="fas fa-users"></i> Users
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#memberships" data-bs-toggle="tab">
                        <i class="fas fa-id-card"></i> Memberships
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#classes" data-bs-toggle="tab">
                        <i class="fas fa-dumbbell"></i> Classes
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#activity" data-bs-toggle="tab">
                        <i class="fas fa-history"></i> Activity Logs
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#registration-codes" data-bs-toggle="tab">
                        <i class="fas fa-key"></i> Registration Codes
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#settings" data-bs-toggle="tab">
                        <i class="fas fa-cog"></i> Settings
                    </a>
                </li>
            </ul>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <div class="tab-content">
                <!-- Dashboard Tab -->
                <div class="tab-pane fade show active" id="dashboard">
                    <h2>Dashboard</h2>
                    <div class="row">
                        <div class="col-md-3 mb-4">
                            <div class="card stat-card">
                                <div class="card-body">
                                    <div class="stat-icon">
                                        <i class="fas fa-users"></i>
                                    </div>
                                    <div class="stat-details">
                                        <h3><?php echo $totalUsers; ?></h3>
                                        <p>Total Users</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-4">
                            <div class="card stat-card">
                                <div class="card-body">
                                    <div class="stat-icon">
                                        <i class="fas fa-id-card"></i>
                                    </div>
                                    <div class="stat-details">
                                        <h3><?php echo $activeMembers; ?></h3>
                                        <p>Active Memberships</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-4">
                            <div class="card stat-card">
                                <div class="card-body">
                                    <div class="stat-icon">
                                        <i class="fas fa-user-plus"></i>
                                    </div>
                                    <div class="stat-details">
                                        <h3><?php echo $todayRegistrations; ?></h3>
                                        <p>Today's Registrations</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-4">
                            <div class="card stat-card">
                                <div class="card-body">
                                    <div class="stat-icon">
                                        <i class="fas fa-sign-in-alt"></i>
                                    </div>
                                    <div class="stat-details">
                                        <h3><?php echo $todayLogins; ?></h3>
                                        <p>Today's Logins</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Membership Distribution</h5>
                                </div>
                                <div class="card-body">
                                    <canvas id="membershipChart"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Class Popularity</h5>
                                </div>
                                <div class="card-body">
                                    <canvas id="classChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12 mb-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Recent Registrations</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Name</th>
                                                    <th>Email</th>
                                                    <th>Plan</th>
                                                    <th>Registration Date</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php while ($user = $recentUsers->fetch_assoc()): ?>
                                                <tr>
                                                    <td><?php echo $user['id']; ?></td>
                                                    <td><?php echo $user['first_name'] . ' ' . $user['last_name']; ?></td>
                                                    <td><?php echo $user['email']; ?></td>
                                                    <td><?php echo $user['plan_name']; ?></td>
                                                    <td><?php echo date('Y-m-d H:i', strtotime($user['created_at'])); ?></td>
                                                </tr>
                                                <?php endwhile; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Users Tab -->
                <div class="tab-pane fade" id="users">
                    <h2>User Management</h2>
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="usersTable" class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Phone</th>
                                            <th>Registration Date</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Data will be loaded via AJAX -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Memberships Tab -->
                <div class="tab-pane fade" id="memberships">
                    <h2>Membership Management</h2>
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="membershipsTable" class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>User</th>
                                            <th>Plan</th>
                                            <th>Price</th>
                                            <th>Start Date</th>
                                            <th>End Date</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Data will be loaded via AJAX -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Classes Tab -->
                <div class="tab-pane fade" id="classes">
                    <h2>Class Management</h2>
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="classesTable" class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Class Name</th>
                                            <th>Enrolled Users</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Data will be loaded via AJAX -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Activity Logs Tab -->
                <div class="tab-pane fade" id="activity">
                    <h2>Activity Logs</h2>
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="activityTable" class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>User</th>
                                            <th>Activity Type</th>
                                            <th>Description</th>
                                            <th>IP Address</th>
                                            <th>Timestamp</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($activity = $recentActivity->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo $activity['id']; ?></td>
                                            <td><?php echo $activity['first_name'] . ' ' . $activity['last_name']; ?></td>
                                            <td><?php echo ucfirst($activity['activity_type']); ?></td>
                                            <td><?php echo $activity['description']; ?></td>
                                            <td><?php echo $activity['ip_address']; ?></td>
                                            <td><?php echo date('Y-m-d H:i:s', strtotime($activity['created_at'])); ?></td>
                                        </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Registration Codes Tab -->
                <div class="tab-pane fade" id="registration-codes">
                    <h2>Registration Codes</h2>
                    <div class="mb-3">
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCodeModal">
                            <i class="fas fa-plus"></i> Add New Code
                        </button>
                    </div>
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="codesTable" class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Code</th>
                                            <th>Discount</th>
                                            <th>Status</th>
                                            <th>Used By</th>
                                            <th>Used At</th>
                                            <th>Expiry Date</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Data will be loaded via AJAX -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Settings Tab -->
                <div class="tab-pane fade" id="settings">
                    <h2>System Settings</h2>
                    <div class="card">
                        <div class="card-body">
                            <form id="settingsForm">
                                <div class="mb-3">
                                    <label for="siteName" class="form-label">Site Name</label>
                                    <input type="text" class="form-control" id="siteName" name="siteName" value="FitLife Gym">
                                </div>
                                <div class="mb-3">
                                    <label for="contactEmail" class="form-label">Contact Email</label>
                                    <input type="email" class="form-control" id="contactEmail" name="contactEmail" value="info@fitlifegym.com">
                                </div>
                                <div class="mb-3">
                                    <label for="contactPhone" class="form-label">Contact Phone</label>
                                    <input type="text" class="form-control" id="contactPhone" name="contactPhone" value="+91 9876543210">
                                </div>
                                <div class="mb-3">
                                    <label for="address" class="form-label">Address</label>
                                    <textarea class="form-control" id="address" name="address" rows="3">123 Fitness Street, Mumbai, India</textarea>
                                </div>
                                <button type="submit" class="btn btn-primary">Save Settings</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Registration Code Modal -->
    <div class="modal fade" id="addCodeModal" tabindex="-1" aria-labelledby="addCodeModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addCodeModalLabel">Add Registration Code</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addCodeForm">
                        <div class="mb-3">
                            <label for="code" class="form-label">Code</label>
                            <div class="input-group">
                                <span class="input-group-text">FIT-</span>
                                <input type="text" class="form-control" id="code" name="code" placeholder="XXXX-XXXX" required>
                            </div>
                            <div class="form-text">Format: FIT-XXXX-XXXX</div>
                        </div>
                        <div class="mb-3">
                            <label for="discountType" class="form-label">Discount Type</label>
                            <select class="form-select" id="discountType" name="discountType" required>
                                <option value="percentage">Percentage</option>
                                <option value="amount">Fixed Amount</option>
                            </select>
                        </div>
                        <div class="mb-3" id="percentageDiscount">
                            <label for="discountPercentage" class="form-label">Discount Percentage</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="discountPercentage" name="discountPercentage" min="1" max="100" value="10">
                                <span class="input-group-text">%</span>
                            </div>
                        </div>
                        <div class="mb-3 d-none" id="amountDiscount">
                            <label for="discountAmount" class="form-label">Discount Amount</label>
                            <div class="input-group">
                                <span class="input-group-text">₹</span>
                                <input type="number" class="form-control" id="discountAmount" name="discountAmount" min="1" value="500">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="expiryDate" class="form-label">Expiry Date</label>
                            <input type="date" class="form-control" id="expiryDate" name="expiryDate" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Create Code</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <!-- Custom JS -->
    <script src="assets/js/admin.js"></script>
    
    <script>
        // Initialize charts
        document.addEventListener('DOMContentLoaded', function() {
            // Membership Distribution Chart
            const membershipCtx = document.getElementById('membershipChart').getContext('2d');
            const membershipChart = new Chart(membershipCtx, {
                type: 'pie',
                data: {
                    labels: [
                        <?php 
                        while ($plan = $planDistribution->fetch_assoc()) {
                            echo "'" . $plan['plan_name'] . "', ";
                        }
                        ?>
                    ],
                    datasets: [{
                        data: [
                            <?php 
                            $planDistribution->data_seek(0);
                            while ($plan = $planDistribution->fetch_assoc()) {
                                echo $plan['count'] . ", ";
                            }
                            ?>
                        ],
                        backgroundColor: [
                            'rgba(54, 162, 235, 0.7)',
                            'rgba(75, 192, 192, 0.7)',
                            'rgba(153, 102, 255, 0.7)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
            
            // Class Popularity Chart
            const classCtx = document.getElementById('classChart').getContext('2d');
            const classChart = new Chart(classCtx, {
                type: 'bar',
                data: {
                    labels: [
                        <?php 
                        while ($class = $classPopularity->fetch_assoc()) {
                            echo "'" . ucfirst($class['class_name']) . "', ";
                        }
                        ?>
                    ],
                    datasets: [{
                        label: 'Number of Users',
                        data: [
                            <?php 
                            $classPopularity->data_seek(0);
                            while ($class = $classPopularity->fetch_assoc()) {
                                echo $class['count'] . ", ";
                            }
                            ?>
                        ],
                        backgroundColor: 'rgba(75, 192, 192, 0.7)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
            
            // Initialize DataTables
            $('#activityTable').DataTable({
                order: [[5, 'desc']]
            });
            
            // Toggle discount type
            $('#discountType').change(function() {
                if ($(this).val() === 'percentage') {
                    $('#percentageDiscount').removeClass('d-none');
                    $('#amountDiscount').addClass('d-none');
                } else {
                    $('#percentageDiscount').addClass('d-none');
                    $('#amountDiscount').removeClass('d-none');
                }
            });
            
            // Set default expiry date to 3 months from now
            const today = new Date();
            const threeMonthsLater = new Date(today.setMonth(today.getMonth() + 3));
            document.getElementById('expiryDate').valueAsDate = threeMonthsLater;
        });
    </script>
</body>
</html>