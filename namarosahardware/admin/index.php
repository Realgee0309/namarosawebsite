<?php
require_once __DIR__ . '/../includes/db.php';
requireAdmin();

$adminPageTitle = 'Dashboard';

// Stats
$totalQuotes   = $pdo->query("SELECT COUNT(*) FROM quotes")->fetchColumn();
$newQuotes     = $pdo->query("SELECT COUNT(*) FROM quotes WHERE status = 'new'")->fetchColumn();
$totalProducts = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
$inStock       = $pdo->query("SELECT COUNT(*) FROM products WHERE in_stock = 1")->fetchColumn();

// Recent quotes
$recentQuotes = $pdo->query(
    "SELECT * FROM quotes ORDER BY created_at DESC LIMIT 10"
)->fetchAll();

require_once __DIR__ . '/partials/admin_header.php';
?>

<div style="margin-bottom:1.5rem; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:1rem;">
    <div>
        <h1 style="font-family:'Bebas Neue',sans-serif; font-size:2rem; color:var(--slate); letter-spacing:0.03em;">Dashboard</h1>
        <p style="color:var(--text-muted); font-size:0.88rem;"><?= date('l, d F Y') ?> · Logged in as <?= e($_SESSION['admin_name'] ?? 'Admin') ?></p>
    </div>
    <a href="<?= BASE_URL ?>admin/quotes.php?status=new" class="btn btn-orange">
        <i class="fas fa-bell"></i>
        <?= $newQuotes ?> New Quote<?= $newQuotes != 1 ? 's' : '' ?>
    </a>
</div>

<!-- Stats -->
<div class="stat-grid">
    <div class="stat-card">
        <div class="stat-num"><?= $totalQuotes ?></div>
        <div class="stat-label">Total Quotes</div>
    </div>
    <div class="stat-card" style="border-top-color:#dc2626;">
        <div class="stat-num" style="color:#dc2626;"><?= $newQuotes ?></div>
        <div class="stat-label">New / Unread</div>
    </div>
    <div class="stat-card" style="border-top-color:#2563eb;">
        <div class="stat-num" style="color:#2563eb;"><?= $totalProducts ?></div>
        <div class="stat-label">Total Products</div>
    </div>
    <div class="stat-card" style="border-top-color:#16a34a;">
        <div class="stat-num" style="color:#16a34a;"><?= $inStock ?></div>
        <div class="stat-label">In Stock</div>
    </div>
</div>

<!-- Recent Quotes -->
<div class="admin-card">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1rem;">
        <h2 style="border-bottom:none; padding-bottom:0; margin-bottom:0;">Recent Quote Requests</h2>
        <a href="<?= BASE_URL ?>admin/quotes.php" class="btn btn-sm btn-slate">View All</a>
    </div>

    <?php if ($recentQuotes): ?>
    <div style="overflow-x:auto;">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Reference</th>
                    <th>Name / Company</th>
                    <th>Phone</th>
                    <th>Delivery Location</th>
                    <th>Status</th>
                    <th>Received</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($recentQuotes as $q): ?>
                <tr>
                    <td><strong style="color:var(--orange); font-size:0.82rem;"><?= e($q['reference']) ?></strong></td>
                    <td>
                        <strong><?= e($q['full_name']) ?></strong>
                        <?php if ($q['company_name']): ?>
                            <br><small style="color:var(--text-muted);"><?= e($q['company_name']) ?></small>
                        <?php endif; ?>
                    </td>
                    <td><a href="tel:<?= e($q['phone']) ?>" style="color:var(--orange); font-weight:600;"><?= e($q['phone']) ?></a></td>
                    <td style="max-width:150px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;"><?= e($q['delivery_location']) ?></td>
                    <td><span class="status-badge status-<?= e($q['status']) ?>"><?= e($q['status']) ?></span></td>
                    <td style="font-size:0.8rem; color:var(--text-muted);"><?= date('d M, H:i', strtotime($q['created_at'])) ?></td>
                    <td>
                        <a href="<?= BASE_URL ?>admin/quotes.php?view=<?= (int)$q['id'] ?>" class="btn btn-sm btn-slate">View</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php else: ?>
        <div style="text-align:center; padding:3rem; color:var(--text-muted);">
            <i class="fas fa-clipboard-list" style="font-size:2.5rem; margin-bottom:0.75rem; opacity:0.2; display:block;"></i>
            No quote requests yet. They'll appear here when customers submit the form.
        </div>
    <?php endif; ?>
</div>

<!-- Quick Actions -->
<div style="display:grid; grid-template-columns:repeat(3,1fr); gap:1rem;">
    <a href="<?= BASE_URL ?>admin/products.php?action=add" class="admin-card" style="text-decoration:none; text-align:center; padding:1.5rem; cursor:pointer; transition:box-shadow 200ms;" onmouseover="this.style.boxShadow='var(--shadow-md)'" onmouseout="this.style.boxShadow=''">
        <i class="fas fa-plus-circle" style="font-size:1.8rem; color:var(--orange); display:block; margin-bottom:0.5rem;"></i>
        <strong style="color:var(--slate);">Add New Product</strong>
        <p style="font-size:0.8rem; color:var(--text-muted); margin-top:0.25rem;">Add items to the catalog</p>
    </a>
    <a href="<?= BASE_URL ?>admin/quotes.php?status=new" class="admin-card" style="text-decoration:none; text-align:center; padding:1.5rem; cursor:pointer; transition:box-shadow 200ms;" onmouseover="this.style.boxShadow='var(--shadow-md)'" onmouseout="this.style.boxShadow=''">
        <i class="fas fa-envelope-open" style="font-size:1.8rem; color:#dc2626; display:block; margin-bottom:0.5rem;"></i>
        <strong style="color:var(--slate);">New Quote Requests</strong>
        <p style="font-size:0.8rem; color:var(--text-muted); margin-top:0.25rem;"><?= $newQuotes ?> waiting for response</p>
    </a>
    <a href="<?= BASE_URL ?>admin/settings.php" class="admin-card" style="text-decoration:none; text-align:center; padding:1.5rem; cursor:pointer; transition:box-shadow 200ms;" onmouseover="this.style.boxShadow='var(--shadow-md)'" onmouseout="this.style.boxShadow=''">
        <i class="fas fa-cog" style="font-size:1.8rem; color:var(--text-muted); display:block; margin-bottom:0.5rem;"></i>
        <strong style="color:var(--slate);">Site Settings</strong>
        <p style="font-size:0.8rem; color:var(--text-muted); margin-top:0.25rem;">Update hours, contacts, info</p>
    </a>
</div>

</main>
</body>
</html>
