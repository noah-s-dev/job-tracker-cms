<?php
$pageTitle = "Profile";
require_once 'includes/header.php';

// Require login
requireLogin();

$user = getCurrentUser();
$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $firstName = sanitizeInput($_POST['first_name']);
    $lastName = sanitizeInput($_POST['last_name']);
    $email = sanitizeInput($_POST['email']);
    $currentPassword = $_POST['current_password'];
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];
    
    // Validation
    if (empty($firstName)) {
        $errors[] = "First name is required.";
    }
    
    if (empty($lastName)) {
        $errors[] = "Last name is required.";
    }
    
    if (empty($email)) {
        $errors[] = "Email is required.";
    } elseif (!isValidEmail($email)) {
        $errors[] = "Please enter a valid email address.";
    }
    
    // Check if email is already taken by another user
    if (empty($errors) && $email !== $user['email']) {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
        $stmt->execute([$email, $user['id']]);
        
        if ($stmt->fetch()) {
            $errors[] = "Email address is already taken.";
        }
    }
    
    // Password change validation
    if (!empty($newPassword)) {
        if (empty($currentPassword)) {
            $errors[] = "Current password is required to change password.";
        } elseif (!verifyPassword($currentPassword, $user['password_hash'])) {
            $errors[] = "Current password is incorrect.";
        } elseif (strlen($newPassword) < 6) {
            $errors[] = "New password must be at least 6 characters long.";
        } elseif ($newPassword !== $confirmPassword) {
            $errors[] = "New passwords do not match.";
        }
    }
    
    // Update user if no errors
    if (empty($errors)) {
        $pdo = getDBConnection();
        
        if (!empty($newPassword)) {
            // Update with new password
            $passwordHash = hashPassword($newPassword);
            $stmt = $pdo->prepare("UPDATE users SET first_name = ?, last_name = ?, email = ?, password_hash = ? WHERE id = ?");
            $result = $stmt->execute([$firstName, $lastName, $email, $passwordHash, $user['id']]);
        } else {
            // Update without password change
            $stmt = $pdo->prepare("UPDATE users SET first_name = ?, last_name = ?, email = ? WHERE id = ?");
            $result = $stmt->execute([$firstName, $lastName, $email, $user['id']]);
        }
        
        if ($result) {
            $success = true;
            setFlashMessage("Profile updated successfully!", "success");
            
            // Refresh user data
            $user = getCurrentUser();
        } else {
            $errors[] = "Failed to update profile. Please try again.";
        }
    }
}
?>

<div class="container">
    <div class="row">
        <div class="col-md-3">
            <div class="sidebar">
                <h5 class="mb-3">Account</h5>
                <nav class="nav flex-column">
                    <a class="nav-link active" href="profile.php">
                        <i class="fas fa-user me-2"></i>Profile
                    </a>
                    <a class="nav-link" href="dashboard.php">
                        <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                    </a>
                    <a class="nav-link" href="applications.php">
                        <i class="fas fa-briefcase me-2"></i>Applications
                    </a>
                </nav>
            </div>
        </div>
        
        <div class="col-md-9">
            <div class="form-container">
                <h2 class="mb-4">
                    <i class="fas fa-user me-2"></i>Profile Settings
                </h2>
                
                <?php if ($success): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle me-2"></i>
                        Profile updated successfully!
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            <?php foreach ($errors as $error): ?>
                                <li><?php echo htmlspecialchars($error); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
                
                <form method="POST" class="needs-validation" novalidate>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="first_name" class="form-label">First Name</label>
                            <input type="text" class="form-control" id="first_name" name="first_name" 
                                   value="<?php echo htmlspecialchars($user['first_name']); ?>" required>
                            <div class="invalid-feedback">Please provide your first name.</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="last_name" class="form-label">Last Name</label>
                            <input type="text" class="form-control" id="last_name" name="last_name" 
                                   value="<?php echo htmlspecialchars($user['last_name']); ?>" required>
                            <div class="invalid-feedback">Please provide your last name.</div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" 
                               value="<?php echo htmlspecialchars($user['username']); ?>" disabled>
                        <div class="form-text">Username cannot be changed.</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" class="form-control" id="email" name="email" 
                               value="<?php echo htmlspecialchars($user['email']); ?>" required>
                        <div class="invalid-feedback">Please provide a valid email address.</div>
                    </div>
                    
                    <hr class="my-4">
                    
                    <h5 class="mb-3">Change Password</h5>
                    <p class="text-muted">Leave password fields empty if you don't want to change your password.</p>
                    
                    <div class="mb-3">
                        <label for="current_password" class="form-label">Current Password</label>
                        <input type="password" class="form-control" id="current_password" name="current_password">
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="new_password" class="form-label">New Password</label>
                            <input type="password" class="form-control" id="new_password" name="new_password">
                            <div class="form-text">Password must be at least 6 characters long.</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="confirm_password" class="form-label">Confirm New Password</label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password">
                        </div>
                    </div>
                    
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Update Profile
                        </button>
                        <a href="dashboard.php" class="btn btn-secondary">
                            <i class="fas fa-times me-2"></i>Cancel
                        </a>
                    </div>
                </form>
                
                <div class="mt-4 pt-4 border-top">
                    <h6 class="text-muted">Account Information</h6>
                    <p class="mb-1"><strong>Member since:</strong> <?php echo formatDate($user['created_at']); ?></p>
                    <p class="mb-0"><strong>Last updated:</strong> <?php echo formatDate($user['updated_at']); ?></p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>

