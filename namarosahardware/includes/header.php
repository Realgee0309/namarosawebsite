<?php
// includes/header.php
// Call: require_once __DIR__ . '/../includes/header.php';
// Set $pageTitle and $pageDesc before including.

$pageTitle = $pageTitle ?? 'Namarosa Hardware — Your Partner in Building & Progress';
$pageDesc  = $pageDesc  ?? 'Quality construction materials, structural steel, tools, electrical supplies, and paints in Bungoma, Kenya.';

// Active nav helper
$currentPage = basename($_SERVER['PHP_SELF'], '.php');

function navClass(string $page, string $current): string {
    return $page === $current ? 'active' : '';
}

$cartCount = cartCount();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="<?= e($pageDesc) ?>" />
    <meta name="theme-color" content="#1e2d3d" />

    <title><?= e($pageTitle) ?></title>

    <script>
        window.BASE_URL = <?= json_encode(BASE_URL) ?>;
    </script>

    <!-- Favicon (inline SVG data URI) -->
    <link rel="icon" type="image/svg+xml" href="<?= BASE_URL ?>assets/images/favicon.svg" />

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Inter:wght@400;500;600;700&family=Roboto+Condensed:wght@700&display=swap" rel="stylesheet" />

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />

    <!-- Main Stylesheet -->
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style.css?v=1.0.5" />

    <!-- Open Graph -->
    <meta property="og:title"       content="<?= e($pageTitle) ?>" />
    <meta property="og:description" content="<?= e($pageDesc) ?>" />
    <meta property="og:image"       content="<?= BASE_URL ?>assets/images/og-image.jpg" />
    <meta property="og:url"         content="https://<?= SITE_DOMAIN ?>" />
    <meta property="og:type"        content="website" />
</head>
<body class="page-<?= e($currentPage) ?>">

<!-- ========== TOP BAR ========== -->
<div class="topbar">
    <div class="container topbar-inner">
        <div class="topbar-left">
            <span><i class="fas fa-clock"></i> Mon–Sat: 7:30 AM – 6:00 PM</span>
            <span><i class="fas fa-map-marker-alt"></i> Main Street, Bungoma</span>
        </div>
        <div class="topbar-right">
            <a href="tel:+254710774174"><i class="fas fa-phone-alt"></i> +254 710 774 174</a>
            <a href="mailto:emmanueljuma1972@gmail.com"><i class="fas fa-envelope"></i> emmanueljuma1972@gmail.com</a>
        </div>
    </div>
</div>

<!-- ========== HEADER / NAVBAR ========== -->
<header class="site-header" id="siteHeader">
    <div class="container header-inner">

        <!-- Logo -->
        <a href="<?= BASE_URL ?>index.php" class="logo" aria-label="Namarosa Hardware Home">
            <img src="<?= BASE_URL ?>assets/images/logo.png" alt="Namarosa Hardware Logo" class="logo-img" />
            <div class="logo-text">
                <span class="logo-name">Namarosa</span>
                <span class="logo-sub">Hardware</span>
            </div>
        </a>

        <!-- Desktop Navigation -->
        <nav class="main-nav" aria-label="Main navigation">
            <a href="<?= BASE_URL ?>index.php"    class="<?= navClass('index',    $currentPage) ?>">Home</a>
            <a href="<?= BASE_URL ?>products.php" class="<?= navClass('products', $currentPage) ?>">Materials</a>
            <a href="<?= BASE_URL ?>products.php?cat=structural-steel" class="<?= navClass('_steel', $currentPage) ?>">Steel</a>
            <a href="<?= BASE_URL ?>products.php?cat=tools"       class="<?= navClass('_tools', $currentPage) ?>">Tools</a>
            <a href="<?= BASE_URL ?>products.php?cat=electrical"  class="<?= navClass('_elec',  $currentPage) ?>">Electrical</a>
            <a href="<?= BASE_URL ?>products.php?cat=paints"      class="<?= navClass('_paint', $currentPage) ?>">Paints</a>
            <a href="<?= BASE_URL ?>products.php?cat=plumbing"    class="">Hardware Supplies</a>
            <a href="<?= BASE_URL ?>contact.php"  class="<?= navClass('contact',  $currentPage) ?>">Contact</a>
        </nav>

        <!-- Header Actions -->
        <div class="header-actions">
            <a href="<?= BASE_URL ?>products.php" class="icon-btn" title="Search Products" aria-label="Search">
                <i class="fas fa-search"></i>
            </a>
            <a href="<?= BASE_URL ?>quote.php" class="icon-btn cart-btn" title="My Quote List" aria-label="Quote list">
                <i class="fas fa-clipboard-list"></i>
                <?php if ($cartCount > 0): ?>
                    <span class="cart-badge"><?= $cartCount ?></span>
                <?php endif; ?>
            </a>
            <a href="<?= BASE_URL ?>quote.php" class="btn btn-orange btn-sm">Request a Quote</a>
        </div>

        <!-- Mobile Hamburger -->
        <button class="hamburger" id="hamburger" aria-label="Open menu" aria-expanded="false">
            <span></span><span></span><span></span>
        </button>
    </div>

    <!-- Mobile Menu -->
    <nav class="mobile-nav" id="mobileNav" aria-label="Mobile navigation">
        <a href="<?= BASE_URL ?>index.php">Home</a>
        <a href="<?= BASE_URL ?>products.php">All Products</a>
        <a href="<?= BASE_URL ?>products.php?cat=building-materials">Building Materials</a>
        <a href="<?= BASE_URL ?>products.php?cat=structural-steel">Structural Steel</a>
        <a href="<?= BASE_URL ?>products.php?cat=tools">Tools</a>
        <a href="<?= BASE_URL ?>products.php?cat=electrical">Electrical</a>
        <a href="<?= BASE_URL ?>products.php?cat=paints">Paints</a>
        <a href="<?= BASE_URL ?>products.php?cat=plumbing">Plumbing & Hardware</a>
        <a href="<?= BASE_URL ?>quote.php" class="cta-mobile">📋 Request a Quote</a>
        <a href="<?= BASE_URL ?>contact.php">Contact Us</a>
    </nav>
</header>

<!-- Flash message -->
<?php if (!empty($_SESSION['flash'])): ?>
    <div class="flash flash-<?= e($_SESSION['flash']['type']) ?>" role="alert">
        <div class="container"><?= e($_SESSION['flash']['msg']) ?></div>
        <button class="flash-close" onclick="this.parentElement.remove()" aria-label="Dismiss">×</button>
    </div>
    <?php unset($_SESSION['flash']); ?>
<?php endif; ?>
