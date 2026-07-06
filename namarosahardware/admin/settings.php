<?php
require_once __DIR__ . '/../includes/db.php';
requireAdmin();
$adminPageTitle = 'Site Settings';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fields = [
        'phone', 'whatsapp', 'email', 'address', 'hours',
        'director_name', 'director_title',
        'facebook_url', 'twitter_url', 'instagram_url',
        'google_maps_embed', 'site_tagline'
    ];
    foreach ($fields as $field) {
        $val = trim($_POST[$field] ?? '');
        $pdo->prepare("UPDATE settings SET setting_value = ? WHERE setting_key = ?")->execute([$val, $field]);
    }

    // Change password
    if (!empty($_POST['new_password'])) {
        $np = $_POST['new_password'];
        if (strlen($np) >= 8) {
            $hash = password_hash($np, PASSWORD_BCRYPT, ['cost' => 12]);
            $pdo->prepare("UPDATE admins SET password_hash = ? WHERE id = ?")->execute([$hash, $_SESSION['admin_id']]);
            $_SESSION['flash'] = ['type' => 'success', 'msg' => 'Settings and password saved.'];
        } else {
            $_SESSION['flash'] = ['type' => 'error', 'msg' => 'Password must be at least 8 characters.'];
        }
    } else {
        $_SESSION['flash'] = ['type' => 'success', 'msg' => 'Settings saved successfully.'];
    }
    redirect(BASE_URL . 'admin/settings.php');
}

// Fetch all settings
$settingsRaw = $pdo->query("SELECT setting_key, setting_value FROM settings")->fetchAll();
$settings = [];
foreach ($settingsRaw as $row) { $settings[$row['setting_key']] = $row['setting_value']; }

function sv(array $s, string $key): string {
    return htmlspecialchars($s[$key] ?? '', ENT_QUOTES, 'UTF-8');
}

require_once __DIR__ . '/partials/admin_header.php';
?>

<div style="margin-bottom:1.5rem; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:1rem;">
    <h1 style="font-family:'Bebas Neue',sans-serif; font-size:2rem; color:var(--slate); letter-spacing:0.03em;">Site Settings</h1>
    <a href="<?= BASE_URL ?>admin/index.php" class="btn btn-sm" style="background:var(--off-white); color:var(--text-mid);">← Dashboard</a>
</div>

<?php if (!empty($_SESSION['flash'])): ?>
    <div style="background:<?= $_SESSION['flash']['type'] === 'success' ? '#d1fae5' : '#fee2e2' ?>; border-left:4px solid <?= $_SESSION['flash']['type'] === 'success' ? 'var(--success)' : 'var(--error)' ?>; border-radius:var(--radius); padding:0.85rem 1rem; margin-bottom:1.25rem; font-size:0.88rem;">
        <?= e($_SESSION['flash']['msg']) ?>
    </div>
    <?php unset($_SESSION['flash']); ?>
<?php endif; ?>

<form method="POST" action="<?= BASE_URL ?>admin/settings.php">
    <div class="admin-card">
        <h2>Contact Information</h2>
        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">Phone Number</label>
                <input type="text" name="phone" class="form-control" value="<?= sv($settings, 'phone') ?>" placeholder="+254 710 774 174" />
            </div>
            <div class="form-group">
                <label class="form-label">WhatsApp Number (digits only)</label>
                <input type="text" name="whatsapp" class="form-control" value="<?= sv($settings, 'whatsapp') ?>" placeholder="254710774174" />
            </div>
            <div class="form-group">
                <label class="form-label">Email Address</label>
                <input type="email" name="email" class="form-control" value="<?= sv($settings, 'email') ?>" />
            </div>
            <div class="form-group">
                <label class="form-label">Director Name</label>
                <input type="text" name="director_name" class="form-control" value="<?= sv($settings, 'director_name') ?>" />
            </div>
            <div class="form-group">
                <label class="form-label">Director Title</label>
                <input type="text" name="director_title" class="form-control" value="<?= sv($settings, 'director_title') ?>" />
            </div>
            <div class="form-group">
                <label class="form-label">Site Tagline</label>
                <input type="text" name="site_tagline" class="form-control" value="<?= sv($settings, 'site_tagline') ?>" />
            </div>
            <div class="form-group form-col-full">
                <label class="form-label">Address</label>
                <input type="text" name="address" class="form-control" value="<?= sv($settings, 'address') ?>" />
            </div>
            <div class="form-group form-col-full">
                <label class="form-label">Store Hours</label>
                <input type="text" name="hours" class="form-control" value="<?= sv($settings, 'hours') ?>" placeholder="Mon–Sat: 7:30 AM – 6:00 PM" />
            </div>
        </div>
    </div>

    <div class="admin-card">
        <h2>Social Media Links</h2>
        <div class="form-grid">
            <div class="form-group">
                <label class="form-label"><i class="fab fa-facebook" style="color:#1877f2;"></i> Facebook URL</label>
                <input type="url" name="facebook_url" class="form-control" value="<?= sv($settings, 'facebook_url') ?>" placeholder="https://facebook.com/..." />
            </div>
            <div class="form-group">
                <label class="form-label"><i class="fab fa-twitter" style="color:#1da1f2;"></i> Twitter URL</label>
                <input type="url" name="twitter_url" class="form-control" value="<?= sv($settings, 'twitter_url') ?>" placeholder="https://twitter.com/..." />
            </div>
            <div class="form-group">
                <label class="form-label"><i class="fab fa-instagram" style="color:#e4405f;"></i> Instagram URL</label>
                <input type="url" name="instagram_url" class="form-control" value="<?= sv($settings, 'instagram_url') ?>" placeholder="https://instagram.com/..." />
            </div>
        </div>
    </div>

    <div class="admin-card">
        <h2>Google Maps Embed</h2>
        <div class="form-group">
            <label class="form-label">Google Maps iFrame src URL</label>
            <input type="url" name="google_maps_embed" class="form-control" value="<?= sv($settings, 'google_maps_embed') ?>" placeholder="https://www.google.com/maps/embed?pb=..." />
            <p class="form-hint">Go to Google Maps → Share → Embed a Map → Copy the src="..." URL only.</p>
        </div>
    </div>

    <div class="admin-card">
        <h2>Change Admin Password</h2>
        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">New Password</label>
                <input type="password" name="new_password" class="form-control" placeholder="Leave blank to keep current" autocomplete="new-password" />
                <p class="form-hint">Minimum 8 characters. Leave blank if you don't want to change it.</p>
            </div>
            <div class="form-group">
                <label class="form-label">Confirm Password</label>
                <input type="password" id="confirmPwd" class="form-control" placeholder="Repeat new password" autocomplete="new-password" />
            </div>
        </div>
    </div>

    <div style="margin-bottom:2rem;">
        <button type="submit" class="btn btn-orange btn-lg"><i class="fas fa-save"></i> Save All Settings</button>
    </div>
</form>

<script>
document.querySelector('form').addEventListener('submit', function(e) {
    const np = document.querySelector('[name="new_password"]').value;
    const cp = document.getElementById('confirmPwd').value;
    if (np && np !== cp) {
        e.preventDefault();
        alert('Passwords do not match. Please re-enter.');
    }
});
</script>

</main>
</body>
</html>
