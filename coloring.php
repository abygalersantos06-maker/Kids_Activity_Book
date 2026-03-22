<?php
// coloring.php - Coloring books page with creative theme
require_once 'includes/db_connect.php';
require_once 'includes/functions.php';

// Initialize variables
$coloring_books = [];

try {
    // Get coloring books (category_id = 1)
    $stmt = $pdo->prepare("SELECT a.*, c.name as category_name, i.file as image_file, i.alt as image_alt 
                           FROM article a 
                           LEFT JOIN category c ON a.category_id = c.id 
                           LEFT JOIN image i ON a.image_id = i.id 
                           WHERE a.category_id = 1 AND a.published = 1 
                           ORDER BY a.created DESC");
    $stmt->execute();
    $coloring_books = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log("Error fetching coloring books: " . $e->getMessage());
    // Continue with empty array
}

// Get coloring tips
$tips = [
    ['title' => 'Color Outside the Lines', 'text' => 'Let creativity flow without boundaries! Every masterpiece starts with a brave first stroke.'],
    ['title' => 'Mix & Match Colors', 'text' => 'Purple dinosaurs? Green elephants? Why not! Encourage your child to explore their unique color combinations.'],
    ['title' => 'Tell a Story', 'text' => 'Each page can be a new adventure. Ask your child what\'s happening in their picture and let their imagination soar.']
];

// Set page variables
$section = 'coloring';
$title = 'Coloring Books - KidsBookery';
$description = 'Discover our magical collection of coloring books for kids. From jungle animals to space adventures, spark creativity and imagination.';

include 'includes/header.php';
include 'includes/navigation.php';
?>

<main>
    <!-- Hero Section - Creative Theme -->
    <section class="hero-section hero-coloring">
        <div class="hero-background">
            <div class="hero-star hero-star-1"></div>
            <div class="hero-star hero-star-2"></div>
            <div class="hero-star hero-star-3"></div>
            <div class="hero-star hero-star-4"></div>
            <div class="hero-star hero-star-5"></div>
            <div class="hero-orb hero-orb-1"></div>
            <div class="hero-orb hero-orb-2"></div>
        </div>
        
        <div class="container">
            <div class="hero-content">
                <span class="hero-badge">🎨 Coloring Books</span>
                <h1>Bring stories to <span>life with color</span></h1>
                <p>From jungle animals to space adventures, our coloring books spark creativity and imagination. Each page is a new canvas for your little artist.</p>
                
                <div class="hero-stats">
                    <div class="stat">
                        <span class="stat-number">50+</span>
                        <span class="stat-label">PAGES</span>
                    </div>
                    <div class="stat">
                        <span class="stat-number">3-8</span>
                        <span class="stat-label">AGE RANGE</span>
                    </div>
                    <div class="stat">
                        <span class="stat-number">100+</span>
                        <span class="stat-label">DESIGNS</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Tips Section -->
    <section class="tips-section">
        <div class="container">
            <h2 class="section-title">Creative tips</h2>
            <div class="tips-grid">
                <?php foreach($tips as $tip): ?>
                <div class="tip-card">
                    <h3><?php echo htmlspecialchars($tip['title']); ?></h3>
                    <p><?php echo htmlspecialchars($tip['text']); ?></p>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Products Grid -->
    <section class="products-section">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">All Coloring Books</h2>
                <span class="product-count"><?php echo count($coloring_books); ?> items</span>
            </div>

            <?php if (empty($coloring_books)): ?>
                <div class="no-products-message">
                    <p>No coloring books available at the moment. Please check back later!</p>
                </div>
            <?php else: ?>
                <div class="products-grid">
                    <?php foreach($coloring_books as $book): 
                        // Get product price
                        $price = getProductPrice($pdo, $book['id']);
                        // Use actual image file
                        $image_file = !empty($book['image_file']) ? $book['image_file'] : 'placeholder.jpg';
                    ?>
                    <div class="product-card" onclick="window.location.href='product.php?id=<?php echo $book['id']; ?>'">
                        <div class="product-image">
                            <img src="images/<?php echo htmlspecialchars($image_file); ?>" 
                                 alt="<?php echo htmlspecialchars($book['image_alt'] ?? $book['title']); ?>">
                            <span class="product-category"><?php echo htmlspecialchars($book['category_name'] ?? 'Coloring'); ?></span>
                        </div>
                        <div class="product-info">
                            <h3><?php echo htmlspecialchars($book['title']); ?></h3>
                            <p class="product-summary"><?php echo htmlspecialchars(truncateText($book['summary'] ?? '', 60)); ?></p>
                            <div class="product-meta">
                                <!-- FIXED: Changed formatPricePHP to formatPrice -->
                                <span class="product-price"><?php echo formatPrice($price); ?></span>
                                <span class="product-rating">★★★★★</span>
                            </div>
                            <button class="btn-add" onclick="event.stopPropagation(); window.location.href='cart.php?add=<?php echo $book['id']; ?>&qty=1'">Add to Cart</button>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Art Gallery Preview -->
    <section class="gallery-preview">
        <div class="container">
            <h2 class="section-title">Inspiration gallery</h2>
            <div class="gallery-grid">
                <div class="gallery-item">🦁</div>
                <div class="gallery-item">🐘</div>
                <div class="gallery-item">🦒</div>
                <div class="gallery-item">🐠</div>
                <div class="gallery-item">🦋</div>
                <div class="gallery-item">🌺</div>
            </div>
        </div>
    </section>
</main>

<style>
/* Coloring Page Specific Styles */
.hero-coloring {
    background: linear-gradient(135deg, var(--royal-blue) 0%, var(--sapphire) 100%);
    position: relative;
    min-height: 70vh;
    display: flex;
    align-items: center;
}

.tips-section {
    padding: 4rem 0;
    background: var(--bg-warm);
}

.tips-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 1.5rem;
}

.tip-card {
    background: var(--white);
    border-radius: var(--radius-lg);
    padding: 1.5rem;
    text-align: center;
    box-shadow: var(--shadow-sm);
    transition: var(--transition-base);
    border: 1px solid rgba(224, 197, 143, 0.2);
}

.tip-card:hover {
    transform: translateY(-5px);
    box-shadow: var(--shadow-md);
}

.tip-card h3 {
    color: var(--primary);
    margin-bottom: 0.8rem;
    font-size: 1.1rem;
}

.tip-card p {
    color: var(--text-soft);
    font-size: 0.85rem;
    line-height: 1.6;
}

.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
    flex-wrap: wrap;
    gap: 1rem;
}

.product-count {
    background: var(--bg-light);
    padding: 0.3rem 1rem;
    border-radius: var(--radius-full);
    font-size: 0.8rem;
    color: var(--text-soft);
}

.no-products-message {
    text-align: center;
    padding: 3rem;
    background: var(--white);
    border-radius: var(--radius-lg);
    border: 1px solid rgba(224, 197, 143, 0.2);
}

.no-products-message p {
    color: var(--text-soft);
    font-size: 1rem;
}

.gallery-preview {
    padding: 4rem 0;
    background: var(--bg-light);
}

.gallery-grid {
    display: grid;
    grid-template-columns: repeat(6, 1fr);
    gap: 1rem;
    text-align: center;
}

.gallery-item {
    background: var(--white);
    border-radius: var(--radius-lg);
    padding: 1.5rem;
    font-size: 2rem;
    transition: var(--transition-base);
    cursor: pointer;
    border: 1px solid rgba(224, 197, 143, 0.2);
}

.gallery-item:hover {
    transform: translateY(-5px) scale(1.05);
    box-shadow: var(--shadow-md);
    border-color: var(--quicksand);
}

/* Hero Stars and Orbs */
.hero-star {
    position: absolute;
    background: var(--quicksand);
    border-radius: 50%;
    opacity: 0.5;
    animation: twinkle 3s ease-in-out infinite;
}

.hero-star-1 { width: 3px; height: 3px; top: 20%; left: 15%; }
.hero-star-2 { width: 2px; height: 2px; top: 40%; right: 20%; animation-delay: 1s; }
.hero-star-3 { width: 4px; height: 4px; bottom: 30%; left: 25%; animation-delay: 2s; }
.hero-star-4 { width: 2px; height: 2px; top: 60%; right: 35%; animation-delay: 0.5s; }
.hero-star-5 { width: 3px; height: 3px; bottom: 20%; right: 45%; animation-delay: 1.5s; }

.hero-orb {
    position: absolute;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(224, 197, 143, 0.15), transparent);
    animation: floatOrb 8s ease-in-out infinite;
}

.hero-orb-1 { width: 200px; height: 200px; top: -50px; right: -50px; }
.hero-orb-2 { width: 150px; height: 150px; bottom: -30px; left: -30px; animation-delay: 2s; }

@keyframes twinkle {
    0%, 100% { opacity: 0.2; transform: scale(1); }
    50% { opacity: 0.8; transform: scale(1.2); }
}

@keyframes floatOrb {
    0%, 100% { transform: translate(0, 0) scale(1); }
    50% { transform: translate(20px, -20px) scale(1.05); }
}

@media (max-width: 768px) {
    .gallery-grid {
        grid-template-columns: repeat(3, 1fr);
    }
    
    .section-header {
        flex-direction: column;
        text-align: center;
    }
    
    .hero-stats {
        flex-wrap: wrap;
        justify-content: center;
    }
}

@media (max-width: 480px) {
    .gallery-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .tips-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<?php include 'includes/footer.php'; ?>