<?php
require_once __DIR__ . '/../includes/db.php';
requireAdmin();
$adminPageTitle = 'Products';

$action = $_GET['action'] ?? 'list';
$editId = (int)($_GET['id'] ?? 0);
$categories = $pdo->query("SELECT * FROM categories WHERE active = 1 ORDER BY sort_order")->fetchAll();

// Handle POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pAction = $_POST['p_action'] ?? '';

    // Toggle stock
    if ($pAction === 'toggle_stock') {
        $pid = (int)$_POST['product_id'];
        $pdo->prepare("UPDATE products SET in_stock = NOT in_stock WHERE id = ?")->execute([$pid]);
        $_SESSION['flash'] = ['type' => 'success', 'msg' => 'Stock status updated.'];
        redirect(BASE_URL . 'admin/products.php');
    }

    // Delete
    if ($pAction === 'delete') {
        $pid = (int)$_POST['product_id'];
        $pdo->prepare("DELETE FROM products WHERE id = ?")->execute([$pid]);
        $_SESSION['flash'] = ['type' => 'success', 'msg' => 'Product deleted.'];
        redirect(BASE_URL . 'admin/products.php');
    }

    // Add / Edit
    if (in_array($pAction, ['add', 'edit'])) {
        $catId  = (int)$_POST['category_id'];
        $name   = trim($_POST['name']           ?? '');
        $desc   = trim($_POST['description']    ?? '');
        $spec   = trim($_POST['specifications'] ?? '');
        $price  = $_POST['price'] !== '' ? (float)$_POST['price'] : null;
        $unit   = trim($_POST['unit']           ?? 'piece');
        $img    = trim($_POST['image_url']      ?? '');
        $stock  = isset($_POST['in_stock'])  ? 1 : 0;
        $feat   = isset($_POST['featured'])  ? 1 : 0;

        if (!$name || !$catId) {
            $_SESSION['flash'] = ['type' => 'error', 'msg' => 'Name and category are required.'];
            redirect(BASE_URL . 'admin/products.php?action=' . $pAction . ($editId ? '&id=' . $editId : ''));
        }

        if ($pAction === 'add') {
            $pdo->prepare("INSERT INTO products (category_id, name, description, specifications, price, unit, image_url, in_stock, featured) VALUES (?,?,?,?,?,?,?,?,?)")
                ->execute([$catId, $name, $desc, $spec, $price, $unit, $img ?: null, $stock, $feat]);
            $_SESSION['flash'] = ['type' => 'success', 'msg' => "Product '{$name}' added."];
        } else {
            $pid = (int)$_POST['product_id'];
            $pdo->prepare("UPDATE products SET category_id=?, name=?, description=?, specifications=?, price=?, unit=?, image_url=?, in_stock=?, featured=?, updated_at=NOW() WHERE id=?")
                ->execute([$catId, $name, $desc, $spec, $price, $unit, $img ?: null, $stock, $feat, $pid]);
            $_SESSION['flash'] = ['type' => 'success', 'msg' => "Product '{$name}' updated."];
        }
        redirect(BASE_URL . 'admin/products.php');
    }
}

// Fetch product for edit
$editProduct = null;
if ($action === 'edit' && $editId) {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$editId]);
    $editProduct = $stmt->fetch();
    if (!$editProduct) redirect(BASE_URL . 'admin/products.php');
}

// List: fetch all products
$catFilter = (int)($_GET['cat'] ?? 0);
$whereClause = $catFilter ? "WHERE p.category_id = {$catFilter}" : '';
$products = $pdo->query(
    "SELECT p.*, c.name AS cat_name FROM products p JOIN categories c ON c.id = p.category_id {$whereClause} ORDER BY c.sort_order, p.name"
)->fetchAll();

require_once __DIR__ . '/partials/admin_header.php';
?>

<div style="margin-bottom:1.5rem; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:1rem;">
    <h1 style="font-family:'Bebas Neue',sans-serif; font-size:2rem; color:var(--slate); letter-spacing:0.03em;">
        <?= $action === 'add' ? 'Add Product' : ($action === 'edit' ? 'Edit Product' : 'Products') ?>
    </h1>
    <div style="display:flex; gap:0.5rem;">
        <?php if ($action !== 'list'): ?>
            <a href="<?= BASE_URL ?>admin/products.php" class="btn btn-sm" style="background:var(--off-white); color:var(--text-mid);">← Back to List</a>
        <?php else: ?>
            <a href="<?= BASE_URL ?>admin/products.php?action=add" class="btn btn-orange btn-sm"><i class="fas fa-plus"></i> Add Product</a>
        <?php endif; ?>
    </div>
</div>

<!-- Flash -->
<?php if (!empty($_SESSION['flash'])): ?>
    <div style="background:<?= $_SESSION['flash']['type'] === 'success' ? '#d1fae5' : '#fee2e2' ?>; border-left:4px solid <?= $_SESSION['flash']['type'] === 'success' ? 'var(--success)' : 'var(--error)' ?>; border-radius:var(--radius); padding:0.85rem 1rem; margin-bottom:1.25rem; font-size:0.88rem; color:<?= $_SESSION['flash']['type'] === 'success' ? '#065f46' : '#991b1b' ?>;">
        <?= e($_SESSION['flash']['msg']) ?>
    </div>
    <?php unset($_SESSION['flash']); ?>
<?php endif; ?>

<!-- ADD / EDIT FORM -->
<?php if (in_array($action, ['add', 'edit'])): ?>
<div class="admin-card">
    <h2><?= $action === 'add' ? 'Add New Product' : 'Edit: ' . e($editProduct['name'] ?? '') ?></h2>
    <form method="POST" action="<?= BASE_URL ?>admin/products.php">
        <input type="hidden" name="p_action" value="<?= $action ?>" />
        <?php if ($editProduct): ?>
            <input type="hidden" name="product_id" value="<?= $editProduct['id'] ?>" />
        <?php endif; ?>

        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">Category <span class="req">*</span></label>
                <select name="category_id" class="form-control" required>
                    <option value="">Select category</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>" <?= ($editProduct['category_id'] ?? '') == $cat['id'] ? 'selected' : '' ?>>
                            <?= e($cat['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Product Name <span class="req">*</span></label>
                <input type="text" name="name" class="form-control" required
                       placeholder="e.g. Y12 Steel Rebar (12m)"
                       value="<?= e($editProduct['name'] ?? '') ?>" />
            </div>
            <div class="form-group form-col-full">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" style="min-height:80px;"
                          placeholder="Short product description..."><?= e($editProduct['description'] ?? '') ?></textarea>
            </div>
            <div class="form-group form-col-full">
                <label class="form-label">Specifications</label>
                <textarea name="specifications" class="form-control" style="min-height:70px;"
                          placeholder="e.g. Diameter: 12mm | Length: 12m | Grade: Y"><?= e($editProduct['specifications'] ?? '') ?></textarea>
            </div>
            <div class="form-group">
                <label class="form-label">Price (KES)</label>
                <input type="number" name="price" class="form-control" step="0.01" min="0"
                       placeholder="Leave blank if price on request"
                       value="<?= $editProduct['price'] ?? '' ?>" />
            </div>
            <div class="form-group">
                <label class="form-label">Unit</label>
                <select name="unit" class="form-control">
                    <?php foreach (['piece','bag','bar','roll','tin','set','kg','litre','trip','sheet','length'] as $u): ?>
                        <option value="<?= $u ?>" <?= ($editProduct['unit'] ?? 'piece') === $u ? 'selected' : '' ?>><?= $u ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group form-col-full">
                <label class="form-label">Image URL</label>
                <input type="url" name="image_url" class="form-control"
                       placeholder="https://... or /assets/images/products/filename.jpg"
                       value="<?= e($editProduct['image_url'] ?? '') ?>" />
                <p class="form-hint">External URL or path to uploaded image. Leave blank to use emoji placeholder.</p>
            </div>
            <div class="form-group">
                <label style="display:flex; align-items:center; gap:0.5rem; cursor:pointer;">
                    <input type="checkbox" name="in_stock" value="1" <?= ($editProduct['in_stock'] ?? 1) ? 'checked' : '' ?> />
                    <span class="form-label" style="margin:0;">In Stock</span>
                </label>
            </div>
            <div class="form-group">
                <label style="display:flex; align-items:center; gap:0.5rem; cursor:pointer;">
                    <input type="checkbox" name="featured" value="1" <?= ($editProduct['featured'] ?? 0) ? 'checked' : '' ?> />
                    <span class="form-label" style="margin:0;">Featured on Homepage</span>
                </label>
            </div>
        </div>

        <div style="margin-top:1.25rem; display:flex; gap:0.75rem;">
            <button type="submit" class="btn btn-orange"><i class="fas fa-save"></i> <?= $action === 'add' ? 'Add Product' : 'Save Changes' ?></button>
            <a href="<?= BASE_URL ?>admin/products.php" class="btn" style="background:var(--off-white); color:var(--text-mid);">Cancel</a>
        </div>
    </form>
</div>
<?php endif; ?>

<!-- PRODUCTS LIST -->
<?php if ($action === 'list'): ?>

<!-- Category filter -->
<div style="display:flex; gap:0.35rem; margin-bottom:1.25rem; flex-wrap:wrap;">
    <a href="<?= BASE_URL ?>admin/products.php" style="padding:0.35rem 0.8rem; border-radius:100px; font-size:0.8rem; font-weight:600; text-decoration:none; background:<?= !$catFilter ? 'var(--orange)' : 'var(--white)' ?>; color:<?= !$catFilter ? 'var(--white)' : 'var(--text-muted)' ?>; border:1px solid <?= !$catFilter ? 'var(--orange)' : 'var(--border)' ?>;">All (<?= count($products) ?>)</a>
    <?php foreach ($categories as $cat): ?>
        <a href="<?= BASE_URL ?>admin/products.php?cat=<?= $cat['id'] ?>"
           style="padding:0.35rem 0.8rem; border-radius:100px; font-size:0.8rem; font-weight:600; text-decoration:none; background:<?= $catFilter == $cat['id'] ? 'var(--orange)' : 'var(--white)' ?>; color:<?= $catFilter == $cat['id'] ? 'var(--white)' : 'var(--text-muted)' ?>; border:1px solid <?= $catFilter == $cat['id'] ? 'var(--orange)' : 'var(--border)' ?>;">
            <?= e($cat['name']) ?>
        </a>
    <?php endforeach; ?>
</div>

<div class="admin-card">
    <div style="overflow-x:auto;">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Unit</th>
                    <th>Stock</th>
                    <th>Featured</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $p): ?>
                <tr>
                    <td>
                        <strong style="color:var(--slate);"><?= e($p['name']) ?></strong>
                        <?php if ($p['specifications']): ?>
                            <br><small style="color:var(--text-muted); font-size:0.75rem;"><?= e(substr($p['specifications'], 0, 60)) ?>…</small>
                        <?php endif; ?>
                    </td>
                    <td><span style="font-size:0.78rem; background:rgba(224,120,32,0.1); color:var(--orange); padding:0.15rem 0.5rem; border-radius:100px; font-weight:600;"><?= e($p['cat_name']) ?></span></td>
                    <td style="font-weight:700; color:var(--orange); white-space:nowrap;"><?= $p['price'] ? formatKES($p['price']) : '<span style="color:var(--text-muted);font-style:italic;font-size:0.8rem;">On request</span>' ?></td>
                    <td style="font-size:0.82rem; color:var(--text-muted);"><?= e($p['unit']) ?></td>
                    <td>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="p_action" value="toggle_stock" />
                            <input type="hidden" name="product_id" value="<?= $p['id'] ?>" />
                            <button type="submit" style="background:<?= $p['in_stock'] ? '#d1fae5' : '#fee2e2' ?>; color:<?= $p['in_stock'] ? '#065f46' : '#991b1b' ?>; border:none; padding:0.2rem 0.65rem; border-radius:100px; font-size:0.75rem; font-weight:700; cursor:pointer;">
                                <?= $p['in_stock'] ? '✓ In Stock' : '✗ Out of Stock' ?>
                            </button>
                        </form>
                    </td>
                    <td style="text-align:center;"><?= $p['featured'] ? '<i class="fas fa-star" style="color:var(--orange);"></i>' : '<i class="far fa-star" style="color:var(--border);"></i>' ?></td>
                    <td>
                        <div style="display:flex; gap:0.35rem;">
                            <a href="<?= BASE_URL ?>admin/products.php?action=edit&id=<?= $p['id'] ?>" class="btn btn-sm btn-slate">Edit</a>
                            <form method="POST" style="display:inline;" onsubmit="return confirm('Delete this product? This cannot be undone.');">
                                <input type="hidden" name="p_action" value="delete" />
                                <input type="hidden" name="product_id" value="<?= $p['id'] ?>" />
                                <button type="submit" class="btn btn-sm" style="background:#fee2e2; color:#991b1b; border-color:#fca5a5;">Del</button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

</main>
</body>
</html>
