<?php
// educational.php - Educational games page with learning theme
require_once 'includes/db_connect.php';
require_once 'includes/functions.php';

// Initialize variables
$educational_games = [];

try {
    // Get educational games (category_id = 3)
    $stmt = $pdo->prepare("SELECT a.*, c.name as category_name, i.file as image_file, i.alt as image_alt 
                           FROM article a 
                           LEFT JOIN category c ON a.category_id = c.id 
                           LEFT JOIN image i ON a.image_id = i.id 
                           WHERE a.category_id = 3 AND a.published = 1 
                           ORDER BY a.created DESC");
    $stmt->execute();
    $educational_games = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log("Error fetching educational games: " . $e->getMessage());
    // Continue with empty array
}

// Learning milestones
$milestones = [
    ['age' => '2-3', 'skills' => 'Colors, Shapes, Sounds', 'icon' => '🎨'],
    ['age' => '4-5', 'skills' => 'Letters, Numbers, Patterns', 'icon' => '🔤'],
    ['age' => '6-7', 'skills' => 'Reading, Counting, Memory', 'icon' => '📚'],
    ['age' => '8-10', 'skills' => 'Logic, Problem Solving, Strategy', 'icon' => '🧠']
];

// Set page variables
$section = 'educational';
$title = 'Educational Games - KidsBookery';
$description = 'Make learning fun with our educational games. From alphabet adventures to counting safaris, every activity teaches something new.';

include 'includes/header.php';
include 'includes/navigation.php';
?>

<main>
    <!-- Hero Section - Learning Theme -->
    <section class="hero-section hero-educational">
        <div class="hero-background">
            <div class="hero-star hero-star-1"></div>
            <div class="hero-star hero-star-2"></div>
            <div class="hero-star hero-star-3"></div>
            <div class="hero-star hero-star-4"></div>
            <div class="hero-star hero-star-5"></div>
            <div class="hero-orb hero-orb-1"></div>
            <div class="hero-orb hero-orb-2"></div>
            <div class="floating-icon icon-1">🔤</div>
            <div class="floating-icon icon-2">🔢</div>
            <div class="floating-icon icon-3">📖</div>
            <div class="floating-icon icon-4">🎨</div>
            <div class="floating-icon icon-5">🧠</div>
        </div>
        
        <div class="container">
            <div class="hero-content">
                <span class="hero-badge">📚 Educational Games</span>
                <h1>Learn through <span>play & discovery</span></h1>
                <p>Educational games that feel like play. From alphabet adventures to counting safaris, every activity teaches something new.</p>
                
                <div class="hero-stats">
                    <div class="stat">
                        <span class="stat-number">50+</span>
                        <span class="stat-label">ACTIVITIES</span>
                    </div>
                    <div class="stat">
                        <span class="stat-number">2-10</span>
                        <span class="stat-label">AGE RANGE</span>
                    </div>
                    <div class="stat">
                        <span class="stat-number">100%</span>
                        <span class="stat-label">EDUCATIONAL</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Learning Milestones -->
    <section class="milestones-section">
        <div class="container">
            <h2 class="section-title">Learning milestones</h2>
            <div class="milestones-grid">
                <?php foreach($milestones as $milestone): ?>
                <div class="milestone-card">
                    <div class="milestone-icon"><?php echo $milestone['icon']; ?></div>
                    <div class="milestone-age">Ages <?php echo htmlspecialchars($milestone['age']); ?></div>
                    <div class="milestone-skills"><?php echo htmlspecialchars($milestone['skills']); ?></div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Products Grid -->
    <section class="products-section">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">All Educational Games</h2>
                <span class="product-count"><?php echo count($educational_games); ?> items</span>
            </div>

            <?php if (empty($educational_games)): ?>
                <div class="no-products-message">
                    <p>No educational games available at the moment. Please check back later!</p>
                </div>
            <?php else: ?>
                <div class="products-grid">
                    <?php foreach($educational_games as $book): 
                        $price = getProductPrice($pdo, $book['id']);
                        $image_file = !empty($book['image_file']) ? $book['image_file'] : 'placeholder.jpg';
                    ?>
                    <div class="product-card" onclick="window.location.href='product.php?id=<?php echo $book['id']; ?>'">
                        <div class="product-image">
                            <img src="images/<?php echo htmlspecialchars($image_file); ?>" 
                                 alt="<?php echo htmlspecialchars($book['image_alt'] ?? $book['title']); ?>">
                            <span class="product-category"><?php echo htmlspecialchars($book['category_name'] ?? 'Educational'); ?></span>
                        </div>
                        <div class="product-info">
                            <h3><?php echo htmlspecialchars($book['title']); ?></h3>
                            <p class="product-summary"><?php echo htmlspecialchars(truncateText($book['summary'] ?? '', 60)); ?></p>
                            <div class="product-meta">
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
</main>

<style>
/* Educational Page Specific Styles */
.hero-educational {
    background: linear-gradient(135deg, var(--royal-blue) 0%, var(--sapphire) 100%);
    position: relative;
    min-height: 70vh;
    display: flex;
    align-items: center;
    overflow: hidden;
}

/* Floating Icons Animation */
.floating-icon {
    position: absolute;
    font-size: 2rem;
    pointer-events: none;
    animation: floatIcon linear infinite;
    opacity: 0;
    filter: drop-shadow(0 5px 15px rgba(0, 0, 0, 0.2));
}

.icon-1 { top: 15%; left: 5%; animation-duration: 12s; animation-delay: 0s; }
.icon-2 { top: 70%; right: 8%; animation-duration: 15s; animation-delay: 2s; }
.icon-3 { bottom: 20%; left: 12%; animation-duration: 10s; animation-delay: 4s; }
.icon-4 { top: 40%; right: 15%; animation-duration: 18s; animation-delay: 1s; }
.icon-5 { bottom: 50%; left: 85%; animation-duration: 14s; animation-delay: 3s; }

@keyframes floatIcon {
    0% {
        transform: translateY(100vh) rotate(0deg);
        opacity: 0;
    }
    20% {
        opacity: 0.6;
    }
    80% {
        opacity: 0.6;
    }
    100% {
        transform: translateY(-20vh) rotate(360deg);
        opacity: 0;
    }
}

/* Milestones Section */
.milestones-section {
    padding: 4rem 0;
    background: var(--bg-warm);
}

.milestones-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 1.5rem;
}

.milestone-card {
    background: var(--white);
    border-radius: var(--radius-lg);
    padding: 1.8rem;
    text-align: center;
    transition: var(--transition-base);
    border: 1px solid rgba(224, 197, 143, 0.2);
    cursor: pointer;
}

.milestone-card:hover {
    transform: translateY(-5px);
    box-shadow: var(--shadow-md);
    border-color: var(--quicksand);
}

.milestone-icon {
    font-size: 2.5rem;
    margin-bottom: 1rem;
    display: inline-block;
}

.milestone-age {
    font-size: 1.2rem;
    font-weight: 700;
    color: var(--primary);
    margin-bottom: 0.5rem;
    font-family: 'Cormorant Garamond', serif;
}

.milestone-skills {
    font-size: 0.8rem;
    color: var(--text-soft);
    line-height: 1.5;
}

/* Section Header */
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

/* Products Section */
.products-section {
    padding: 4rem 0;
}

.products-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 2rem;
}

.product-card {
    background: var(--white);
    border-radius: var(--radius-lg);
    overflow: hidden;
    box-shadow: var(--shadow-sm);
    transition: var(--transition-base);
    cursor: pointer;
    border: 1px solid rgba(224, 197, 143, 0.2);
}

.product-card:hover {
    transform: translateY(-5px);
    box-shadow: var(--shadow-lg);
    border-color: var(--quicksand);
}

.product-image {
    position: relative;
    height: 220px;
    overflow: hidden;
    background: var(--bg-warm);
}

.product-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform var(--transition-slow);
}

.product-card:hover .product-image img {
    transform: scale(1.05);
}

.product-category {
    position: absolute;
    top: 0.8rem;
    left: 0.8rem;
    background: var(--primary);
    color: var(--white);
    padding: 0.2rem 0.8rem;
    border-radius: var(--radius-full);
    font-size: 0.7rem;
    font-weight: 600;
}

.product-info {
    padding: 1.2rem;
}

.product-info h3 {
    font-size: 1.1rem;
    margin-bottom: 0.5rem;
    color: var(--primary);
}

.product-summary {
    color: var(--text-soft);
    font-size: 0.8rem;
    margin-bottom: 0.8rem;
    line-height: 1.5;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.product-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
    padding-top: 0.5rem;
    border-top: 1px solid rgba(224, 197, 143, 0.2);
}

.product-price {
    font-size: 1.2rem;
    font-weight: 700;
    color: var(--primary);
}

.product-rating {
    color: var(--quicksand);
    font-size: 0.8rem;
    letter-spacing: 1px;
}

.btn-add {
    width: 100%;
    padding: 0.6rem;
    background: var(--bg-light);
    color: var(--text-dark);
    border: 1px solid rgba(224, 197, 143, 0.3);
    border-radius: var(--radius-full);
    cursor: pointer;
    transition: var(--transition-base);
    font-weight: 500;
    font-size: 0.8rem;
}

.btn-add:hover {
    background: var(--quicksand);
    color: var(--primary);
    transform: translateY(-2px);
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

/* Hero Stats */
.hero-stats {
    display: flex;
    justify-content: center;
    gap: 2rem;
    margin-top: 2rem;
    flex-wrap: wrap;
}

.stat {
    text-align: center;
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(8px);
    padding: 0.8rem 1.5rem;
    border-radius: var(--radius-lg);
    border: 1px solid rgba(224, 197, 143, 0.3);
}

.stat-number {
    display: block;
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--quicksand);
    font-family: 'Cormorant Garamond', serif;
}

.stat-label {
    font-size: 0.7rem;
    text-transform: uppercase;
    letter-spacing: 1px;
    color: rgba(245, 240, 233, 0.9);
}

/* Responsive */
@media (max-width: 768px) {
    .milestones-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .products-grid {
        grid-template-columns: 1fr;
    }
    
    .section-header {
        flex-direction: column;
        text-align: center;
    }
    
    .hero-stats {
        gap: 1rem;
    }
    
    .stat {
        padding: 0.5rem 1rem;
    }
    
    .stat-number {
        font-size: 1.2rem;
    }
    
    .floating-icon {
        font-size: 1.5rem;
    }
}

@media (max-width: 480px) {
    .milestones-grid {
        grid-template-columns: 1fr;
    }
    
    .hero-stats {
        flex-direction: column;
        align-items: center;
    }
    
    .stat {
        width: 100%;
        max-width: 200px;
    }
}
</style>

<?php include 'includes/footer.php'; ?>