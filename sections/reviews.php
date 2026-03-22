<?php
// sections/reviews.php - Customer reviews section
?>
<div class="section-title">💬 Happy Customers</div>
<div class="reviews-grid">
    <?php foreach($reviews as $review): ?>
    <div class="review-card">
        <div class="review-name"><?php echo e($review['name']); ?></div>
        <div class="review-stars"><?php echo getStarRating($review['rating']); ?></div>
        <p class="review-text">"<?php echo e($review['text']); ?>"</p>
    </div>
    <?php endforeach; ?>
</div>