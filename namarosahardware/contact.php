<?php
require_once __DIR__ . '/includes/db.php';

$pageTitle = 'Contact Us — Namarosa Hardware | Main Street, Bungoma';
$pageDesc  = 'Visit Namarosa Hardware on Main Street, Bungoma. Call Emmanuel Juma on +254 710 774 174. Open Mon–Sat 7:30 AM – 6:00 PM.';

// Handle contact form POST
$errors  = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['contact_submit'])) {
    $cName    = trim($_POST['c_name']    ?? '');
    $cPhone   = trim($_POST['c_phone']   ?? '');
    $cEmail   = trim($_POST['c_email']   ?? '');
    $cSubject = trim($_POST['c_subject'] ?? '');
    $cMessage = trim($_POST['c_message'] ?? '');

    if (strlen($cName)    < 2)  $errors[] = 'Please enter your name.';
    if (strlen($cPhone)   < 9)  $errors[] = 'Please enter a valid phone number.';
    if (strlen($cMessage) < 10) $errors[] = 'Please enter a message (at least 10 characters).';
    if ($cEmail && !filter_var($cEmail, FILTER_VALIDATE_EMAIL)) $errors[] = 'Invalid email address.';

    if (!$errors) {
        $to      = SITE_EMAIL;
        $subject = 'Website Enquiry: ' . ($cSubject ?: 'General Enquiry') . ' — ' . $cName;
        $body    = "New contact message from Namarosa Hardware website.\n\n"
                 . "Name:    {$cName}\n"
                 . "Phone:   {$cPhone}\n"
                 . ($cEmail   ? "Email:   {$cEmail}\n"   : '')
                 . ($cSubject ? "Subject: {$cSubject}\n" : '')
                 . "\nMessage:\n{$cMessage}\n\n"
                 . "Sent: " . date('d M Y H:i');
        $headers = "From: noreply@namarosahardware.co.ke\r\nReply-To: " . ($cEmail ?: $to);
        @mail($to, $subject, $body, $headers);
        $success = true;
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<!-- Page Hero -->
<section class="page-hero">
    <div class="container page-hero-content">
        <h1>Contact <span>&amp; Location</span></h1>
        <div class="breadcrumb">
            <a href="<?= BASE_URL ?>index.php">Home</a>
            <i class="fas fa-chevron-right"></i>
            <span>Contact Us</span>
        </div>
    </div>
</section>

<!-- ========== DIRECTOR CARD + CONTACT DETAILS ========== -->
<section style="background:var(--off-white); padding:3rem 0; border-bottom:1px solid var(--border);">
    <div class="container">
        <div style="display:grid; grid-template-columns:1fr 1fr 1fr 1fr; gap:1rem;">

            <!-- Director -->
            <div style="background:var(--slate); border-radius:var(--radius-lg); padding:1.5rem; grid-column:span 1; position:relative; overflow:hidden;">
                <div style="position:absolute; top:0; left:0; right:0; height:4px; background:linear-gradient(90deg, var(--orange), var(--orange-light));"></div>
                <div style="display:flex; gap:0.85rem; align-items:center; margin-bottom:1rem;">
                    <div style="width:56px; height:56px; background:var(--slate-mid); border:2px solid var(--orange); border-radius:var(--radius); display:flex; align-items:center; justify-content:center; font-size:1.5rem; color:var(--orange); flex-shrink:0;">
                        <i class="fas fa-user-tie"></i>
                    </div>
                    <div>
                        <p style="font-family:var(--font-display); font-size:1.2rem; color:var(--white); letter-spacing:0.03em;">Emmanuel Juma</p>
                        <p style="font-size:0.75rem; color:var(--orange); text-transform:uppercase; letter-spacing:0.08em; font-weight:700;">Director</p>
                    </div>
                </div>
                <p style="font-size:0.82rem; color:rgba(255,255,255,0.65); line-height:1.7; margin-bottom:1rem;">
                    Namarosa Hardware is your Partner in Building &amp; Progress — quality solutions for all your construction needs in Bungoma County.
                </p>
                <a href="https://wa.me/254710774174" target="_blank" rel="noopener"
                   class="btn" style="background:#25d366; color:var(--white); width:100%; justify-content:center; font-size:0.85rem;">
                    <i class="fab fa-whatsapp"></i> Message Directly
                </a>
            </div>

            <!-- Phone -->
            <div class="cdetail-card">
                <div class="cdetail-icon" style="background:rgba(224,120,32,0.1);">
                    <i class="fas fa-phone-alt" style="color:var(--orange);"></i>
                </div>
                <div>
                    <p class="cdetail-label">Phone</p>
                    <p class="cdetail-value">
                        <a href="tel:+254710774174">+254 710 774 174</a>
                    </p>
                    <p style="font-size:0.75rem; color:var(--text-muted); margin-top:0.25rem;">Call or WhatsApp</p>
                </div>
            </div>

            <!-- Email -->
            <div class="cdetail-card">
                <div class="cdetail-icon">
                    <i class="fas fa-envelope"></i>
                </div>
                <div>
                    <p class="cdetail-label">Email</p>
                    <p class="cdetail-value">
                        <a href="mailto:emmanueljuma1972@gmail.com" style="word-break:break-all; font-size:0.82rem;">emmanueljuma1972@gmail.com</a>
                    </p>
                    <p style="font-size:0.75rem; color:var(--text-muted); margin-top:0.25rem;">We respond same day</p>
                </div>
            </div>

            <!-- Hours -->
            <div class="cdetail-card">
                <div class="cdetail-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <div>
                    <p class="cdetail-label">Store Hours</p>
                    <p class="cdetail-value" style="font-size:0.88rem;">Mon–Sat: 7:30 AM – 6:00 PM</p>
                    <p style="font-size:0.75rem; color:var(--error); margin-top:0.25rem; font-weight:600;">Sunday: Closed</p>
                </div>
            </div>
        </div>

        <!-- Address + Quick links row -->
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem; margin-top:1rem;">
            <div class="cdetail-card">
                <div class="cdetail-icon"><i class="fas fa-map-marker-alt"></i></div>
                <div>
                    <p class="cdetail-label">Location</p>
                    <p class="cdetail-value">Main Street, Bungoma,<br>Bungoma County, Kenya</p>
                    <a href="https://maps.google.com/?q=Namarosa+Hardware+Bungoma" target="_blank" rel="noopener"
                       style="font-size:0.78rem; color:var(--orange); margin-top:0.35rem; display:inline-flex; align-items:center; gap:0.3rem;">
                        <i class="fab fa-google"></i> Open in Google Maps
                    </a>
                </div>
            </div>
            <div class="cdetail-card">
                <div class="cdetail-icon"><i class="fas fa-globe"></i></div>
                <div>
                    <p class="cdetail-label">Website</p>
                    <p class="cdetail-value">www.namarosahardware.co.ke</p>
                    <div style="display:flex; gap:0.5rem; margin-top:0.5rem;">
                        <a href="https://wa.me/254710774174" target="_blank" rel="noopener"
                           style="background:#25d366; color:var(--white); padding:0.25rem 0.6rem; border-radius:var(--radius); font-size:0.75rem; font-weight:600; display:inline-flex; align-items:center; gap:0.3rem;">
                            <i class="fab fa-whatsapp"></i> WhatsApp
                        </a>
                        <a href="tel:+254710774174"
                           style="background:var(--orange); color:var(--white); padding:0.25rem 0.6rem; border-radius:var(--radius); font-size:0.75rem; font-weight:600; display:inline-flex; align-items:center; gap:0.3rem;">
                            <i class="fas fa-phone-alt"></i> Call Now
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ========== MAP + CONTACT FORM ========== -->
<section style="padding:4rem 0;">
    <div class="container">
        <div class="contact-grid">

            <!-- Map -->
            <div>
                <div class="section-header" style="margin-bottom:1.5rem;">
                    <span class="section-eyebrow">Find Us</span>
                    <h2 class="section-title" style="font-size:1.8rem;">Visit Our Store</h2>
                    <p class="section-sub" style="font-size:0.9rem;">
                        Located on Main Street, Bungoma Town. Delivery trucks and contractors are welcome.
                    </p>
                </div>

                <div class="map-container" style="aspect-ratio:4/3;">
                    <iframe
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3989.3!2d34.5600!3d0.5633!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zMCczOC4wIk4gMzTCsDMzJzM2LjAiRQ!5e0!3m2!1sen!2ske!4v1700000000000!5m2!1sen!2ske"
                        allowfullscreen=""
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"
                        title="Namarosa Hardware location on Google Maps"
                        style="border:0; width:100%; height:100%;">
                    </iframe>
                </div>

                <!-- Directions CTA -->
                <div style="margin-top:1rem; display:flex; gap:0.75rem; flex-wrap:wrap;">
                    <a href="https://maps.google.com/?q=Namarosa+Hardware+Bungoma+Kenya&daddr=Namarosa+Hardware+Main+Street+Bungoma"
                       target="_blank" rel="noopener"
                       class="btn btn-orange btn-sm">
                        <i class="fas fa-directions"></i> Get Directions
                    </a>
                    <a href="https://wa.me/254710774174?text=Hi%20Emmanuel%2C%20I%20need%20directions%20to%20Namarosa%20Hardware."
                       target="_blank" rel="noopener"
                       class="btn btn-sm" style="background:#25d366; color:var(--white);">
                        <i class="fab fa-whatsapp"></i> Ask for Directions
                    </a>
                </div>
            </div>

            <!-- Contact Form -->
            <div>
                <div class="section-header" style="margin-bottom:1.5rem;">
                    <span class="section-eyebrow">Send a Message</span>
                    <h2 class="section-title" style="font-size:1.8rem;">Get in Touch</h2>
                    <p class="section-sub" style="font-size:0.9rem;">
                        For general enquiries, product availability, or delivery questions. We respond same day during business hours.
                    </p>
                </div>

                <?php if ($success): ?>
                    <div style="background:#d1fae5; border:1px solid #6ee7b7; border-radius:var(--radius-lg); padding:1.5rem; text-align:center;">
                        <i class="fas fa-check-circle" style="font-size:2rem; color:#059669; margin-bottom:0.75rem; display:block;"></i>
                        <h3 style="color:#065f46; font-family:var(--font-display); font-size:1.4rem; letter-spacing:0.03em; margin-bottom:0.5rem;">Message Sent!</h3>
                        <p style="color:#047857; font-size:0.9rem;">Thank you. Emmanuel Juma will get back to you shortly. You can also call directly on <a href="tel:+254710774174" style="color:var(--orange); font-weight:700;">+254 710 774 174</a>.</p>
                    </div>
                <?php else: ?>

                <?php if ($errors): ?>
                    <div style="background:#fee2e2; border-left:4px solid var(--error); border-radius:var(--radius); padding:1rem; margin-bottom:1.25rem; font-size:0.88rem; color:#991b1b;">
                        <ul style="margin:0; padding-left:1.25rem;">
                            <?php foreach ($errors as $err): ?>
                                <li><?= e($err) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form method="POST" action="<?= BASE_URL ?>contact.php" novalidate>
                    <input type="hidden" name="contact_submit" value="1" />
                    <input type="text" name="url_field" style="display:none" tabindex="-1" autocomplete="off" />

                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label" for="c_name">Your Name <span class="req">*</span></label>
                            <input type="text" id="c_name" name="c_name" class="form-control"
                                   placeholder="Full name"
                                   value="<?= e($_POST['c_name'] ?? '') ?>" required autocomplete="name" />
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="c_phone">Phone Number <span class="req">*</span></label>
                            <input type="tel" id="c_phone" name="c_phone" class="form-control"
                                   placeholder="+254 7XX XXX XXX"
                                   value="<?= e($_POST['c_phone'] ?? '') ?>" required autocomplete="tel" />
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="c_email">Email Address</label>
                            <input type="email" id="c_email" name="c_email" class="form-control"
                                   placeholder="Optional"
                                   value="<?= e($_POST['c_email'] ?? '') ?>" autocomplete="email" />
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="c_subject">Subject</label>
                            <select id="c_subject" name="c_subject" class="form-control">
                                <option value="">Select a subject...</option>
                                <option value="Product Enquiry" <?= ($_POST['c_subject'] ?? '') === 'Product Enquiry' ? 'selected' : '' ?>>Product Enquiry</option>
                                <option value="Price / Quote" <?= ($_POST['c_subject'] ?? '') === 'Price / Quote' ? 'selected' : '' ?>>Price / Quote</option>
                                <option value="Delivery Question" <?= ($_POST['c_subject'] ?? '') === 'Delivery Question' ? 'selected' : '' ?>>Delivery Question</option>
                                <option value="Stock Availability" <?= ($_POST['c_subject'] ?? '') === 'Stock Availability' ? 'selected' : '' ?>>Stock Availability</option>
                                <option value="General Enquiry" <?= ($_POST['c_subject'] ?? '') === 'General Enquiry' ? 'selected' : '' ?>>General Enquiry</option>
                            </select>
                        </div>

                        <div class="form-group form-col-full">
                            <label class="form-label" for="c_message">Your Message <span class="req">*</span></label>
                            <textarea id="c_message" name="c_message" class="form-control" style="min-height:140px;"
                                      placeholder="How can we help you today?"
                                      required><?= e($_POST['c_message'] ?? '') ?></textarea>
                        </div>
                    </div>

                    <div style="margin-top:1.25rem; display:flex; gap:0.75rem; align-items:center; flex-wrap:wrap;">
                        <button type="submit" class="btn btn-orange btn-lg">
                            <i class="fas fa-paper-plane"></i> Send Message
                        </button>
                        <span style="font-size:0.8rem; color:var(--text-muted);">
                            <i class="fas fa-shield-alt" style="color:var(--orange);"></i> Your info is private &amp; secure.
                        </span>
                    </div>
                </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- ========== QUICK ACTIONS STRIP ========== -->
<section style="background:var(--slate); padding:2.5rem 0; border-top:3px solid var(--orange);">
    <div class="container">
        <div style="display:grid; grid-template-columns:repeat(3,1fr); gap:1px; background:rgba(255,255,255,0.1); border-radius:var(--radius-lg); overflow:hidden;">
            <a href="tel:+254710774174"
               style="background:var(--slate-mid); padding:1.75rem 1.5rem; text-align:center; text-decoration:none; transition:background var(--transition);"
               onmouseover="this.style.background='var(--slate-light)'" onmouseout="this.style.background='var(--slate-mid)'">
                <i class="fas fa-phone-alt" style="font-size:1.8rem; color:var(--orange); margin-bottom:0.6rem; display:block;"></i>
                <p style="color:var(--white); font-weight:700; font-size:0.95rem;">Call Us Now</p>
                <p style="color:rgba(255,255,255,0.55); font-size:0.8rem; margin-top:0.25rem;">+254 710 774 174</p>
            </a>
            <a href="https://wa.me/254710774174" target="_blank" rel="noopener"
               style="background:var(--slate-mid); padding:1.75rem 1.5rem; text-align:center; text-decoration:none; transition:background var(--transition);"
               onmouseover="this.style.background='var(--slate-light)'" onmouseout="this.style.background='var(--slate-mid)'">
                <i class="fab fa-whatsapp" style="font-size:1.8rem; color:#25d366; margin-bottom:0.6rem; display:block;"></i>
                <p style="color:var(--white); font-weight:700; font-size:0.95rem;">WhatsApp Chat</p>
                <p style="color:rgba(255,255,255,0.55); font-size:0.8rem; margin-top:0.25rem;">Fastest response</p>
            </a>
            <a href="<?= BASE_URL ?>quote.php"
               style="background:var(--orange); padding:1.75rem 1.5rem; text-align:center; text-decoration:none; transition:background var(--transition);"
               onmouseover="this.style.background='var(--orange-dark)'" onmouseout="this.style.background='var(--orange)'">
                <i class="fas fa-clipboard-list" style="font-size:1.8rem; color:var(--white); margin-bottom:0.6rem; display:block;"></i>
                <p style="color:var(--white); font-weight:700; font-size:0.95rem;">Request a Quote</p>
                <p style="color:rgba(255,255,255,0.75); font-size:0.8rem; margin-top:0.25rem;">Free, no obligation</p>
            </a>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
