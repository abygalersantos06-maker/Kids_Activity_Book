<?php
// register.php - Complete registration page
require_once 'includes/db_connect.php';
require_once 'includes/functions.php';

if (isLoggedIn()) {
    redirect('index.php');
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    $errors = [];
    if (empty($first_name)) $errors[] = 'First name is required';
    if (empty($last_name)) $errors[] = 'Last name is required';
    if (empty($username)) $errors[] = 'Username is required';
    if (empty($email)) $errors[] = 'Email is required';
    if (empty($password)) $errors[] = 'Password is required';
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email is required';
    if (strlen($username) < 3) $errors[] = 'Username must be at least 3 characters';
    if (strlen($password) < 6) $errors[] = 'Password must be at least 6 characters';
    if ($password !== $confirm_password) $errors[] = 'Passwords do not match';
    
    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        if ($stmt->fetch()) {
            $errors[] = 'Username or email already exists';
        }
    }
    
    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        try {
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password, first_name, last_name, role) VALUES (?, ?, ?, ?, ?, 'shopper')");
            $stmt->execute([$username, $email, $hashed_password, $first_name, $last_name]);
            $success = 'Account created successfully! You can now login.';
            $_POST = [];
        } catch (PDOException $e) {
            error_log("Registration error: " . $e->getMessage());
            $error = 'An error occurred. Please try again.';
        }
    } else {
        $error = implode('<br>', $errors);
    }
}

$title = 'Register - KidsBookery';
include 'includes/header.php';
include 'includes/navigation.php';
?>

<main class="auth-page">
    <div class="container">
        <div class="auth-container">
            <div class="auth-card">
                <div class="auth-header">
                    <div class="auth-icon">✨</div>
                    <h1>Join the Adventure!</h1>
                    <p>Create your KidsBookery account</p>
                </div>
                
                <?php if ($error): ?>
                <div class="alert alert-error">
                    <span>⚠️</span>
                    <?= $error ?>
                </div>
                <?php endif; ?>
                
                <?php if ($success): ?>
                <div class="alert alert-success">
                    <span>✓</span>
                    <?= $success ?>
                </div>
                <?php endif; ?>
                
                <form method="POST" action="register.php" class="auth-form">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="first_name">First Name</label>
                            <input type="text" id="first_name" name="first_name" required 
                                   value="<?= html_escape($_POST['first_name'] ?? '') ?>"
                                   placeholder="Enter first name">
                        </div>
                        <div class="form-group">
                            <label for="last_name">Last Name</label>
                            <input type="text" id="last_name" name="last_name" required 
                                   value="<?= html_escape($_POST['last_name'] ?? '') ?>"
                                   placeholder="Enter last name">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" id="username" name="username" required 
                               value="<?= html_escape($_POST['username'] ?? '') ?>"
                               placeholder="Choose a username">
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" required 
                               value="<?= html_escape($_POST['email'] ?? '') ?>"
                               placeholder="Enter your email">
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" id="password" name="password" required 
                                   placeholder="Create a password">
                        </div>
                        <div class="form-group">
                            <label for="confirm_password">Confirm Password</label>
                            <input type="password" id="confirm_password" name="confirm_password" required 
                                   placeholder="Confirm your password">
                        </div>
                    </div>
                    
                    <button type="submit" class="btn-login">Create Account</button>
                </form>
                
                <div class="auth-footer">
                    <p>Already have an account? <a href="login.php">Login here</a></p>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include 'includes/footer.php'; ?>