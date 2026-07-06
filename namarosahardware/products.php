<?php
require_once __DIR__ . '/includes/db.php';

$catSlug = $_GET['cat'] ?? 'all';

// Get all categories
$categories = $pdo->query("SELECT * FROM categories WHERE active = 1 ORDER BY sort_order")->fetchAll();

// Map slug to id
$activeCat = null;
foreach ($categories as $c) {
    if ($c['slug'] === $catSlug) { $activeCat = $c; break; }
}

// Build query
if ($activeCat) {
    $stmt = $pdo->prepare(
        "SELECT p.*, c.name AS cat_name, c.slug AS cat_slug, c.icon AS cat_icon
         FROM products p
         JOIN categories c ON c.id = p.category_id
         WHERE p.category_id = ?
         ORDER BY p.in_stock DESC, p.sort_order, p.name"
    );
    $stmt->execute([$activeCat['id']]);
} else {
    $stmt = $pdo->query(
        "SELECT p.*, c.name AS cat_name, c.slug AS cat_slug, c.icon AS cat_icon
         FROM products p
         JOIN categories c ON c.id = p.category_id
         WHERE c.active = 1
         ORDER BY c.sort_order, p.in_stock DESC, p.name"
    );
}
$products = $stmt->fetchAll();

// Counts per category
$countsStmt = $pdo->query(
    "SELECT c.slug, COUNT(p.id) as cnt
     FROM categories c
     LEFT JOIN products p ON p.category_id = c.id AND c.active = 1
     GROUP BY c.id, c.slug"
);
$counts = [];
foreach ($countsStmt->fetchAll() as $row) {
    $counts[$row['slug']] = $row['cnt'];
}
$totalCount = array_sum($counts);

$pageTitle  = $activeCat
    ? e($activeCat['name']) . ' — Namarosa Hardware Bungoma'
    : 'Product Catalog — Namarosa Hardware Bungoma';
$pageDesc   = 'Browse our full range of quality construction materials, steel, tools, electrical supplies and paints at Namarosa Hardware, Bungoma.';

require_once __DIR__ . '/includes/header.php';
?>

<!-- Page Hero -->
<section class="page-hero">
    <div class="container page-hero-content">
        <h1>
            <span><?= $activeCat ? e($activeCat['name']) : 'All Products' ?></span>
        </h1>
        <div class="breadcrumb">
            <a href="<?= BASE_URL ?>index.php">Home</a>
            <i class="fas fa-chevron-right"></i>
            <?php if ($activeCat): ?>
                <a href="<?= BASE_URL ?>products.php">Products</a>
                <i class="fas fa-chevron-right"></i>
                <span><?= e($activeCat['name']) ?></span>
            <?php else: ?>
                <span>Products</span>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Catalog -->
<div class="container">
    <div class="catalog-layout">

        <!-- Sidebar -->
        <aside class="catalog-sidebar">
            <h3 class="sidebar-heading"><i class="fas fa-list"></i> Our Product Categories</h3>

            <div class="sidebar-group">
                <a href="<?= BASE_URL ?>products.php" class="sidebar-link <?= $catSlug === 'all' ? 'active' : '' ?>">
                    All Products
                    <span class="count"><?= $totalCount ?></span>
                </a>
            </div>

            <div class="sidebar-group">
                <p class="sidebar-group-title">Categories</p>
                <?php foreach ($categories as $cat): ?>
                    <a href="<?= BASE_URL ?>products.php?cat=<?= e($cat['slug']) ?>"
                       class="sidebar-link <?= $cat['slug'] === $catSlug ? 'active' : '' ?>">
                        <span><i class="fas <?= e($cat['icon']) ?>" style="width:16px; color:var(--orange); margin-right:0.35rem;"></i><?= e($cat['name']) ?></span>
                        <span class="count"><?= $counts[$cat['slug']] ?? 0 ?></span>
                    </a>
                <?php endforeach; ?>
            </div>

            <div style="margin-top:1.5rem; padding:1.25rem; background:var(--slate); border-radius:var(--radius); color:var(--white);">
                <p style="font-size:0.82rem; font-weight:700; color:var(--orange); text-transform:uppercase; letter-spacing:0.06em; margin-bottom:0.5rem;">Need a custom quote?</p>
                <p style="font-size:0.8rem; color:rgba(255,255,255,0.7); line-height:1.5; margin-bottom:0.85rem;">Add items to your quote list and submit — we'll respond quickly with prices.</p>
                <a href="<?= BASE_URL ?>quote.php" class="btn btn-orange btn-sm btn-full">
                    <i class="fas fa-clipboard-list"></i> View Quote List
                </a>
            </div>

            <!-- Quick contact -->
            <div style="margin-top:1rem; font-size:0.82rem; color:var(--text-muted); line-height:1.6;">
                <p><strong style="color:var(--slate);">Can't find what you need?</strong></p>
                <p><a href="tel:+254710774174" style="color:var(--orange);">📞 +254 710 774 174</a></p>
                <p><a href="https://wa.me/254710774174" target="_blank" style="color:var(--orange);">💬 WhatsApp us</a></p>
            </div>
        </aside>

        <!-- Main Grid -->
        <div class="catalog-main">
            <!-- Toolbar -->
            <div class="catalog-toolbar">
                <div class="catalog-search">
                    <input
                        type="search"
                        id="catalogSearch"
                        placeholder="Search products..."
                        aria-label="Search products"
                    />
                    <i class="fas fa-search"></i>
                </div>
                <p class="catalog-info" id="catalogCount">
                    Showing <?= count($products) ?> product<?= count($products) !== 1 ? 's' : '' ?>
                    <?= $activeCat ? 'in ' . e($activeCat['name']) : '' ?>
                </p>
            </div>

            <!-- Products -->
            <?php if ($products): ?>
            <div class="catalog-grid">
                <?php
                $emojiMap = [
                    'building-materials'  => '🧱',
                    'structural-steel'    => '🔩',
                    'tools'               => '🔧',
                    'electrical'          => '⚡',
                    'paints'              => '🎨',
                    'plumbing'            => '🪛',
                ];
                foreach ($products as $p):
                    $emoji = $emojiMap[$p['cat_slug']] ?? '📦';
                ?>
                <div class="product-card"
                     data-search="<?= e(strtolower($p['name'] . ' ' . $p['cat_name'] . ' ' . $p['description'])) ?>"
                     data-category="<?= e($p['cat_slug']) ?>">
                    <div class="product-img">
                        <?php if ($p['image_url']): ?>
                            <img src="<?= e(imageUrl($p['image_url'])) ?>" alt="<?= e($p['name']) ?>" loading="lazy" />
                        <?php else: ?>
                            <div class="product-img-placeholder" style="font-size:3rem;"><?= $emoji ?></div>
                        <?php endif; ?>
                        <?php if (!$p['in_stock']): ?>
                            <span class="product-badge badge-oos">Out of Stock</span>
                        <?php endif; ?>
                    </div>
                    <div class="product-body">
                        <span class="product-category"><?= e($p['cat_name']) ?></span>
                        <h3 class="product-name"><?= e($p['name']) ?></h3>
                        <?php if ($p['description']): ?>
                            <p class="product-spec"><?= e(substr($p['description'], 0, 90)) ?>...</p>
                        <?php endif; ?>
                        <?php if ($p['specifications']): ?>
                            <p class="product-spec" style="font-style:italic; font-size:0.75rem; color:var(--text-muted);"><?= e(substr($p['specifications'], 0, 100)) ?></p>
                        <?php endif; ?>
                        <?php if ($p['price']): ?>
                            <p class="product-price">
                                <?= formatKES($p['price']) ?>
                                <span class="per">/ <?= e($p['unit']) ?></span>
                            </p>
                        <?php else: ?>
                            <p class="product-price" style="font-family:var(--font-body); font-size:0.85rem; color:var(--text-muted); font-style:italic;">Price on request</p>
                        <?php endif; ?>
                    </div>
                    <div class="product-actions">
                        <button
                            class="btn btn-orange btn-sm btn-add-quote"
                            data-id="<?= e($p['id']) ?>"
                            data-name="<?= e($p['name']) ?>"
                            data-unit="<?= e($p['unit']) ?>"
                            <?= !$p['in_stock'] ? 'disabled style="opacity:0.5; cursor:not-allowed;"' : '' ?>>
                            <i class="fas fa-plus"></i>
                            <?= $p['in_stock'] ? 'Add to Quote' : 'Out of Stock' ?>
                        </button>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <?php else: ?>
            <div style="text-align:center; padding:5rem 2rem; color:var(--text-muted);">
                <i class="fas fa-search" style="font-size:3rem; margin-bottom:1rem; opacity:0.2; display:block;"></i>
                <h3 style="color:var(--slate); margin-bottom:0.5rem;">No products found</h3>
                <p style="margin-bottom:1.5rem;">We may stock this item — contact us to confirm availability.</p>
                <a href="tel:+254710774174" class="btn btn-orange">
                    <i class="fas fa-phone-alt"></i> Call Us
                </a>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Quote CTA -->
<div style="background:var(--off-white); border-top:1px solid var(--border); padding:2.5rem 0;">
    <div class="container" style="display:flex; align-items:center; justify-content:space-between; gap:1.5rem; flex-wrap:wrap;">
        <div>
            <h3 style="font-family:var(--font-display); font-size:1.5rem; color:var(--slate); letter-spacing:0.03em;">
                Found what you need?
            </h3>
            <p style="color:var(--text-muted); font-size:0.9rem; margin-top:0.25rem;">
                Your quote list has <strong id="quoteCount"><?= cartCount() ?></strong> item<?= cartCount() !== 1 ? 's' : '' ?>. Submit it and we'll respond with pricing.
            </p>
        </div>
        <div style="display:flex; gap:0.75rem; flex-wrap:wrap;">
            <a href="<?= BASE_URL ?>quote.php" class="btn btn-orange">
                <i class="fas fa-clipboard-list"></i> View & Submit Quote
            </a>
            <a href="https://wa.me/254710774174" target="_blank" rel="noopener" class="btn btn-slate">
                <i class="fab fa-whatsapp"></i> WhatsApp Us
            </a>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
