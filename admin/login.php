<?php
// admin/login.php - Dedicated Admin Login Page (Only allows specific admin account)
require_once '../includes/db_connect.php';
require_once '../includes/functions.php';

// Redirect if already logged in
if (isLoggedIn()) {
    if (isAdmin()) {
        redirect('dashboard.php');
    } else {
        redirect('../index.php');
    }
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $error = 'Please enter both username and password.';
    } else {
        try {
            // Only allow the specific admin account 'admin'
            $stmt = $pdo->prepare("SELECT id, username, email, password, first_name, last_name, role, is_super_admin, admin_level FROM users WHERE username = ? AND role = 'admin'");
            $stmt->execute([$username]);
            $user = $stmt->fetch();
            
            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['user_role'] = $user['role'];
                $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
                $_SESSION['is_super_admin'] = $user['is_super_admin'];
                $_SESSION['admin_level'] = $user['admin_level'];
                
                // Update last login
                $stmt = $pdo->prepare("UPDATE users SET last_login = NOW(), last_ip = ? WHERE id = ?");
                $stmt->execute([$_SERVER['REMOTE_ADDR'], $user['id']]);
                
                // Log admin login
                $stmt = $pdo->prepare("INSERT INTO admin_logs (admin_id, action, details, ip_address) VALUES (?, 'admin_login', 'Admin logged in from admin login page', ?)");
                $stmt->execute([$user['id'], $_SERVER['REMOTE_ADDR']]);
                
                redirect('dashboard.php');
            } else {
                $error = 'Invalid admin credentials. Only the admin account is allowed.';
            }
        } catch (PDOException $e) {
            error_log("Admin login error: " . $e->getMessage());
            $error = 'An error occurred. Please try again.';
        }
    }
}

$title = 'Admin Login - KidsBookery';
include '../includes/header.php';
?>

<main class="admin-login-page">
    <div class="container">
        <div class="admin-login-container">
            <div class="admin-login-card">
                <div class="admin-login-header">
                    <div class="admin-logo">⭐</div>
                    <h1>Admin Portal</h1>
                    <p>Enter your admin credentials</p>
                </div>
                
                <?php if ($error): ?>
                <div class="alert alert-error">
                    <span>⚠️</span>
                    <?= html_escape($error) ?>
                </div>
                <?php endif; ?>
                
                <form method="POST" action="login.php" class="admin-login-form">
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" id="username" name="username" required 
                               placeholder="Enter admin username"
                               value="<?= html_escape($_POST['username'] ?? '') ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" required 
                               placeholder="Enter admin password">
                    </div>
                    
                    <button type="submit" class="btn-admin-login">Access Dashboard</button>
                </form>
                
                <div class="admin-login-footer">
                    <a href="../index.php">← Back to Store</a>
                </div>
            </div>
        </div>
    </div>
</main>

<style>
.admin-login-page {
    min-height: 100vh;
    display: flex;
    align-items: center;
    background: linear-gradient(135deg, #112250 0%, #3C507D 100%);
    padding: 2rem;
}

.admin-login-container {
    max-width: 450px;
    margin: 0 auto;
    width: 100%;
}

.admin-login-card {
    background: var(--white);
    border-radius: 1.5rem;
    padding: 2.5rem;
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    border: 1px solid rgba(224, 197, 143, 0.3);
    animation: fadeInUp 0.6s ease;
}

.admin-login-header {
    text-align: center;
    margin-bottom: 2rem;
}

.admin-logo {
    font-size: 3rem;
    margin-bottom: 1rem;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.1); }
}

.admin-login-header h1 {
    font-size: 1.8rem;
    color: #112250;
    margin-bottom: 0.5rem;
}

.admin-login-header p {
    color: #6B7280;
    font-size: 0.9rem;
}

.admin-login-form .form-group {
    margin-bottom: 1.2rem;
}

.admin-login-form label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 600;
    font-size: 0.85rem;
    color: #374151;
}

.admin-login-form input {
    width: 100%;
    padding: 0.8rem 1rem;
    border: 2px solid rgba(224, 197, 143, 0.4);
    border-radius: 0.5rem;
    font-size: 0.9rem;
    transition: all 0.3s ease;
    background: #FFFFFF;
}

.admin-login-form input:focus {
    outline: none;
    border-color: #E0C58F;
    box-shadow: 0 0 0 3px rgba(224, 197, 143, 0.2);
}

.btn-admin-login {
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
    margin-top: 0.5rem;
}

.btn-admin-login:hover {
    background: #0A1838;
    transform: translateY(-2px);
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

.admin-login-footer {
    text-align: center;
    margin-top: 1.8rem;
    padding-top: 1.2rem;
    border-top: 1px solid rgba(224, 197, 143, 0.3);
}

.admin-login-footer a {
    color: #6B7280;
    text-decoration: none;
    font-size: 0.85rem;
    transition: color 0.2s ease;
}

.admin-login-footer a:hover {
    color: #112250;
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
    .admin-login-card {
        padding: 1.5rem;
    }
    
    .admin-login-header h1 {
        font-size: 1.5rem;
    }
}
</style>

<?php include '../includes/footer.php'; ?>