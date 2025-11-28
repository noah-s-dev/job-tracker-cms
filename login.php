<?php
$pageTitle = "Login";
require_once 'includes/header.php';

// Redirect if already logged in
if (isLoggedIn()) {
    redirect('dashboard.php');
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = sanitizeInput($_POST['username']);
    $password = $_POST['password'];
    
    // Validation
    if (empty($username)) {
        $errors[] = "Username or email is required.";
    }
    
    if (empty($password)) {
        $errors[] = "Password is required.";
    }
    
    // Authenticate user
    if (empty($errors)) {
        $pdo = getDBConnection();
        
        // Check if input is email or username
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $username]);
        $user = $stmt->fetch();
        
        if ($user && verifyPassword($password, $user['password_hash'])) {
            // Login successful
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            
            setFlashMessage("Welcome back, " . htmlspecialchars($user['first_name']) . "!", "success");
            
            // Redirect to intended page or dashboard
            $redirectTo = isset($_GET['redirect']) ? $_GET['redirect'] : 'dashboard.php';
            redirect($redirectTo);
        } else {
            $errors[] = "Invalid username/email or password.";
        }
    }
}
?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="form-container">
                <h2 class="text-center mb-4">
                    <i class="fas fa-sign-in-alt me-2"></i>Sign In
                </h2>
                
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
                    <div class="mb-3">
                        <label for="username" class="form-label">Username or Email</label>
                        <input type="text" class="form-control" id="username" name="username" 
                               value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" required>
                        <div class="invalid-feedback">Please enter your username or email.</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                        <div class="invalid-feedback">Please enter your password.</div>
                    </div>
                    
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-sign-in-alt me-2"></i>Sign In
                        </button>
                    </div>
                </form>
                
                <div class="text-center mt-4">
                    <p>Don't have an account? <a href="register.php">Create one here</a></p>
                </div>
                
                <div class="text-center mt-3">
                    <small class="text-muted">
                        <strong>Demo Account:</strong><br>
                        Username: demo_user<br>
                        Password: demo123
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>

