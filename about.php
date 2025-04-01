<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - FitLife Gym</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
    <!-- Animate.css for animations -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
</head>
<body>

    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top bg-dark">
        <div class="container">
            <a class="navbar-brand" href="index.html">
                <img src="assets/images/logo.png" alt="FitLife Gym" height="40">
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
                        <ul class="dropdown-menu">
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
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#">Morning (6AM - 10AM)</a></li>
                            <li><a class="dropdown-item" href="#">Afternoon (12PM - 4PM)</a></li>
                            <li><a class="dropdown-item" href="#">Evening (5PM - 9PM)</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="about.php" title="About Us">
                            <i class="fas fa-info-circle"></i>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="contact.php" title="Contact">
                            <i class="fas fa-envelope"></i>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="admin.php" title="Admin Login">
                            <i class="fas fa-user-shield"></i>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- About Us Section -->
    <section class="container mt-5 pt-5">
        <h2 class="text-center fw-bold">About FitLife Gym</h2>
        <p class="text-center">
            Your journey to fitness begins here! At FitLife Gym, we offer a range of classes, personal training sessions, and state-of-the-art equipment to help you achieve your health goals.
        </p>

        <div class="row align-items-center">
            <div class="col-md-6 text-center">
                <!-- Placeholder Box (Replace with an image later) -->
                <div class="bg-secondary text-white p-5 rounded shadow-lg">
                    Image Placeholder
                </div>
            </div>
            <div class="col-md-6">
                <h4 class="fw-bold">Our Mission</h4>
                <p>We strive to provide a motivating and inclusive environment where individuals of all fitness levels can improve their health and well-being.</p>
                
                <h4 class="fw-bold">Our Trainers</h4>
                <p>Our certified trainers are dedicated to guiding you through your fitness journey with customized programs and expert advice.</p>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white text-center py-3 mt-5">
        &copy; 2025 FitLife Gym. All rights reserved.
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
