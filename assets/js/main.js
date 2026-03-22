// assets/js/main.js 

document.addEventListener('DOMContentLoaded', function() {
    
    // =====  NAVIGATION SCROLL BEHAVIOR =====
    const nav = document.querySelector('.floating-nav');
    let lastScrollTop = 0;
    let scrollThreshold = 50; // Minimum scroll to trigger hide
    let ticking = false;
    
    if (nav) {
        window.addEventListener('scroll', function() {
            lastScrollTop = window.scrollY;
            
            if (!ticking) {
                window.requestAnimationFrame(function() {
                    handleNavScroll(lastScrollTop);
                    ticking = false;
                });
                ticking = true;
            }
        });
        
        function handleNavScroll(scrollTop) {
            if (scrollTop > scrollThreshold) {
                // Scrolled down past threshold - hide nav
                nav.classList.add('hide');
                nav.classList.remove('show');
            } else {
                // At the top - show nav
                nav.classList.remove('hide');
                nav.classList.add('show');
            }
        }
        
        // Show nav when scrolling up
        let prevScrollTop = 0;
        window.addEventListener('scroll', function() {
            const currentScrollTop = window.scrollY;
            
            if (currentScrollTop < prevScrollTop && currentScrollTop > scrollThreshold) {
                // Scrolling up - show nav
                nav.classList.remove('hide');
                nav.classList.add('show');
            }
            
            prevScrollTop = currentScrollTop;
        });
    }
    
    // ===== SET ACTIVE NAV LINK =====
    function setActiveNavLink() {
        const urlParams = new URLSearchParams(window.location.search);
        const category = urlParams.get('category');
        const currentPage = window.location.pathname.split('/').pop() || 'index.php';
        
        // Update main nav links
        document.querySelectorAll('.nav-links a').forEach(link => {
            link.classList.remove('active');
            
            const href = link.getAttribute('href');
            
            if (currentPage === 'index.php' && !category && href === 'index.php') {
                link.classList.add('active');
            }
            
            if (category && href.includes(`category=${category}`)) {
                link.classList.add('active');
            }
        });
        
        // Update category pills
        document.querySelectorAll('.pill').forEach(pill => {
            pill.classList.remove('active');
            
            if (category && pill.getAttribute('href')?.includes(`category=${category}`)) {
                pill.classList.add('active');
            } else if (!category && pill.getAttribute('href') === 'index.php') {
                pill.classList.add('active');
            }
        });
    }
    
    setActiveNavLink();
    
    // ===== SMOOTH SCROLL FOR ANCHOR LINKS =====
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
    
    // ===== ADD TO CART ANIMATION =====
    document.querySelectorAll('.btn-add, .btn-add-to-cart').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.stopPropagation();
            const originalText = this.innerHTML;
            this.innerHTML = '✓ Added!';
            this.style.background = 'var(--coffee)';
            this.style.color = 'white';
            this.style.borderColor = 'var(--coffee)';
            
            setTimeout(() => {
                this.innerHTML = originalText;
                this.style.background = '';
                this.style.color = '';
                this.style.borderColor = '';
            }, 2000);
        });
    });
    
    // ===== PRODUCT CARD CLICK =====
    document.querySelectorAll('.product-card').forEach(card => {
        card.addEventListener('click', function(e) {
            if (!e.target.classList.contains('btn-add') && !e.target.closest('.btn-add')) {
                const href = this.getAttribute('data-href') || this.getAttribute('onclick')?.match(/'([^']+)'/)?.[1];
                if (href) window.location.href = href;
            }
        });
    });
});

// ===== QUANTITY SELECTOR =====
function incrementQuantity(inputId) {
    const input = document.getElementById(inputId);
    if (input) {
        input.value = parseInt(input.value) + 1;
    }
}

function decrementQuantity(inputId) {
    const input = document.getElementById(inputId);
    if (input && parseInt(input.value) > 1) {
        input.value = parseInt(input.value) - 1;
    }
}

function confirmClear() {
    return confirm('Are you sure you want to clear your cart?');
}