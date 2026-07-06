<?php
require_once __DIR__ . '/includes/db.php';

$pageTitle = 'Request a Quote — Namarosa Hardware Bungoma';
$pageDesc  = 'Submit your materials list and quantities. We\'ll respond quickly with competitive prices for your construction project.';

// Get cart items to pre-fill form
$cartItems = getCart();

// Build pre-filled materials text from cart
$prefilled = '';
foreach ($cartItems as $item) {
    $prefilled .= "- {$item['name']} x {$item['qty']} {$item['unit']}\n";
}

// Handle POST
$errors  = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['full_name']     ?? '');
    $company  = trim($_POST['company_name']  ?? '');
    $phone    = trim($_POST['phone']         ?? '');
    $email    = trim($_POST['email']         ?? '');
    $delivery = trim($_POST['delivery_loc']  ?? '');
    $expDate  = trim($_POST['expected_date'] ?? '');
    $materials= trim($_POST['materials_text'] ?? '');

    // Validation
    if (strlen($name)      < 2)  $errors[] = 'Please enter your full name.';
    if (strlen($phone)     < 9)  $errors[] = 'Please enter a valid phone number.';
    if (strlen($delivery)  < 3)  $errors[] = 'Please enter a delivery location or site address.';
    if (strlen($materials) < 5)  $errors[] = 'Please describe the materials and quantities you need.';

    if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email address or leave it blank.';
    }

    if (!$errors) {
        $ref = generateRef();

        $stmt = $pdo->prepare(
            "INSERT INTO quotes (reference, full_name, company_name, phone, email, delivery_location, expected_date, materials_text, ip_address)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([
            $ref,
            $name,
            $company ?: null,
            $phone,
            $email   ?: null,
            $delivery,
            $expDate ?: null,
            $materials,
            $_SERVER['REMOTE_ADDR'] ?? null,
        ]);
        $quoteId = $pdo->lastInsertId();

        // Save cart items as line items
        if ($cartItems) {
            $itemStmt = $pdo->prepare(
                "INSERT INTO quote_items (quote_id, product_id, product_name, quantity, unit)
                 VALUES (?, ?, ?, ?, ?)"
            );
            foreach ($cartItems as $item) {
                $itemStmt->execute([
                    $quoteId,
                    $item['product_id'],
                    $item['name'],
                    $item['qty'],
                    $item['unit'],
                ]);
            }
        }

        // Send email notification
        $to      = SITE_EMAIL;
        $subject = "New Quote Request [{$ref}] from {$name}";
        $body    = "New quote request received from Namarosa Hardware website.\n\n"
                 . "Reference:  {$ref}\n"
                 . "Name:       {$name}\n"
                 . ($company ? "Company:    {$company}\n" : '')
                 . "Phone:      {$phone}\n"
                 . ($email   ? "Email:      {$email}\n"   : '')
                 . "Delivery:   {$delivery}\n"
                 . ($expDate ? "Exp. Date:  {$expDate}\n" : '')
                 . "\nMaterials Requested:\n{$materials}\n\n"
                 . "Submitted: " . date('d M Y H:i') . "\n"
                 . "Admin URL: https://" . SITE_DOMAIN . BASE_URL . "admin/quotes.php\n";

        $headers = "From: noreply@namarosahardware.co.ke\r\nReply-To: {$email}";
        @mail($to, $subject, $body, $headers);

        clearCart();
        $_SESSION['flash'] = ['type' => 'success', 'msg' => "Quote [{$ref}] submitted! We'll call you on {$phone} within a few hours."];
        redirect(BASE_URL . 'quote.php?submitted=1&ref=' . urlencode($ref));
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<!-- Page Hero -->
<section class="page-hero">
    <div class="container page-hero-content">
        <h1>Request a <span>Quote</span></h1>
        <div class="breadcrumb">
            <a href="<?= BASE_URL ?>index.php">Home</a>
            <i class="fas fa-chevron-right"></i>
            <span>Request a Quote</span>
        </div>
    </div>
</section>

<?php if (isset($_GET['submitted'])): ?>
<!-- Success State -->
<div class="container" style="padding: 4rem 0;">
    <div style="max-width:600px; margin:0 auto; text-align:center; padding:3rem; background:var(--white); border-radius:var(--radius-lg); border:1px solid var(--border); box-shadow:var(--shadow-md);">
        <div style="width:72px; height:72px; background:#d1fae5; border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 1.5rem; font-size:2rem; color:#059669;">
            <i class="fas fa-check-circle"></i>
        </div>
        <h2 style="font-family:var(--font-display); font-size:2rem; color:var(--slate); letter-spacing:0.03em; margin-bottom:0.5rem;">Quote Submitted!</h2>
        <p style="color:var(--text-muted); margin-bottom:0.5rem;">
            Reference: <strong style="color:var(--orange); font-size:1.05rem;"><?= e($_GET['ref'] ?? '') ?></strong>
        </p>
        <p style="color:var(--text-muted); line-height:1.7; margin-bottom:2rem;">
            Thank you! Emmanuel Juma will review your request and call you with pricing shortly.
            Store hours are <strong>Mon–Sat, 7:30 AM – 6:00 PM</strong>.
        </p>
        <div style="display:flex; gap:1rem; justify-content:center; flex-wrap:wrap;">
            <a href="<?= BASE_URL ?>products.php" class="btn btn-orange">Browse More Products</a>
            <a href="https://wa.me/254710774174?text=Hi%2C%20I%20just%20submitted%20quote%20<?= e($_GET['ref'] ?? '') ?>%20on%20your%20website."
               class="btn btn-slate" target="_blank" rel="noopener">
                <i class="fab fa-whatsapp"></i> Follow Up on WhatsApp
            </a>
        </div>
    </div>
</div>

<?php else: ?>

<!-- Quote Form Layout -->
<div class="container">
    <div class="quote-layout">

        <!-- Left: Form -->
        <div class="quote-form-card">
            <h2><i class="fas fa-clipboard-list" style="color:var(--orange); margin-right:0.5rem;"></i>Quote Request Form</h2>

            <?php if ($errors): ?>
                <div class="flash flash-error" style="position:relative; border-radius:var(--radius); padding:0.85rem 1rem; margin-bottom:1.5rem; border:none; border-left:4px solid var(--error);">
                    <ul style="margin:0; padding:0 0 0 1.25rem;">
                        <?php foreach ($errors as $e): ?>
                            <li><?= e($e) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form method="POST" action="<?= BASE_URL ?>quote.php" novalidate>

                <!-- Honeypot -->
                <input type="text" name="website_url" style="display:none" tabindex="-1" autocomplete="off" />

                <div class="form-grid">
                    <!-- Name -->
                    <div class="form-group">
                        <label class="form-label" for="full_name">Full Name <span class="req">*</span></label>
                        <input
                            type="text"
                            id="full_name"
                            name="full_name"
                            class="form-control"
                            placeholder="e.g. John Kamau"
                            value="<?= e($_POST['full_name'] ?? '') ?>"
                            required
                            autocomplete="name"
                        />
                    </div>

                    <!-- Company -->
                    <div class="form-group">
                        <label class="form-label" for="company_name">Company / Business Name</label>
                        <input
                            type="text"
                            id="company_name"
                            name="company_name"
                            class="form-control"
                            placeholder="Optional"
                            value="<?= e($_POST['company_name'] ?? '') ?>"
                            autocomplete="organization"
                        />
                    </div>

                    <!-- Phone -->
                    <div class="form-group">
                        <label class="form-label" for="phone">Phone Number <span class="req">*</span></label>
                        <input
                            type="tel"
                            id="phone"
                            name="phone"
                            class="form-control"
                            placeholder="+254 7XX XXX XXX"
                            value="<?= e($_POST['phone'] ?? '') ?>"
                            required
                            autocomplete="tel"
                        />
                    </div>

                    <!-- Email -->
                    <div class="form-group">
                        <label class="form-label" for="email">Email Address</label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            class="form-control"
                            placeholder="Optional"
                            value="<?= e($_POST['email'] ?? '') ?>"
                            autocomplete="email"
                        />
                    </div>

                    <!-- Delivery Location -->
                    <div class="form-group">
                        <label class="form-label" for="delivery_loc">Delivery Location / Site Address <span class="req">*</span></label>
                        <input
                            type="text"
                            id="delivery_loc"
                            name="delivery_loc"
                            class="form-control"
                            placeholder="e.g. Kanduyi, Bungoma Town, Plot No..."
                            value="<?= e($_POST['delivery_loc'] ?? '') ?>"
                            required
                        />
                    </div>

                    <!-- Expected Date -->
                    <div class="form-group">
                        <label class="form-label" for="expected_date">Expected Delivery Date</label>
                        <input
                            type="date"
                            id="expected_date"
                            name="expected_date"
                            class="form-control"
                            min="<?= date('Y-m-d') ?>"
                            value="<?= e($_POST['expected_date'] ?? '') ?>"
                        />
                    </div>

                    <!-- Materials -->
                    <div class="form-group form-col-full">
                        <label class="form-label" for="materials_text">
                            Materials &amp; Quantities Needed <span class="req">*</span>
                        </label>
                        <textarea
                            id="materials_text"
                            name="materials_text"
                            class="form-control"
                            style="min-height:180px;"
                            placeholder="List each item and quantity on a new line, e.g:
50 bags of cement (50kg)
20 Y12 steel rebars (12m)
100m of 2.5mm electrical wire
10 litres of Gocoat white paint
..."
                            required
                        ><?= e($_POST['materials_text'] ?? $prefilled) ?></textarea>
                        <p class="form-hint">
                            <i class="fas fa-info-circle"></i>
                            Be as specific as possible — include brand preferences, dimensions, or grade if known.
                            We'll confirm availability and pricing.
                            <span id="charCount">0</span> characters.
                        </p>
                    </div>
                </div>

                <!-- Submit -->
                <div style="margin-top:1.5rem; display:flex; gap:1rem; align-items:center; flex-wrap:wrap;">
                    <button type="submit" class="btn btn-orange btn-lg">
                        <i class="fas fa-paper-plane"></i> Submit Quote Request
                    </button>
                    <a href="<?= BASE_URL ?>products.php" class="btn" style="background:var(--off-white); color:var(--text-mid);">
                        <i class="fas fa-arrow-left"></i> Continue Browsing
                    </a>
                </div>

                <p style="font-size:0.78rem; color:var(--text-muted); margin-top:1rem;">
                    <i class="fas fa-lock"></i>
                    Your information is private. We only use it to prepare your quote and will not share it.
                    You'll receive a call on the number provided during business hours.
                </p>
            </form>
        </div>

        <!-- Right: Cart + Contact -->
        <div>
            <!-- Quote Cart from Catalog -->
            <div class="quote-sidebar-card" style="margin-bottom:1rem;">
                <div class="quote-cart">
                    <h3 class="quote-cart-heading">
                        <i class="fas fa-clipboard-list" style="color:var(--orange); margin-right:0.35rem;"></i>
                        Items from Catalog
                    </h3>

                    <?php if ($cartItems): ?>
                        <?php foreach ($cartItems as $key => $item): ?>
                        <div class="cart-item">
                            <span class="cart-item-name"><?= e($item['name']) ?></span>
                            <span class="cart-item-qty">
                                <input
                                    type="number"
                                    class="qty-input"
                                    min="1"
                                    value="<?= (int)$item['qty'] ?>"
                                    style="width:60px; padding:0.2rem 0.4rem; border:1px solid var(--border); border-radius:4px; font-size:0.82rem; text-align:center;"
                                    title="Quantity"
                                />
                                <small><?= e($item['unit']) ?></small>
                            </span>
                            <button class="cart-item-remove" data-key="<?= e($key) ?>" title="Remove" aria-label="Remove <?= e($item['name']) ?>">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <?php endforeach; ?>

                        <div style="margin-top:0.75rem;">
                            <a href="<?= BASE_URL ?>products.php" class="btn btn-outline-orange btn-sm btn-full">
                                <i class="fas fa-plus"></i> Add More Items
                            </a>
                        </div>

                    <?php else: ?>
                        <div class="cart-empty">
                            <i class="fas fa-clipboard-list"></i>
                            No items added yet.
                            <a href="<?= BASE_URL ?>products.php" style="color:var(--orange); display:block; margin-top:0.5rem; font-size:0.85rem;">Browse Products →</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Contact Card -->
            <div class="contact-info-card">
                <h3><i class="fas fa-headset"></i> Prefer to Call?</h3>
                <div class="cinfo-item">
                    <i class="fas fa-phone-alt"></i>
                    <div>
                        <strong>Emmanuel Juma</strong><br>
                        <a href="tel:+254710774174">+254 710 774 174</a>
                    </div>
                </div>
                <div class="cinfo-item">
                    <i class="fab fa-whatsapp"></i>
                    <div>
                        <a href="https://wa.me/254710774174?text=Hi%2C%20I%27d%20like%20to%20request%20a%20quote..." target="_blank" rel="noopener">
                            Chat on WhatsApp
                        </a>
                    </div>
                </div>
                <div class="cinfo-item">
                    <i class="fas fa-clock"></i>
                    <span>Mon–Sat: 7:30 AM – 6:00 PM</span>
                </div>
                <div class="cinfo-item">
                    <i class="fas fa-map-marker-alt"></i>
                    <span>Main Street, Bungoma</span>
                </div>
                <div style="margin-top:1rem; padding-top:1rem; border-top:1px solid rgba(255,255,255,0.1);">
                    <p style="font-size:0.78rem; color:rgba(255,255,255,0.5); line-height:1.6;">
                        Typical response time: <strong style="color:rgba(255,255,255,0.8);">Same business day</strong> for quotes submitted before 4 PM.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
