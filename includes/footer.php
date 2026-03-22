<?php
$base_path = '';
if (strpos($_SERVER['PHP_SELF'], '/admin/') !== false) {
    $base_path = '../';
}
?>

<footer class="footer">
    <div class="container">
        <div class="footer-grid">
            <div class="footer-about">
                <div class="footer-logo">
                    <img src="<?php echo $base_path; ?>images/blue_logo.png" 
                         alt="KidsBookery Logo" 
                         class="footer-logo-img"
                         style="width: 100px; height: auto;">
                </div>
                <p>Creating magical moments through books since 2020. We believe every child deserves to explore, imagine, and create without limits.</p>
                <div class="footer-social">
                    <a href="#" target="_blank" rel="noopener" aria-label="Instagram">📷</a>
                    <a href="#" target="_blank" rel="noopener" aria-label="Facebook">📘</a>
                    <a href="#" target="_blank" rel="noopener" aria-label="Pinterest">📌</a>
                    <a href="#" target="_blank" rel="noopener" aria-label="Twitter">🐦</a>
                </div>
            </div>
            
            <div class="footer-section">
                <h4>Shop</h4>
                <ul>
                    <li><a href="<?php echo $base_path; ?>index.php">Home</a></li>
                    <li><a href="<?php echo $base_path; ?>coloring.php">Coloring Books</a></li>
                    <li><a href="<?php echo $base_path; ?>puzzles.php">Puzzle Books</a></li>
                    <li><a href="<?php echo $base_path; ?>educational.php">Educational Games</a></li>
                    <li><a href="<?php echo $base_path; ?>member.php">Member Printables</a></li>
                </ul>
            </div>
            
            <div class="footer-section">
                <h4>Support</h4>
                <ul>
                    <li><a href="<?php echo $base_path; ?>faq.php">FAQ</a></li>
                    <li><a href="<?php echo $base_path; ?>shipping.php">Shipping & Returns</a></li>
                    <li><a href="<?php echo $base_path; ?>contact.php">Contact Us</a></li>
                    <li><a href="<?php echo $base_path; ?>privacy.php">Privacy Policy</a></li>
                    <li><a href="<?php echo $base_path; ?>terms.php">Terms of Service</a></li>
                </ul>
            </div>
            
            <div class="footer-section">
                <h4>Connect</h4>
                <div class="footer-contact">
                    <p><span>📧</span> <a href="mailto:hello@kidsbookery.com">hello@kidsbookery.com</a></p>
                    <p><span>📞</span> <a href="tel:+15551234567">+1 (555) 123-4567</a></p>
                    <p><span>📍</span> 123 Imagination Lane, Storyville</p>
                </div>
                
                <div class="footer-newsletter">
                    <h5>Get 20% off</h5>
                    <form class="footer-newsletter-form" onsubmit="event.preventDefault(); alert('Thanks for subscribing!');">
                        <input type="email" placeholder="Email address">
                        <button type="submit">→</button>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="footer-bottom">
            <div>© <?php echo date('Y'); ?> KidsBookery. All rights reserved.</div>
            <div class="footer-bottom-links">
                <a href="<?php echo $base_path; ?>privacy.php">Privacy</a>
                <a href="<?php echo $base_path; ?>terms.php">Terms</a>
                <a href="<?php echo $base_path; ?>sitemap.php">Sitemap</a>
                <a href="<?php echo $base_path; ?>accessibility.php">Accessibility</a>
            </div>
        </div>
    </div>
</footer>
</body>
</html>