# Namarosa Hardware Website
## www.namarosahardware.co.ke

---

## Project Overview
Full-stack PHP/MySQL website for Namarosa Hardware, Bungoma, Kenya.
Built with vanilla PHP + PDO, MySQL, HTML5, CSS3, and JavaScript — zero framework dependencies.

---

## File Structure

```
namarosa/
├── index.php               # Homepage
├── products.php            # Product catalog (filterable)
├── quote.php               # RFQ form + quote cart
├── contact.php             # Contact page + map
├── .htaccess               # Apache rules (security, caching)
├── database.sql            # Full schema + seed data
│
├── includes/
│   ├── db.php              # PDO connection + helpers + session
│   ├── header.php          # Shared HTML header / navbar
│   └── footer.php          # Shared footer + WhatsApp FAB
│
├── api/
│   └── cart.php            # AJAX: add/remove/count cart items
│
├── admin/
│   ├── login.php           # Admin login
│   ├── logout.php          # Session destroy
│   ├── index.php           # Dashboard (stats + recent quotes)
│   ├── quotes.php          # View / update quote requests
│   ├── products.php        # Add / edit / delete products + toggle stock
│   ├── settings.php        # Update contact info, hours, maps embed
│   ├── .htaccess           # Disable directory listing
│   └── partials/
│       └── admin_header.php
│
└── assets/
    ├── css/style.css       # Full design system (4KB gzipped)
    ├── js/main.js          # Interactions, AJAX cart, animations
    └── images/
        ├── favicon.svg
        ├── hero-hardware.jpg   ← ADD YOUR OWN HERO IMAGE
        └── products/           ← UPLOAD product images here
```

---

## Quick Setup (cPanel / Shared Hosting)

### Step 1 — Upload Files
Upload the entire `namarosa/` folder contents to `public_html/` (or your domain root).

### Step 2 — Create Database
1. In cPanel → MySQL Databases, create a new database: `namarosa_hardware`
2. Create a database user and assign ALL PRIVILEGES
3. In phpMyAdmin, select the database → Import → upload `database.sql`

### Step 3 — Update DB Credentials
Edit `includes/db.php`:
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'namarosa_hardware');
define('DB_USER', 'your_cpanel_db_user');
define('DB_PASS', 'your_password');
```

### Step 4 — Add Hero Image
Upload a 1920×800px hardware store photo as:
`assets/images/hero-hardware.jpg`

Use a royalty-free construction site photo from Unsplash or Pexels.

### Step 5 — First Login
Go to: `www.namarosahardware.co.ke/admin/login.php`
- Username: `admin`
- Password: `password` (⚠️ CHANGE THIS IMMEDIATELY in Settings)

### Step 6 — Enable HTTPS
In cPanel → SSL/TLS → Install Let's Encrypt SSL (free).
Then uncomment the HTTPS redirect lines in `.htaccess`.

---

## Admin Panel Features

| Feature | URL |
|---|---|
| Dashboard | /admin/index.php |
| View / respond to quotes | /admin/quotes.php |
| Add / edit / delete products | /admin/products.php |
| Toggle product stock status | /admin/products.php |
| Update store hours & contact | /admin/settings.php |
| Change admin password | /admin/settings.php |

---

## Email Setup
The site uses PHP `mail()` for quote notifications. For reliable delivery:
- Configure your hosting's mail server (cPanel → Email Accounts)
- OR replace `@mail(...)` in `quote.php` with PHPMailer + Gmail SMTP:

```php
// Install: composer require phpmailer/phpmailer
use PHPMailer\PHPMailer\PHPMailer;
$mail = new PHPMailer(true);
$mail->isSMTP();
$mail->Host = 'smtp.gmail.com';
$mail->SMTPAuth = true;
$mail->Username = 'your@gmail.com';
$mail->Password = 'your_app_password';  // Gmail App Password
$mail->SMTPSecure = 'tls';
$mail->Port = 587;
```

---

## WhatsApp Integration
The floating WhatsApp button links to:
`https://wa.me/254710774174`

Pre-filled messages are used on:
- Homepage CTA → "Hello Namarosa Hardware, I would like to enquire about..."
- Quote success → "Hi, I just submitted quote NHQ-XXXX-2024..."
- Contact page → "Hi Emmanuel, I need directions..."

Change the number in `includes/db.php`:
```php
define('WHATSAPP_NO', '254710774174');
```

---

## Adding Product Images
1. Upload images to `assets/images/products/`
   Recommended size: 600×450px, JPEG, < 100KB
2. In Admin → Products → Edit Product → Image URL:
   Enter: `/assets/images/products/your-image.jpg`

---

## Brand Colors (CSS Variables)
```css
--orange:     #e07820;   /* Primary brand orange */
--slate:      #1e2d3d;   /* Dark header/footer */
--white:      #ffffff;
--off-white:  #f8f6f3;   /* Section backgrounds */
```

---

## Performance Notes
- CSS and JS are single files (no build tool needed)
- Images should be compressed: use squoosh.app or tinypng.com
- Enable Gzip via .htaccess (already configured)
- Browser caching headers set for 1 month on images

---

## Security
- All user inputs sanitised with htmlspecialchars (`e()` helper)
- PDO prepared statements prevent SQL injection
- Honeypot fields on all forms (anti-spam)
- Admin requires PHP session authentication
- `.sql` files blocked from public access via .htaccess
- `Options -Indexes` prevents directory listing

---

## Credits
- Design: Namarosa Hardware brand (orange + dark slate)
- Icons: Font Awesome 6 (CDN)
- Fonts: Bebas Neue (display) + Inter (body) via Google Fonts
- Map: Google Maps Embed API (no key required for embed)
- WhatsApp: wa.me shortlink

---
**Namarosa Hardware — Quality · Reliability · Value**
Main Street, Bungoma | +254 710 774 174
