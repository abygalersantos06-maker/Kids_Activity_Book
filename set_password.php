<?php
// set_password.php - Set correct admin password
require_once 'includes/db_connect.php';
require_once 'includes/functions.php';

// Create correct hash for 'admin123'
$correct_hash = password_hash('admin123', PASSWORD_DEFAULT);

// First, check if users table exists
try {
    $stmt = $pdo->query("SHOW TABLES LIKE 'users'");
    if ($stmt->rowCount() == 0) {
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS `users` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `username` varchar(50) NOT NULL,
                `email` varchar(254) NOT NULL,
                `password` varchar(255) NOT NULL,
                `first_name` varchar(100) NOT NULL,
                `last_name` varchar(100) NOT NULL,
                `role` enum('admin','shopper') NOT NULL DEFAULT 'shopper',
                `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `last_login` timestamp NULL DEFAULT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `username` (`username`),
                UNIQUE KEY `email` (`email`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");
        echo "<p>Users table created.</p>";
    }
} catch (PDOException $e) {
    echo "<p>Note: " . $e->getMessage() . "</p>";
}

// Check if admin exists
$stmt = $pdo->prepare("SELECT id FROM users WHERE username = 'admin'");
$stmt->execute();
$admin_exists = $stmt->fetch();

if (!$admin_exists) {
    $stmt = $pdo->prepare("INSERT INTO users (username, email, password, first_name, last_name, role) 
                           VALUES ('admin', 'admin@kidsbookery.com', ?, 'Admin', 'User', 'admin')");
    $stmt->execute([$correct_hash]);
    echo "<p>Admin user created!</p>";
} else {
    $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE username = 'admin'");
    $stmt->execute([$correct_hash]);
    echo "<p>Admin password updated!</p>";
}

// Verify it worked
$stmt = $pdo->prepare("SELECT password FROM users WHERE username = 'admin'");
$stmt->execute();
$user = $stmt->fetch();

echo "<h2>Password Fix</h2>";
echo "<p>New hash created: " . $correct_hash . "</p>";

if (password_verify('admin123', $user['password'])) {
    echo "<p style='color: green; font-weight: bold;'>✓ SUCCESS! Password 'admin123' now works for admin!</p>";
    echo "<p><a href='login.php' style='display: inline-block; background: #112250; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Click here to login</a></p>";
} else {
    echo "<p style='color: red;'>Failed to set password. Please check database connection.</p>";
}
?>