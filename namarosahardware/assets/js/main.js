/* ============================================================
   NAMAROSA HARDWARE — Main JavaScript
   ============================================================ */

'use strict';

// ---- DOM Ready ----
document.addEventListener('DOMContentLoaded', () => {
    initMobileMenu();
    initBackToTop();
    initScrollHeader();
    initAddToQuote();
    initCatalogSearch();
    initQuoteCartRemove();
    initAnimations();
    initFlashDismiss();
});

// ============================================================
// Mobile Menu
// ============================================================
function initMobileMenu() {
    const btn  = document.getElementById('hamburger');
    const nav  = document.getElementById('mobileNav');
    if (!btn || !nav) return;

    btn.addEventListener('click', () => {
        const isOpen = nav.classList.toggle('open');
        btn.classList.toggle('open', isOpen);
        btn.setAttribute('aria-expanded', isOpen);
        document.body.style.overflow = isOpen ? 'hidden' : '';
    });

    // Close on outside click
    document.addEventListener('click', e => {
        if (!btn.contains(e.target) && !nav.contains(e.target)) {
            nav.classList.remove('open');
            btn.classList.remove('open');
            btn.setAttribute('aria-expanded', 'false');
            document.body.style.overflow = '';
        }
    });
}

// ============================================================
// Sticky header shadow
// ============================================================
function initScrollHeader() {
    const header = document.getElementById('siteHeader');
    if (!header) return;
    const onScroll = () => {
        header.style.boxShadow = window.scrollY > 50
            ? '0 4px 20px rgba(0,0,0,0.3)'
            : '';
    };
    window.addEventListener('scroll', onScroll, { passive: true });
}

// ============================================================
// Back to Top
// ============================================================
function initBackToTop() {
    const btn = document.getElementById('backToTop');
    if (!btn) return;

    window.addEventListener('scroll', () => {
        btn.classList.toggle('visible', window.scrollY > 400);
    }, { passive: true });

    btn.addEventListener('click', () => {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });
}

// ============================================================
// Add-to-Quote via AJAX
// ============================================================
function initAddToQuote() {
    document.querySelectorAll('.btn-add-quote').forEach(btn => {
        btn.addEventListener('click', async function (e) {
            e.preventDefault();

            const productId = this.dataset.id;
            const productName = this.dataset.name;
            const qty = 1;

            // Visual feedback immediately
            const original = this.innerHTML;
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            this.disabled = true;

            try {
                const baseUrl = window.BASE_URL || '/';
                const res = await fetch(baseUrl + 'api/cart.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ action: 'add', product_id: productId, qty })
                });
                const data = await res.json();

                if (data.success) {
                    showToast(`${productName} added to quote list!`);
                    updateCartBadge(data.cart_count);
                    this.innerHTML = '<i class="fas fa-check"></i> Added';
                    this.classList.add('btn-slate');
                    this.classList.remove('btn-outline-orange');
                    setTimeout(() => {
                        this.innerHTML = original;
                        this.classList.remove('btn-slate');
                        this.classList.add('btn-outline-orange');
                        this.disabled = false;
                    }, 2500);
                } else {
                    this.innerHTML = original;
                    this.disabled = false;
                    showToast('Could not add item. Try again.', true);
                }
            } catch {
                this.innerHTML = original;
                this.disabled = false;
                showToast('Network error. Please try again.', true);
            }
        });
    });
}

function updateCartBadge(count) {
    const badge = document.querySelector('.cart-badge');
    const cartBtn = document.querySelector('.cart-btn');
    if (!cartBtn) return;

    if (count > 0) {
        if (badge) {
            badge.textContent = count;
        } else {
            const newBadge = document.createElement('span');
            newBadge.className = 'cart-badge';
            newBadge.textContent = count;
            cartBtn.appendChild(newBadge);
        }
    } else if (badge) {
        badge.remove();
    }
}

// ============================================================
// Toast Notification
// ============================================================
function showToast(msg, isError = false) {
    const toast = document.getElementById('quoteToast');
    const msgEl = document.getElementById('toastMsg');
    if (!toast || !msgEl) return;

    msgEl.textContent = msg;
    toast.style.background = isError ? '#dc2626' : '';
    toast.classList.add('show');

    clearTimeout(toast._timer);
    toast._timer = setTimeout(() => toast.classList.remove('show'), 3000);
}

// ============================================================
// Catalog Search (client-side filter)
// ============================================================
function initCatalogSearch() {
    const input = document.getElementById('catalogSearch');
    if (!input) return;

    input.addEventListener('input', debounce(function () {
        const query = this.value.toLowerCase().trim();
        const cards = document.querySelectorAll('.product-card[data-search]');
        let visible = 0;

        cards.forEach(card => {
            const text = card.dataset.search;
            const match = !query || text.includes(query);
            card.style.display = match ? '' : 'none';
            if (match) visible++;
        });

        const info = document.getElementById('catalogCount');
        if (info) {
            info.textContent = query
                ? `${visible} result${visible !== 1 ? 's' : ''} for "${this.value}"`
                : `Showing ${visible} product${visible !== 1 ? 's' : ''}`;
        }
    }, 250));
}

// ============================================================
// Quote Cart — remove items
// ============================================================
function initQuoteCartRemove() {
    document.querySelectorAll('.cart-item-remove').forEach(btn => {
        btn.addEventListener('click', async function () {
            const key = this.dataset.key;
            const row = this.closest('.cart-item');

            try {
                const baseUrl = window.BASE_URL || '/';
                const res = await fetch(baseUrl + 'api/cart.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ action: 'remove', key })
                });
                const data = await res.json();
                if (data.success) {
                    row.style.opacity = '0';
                    row.style.transition = 'opacity 0.25s';
                    setTimeout(() => {
                        row.remove();
                        updateCartBadge(data.cart_count);
                        // Show empty state if needed
                        const remaining = document.querySelectorAll('.cart-item').length;
                        if (remaining === 0) {
                            const cart = document.querySelector('.quote-cart');
                            if (cart) {
                                cart.innerHTML = `
                                    <p class="quote-cart-heading">Items from Catalog</p>
                                    <div class="cart-empty">
                                        <i class="fas fa-clipboard-list"></i>
                                        No items added yet. Browse products and click "Add to Quote".
                                    </div>`;
                            }
                        }
                    }, 250);
                }
            } catch {
                showToast('Could not remove item.', true);
            }
        });
    });
}

// ============================================================
// Quantity input on quote page
// ============================================================
document.querySelectorAll('.qty-input').forEach(input => {
    input.addEventListener('change', function () {
        const val = parseInt(this.value);
        if (isNaN(val) || val < 1) this.value = 1;
    });
});

// ============================================================
// Scroll animations
// ============================================================
function initAnimations() {
    if (!('IntersectionObserver' in window)) return;

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate-in');
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.1, rootMargin: '0px 0px -40px 0px' });

    document.querySelectorAll('.product-card, .category-card, .feature-item, .cdetail-card').forEach(el => {
        observer.observe(el);
    });
}

// ============================================================
// Flash message auto-dismiss
// ============================================================
function initFlashDismiss() {
    const flash = document.querySelector('.flash');
    if (flash) {
        setTimeout(() => {
            flash.style.transition = 'opacity 0.5s';
            flash.style.opacity = '0';
            setTimeout(() => flash.remove(), 500);
        }, 5000);
    }
}

// ============================================================
// Quote form — character counter for materials textarea
// ============================================================
const materialsArea = document.getElementById('materials_text');
const charCount = document.getElementById('charCount');
if (materialsArea && charCount) {
    materialsArea.addEventListener('input', function () {
        charCount.textContent = this.value.length;
    });
}

// ============================================================
// Product image lazy load placeholder
// ============================================================
document.querySelectorAll('img[data-src]').forEach(img => {
    img.src = img.dataset.src;
    img.removeAttribute('data-src');
});

// ============================================================
// Category filter tabs (products page)
// ============================================================
const filterTabs = document.querySelectorAll('.filter-tab');
filterTabs.forEach(tab => {
    tab.addEventListener('click', function () {
        filterTabs.forEach(t => t.classList.remove('active'));
        this.classList.add('active');

        const target = this.dataset.filter;
        document.querySelectorAll('.product-card[data-category]').forEach(card => {
            const show = target === 'all' || card.dataset.category === target;
            card.style.display = show ? '' : 'none';
        });
    });
});

// ============================================================
// Phone number formatting (Kenya)
// ============================================================
const phoneInputs = document.querySelectorAll('input[type="tel"]');
phoneInputs.forEach(input => {
    input.addEventListener('input', function () {
        // Strip non-digits
        let val = this.value.replace(/\D/g, '');
        // Limit length
        if (val.length > 12) val = val.slice(0, 12);
        this.value = val;
    });
});

// ============================================================
// Utility: debounce
// ============================================================
function debounce(fn, delay) {
    let timer;
    return function (...args) {
        clearTimeout(timer);
        timer = setTimeout(() => fn.apply(this, args), delay);
    };
}
