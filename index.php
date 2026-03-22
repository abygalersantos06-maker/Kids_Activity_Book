<?php
// index.php - Homepage with discovery theme
require_once 'includes/db_connect.php';
require_once 'includes/functions.php';

// Initialize variables to prevent errors
$featured_books = [];
$categories = [];

try {
    // Get featured products
    $stmt = $pdo->query("SELECT a.*, c.name as category_name, i.file as image_file, i.alt as image_alt
                          FROM article a 
                          LEFT JOIN category c ON a.category_id = c.id 
                          LEFT JOIN image i ON a.image_id = i.id 
                          WHERE a.published = 1 
                          ORDER BY RAND() 
                          LIMIT 6");
    $featured_books = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log("Error fetching featured books: " . $e->getMessage());
    // Continue with empty array
}

try {
    // Get categories
    $cat_stmt = $pdo->query("SELECT * FROM category WHERE navigation = 1");
    $categories = $cat_stmt->fetchAll();
} catch (PDOException $e) {
    error_log("Error fetching categories: " . $e->getMessage());
    // Continue with empty array
}

// Get testimonials
$testimonials = [
    [
        'name' => 'Sarah Johnson', 
        'role' => 'Mother of 6-year-old', 
        'text' => 'My daughter asks for these books every night! The illustrations are beautiful and she learns so much.', 
        'rating' => 5, 
        'avatar' => '👩'
    ],
    [
        'name' => 'Michael Chen', 
        'role' => 'Kindergarten Teacher', 
        'text' => 'I use these in my classroom. The educational games are perfect for early learners and keep them engaged.', 
        'rating' => 5, 
        'avatar' => '👨‍🏫'
    ],
    [
        'name' => 'Emma Rodriguez', 
        'role' => 'Parent of twins', 
        'text' => 'Finally, books that keep my sons engaged without screens. Worth every penny!', 
        'rating' => 5, 
        'avatar' => '👩‍👦‍👦'
    ],
    [
        'name' => 'David Kim', 
        'role' => 'Homeschooling Dad', 
        'text' => 'The variety of activities is amazing. My kids never get bored and they\'re learning so much.', 
        'rating' => 5, 
        'avatar' => '👨'
    ]
];

// Check for logout success message
if (isset($_GET['logout']) && $_GET['logout'] === 'success') {
    echo '<div class="alert alert-success" style="position: fixed; top: 100px; right: 20px; z-index: 1000; max-width: 300px;">
            <span>👋</span> You have been logged out successfully!
          </div>';
    echo '<script>setTimeout(function(){ document.querySelector(".alert-success").style.display = "none"; }, 3000);</script>';
}

// Set page variables
$section = '';
$title = 'KidsBookery - Where imagination takes flight';
$description = 'Discover enchanting activity books that turn ordinary moments into extraordinary adventures. Coloring, puzzles, games & printables for curious minds.';

include 'includes/header.php';
include 'includes/navigation.php';
?>

<main>
    <!-- Hero Section - Discovery Theme -->
    <section class="hero-section hero-home">
        <div class="hero-background">
            <div class="hero-orb hero-orb-1"></div>
            <div class="hero-orb hero-orb-2"></div>
            <div class="hero-orb hero-orb-3"></div>
            <div class="hero-star hero-star-1"></div>
            <div class="hero-star hero-star-2"></div>
            <div class="hero-star hero-star-3"></div>
            <div class="hero-star hero-star-4"></div>
            <div class="hero-star hero-star-5"></div>
            <div class="hero-circle hero-circle-1"></div>
            <div class="hero-circle hero-circle-2"></div>
        </div>
        
        <div class="container">
            <div class="hero-content">
                <span class="hero-badge">Welcome to KidsBookery</span>
                <h1>Where imagination <span>takes flight</span></h1>
                <p>Discover 24 enchanting activity books that turn ordinary moments into extraordinary adventures. Coloring, puzzles, games & printables for curious minds.</p>
                
                <div class="hero-stats">
                    <div class="stat">
                        <span class="stat-number">24</span>
                        <span class="stat-label">ACTIVITY BOOKS</span>
                    </div>
                    <div class="stat">
                        <span class="stat-number">10k+</span>
                        <span class="stat-label">HAPPY READERS</span>
                    </div>
                    <div class="stat">
                        <span class="stat-number">4.9</span>
                        <span class="stat-label">STAR RATING</span>
                    </div>
                </div>
                
                <div class="hero-cta">
                    <a href="#featured-books" class="btn btn-primary">Explore Collection</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Books Section -->
    <section id="featured-books" class="featured-section">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Featured Books</h2>
                <a href="coloring.php" class="view-all">View All →</a>
            </div>

            <div class="products-grid">
                <?php if (empty($featured_books)): ?>
                    <div class="no-products-message" style="grid-column: 1/-1; text-align: center; padding: 40px;">
                        <p>No featured books available at the moment. Please check back later!</p>
                    </div>
                <?php else: ?>
                    <?php foreach($featured_books as $book): 
                        // Get product price using function from functions.php
                        $price = getProductPrice($pdo, $book['id']);
                        // Use proper image path
                        $image_file = !empty($book['image_file']) ? $book['image_file'] : 'placeholder.jpg';
                    ?>
                    <div class="product-card" onclick="window.location.href='product.php?id=<?php echo $book['id']; ?>'">
                        <div class="product-image">
                            <img src="images/<?php echo htmlspecialchars($image_file); ?>" 
                                 alt="<?php echo htmlspecialchars($book['image_alt'] ?? $book['title']); ?>">
                            <span class="product-category"><?php echo htmlspecialchars($book['category_name'] ?? 'Uncategorized'); ?></span>
                        </div>
                        <div class="product-info">
                            <h3><?php echo htmlspecialchars($book['title']); ?></h3>
                            <p class="product-summary"><?php echo htmlspecialchars(truncateText($book['summary'] ?? '', 60)); ?></p>
                            <div class="product-meta">
                                <span class="product-price"><?php echo formatPrice($price); ?></span>
                                <span class="product-rating">★★★★★</span>
                            </div>
                            <button class="btn-add" onclick="event.stopPropagation(); window.location.href='cart.php?add=<?php echo $book['id']; ?>'">Add to Cart</button>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Categories Showcase -->
    <section class="categories-showcase">
        <div class="container">
            <h2 class="section-title">Browse by category</h2>
            <div class="categories-grid">
                <?php if (empty($categories)): ?>
                    <!-- Fallback categories if database is empty -->
                    <?php 
                    $fallback_categories = [
                        ['id' => 1, 'name' => 'Coloring Books', 'description' => 'Creative and fun coloring books for kids'],
                        ['id' => 2, 'name' => 'Puzzle Books', 'description' => 'Engaging puzzles and brain teasers'],
                        ['id' => 3, 'name' => 'Educational Games', 'description' => 'Learning games for young children'],
                        ['id' => 4, 'name' => 'Printables', 'description' => 'Instant download activity sheets and worksheets']
                    ];
                    foreach($fallback_categories as $cat): 
                        $icon = getCategoryIcon($cat['id']);
                        $link = getCategoryLink($cat['id']);
                    ?>
                    <a href="<?php echo $link; ?>" class="category-card">
                        <div class="category-icon"><?php echo $icon; ?></div>
                        <h3><?php echo htmlspecialchars($cat['name']); ?></h3>
                        <p><?php echo htmlspecialchars($cat['description']); ?></p>
                        <span class="category-link">Explore →</span>
                    </a>
                    <?php endforeach; ?>
                <?php else: ?>
                    <?php foreach($categories as $cat): 
                        $icon = getCategoryIcon($cat['id']);
                        $link = getCategoryLink($cat['id']);
                    ?>
                    <a href="<?php echo $link; ?>" class="category-card">
                        <div class="category-icon"><?php echo $icon; ?></div>
                        <h3><?php echo htmlspecialchars($cat['name']); ?></h3>
                        <p><?php echo htmlspecialchars($cat['description']); ?></p>
                        <span class="category-link">Explore →</span>
                    </a>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section class="testimonials-section">
        <div class="container">
            <h2 class="section-title">What parents say</h2>
            <div class="testimonials-grid">
                <?php foreach($testimonials as $testimonial): ?>
                <div class="testimonial-card">
                    <div class="testimonial-avatar"><?php echo $testimonial['avatar']; ?></div>
                    <div class="testimonial-stars">★★★★★</div>
                    <p class="testimonial-text">"<?php echo htmlspecialchars($testimonial['text']); ?>"</p>
                    <div class="testimonial-author">
                        <strong><?php echo htmlspecialchars($testimonial['name']); ?></strong>
                        <span><?php echo htmlspecialchars($testimonial['role']); ?></span>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Newsletter Section - FIXED -->
    <section class="newsletter-section">
        <div class="container">
            <div class="newsletter-content">
                <div class="newsletter-icon">✉️</div>
                <h2>Join the adventure</h2>
                <p>Subscribe for 20% off your first order and weekly activity ideas</p>
                <form class="newsletter-form" method="post" action="subscribe.php">
                    <input type="email" name="email" placeholder="Your email address" required>
                    <button type="submit">Subscribe →</button>
                </form>
                <p class="newsletter-note">No spam, unsubscribe anytime.</p>
            </div>
        </div>
    </section>
</main>

<style>
/* Newsletter Section Styling */
.newsletter-section {
    background: linear-gradient(135deg, #112250 0%, #3C507D 100%);
    padding: 4rem 0;
    margin: 2rem 0 0;
    position: relative;
    overflow: hidden;
}

.newsletter-section::before {
    content: '✨';
    position: absolute;
    font-size: 15rem;
    opacity: 0.05;
    bottom: -3rem;
    right: -3rem;
    pointer-events: none;
    animation: float 20s ease-in-out infinite;
}

.newsletter-section::after {
    content: '📚';
    position: absolute;
    font-size: 12rem;
    opacity: 0.05;
    top: -2rem;
    left: -2rem;
    pointer-events: none;
    animation: float 15s ease-in-out infinite reverse;
}

@keyframes float {
    0%, 100% { transform: translate(0, 0) rotate(0deg); }
    50% { transform: translate(30px, -20px) rotate(10deg); }
}

.newsletter-content {
    max-width: 600px;
    margin: 0 auto;
    text-align: center;
    position: relative;
    z-index: 2;
}

.newsletter-icon {
    font-size: 3rem;
    margin-bottom: 1rem;
    animation: bounce 2s ease-in-out infinite;
}

@keyframes bounce {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-10px); }
}

.newsletter-content h2 {
    font-family: 'Cormorant Garamond', serif;
    font-size: 2rem;
    color: #FFFFFF;
    margin-bottom: 0.5rem;
}

.newsletter-content p {
    color: rgba(255, 255, 255, 0.8);
    font-size: 1rem;
    margin-bottom: 1.5rem;
}

.newsletter-form {
    display: flex;
    gap: 0.5rem;
    max-width: 500px;
    margin: 0 auto;
}

.newsletter-form input {
    flex: 1;
    padding: 0.9rem 1.2rem;
    border: none;
    border-radius: 50px;
    font-size: 0.9rem;
    background: rgba(255, 255, 255, 0.95);
    transition: all 0.3s ease;
}

.newsletter-form input:focus {
    outline: none;
    background: #FFFFFF;
    box-shadow: 0 0 0 3px rgba(224, 197, 143, 0.3);
}

.newsletter-form button {
    padding: 0.9rem 1.8rem;
    background: #E0C58F;
    color: #112250;
    border: none;
    border-radius: 50px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    font-size: 0.9rem;
}

.newsletter-form button:hover {
    background: #F5E6D0;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
}

.newsletter-note {
    font-size: 0.7rem;
    margin-top: 1rem;
    opacity: 0.6;
}

/* Responsive Newsletter */
@media (max-width: 768px) {
    .newsletter-section {
        padding: 3rem 1rem;
    }
    
    .newsletter-content h2 {
        font-size: 1.6rem;
    }
    
    .newsletter-form {
        flex-direction: column;
        gap: 0.8rem;
        padding: 0 1rem;
    }
    
    .newsletter-form input,
    .newsletter-form button {
        width: 100%;
        padding: 0.8rem;
    }
    
    .newsletter-icon {
        font-size: 2.5rem;
    }
}

@media (max-width: 480px) {
    .newsletter-content h2 {
        font-size: 1.4rem;
    }
    
    .newsletter-content p {
        font-size: 0.85rem;
    }
}

/* Testimonials Section Styling */
.testimonials-section {
    background: #F5F0E9;
    padding: 4rem 0;
}

.testimonials-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 1.5rem;
}

.testimonial-card {
    background: #FFFFFF;
    border-radius: 1rem;
    padding: 1.5rem;
    text-align: center;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    transition: all 0.3s ease;
    border: 1px solid rgba(224, 197, 143, 0.2);
}

.testimonial-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
}

.testimonial-avatar {
    width: 60px;
    height: 60px;
    background: linear-gradient(135deg, #E0C58F, #F5E6D0);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1rem;
    font-size: 2rem;
    border: 3px solid #E0C58F;
}

.testimonial-stars {
    color: #E0C58F;
    font-size: 1rem;
    margin-bottom: 0.8rem;
    letter-spacing: 2px;
}

.testimonial-text {
    font-size: 0.85rem;
    color: #4A5B6E;
    line-height: 1.6;
    margin-bottom: 1rem;
    font-style: italic;
}

.testimonial-author strong {
    display: block;
    color: #112250;
    font-size: 0.9rem;
    margin-bottom: 0.2rem;
}

.testimonial-author span {
    font-size: 0.7rem;
    color: #8B9AB0;
}

/* Categories Section */
.categories-showcase {
    padding: 4rem 0;
}

.categories-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
}

.category-card {
    background: #FFFFFF;
    border-radius: 1rem;
    padding: 1.8rem;
    text-align: center;
    text-decoration: none;
    transition: all 0.3s ease;
    border: 1px solid rgba(224, 197, 143, 0.2);
}

.category-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
    border-color: #E0C58F;
}

.category-icon {
    font-size: 2.5rem;
    margin-bottom: 1rem;
}

.category-card h3 {
    color: #112250;
    margin-bottom: 0.5rem;
    font-size: 1.2rem;
}

.category-card p {
    color: #4A5B6E;
    font-size: 0.8rem;
    margin-bottom: 1rem;
    line-height: 1.5;
}

.category-link {
    color: #E0C58F;
    font-weight: 600;
    font-size: 0.85rem;
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
    transition: gap 0.3s ease;
}

.category-card:hover .category-link {
    gap: 0.6rem;
}
</style>

<?php include 'includes/footer.php'; ?>