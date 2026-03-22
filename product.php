<?php
// product.php - Product detail page
require_once 'includes/db_connect.php';
require_once 'includes/functions.php';

// Get product ID from URL
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    header('Location: coloring.php');
    exit;
}

// Get product details
$product = getProductById($pdo, $id);

if (!$product) {
    header('Location: coloring.php');
    exit;
}

// Get product price
$price = getProductPrice($pdo, $id);

// Get related products
$related_products = getRelatedProducts($pdo, $product['category_id'], $id, 4);

// Set page variables
$title = $product['title'] . ' - KidsBookery';
$description = $product['summary'];

include 'includes/header.php';
include 'includes/navigation.php';
?>

<main class="product-detail-page">
    <div class="container">
        <!-- Breadcrumb -->
        <div class="breadcrumb">
            <a href="index.php">Home</a> &gt;
            <a href="<?php echo getCategoryLink($product['category_id']); ?>">
                <?php echo htmlspecialchars($product['category_name']); ?>
            </a> &gt;
            <span><?php echo htmlspecialchars($product['title']); ?></span>
        </div>

        <!-- Product Detail -->
        <div class="product-detail-container">
            <!-- Product Gallery -->
            <div class="product-gallery">
                <div class="main-image">
                    <img src="images/<?php echo !empty($product['image_file']) ? htmlspecialchars($product['image_file']) : 'placeholder.jpg'; ?>" 
                         alt="<?php echo htmlspecialchars($product['image_alt'] ?? $product['title']); ?>">
                </div>
            </div>

            <!-- Product Info -->
            <div class="product-info">
                <h1><?php echo htmlspecialchars($product['title']); ?></h1>
                
                <div class="product-rating-summary">
                    <div class="stars">★★★★★</div>
                    <span class="rating-count">(24 reviews)</span>
                </div>

                <div class="product-price-box">
                    <div class="price-container">
                        <span class="current-price"><?php echo formatPrice($price); ?></span>
                        <span class="price-badge">Free Shipping</span>
                    </div>
                    <p class="price-note">Instant PDF download after purchase</p>
                </div>

                <div class="product-description">
                    <h3>About this book</h3>
                    <p><?php echo nl2br(htmlspecialchars($product['content'])); ?></p>
                </div>

                <div class="product-metadata">
                    <div class="metadata-item">
                        <span class="metadata-label">Category</span>
                        <span class="metadata-value">
                            <a href="<?php echo getCategoryLink($product['category_id']); ?>">
                                <?php echo htmlspecialchars($product['category_name']); ?>
                            </a>
                        </span>
                    </div>
                    <div class="metadata-item">
                        <span class="metadata-label">Created by</span>
                        <span class="metadata-value">
                            <a href="member.php?id=<?php echo $product['member_id']; ?>">
                                <?php 
                                // Get member name using the function
                                $member_name = getMemberName($pdo, $product['member_id']);
                                echo htmlspecialchars($member_name);
                                ?>
                            </a>
                        </span>
                    </div>
                    <div class="metadata-item">
                        <span class="metadata-label">Published</span>
                        <span class="metadata-value"><?php echo formatDate($product['created']); ?></span>
                    </div>
                    <div class="metadata-item">
                        <span class="metadata-label">Format</span>
                        <span class="metadata-value">PDF Download</span>
                    </div>
                </div>

                <div class="product-actions">
                    <div class="quantity-selector">
                        <button class="qty-btn" type="button" onclick="decrementQty()">-</button>
                        <input type="number" id="quantity" value="1" min="1" max="10" readonly>
                        <button class="qty-btn" type="button" onclick="incrementQty()">+</button>
                    </div>
                    
                    <div class="action-buttons">
                        <a href="cart.php?add=<?php echo $id; ?>&qty=1" class="btn-add-to-cart" id="add-to-cart-btn">
                            <span>🛒</span> Add to Cart
                        </a>
                        <button class="btn-buy-now" type="button" onclick="buyNow()">
                            <span>⚡</span> Buy Now
                        </button>
                    </div>
                </div>

                <div class="product-features">
                    <div class="feature-item">
                        <span class="feature-icon">📥</span>
                        <div>
                            <strong>Instant Download</strong>
                            <p>Get it immediately after purchase</p>
                        </div>
                    </div>
                    <div class="feature-item">
                        <span class="feature-icon">🔄</span>
                        <div>
                            <strong>Print Unlimited</strong>
                            <p>Print as many copies as you need</p>
                        </div>
                    </div>
                    <div class="feature-item">
                        <span class="feature-icon">🎨</span>
                        <div>
                            <strong>Kid-Friendly</strong>
                            <p>Designed for ages 3-8</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Related Products -->
        <?php if (!empty($related_products)): ?>
        <section class="related-products">
            <h2 class="section-title">You might also like</h2>
            <div class="related-grid">
                <?php foreach($related_products as $related): 
                    $related_price = getProductPrice($pdo, $related['id']);
                ?>
                <div class="related-card" onclick="window.location.href='product.php?id=<?php echo $related['id']; ?>'">
                    <div class="related-image">
                        <img src="images/<?php echo !empty($related['image_file']) ? htmlspecialchars($related['image_file']) : 'placeholder.jpg'; ?>" 
                             alt="<?php echo htmlspecialchars($related['image_alt'] ?? $related['title']); ?>">
                    </div>
                    <div class="related-info">
                        <h4><?php echo htmlspecialchars(truncateText($related['title'], 40)); ?></h4>
                        <span class="related-price"><?php echo formatPrice($related_price); ?></span>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </section>
        <?php endif; ?>

        <!-- Reviews Section -->
        <section class="reviews-section">
            <div class="reviews-header">
                <h2>Customer Reviews</h2>
                <div class="reviews-summary">
                    <span class="average-rating">4.9</span>
                    <div class="stars">★★★★★</div>
                    <span>24 reviews</span>
                </div>
            </div>

            <div class="reviews-grid">
                <div class="review-card">
                    <div class="review-avatar">👩</div>
                    <div class="review-content">
                        <div class="review-header">
                            <span class="review-name">Sarah J.</span>
                            <span class="review-date">March 2026</span>
                        </div>
                        <div class="review-stars">★★★★★</div>
                        <p class="review-text">My daughter absolutely loves this book! The illustrations are beautiful and she spends hours coloring.</p>
                    </div>
                </div>
                
                <div class="review-card">
                    <div class="review-avatar">👨</div>
                    <div class="review-content">
                        <div class="review-header">
                            <span class="review-name">Michael C.</span>
                            <span class="review-date">February 2026</span>
                        </div>
                        <div class="review-stars">★★★★★</div>
                        <p class="review-text">Perfect for my kindergarten classroom. The activities are engaging and educational.</p>
                    </div>
                </div>
            </div>

            <!-- Write Review Form -->
            <div class="write-review">
                <h3>Write a Review</h3>
                <form class="review-form" onsubmit="event.preventDefault(); alert('Thank you for your review!');">
                    <textarea placeholder="Share your thoughts about this book..." rows="4"></textarea>
                    <div class="review-form-actions">
                        <select>
                            <option>5 ★</option>
                            <option>4 ★</option>
                            <option>3 ★</option>
                            <option>2 ★</option>
                            <option>1 ★</option>
                        </select>
                        <button type="submit" class="btn-submit-review">Submit Review</button>
                    </div>
                </form>
            </div>
        </section>
    </div>
</main>

<script>
function incrementQty() {
    let qty = document.getElementById('quantity');
    let value = parseInt(qty.value);
    if (value < 10) {
        qty.value = value + 1;
        updateAddToCartLink();
    }
}

function decrementQty() {
    let qty = document.getElementById('quantity');
    let value = parseInt(qty.value);
    if (value > 1) {
        qty.value = value - 1;
        updateAddToCartLink();
    }
}

function updateAddToCartLink() {
    let qty = document.getElementById('quantity').value;
    let addBtn = document.getElementById('add-to-cart-btn');
    addBtn.href = 'cart.php?add=<?php echo $id; ?>&qty=' + qty;
}

function buyNow() {
    let qty = document.getElementById('quantity').value;
    window.location.href = 'checkout.php?add=<?php echo $id; ?>&qty=' + qty;
}
</script>

<?php include 'includes/footer.php'; ?>