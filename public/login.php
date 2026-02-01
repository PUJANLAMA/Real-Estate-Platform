<?php
require_once "../config/db.php";

// Redirect if already logged in
if (isLoggedIn()) {
    redirect('index.php');
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF token
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Invalid security token. Please try again.';
    } else {
        $username = sanitizeInput($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        
        // Validation
        if (empty($username)) {
            $errors[] = 'Username is required.';
        }
        
        if (empty($password)) {
            $errors[] = 'Password is required.';
        }
        
        // Authenticate user
        if (empty($errors)) {
            $stmt = $pdo->prepare("SELECT id, username, password FROM users WHERE username = ? OR email = ?");
            $stmt->execute([$username, $username]);
            $user = $stmt->fetch();
            
            if ($user && verifyPassword($password, $user['password'])) {
                // Regenerate session ID to prevent session fixation
                session_regenerate_id(true);
                
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                
                // Update last login
                $updateStmt = $pdo->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
                $updateStmt->execute([$user['id']]);
                
                redirect('index.php', 'Welcome back, ' . $user['username'] . '!', 'success');
            } else {
                $errors[] = 'Invalid username or password.';
            }
        }
    }
}

include "../includes/header.php";
?>

<div class="auth-container">
    <div class="auth-box">
        <h2>Login</h2>
        <p class="auth-subtitle">Access your account</p>
        
        <?php if (!empty($errors)): ?>
            <div class="error-message">
                <?php foreach ($errors as $error): ?>
                    <p>✗ <?php echo htmlspecialchars($error); ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <?php echo displayMessage(); ?>
        
        <form method="POST" action="">
            <?php echo getCSRFField(); ?>
            
            <div class="form-group">
                <label for="username">Username or Email</label>
                <input type="text" id="username" name="username" required 
                       value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>"
                       placeholder="Enter username or email">
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required 
                       placeholder="Enter password">
            </div>
            
            <button type="submit" class="btn btn-primary btn-block">
                Login
            </button>
        </form>
        
        <p class="auth-link">
            Don't have an account? <a href="register.php">Register here</a>
        </p>
    </div>
</div>

<?php include "../includes/footer.php"; ?>
