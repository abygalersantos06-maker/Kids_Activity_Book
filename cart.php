<?php
// cart.php - Shopping cart page
require_once 'includes/db_connect.php';
require_once 'includes/functions.php';

// Handle add to cart
if (isset($_GET['add'])) {
    $id = (int)$_GET['add'];
    $qty = isset($_GET['qty']) ? (int)$_GET['qty'] : 1;
    
    // Validate quantity
    if ($qty < 1) $qty = 1;
    if ($qty > 10) $qty = 10;
    
    $found = false;
    foreach ($_SESSION['cart'] as &$item) {
        if ($item['id'] == $id) {
            $item['quantity'] += $qty;
            // Cap at 10
            if ($item['quantity'] > 10) $item['quantity'] = 10;
            $found = true;
            break;
        }
    }
    
    if (!$found) {
        $_SESSION['cart'][] = ['id' => $id, 'quantity' => $qty];
    }
    
    header('Location: cart.php');
    exit;
}

// Handle remove from cart
if (isset($_GET['remove'])) {
    $id = (int)$_GET['remove'];
    foreach ($_SESSION['cart'] as $key => $item) {
        if ($item['id'] == $id) {
            unset($_SESSION['cart'][$key]);
            // Reindex array
            $_SESSION['cart'] = array_values($_SESSION['cart']);
            break;
        }
    }
    header('Location: cart.php');
    exit;
}

// Handle update quantities
if (isset($_POST['update'])) {
    if (isset($_POST['quantity']) && is_array($_POST['quantity'])) {
        foreach ($_POST['quantity'] as $key => $qty) {
            if (isset($_SESSION['cart'][$key])) {
                $qty = (int)$qty;
                // Validate quantity
                if ($qty < 1) $qty = 1;
                if ($qty > 10) $qty = 10;
                $_SESSION['cart'][$key]['quantity'] = $qty;
            }
        }
    }
    header('Location: cart.php');
    exit;
}

// Handle clear cart
if (isset($_GET['clear'])) {
    $_SESSION['cart'] = [];
    header('Location: cart.php');
    exit;
}

// Get cart items with details
$cart_items = [];
$subtotal = 0;

if (!empty($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $stmt = $pdo->prepare("SELECT a.*, i.file as image_file, i.alt as image_alt FROM article a LEFT JOIN image i ON a.image_id = i.id WHERE a.id = ?");
        $stmt->execute([$item['id']]);
        $product = $stmt->fetch();
        
        if ($product) {
            $price = getProductPrice($pdo, $product['id']);
            $product['price'] = $price;
            $product['quantity'] = $item['quantity'];
            $product['total'] = $price * $item['quantity'];
            $cart_items[] = $product;
            $subtotal += $product['total'];
        }
    }
}

// Calculate shipping (free for orders over ₱1500)
$shipping_fee = 0;
$free_shipping_threshold = 1500;
$shipping_message = '';
$shipping_needed = 0;

if ($subtotal >= $free_shipping_threshold) {
    $shipping_fee = 0;
    $shipping_message = 'Free Shipping';
} else {
    $shipping_fee = 99;
    $shipping_needed = $free_shipping_threshold - $subtotal;
    $shipping_message = '₱99';
}

$total = $subtotal + $shipping_fee;

$page_title = 'Shopping Cart';
include 'includes/header.php';
include 'includes/navigation.php';
?>

<main class="cart-page">
    <div class="container">
        <h1 class="page-title">Shopping Cart</h1>

        <?php if(empty($cart_items)): ?>
            <div class="empty-cart">
                <div class="empty-icon">🛒</div>
                <h2>Your cart is empty</h2>
                <p>Looks like you haven't added any books yet</p>
                <a href="index.php" class="btn btn-primary">Browse Books</a>
            </div>
        <?php else: ?>
            <div class="cart-container">
                <form method="POST" action="cart.php" class="cart-items-form">
                    <div class="cart-items">
                        <div class="cart-header">
                            <div class="header-product">Product</div>
                            <div class="header-price">Price</div>
                            <div class="header-quantity">Quantity</div>
                            <div class="header-total">Total</div>
                            <div class="header-remove"></div>
                        </div>
                        
                        <?php foreach($cart_items as $key => $item): ?>
                        <div class="cart-item">
                            <div class="item-product">
                                <div class="item-image">
                                    <?php if (!empty($item['image_file'])): ?>
                                        <img src="images/<?php echo htmlspecialchars($item['image_file']); ?>" 
                                             alt="<?php echo htmlspecialchars($item['image_alt'] ?? $item['title']); ?>">
                                    <?php else: ?>
                                        <div class="image-placeholder">📖</div>
                                    <?php endif; ?>
                                </div>
                                <div class="item-details">
                                    <h3><a href="product.php?id=<?php echo $item['id']; ?>"><?php echo htmlspecialchars($item['title']); ?></a></h3>
                                    <p class="item-summary"><?php echo htmlspecialchars(truncateText($item['summary'] ?? '', 60)); ?></p>
                                </div>
                            </div>
                            <div class="item-price"><?php echo formatPrice($item['price']); ?></div>
                            <div class="item-quantity">
                                <input type="number" 
                                       name="quantity[<?php echo $key; ?>]" 
                                       value="<?php echo $item['quantity']; ?>" 
                                       min="1" 
                                       max="10" 
                                       class="quantity-input">
                            </div>
                            <div class="item-total"><?php echo formatPrice($item['total']); ?></div>
                            <div class="item-remove">
                                <a href="cart.php?remove=<?php echo $item['id']; ?>" class="remove-link" onclick="return confirm('Remove this item from your cart?')">🗑️</a>
                            </div>
                        </div>
                        <?php endforeach; ?>
                        
                        <div class="cart-actions">
                            <button type="submit" name="update" class="btn-update">Update Cart</button>
                            <a href="cart.php?clear" class="btn-clear" onclick="return confirm('Clear all items from your cart?')">Clear Cart</a>
                        </div>
                    </div>
                </form>

                <!-- In cart.php, replace the cart-summary section with this -->
                <div class="cart-summary">
                    <h2>Order Summary</h2>
                    
                    <!-- Free Shipping Notice - Now properly positioned -->
                    <?php if($subtotal < $free_shipping_threshold): ?>
                    <div class="free-shipping-notice">
                        <span>🎁</span>
                        <span>Add ₱<?php echo number_format($shipping_needed, 2); ?> more to get FREE shipping!</span>
                    </div>
                    <?php else: ?>
                    <div class="free-shipping-notice">
                        <span>✨</span>
                        <span>Congratulations! You've earned FREE shipping!</span>
                    </div>
                    <?php endif; ?>
                    
                    <div class="summary-row">
                        <span>Subtotal</span>
                        <span><?php echo formatPrice($subtotal); ?></span>
                    </div>
                    <div class="summary-row">
                        <span>Shipping</span>
                        <span><?php echo $shipping_message; ?></span>
                    </div>
                    <div class="summary-row total">
                        <span>Total</span>
                        <span><?php echo formatPrice($total); ?></span>
                    </div>
                    
                    <a href="checkout.php" class="btn-checkout">Proceed to Checkout</a>
                    
                    <div class="payment-icons">
                        <span>💳 Credit Card</span>
                        <span>📱 GCash</span>
                        <span>🏦 PayMaya</span>
                        <span>💵 COD</span>
                    </div>
                    
                    <div class="continue-shopping">
                        <a href="index.php" class="continue-link">← Continue Shopping</a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</main>

<style>
/* Cart Page Styles */
.cart-page {
    padding: 2rem 0 4rem;
}

.page-title {
    font-family: 'Cormorant Garamond', serif;
    font-size: 2rem;
    color: var(--primary);
    margin-bottom: 2rem;
}

.empty-cart {
    text-align: center;
    padding: 4rem;
    background: var(--white);
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-sm);
}

.empty-icon {
    font-size: 4rem;
    margin-bottom: 1rem;
}

.empty-cart h2 {
    margin-bottom: 0.5rem;
    color: var(--primary);
}

.empty-cart p {
    color: var(--text-soft);
    margin-bottom: 1.5rem;
}

.cart-container {
    display: grid;
    grid-template-columns: 1fr 340px;
    gap: 2rem;
}

.cart-items {
    background: var(--white);
    border-radius: var(--radius-lg);
    overflow: hidden;
    border: 1px solid rgba(224, 197, 143, 0.2);
}

.cart-header {
    display: grid;
    grid-template-columns: 2.5fr 1fr 1fr 1fr 0.5fr;
    background: var(--bg-light);
    padding: 1rem 1.2rem;
    font-weight: 600;
    font-size: 0.85rem;
    color: var(--text-dark);
    border-bottom: 1px solid rgba(224, 197, 143, 0.2);
}

.cart-item {
    display: grid;
    grid-template-columns: 2.5fr 1fr 1fr 1fr 0.5fr;
    align-items: center;
    padding: 1.2rem;
    border-bottom: 1px solid rgba(224, 197, 143, 0.1);
    transition: var(--transition-fast);
}

.cart-item:hover {
    background: rgba(245, 240, 233, 0.3);
}

.item-product {
    display: flex;
    gap: 1rem;
    align-items: center;
}

.item-image {
    width: 70px;
    height: 70px;
    border-radius: var(--radius-sm);
    overflow: hidden;
    background: var(--bg-warm);
    flex-shrink: 0;
}

.item-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.image-placeholder {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    background: var(--bg-light);
}

.item-details h3 {
    font-size: 1rem;
    margin-bottom: 0.25rem;
}

.item-details h3 a {
    text-decoration: none;
    color: var(--primary);
    transition: var(--transition-fast);
}

.item-details h3 a:hover {
    color: var(--quicksand);
}

.item-summary {
    font-size: 0.7rem;
    color: var(--text-soft);
    line-height: 1.4;
}

.item-price,
.item-total {
    font-weight: 600;
    color: var(--primary);
}

.quantity-input {
    width: 70px;
    padding: 0.5rem;
    border: 1px solid rgba(224, 197, 143, 0.4);
    border-radius: var(--radius-sm);
    text-align: center;
    font-size: 0.85rem;
    background: var(--white);
}

.quantity-input:focus {
    outline: none;
    border-color: var(--quicksand);
}

.remove-link {
    color: #EF4444;
    text-decoration: none;
    font-size: 1.2rem;
    transition: var(--transition-fast);
    display: inline-block;
}

.remove-link:hover {
    transform: scale(1.1);
    color: #DC2626;
}

.cart-actions {
    padding: 1.2rem;
    display: flex;
    gap: 1rem;
    background: var(--bg-light);
    border-top: 1px solid rgba(224, 197, 143, 0.2);
}

.btn-update,
.btn-clear {
    padding: 0.6rem 1.2rem;
    border-radius: var(--radius-full);
    font-size: 0.8rem;
    cursor: pointer;
    font-weight: 500;
    transition: var(--transition-fast);
}

.btn-update {
    background: var(--primary);
    color: var(--white);
    border: none;
}

.btn-update:hover {
    background: var(--primary-dark);
    transform: translateY(-2px);
}

.btn-clear {
    background: transparent;
    border: 1px solid #EF4444;
    color: #EF4444;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
}

.btn-clear:hover {
    background: #EF4444;
    color: var(--white);
    transform: translateY(-2px);
}

.cart-summary {
    background: var(--white);
    border-radius: var(--radius-lg);
    padding: 1.5rem;
    border: 1px solid rgba(224, 197, 143, 0.2);
    position: sticky;
    top: 100px;
    height: fit-content;
}

.cart-summary h2 {
    font-size: 1.2rem;
    margin-bottom: 1rem;
    color: var(--primary);
}

.free-shipping-notice {
    background: linear-gradient(135deg, rgba(224, 197, 143, 0.15), rgba(224, 197, 143, 0.05));
    border-radius: var(--radius-sm);
    padding: 0.8rem;
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.8rem;
    color: var(--quicksand);
    border: 1px solid rgba(224, 197, 143, 0.3);
}

.summary-row {
    display: flex;
    justify-content: space-between;
    padding: 0.7rem 0;
    border-bottom: 1px solid rgba(224, 197, 143, 0.1);
    font-size: 0.9rem;
}

.summary-row.total {
    font-weight: 700;
    font-size: 1.1rem;
    color: var(--primary);
    border-bottom: none;
    margin-top: 0.5rem;
    padding-top: 0.7rem;
    border-top: 2px solid rgba(224, 197, 143, 0.3);
}

.btn-checkout {
    display: block;
    text-align: center;
    background: var(--primary);
    color: var(--white);
    padding: 0.9rem;
    border-radius: var(--radius-full);
    text-decoration: none;
    font-weight: 600;
    margin: 1rem 0;
    transition: var(--transition-fast);
}

.btn-checkout:hover {
    background: var(--primary-dark);
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
}

.payment-icons {
    display: flex;
    justify-content: center;
    gap: 1rem;
    margin: 1rem 0;
    font-size: 0.7rem;
    color: var(--text-soft);
}

.payment-icons span {
    background: var(--bg-light);
    padding: 0.3rem 0.7rem;
    border-radius: var(--radius-full);
}

.continue-shopping {
    text-align: center;
    margin-top: 1rem;
}

.continue-link {
    color: var(--text-soft);
    text-decoration: none;
    font-size: 0.85rem;
    transition: var(--transition-fast);
}

.continue-link:hover {
    color: var(--primary);
}

/* Responsive */
@media (max-width: 900px) {
    .cart-container {
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }
    
    .cart-summary {
        position: static;
    }
}

@media (max-width: 768px) {
    .cart-header {
        display: none;
    }
    
    .cart-item {
        grid-template-columns: 1fr;
        gap: 0.8rem;
        padding: 1.2rem;
    }
    
    .item-product {
        gap: 1rem;
    }
    
    .item-price,
    .item-quantity,
    .item-total,
    .item-remove {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.3rem 0;
    }
    
    .item-price::before {
        content: "Price:";
        font-weight: 600;
        color: var(--text-soft);
    }
    
    .item-quantity::before {
        content: "Quantity:";
        font-weight: 600;
        color: var(--text-soft);
    }
    
    .item-total::before {
        content: "Total:";
        font-weight: 600;
        color: var(--text-soft);
    }
    
    .item-remove {
        justify-content: flex-end;
        padding-top: 0.5rem;
    }
    
    .cart-actions {
        flex-direction: column;
    }
    
    .btn-update,
    .btn-clear {
        text-align: center;
        justify-content: center;
    }
}
</style>

<?php include 'includes/footer.php'; ?>