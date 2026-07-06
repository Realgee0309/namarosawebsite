<?php
require_once __DIR__ . '/includes/db.php';

$pageTitle = 'Namarosa Hardware — Your Partner in Building & Progress | Bungoma';
$pageDesc  = 'Quality construction materials, structural steel, tools, electrical supplies, and paints in Bungoma, Kenya. Request a quote online.';

// Fetch featured products
$stmt = $pdo->query(
    "SELECT p.*, c.name AS cat_name, c.slug AS cat_slug
     FROM products p
     JOIN categories c ON c.id = p.category_id
     WHERE p.featured = 1 AND p.in_stock = 1
     ORDER BY p.sort_order, p.id
     LIMIT 8"
);
$featuredProducts = $stmt->fetchAll();

// Fetch categories
$cats = $pdo->query("SELECT * FROM categories WHERE active = 1 ORDER BY sort_order")->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>

<!-- ===================== HERO ===================== -->
<section class="hero" aria-label="Namarosa Hardware hero">
    <div class="hero-bg"></div>
    <div class="container">
        <div class="hero-content">
            <div class="hero-badge animate-in">
                <i class="fas fa-map-marker-alt"></i>
                Bungoma's Construction Partner
            </div>
            <h1 class="hero-headline animate-in delay-1">
                Namarosa<br>
                <span class="accent">Hardware</span>
            </h1>
            <p class="hero-sub animate-in delay-2">
                Your Partner in Building &amp; Progress. Complete solutions for all your
                construction &amp; hardware needs — cement, steel, tools, electricals, and more.
            </p>
            <div class="hero-cta animate-in delay-3">
                <a href="<?= BASE_URL ?>products.php" class="btn btn-orange btn-lg">
                    <i class="fas fa-th-large"></i> View Products
                </a>
                <a href="<?= BASE_URL ?>quote.php" class="btn btn-outline btn-lg">
                    <i class="fas fa-clipboard-list"></i> Request a Quote
                </a>
            </div>
        </div>

        <div class="hero-stats animate-in">
            <div class="hero-stat">
                <strong>500+</strong>
                <span>Products in Stock</span>
            </div>
            <div class="hero-stat">
                <strong>10+</strong>
                <span>Years in Business</span>
            </div>
            <div class="hero-stat">
                <strong>1000s</strong>
                <span>Happy Contractors</span>
            </div>
        </div>
    </div>
</section>

<!-- ===================== CATEGORIES ===================== -->
<section class="categories-section" aria-label="Product categories">
    <div class="container">
        <div class="section-header centered">
            <span class="section-eyebrow">Browse by Category</span>
            <h2 class="section-title">Quality Construction Supplies</h2>
            <p class="section-sub">Everything your project needs under one roof — from foundation to finishes.</p>
        </div>
        <div class="categories-grid">
            <?php
            $catIcons = [
                'fa-building'      => 'Building Materials',
                'fa-industry'      => 'Structural Steel',
                'fa-tools'         => 'Hand & Power Tools',
                'fa-bolt'          => 'Electrical & Wiring',
                'fa-paint-roller'  => 'Paints & Finishes',
                'fa-wrench'        => 'Plumbing & Hardware',
            ];
            foreach ($cats as $cat): ?>
                <a href="<?= BASE_URL ?>products.php?cat=<?= e($cat['slug']) ?>" class="category-card">
                    <div class="cat-icon">
                        <i class="fas <?= e($cat['icon']) ?>"></i>
                    </div>
                    <span class="cat-name"><?= e($cat['name']) ?></span>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ===================== SPLIT: Director + Why Us ===================== -->
<section class="split-section" aria-label="About and why choose us">
    <div class="container">
        <div class="split-grid">

            <!-- Director Card -->
            <div class="director-card">
                <div class="director-header">
                    <div class="director-photo">
                        <i class="fas fa-user-tie"></i>
                    </div>
                    <div>
                        <p class="director-name">Emmanuel Juma</p>
                        <p class="director-title">Director</p>
                    </div>
                </div>
                <p style="color:rgba(255,255,255,0.7); font-size:0.88rem; line-height:1.7; margin-bottom:1.25rem;">
                    Namarosa Hardware is your Partner in Building Progress. "Complete Solutions for All Your
                    Construction &amp; Hardware Needs." Our company history from Namarosa Hardware heresi
                    a component vaan is variously complete solutions for all year construction needs.
                </p>
                <ul class="director-contacts">
                    <li>
                        <i class="fas fa-phone-alt"></i>
                        <a href="tel:+254710774174">+254 710 774 174</a>
                    </li>
                    <li>
                        <i class="fas fa-envelope"></i>
                        <a href="mailto:emmanueljuma1972@gmail.com">emmanueljuma1972@gmail.com</a>
                    </li>
                    <li>
                        <i class="fas fa-clock"></i>
                        <span>Mon–Sat: 7:30 AM – 6:00 PM</span>
                    </li>
                    <li>
                        <i class="fas fa-map-marker-alt"></i>
                        <span>Main Street, Bungoma</span>
                    </li>
                </ul>
                <a href="<?= BASE_URL ?>contact.php" class="btn btn-orange" style="margin-top:1.25rem;">
                    <i class="fas fa-map-marker-alt"></i> Find Our Store
                </a>
            </div>

            <!-- Why Choose Us -->
            <div>
                <span class="section-eyebrow">Why Choose Us</span>
                <h2 class="section-title" style="margin-bottom:1.75rem;">
                    Built on Quality,<br>Delivered with Care
                </h2>
                <div class="features-list">
                    <div class="feature-item">
                        <div class="feature-icon">
                            <i class="fas fa-certificate"></i>
                        </div>
                        <div class="feature-text">
                            <strong>Quality Materials</strong>
                            <p>We stock only trusted, specification-grade brands — Bamburi cement, BRC steel, and top-tier electrical supplies. No substandard shortcuts.</p>
                        </div>
                    </div>
                    <div class="feature-item">
                        <div class="feature-icon">
                            <i class="fas fa-truck"></i>
                        </div>
                        <div class="feature-text">
                            <strong>Fast Delivery Across Bungoma County</strong>
                            <p>Timely delivery of bulk materials straight to your construction site. We understand that project timelines matter.</p>
                        </div>
                    </div>
                    <div class="feature-item">
                        <div class="feature-icon">
                            <i class="fas fa-tags"></i>
                        </div>
                        <div class="feature-text">
                            <strong>Competitive Trade Pricing</strong>
                            <p>Fair, contractor-friendly pricing with volume discounts available. Request a custom quote for your project — no obligation.</p>
                        </div>
                    </div>
                    <div class="feature-item">
                        <div class="feature-icon">
                            <i class="fab fa-whatsapp"></i>
                        </div>
                        <div class="feature-text">
                            <strong>WhatsApp-Ready Support</strong>
                            <p>Quick answers for contractors on site. Chat with us directly on WhatsApp at +254 710 774 174.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ===================== POPULAR PRODUCTS ===================== -->
<section class="products-section" aria-label="Popular products">
    <div class="container">
        <div class="section-header">
            <div style="display:flex; justify-content:space-between; align-items:flex-end; flex-wrap:wrap; gap:1rem;">
                <div>
                    <span class="section-eyebrow">In Stock Now</span>
                    <h2 class="section-title">Popular Products</h2>
                </div>
                <a href="<?= BASE_URL ?>products.php" class="btn btn-outline-orange">
                    View All Products <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>

        <?php if ($featuredProducts): ?>
        <div class="products-grid">
            <?php foreach ($featuredProducts as $p): ?>
            <div class="product-card" data-search="<?= e(strtolower($p['name'] . ' ' . $p['cat_name'])) ?>" data-category="<?= e($p['cat_slug']) ?>">
                <div class="product-img">
                    <?php if ($p['image_url']): ?>
                        <img src="<?= e(imageUrl($p['image_url'])) ?>" alt="<?= e($p['name']) ?>" loading="lazy" />
                    <?php else: ?>
                        <div class="product-img-placeholder">
                            <?php
                            $icons = ['fa-building' => '🧱', 'fa-industry' => '🔩', 'fa-tools' => '🔧', 'fa-bolt' => '⚡', 'fa-paint-roller' => '🎨', 'fa-wrench' => '🪛'];
                            $emoji = $icons[$p['icon'] ?? ''] ?? '📦';
                            echo $emoji;
                            ?>
                        </div>
                    <?php endif; ?>
                    <?php if (!$p['in_stock']): ?>
                        <span class="product-badge badge-oos">Out of Stock</span>
                    <?php else: ?>
                        <span class="product-badge">In Stock</span>
                    <?php endif; ?>
                </div>
                <div class="product-body">
                    <span class="product-category"><?= e($p['cat_name']) ?></span>
                    <h3 class="product-name"><?= e($p['name']) ?></h3>
                    <?php if ($p['specifications']): ?>
                        <p class="product-spec"><?= e(substr($p['specifications'], 0, 80)) ?>...</p>
                    <?php endif; ?>
                    <?php if ($p['price']): ?>
                        <p class="product-price">
                            <?= formatKES($p['price']) ?>
                            <span class="per">/ <?= e($p['unit']) ?></span>
                        </p>
                    <?php else: ?>
                        <p class="product-price" style="font-size:0.88rem; color:var(--text-muted);">Price on request</p>
                    <?php endif; ?>
                </div>
                <div class="product-actions">
                    <button
                        class="btn btn-outline-orange btn-sm btn-add-quote"
                        data-id="<?= e($p['id']) ?>"
                        data-name="<?= e($p['name']) ?>"
                        data-unit="<?= e($p['unit']) ?>"
                        <?= !$p['in_stock'] ? 'disabled' : '' ?>>
                        <i class="fas fa-plus"></i> Add to Quote
                    </button>
                    <a href="<?= BASE_URL ?>products.php?cat=<?= e($p['cat_slug']) ?>" class="btn btn-sm" style="background:var(--off-white); color:var(--text-mid);">
                        <i class="fas fa-eye"></i>
                    </a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
            <div style="text-align:center; padding:3rem; color:var(--text-muted);">
                <i class="fas fa-box-open" style="font-size:2.5rem; margin-bottom:1rem; opacity:0.3; display:block;"></i>
                Products are being updated. Please <a href="<?= BASE_URL ?>contact.php" style="color:var(--orange);">contact us</a> for availability.
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- ===================== CTA BANNER ===================== -->
<section style="background: linear-gradient(135deg, var(--slate) 0%, var(--slate-mid) 100%); padding: 3.5rem 0; border-top: 3px solid var(--orange); border-bottom: 3px solid var(--orange);">
    <div class="container" style="text-align:center;">
        <h2 style="font-family:var(--font-display); font-size:clamp(1.8rem,4vw,2.8rem); color:var(--white); letter-spacing:0.03em; margin-bottom:0.75rem;">
            Ready to Start Your Project?
        </h2>
        <p style="color:rgba(255,255,255,0.7); max-width:540px; margin: 0 auto 2rem; font-size:1rem;">
            Send us your materials list and we'll get back with a competitive quote. Fast, free, no obligation.
        </p>
        <div style="display:flex; gap:1rem; justify-content:center; flex-wrap:wrap;">
            <a href="<?= BASE_URL ?>quote.php" class="btn btn-orange btn-lg">
                <i class="fas fa-clipboard-list"></i> Request a Free Quote
            </a>
            <a href="https://wa.me/254710774174?text=Hello%20Namarosa%20Hardware%2C%20I%20need%20a%20quote%20for..."
               class="btn btn-outline btn-lg"
               target="_blank" rel="noopener">
                <i class="fab fa-whatsapp"></i> Chat on WhatsApp
            </a>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
