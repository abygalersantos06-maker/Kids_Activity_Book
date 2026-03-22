<?php
// faq.php - Beautiful FAQ page
require_once 'includes/db_connect.php';
require_once 'includes/functions.php';
include 'includes/header.php';
include 'includes/navigation.php';

$faqs = [
    'orders' => [
        ['q' => 'How do I place an order?', 'a' => 'Simply browse our collection, add items to your cart, and proceed to checkout. You can pay using credit card, PayPal, or other available methods.'],
        ['q' => 'Can I modify my order after placing it?', 'a' => 'Yes, you can modify your order within 1 hour of placing it. Please contact our support team immediately.'],
        ['q' => 'Do you offer bulk discounts?', 'a' => 'Yes! For orders of 10+ books, please contact us for special pricing.'],
    ],
    'shipping' => [
        ['q' => 'How long does shipping take?', 'a' => 'Standard shipping takes 3-5 business days. Express shipping is available for 1-2 business days.'],
        ['q' => 'Do you ship internationally?', 'a' => 'Yes, we ship worldwide! International shipping typically takes 7-14 business days.'],
        ['q' => 'Is shipping really free?', 'a' => 'Yes, we offer free standard shipping on all orders over ₱1,500 within the Philippines.'],
    ],
    'digital' => [
        ['q' => 'How do I access my printables?', 'a' => 'After purchase, you\'ll receive an email with download links. You can also access them from your account dashboard.'],
        ['q' => 'Can I print multiple copies?', 'a' => 'Yes! All printables come with unlimited printing for personal use.'],
        ['q' => 'What format are the printables?', 'a' => 'All printables are in PDF format, compatible with any device.'],
    ],
    'returns' => [
        ['q' => 'What is your return policy?', 'a' => 'We offer 30-day returns for physical books in original condition. Digital products are non-refundable.'],
        ['q' => 'How do I initiate a return?', 'a' => 'Contact our support team with your order number and reason for return. We\'ll provide a return label.'],
        ['q' => 'When will I get my refund?', 'a' => 'Refunds are processed within 5-7 business days after we receive the returned item.'],
    ]
];
?>

<main class="faq-page">
    <!-- Hero Section -->
    <section class="faq-hero">
        <div class="container">
            <h1>Hello! How can we help?</h1>
            <p>Find answers to commonly asked questions below</p>
            <div class="faq-search">
                <input type="text" placeholder="Search for answers..." id="faqSearch">
                <button>🔍</button>
            </div>
        </div>
    </section>

    <!-- FAQ Categories -->
    <section class="faq-section">
        <div class="container">
            <div class="faq-categories">
                <button class="faq-category active" data-category="all">All Questions</button>
                <button class="faq-category" data-category="orders">Orders</button>
                <button class="faq-category" data-category="shipping">Shipping</button>
                <button class="faq-category" data-category="digital">Digital Products</button>
                <button class="faq-category" data-category="returns">Returns</button>
            </div>

            <div class="faq-grid">
                <?php foreach($faqs as $category => $questions): ?>
                    <?php foreach($questions as $faq): ?>
                    <div class="faq-item" data-category="<?php echo $category; ?>">
                        <div class="faq-question">
                            <h3><?php echo htmlspecialchars($faq['q']); ?></h3>
                            <span class="faq-toggle">+</span>
                        </div>
                        <div class="faq-answer">
                            <p><?php echo htmlspecialchars($faq['a']); ?></p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Still Need Help -->
    <section class="help-section">
        <div class="container">
            <div class="help-card">
                <div class="help-icon">💬</div>
                <h2>Still need help?</h2>
                <p>Our support team is ready to assist you</p>
                <div class="help-options">
                    <a href="mailto:support@kidsbookery.com" class="help-option">
                        <span>📧</span>
                        support@kidsbookery.com
                    </a>
                    <a href="tel:+63212345678" class="help-option">
                        <span>📞</span>
                        +63 (2) 1234 5678
                    </a>
                </div>
                <p class="help-hours">Mon-Fri, 9am-6pm PHT</p>
            </div>
        </div>
    </section>
</main>

<script>
// FAQ toggle functionality
document.querySelectorAll('.faq-question').forEach(question => {
    question.addEventListener('click', () => {
        const item = question.parentElement;
        const isActive = item.classList.contains('active');
        
        // Close all other items
        document.querySelectorAll('.faq-item').forEach(i => {
            i.classList.remove('active');
        });
        
        // Open clicked item if it wasn't active
        if (!isActive) {
            item.classList.add('active');
        }
    });
});

// Category filtering
document.querySelectorAll('.faq-category').forEach(btn => {
    btn.addEventListener('click', () => {
        const category = btn.dataset.category;
        
        // Update active button
        document.querySelectorAll('.faq-category').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        
        // Filter items
        document.querySelectorAll('.faq-item').forEach(item => {
            if (category === 'all' || item.dataset.category === category) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
            }
        });
    });
});

// Search functionality
document.getElementById('faqSearch')?.addEventListener('input', (e) => {
    const searchTerm = e.target.value.toLowerCase();
    
    document.querySelectorAll('.faq-item').forEach(item => {
        const question = item.querySelector('.faq-question h3').textContent.toLowerCase();
        const answer = item.querySelector('.faq-answer p').textContent.toLowerCase();
        
        if (question.includes(searchTerm) || answer.includes(searchTerm)) {
            item.style.display = 'block';
        } else {
            item.style.display = 'none';
        }
    });
});
</script>

<style>
/* FAQ Page Specific Styles - Using Your Color Palette */
:root {
    --primary-blue: #112250;
    --secondary-blue: #3C507D;
    --gold: #E0C58F;
    --cream: #F5F0E9;
    --sand: #D9CBC2;
    --white: #FFFFFF;
    --dark-text: #1A2A3A;
    --gray-text: #4A5B6E;
}

.faq-page {
    padding-top: 100px;
}

/* Hero Section */
.faq-hero {
    background: linear-gradient(135deg, var(--primary-blue) 0%, var(--secondary-blue) 100%);
    color: white;
    padding: 80px 0;
    text-align: center;
    position: relative;
    overflow: hidden;
}

.faq-hero::before {
    content: '❓';
    position: absolute;
    top: -20px;
    right: -20px;
    font-size: 200px;
    opacity: 0.1;
    transform: rotate(15deg);
}

.faq-hero::after {
    content: '📚';
    position: absolute;
    bottom: -30px;
    left: -30px;
    font-size: 180px;
    opacity: 0.1;
    transform: rotate(-10deg);
}

.faq-hero h1 {
    font-size: 48px;
    margin-bottom: 15px;
    color: white;
    font-family: 'Cormorant Garamond', serif;
}

.faq-hero p {
    font-size: 18px;
    margin-bottom: 30px;
    opacity: 0.9;
}

.faq-search {
    display: flex;
    max-width: 500px;
    margin: 0 auto;
    gap: 10px;
}

.faq-search input {
    flex: 1;
    padding: 18px 25px;
    border: none;
    border-radius: 50px;
    font-size: 16px;
    outline: none;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    background: white;
}

.faq-search input:focus {
    box-shadow: 0 0 0 3px rgba(224, 197, 143, 0.3);
}

.faq-search button {
    width: 60px;
    border: none;
    border-radius: 50px;
    background: var(--gold);
    color: var(--primary-blue);
    font-size: 20px;
    cursor: pointer;
    transition: all 0.3s ease;
}

.faq-search button:hover {
    background: #F5E6D0;
    transform: scale(1.05);
}

/* FAQ Categories */
.faq-section {
    padding: 60px 0;
    background: var(--cream);
}

.faq-categories {
    display: flex;
    gap: 15px;
    justify-content: center;
    margin-bottom: 40px;
    flex-wrap: wrap;
}

.faq-category {
    padding: 12px 28px;
    background: white;
    border: 2px solid var(--gold);
    border-radius: 50px;
    font-size: 15px;
    font-weight: 600;
    color: var(--primary-blue);
    cursor: pointer;
    transition: all 0.3s ease;
}

.faq-category:hover {
    background: var(--gold);
    transform: translateY(-2px);
}

.faq-category.active {
    background: var(--primary-blue);
    border-color: var(--primary-blue);
    color: white;
}

/* FAQ Grid */
.faq-grid {
    max-width: 800px;
    margin: 0 auto;
}

.faq-item {
    background: white;
    border-radius: 20px;
    margin-bottom: 15px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.03);
    border: 1px solid rgba(224, 197, 143, 0.3);
    overflow: hidden;
    transition: all 0.3s ease;
}

.faq-item:hover {
    box-shadow: 0 10px 30px rgba(17, 34, 80, 0.1);
}

.faq-question {
    padding: 22px 25px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    cursor: pointer;
    background: white;
}

.faq-question h3 {
    font-size: 18px;
    color: var(--primary-blue);
    font-weight: 600;
    margin: 0;
}

.faq-toggle {
    font-size: 24px;
    color: var(--gold);
    transition: transform 0.3s ease;
    font-weight: 600;
}

.faq-item.active .faq-toggle {
    transform: rotate(45deg);
}

.faq-answer {
    max-height: 0;
    padding: 0 25px;
    overflow: hidden;
    transition: all 0.3s ease;
    background: rgba(224, 197, 143, 0.05);
}

.faq-item.active .faq-answer {
    max-height: 300px;
    padding: 25px;
}

.faq-answer p {
    color: var(--gray-text);
    line-height: 1.7;
    margin: 0;
}

/* Help Section */
.help-section {
    padding: 60px 0;
    background: linear-gradient(135deg, var(--cream) 0%, white 100%);
}

.help-card {
    max-width: 600px;
    margin: 0 auto;
    background: white;
    padding: 50px;
    border-radius: 40px;
    text-align: center;
    box-shadow: 0 30px 60px rgba(17, 34, 80, 0.1);
    border: 1px solid rgba(224, 197, 143, 0.3);
}

.help-icon {
    font-size: 60px;
    margin-bottom: 20px;
}

.help-card h2 {
    font-size: 32px;
    margin-bottom: 10px;
    color: var(--primary-blue);
    font-family: 'Cormorant Garamond', serif;
}

.help-card p {
    color: var(--gray-text);
    margin-bottom: 30px;
}

.help-options {
    display: flex;
    flex-direction: column;
    gap: 15px;
    margin-bottom: 20px;
}

.help-option {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    padding: 15px;
    background: var(--cream);
    border-radius: 50px;
    text-decoration: none;
    color: var(--primary-blue);
    font-weight: 500;
    transition: all 0.3s ease;
    border: 1px solid rgba(224, 197, 143, 0.3);
}

.help-option:hover {
    background: var(--gold);
    transform: translateY(-2px);
    color: var(--primary-blue);
}

.help-hours {
    font-size: 14px;
    color: var(--gray-text);
    margin-top: 15px;
}

/* Responsive */
@media (max-width: 768px) {
    .faq-page {
        padding-top: 80px;
    }
    
    .faq-hero {
        padding: 60px 0;
    }
    
    .faq-hero h1 {
        font-size: 32px;
    }
    
    .faq-hero p {
        font-size: 16px;
    }
    
    .faq-search {
        flex-direction: column;
        padding: 0 20px;
    }
    
    .faq-search button {
        width: 100%;
        padding: 15px;
    }
    
    .faq-categories {
        gap: 10px;
        padding: 0 20px;
    }
    
    .faq-category {
        padding: 8px 16px;
        font-size: 13px;
    }
    
    .faq-question {
        padding: 18px 20px;
    }
    
    .faq-question h3 {
        font-size: 16px;
    }
    
    .help-card {
        padding: 30px 20px;
        margin: 0 20px;
    }
    
    .help-card h2 {
        font-size: 24px;
    }
}

@media (max-width: 480px) {
    .faq-categories {
        flex-direction: column;
        align-items: center;
    }
    
    .faq-category {
        width: 100%;
        text-align: center;
    }
    
    .faq-item.active .faq-answer {
        max-height: 400px;
    }
}
</style>

<?php include 'includes/footer.php'; ?>