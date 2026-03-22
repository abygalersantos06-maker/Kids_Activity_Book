<?php
// includes/navigation.php - Works from root AND admin folder

// Detect if we're in admin folder
$in_admin = strpos($_SERVER['PHP_SELF'], '/admin/') !== false;
$base_path = $in_admin ? '../' : '';

// Define getCartCount function if not exists
if (!function_exists('getCartCount')) {
    function getCartCount() {
        if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
            return 0;
        }
        return array_sum(array_column($_SESSION['cart'], 'quantity'));
    }
}

// Define html_escape if not exists
if (!function_exists('html_escape')) {
    function html_escape($string) {
        return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    }
}

// Get current page name for active class
$current_page = basename($_SERVER['PHP_SELF']);

// Check login status
$is_logged_in = isset($_SESSION['user_id']);
$is_admin = isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';

// Get user info
$user = [];
if ($is_logged_in) {
    $user['first_name'] = $_SESSION['user_name'] ?? $_SESSION['username'] ?? 'User';
}

// Active class helper
function isActive($page, $current_page) {
    return $current_page == $page ? 'active' : '';
}

$cart_count = getCartCount();
?>

<!-- Single Floating Navigation -->
<nav class="floating-nav">
    <div class="nav-container">
        <div class="logo">
            <a href="<?php echo $base_path; ?>index.php">
                <img src="<?php echo $base_path; ?>images/logo.png" 
                     alt="KidsBookery Logo" 
                     class="logo-img"
                     style="height: 60px; width: 70;">
                <div class="logo-text" style="display: none;">Kids<span>Bookery</span></div>
            </a>
        </div>

        <div class="nav-links">
            <a href="<?php echo $base_path; ?>index.php" class="<?php echo isActive('index.php', $current_page); ?>">Home</a>
            <a href="<?php echo $base_path; ?>coloring.php" class="<?php echo isActive('coloring.php', $current_page); ?>">Coloring</a>
            <a href="<?php echo $base_path; ?>puzzles.php" class="<?php echo isActive('puzzles.php', $current_page); ?>">Puzzles</a>
            <a href="<?php echo $base_path; ?>educational.php" class="<?php echo isActive('educational.php', $current_page); ?>">Educational</a>
            <a href="<?php echo $base_path; ?>member.php" class="<?php echo isActive('member.php', $current_page); ?>">Members</a>
            <?php if ($is_admin): ?>
            <a href="<?php echo $base_path; ?>admin/dashboard.php" class="<?php echo $current_page == 'dashboard.php' ? 'active' : ''; ?>">Admin</a>
            <?php endif; ?>
        </div>

        <div class="nav-actions">
            <div class="nav-search">
                <form action="<?php echo $base_path; ?>search.php" method="GET">
                    <input type="text" name="term" placeholder="Search books..." value="<?php echo isset($_GET['term']) ? html_escape($_GET['term']) : ''; ?>">
                    <button type="submit">🔍</button>
                </form>
            </div>

            <a href="<?php echo $base_path; ?>cart.php" class="cart-link">
                <span class="cart-icon">🛒</span>
                <span class="cart-text">Cart</span>
                <?php if($cart_count > 0): ?>
                <span class="cart-count"><?php echo $cart_count; ?></span>
                <?php endif; ?>
            </a>

            <?php if ($is_logged_in): ?>
            <div class="user-menu">
                <button class="user-menu-btn">
                    <span class="user-icon">👤</span>
                    <span><?php echo html_escape($user['first_name']); ?></span>
                    <span class="dropdown-arrow">▼</span>
                </button>
                <div class="user-dropdown">
                    <a href="<?php echo $base_path; ?>profile.php">My Profile</a>
                    <a href="<?php echo $base_path; ?>orders.php">My Orders</a>
                    <a href="<?php echo $base_path; ?>logout.php">Logout</a>
                </div>
            </div>
            <?php else: ?>
            <a href="<?php echo $base_path; ?>login.php" class="login-link">Login</a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<script>
// User menu dropdown
document.addEventListener('DOMContentLoaded', function() {
    const userMenuBtn = document.querySelector('.user-menu-btn');
    const userDropdown = document.querySelector('.user-dropdown');
    
    if (userMenuBtn) {
        userMenuBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            userDropdown.classList.toggle('show');
        });
    }
    
    document.addEventListener('click', function() {
        if (userDropdown) {
            userDropdown.classList.remove('show');
        }
    });
});
</script>