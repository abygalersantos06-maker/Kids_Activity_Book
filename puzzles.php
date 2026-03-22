<?php
// puzzles.php - Puzzle books page with brain teaser theme
require_once 'includes/db_connect.php';
require_once 'includes/functions.php';

// Initialize variables
$puzzle_books = [];

try {
    // Get puzzle books (category_id = 2)
    $stmt = $pdo->prepare("SELECT a.*, c.name as category_name, i.file as image_file, i.alt as image_alt 
                           FROM article a 
                           LEFT JOIN category c ON a.category_id = c.id 
                           LEFT JOIN image i ON a.image_id = i.id 
                           WHERE a.category_id = 2 AND a.published = 1 
                           ORDER BY a.created DESC");
    $stmt->execute();
    $puzzle_books = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log("Error fetching puzzle books: " . $e->getMessage());
    // Continue with empty array
}

// Puzzle stats
$puzzle_stats = [
    ['number' => '150+', 'label' => 'PUZZLES'],
    ['number' => '5', 'label' => 'DIFFICULTY LEVELS'],
    ['number' => '30min', 'label' => 'AVG. PLAY TIME']
];

// Set page variables
$section = 'puzzles';
$title = 'Puzzle Books - KidsBookery';
$description = 'Challenge young minds with our collection of puzzle books. Mazes, crosswords, and logic puzzles that make learning fun.';

include 'includes/header.php';
include 'includes/navigation.php';
?>

<main>
    <!-- Hero Section - Brain Teaser Theme -->
    <section class="hero-section hero-puzzles">
        <div class="hero-background">
            <div class="hero-star hero-star-1"></div>
            <div class="hero-star hero-star-2"></div>
            <div class="hero-star hero-star-3"></div>
            <div class="hero-star hero-star-4"></div>
            <div class="hero-star hero-star-5"></div>
            <div class="hero-orb hero-orb-1"></div>
            <div class="hero-orb hero-orb-2"></div>
            <div class="floating-puzzle piece-1">🧩</div>
            <div class="floating-puzzle piece-2">🧩</div>
            <div class="floating-puzzle piece-3">🧩</div>
            <div class="floating-puzzle piece-4">🧩</div>
            <div class="floating-puzzle piece-5">🧩</div>
        </div>
        
        <div class="container">
            <div class="hero-content">
                <span class="hero-badge">🧩 Puzzle Books</span>
                <h1>Challenge young <span>minds to think</span></h1>
                <p>Mazes, crosswords, and logic puzzles that make learning fun. Each puzzle builds critical thinking skills while keeping kids entertained.</p>
                
                <div class="hero-stats">
                    <?php foreach($puzzle_stats as $stat): ?>
                    <div class="stat">
                        <span class="stat-number"><?php echo htmlspecialchars($stat['number']); ?></span>
                        <span class="stat-label"><?php echo htmlspecialchars($stat['label']); ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- Difficulty Levels -->
    <section class="difficulty-section">
        <div class="container">
            <h2 class="section-title">Puzzle levels</h2>
            <div class="difficulty-grid">
                <div class="difficulty-card easy">
                    <span class="level-badge">🌱</span>
                    <h3>Easy</h3>
                    <p>Ages 3-4 • Simple mazes</p>
                </div>
                <div class="difficulty-card medium">
                    <span class="level-badge">🌟</span>
                    <h3>Medium</h3>
                    <p>Ages 5-6 • Word searches</p>
                </div>
                <div class="difficulty-card hard">
                    <span class="level-badge">🚀</span>
                    <h3>Hard</h3>
                    <p>Ages 7-8 • Crosswords</p>
                </div>
                <div class="difficulty-card expert">
                    <span class="level-badge">🏆</span>
                    <h3>Expert</h3>
                    <p>Ages 9-10 • Logic puzzles</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Products Grid -->
    <section class="products-section">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">All Puzzle Books</h2>
                <span class="product-count"><?php echo count($puzzle_books); ?> items</span>
            </div>

            <?php if (empty($puzzle_books)): ?>
                <div class="no-products-message">
                    <p>No puzzle books available at the moment. Please check back later!</p>
                </div>
            <?php else: ?>
                <div class="products-grid">
                    <?php foreach($puzzle_books as $book): 
                        $price = getProductPrice($pdo, $book['id']);
                        $image_file = !empty($book['image_file']) ? $book['image_file'] : 'placeholder.jpg';
                    ?>
                    <div class="product-card" onclick="window.location.href='product.php?id=<?php echo $book['id']; ?>'">
                        <div class="product-image">
                            <img src="images/<?php echo htmlspecialchars($image_file); ?>" 
                                 alt="<?php echo htmlspecialchars($book['image_alt'] ?? $book['title']); ?>">
                            <span class="product-category"><?php echo htmlspecialchars($book['category_name'] ?? 'Puzzles'); ?></span>
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
/* Puzzles Page Specific Styles */
.hero-puzzles {
    background: linear-gradient(135deg, var(--royal-blue) 0%, var(--sapphire) 100%);
    position: relative;
    min-height: 70vh;
    display: flex;
    align-items: center;
    overflow: hidden;
}

/* Floating Puzzles Animation */
.floating-puzzle {
    position: absolute;
    font-size: 2rem;
    pointer-events: none;
    animation: floatPuzzle linear infinite;
    opacity: 0;
    filter: drop-shadow(0 5px 15px rgba(0, 0, 0, 0.2));
}

.piece-1 { top: 10%; left: 5%; animation-duration: 12s; animation-delay: 0s; }
.piece-2 { top: 70%; right: 8%; animation-duration: 15s; animation-delay: 2s; }
.piece-3 { bottom: 15%; left: 15%; animation-duration: 10s; animation-delay: 4s; }
.piece-4 { top: 40%; right: 20%; animation-duration: 18s; animation-delay: 1s; }
.piece-5 { bottom: 50%; left: 80%; animation-duration: 14s; animation-delay: 3s; }

@keyframes floatPuzzle {
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

/* Difficulty Section */
.difficulty-section {
    padding: 4rem 0;
    background: var(--bg-warm);
}

.difficulty-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1.5rem;
}

.difficulty-card {
    background: var(--white);
    border-radius: var(--radius-lg);
    padding: 2rem 1.5rem;
    text-align: center;
    transition: var(--transition-base);
    border: 1px solid rgba(224, 197, 143, 0.2);
    cursor: pointer;
}

.difficulty-card:hover {
    transform: translateY(-5px);
    box-shadow: var(--shadow-md);
    border-color: var(--quicksand);
}

.level-badge {
    font-size: 2.5rem;
    display: block;
    margin-bottom: 1rem;
}

.difficulty-card h3 {
    color: var(--primary);
    margin-bottom: 0.5rem;
    font-size: 1.2rem;
}

.difficulty-card p {
    color: var(--text-soft);
    font-size: 0.85rem;
}

.difficulty-card.easy .level-badge { color: #10B981; }
.difficulty-card.medium .level-badge { color: #F59E0B; }
.difficulty-card.hard .level-badge { color: #EF4444; }
.difficulty-card.expert .level-badge { color: #8B5CF6; }

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

/* Products Grid */
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

/* Responsive Design */
@media (max-width: 768px) {
    .difficulty-grid {
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
        flex-wrap: wrap;
        justify-content: center;
    }
    
    .floating-puzzle {
        font-size: 1.5rem;
    }
}

@media (max-width: 480px) {
    .difficulty-grid {
        grid-template-columns: 1fr;
    }
    
    .hero-stats {
        flex-direction: column;
        align-items: center;
    }
    
    .stat {
        width: 100%;
    }
}
</style>

<?php include 'includes/footer.php'; ?>