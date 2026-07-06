<?php
// admin/partials/admin_header.php
// Requires: $adminPageTitle
requireAdmin();
$adminUser = $_SESSION['admin_name'] ?? 'Admin';
$adminPage = basename($_SERVER['PHP_SELF'], '.php');
function aNav(string $file, string $current): string {
    return $file === $current ? 'active' : '';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= e($adminPageTitle ?? 'Admin') ?> — Namarosa Hardware CMS</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style.css?v=1.0.5" />
</head>
<body class="admin-body">

<nav class="admin-nav">
    <div style="display:flex; align-items:center; gap:1rem; flex-wrap:wrap;">
        <span class="admin-nav-logo" style="display:flex; align-items:center; gap:0.5rem;">
            <img src="<?= BASE_URL ?>assets/images/logo.png" alt="Logo" style="height:32px; width:auto; filter:brightness(0) invert(1);" />
            <span>Admin</span>
        </span>
        <div style="display:flex; gap:0.25rem; flex-wrap:wrap;">
            <a href="<?= BASE_URL ?>admin/index.php"    class="<?= aNav('index',    $adminPage) ?>"><i class="fas fa-home"></i> Dashboard</a>
            <a href="<?= BASE_URL ?>admin/quotes.php"   class="<?= aNav('quotes',   $adminPage) ?>"><i class="fas fa-clipboard-list"></i> Quotes</a>
            <a href="<?= BASE_URL ?>admin/products.php" class="<?= aNav('products', $adminPage) ?>"><i class="fas fa-boxes"></i> Products</a>
            <a href="<?= BASE_URL ?>admin/settings.php" class="<?= aNav('settings', $adminPage) ?>"><i class="fas fa-cog"></i> Settings</a>
            <a href="<?= BASE_URL ?>index.php" target="_blank"><i class="fas fa-external-link-alt"></i> View Site</a>
        </div>
    </div>
    <div style="display:flex; align-items:center; gap:1rem;">
        <span style="color:rgba(255,255,255,0.6); font-size:0.82rem;"><i class="fas fa-user-circle"></i> <?= e($adminUser) ?></span>
        <a href="<?= BASE_URL ?>admin/logout.php" style="color:#f87171; font-size:0.82rem;"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>
</nav>

<main class="admin-main">
