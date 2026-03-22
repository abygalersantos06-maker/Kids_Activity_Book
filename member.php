<?php
declare(strict_types = 1);
require 'includes/db_connect.php';
require 'includes/functions.php';

// Get member ID from URL
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

// Initialize variables
$member = null;
$articles = [];

// Get top 3 members by article count
$sql = "SELECT m.id, m.forename, m.surname, m.picture, m.joined, COUNT(a.id) as article_count
        FROM member m
        LEFT JOIN article a ON m.id = a.member_id AND a.published = 1
        GROUP BY m.id
        ORDER BY article_count DESC
        LIMIT 3";
$stmt = $pdo->query($sql);
$top_members = $stmt->fetchAll();

// If valid id, try to get specific member data
if ($id) {
    $sql = "SELECT forename, surname, joined, picture FROM member WHERE id = :id;";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $id]);
    $member = $stmt->fetch();
    
    if ($member) {
        $sql = "SELECT a.id, a.title, a.summary, a.category_id, a.member_id, a.created, a.price,
                c.name AS category,
                CONCAT(m.forename, ' ', m.surname) AS author,
                i.file AS image_file,
                i.alt AS image_alt
                FROM article a
                JOIN category c ON a.category_id = c.id
                JOIN member m ON a.member_id = m.id
                LEFT JOIN image i ON a.image_id = i.id
                WHERE a.member_id = :id AND a.published = 1
                ORDER BY a.created DESC;";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        $articles = $stmt->fetchAll();
    }
}

// Get navigation categories
$sql = "SELECT id, name FROM category WHERE navigation = 1;";
$stmt = $pdo->query($sql);
$navigation = $stmt->fetchAll();

// Set page variables
$section = '';
$title = $member ? $member['forename'] . ' ' . $member['surname'] . ' - KidsBookery' : 'Top Creators - KidsBookery';
$description = $member ? 'View all activity books created by ' . $member['forename'] . ' ' . $member['surname'] : 'Meet our top creators';

include 'includes/header.php';
include 'includes/navigation.php';
?>

<main class="member-page">
    <div class="container">
        
        <!-- Top Members Section - Only Initials -->
        <section class="top-members-section">
            <h2 class="section-title">🏆 Top Creators</h2>
            <div class="top-members-grid">
                <?php 
                $medals = ['🥇', '🥈', '🥉'];
                foreach($top_members as $index => $top_member): 
                ?>
                <a href="member.php?id=<?php echo $top_member['id']; ?>" class="top-member-card">
                    <div class="medal"><?php echo $medals[$index]; ?></div>
                    <div class="top-member-avatar">
                        <div class="avatar-initials">
                            <?php echo strtoupper(substr($top_member['forename'], 0, 1) . substr($top_member['surname'], 0, 1)); ?>
                        </div>
                    </div>
                    <h3><?php echo htmlspecialchars($top_member['forename'] . ' ' . $top_member['surname']); ?></h3>
                    <div class="member-rank">
                        <span class="rank-number">#<?php echo $index + 1; ?></span>
                        <span class="rank-label">Creator</span>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>
        </section>

        <?php if ($member): ?>
            <!-- Member Profile Header - Only Initials -->
            <section class="member-header">
                <div class="member-avatar">
                    <div class="avatar-initials large">
                        <?php echo strtoupper(substr($member['forename'], 0, 1) . substr($member['surname'], 0, 1)); ?>
                    </div>
                </div>
                
                <div class="member-info">
                    <h1><?php echo htmlspecialchars($member['forename'] . ' ' . $member['surname']); ?></h1>
                    
                    <?php 
                    // Check rank
                    $rank = 0;
                    foreach($top_members as $index => $top_member) {
                        if ($top_member['id'] == $id) {
                            $rank = $index + 1;
                            break;
                        }
                    }
                    ?>
                    
                    <?php if ($rank > 0): ?>
                    <div class="member-rank-badge">
                        <span class="rank-medal"><?php echo $rank == 1 ? '🥇' : ($rank == 2 ? '🥈' : '🥉'); ?></span>
                        <span class="rank-text">Rank #<?php echo $rank; ?> Creator</span>
                    </div>
                    <?php endif; ?>
                    
                    <div class="member-stats">
                        <div class="stat-item">
                            <span class="stat-number"><?php echo count($articles); ?></span>
                            <span class="stat-label">Books</span>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Member's Articles Section -->
            <section class="member-articles">
                <h2 class="section-title">Books by <?php echo htmlspecialchars($member['forename']); ?></h2>
                
                <?php if (empty($articles)): ?>
                    <div class="no-articles">
                        <p>No books created yet.</p>
                    </div>
                <?php else: ?>
                    <div class="products-grid">
                        <?php foreach($articles as $article): 
                            $price = getProductPrice($pdo, $article['id']);
                            $image_file = !empty($article['image_file']) ? $article['image_file'] : 'placeholder.jpg';
                        ?>
                        <div class="product-card" onclick="window.location.href='product.php?id=<?php echo $article['id']; ?>'">
                            <div class="product-image">
                                <img src="images/<?php echo htmlspecialchars($image_file); ?>" 
                                     alt="<?php echo htmlspecialchars($article['image_alt'] ?? $article['title']); ?>">
                                <span class="product-category"><?php echo htmlspecialchars($article['category']); ?></span>
                            </div>
                            <div class="product-info">
                                <h3><?php echo htmlspecialchars($article['title']); ?></h3>
                                <p class="product-summary"><?php echo htmlspecialchars(truncateText($article['summary'] ?? '', 60)); ?></p>
                                <div class="product-meta">
                                    <span class="product-price"><?php echo formatPrice($price); ?></span>
                                    <span class="product-rating">★★★★★</span>
                                </div>
                                <button class="btn-add" onclick="event.stopPropagation(); window.location.href='cart.php?add=<?php echo $article['id']; ?>&qty=1'">Add to Cart</button>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </section>
        <?php endif; ?>
    </div>
</main>

<style>
/* Member Page Simplified Styles - No Images */
.member-page {
    padding: 6rem 0 4rem;
}

/* Top Members Section */
.top-members-section {
    margin-bottom: 4rem;
    text-align: center;
}

.top-members-section .section-title {
    margin-bottom: 2rem;
}

.top-members-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 2rem;
    max-width: 800px;
    margin: 0 auto;
}

.top-member-card {
    background: var(--white);
    border-radius: var(--radius-xl);
    padding: 2rem 1.5rem;
    text-align: center;
    text-decoration: none;
    transition: var(--transition-base);
    border: 1px solid rgba(224, 197, 143, 0.2);
    position: relative;
}

.top-member-card:hover {
    transform: translateY(-5px);
    box-shadow: var(--shadow-md);
    border-color: var(--quicksand);
}

.medal {
    font-size: 2rem;
    margin-bottom: 1rem;
}

.top-member-avatar {
    width: 80px;
    height: 80px;
    margin: 0 auto 1rem;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--quicksand), #F5E6D0);
    display: flex;
    align-items: center;
    justify-content: center;
}

.avatar-initials {
    font-size: 2rem;
    font-weight: 700;
    color: var(--primary);
}

.top-member-card h3 {
    font-size: 1.1rem;
    color: var(--primary);
    margin-bottom: 0.5rem;
}

.member-rank {
    display: flex;
    align-items: baseline;
    justify-content: center;
    gap: 0.3rem;
}

.rank-number {
    font-size: 1rem;
    font-weight: 700;
    color: var(--quicksand);
}

.rank-label {
    font-size: 0.7rem;
    color: var(--text-soft);
}

/* Member Profile Header */
.member-header {
    display: flex;
    align-items: center;
    gap: 2rem;
    background: var(--white);
    border-radius: var(--radius-xl);
    padding: 2rem;
    margin-bottom: 3rem;
    border: 1px solid rgba(224, 197, 143, 0.2);
    box-shadow: var(--shadow-sm);
    flex-wrap: wrap;
}

.member-avatar {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--quicksand), #F5E6D0);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.avatar-initials.large {
    font-size: 2.5rem;
    font-weight: 700;
    color: var(--primary);
}

.member-info {
    flex: 1;
}

.member-info h1 {
    font-size: 1.8rem;
    color: var(--primary);
    margin-bottom: 0.5rem;
}

.member-rank-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    background: var(--bg-light);
    padding: 0.3rem 0.8rem;
    border-radius: var(--radius-full);
    margin-bottom: 1rem;
}

.rank-medal {
    font-size: 1rem;
}

.rank-text {
    font-size: 0.8rem;
    font-weight: 500;
    color: var(--primary);
}

.member-stats {
    display: flex;
    gap: 1.5rem;
}

.stat-item {
    background: var(--bg-light);
    border-radius: var(--radius-lg);
    padding: 0.5rem 1rem;
    text-align: center;
    min-width: 80px;
}

.stat-number {
    display: block;
    font-size: 1.2rem;
    font-weight: 700;
    color: var(--primary);
}

.stat-label {
    font-size: 0.7rem;
    color: var(--text-soft);
    text-transform: uppercase;
}

/* Member Articles */
.member-articles {
    margin-top: 2rem;
}

.member-articles .section-title {
    text-align: left;
    margin-bottom: 1.5rem;
}

.member-articles .section-title::after {
    left: 0;
    transform: translateX(0);
}

.no-articles {
    text-align: center;
    padding: 3rem;
    background: var(--white);
    border-radius: var(--radius-lg);
    border: 1px solid rgba(224, 197, 143, 0.2);
}

.no-articles p {
    color: var(--text-soft);
}

/* Product Cards */
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
    height: 200px;
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
    font-size: 1rem;
    margin-bottom: 0.5rem;
    color: var(--primary);
}

.product-summary {
    color: var(--text-soft);
    font-size: 0.75rem;
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
    font-size: 1.1rem;
    font-weight: 700;
    color: var(--primary);
}

.product-rating {
    color: var(--quicksand);
    font-size: 0.7rem;
    letter-spacing: 1px;
}

.btn-add {
    width: 100%;
    padding: 0.5rem;
    background: var(--bg-light);
    color: var(--text-dark);
    border: 1px solid rgba(224, 197, 143, 0.3);
    border-radius: var(--radius-full);
    cursor: pointer;
    transition: var(--transition-base);
    font-weight: 500;
    font-size: 0.75rem;
}

.btn-add:hover {
    background: var(--quicksand);
    color: var(--primary);
    transform: translateY(-2px);
}

/* Responsive */
@media (max-width: 768px) {
    .member-page {
        padding: 5rem 0 3rem;
    }
    
    .member-header {
        flex-direction: column;
        text-align: center;
    }
    
    .member-stats {
        justify-content: center;
    }
    
    .top-members-grid {
        grid-template-columns: 1fr;
        max-width: 280px;
    }
    
    .products-grid {
        grid-template-columns: 1fr;
    }
    
    .member-articles .section-title {
        text-align: center;
    }
    
    .member-articles .section-title::after {
        left: 50%;
        transform: translateX(-50%);
    }
}

@media (max-width: 480px) {
    .member-stats {
        flex-direction: column;
        align-items: center;
    }
    
    .stat-item {
        width: 100%;
    }
    
    .member-avatar {
        width: 80px;
        height: 80px;
    }
    
    .avatar-initials.large {
        font-size: 2rem;
    }
}
</style>

<?php include 'includes/footer.php'; ?>