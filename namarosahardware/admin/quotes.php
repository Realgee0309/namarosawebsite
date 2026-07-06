<?php
require_once __DIR__ . '/../includes/db.php';
requireAdmin();
$adminPageTitle = 'Quote Requests';

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $qid    = (int)$_POST['quote_id'];
    $status = $_POST['new_status'] ?? 'new';
    $notes  = trim($_POST['admin_notes'] ?? '');
    $allowed = ['new', 'read', 'responded', 'closed'];
    if (in_array($status, $allowed)) {
        $pdo->prepare("UPDATE quotes SET status = ?, admin_notes = ? WHERE id = ?")->execute([$status, $notes, $qid]);
        $_SESSION['flash'] = ['type' => 'success', 'msg' => 'Quote status updated.'];
    }
    redirect(BASE_URL . 'admin/quotes.php');
}

// View single quote
$viewQuote = null;
if (isset($_GET['view'])) {
    $vid = (int)$_GET['view'];
    $viewQuote = $pdo->prepare("SELECT * FROM quotes WHERE id = ?");
    $viewQuote->execute([$vid]);
    $viewQuote = $viewQuote->fetch();
    if ($viewQuote && $viewQuote['status'] === 'new') {
        $pdo->prepare("UPDATE quotes SET status = 'read' WHERE id = ?")->execute([$vid]);
        $viewQuote['status'] = 'read';
    }
    // Get line items
    $lineItems = $pdo->prepare("SELECT * FROM quote_items WHERE quote_id = ?");
    $lineItems->execute([$vid]);
    $lineItems = $lineItems->fetchAll();
}

// Filters
$filterStatus = $_GET['status'] ?? 'all';
$allowed = ['all', 'new', 'read', 'responded', 'closed'];
if (!in_array($filterStatus, $allowed)) $filterStatus = 'all';

$where = $filterStatus !== 'all' ? "WHERE status = " . $pdo->quote($filterStatus) : '';
$quotes = $pdo->query("SELECT * FROM quotes {$where} ORDER BY created_at DESC")->fetchAll();

require_once __DIR__ . '/partials/admin_header.php';
?>

<div style="margin-bottom:1.5rem; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:1rem;">
    <h1 style="font-family:'Bebas Neue',sans-serif; font-size:2rem; color:var(--slate); letter-spacing:0.03em;">Quote Requests</h1>
    <a href="<?= BASE_URL ?>admin/index.php" class="btn btn-sm" style="background:var(--off-white); color:var(--text-mid);">← Dashboard</a>
</div>

<!-- Flash -->
<?php if (!empty($_SESSION['flash'])): ?>
    <div class="flash flash-<?= e($_SESSION['flash']['type']) ?>" style="border-radius:var(--radius); margin-bottom:1rem; border:none; border-left:4px solid var(--success);">
        <div><?= e($_SESSION['flash']['msg']) ?></div>
    </div>
    <?php unset($_SESSION['flash']); ?>
<?php endif; ?>

<!-- Single Quote View -->
<?php if ($viewQuote): ?>
<div class="admin-card" style="border-top:4px solid var(--orange);">
    <div style="display:flex; justify-content:space-between; align-items:flex-start; flex-wrap:wrap; gap:1rem; margin-bottom:1.5rem;">
        <div>
            <h2 style="border-bottom:none; padding-bottom:0; margin-bottom:0.25rem;">
                Quote: <span style="color:var(--orange);"><?= e($viewQuote['reference']) ?></span>
            </h2>
            <p style="color:var(--text-muted); font-size:0.85rem;">Received <?= date('d M Y \a\t H:i', strtotime($viewQuote['created_at'])) ?></p>
        </div>
        <span class="status-badge status-<?= e($viewQuote['status']) ?>" style="font-size:0.82rem; padding:0.3rem 0.85rem;"><?= e($viewQuote['status']) ?></span>
    </div>

    <div style="display:grid; grid-template-columns:1fr 1fr; gap:2rem;">
        <!-- Contact Info -->
        <div>
            <h3 style="font-size:0.8rem; text-transform:uppercase; letter-spacing:0.08em; color:var(--text-muted); margin-bottom:0.75rem;">Customer Details</h3>
            <table style="font-size:0.9rem; width:100%; border-collapse:collapse;">
                <tr><td style="padding:0.4rem 0; color:var(--text-muted); width:120px;">Name</td><td><strong><?= e($viewQuote['full_name']) ?></strong></td></tr>
                <?php if ($viewQuote['company_name']): ?><tr><td style="padding:0.4rem 0; color:var(--text-muted);">Company</td><td><?= e($viewQuote['company_name']) ?></td></tr><?php endif; ?>
                <tr><td style="padding:0.4rem 0; color:var(--text-muted);">Phone</td><td><a href="tel:<?= e($viewQuote['phone']) ?>" style="color:var(--orange); font-weight:700;"><?= e($viewQuote['phone']) ?></a></td></tr>
                <?php if ($viewQuote['email']): ?><tr><td style="padding:0.4rem 0; color:var(--text-muted);">Email</td><td><a href="mailto:<?= e($viewQuote['email']) ?>"><?= e($viewQuote['email']) ?></a></td></tr><?php endif; ?>
                <tr><td style="padding:0.4rem 0; color:var(--text-muted);">Site / Delivery</td><td><?= e($viewQuote['delivery_location']) ?></td></tr>
                <?php if ($viewQuote['expected_date']): ?><tr><td style="padding:0.4rem 0; color:var(--text-muted);">Expected Date</td><td><?= date('d M Y', strtotime($viewQuote['expected_date'])) ?></td></tr><?php endif; ?>
            </table>

            <!-- Quick reply actions -->
            <div style="margin-top:1.25rem; display:flex; gap:0.5rem; flex-wrap:wrap;">
                <a href="tel:<?= e($viewQuote['phone']) ?>" class="btn btn-orange btn-sm"><i class="fas fa-phone-alt"></i> Call</a>
                <a href="https://wa.me/<?= preg_replace('/\D/', '', $viewQuote['phone']) ?>?text=Hello+<?= urlencode($viewQuote['full_name']) ?>%2C+thank+you+for+your+quote+request+<?= urlencode($viewQuote['reference']) ?>+from+Namarosa+Hardware.+Regarding+your+materials..."
                   target="_blank" rel="noopener"
                   class="btn btn-sm" style="background:#25d366; color:#fff;">
                    <i class="fab fa-whatsapp"></i> WhatsApp
                </a>
                <?php if ($viewQuote['email']): ?>
                    <a href="mailto:<?= e($viewQuote['email']) ?>?subject=Re: Quote <?= e($viewQuote['reference']) ?> — Namarosa Hardware"
                       class="btn btn-slate btn-sm"><i class="fas fa-envelope"></i> Email</a>
                <?php endif; ?>
            </div>
        </div>

        <!-- Materials -->
        <div>
            <h3 style="font-size:0.8rem; text-transform:uppercase; letter-spacing:0.08em; color:var(--text-muted); margin-bottom:0.75rem;">Materials Requested</h3>
            <div style="background:var(--off-white); border:1px solid var(--border); border-radius:var(--radius); padding:1rem; font-size:0.88rem; line-height:1.8; white-space:pre-wrap; max-height:200px; overflow-y:auto;"><?= e($viewQuote['materials_text']) ?></div>

            <?php if ($lineItems): ?>
            <h3 style="font-size:0.8rem; text-transform:uppercase; letter-spacing:0.08em; color:var(--text-muted); margin:1rem 0 0.5rem;">From Product Catalog</h3>
            <table style="font-size:0.85rem; width:100%;">
                <?php foreach ($lineItems as $li): ?>
                <tr>
                    <td style="padding:0.3rem 0; border-bottom:1px solid var(--border);"><?= e($li['product_name']) ?></td>
                    <td style="padding:0.3rem 0; border-bottom:1px solid var(--border); text-align:right; color:var(--orange); font-weight:700;"><?= e($li['quantity']) ?> <?= e($li['unit']) ?></td>
                </tr>
                <?php endforeach; ?>
            </table>
            <?php endif; ?>
        </div>
    </div>

    <!-- Status Update Form -->
    <div style="margin-top:2rem; padding-top:1.5rem; border-top:1px solid var(--border);">
        <h3 style="font-size:0.88rem; font-weight:700; color:var(--slate); margin-bottom:0.75rem;">Update Quote Status</h3>
        <form method="POST" action="<?= BASE_URL ?>admin/quotes.php">
            <input type="hidden" name="update_status" value="1" />
            <input type="hidden" name="quote_id" value="<?= (int)$viewQuote['id'] ?>" />
            <div style="display:flex; gap:0.75rem; align-items:flex-end; flex-wrap:wrap;">
                <div>
                    <label style="font-size:0.78rem; font-weight:700; text-transform:uppercase; letter-spacing:0.06em; color:var(--text-muted); display:block; margin-bottom:0.35rem;">Status</label>
                    <select name="new_status" class="form-control" style="width:auto;">
                        <option value="new"       <?= $viewQuote['status'] === 'new'       ? 'selected' : '' ?>>New</option>
                        <option value="read"      <?= $viewQuote['status'] === 'read'      ? 'selected' : '' ?>>Read</option>
                        <option value="responded" <?= $viewQuote['status'] === 'responded' ? 'selected' : '' ?>>Responded</option>
                        <option value="closed"    <?= $viewQuote['status'] === 'closed'    ? 'selected' : '' ?>>Closed</option>
                    </select>
                </div>
                <div style="flex:1; min-width:200px;">
                    <label style="font-size:0.78rem; font-weight:700; text-transform:uppercase; letter-spacing:0.06em; color:var(--text-muted); display:block; margin-bottom:0.35rem;">Admin Notes</label>
                    <input type="text" name="admin_notes" class="form-control" placeholder="e.g. Called customer, quoted KES 25,000..." value="<?= e($viewQuote['admin_notes'] ?? '') ?>" />
                </div>
                <button type="submit" class="btn btn-orange">Update</button>
                <a href="<?= BASE_URL ?>admin/quotes.php" class="btn" style="background:var(--off-white); color:var(--text-mid);">← Back to List</a>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<!-- Filter Tabs -->
<div style="display:flex; gap:0.35rem; margin-bottom:1.25rem; flex-wrap:wrap;">
    <?php foreach (['all' => 'All', 'new' => 'New', 'read' => 'Read', 'responded' => 'Responded', 'closed' => 'Closed'] as $k => $label): ?>
        <a href="<?= BASE_URL ?>admin/quotes.php?status=<?= $k ?>"
           style="padding:0.4rem 0.9rem; border-radius:100px; font-size:0.8rem; font-weight:600; text-decoration:none;
                  background:<?= $filterStatus === $k ? 'var(--orange)' : 'var(--white)' ?>;
                  color:<?= $filterStatus === $k ? 'var(--white)' : 'var(--text-muted)' ?>;
                  border:1px solid <?= $filterStatus === $k ? 'var(--orange)' : 'var(--border)' ?>;">
            <?= $label ?>
        </a>
    <?php endforeach; ?>
</div>

<!-- Quotes Table -->
<div class="admin-card">
    <?php if ($quotes): ?>
    <div style="overflow-x:auto;">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Ref</th>
                    <th>Name</th>
                    <th>Phone</th>
                    <th>Materials (Preview)</th>
                    <th>Delivery</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($quotes as $q): ?>
                <tr style="<?= $q['status'] === 'new' ? 'background:#fffbeb;' : '' ?>">
                    <td><strong style="color:var(--orange); font-size:0.8rem;"><?= e($q['reference']) ?></strong></td>
                    <td>
                        <strong><?= e($q['full_name']) ?></strong>
                        <?php if ($q['company_name']): ?>
                            <br><small style="color:var(--text-muted);"><?= e($q['company_name']) ?></small>
                        <?php endif; ?>
                    </td>
                    <td><a href="tel:<?= e($q['phone']) ?>" style="color:var(--orange); font-weight:600; white-space:nowrap;"><?= e($q['phone']) ?></a></td>
                    <td style="max-width:200px;">
                        <span style="font-size:0.8rem; color:var(--text-muted); display:block; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                            <?= e(substr($q['materials_text'], 0, 80)) ?>
                        </span>
                    </td>
                    <td style="font-size:0.82rem; max-width:120px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;"><?= e($q['delivery_location']) ?></td>
                    <td><span class="status-badge status-<?= e($q['status']) ?>"><?= e($q['status']) ?></span></td>
                    <td style="font-size:0.78rem; color:var(--text-muted); white-space:nowrap;"><?= date('d M Y', strtotime($q['created_at'])) ?></td>
                    <td>
                        <a href="<?= BASE_URL ?>admin/quotes.php?view=<?= (int)$q['id'] ?>" class="btn btn-sm btn-orange">View</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php else: ?>
        <div style="text-align:center; padding:3rem; color:var(--text-muted);">
            <i class="fas fa-inbox" style="font-size:2.5rem; margin-bottom:0.75rem; opacity:0.2; display:block;"></i>
            No quotes found with status: <strong><?= e($filterStatus) ?></strong>.
        </div>
    <?php endif; ?>
</div>

</main>
</body>
</html>
