<?php
// checkout.php - Checkout page
require_once 'includes/db_connect.php';
require_once 'includes/functions.php';

// Redirect if cart is empty
if (empty($_SESSION['cart'])) {
    header('Location: index.php');
    exit;
}

// Calculate cart total for display
$cart_total = 0;
$cart_items = [];
$free_shipping_threshold = 1500;

foreach ($_SESSION['cart'] as $item) {
    $stmt = $pdo->prepare("SELECT * FROM article WHERE id = ?");
    $stmt->execute([$item['id']]);
    $product = $stmt->fetch();
    
    if ($product) {
        $price = getProductPrice($pdo, $product['id']);
        $item_total = $price * $item['quantity'];
        $cart_total += $item_total;
        
        $cart_items[] = [
            'product' => $product,
            'quantity' => $item['quantity'],
            'price' => $price,
            'total' => $item_total
        ];
    }
}

// Calculate shipping
if ($cart_total >= $free_shipping_threshold) {
    $shipping_fee = 0;
    $shipping_text = 'Free Shipping';
} else {
    $shipping_fee = 99;
    $shipping_text = '₱99';
}

$grand_total = $cart_total + $shipping_fee;

// Process checkout
$order_success = false;
$order_number = '';

if (isset($_POST['place_order'])) {
    // Validate form data
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $zip = trim($_POST['zip'] ?? '');
    $payment = $_POST['payment'] ?? 'card';
    
    // Basic validation
    $errors = [];
    if (empty($first_name)) $errors[] = 'First name is required';
    if (empty($last_name)) $errors[] = 'Last name is required';
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email is required';
    if (empty($address)) $errors[] = 'Address is required';
    if (empty($city)) $errors[] = 'City is required';
    if (empty($zip)) $errors[] = 'Zip code is required';
    
    if (empty($errors)) {
        // Save order to database
        try {
            $session_id = session_id();
            $user_id = isLoggedIn() ? getCurrentUserId() : null;
            
            $stmt = $pdo->prepare("
                INSERT INTO orders (member_id, user_id, session_id, total_amount, status, first_name, last_name, email, address, city, zip_code, payment_method) 
                VALUES (?, ?, ?, ?, 'pending', ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                null, $user_id, $session_id, $grand_total,
                $first_name, $last_name, $email, $address, $city, $zip, $payment
            ]);
            
            $order_id = $pdo->lastInsertId();
            $order_number = 'KB-' . date('Ymd') . '-' . str_pad($order_id, 4, '0', STR_PAD_LEFT);
            
            // Save order items
            foreach ($cart_items as $item) {
                $stmt = $pdo->prepare("
                    INSERT INTO order_items (order_id, article_id, quantity, price) 
                    VALUES (?, ?, ?, ?)
                ");
                $stmt->execute([$order_id, $item['product']['id'], $item['quantity'], $item['price']]);
            }
            
            // Clear cart
            $_SESSION['cart'] = [];
            $order_success = true;
            
        } catch (PDOException $e) {
            error_log("Checkout error: " . $e->getMessage());
            $error_message = 'An error occurred while processing your order. Please try again.';
        }
    } else {
        $error_message = implode('<br>', $errors);
    }
}

$page_title = 'Checkout';
include 'includes/header.php';
include 'includes/navigation.php';
?>

<main class="checkout-page">
    <div class="container">
        <?php if($order_success): ?>
            <div class="checkout-success">
                <div class="success-icon">✅</div>
                <h1>Thank You for Your Order!</h1>
                <p>Your order has been placed successfully. You'll receive a confirmation email shortly.</p>
                <div class="order-details">
                    <p><strong>Order #:</strong> <?php echo $order_number; ?></p>
                    <p><strong>Total:</strong> <?php echo formatPrice($grand_total); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($_POST['email'] ?? ''); ?></p>
                </div>
                <div class="success-actions">
                    <a href="index.php" class="btn btn-primary">Continue Shopping</a>
                </div>
            </div>
        <?php else: ?>
            <h1 class="page-title">Checkout</h1>
            
            <?php if (isset($error_message)): ?>
                <div class="error-message">
                    <p><?php echo $error_message; ?></p>
                </div>
            <?php endif; ?>
            
            <div class="checkout-container">
                <div class="checkout-form">
                    <h2>Shipping Information</h2>
                    <form method="POST" action="checkout.php" id="checkout-form">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="first_name">First Name *</label>
                                <input type="text" id="first_name" name="first_name" required 
                                       value="<?php echo htmlspecialchars($_POST['first_name'] ?? ''); ?>"
                                       placeholder="Enter first name">
                            </div>
                            <div class="form-group">
                                <label for="last_name">Last Name *</label>
                                <input type="text" id="last_name" name="last_name" required 
                                       value="<?php echo htmlspecialchars($_POST['last_name'] ?? ''); ?>"
                                       placeholder="Enter last name">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="email">Email Address *</label>
                            <input type="email" id="email" name="email" required 
                                   value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                                   placeholder="Enter email address">
                        </div>
                        
                        <div class="form-group">
                            <label for="address">Street Address *</label>
                            <input type="text" id="address" name="address" required 
                                   value="<?php echo htmlspecialchars($_POST['address'] ?? ''); ?>"
                                   placeholder="Street address">
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="city">City *</label>
                                <input type="text" id="city" name="city" required 
                                       value="<?php echo htmlspecialchars($_POST['city'] ?? ''); ?>"
                                       placeholder="City">
                            </div>
                            <div class="form-group">
                                <label for="zip">Zip Code *</label>
                                <input type="text" id="zip" name="zip" required 
                                       value="<?php echo htmlspecialchars($_POST['zip'] ?? ''); ?>"
                                       placeholder="Zip code">
                            </div>
                        </div>
                        
                        <h2>Payment Method</h2>
                        <div class="payment-methods">
                            <label class="payment-option">
                                <input type="radio" name="payment" value="card" checked>
                                <span class="payment-icon">💳</span>
                                <span class="payment-label">Credit Card</span>
                            </label>
                            <label class="payment-option">
                                <input type="radio" name="payment" value="gcash">
                                <span class="payment-icon">📱</span>
                                <span class="payment-label">GCash</span>
                            </label>
                            <label class="payment-option">
                                <input type="radio" name="payment" value="paymaya">
                                <span class="payment-icon">🏦</span>
                                <span class="payment-label">PayMaya</span>
                            </label>
                            <label class="payment-option">
                                <input type="radio" name="payment" value="cod">
                                <span class="payment-icon">💵</span>
                                <span class="payment-label">Cash on Delivery</span>
                            </label>
                        </div>
                        
                        <div class="form-actions">
                            <button type="submit" name="place_order" class="btn-place-order">Place Order</button>
                            <a href="cart.php" class="btn-back">← Back to Cart</a>
                        </div>
                    </form>
                </div>
                
                <div class="checkout-summary">
                    <h2>Your Order</h2>
                    
                    <div class="order-items">
                        <?php foreach ($cart_items as $item): ?>
                        <div class="order-item">
                            <div class="order-item-info">
                                <span class="order-item-title"><?php echo htmlspecialchars($item['product']['title']); ?></span>
                                <span class="order-item-qty">× <?php echo $item['quantity']; ?></span>
                            </div>
                            <span class="order-item-price"><?php echo formatPrice($item['total']); ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <div class="order-totals">
                        <div class="order-row">
                            <span>Subtotal</span>
                            <span><?php echo formatPrice($cart_total); ?></span>
                        </div>
                        <div class="order-row">
                            <span>Shipping</span>
                            <span><?php echo $shipping_text; ?></span>
                        </div>
                        <div class="order-row total">
                            <span>Total</span>
                            <span><?php echo formatPrice($grand_total); ?></span>
                        </div>
                    </div>
                    
                    <p class="secure-checkout">
                        <span>🔒</span> Secure Checkout - Your information is encrypted
                    </p>
                </div>
            </div>
        <?php endif; ?>
    </div>
</main>

<style>
/* Checkout Page Styles */
.checkout-page {
    padding: 2rem 0 4rem;
}

.page-title {
    font-family: 'Cormorant Garamond', serif;
    font-size: 2rem;
    color: var(--primary);
    margin-bottom: 2rem;
}

.error-message {
    background: #FEF3F2;
    border-left: 4px solid #EF4444;
    padding: 1rem;
    border-radius: var(--radius-sm);
    margin-bottom: 1.5rem;
    color: #DC2626;
    font-size: 0.85rem;
}

.checkout-container {
    display: grid;
    grid-template-columns: 1fr 380px;
    gap: 2rem;
}

.checkout-form {
    background: var(--white);
    border-radius: var(--radius-lg);
    padding: 1.8rem;
    border: 1px solid rgba(224, 197, 143, 0.2);
}

.checkout-form h2 {
    font-size: 1.2rem;
    margin: 1.5rem 0 1rem;
    color: var(--primary);
}

.checkout-form h2:first-of-type {
    margin-top: 0;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
}

.form-group {
    margin-bottom: 1rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.4rem;
    font-weight: 500;
    font-size: 0.8rem;
    color: var(--text-dark);
}

.form-group input {
    width: 100%;
    padding: 0.7rem 1rem;
    border: 1px solid rgba(224, 197, 143, 0.4);
    border-radius: var(--radius-sm);
    font-size: 0.85rem;
    transition: var(--transition-fast);
}

.form-group input:focus {
    outline: none;
    border-color: var(--quicksand);
    box-shadow: 0 0 0 3px rgba(224, 197, 143, 0.1);
}

.payment-methods {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 0.8rem;
    margin: 1rem 0 1.5rem;
}

.payment-option {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.7rem 1rem;
    background: var(--bg-light);
    border-radius: var(--radius-full);
    cursor: pointer;
    transition: var(--transition-fast);
    border: 1px solid transparent;
}

.payment-option:hover {
    background: rgba(224, 197, 143, 0.2);
}

.payment-option input:checked + .payment-icon + .payment-label {
    font-weight: 600;
}

.payment-option:has(input:checked) {
    border-color: var(--quicksand);
    background: rgba(224, 197, 143, 0.1);
}

.payment-icon {
    font-size: 1rem;
}

.payment-label {
    font-size: 0.8rem;
    color: var(--text-dark);
}

.form-actions {
    display: flex;
    gap: 1rem;
    margin-top: 1.5rem;
}

.btn-place-order {
    flex: 2;
    padding: 0.8rem;
    background: var(--primary);
    color: var(--white);
    border: none;
    border-radius: var(--radius-full);
    font-weight: 600;
    cursor: pointer;
    transition: var(--transition-fast);
}

.btn-place-order:hover {
    background: var(--primary-dark);
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
}

.btn-back {
    flex: 1;
    padding: 0.8rem;
    background: transparent;
    color: var(--text-soft);
    border: 1px solid rgba(224, 197, 143, 0.4);
    border-radius: var(--radius-full);
    text-decoration: none;
    text-align: center;
    font-size: 0.85rem;
    transition: var(--transition-fast);
}

.btn-back:hover {
    background: var(--bg-light);
    color: var(--primary);
}

.checkout-summary {
    background: var(--white);
    border-radius: var(--radius-lg);
    padding: 1.5rem;
    border: 1px solid rgba(224, 197, 143, 0.2);
    position: sticky;
    top: 100px;
    height: fit-content;
}

.checkout-summary h2 {
    font-size: 1.2rem;
    margin-bottom: 1rem;
    color: var(--primary);
}

.order-items {
    margin: 1rem 0;
    max-height: 300px;
    overflow-y: auto;
}

.order-item {
    display: flex;
    justify-content: space-between;
    padding: 0.7rem 0;
    border-bottom: 1px solid rgba(224, 197, 143, 0.1);
}

.order-item-title {
    font-size: 0.85rem;
    color: var(--text-dark);
}

.order-item-qty {
    font-size: 0.75rem;
    color: var(--text-soft);
    margin-left: 0.3rem;
}

.order-item-price {
    font-weight: 600;
    color: var(--primary);
    font-size: 0.85rem;
}

.order-totals {
    margin-top: 1rem;
    padding-top: 1rem;
    border-top: 1px solid rgba(224, 197, 143, 0.2);
}

.order-row {
    display: flex;
    justify-content: space-between;
    padding: 0.5rem 0;
    font-size: 0.85rem;
}

.order-row.total {
    font-weight: 700;
    font-size: 1rem;
    color: var(--primary);
    margin-top: 0.5rem;
    padding-top: 0.7rem;
    border-top: 2px solid rgba(224, 197, 143, 0.3);
}

.secure-checkout {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    margin-top: 1rem;
    padding-top: 1rem;
    border-top: 1px solid rgba(224, 197, 143, 0.2);
    font-size: 0.7rem;
    color: var(--text-soft);
}

.checkout-success {
    text-align: center;
    padding: 3rem;
    background: var(--white);
    border-radius: var(--radius-lg);
    border: 1px solid rgba(224, 197, 143, 0.2);
}

.success-icon {
    font-size: 4rem;
    margin-bottom: 1rem;
}

.checkout-success h1 {
    color: var(--primary);
    margin-bottom: 0.5rem;
}

.order-details {
    background: var(--bg-light);
    padding: 1rem;
    border-radius: var(--radius-sm);
    margin: 1.5rem 0;
    text-align: left;
}

.order-details p {
    margin: 0.3rem 0;
}

.success-actions {
    margin-top: 1.5rem;
}

/* Responsive */
@media (max-width: 900px) {
    .checkout-container {
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }
    
    .checkout-summary {
        position: static;
    }
}

@media (max-width: 640px) {
    .form-row {
        grid-template-columns: 1fr;
        gap: 0;
    }
    
    .payment-methods {
        grid-template-columns: 1fr 1fr;
    }
    
    .form-actions {
        flex-direction: column;
    }
    
    .btn-place-order,
    .btn-back {
        width: 100%;
    }
}
</style>

<?php include 'includes/footer.php'; ?>