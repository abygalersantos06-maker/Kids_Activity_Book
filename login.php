<?php
// login.php - Complete login page with admin redirect
require_once 'includes/db_connect.php';
require_once 'includes/functions.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Redirect if already logged in
if (isset($_SESSION['user_id']) && isset($_SESSION['user_role'])) {
    if ($_SESSION['user_role'] === 'admin') {
        header('Location: admin/dashboard.php');
        exit;
    } else {
        header('Location: index.php');
        exit;
    }
}

$error = '';
$success = '';
$remembered_username = '';

// Check for saved login cookie
if (isset($_COOKIE['remember_username'])) {
    $remembered_username = $_COOKIE['remember_username'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $remember_me = isset($_POST['remember_me']) ? true : false;
    
    if (empty($username) || empty($password)) {
        $error = 'Please enter both username and password.';
    } else {
        try {
            $stmt = $pdo->prepare("SELECT id, username, email, password, first_name, last_name, role FROM users WHERE username = ? OR email = ?");
            $stmt->execute([$username, $username]);
            $user = $stmt->fetch();
            
            if ($user && password_verify($password, $user['password'])) {
                // Set session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['user_role'] = $user['role'];
                $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
                
                // Update last login
                $ip_address = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
                $stmt = $pdo->prepare("UPDATE users SET last_login = NOW(), last_ip = ? WHERE id = ?");
                $stmt->execute([$ip_address, $user['id']]);
                
                // Handle Remember Me
                if ($remember_me) {
                    setcookie('remember_username', $username, time() + (86400 * 30), "/");
                } else {
                    setcookie('remember_username', '', time() - 3600, "/");
                }
                
                // Redirect based on role
                if ($user['role'] === 'admin') {
                    header('Location: admin/dashboard.php');
                    exit;
                } else {
                    header('Location: index.php');
                    exit;
                }
            } else {
                $error = 'Invalid username or password.';
            }
        } catch (PDOException $e) {
            error_log("Login error: " . $e->getMessage());
            $error = 'An error occurred. Please try again.';
        }
    }
}

$title = 'Login - KidsBookery';
include 'includes/header.php';
include 'includes/navigation.php';
?>

<main class="auth-page">
    <div class="container">
        <div class="auth-container">
            <div class="auth-card">
                <div class="auth-header">
                    <div class="auth-icon">🔐</div>
                    <h1>Welcome Back!</h1>
                    <p>Login to your KidsBookery account</p>
                </div>
                
                <?php if ($error): ?>
                <div class="alert alert-error">
                    <span>⚠️</span>
                    <?= htmlspecialchars($error) ?>
                </div>
                <?php endif; ?>
                
                <form method="POST" action="login.php" class="auth-form" id="loginForm">
                    <div class="form-group">
                        <label for="username">Username or Email</label>
                        <div class="input-wrapper">
                            <input type="text" 
                                   id="username" 
                                   name="username" 
                                   required 
                                   placeholder="Enter your username or email"
                                   value="<?= htmlspecialchars($_POST['username'] ?? $remembered_username) ?>">
                            <span class="input-icon">👤</span>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="password">Password</label>
                        <div class="input-wrapper">
                            <input type="password" 
                                   id="password" 
                                   name="password" 
                                   required 
                                   placeholder="Enter your password">
                            <span class="input-icon">🔒</span>
                            <button type="button" class="toggle-password" onclick="togglePassword()">👁️</button>
                        </div>
                    </div>
                    
                    <div class="form-options">
                        <label class="checkbox-label">
                            <input type="checkbox" name="remember_me" <?= $remembered_username ? 'checked' : '' ?>>
                            <span class="checkbox-custom"></span>
                            Remember Me
                        </label>
                    </div>
                    
                    <button type="submit" class="btn-login">Login</button>
                </form>
                
                <!-- Admin Demo Account Only -->
                <div class="demo-accounts">
                    <div class="divider">
                        <span>Admin Quick Login</span>
                    </div>
                    <div class="demo-list">
                        <button type="button" class="demo-btn admin" onclick="fillAndSubmit('admin', 'admin123')">👑 Admin Login</button>
                    </div>
                </div>
                
                <div class="auth-footer">
                    <p>Don't have an account? <a href="register.php">Create one now</a></p>
                </div>
            </div>
        </div>
    </div>
</main>

<style>
/* Auth Pages Styling */
.auth-page {
    min-height: calc(100vh - 400px);
    display: flex;
    align-items: center;
    background: linear-gradient(135deg, #112250 0%, #3C507D 100%);
    padding: 6rem 0;
}

.auth-container {
    max-width: 500px;
    margin: 0 auto;
    width: 100%;
    padding: 1rem;
}

.auth-card {
    background: #FFFFFF;
    border-radius: 1.5rem;
    padding: 2.5rem;
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    border: 1px solid rgba(224, 197, 143, 0.3);
    animation: fadeInUp 0.6s ease;
}

.auth-header {
    text-align: center;
    margin-bottom: 2rem;
}

.auth-icon {
    font-size: 3rem;
    margin-bottom: 1rem;
    animation: bounce 1s ease;
}

.auth-header h1 {
    font-size: 1.8rem;
    margin-bottom: 0.5rem;
    color: #112250;
}

.auth-header p {
    color: #6B7280;
    font-size: 0.9rem;
}

.alert {
    padding: 0.8rem 1rem;
    border-radius: 0.5rem;
    margin-bottom: 1.5rem;
    font-size: 0.85rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.alert-error {
    background: #FEF3F2;
    border-left: 4px solid #EF4444;
    color: #DC2626;
}

.input-wrapper {
    position: relative;
}

.input-wrapper input {
    width: 100%;
    padding: 0.8rem 2.5rem 0.8rem 2rem;
    border: 2px solid rgba(224, 197, 143, 0.4);
    border-radius: 0.5rem;
    font-size: 0.9rem;
    transition: all 0.3s ease;
    background: #FFFFFF;
}

.input-wrapper input:focus {
    outline: none;
    border-color: #E0C58F;
    box-shadow: 0 0 0 3px rgba(224, 197, 143, 0.2);
}

.input-icon {
    position: absolute;
    left: 0.8rem;
    top: 50%;
    transform: translateY(-50%);
    font-size: 0.9rem;
    color: #6B7280;
    pointer-events: none;
}

.toggle-password {
    position: absolute;
    right: 0.8rem;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    cursor: pointer;
    font-size: 0.9rem;
    color: #6B7280;
    transition: color 0.2s ease;
}

.toggle-password:hover {
    color: #112250;
}

.form-options {
    margin-bottom: 1.5rem;
}

.checkbox-label {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    cursor: pointer;
    font-size: 0.85rem;
    color: #6B7280;
}

.checkbox-label input {
    display: none;
}

.checkbox-custom {
    width: 18px;
    height: 18px;
    border: 2px solid rgba(224, 197, 143, 0.4);
    border-radius: 4px;
    display: inline-block;
    position: relative;
    transition: all 0.2s ease;
}

.checkbox-label input:checked + .checkbox-custom {
    background: #112250;
    border-color: #112250;
}

.checkbox-label input:checked + .checkbox-custom::after {
    content: '✓';
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    color: white;
    font-size: 10px;
}

.btn-login {
    width: 100%;
    padding: 0.9rem;
    background: #112250;
    color: #FFFFFF;
    border: none;
    border-radius: 9999px;
    font-size: 0.95rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    margin-top: 1rem;
}

.btn-login:hover {
    background: #0A1838;
    transform: translateY(-2px);
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

.demo-accounts {
    margin-top: 1.5rem;
}

.divider {
    position: relative;
    text-align: center;
    margin: 1.5rem 0;
}

.divider::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 0;
    right: 0;
    height: 1px;
    background: rgba(224, 197, 143, 0.3);
}

.divider span {
    position: relative;
    background: #FFFFFF;
    padding: 0 1rem;
    font-size: 0.75rem;
    color: #6B7280;
}

.demo-list {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
    justify-content: center;
}

.demo-btn {
    padding: 0.5rem 1rem;
    border: 1px solid rgba(224, 197, 143, 0.3);
    border-radius: 9999px;
    font-size: 0.75rem;
    cursor: pointer;
    transition: all 0.2s ease;
    background: #F5F0E9;
    color: #1F2937;
    font-weight: 500;
}

.demo-btn.admin {
    border-left: 3px solid #8B5CF6;
    background: linear-gradient(135deg, #F5F0E9, #E8D8C4);
}

.demo-btn:hover {
    background: #E0C58F;
    color: #112250;
    transform: translateY(-2px);
}

.auth-footer {
    text-align: center;
    margin-top: 1.8rem;
    padding-top: 1.2rem;
    border-top: 1px solid rgba(224, 197, 143, 0.3);
}

.auth-footer a {
    color: #112250;
    text-decoration: none;
    font-weight: 600;
    transition: color 0.2s ease;
}

.auth-footer a:hover {
    color: #E0C58F;
}

@keyframes bounce {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-5px); }
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@media (max-width: 480px) {
    .auth-card {
        padding: 1.5rem;
    }
    .auth-header h1 {
        font-size: 1.5rem;
    }
    .demo-list {
        flex-direction: column;
    }
    .demo-btn {
        width: 100%;
        text-align: center;
    }
}
</style>

<script>
function togglePassword() {
    const passwordInput = document.getElementById('password');
    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
    passwordInput.setAttribute('type', type);
}

function fillAndSubmit(username, password) {
    document.getElementById('username').value = username;
    document.getElementById('password').value = password;
    document.getElementById('loginForm').submit();
}
</script>

<?php include 'includes/footer.php'; ?>