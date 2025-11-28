<?php
$pageTitle = "Home";
require_once 'includes/header.php';

// Redirect to dashboard if logged in
if (isLoggedIn()) {
    redirect('dashboard.php');
}
?>

<div class="hero-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1>Track Your Job Applications</h1>
                <p class="lead">Stay organized and never miss an opportunity. Keep track of all your job applications, interviews, and follow-ups in one place.</p>
                <div class="mt-4">
                    <a href="register.php" class="btn btn-light btn-lg me-3">
                        <i class="fas fa-user-plus me-2"></i>Get Started
                    </a>
                    <a href="login.php" class="btn btn-outline-light btn-lg">
                        <i class="fas fa-sign-in-alt me-2"></i>Sign In
                    </a>
                </div>
            </div>
            <div class="col-lg-6 text-center">
                <i class="fas fa-briefcase" style="font-size: 12rem; opacity: 0.3;"></i>
            </div>
        </div>
    </div>
</div>

<div class="container">
    <div class="row">
        <div class="col-12 text-center mb-5">
            <h2>Why Choose Job Application Tracker?</h2>
            <p class="lead text-muted">Everything you need to manage your job search effectively</p>
        </div>
    </div>
    
    <div class="row">
        <div class="col-lg-4 mb-4">
            <div class="card feature-card">
                <div class="card-body">
                    <i class="fas fa-list-alt"></i>
                    <h5 class="card-title">Organize Applications</h5>
                    <p class="card-text">Keep all your job applications organized with company details, positions, dates, and current status.</p>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4 mb-4">
            <div class="card feature-card">
                <div class="card-body">
                    <i class="fas fa-chart-line"></i>
                    <h5 class="card-title">Track Progress</h5>
                    <p class="card-text">Monitor your application status from initial submission to final decision with visual progress tracking.</p>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4 mb-4">
            <div class="card feature-card">
                <div class="card-body">
                    <i class="fas fa-bell"></i>
                    <h5 class="card-title">Follow-up Reminders</h5>
                    <p class="card-text">Never miss a follow-up opportunity with built-in reminder system for important dates.</p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mt-5">
        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="fas fa-shield-alt text-primary me-2"></i>Secure & Private
                    </h5>
                    <p class="card-text">Your job search data is completely private and secure. Only you have access to your application information.</p>
                </div>
            </div>
        </div>
        
        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="fas fa-mobile-alt text-primary me-2"></i>Mobile Friendly
                    </h5>
                    <p class="card-text">Access your job application tracker from anywhere with our responsive design that works on all devices.</p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mt-5">
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center py-5">
                    <h3>Ready to Get Started?</h3>
                    <p class="lead">Join thousands of job seekers who are staying organized with our application tracker.</p>
                    <div class="mt-4">
                        <a href="register.php" class="btn btn-primary btn-lg me-3">
                            <i class="fas fa-user-plus me-2"></i>Create Free Account
                        </a>
                        <a href="login.php" class="btn btn-outline-primary btn-lg">
                            <i class="fas fa-sign-in-alt me-2"></i>Sign In
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>

