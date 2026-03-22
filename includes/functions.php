<?php
/**
 * Helper Functions for KidsBookery
 * ALL FUNCTIONS IN ONE FILE
 */

declare(strict_types=1);

// ============================================================================
// CART FUNCTIONS
// ============================================================================

/**
 * Get total number of items in cart
 */
function getCartCount(): int
{
    if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
        return 0;
    }
    return array_sum(array_column($_SESSION['cart'], 'quantity'));
}

/**
 * Get cart total
 */
function getCartTotal(PDO $pdo): float
{
    $total = 0.00;
    
    if (empty($_SESSION['cart'])) {
        return $total;
    }
    
    foreach ($_SESSION['cart'] as $item) {
        $total += getProductPrice($pdo, $item['id']) * $item['quantity'];
    }
    
    return round($total, 2);
}

/**
 * Add item to cart
 */
function addToCart(int $product_id, int $quantity = 1): bool
{
    if ($quantity < 1) {
        return false;
    }
    
    if ($quantity > 10) {
        $quantity = 10;
    }
    
    $found = false;
    foreach ($_SESSION['cart'] as &$item) {
        if ($item['id'] === $product_id) {
            $item['quantity'] += $quantity;
            if ($item['quantity'] > 10) {
                $item['quantity'] = 10;
            }
            $found = true;
            break;
        }
    }
    
    if (!$found) {
        $_SESSION['cart'][] = [
            'id'       => $product_id,
            'quantity' => $quantity,
            'added_at' => time()
        ];
    }
    
    return true;
}

/**
 * Remove item from cart
 */
function removeFromCart(int $product_id): bool
{
    foreach ($_SESSION['cart'] as $key => $item) {
        if ($item['id'] === $product_id) {
            unset($_SESSION['cart'][$key]);
            $_SESSION['cart'] = array_values($_SESSION['cart']);
            return true;
        }
    }
    return false;
}

/**
 * Clear entire cart
 */
function clearCart(): void
{
    $_SESSION['cart'] = [];
}

/**
 * Check if product is in cart
 */
function isInCart(int $product_id)
{
    foreach ($_SESSION['cart'] as $item) {
        if ($item['id'] === $product_id) {
            return $item['quantity'];
        }
    }
    return false;
}

// ============================================================================
// PRODUCT FUNCTIONS
// ============================================================================

/**
 * Get product price from database
 */
function getProductPrice(PDO $pdo, int $id): float
{
    try {
        $stmt = $pdo->prepare("SELECT price FROM article WHERE id = ?");
        $stmt->execute([$id]);
        $product = $stmt->fetch();
        
        if ($product && isset($product['price'])) {
            return (float)$product['price'];
        }
    } catch (PDOException $e) {
        error_log("Error getting product price: " . $e->getMessage());
    }
    
    // Fallback prices in PHP
    $prices = [
        1 => 699, 2 => 799, 3 => 899, 4 => 749, 5 => 849, 6 => 649,
        7 => 999, 8 => 799, 9 => 749, 10 => 849, 11 => 899, 12 => 699,
        13 => 1099, 14 => 899, 15 => 799, 16 => 849, 17 => 749, 18 => 799,
        19 => 549, 20 => 599, 21 => 649, 22 => 499, 23 => 549, 24 => 699
    ];
    return $prices[$id] ?? 599;
}
/**
 * Get full member name by ID
 */
function getMemberName(PDO $pdo, int $member_id): string
{
    try {
        $stmt = $pdo->prepare("SELECT forename, surname FROM member WHERE id = ?");
        $stmt->execute([$member_id]);
        $result = $stmt->fetch();
        return $result ? trim($result['forename'] . ' ' . $result['surname']) : 'Unknown';
    } catch (PDOException $e) {
        error_log("Error getting member name: " . $e->getMessage());
        return 'Unknown';
    }
}
/**
 * Get product by ID
 */
function getProductById(PDO $pdo, int $id)
{
    try {
        $stmt = $pdo->prepare("
            SELECT a.*, c.name as category_name, i.file as image_file, i.alt as image_alt
            FROM article a
            LEFT JOIN category c ON a.category_id = c.id
            LEFT JOIN image i ON a.image_id = i.id
            WHERE a.id = ? AND a.published = 1
        ");
        $stmt->execute([$id]);
        return $stmt->fetch();
    } catch (PDOException $e) {
        error_log("Error getting product: " . $e->getMessage());
        return false;
    }
}

/**
 * Get related products
 */
function getRelatedProducts(PDO $pdo, int $category_id, int $current_id, int $limit = 4): array
{
    try {
        $stmt = $pdo->prepare("
            SELECT a.*, c.name as category_name, i.file as image_file, i.alt as image_alt
            FROM article a
            LEFT JOIN category c ON a.category_id = c.id
            LEFT JOIN image i ON a.image_id = i.id
            WHERE a.category_id = ? AND a.id != ? AND a.published = 1 
            ORDER BY RAND() LIMIT ?
        ");
        $stmt->execute([$category_id, $current_id, $limit]);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Error getting related products: " . $e->getMessage());
        return [];
    }
}

/**
 * Get navigation categories
 */
function getNavigationCategories(PDO $pdo): array
{
    try {
        $stmt = $pdo->query("SELECT id, name FROM category WHERE navigation = 1 ORDER BY id");
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Error getting navigation categories: " . $e->getMessage());
        return [];
    }
}

/**
 * Get category icon
 */
function getCategoryIcon(int $category_id): string
{
    $icons = [
        1 => '🎨',
        2 => '🧩',
        3 => '📚',
        4 => '📄'
    ];
    return $icons[$category_id] ?? '📖';
}

/**
 * Get category link
 */
function getCategoryLink(int $category_id): string
{
    $links = [
        1 => 'coloring.php',
        2 => 'puzzles.php',
        3 => 'educational.php',
        4 => 'member.php'
    ];
    return $links[$category_id] ?? 'category.php?id=' . $category_id;
}

// ============================================================================
// TEXT AND FORMATTING FUNCTIONS
// ============================================================================

/**
 * Format price as PHP Currency
 */
function formatPrice(float $price): string
{
    return '₱' . number_format($price, 2);
}

/**
 * Escape HTML output
 */
function html_escape(string $text): string
{
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

/**
 * Truncate text to specified length
 */
function truncateText(string $text, int $length = 100): string
{
    $text = strip_tags($text);
    
    if (strlen($text) <= $length) {
        return $text;
    }
    
    $truncated = substr($text, 0, $length);
    $last_space = strrpos($truncated, ' ');
    
    if ($last_space !== false) {
        $truncated = substr($truncated, 0, $last_space);
    }
    
    return $truncated . '...';
}

/**
 * Format date nicely
 */
function formatDate(string $date): string
{
    return date('F j, Y', strtotime($date));
}

/**
 * Get star rating HTML
 */
function getStarRating(float $rating): string
{
    $full_stars = floor($rating);
    $half_star = ($rating - $full_stars) >= 0.5;
    $empty_stars = 5 - $full_stars - ($half_star ? 1 : 0);
    
    $html = '';
    for ($i = 0; $i < $full_stars; $i++) $html .= '★';
    if ($half_star) $html .= '½';
    for ($i = 0; $i < $empty_stars; $i++) $html .= '☆';
    
    return $html;
}

// ============================================================================
// USER AUTHENTICATION FUNCTIONS
// ============================================================================

/**
 * Check if user is logged in
 */
function isLoggedIn(): bool
{
    return isset($_SESSION['user_id']);
}

/**
 * Get current user ID
 */
function getCurrentUserId(): ?int
{
    return $_SESSION['user_id'] ?? null;
}

/**
 * Get current user role
 */
function getUserRole(): ?string
{
    return $_SESSION['user_role'] ?? null;
}

/**
 * Check if user is admin
 */
function isAdmin(): bool
{
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

/**
 * Get current user data
 */
function getCurrentUser(PDO $pdo): ?array
{
    if (!isLoggedIn()) {
        return null;
    }
    
    try {
        $stmt = $pdo->prepare("SELECT id, username, email, first_name, last_name, role FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        return $stmt->fetch();
    } catch (PDOException $e) {
        error_log("Error getting current user: " . $e->getMessage());
        return null;
    }
}

/**
 * Redirect to URL
 */
function redirect(string $url): void
{
    header("Location: $url");
    exit;
}
?>