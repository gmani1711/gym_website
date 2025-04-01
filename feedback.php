<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback - FitLife Gym</title>
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
                        <a class="nav-link active" href="feedback.php" title="Feedback">
                            <i class="fas fa-comment"></i>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#loginModal" title="Login">
                            <i class="fas fa-sign-in-alt"></i>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Page Header -->
    <header class="page-header">
        <div class="overlay"></div>
        <div class="container">
            <div class="row">
                <div class="col-md-12 text-center text-white">
                    <h1 class="display-4 fw-bold">Feedback</h1>
                    <p class="lead">We value your opinion! Help us improve our services</p>
                </div>
            </div>
        </div>
    </header>

    <!-- Feedback Section -->
    <section class="feedback-section py-5">
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

            <?php if (isset($_SESSION['errors'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <ul class="mb-0">
                        <?php 
                            foreach ($_SESSION['errors'] as $error) {
                                echo "<li>$error</li>";
                            }
                            unset($_SESSION['errors']);
                        ?>
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <div class="row">
                <div class="col-md-8 offset-md-2">
                    <div class="card">
                        <div class="card-body">
                            <h2 class="card-title text-center mb-4">Share Your Feedback</h2>
                            <form action="backend/feedback.php" method="post">
                                <input type="hidden" name="csrf_token" value="<?php echo isset($csrfToken) ? $csrfToken : ''; ?>">
                                
                                <div class="mb-3">
                                    <label for="name" class="form-label">Your Name</label>
                                    <input type="text" class="form-control" id="name" name="name" required value="<?php echo isset($_SESSION['form_data']['name']) ? htmlspecialchars($_SESSION['form_data']['name']) : ''; ?>">
                                </div>
                                
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email Address</label>
                                    <input type="email" class="form-control" id="email" name="email" required value="<?php echo isset($_SESSION['form_data']['email']) ? htmlspecialchars($_SESSION['form_data']['email']) : ''; ?>">
                                </div>
                                
                                <div class="mb-3">
                                    <label for="subject" class="form-label">Subject</label>
                                    <input type="text" class="form-control" id="subject" name="subject" required value="<?php echo isset($_SESSION['form_data']['subject']) ? htmlspecialchars($_SESSION['form_data']['subject']) : ''; ?>">
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Rate Your Experience</label>
                                    <div class="rating">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="rating" id="rating1" value="1" <?php echo (isset($_SESSION['form_data']['rating']) && $_SESSION['form_data']['rating'] == 1) ? 'checked' : ''; ?>>
                                            <label class="form-check-label" for="rating1">1 <i class="far fa-star"></i></label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="rating" id="rating2" value="2" <?php echo (isset($_SESSION['form_data']['rating']) && $_SESSION['form_data']['rating'] == 2) ? 'checked' : ''; ?>>
                                            <label class="form-check-label" for="rating2">2 <i class="far fa-star"></i></label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="rating" id="rating3" value="3" <?php echo (isset($_SESSION['form_data']['rating']) && $_SESSION['form_data']['rating'] == 3) ? 'checked' : ''; ?>>
                                            <label class="form-check-label" for="rating3">3 <i class="far fa-star"></i></label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="rating" id="rating4" value="4" <?php echo (isset($_SESSION['form_data']['rating']) && $_SESSION['form_data']['rating'] == 4) ? 'checked' : ''; ?>>
                                            <label class="form-check-label" for="rating4">4 <i class="far fa-star"></i></label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="rating" id="rating5" value="5" <?php echo (isset($_SESSION['form_data']['rating']) && $_SESSION['form_data']['rating'] == 5) ? 'checked' : ''; ?> checked>
                                            <label class="form-check-label" for="rating5">5 <i class="far fa-star"></i></label>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="message" class="form-label">Your Feedback</label>
                                    <textarea class="form-control" id="message" name="message" rows="5" required><?php echo isset($_SESSION['form_data']['message']) ? htmlspecialchars($_SESSION['form_data']['message']) : ''; ?></textarea>
                                </div>
                                
                                <div class="text-center">
                                    <button type="submit" class="btn btn-primary">Submit Feedback</button>
                                </div>
                            </form>
                            <?php unset($_SESSION['form_data']); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section class="testimonials py-5 bg-light">
        <div class="container">
            <h2 class="text-center mb-5">What Our Members Say</h2>
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="card testimonial-card animate__animated animate__fadeIn">
                        <div class="card-body">
                            <div class="testimonial-rating mb-3">
                                <i class="fas fa-star text-warning"></i>
                                <i class="fas fa-star text-warning"></i>
                                <i class="fas fa-star text-warning"></i>
                                <i class="fas fa-star text-warning"></i>
                                <i class="fas fa-star text-warning"></i>
                            </div>
                            <p class="card-text">"FitLife Gym has completely transformed my life. I've lost 15kg and gained so much confidence! The trainers are exceptional and the facilities are top-notch."</p>
                            <div class="d-flex align-items-center mt-3">
                                <img src="/placeholder.svg?height=50&width=50" class="rounded-circle me-3" alt="Testimonial">
                                <div>
                                    <h5 class="mb-0">Priya Sharma</h5>
                                    <small class="text-muted">Member since 2022</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card testimonial-card animate__animated animate__fadeIn" style="animation-delay: 0.2s;">
                        <div class="card-body">
                            <div class="testimonial-rating mb-3">
                                <i class="fas fa-star text-warning"></i>
                                <i class="fas fa-star text-warning"></i>
                                <i class="fas fa-star text-warning"></i>
                                <i class="fas fa-star text-warning"></i>
                                <i class="fas fa-star-half-alt text-warning"></i>
                            </div>
                            <p class="card-text">"The trainers here are exceptional. They've helped me achieve fitness goals I never thought possible. The community is supportive and motivating."</p>
                            <div class="d-flex align-items-center mt-3">
                                <img src="/placeholder.svg?height=50&width=50" class="rounded-circle me-3" alt="Testimonial">
                                <div>
                                    <h5 class="mb-0">Rahul Patel</h5>
                                    <small class="text-muted">Member since 2021</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card testimonial-card animate__animated animate__fadeIn" style="animation-delay: 0.4s;">
                        <div class="card-body">
                            <div class="testimonial-rating mb-3">
                                <i class="fas fa-star text-warning"></i>
                                <i class="fas fa-star text-warning"></i>
                                <i class="fas fa-star text-warning"></i>
                                <i class="fas fa-star text-warning"></i>
                                <i class="far fa-star text-warning"></i>
                            </div>
                            <p class="card-text">"The health tracking system has been a game-changer for me. I can see my progress and stay motivated. The variety of classes keeps my routine fresh and exciting."</p>
                            <div class="d-flex align-items-center mt-3">
                                <img src="/placeholder.svg?height=50&width=50" class="rounded-circle me-3" alt="Testimonial">
                                <div>
                                    <h5 class="mb-0">Ananya Singh</h5>
                                    <small class="text-muted">Member since 2023</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
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

    <!-- Login Modal -->
    <div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="loginModalLabel">Login to Your Account</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="backend/login.php" method="post">
                        <div class="mb-3">
                            <label for="loginEmail" class="form-label">Email address</label>
                            <input type="email" class="form-control" id="loginEmail" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="loginPassword" class="form-label">Password</label>
                            <input type="password" class="form-control" id="loginPassword" name="password" required>
                        </div>
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="rememberMe" name="rememberMe">
                            <label class="form-check-label" for="rememberMe">Remember me</label>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Login</button>
                    </form>
                    <div class="text-center mt-3">
                        <p>Don't have an account? <a href="membership.html">Sign up</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JS -->
    <script src="assets/js/main.js"></script>
    <script>
        // Generate CSRF token on page load
        document.addEventListener('DOMContentLoaded', function() {
            // In a real application, this would be handled by the server
            // For this example, we'll simulate it with a client-side function
            fetch('backend/feedback.php')
                .then(response => {
                    // The token would be set in the session by the server
                    console.log('CSRF token generated');
                })
                .catch(error => {
                    console.error('Error generating CSRF token:', error);
                });
                
            // Star rating visual effect
            const ratingInputs = document.querySelectorAll('input[name="rating"]');
            const ratingLabels = document.querySelectorAll('.rating .form-check-label');
            
            ratingInputs.forEach(input => {
                input.addEventListener('change', function() {
                    const rating = parseInt(this.value);
                    
                    ratingLabels.forEach((label, index) => {
                        const star = label.querySelector('i');
                        if (index < rating) {
                            star.className = 'fas fa-star';
                        } else {
                            star.className = 'far fa-star';
                        }
                    });
                });
            });
        });
    </script>
</body>
</html>