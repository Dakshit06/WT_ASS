<?php
include 'config/db.php';
session_start();

// Get stats for dashboard
$stats = $conn->query("SELECT 
    COUNT(CASE WHEN role = 'mentor' THEN 1 END) as total_mentors,
    COUNT(CASE WHEN role = 'mentee' THEN 1 END) as total_mentees
    FROM users")->fetch_assoc();

// Get featured mentors with safer query
$featured_mentors = $conn->query("
    SELECT u.* 
    FROM users u 
    WHERE u.role = 'mentor' 
    ORDER BY u.created_at DESC
    LIMIT 4");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Mentor Management System - Home</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <!-- Hero Slider Section -->
    <div id="heroSlider" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-indicators">
            <button type="button" data-bs-target="#heroSlider" data-bs-slide-to="0" class="active"></button>
            <button type="button" data-bs-target="#heroSlider" data-bs-slide-to="1"></button>
            <button type="button" data-bs-target="#heroSlider" data-bs-slide-to="2"></button>
        </div>
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img src="https://images.unsplash.com/photo-1531545514256-b1400bc00f31" class="d-block w-100" alt="Mentorship">
                <div class="carousel-caption">
                    <h2>Find Your Perfect Mentor</h2>
                    <p>Connect with industry experts who can guide you towards success</p>
                    <a href="mentors.php" class="btn btn-primary btn-lg">Find a Mentor</a>
                </div>
            </div>
            <div class="carousel-item">
                <img src="https://images.unsplash.com/photo-1522071820081-009f0129c71c" class="d-block w-100" alt="Learning">
                <div class="carousel-caption">
                    <h2>Structured Learning Paths</h2>
                    <p>Follow customized learning pathways designed for your growth</p>
                    <a href="register.php" class="btn btn-primary btn-lg">Start Learning</a>
                </div>
            </div>
            <div class="carousel-item">
                <img src="https://images.unsplash.com/photo-1516321318423-f06f85e504b3" class="d-block w-100" alt="Success">
                <div class="carousel-caption">
                    <h2>Track Your Progress</h2>
                    <p>Monitor your growth with detailed analytics and feedback</p>
                    <a href="dashboard.php" class="btn btn-primary btn-lg">View Dashboard</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Section -->
    <section class="py-5 bg-light">
        <div class="container">
            <div class="row g-4">
                <div class="col-md-3">
                    <div class="stats-card text-center p-4">
                        <i class="bi bi-people-fill mb-3"></i>
                        <h3 class="counter-number"><?php echo $stats['total_mentors']; ?></h3>
                        <p class="text-muted mb-0">Expert Mentors</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stats-card text-center p-4">
                        <i class="bi bi-person-check-fill mb-3"></i>
                        <h3 class="counter-number"><?php echo $stats['total_mentees']; ?></h3>
                        <p class="text-muted mb-0">Active Mentees</p>
                    </div>
                </div>
                <!-- Add more stats cards -->
            </div>
        </div>
    </section>

    <!-- Featured Mentors -->
    <section class="py-5">
        <div class="container">
            <h2 class="text-center mb-5">Featured Mentors</h2>
            <div class="row g-4">
                <?php while($mentor = $featured_mentors->fetch_assoc()): ?>
                    <div class="col-md-3">
                        <div class="card mentor-card h-100">
                            <img src="<?php echo $mentor['profile_image'] ?? 'assets/images/default-profile.jpg'; ?>" 
                                 class="card-img-top" alt="<?php echo htmlspecialchars($mentor['name']); ?>">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($mentor['name']); ?></h5>
                                <p class="card-text"><?php echo htmlspecialchars($mentor['skills']); ?></p>
                                <div class="d-grid">
                                    <a href="view.php?id=<?php echo $mentor['id']; ?>" class="btn btn-outline-primary">View Profile</a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    </section>

    <!-- Call to Action -->
    <section class="py-5 bg-primary text-white">
        <div class="container text-center">
            <h2 class="mb-4">Ready to Start Your Journey?</h2>
            <p class="lead mb-4">Join our community of mentors and mentees today</p>
            <a href="register.php" class="btn btn-light btn-lg">Get Started</a>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Initialize carousel
        var heroSlider = new bootstrap.Carousel(document.getElementById('heroSlider'), {
            interval: 5000,
            wrap: true
        });
    </script>
</body>
</html>
