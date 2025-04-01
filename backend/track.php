<?php
// Include database configuration
require_once 'config.php';

// Check if user is logged in
if (!is_logged_in()) {
    $_SESSION['error'] = "Please login to access this page.";
    redirect("../index.html");
    exit();
}

$userId = $_SESSION['user_id'];

// Function to get user's attendance data
function get_attendance_data($userId, $limit = 30) {
    global $conn;
    
    $sql = "SELECT check_in, check_out FROM attendance 
            WHERE user_id = ? 
            ORDER BY check_in DESC 
            LIMIT ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $userId, $limit);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $attendanceData = [];
    while ($row = $result->fetch_assoc()) {
        $attendanceData[] = $row;
    }
    
    return $attendanceData;
}

// Function to get user's workout history
function get_workout_history($userId, $limit = 30) {
    global $conn;
    
    $sql = "SELECT workout_date, workout_type, duration, calories_burned, notes 
            FROM workouts 
            WHERE user_id = ? 
            ORDER BY workout_date DESC 
            LIMIT ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $userId, $limit);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $workoutHistory = [];
    while ($row = $result->fetch_assoc()) {
        $workoutHistory[] = $row;
    }
    
    return $workoutHistory;
}

// Function to get user's health tracking data
function get_health_tracking_data($userId, $limit = 30) {
    global $conn;
    
    $sql = "SELECT tracking_date, weight, bmi, notes 
            FROM health_tracking 
            WHERE user_id = ? 
            ORDER BY tracking_date DESC 
            LIMIT ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $userId, $limit);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $healthData = [];
    while ($row = $result->fetch_assoc()) {
        $healthData[] = $row;
    }
    
    return $healthData;
}

// Function to get user's profile data
function get_user_profile($userId) {
    global $conn;
    
    $sql = "SELECT first_name, last_name, email, phone, height, weight, age, bmi, 
            fitness_goals, health_conditions, membership_plan, registration_date 
            FROM users 
            WHERE id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        return $result->fetch_assoc();
    }
    
    return null;
}

// Function to calculate missed days
function calculate_missed_days($userId, $days = 30) {
    global $conn;
    
    // Get the dates the user attended in the last X days
    $sql = "SELECT DISTINCT DATE(check_in) as attendance_date 
            FROM attendance 
            WHERE user_id = ? 
            AND check_in >= DATE_SUB(CURDATE(), INTERVAL ? DAY)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $userId, $days);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $attendedDates = [];
    while ($row = $result->fetch_assoc()) {
        $attendedDates[] = $row['attendance_date'];
    }
    
    // Calculate all dates in the period
    $allDates = [];
    for ($i = 0; $i &lt; $days; $i++) {
        $date = date('Y-m-d', strtotime("-$i days"));
        $allDates[] = $date;
    }
    
    // Calculate missed dates (excluding today)
    $missedDates = array_diff($allDates, $attendedDates);
    array_shift($missedDates); // Remove today from missed dates
    
    return $missedDates;
}

// Function to get workout recommendations based on user profile
function get_workout_recommendations($userId) {
    global $conn;
    
    // Get user profile
    $profile = get_user_profile($userId);
    
    if (!$profile) {
        return [];
    }
    
    $recommendations = [];
    
    // Based on BMI
    $bmi = $profile['bmi'];
    $bmiCategory = get_bmi_category($bmi);
    
    if ($bmiCategory == "Underweight") {
        $recommendations[] = [
            'type' => 'BMI',
            'title' => 'Weight Gain Program',
            'description' => 'Focus on strength training with progressive overload to build muscle mass. Include compound exercises like squats, deadlifts, and bench press.'
        ];
    } elseif ($bmiCategory == "Overweight" || $bmiCategory == "Obese") {
        $recommendations[] = [
            'type' => 'BMI',
            'title' => 'Weight Loss Program',
            'description' => 'Combine cardio exercises (30-45 minutes, 5 days a week) with strength training. Focus on high-intensity interval training (HIIT) for efficient calorie burning.'
        ];
    } else {
        $recommendations[] = [
            'type' => 'BMI',
            'title' => 'Maintenance Program',
            'description' => 'Balance between strength training and cardio. Focus on improving overall fitness and muscle tone with a mix of resistance training and moderate cardio.'
        ];
    }
    
    // Based on fitness goals
    $fitnessGoals = $profile['fitness_goals'];
    
    if ($fitnessGoals == "weight_loss") {
        $recommendations[] = [
            'type' => 'Goal',
            'title' => 'Weight Loss Routine',
            'description' => 'Cardio-focused routine with calorie deficit diet. Include 45-60 minutes of cardio 5 days a week, plus 2-3 days of full-body strength training.'
        ];
    } elseif ($fitnessGoals == "muscle_gain") {
        $recommendations[] = [
            'type' => 'Goal',
            'title' => 'Muscle Building Routine',
            'description' => 'Progressive overload strength training with calorie surplus diet. Focus on compound movements and split routines targeting different muscle groups.'
        ];
    } elseif ($fitnessGoals == "endurance") {
        $recommendations[] = [
            'type' => 'Goal',
            'title' => 'Endurance Building Routine',
            'description' => 'Long-duration cardio sessions with gradual intensity increases. Include swimming, cycling, or running with progressive distance goals.'
        ];
    } elseif ($fitnessGoals == "flexibility") {
        $recommendations[] = [
            'type' => 'Goal',
            'title' => 'Flexibility Improvement Routine',
            'description' => 'Regular yoga or Pilates sessions with dedicated stretching routines. Focus on dynamic stretching before workouts and static stretching after.'
        ];
    } else {
        $recommendations[] = [
            'type' => 'Goal',
            'title' => 'General Fitness Routine',
            'description' => 'Balanced approach with cardio, strength, and flexibility training. Include 2-3 days of strength training, 2-3 days of cardio, and 1-2 days of flexibility work.'
        ];
    }
    
    // Based on health conditions
    $healthConditions = explode(',', $profile['health_conditions']);
    
    if (in_array('diabetes', $healthConditions)) {
        $recommendations[] = [
            'type' => 'Health',
            'title' => 'Diabetes-Friendly Workout',
            'description' => 'Regular moderate-intensity exercise with blood sugar monitoring. Focus on consistent cardio sessions and strength training to improve insulin sensitivity.'
        ];
    }
    
    if (in_array('hypertension', $healthConditions)) {
        $recommendations[] = [
            'type' => 'Health',
            'title' => 'Hypertension-Friendly Workout',
            'description' => 'Moderate-intensity cardio with proper breathing techniques. Avoid heavy lifting and focus on controlled movements with lower resistance and higher repetitions.'
        ];
    }
    
    if (in_array('asthma', $healthConditions)) {
        $recommendations[] = [
            'type' => 'Health',
            'title' => 'Asthma-Friendly Workout',
            'description' => 'Indoor workouts with proper warm-up and controlled breathing. Swimming and yoga can be particularly beneficial for improving lung capacity.'
        ];
    }
    
    if (in_array('heart_disease', $healthConditions)) {
        $recommendations[] = [
            'type' => 'Health',
            'title' => 'Heart-Friendly Workout',
            'description' => 'Low to moderate intensity exercise with careful monitoring. Focus on walking, swimming, or cycling with gradual progression and regular rest periods.'
        ];
    }
    
    return $recommendations;
}

// Function to get diet recommendations based on user profile
function get_diet_recommendations($userId) {
    global $conn;
    
    // Get user profile
    $profile = get_user_profile($userId);
    
    if (!$profile) {
        return [];
    }
    
    $recommendations = [];
    
    // Based on BMI
    $bmi = $profile['bmi'];
    $bmiCategory = get_bmi_category($bmi);
    
    if ($bmiCategory == "Underweight") {
        $recommendations[] = [
            'type' => 'BMI',
            'title' => 'Weight Gain Diet',
            'description' => 'Calorie surplus diet with focus on protein and healthy fats. Aim for 5-6 smaller meals throughout the day with protein-rich foods and calorie-dense options like nuts, avocados, and whole grains.'
        ];
    } elseif ($bmiCategory == "Overweight" || $bmiCategory == "Obese") {
        $recommendations[] = [
            'type' => 'BMI',
            'title' => 'Weight Loss Diet',
            'description' => 'Calorie deficit diet with high protein and fiber. Focus on vegetables, lean proteins, and limited carbohydrates. Practice portion control and mindful eating.'
        ];
    } else {
        $recommendations[] = [
            'type' => 'BMI',
            'title' => 'Maintenance Diet',
            'description' => 'Balanced diet with focus on whole foods. Include a mix of lean proteins, complex carbohydrates, healthy fats, and plenty of fruits and vegetables.'
        ];
    }
    
    // Based on fitness goals
    $fitnessGoals = $profile['fitness_goals'];
    
    if ($fitnessGoals == "weight_loss") {
        $recommendations[] = [
            'type' => 'Goal',
            'title' => 'Weight Loss Nutrition Plan',
            'description' => 'Create a moderate calorie deficit (300-500 calories below maintenance). Focus on high-protein foods, fiber-rich vegetables, and limited processed foods.'
        ];
    } elseif ($fitnessGoals == "muscle_gain") {
        $recommendations[] = [
            'type' => 'Goal',
            'title' => 'Muscle Building Nutrition Plan',
            'description' => 'Consume a calorie surplus with high protein intake (1.6-2.2g per kg of body weight). Include protein with every meal and focus on post-workout nutrition.'
        ];
    } elseif ($fitnessGoals == "endurance") {
        $recommendations[] = [
            'type' => 'Goal',
            'title' => 'Endurance Nutrition Plan',
            'description' => 'Carbohydrate-focused diet with proper hydration. Include complex carbs for sustained energy and protein for recovery. Time nutrition around training sessions.'
        ];
    } elseif ($fitnessGoals == "flexibility") {
        $recommendations[] = [
            'type' => 'Goal',
            'title' => 'Flexibility Support Nutrition Plan',
            'description' => 'Anti-inflammatory foods with adequate protein. Include omega-3 fatty acids, turmeric, ginger, and antioxidant-rich fruits and vegetables.'
        ];
    } else {
        $recommendations[] = [
            'type' => 'Goal',
            'title' => 'General Wellness Nutrition Plan',
            'description' => 'Balanced diet following the plate method (½ vegetables, ¼ protein, ¼ complex carbs). Focus on whole foods and limit processed foods, added sugars, and excessive sodium.'
        ];
    }
    
    // Based on health conditions
    $healthConditions = explode(',', $profile['health_conditions']);
    
    if (in_array('diabetes', $healthConditions)) {
        $recommendations[] = [
            'type' => 'Health',
            'title' => 'Diabetes-Friendly Diet',
            'description' => 'Low glycemic index foods with consistent meal timing. Focus on fiber-rich foods, lean proteins, and healthy fats. Limit refined carbohydrates and monitor carb intake.'
        ];
    }
    
    if (in_array('hypertension', $healthConditions)) {
        $recommendations[] = [
            'type' => 'Health',
            'title' => 'Hypertension-Friendly Diet',
            'description' => 'Low sodium diet with potassium-rich foods. Follow the DASH diet principles with emphasis on fruits, vegetables, whole grains, and limited processed foods.'
        ];
    }
    
    if (in_array('asthma', $healthConditions)) {
        $recommendations[] = [
            'type' => 'Health',
            'title' => 'Asthma-Friendly Diet',
            'description' => 'Anti-inflammatory foods with vitamin D and antioxidants. Include fatty fish, fruits, vegetables, and consider limiting common trigger foods like dairy or sulfites.'
        ];
    }
    
    if (in_array('heart_disease', $healthConditions)) {
        $recommendations[] = [
            'type' => 'Health',
            'title' => 'Heart-Friendly Diet',
            'description' => 'Mediterranean diet approach with limited saturated fats. Focus on omega-3 fatty acids, fiber-rich foods, lean proteins, and plenty of fruits and vegetables.'
        ];
    }
    
    return $recommendations;
}

// Handle AJAX requests
if (isset($_GET['action'])) {
    $action = $_GET['action'];
    $response = [];
    
    switch ($action) {
        case 'get_attendance':
            $response = get_attendance_data($userId);
            break;
            
        case 'get_workouts':
            $response = get_workout_history($userId);
            break;
            
        case 'get_health_data':
            $response = get_health_tracking_data($userId);
            break;
            
        case 'get_missed_days':
            $response = calculate_missed_days($userId);
            break;
            
        case 'get_recommendations':
            $response = [
                'workout' => get_workout_recommendations($userId),
                'diet' => get_diet_recommendations($userId)
            ];
            break;
            
        default:
            $response = ['error' => 'Invalid action'];
    }
    
    // Return JSON response
    header('Content-Type: application/json');
    echo json_encode($response);
    exit();
}

// If not an AJAX request, redirect to dashboard
redirect("../dashboard.php");
?>