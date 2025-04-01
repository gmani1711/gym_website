<?php
// Include database configuration
require_once 'backend/config.php';

// Check if user is logged in
if (!is_logged_in()) {
    $_SESSION['error'] = "Please login to access this page.";
    header("Location: index.html");
    exit();
}

// Get user ID from session
$userId = $_SESSION['user_id'];

// Include tracking functions
require_once 'backend/track.php';

// Get user profile
$userProfile = get_user_profile($userId);

// Get attendance data
$attendanceData = get_attendance_data($userId, 10);

// Get workout history
$workoutHistory = get_workout_history($userId, 10);

// Get health tracking data
$healthData = get_health_tracking_data($userId, 10);

// Get missed days
$missedDays = calculate_missed_days($userId);

// Get recommendations
$workoutRecommendations = get_workout_recommendations($userId);
$dietRecommendations = get_diet_recommendations($userId);
?>

&lt;!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - FitLife Gym</title>
    &lt;!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    &lt;!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    &lt;!-- Chart.js -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.css">
    &lt;!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/dashboard.css">
</head>
<body>
    &lt;!-- Navigation Bar -->
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
                            <li><a class="dropdown-item" href="#">Yoga</a></li>
                            <li><a class="dropdown-item" href="#">Zumba</a></li>
                            <li><a class="dropdown-item" href="#">Strength Training</a></li>
                            <li><a class="dropdown-item" href="#">Cardio</a></li>
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
                        <a class="nav-link" href="about.php" title="About Us">
                            <i class="fas fa-info-circle"></i>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="contact.php" title="Contact Us">
                            <i class="fas fa-phone"></i>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="feedback.php" title="Feedback">
                            <i class="fas fa-comment"></i>
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

    &lt;!-- Dashboard Header -->
    <header class="dashboard-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1>Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?></h1>
                    <p class="lead">Track your fitness journey and progress</p>
                </div>
                <div class="col-md-4 text-md-end">
                    <span class="badge bg-primary"><?php echo htmlspecialchars($userProfile['membership_plan']); ?> Member</span>
                    <p class="mb-0 mt-2">Member since: <?php echo date('F j, Y', strtotime($userProfile['registration_date'])); ?></p>
                </div>
            </div>
        </div>
    </header>

    &lt;!-- Dashboard Content -->
    <section class="dashboard-content py-5">
        <div class="container">
            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php 
                        echo $_SESSION['success'];
                        unset($_SESSION['success']);
                    ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php 
                        echo $_SESSION['error'];
                        unset($_SESSION['error']);
                    ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            &lt;!-- Quick Stats -->
            <div class="row mb-4">
                <div class="col-md-3 mb-3">
                    <div class="card stat-card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="card-subtitle mb-2 text-muted">Current BMI</h6>
                                    <h2 class="card-title mb-0"><?php echo number_format($userProfile['bmi'], 1); ?></h2>
                                    <p class="card-text"><?php echo get_bmi_category($userProfile['bmi']); ?></p>
                                </div>
                                <div class="stat-icon">
                                    <i class="fas fa-weight"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card stat-card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="card-subtitle mb-2 text-muted">Gym Visits</h6>
                                    <h2 class="card-title mb-0"><?php echo count($attendanceData); ?></h2>
                                    <p class="card-text">Last 30 days</p>
                                </div>
                                <div class="stat-icon">
                                    <i class="fas fa-calendar-check"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card stat-card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="card-subtitle mb-2 text-muted">Missed Days</h6>
                                    <h2 class="card-title mb-0"><?php echo count($missedDays); ?></h2>
                                    <p class="card-text">Last 30 days</p>
                                </div>
                                <div class="stat-icon">
                                    <i class="fas fa-calendar-times"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card stat-card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="card-subtitle mb-2 text-muted">Workouts</h6>
                                    <h2 class="card-title mb-0"><?php echo count($workoutHistory); ?></h2>
                                    <p class="card-text">Recorded sessions</p>
                                </div>
                                <div class="stat-icon">
                                    <i class="fas fa-heartbeat"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            &lt;!-- Main Dashboard Tabs -->
            <ul class="nav nav-tabs mb-4" id="dashboardTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="overview-tab" data-bs-toggle="tab" data-bs-target="#overview" type="button" role="tab" aria-controls="overview" aria-selected="true">Overview</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="progress-tab" data-bs-toggle="tab" data-bs-target="#progress" type="button" role="tab" aria-controls="progress" aria-selected="false">Progress Tracking</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="recommendations-tab" data-bs-toggle="tab" data-bs-target="#recommendations" type="button" role="tab" aria-controls="recommendations" aria-selected="false">Recommendations</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile" type="button" role="tab" aria-controls="profile" aria-selected="false">Profile</button>
                </li>
            </ul>

            <div class="tab-content" id="dashboardTabsContent">
                &lt;!-- Overview Tab -->
                <div class="tab-pane fade show active" id="overview" role="tabpanel" aria-labelledby="overview-tab">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Health Metrics</h5>
                                </div>
                                <div class="card-body">
                                    <canvas id="healthChart" height="300"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Fitness Goals</h5>
                                </div>
                                <div class="card-body">
                                    <h6>Primary Goal: <?php echo ucwords(str_replace('_', ' ', $userProfile['fitness_goals'])); ?></h6>
                                    <div class="progress mb-3">
                                        <div class="progress-bar bg-success" role="progressbar" style="width: 65%" aria-valuenow="65" aria-valuemin="0" aria-valuemax="100">65%</div>
                                    </div>
                                    
                                    <h6>Weekly Attendance</h6>
                                    <div class="progress mb-3">
                                        <div class="progress-bar bg-primary" role="progressbar" style="width: <?php echo (count($attendanceData) / 30) * 100; ?>%" aria-valuenow="<?php echo (count($attendanceData) / 30) * 100; ?>" aria-valuemin="0" aria-valuemax="100"><?php echo round((count($attendanceData) / 30) * 100); ?>%</div>
                                    </div>
                                    
                                    <h6>BMI Improvement</h6>
                                    <div class="progress mb-3">
                                        <div class="progress-bar bg-info" role="progressbar" style="width: 40%" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100">40%</div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Quick Actions</h5>
                                </div>
                                <div class="card-body">
                                    <div class="d-grid gap-2">
                                        <button class="btn btn-primary" type="button" data-bs-toggle="modal" data-bs-target="#logWorkoutModal">
                                            <i class="fas fa-plus-circle me-2"></i> Log Workout
                                        </button>
                                        <button class="btn btn-success" type="button" data-bs-toggle="modal" data-bs-target="#updateHealthModal">
                                            <i class="fas fa-heartbeat me-2"></i> Update Health Data
                                        </button>
                                        <button class="btn btn-info text-white" type="button" data-bs-toggle="modal" data-bs-target="#bookClassModal">
                                            <i class="fas fa-calendar-plus me-2"></i> Book a Class
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="card-title mb-0">Recent Workouts</h5>
                                    <a href="#" class="btn btn-sm btn-outline-primary">View All</a>
                                </div>
                                <div class="card-body">
                                    <?php if (count($workoutHistory) > 0): ?>
                                        <div class="table-responsive">
                                            <table class="table table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>Date</th>
                                                        <th>Type</th>
                                                        <th>Duration</th>
                                                        <th>Calories</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($workoutHistory as $workout): ?>
                                                        <tr>
                                                            <td><?php echo date('M d, Y', strtotime($workout['workout_date'])); ?></td>
                                                            <td><?php echo htmlspecialchars($workout['workout_type']); ?></td>
                                                            <td><?php echo htmlspecialchars($workout['duration']); ?> min</td>
                                                            <td><?php echo htmlspecialchars($workout['calories_burned']); ?></td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    <?php else: ?>
                                        <p class="text-center">No workout history available. Start logging your workouts!</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="card-title mb-0">Attendance History</h5>
                                    <a href="#" class="btn btn-sm btn-outline-primary">View All</a>
                                </div>
                                <div class="card-body">
                                    <?php if (count($attendanceData) > 0): ?>
                                        <div class="table-responsive">
                                            <table class="table table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>Date</th>
                                                        <th>Check In</th>
                                                        <th>Check Out</th>
                                                        <th>Duration</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($attendanceData as $attendance): ?>
                                                        <tr>
                                                            <td><?php echo date('M d, Y', strtotime($attendance['check_in'])); ?></td>
                                                            <td><?php echo date('h:i A', strtotime($attendance['check_in'])); ?></td>
                                                            <td>
                                                                <?php 
                                                                    if (!empty($attendance['check_out'])) {
                                                                        echo date('h:i A', strtotime($attendance['check_out']));
                                                                    } else {
                                                                        echo 'In Progress';
                                                                    }
                                                                ?>
                                                            </td>
                                                            <td>
                                                                <?php 
                                                                    if (!empty($attendance['check_out'])) {
                                                                        $duration = (strtotime($attendance['check_out']) - strtotime($attendance['check_in'])) / 60;
                                                                        echo round($duration) . ' min';
                                                                    } else {
                                                                        echo '-';
                                                                    }
                                                                ?>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    <?php else: ?>
                                        <p class="text-center">No attendance history available.</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                &lt;!-- Progress Tracking Tab -->
                <div class="tab-pane fade" id="progress" role="tabpanel" aria-labelledby="progress-tab">
                    <div class="row">
                        <div class="col-md-12 mb-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Weight & BMI Tracking</h5>
                                </div>
                                <div class="card-body">
                                    <canvas id="weightBmiChart" height="300"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Workout Frequency</h5>
                                </div>
                                <div class="card-body">
                                    <canvas id="workoutFrequencyChart" height="300"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Workout Types Distribution</h5>
                                </div>
                                <div class="card-body">
                                    <canvas id="workoutTypesChart" height="300"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Health Data History</h5>
                                </div>
                                <div class="card-body">
                                    <?php if (count($healthData) > 0): ?>
                                        <div class="table-responsive">
                                            <table class="table table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>Date</th>
                                                        <th>Weight (kg)</th>
                                                        <th>BMI</th>
                                                        <th>BMI Category</th>
                                                        <th>Notes</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($healthData as $data): ?>
                                                        <tr>
                                                            <td><?php echo date('M d, Y', strtotime($data['tracking_date'])); ?></td>
                                                            <td><?php echo htmlspecialchars($data['weight']); ?></td>
                                                            <td><?php echo htmlspecialchars($data['bmi']); ?></td>
                                                            <td><?php echo get_bmi_category($data['bmi']); ?></td>
                                                            <td><?php echo htmlspecialchars($data['notes'] ?? ''); ?></td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    <?php else: ?>
                                        <p class="text-center">No health tracking data available.</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                &lt;!-- Recommendations Tab -->
                <div class="tab-pane fade" id="recommendations" role="tabpanel" aria-labelledby="recommendations-tab">
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Workout Recommendations</h5>
                                </div>
                                <div class="card-body">
                                    <?php if (count($workoutRecommendations) > 0): ?>
                                        <div class="accordion" id="workoutRecommendationsAccordion">
                                            <?php foreach ($workoutRecommendations as $index => $recommendation): ?>
                                                <div class="accordion-item">
                                                    <h2 class="accordion-header" id="workout-heading-<?php echo $index; ?>">
                                                        <button class="accordion-button <?php echo $index > 0 ? 'collapsed' : ''; ?>" type="button" data-bs-toggle="collapse" data-bs-target="#workout-collapse-<?php echo $index; ?>" aria-expanded="<?php echo $index === 0 ? 'true' : 'false'; ?>" aria-controls="workout-collapse-<?php echo $index; ?>">
                                                            <?php echo htmlspecialchars($recommendation['title']); ?>
                                                            <span class="badge bg-secondary ms-2"><?php echo htmlspecialchars($recommendation['type']); ?></span>
                                                        </button>
                                                    </h2>
                                                    <div id="workout-collapse-<?php echo $index; ?>" class="accordion-collapse collapse <?php echo $index === 0 ? 'show' : ''; ?>" aria-labelledby="workout-heading-<?php echo $index; ?>" data-bs-parent="#workoutRecommendationsAccordion">
                                                        <div class="accordion-body">
                                                            <?php echo htmlspecialchars($recommendation['description']); ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php else: ?>
                                        <p class="text-center">No workout recommendations available.</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Diet Recommendations</h5>
                                </div>
                                <div class="card-body">
                                    <?php if (count($dietRecommendations) > 0): ?>
                                        <div class="accordion" id="dietRecommendationsAccordion">
                                            <?php foreach ($dietRecommendations as $index => $recommendation): ?>
                                                <div class="accordion-item">
                                                    <h2 class="accordion-header" id="diet-heading-<?php echo $index; ?>">
                                                        <button class="accordion-button <?php echo $index > 0 ? 'collapsed' : ''; ?>" type="button" data-bs-toggle="collapse" data-bs-target="#diet-collapse-<?php echo $index; ?>" aria-expanded="<?php echo $index === 0 ? 'true' : 'false'; ?>" aria-controls="diet-collapse-<?php echo $index; ?>">
                                                            <?php echo htmlspecialchars($recommendation['title']); ?>
                                                            <span class="badge bg-secondary ms-2"><?php echo htmlspecialchars($recommendation['type']); ?></span>
                                                        </button>
                                                    </h2>
                                                    <div id="diet-collapse-<?php echo $index; ?>" class="accordion-collapse collapse <?php echo $index === 0 ? 'show' : ''; ?>" aria-labelledby="diet-heading-<?php echo $index; ?>" data-bs-parent="#dietRecommendationsAccordion">
                                                        <div class="accordion-body">
                                                            <?php echo htmlspecialchars($recommendation['description']); ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php else: ?>
                                        <p class="text-center">No diet recommendations available.</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Personalized Fitness Plan</h5>
                                </div>
                                <div class="card-body">
                                    <div class="alert alert-info">
                                        <h5 class="alert-heading">Your Custom Plan</h5>
                                        <p>Based on your profile, goals, and health conditions, we've created a personalized fitness plan for you.</p>
                                    </div>
                                    
                                    <h6 class="mt-4">Weekly Schedule</h6>
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Day</th>
                                                    <th>Morning</th>
                                                    <th>Evening</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>Monday</td>
                                                    <td>Cardio (30 min)</td>
                                                    <td>Upper Body Strength</td>
                                                </tr>
                                                <tr>
                                                    <td>Tuesday</td>
                                                    <td>Yoga</td>
                                                    <td>Rest</td>
                                                </tr>
                                                <tr>
                                                    <td>Wednesday</td>
                                                    <td>HIIT (20 min)</td>
                                                    <td>Lower Body Strength</td>
                                                </tr>
                                                <tr>
                                                    <td>Thursday</td>
                                                    <td>Rest</td>
                                                    <td>Flexibility Training</td>
                                                </tr>
                                                <tr>
                                                    <td>Friday</td>
                                                    <td>Cardio (30 min)</td>
                                                    <td>Full Body Workout</td>
                                                </tr>
                                                <tr>
                                                    <td>Saturday</td>
                                                    <td>Group Class</td>
                                                    <td>Rest</td>
                                                </tr>
                                                <tr>
                                                    <td>Sunday</td>
                                                    <td>Rest</td>
                                                    <td>Rest</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    
                                    <div class="mt-4">
                                        <a href="#" class="btn btn-primary">Download Full Plan</a>
                                        <a href="#" class="btn btn-outline-secondary ms-2">Request Modifications</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                &lt;!-- Profile Tab -->
                <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                    <div class="row">
                        <div class="col-md-4 mb-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Personal Information</h5>
                                </div>
                                <div class="card-body">
                                    <div class="text-center mb-4">
                                        <img src="/placeholder.svg?height=150&width=150" class="rounded-circle img-thumbnail" alt="Profile Picture">
                                        <h4 class="mt-3"><?php echo htmlspecialchars($userProfile['first_name'] . ' ' . $userProfile['last_name']); ?></h4>
                                        <p class="text-muted"><?php echo htmlspecialchars($userProfile['membership_plan']); ?> Member</p>
                                    </div>
                                    
                                    <ul class="list-group list-group-flush">
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <span><i class="fas fa-envelope me-2"></i> Email</span>
                                            <span><?php echo htmlspecialchars($userProfile['email']); ?></span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <span><i class="fas fa-phone me-2"></i> Phone</span>
                                            <span><?php echo htmlspecialchars($userProfile['phone']); ?></span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <span><i class="fas fa-calendar me-2"></i> Age</span>
                                            <span><?php echo htmlspecialchars($userProfile['age']); ?> years</span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <span><i class="fas fa-ruler-vertical me-2"></i> Height</span>
                                            <span><?php echo htmlspecialchars($userProfile['height']); ?> cm</span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <span><i class="fas fa-weight me-2"></i> Weight</span>
                                            <span><?php echo htmlspecialchars($userProfile['weight']); ?> kg</span>
                                        </li>
                                    </ul>
                                    
                                    <div class="d-grid gap-2 mt-3">
                                        <button class="btn btn-primary" type="button" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                                            <i class="fas fa-edit me-2"></i> Edit Profile
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-8 mb-4">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Membership Details</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h6>Current Plan</h6>
                                            <p><?php echo htmlspecialchars($userProfile['membership_plan']); ?></p>
                                            
                                            <h6>Member Since</h6>
                                            <p><?php echo date('F j, Y', strtotime($userProfile['registration_date'])); ?></p>
                                            
                                            <h6>Renewal Date</h6>
                                            <p><?php echo date('F j, Y', strtotime($userProfile['registration_date'] . ' +1 month')); ?></p>
                                        </div>
                                        <div class="col-md-6">
                                            <h6>Payment Method</h6>
                                            <p>Credit Card (ending in 1234)</p>
                                            
                                            <h6>Billing Cycle</h6>
                                            <p>Monthly</p>
                                            
                                            <h6>Next Billing</h6>
                                            <p><?php echo date('F j, Y', strtotime($userProfile['registration_date'] . ' +1 month')); ?></p>
                                        </div>
                                    </div>
                                    
                                    <div class="mt-3">
                                        <a href="membership.html" class="btn btn-outline-primary">Upgrade Plan</a>
                                        <button class="btn btn-outline-secondary ms-2" type="button" data-bs-toggle="modal" data-bs-target="#updatePaymentModal">Update Payment Method</button>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Health Information</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h6>Fitness Goals</h6>
                                            <p><?php echo ucwords(str_replace('_', ' ', $userProfile['fitness_goals'])); ?></p>
                                            
                                            <h6>BMI</h6>
                                            <p><?php echo number_format($userProfile['bmi'], 1); ?> (<?php echo get_bmi_category($userProfile['bmi']); ?>)</p>
                                        </div>
                                        <div class="col-md-6">
                                            <h6>Health Conditions</h6>
                                            <?php 
                                                $healthConditions = explode(',', $userProfile['health_conditions']);
                                                if (in_array('none', $healthConditions)) {
                                                    echo '<p>None</p>';
                                                } else {
                                                    echo '<ul class="list-unstyled">';
                                                    foreach ($healthConditions as $condition) {
                                                        echo '<li><i class="fas fa-check-circle text-success me-2"></i>' . ucwords(str_replace('_', ' ', $condition)) . '</li>';
                                                    }
                                                    echo '</ul>';
                                                }
                                            ?>
                                        </div>
                                    </div>
                                    
                                    <div class="mt-3">
                                        <button class="btn btn-outline-primary" type="button" data-bs-toggle="modal" data-bs-target="#updateHealthInfoModal">Update Health Information</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    &lt;!-- Modals -->
    &lt;!-- Log Workout Modal -->
    <div class="modal fade" id="logWorkoutModal" tabindex="-1" aria-labelledby="logWorkoutModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="logWorkoutModalLabel">Log Workout</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="backend/log_workout.php" method="post">
                        <div class="mb-3">
                            <label for="workoutDate" class="form-label">Date</label>
                            <input type="date" class="form-control" id="workoutDate" name="workoutDate" value="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="workoutType" class="form-label">Workout Type</label>
                            <select class="form-select" id="workoutType" name="workoutType" required>
                                <option value="">Select workout type</option>
                                <option value="Cardio">Cardio</option>
                                <option value="Strength Training">Strength Training</option>
                                <option value="Yoga">Yoga</option>
                                <option value="Zumba">Zumba</option>
                                <option value="HIIT">HIIT</option>
                                <option value="Pilates">Pilates</option>
                                <option value="CrossFit">CrossFit</option>
                                <option value="Swimming">Swimming</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="duration" class="form-label">Duration (minutes)</label>
                            <input type="number" class="form-control" id="duration" name="duration" min="1" max="300" required>
                        </div>
                        <div class="mb-3">
                            <label for="caloriesBurned" class="form-label">Calories Burned (optional)</label>
                            <input type="number" class="form-control" id="caloriesBurned" name="caloriesBurned" min="1" max="2000">
                        </div>
                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes (optional)</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Log Workout</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    &lt;!-- Update Health Data Modal -->
    <div class="modal fade" id="updateHealthModal" tabindex="-1" aria-labelledby="updateHealthModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="updateHealthModalLabel">Update Health Data</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="backend/update_health.php" method="post">
                        <div class="mb-3">
                            <label for="trackingDate" class="form-label">Date</label>
                            <input type="date" class="form-control" id="trackingDate" name="trackingDate" value="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="weight" class="form-label">Weight (kg)</label>
                            <input type="number" class="form-control" id="weight" name="weight" min="30" max="200" value="<?php echo $userProfile['weight']; ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="healthNotes" class="form-label">Notes (optional)</label>
                            <textarea class="form-control" id="healthNotes" name="healthNotes" rows="3"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Update Health Data</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    &lt;!-- Book Class Modal -->
    <div class="modal fade" id="bookClassModal" tabindex="-1" aria-labelledby="bookClassModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="bookClassModalLabel">Book a Class</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="backend/book_class.php" method="post">
                        <div class="mb-3">
                            <label for="classType" class="form-label">Class Type</label>
                            <select class="form-select" id="classType" name="classType" required>
                                <option value="">Select class type</option>
                                <option value="Yoga">Yoga</option>
                                <option value="Zumba">Zumba</option>
                                <option value="HIIT">HIIT</option>
                                <option value="Pilates">Pilates</option>
                                <option value="Spinning">Spinning</option>
                                <option value="Boxing">Boxing</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="classDate" class="form-label">Date</label>
                            <input type="date" class="form-control" id="classDate" name="classDate" min="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="classTime" class="form-label">Time Slot</label>
                            <select class="form-select" id="classTime" name="classTime" required>
                                <option value="">Select time slot</option>
                                <option value="06:00">06:00 AM - 07:00 AM</option>
                                <option value="07:00">07:00 AM - 08:00 AM</option>
                                <option value="08:00">08:00 AM - 09:00 AM</option>
                                <option value="09:00">09:00 AM - 10:00 AM</option>
                                <option value="17:00">05:00 PM - 06:00 PM</option>
                                <option value="18:00">06:00 PM - 07:00 PM</option>
                                <option value="19:00">07:00 PM - 08:00 PM</option>
                                <option value="20:00">08:00 PM - 09:00 PM</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Book Class</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    &lt;!-- Edit Profile Modal -->
    <div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editProfileModalLabel">Edit Profile</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="backend/update_profile.php" method="post">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="firstName" class="form-label">First Name</label>
                                <input type="text" class="form-control" id="firstName" name="firstName" value="<?php echo $userProfile['first_name']; ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="lastName" class="form-label">Last Name</label>
                                <input type="text" class="form-control" id="lastName" name="lastName" value="<?php echo $userProfile['last_name']; ?>" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?php echo $userProfile['email']; ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="phone" class="form-label">Phone</label>
                            <div class="input-group">
                                <span class="input-group-text">+91</span>
                                <input type="tel" class="form-control" id="phone" name="phone" value="<?php echo substr($userProfile['phone'], 3); ?>" pattern="[0-9]{10}" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="height" class="form-label">Height (cm)</label>
                                <input type="number" class="form-control" id="height" name="height" value="<?php echo $userProfile['height']; ?>" min="100" max="250" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="weight" class="form-label">Weight (kg)</label>
                                <input type="number" class="form-control" id="weight" name="weight" value="<?php echo $userProfile['weight']; ?>" min="30" max="200" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="age" class="form-label">Age</label>
                                <input type="number" class="form-control" id="age" name="age" value="<?php echo $userProfile['age']; ?>" min="16" max="80" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="currentPassword" class="form-label">Current Password (required to save changes)</label>
                            <input type="password" class="form-control" id="currentPassword" name="currentPassword" required>
                        </div>
                        <div class="mb-3">
                            <label for="newPassword" class="form-label">New Password (leave blank to keep current)</label>
                            <input type="password" class="form-control" id="newPassword" name="newPassword">
                        </div>
                        <div class="mb-3">
                            <label for="confirmPassword" class="form-label">Confirm New Password</label>
                            <input type="password" class="form-control" id="confirmPassword" name="confirmPassword">
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Save Changes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    &lt;!-- Footer -->
    <footer class="footer py-5">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <h5>FitLife Gym</h5>
                    <p>Your ultimate fitness destination for a healthier, stronger you.</p>
                    <div class="social-icons">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <h5>Quick Links</h5>
                    <ul class="list-unstyled">
                        <li><a href="index.html">Home</a></li>
                        <li><a href="membership.html">Membership</a></li>
                        <li><a href="about.php">About Us</a></li>
                        <li><a href="contact.php">Contact Us</a></li>
                        <li><a href="feedback.php">Feedback</a></li>
                    </ul>
                </div>
                <div class="col-md-4 mb-4">
                    <h5>Contact Us</h5>
                    <address>
                        <p><i class="fas fa-map-marker-alt"></i> 123 Fitness Street, Mumbai, India</p>
                        <p><i class="fas fa-phone"></i> +91 9876543210</p>
                        <p><i class="fas fa-envelope"></i> info@fitlifegym.com</p>
                    </address>
                </div>
            </div>
            <hr>
            <div class="text-center">
                <p>&copy; 2023 FitLife Gym. All rights reserved.</p>
            </div>
        </div>
    </footer>

    &lt;!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    &lt;!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.js"></script>
    &lt;!-- Custom JS -->
    <script src="assets/js/main.js"></script>
    <script src="assets/js/dashboard.js"></script>
</body>
</html>