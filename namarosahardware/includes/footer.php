<?php
// includes/footer.php
?>

<!-- ========== FOOTER ========== -->
<footer class="site-footer">
    <div class="container footer-grid">

        <!-- Column 1: Brand -->
        <div class="footer-brand">
            <div class="footer-logo">
                <img src="<?= BASE_URL ?>assets/images/logo.png" alt="Namarosa Hardware Logo" class="logo-img" />
                <div class="logo-text">
                    <span class="logo-name">Namarosa</span>
                    <span class="logo-sub">Hardware</span>
                </div>
            </div>
            <p class="footer-tagline">Quality · Reliability · Value</p>
            <p class="footer-desc">Your trusted partner in building and progress. Complete solutions for all your construction and hardware needs in Bungoma County.</p>
            <div class="footer-socials">
                <a href="https://wa.me/<?= WHATSAPP_NO ?>" target="_blank" rel="noopener" aria-label="WhatsApp"><i class="fab fa-whatsapp"></i></a>
                <a href="tel:+254710774174" aria-label="Call us"><i class="fas fa-phone-alt"></i></a>
                <a href="mailto:emmanueljuma1972@gmail.com" aria-label="Email us"><i class="fas fa-envelope"></i></a>
            </div>
        </div>

        <!-- Column 2: Quick Links -->
        <div class="footer-links">
            <h4 class="footer-heading">Quick Links</h4>
            <ul>
                <li><a href="<?= BASE_URL ?>index.php">Home</a></li>
                <li><a href="<?= BASE_URL ?>products.php?cat=building-materials">Building Materials</a></li>
                <li><a href="<?= BASE_URL ?>products.php?cat=structural-steel">Structural Steel</a></li>
                <li><a href="<?= BASE_URL ?>products.php?cat=tools">Power Tools</a></li>
                <li><a href="<?= BASE_URL ?>products.php?cat=electrical">Electrical</a></li>
                <li><a href="<?= BASE_URL ?>products.php?cat=paints">Paints</a></li>
                <li><a href="<?= BASE_URL ?>products.php?cat=plumbing">Plumbing</a></li>
                <li><a href="<?= BASE_URL ?>quote.php">Request a Quote</a></li>
                <li><a href="<?= BASE_URL ?>contact.php">Contact Us</a></li>
            </ul>
        </div>

        <!-- Column 3: Contact -->
        <div class="footer-contact">
            <h4 class="footer-heading">Contact Us</h4>
            <ul class="contact-list">
                <li>
                    <i class="fas fa-map-marker-alt"></i>
                    <span>Main Street, Bungoma,<br>Bungoma County, Kenya</span>
                </li>
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
                    <span>Mon–Sat: 7:30 AM – 6:00 PM<br>Sunday: Closed</span>
                </li>
            </ul>
        </div>

        <!-- Column 4: Director & Map -->
        <div class="footer-director">
            <h4 class="footer-heading">Meet the Director</h4>
            <div class="director-mini">
                <div class="director-info">
                    <strong>Emmanuel Juma</strong>
                    <span>Director, Namarosa Hardware</span>
                    <a href="tel:+254710774174" class="director-call"><i class="fas fa-phone-alt"></i> Call Direct</a>
                </div>
            </div>
            <div class="footer-map">
                <a href="https://maps.google.com/?q=Namarosa+Hardware+Bungoma" target="_blank" rel="noopener" class="map-link">
                    <i class="fab fa-google"></i>
                    <span>View on Google Maps</span>
                </a>
            </div>
            <p class="footer-url">www.namarosahardware.co.ke</p>
        </div>
    </div>

    <div class="footer-bottom">
        <div class="container footer-bottom-inner">
            <span>© <?= date('Y') ?> Namarosa Hardware. All rights reserved.</span>
            <span>Quality · Reliability · Value | Bungoma, Kenya</span>
        </div>
    </div>
</footer>

<!-- ========== WhatsApp FAB ========== -->
<a href="https://wa.me/<?= WHATSAPP_NO ?>?text=Hello%20Namarosa%20Hardware%2C%20I%20would%20like%20to%20enquire%20about..."
   class="whatsapp-fab"
   target="_blank"
   rel="noopener"
   aria-label="Chat on WhatsApp">
    <i class="fab fa-whatsapp"></i>
    <span class="fab-tooltip">WhatsApp Chat</span>
</a>

<!-- ========== Back to Top ========== -->
<button class="back-to-top" id="backToTop" aria-label="Back to top" title="Back to top">
    <i class="fas fa-chevron-up"></i>
</button>

<!-- Add-to-Quote Toast -->
<div class="toast" id="quoteToast" role="status" aria-live="polite">
    <i class="fas fa-check-circle"></i>
    <span id="toastMsg">Item added to quote list!</span>
    <a href="<?= BASE_URL ?>quote.php" class="toast-link">View List →</a>
</div>

<!-- Main JS -->
<script src="<?= BASE_URL ?>assets/js/main.js"></script>
</body>
</html>
