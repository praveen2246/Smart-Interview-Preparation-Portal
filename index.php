<?php
/**
 * SIPP - Smart Interview Preparation Portal
 * Home Page / Landing Page
 */

session_start();
$base_url = '';
$page_title = 'Home - SIPP';

// Include models and controllers
require_once 'config/Database.php';

// Get database connection
$database = new Database();
$connection = $database->connect();

// Check if user is already logged in
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
} elseif (isset($_SESSION['admin_id'])) {
    header('Location: admin/dashboard.php');
    exit;
}

include 'views/header.php';
?>

<!-- Hero Section -->
<section class="hero-section py-5" style="background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 100%); color: white;">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h1 class="display-4 fw-bold mb-3">Smart Interview Preparation Portal</h1>
                <p class="lead mb-4">Master PHP, Java, and React with AI-powered mock tests, performance tracking, and personalized recommendations.</p>
                <div class="d-grid gap-2 d-sm-flex">
                    <a href="register.php" class="btn btn-light btn-lg px-4 py-2 me-sm-3">
                        <i class="bi bi-person-plus"></i> Get Started
                    </a>
                    <a href="login.php" class="btn btn-outline-light btn-lg px-4 py-2">
                        <i class="bi bi-box-arrow-in-right"></i> Sign In
                    </a>
                </div>
            </div>
            <div class="col-md-6 text-center mt-4 mt-md-0">
                <i class="bi bi-lightning-fill" style="font-size: 5rem; opacity: 0.9;"></i>
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="py-5 bg-light">
    <div class="container">
        <h2 class="text-center mb-5 fw-bold">Powerful Features</h2>
        <div class="row g-4">
            <div class="col-md-6 col-lg-3">
                <div class="card h-100 text-center">
                    <div class="card-body">
                        <i class="bi bi-pencil-square text-primary" style="font-size: 2.5rem;"></i>
                        <h5 class="card-title mt-3">Mock Tests</h5>
                        <p class="card-text text-muted">Take randomized tests with 10 questions per session</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="card h-100 text-center">
                    <div class="card-body">
                        <i class="bi bi-hourglass-split text-success" style="font-size: 2.5rem;"></i>
                        <h5 class="card-title mt-3">10-Minute Timer</h5>
                        <p class="card-text text-muted">Complete tests within time constraints</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="card h-100 text-center">
                    <div class="card-body">
                        <i class="bi bi-graph-up text-info" style="font-size: 2.5rem;"></i>
                        <h5 class="card-title mt-3">Analytics</h5>
                        <p class="card-text text-muted">Track performance with detailed charts and reports</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="card h-100 text-center">
                    <div class="card-body">
                        <i class="bi bi-lightbulb text-warning" style="font-size: 2.5rem;"></i>
                        <h5 class="card-title mt-3">Smart Recommendations</h5>
                        <p class="card-text text-muted">Get personalized improvement suggestions</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Technologies Section -->
<section class="py-5">
    <div class="container">
        <h2 class="text-center mb-5 fw-bold">Supported Technologies</h2>
        <div class="row g-4 justify-content-center">
            <div class="col-md-4">
                <div class="card text-center">
                    <div class="card-body">
                        <h3 class="mb-3">
                            <span class="badge bg-primary">PHP</span>
                        </h3>
                        <p class="text-muted">Core concepts, OOP, MySQL, Security, Frameworks</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-center">
                    <div class="card-body">
                        <h3 class="mb-3">
                            <span class="badge bg-warning">Java</span>
                        </h3>
                        <p class="text-muted">Core concepts, Collections, Concurrency, Design Patterns</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-center">
                    <div class="card-body">
                        <h3 class="mb-3">
                            <span class="badge bg-info">React</span>
                        </h3>
                        <p class="text-muted">Hooks, Components, State Management, Routing</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-5 bg-primary text-white">
    <div class="container text-center">
        <h2 class="mb-3 fw-bold">Ready to Ace Your Interviews?</h2>
        <p class="lead mb-4">Join thousands of learners preparing for their dream jobs</p>
        <a href="register.php" class="btn btn-light btn-lg px-5">Start Learning Now</a>
    </div>
</section>

<?php include 'views/footer.php'; ?>
