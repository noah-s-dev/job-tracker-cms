<?php
require_once 'config/database.php';
require_once 'includes/functions.php';
startSession();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' - ' : ''; ?>Job Application Tracker</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark" style="background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%); box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php">
                <i class="fas fa-briefcase me-2"></i>Job Tracker Pro
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <?php if (isLoggedIn()): ?>
                        <li class="nav-item">
                            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>" href="dashboard.php" style="font-weight: 500; transition: all 0.3s ease;">
                                <i class="fas fa-tachometer-alt me-1"></i>Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'applications.php' ? 'active' : ''; ?>" href="applications.php" style="font-weight: 500; transition: all 0.3s ease;">
                                <i class="fas fa-list me-1"></i>Applications
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'add_application.php' ? 'active' : ''; ?>" href="add_application.php" style="font-weight: 500; transition: all 0.3s ease;">
                                <i class="fas fa-plus me-1"></i>Add Application
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
                
                <ul class="navbar-nav">
                    <?php if (isLoggedIn()): ?>
                        <?php $user = getCurrentUser(); ?>
                        <?php if ($user): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <div style="width: 32px; height: 32px; border-radius: 50%; background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%); display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 4px rgba(0,0,0,0.2); margin-right: 8px;">
                                    <span style="color: white; font-weight: bold; font-size: 14px; text-transform: uppercase;"><?php echo strtoupper(substr($user['first_name'], 0, 1)); ?></span>
                                </div>
                                <span><?php echo htmlspecialchars($user['first_name']); ?></span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" style="border: none; box-shadow: 0 4px 12px rgba(0,0,0,0.15); border-radius: 8px;">
                                <li><a class="dropdown-item" href="profile.php">
                                    <i class="fas fa-user me-2"></i>Profile
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger" href="logout.php">
                                    <i class="fas fa-sign-out-alt me-2"></i>Logout
                                </a></li>
                            </ul>
                        </li>
                        <?php endif; ?>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'login.php' ? 'active' : ''; ?>" href="login.php">
                                <i class="fas fa-sign-in-alt me-1"></i>Login
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'register.php' ? 'active' : ''; ?>" href="register.php">
                                <i class="fas fa-user-plus me-1"></i>Register
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <main class="container-fluid mt-4">
        <?php
        $flashMessage = getFlashMessage();
        if ($flashMessage):
        ?>
        <div class="container">
            <div class="alert alert-<?php echo $flashMessage['type'] == 'error' ? 'danger' : $flashMessage['type']; ?> alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($flashMessage['message']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
        <?php endif; ?>

