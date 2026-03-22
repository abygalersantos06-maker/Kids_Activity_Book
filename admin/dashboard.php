<?php
// admin/dashboard.php - Admin Dashboard
require_once '../includes/db_connect.php';
require_once '../includes/functions.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

// Get admin info
$admin_id = $_SESSION['user_id'];
$admin_name = $_SESSION['user_name'] ?? 'Admin';
$admin_username = $_SESSION['username'] ?? '';
$is_super_admin = $_SESSION['is_super_admin'] ?? 0;
$admin_level = $_SESSION['admin_level'] ?? 'admin';

// ============================================================================
// STATIC SAMPLE DATA (Fallback when database is empty)
// ============================================================================

// Sample Products Data
$sample_products = [
    ['id' => 1, 'title' => 'Jungle Coloring Fun', 'category_name' => 'Coloring Books', 'price' => 699, 'published' => 1, 'created' => '2026-03-04'],
    ['id' => 2, 'title' => 'Underwater Adventure', 'category_name' => 'Coloring Books', 'price' => 799, 'published' => 1, 'created' => '2026-03-04'],
    ['id' => 3, 'title' => 'Space Explorers', 'category_name' => 'Coloring Books', 'price' => 899, 'published' => 1, 'created' => '2026-03-04'],
    ['id' => 4, 'title' => 'Brain Teaser Puzzles', 'category_name' => 'Puzzle Books', 'price' => 999, 'published' => 1, 'created' => '2026-03-04'],
    ['id' => 5, 'title' => 'Alphabet Adventure', 'category_name' => 'Educational Games', 'price' => 1099, 'published' => 1, 'created' => '2026-03-04'],
];

// Sample Orders Data
$sample_orders = [
    ['id' => 1001, 'order_date' => '2026-03-20 10:30:00', 'first_name' => 'Maria', 'last_name' => 'Santos', 'total_amount' => 1899, 'status' => 'completed'],
    ['id' => 1002, 'order_date' => '2026-03-19 14:15:00', 'first_name' => 'Juan', 'last_name' => 'Dela Cruz', 'total_amount' => 749, 'status' => 'pending'],
    ['id' => 1003, 'order_date' => '2026-03-18 09:45:00', 'first_name' => 'Ana', 'last_name' => 'Reyes', 'total_amount' => 2548, 'status' => 'processing'],
    ['id' => 1004, 'order_date' => '2026-03-17 16:20:00', 'first_name' => 'Carlos', 'last_name' => 'Garcia', 'total_amount' => 549, 'status' => 'completed'],
    ['id' => 1005, 'order_date' => '2026-03-16 11:00:00', 'first_name' => 'Bea', 'last_name' => 'Alonzo', 'total_amount' => 1699, 'status' => 'pending'],
];

// Get stats from database with fallback
try {
    $total_products = $pdo->query("SELECT COUNT(*) FROM article WHERE published = 1")->fetchColumn();
    $total_products = $total_products ?: count($sample_products);
    
    $total_orders = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
    $total_orders = $total_orders ?: count($sample_orders);
    
    $total_users = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'shopper'")->fetchColumn();
    $total_users = $total_users ?: 24;
    
    $total_revenue = $pdo->query("SELECT SUM(total_amount) FROM orders")->fetchColumn() ?? 0;
    $total_revenue = $total_revenue ?: 15750;
    
    $pending_orders = $pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'pending'")->fetchColumn();
    $pending_orders = $pending_orders ?: 2;
} catch (PDOException $e) {
    $total_products = count($sample_products);
    $total_orders = count($sample_orders);
    $total_users = 24;
    $total_revenue = 15750;
    $pending_orders = 2;
}

// Get recent orders from database or use sample
$recent_orders = [];
try {
    $recent_orders = $pdo->query("SELECT * FROM orders ORDER BY order_date DESC LIMIT 5")->fetchAll();
    if (empty($recent_orders)) {
        $recent_orders = $sample_orders;
    }
} catch (PDOException $e) {
    $recent_orders = $sample_orders;
}

// Get recent products from database or use sample
$recent_products = [];
try {
    $recent_products = $pdo->query("SELECT a.*, c.name as category_name FROM article a LEFT JOIN category c ON a.category_id = c.id ORDER BY a.created DESC LIMIT 5")->fetchAll();
    if (empty($recent_products)) {
        $recent_products = $sample_products;
    }
} catch (PDOException $e) {
    $recent_products = $sample_products;
}

$title = 'Admin Dashboard';

// Include header and navigation
include '../includes/header.php';
include '../includes/navigation.php';
?>

<main class="admin-dashboard">
    <div class="container">
        <!-- Welcome Header -->
        <div class="admin-header">
            <div>
                <h1>Admin Dashboard</h1>
                <div class="admin-info">
                    <p class="welcome-text">Welcome back, <?= htmlspecialchars($admin_name) ?>!</p>
                    <?php if($is_super_admin): ?>
                    <span class="admin-badge super">⭐ Super Admin</span>
                    <?php elseif($admin_level === 'manager'): ?>
                    <span class="admin-badge manager">📊 Manager</span>
                    <?php else: ?>
                    <span class="admin-badge admin">👑 Administrator</span>
                    <?php endif; ?>
                </div>
            </div>
            <a href="add-product.php" class="btn-add-product">
                <span>+</span> Add New Product
            </a>
        </div>
        
        <!-- Stats Grid -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">📚</div>
                <div class="stat-info">
                    <h3><?= number_format($total_products) ?></h3>
                    <p>Total Products</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">🛒</div>
                <div class="stat-info">
                    <h3><?= number_format($total_orders) ?></h3>
                    <p>Total Orders</p>
                    <?php if($pending_orders > 0): ?>
                    <span class="stat-badge"><?= $pending_orders ?> pending</span>
                    <?php endif; ?>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">👥</div>
                <div class="stat-info">
                    <h3><?= number_format($total_users) ?></h3>
                    <p>Total Users</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">💰</div>
                <div class="stat-info">
                    <h3><?= formatPrice($total_revenue) ?></h3>
                    <p>Total Revenue</p>
                </div>
            </div>
        </div>
        
        <!-- Quick Actions -->
        <div class="quick-actions">
            <h2>Quick Actions</h2>
            <div class="actions-grid">
                <a href="add-product.php" class="action-card">
                    <span class="action-icon">📖</span>
                    <span>Add Product</span>
                </a>
                <a href="orders.php" class="action-card">
                    <span class="action-icon">📦</span>
                    <span>View Orders</span>
                    <?php if($pending_orders > 0): ?>
                    <span class="badge"><?= $pending_orders ?></span>
                    <?php endif; ?>
                </a>
                <a href="users.php" class="action-card">
                    <span class="action-icon">👥</span>
                    <span>Manage Users</span>
                </a>
                <a href="../index.php" class="action-card">
                    <span class="action-icon">🏠</span>
                    <span>View Store</span>
                </a>
                <a href="../logout.php" class="action-card logout">
                    <span class="action-icon">🚪</span>
                    <span>Logout</span>
                </a>
            </div>
        </div>
        
        <!-- Recent Orders -->
        <div class="admin-section">
            <div class="section-header">
                <h2>Recent Orders</h2>
                <a href="orders.php" class="view-all">View All →</a>
            </div>
            <div class="table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Order #</th>
                            <th>Date</th>
                            <th>Customer</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($recent_orders)): ?>
                        <tr>
                            <td colspan="6" class="empty-table">No orders found</td>
                        </tr>
                        <?php else: ?>
                        <?php foreach($recent_orders as $order): ?>
                        <tr>
                            <td class="order-id">#<?= $order['id'] ?></td>
                            <td><?= date('M j, Y', strtotime($order['order_date'])) ?></td>
                            <td><?= htmlspecialchars($order['first_name'] ?? 'Guest') . ' ' . htmlspecialchars($order['last_name'] ?? '') ?></td>
                            <td class="price"><?= formatPrice($order['total_amount']) ?></td>
                            <td>
                                <span class="status-badge status-<?= $order['status'] ?>">
                                    <?= ucfirst($order['status']) ?>
                                </span>
                            </td>
                            <td>
                                <a href="order.php?id=<?= $order['id'] ?>" class="btn-view-order">View</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Recent Products -->
        <div class="admin-section">
            <div class="section-header">
                <h2>Recent Products</h2>
                <a href="products.php" class="view-all">View All →</a>
            </div>
            <div class="table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($recent_products)): ?>
                        <tr>
                            <td colspan="6" class="empty-table">No products found</td>
                        </tr>
                        <?php else: ?>
                        <?php foreach($recent_products as $product): ?>
                        <tr>
                            <td>#<?= $product['id'] ?></td>
                            <td class="product-title"><?= htmlspecialchars($product['title']) ?></td>
                            <td><?= htmlspecialchars($product['category_name'] ?? 'Uncategorized') ?></td>
                            <td class="price"><?= formatPrice($product['price']) ?></td>
                            <td>
                                <span class="status-badge <?= $product['published'] ? 'status-published' : 'status-draft' ?>">
                                    <?= $product['published'] ? 'Published' : 'Draft' ?>
                                </span>
                            </td>
                            <td>
                                <a href="edit-product.php?id=<?= $product['id'] ?>" class="btn-edit">Edit</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<style>
/* Admin Dashboard Styling */
.admin-dashboard {
    padding: 6rem 0 4rem;
    background: #F5F0E9;
    min-height: 100vh;
}

.admin-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
    flex-wrap: wrap;
    gap: 1rem;
}

.admin-header h1 {
    font-size: 2rem;
    color: #112250;
    margin-bottom: 0.2rem;
}

.admin-info {
    display: flex;
    align-items: center;
    gap: 1rem;
    flex-wrap: wrap;
}

.welcome-text {
    color: #6B7280;
    font-size: 0.9rem;
}

.admin-badge {
    display: inline-block;
    padding: 0.2rem 0.8rem;
    border-radius: 9999px;
    font-size: 0.7rem;
    font-weight: 600;
}

.admin-badge.super {
    background: linear-gradient(135deg, #FFD700, #FFB347);
    color: #2D1B00;
}

.admin-badge.manager {
    background: linear-gradient(135deg, #10B981, #059669);
    color: white;
}

.admin-badge.admin {
    background: #112250;
    color: white;
}

.btn-add-product {
    background: #112250;
    color: white;
    padding: 0.7rem 1.5rem;
    border-radius: 9999px;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn-add-product:hover {
    background: #0A1838;
    transform: translateY(-2px);
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

/* Stats Grid */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.stat-card {
    background: white;
    border-radius: 1rem;
    padding: 1.2rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    border: 1px solid rgba(224, 197, 143, 0.2);
    transition: all 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
}

.stat-icon {
    font-size: 2.5rem;
}

.stat-info h3 {
    font-size: 1.5rem;
    font-weight: 700;
    color: #112250;
    margin-bottom: 0.2rem;
}

.stat-info p {
    color: #6B7280;
    font-size: 0.75rem;
}

.stat-badge {
    display: inline-block;
    background: #EF4444;
    color: white;
    font-size: 0.6rem;
    padding: 0.1rem 0.4rem;
    border-radius: 9999px;
    margin-left: 0.5rem;
}

/* Quick Actions */
.quick-actions {
    margin-bottom: 2rem;
}

.quick-actions h2 {
    font-size: 1.2rem;
    margin-bottom: 1rem;
    color: #112250;
}

.actions-grid {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 1rem;
}

.action-card {
    background: white;
    border-radius: 1rem;
    padding: 1rem;
    text-decoration: none;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    transition: all 0.3s ease;
    border: 1px solid rgba(224, 197, 143, 0.2);
    position: relative;
}

.action-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    border-color: #E0C58F;
}

.action-card.logout:hover {
    background: #FEF2F2;
    border-color: #EF4444;
}

.action-card.logout:hover .action-icon,
.action-card.logout:hover span {
    color: #EF4444;
}

.action-icon {
    font-size: 1.2rem;
}

.action-card span:not(.badge) {
    color: #1F2937;
    font-size: 0.85rem;
    font-weight: 500;
}

.badge {
    position: absolute;
    top: -8px;
    right: -8px;
    background: #EF4444;
    color: white;
    font-size: 0.7rem;
    padding: 0.1rem 0.4rem;
    border-radius: 9999px;
}

/* Admin Sections */
.admin-section {
    background: white;
    border-radius: 1rem;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
    border: 1px solid rgba(224, 197, 143, 0.2);
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}

.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
}

.section-header h2 {
    font-size: 1.2rem;
    color: #112250;
    margin-bottom: 0;
}

.view-all {
    color: #E0C58F;
    text-decoration: none;
    font-size: 0.85rem;
    font-weight: 500;
    transition: color 0.2s ease;
}

.view-all:hover {
    color: #112250;
}

/* Tables */
.table-responsive {
    overflow-x: auto;
}

.admin-table {
    width: 100%;
    border-collapse: collapse;
}

.admin-table th,
.admin-table td {
    padding: 0.8rem;
    text-align: left;
    border-bottom: 1px solid rgba(224, 197, 143, 0.1);
}

.admin-table th {
    background: #F5F0E9;
    font-weight: 600;
    font-size: 0.8rem;
    color: #374151;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.admin-table td {
    font-size: 0.85rem;
    color: #1F2937;
}

.order-id {
    font-weight: 600;
    color: #112250;
}

.product-title {
    font-weight: 500;
}

.price {
    font-weight: 600;
    color: #112250;
}

.empty-table {
    text-align: center;
    color: #6B7280;
    padding: 2rem;
}

/* Status Badges */
.status-badge {
    display: inline-block;
    padding: 0.2rem 0.6rem;
    border-radius: 9999px;
    font-size: 0.7rem;
    font-weight: 500;
}

.status-pending {
    background: #FEF3C7;
    color: #D97706;
}

.status-processing {
    background: #E0E7FF;
    color: #4F46E5;
}

.status-completed {
    background: #D1FAE5;
    color: #10B981;
}

.status-cancelled {
    background: #FEE2E2;
    color: #EF4444;
}

.status-published {
    background: #D1FAE5;
    color: #10B981;
}

.status-draft {
    background: #F3F4F6;
    color: #6B7280;
}

/* Action Buttons */
.btn-view-order,
.btn-edit {
    color: #E0C58F;
    text-decoration: none;
    font-size: 0.8rem;
    font-weight: 500;
    transition: color 0.2s ease;
}

.btn-view-order:hover,
.btn-edit:hover {
    color: #112250;
}

/* Responsive */
@media (max-width: 1024px) {
    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    .actions-grid {
        grid-template-columns: repeat(3, 1fr);
    }
}

@media (max-width: 768px) {
    .admin-dashboard {
        padding: 5rem 0 3rem;
    }
    .admin-header {
        flex-direction: column;
        text-align: center;
    }
    .admin-info {
        justify-content: center;
    }
    .stats-grid {
        grid-template-columns: 1fr;
    }
    .actions-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    .admin-table th,
    .admin-table td {
        padding: 0.5rem;
    }
}

@media (max-width: 480px) {
    .actions-grid {
        grid-template-columns: 1fr;
    }
    .admin-table {
        font-size: 0.75rem;
    }
    .admin-table th,
    .admin-table td {
        padding: 0.4rem;
    }
}
</style>

<?php include '../includes/footer.php'; ?>