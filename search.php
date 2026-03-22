<?php
// search.php - Complete working search with pagination
declare(strict_types = 1);
require 'includes/db_connect.php';
require 'includes/functions.php';

$term = isset($_GET['term']) ? trim($_GET['term']) : '';
$show = filter_input(INPUT_GET, 'show', FILTER_VALIDATE_INT) ?? 12;
$from = filter_input(INPUT_GET, 'from', FILTER_VALIDATE_INT) ?? 0;
$count = 0;
$articles = [];

if (!empty($term)) {
    $searchTerm = '%' . $term . '%';
    
    // Count total matching articles
    $countSql = "SELECT COUNT(*) FROM article 
                 WHERE (title LIKE :term OR summary LIKE :term OR content LIKE :term)
                 AND published = 1";
    $countStmt = $pdo->prepare($countSql);
    $countStmt->execute(['term' => $searchTerm]);
    $count = $countStmt->fetchColumn();
    
    if ($count > 0) {
        // Get paginated results
        $sql = "SELECT a.id, a.title, a.summary, a.content, a.price, a.created,
                       c.id as category_id, c.name AS category_name,
                       m.id as member_id, CONCAT(m.forename, ' ', m.surname) AS author,
                       i.file AS image_file, i.alt AS image_alt
                FROM article a
                JOIN category c ON a.category_id = c.id
                JOIN member m ON a.member_id = m.id
                LEFT JOIN image i ON a.image_id = i.id
                WHERE (a.title LIKE :term OR a.summary LIKE :term OR a.content LIKE :term)
                AND a.published = 1
                ORDER BY a.created DESC
                LIMIT :limit OFFSET :offset";
        
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue('term', $searchTerm, PDO::PARAM_STR);
        $stmt->bindValue('limit', $show, PDO::PARAM_INT);
        $stmt->bindValue('offset', $from, PDO::PARAM_INT);
        $stmt->execute();
        $articles = $stmt->fetchAll();
    }
}

// Calculate pagination
$total_pages = $count > 0 ? ceil($count / $show) : 0;
$current_page = $count > 0 ? floor($from / $show) + 1 : 1;

// Get navigation categories
$navigation = getNavigationCategories($pdo);

$title = !empty($term) ? "Search results for: " . html_escape($term) : "Search";
$description = $title . " - Kids Activity Books";

include 'includes/header.php';
include 'includes/navigation.php';
?>

<main class="container" id="content">
    <!-- Search Header -->
    <div class="search-header">
        <div class="search-form-wrapper">
            <form action="search.php" method="get" class="search-form">
                <div class="search-input-group">
                    <input type="text" name="term" value="<?= html_escape($term) ?>" 
                           placeholder="Search for books, puzzles, activities..." 
                           class="search-input" autofocus>
                    <button type="submit" class="search-btn">
                        <span class="search-icon">🔍</span>
                        Search
                    </button>
                </div>
            </form>
        </div>
        
        <?php if (!empty($term)): ?>
        <div class="search-stats">
            <p><strong><?= number_format($count) ?></strong> <?= $count == 1 ? 'result' : 'results' ?> 
               found for "<strong><?= html_escape($term) ?></strong>"</p>
        </div>
        <?php endif; ?>
    </div>

    <!-- Search Results -->
    <?php if (!empty($term)): ?>
        <?php if (empty($articles)): ?>
            <div class="no-results">
                <div class="no-results-icon">🔍</div>
                <h2>No books found</h2>
                <p>We couldn't find any books matching "<?= html_escape($term) ?>".</p>
                <div class="no-results-suggestions">
                    <h3>Try these suggestions:</h3>
                    <ul>
                        <li>Check your spelling</li>
                        <li>Use more general terms</li>
                        <li>Try different keywords</li>
                        <li>Browse our categories below</li>
                    </ul>
                </div>
            </div>
        <?php else: ?>
            <div class="results-grid">
                <?php foreach ($articles as $article): ?>
                <div class="book-card">
                    <div class="book-image">
                        <img src="images/<?= html_escape($article['image_file'] ?? 'placeholder.jpg') ?>" 
                             alt="<?= html_escape($article['image_alt'] ?? $article['title']) ?>">
                        <?php if ($article['price']): ?>
                        <span class="price-badge"><?= formatPrice($article['price']) ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="book-info">
                        <h3>
                            <a href="product.php?id=<?= $article['id'] ?>">
                                <?= html_escape($article['title']) ?>
                            </a>
                        </h3>
                        <p class="book-summary"><?= html_escape(truncateText($article['summary'], 100)) ?></p>
                        <div class="book-meta">
                            <span class="book-category">
                                <a href="<?= getCategoryLink($article['category_id']) ?>">
                                    📚 <?= html_escape($article['category_name']) ?>
                                </a>
                            </span>
                            <span class="book-author">
                                By <a href="member.php?id=<?= $article['member_id'] ?>">
                                    <?= html_escape($article['author']) ?>
                                </a>
                            </span>
                        </div>
                        <div class="book-actions">
                            <a href="product.php?id=<?= $article['id'] ?>" class="btn-view">View Details</a>
                            <a href="cart.php?add=<?= $article['id'] ?>" class="btn-add">+ Add to Cart</a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
            <nav class="pagination" role="navigation" aria-label="Pagination">
                <div class="pagination-info">
                    Showing <?= ($from + 1) ?> to <?= min($from + $show, $count) ?> of <?= $count ?> results
                </div>
                <ul class="pagination-list">
                    <?php if ($current_page > 1): ?>
                    <li>
                        <a href="?term=<?= urlencode($term) ?>&show=<?= $show ?>&from=<?= ($current_page - 2) * $show ?>" 
                           class="pagination-prev" aria-label="Previous page">← Previous</a>
                    </li>
                    <?php endif; ?>
                    
                    <?php
                    $start_page = max(1, $current_page - 2);
                    $end_page = min($total_pages, $current_page + 2);
                    
                    if ($start_page > 1): ?>
                        <li><a href="?term=<?= urlencode($term) ?>&show=<?= $show ?>&from=0">1</a></li>
                        <?php if ($start_page > 2): ?><li><span>...</span></li><?php endif; ?>
                    <?php endif; ?>
                    
                    <?php for ($i = $start_page; $i <= $end_page; $i++): ?>
                        <li>
                            <a href="?term=<?= urlencode($term) ?>&show=<?= $show ?>&from=<?= ($i - 1) * $show ?>"
                               class="<?= ($i == $current_page) ? 'active' : '' ?>">
                                <?= $i ?>
                            </a>
                        </li>
                    <?php endfor; ?>
                    
                    <?php if ($end_page < $total_pages): ?>
                        <?php if ($end_page < $total_pages - 1): ?><li><span>...</span></li><?php endif; ?>
                        <li><a href="?term=<?= urlencode($term) ?>&show=<?= $show ?>&from=<?= ($total_pages - 1) * $show ?>"><?= $total_pages ?></a></li>
                    <?php endif; ?>
                    
                    <?php if ($current_page < $total_pages): ?>
                    <li>
                        <a href="?term=<?= urlencode($term) ?>&show=<?= $show ?>&from=<?= $current_page * $show ?>" 
                           class="pagination-next" aria-label="Next page">Next →</a>
                    </li>
                    <?php endif; ?>
                </ul>
            </nav>
            <?php endif; ?>
        <?php endif; ?>
    <?php else: ?>
        <div class="search-prompt">
            <div class="search-prompt-icon">🔎</div>
            <h2>Find your next adventure</h2>
            <p>Search for coloring books, puzzles, educational games, and more!</p>
            <div class="popular-searches">
                <h3>Popular searches:</h3>
                <div class="popular-tags">
                    <a href="?term=coloring">coloring</a>
                    <a href="?term=puzzle">puzzle</a>
                    <a href="?term=educational">educational</a>
                    <a href="?term=printable">printable</a>
                    <a href="?term=alphabet">alphabet</a>
                    <a href="?term=numbers">numbers</a>
                    <a href="?term=mazes">mazes</a>
                    <a href="?term=animals">animals</a>
                </div>
            </div>
        </div>
    <?php endif; ?>
</main>

<?php include 'includes/footer.php'; ?>