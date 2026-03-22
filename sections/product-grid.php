<?php
// sections/product-grid.php - Books grid display
$current_category = getCurrentCategory();
$filtered_products = getProductsByCategory($products, $current_category);
$product_count = count($filtered_products);
?>

<!-- Section Title -->
<div class="section-title">
    <span>📚 Featured Books</span>
    <span style="font-size: 14px; color: #666;">
        (<?php echo $current_category == 'all' ? 'All 24 books' : $current_category . ' - ' . $product_count . ' items'; ?>)
    </span>
</div>

<!-- Books Grid -->
<div class="books-grid">
    <?php if(empty($filtered_products)): ?>
        <p style="grid-column: 1/-1; text-align: center; padding: 50px;">
            No books found in this category
        </p>
    <?php else: ?>
        <?php foreach($filtered_products as $book): ?>
            <div class="book-card">
                <div class="book-emoji"><?php echo $book['emoji']; ?></div>
                <span class="age-badge"><?php echo e($book['age']); ?> yrs</span>
                <div class="book-info">
                    <h3><?php echo e($book['title']); ?></h3>
                    <p class="book-category"><?php echo e($book['category']); ?></p>
                    <p class="book-desc"><?php echo e($book['desc']); ?></p>
                    <p class="book-price"><?php echo formatPrice($book['price']); ?></p>
                    <a href="?add=<?php echo $book['id']; ?>" class="add-btn">➕ Add to Cart</a>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>