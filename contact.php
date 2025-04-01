<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - FitLife Gym</title>
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
                        <a class="nav-link active" href="contact.php" title="Contact Us">
                            <i class="fas fa-phone"></i>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="feedback.php" title="Feedback">
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
                    <h1 class="display-4 fw-bold">Contact Us</h1>
                    <p class="lead">Get in touch with our team for any inquiries or support</p>
                </div>
            </div>
        </div>
    </header>

    <!-- Contact Section -->
    <section class="contact-section py-5">
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
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <h2 class="card-title mb-4">Send Us a Message</h2>
                            <form action="backend/contact.php" method="post">
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
                                    <label for="message" class="form-label">Message</label>
                                    <textarea class="form-control" id="message" name="message" rows="5" required><?php echo isset($_SESSION['form_data']['message']) ? htmlspecialchars($_SESSION['form_data']['message']) : ''; ?></textarea>
                                </div>
                                
                                <button type="submit" class="btn btn-primary">Send Message</button>
                            </form>
                            <?php unset($_SESSION['form_data']); ?>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <h2 class="card-title mb-4">Contact Information</h2>
                            <div class="contact-info">
                                <div class="d-flex mb-4">
                                    <div class="contact-icon me-3">
                                        <i class="fas fa-map-marker-alt fa-2x text-primary"></i>
                                    </div>
                                    <div>
                                        <h5>Our Location</h5>
                                        <p class="mb-0">123 Fitness Street, Mumbai, India</p>
                                    </div>
                                </div>
                                
                                <div class="d-flex mb-4">
                                    <div class="contact-icon me-3">
                                        <i class="fas fa-phone fa-2x text-primary"></i>
                                    </div>
                                    <div>
                                        <h5>Phone Number</h5>
                                        <p class="mb-0">+91 9876543210</p>
                                        <p class="mb-0">+91 9876543211</p>
                                    </div>
                                </div>
                                
                                <div class="d-flex mb-4">
                                    <div class="contact-icon me-3">
                                        <i class="fas fa-envelope fa-2x text-primary"></i>
                                    </div>
                                    <div>
                                        <h5>Email Address</h5>
                                        <p class="mb-0">info@fitlifegym.com</p>
                                        <p class="mb-0">support@fitlifegym.com</p>
                                    </div>
                                </div>
                                
                                <div class="d-flex">
                                    <div class="contact-icon me-3">
                                        <i class="fas fa-clock fa-2x text-primary"></i>
                                    </div>
                                    <div>
                                        <h5>Working Hours</h5>
                                        <p class="mb-0">Monday - Friday: 6:00 AM - 10:00 PM</p>
                                        <p class="mb-0">Saturday - Sunday: 8:00 AM - 8:00 PM</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Map Section -->
    <section class="map-section py-5 bg-light">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <h2 class="text-center mb-4">Find Us On Map</h2>
                    <div class="map-container">
                        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d241317.11609823277!2d72.74109995709657!3d19.08219783958221!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3be7c6306644edc1%3A0x5da4ed8f8d648c69!2sMumbai%2C%20Maharashtra!5e0!3m2!1sen!2sin!4v1625147890962!5m2!1sen!2sin" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="faq-section py-5">
        <div class="container">
            <h2 class="text-center mb-5">Frequently Asked Questions</h2>
            <div class="row">
                <div class="col-md-10 offset-md-1">
                    <div class="accordion" id="contactFAQ">
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingOne">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                    What are your gym operating hours?
                                </button>
                            </h2>
                            <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#contactFAQ">
                                <div class="accordion-body">
                                    Our gym is open Monday through Friday from 6:00 AM to 10:00 PM, and on weekends from 8:00 AM to 8:00 PM. We are closed on major holidays, but may have limited hours on holiday eves.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingTwo">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                    How can I book a personal training session?
                                </button>
                            </h2>
                            <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#contactFAQ">
                                <div class="accordion-body">
                                    You can book a personal training session by contacting our front desk, calling our phone number, or using the booking feature in your member dashboard. Personal training sessions can be booked up to 2 weeks in advance.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingThree">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                    What is your cancellation policy?
                                </button>
                            </h2>
                            <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#contactFAQ">
                                <div class="accordion-body">
                                    Our cancellation policy requires a 30-day notice for membership cancellations. For class or personal training cancellations, we require at least 24 hours notice to avoid being charged for the session.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingFour">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                                    Do you offer any trial memberships?
                                </button>
                            </h2>
                            <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingFour" data-bs-parent="#contactFAQ">
                                <div class="accordion-body">
                                    Yes, we offer a 7-day trial membership for new members. This allows you to experience our facilities, classes, and services before committing to a full membership. Contact our front desk or fill out the contact form to arrange your trial.
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
            fetch('backend/contact.php')
                .then(response => {
                    // The token would be set in the session by the server
                    console.log('CSRF token generated');
                })
                .catch(error => {
                    console.error('Error generating CSRF token:', error);
                });
        });
    </script>
</body>
</html>