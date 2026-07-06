<?php
// ============================================================
// Namarosa Hardware — Configuration & Database Connection
// ============================================================

// --- Site Configuration ---
define('SITE_NAME',    'Namarosa Hardware');
define('SITE_DOMAIN',  'www.namarosahardware.co.ke');
define('SITE_EMAIL',   'emmanueljuma1972@gmail.com');
define('WHATSAPP_NO',  '254710774174');
define('SITE_PHONE',   '+254 710 774 174');

// --- Database Credentials (update for your host) ---
define('DB_HOST', 'localhost');
define('DB_NAME', 'namarosa_hardware');
define('DB_USER', 'root');       // ← change to your DB username
define('DB_PASS', '');           // ← change to your DB password
define('DB_CHARSET', 'utf8mb4');

// --- Environment ---
define('DEBUG_MODE', false);      // set true only for local dev

// --- File Paths & URL ---
$baseFolder = '/';
if (isset($_SERVER['DOCUMENT_ROOT']) && isset($_SERVER['HTTP_HOST'])) {
    $docRoot = str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']);
    $currentDir = str_replace('\\', '/', dirname(__DIR__));
    if (strpos($currentDir, $docRoot) === 0) {
        $baseFolder = substr($currentDir, strlen($docRoot));
        $baseFolder = '/' . trim($baseFolder, '/');
        if ($baseFolder !== '/') {
            $baseFolder .= '/';
        }
    }
}
define('BASE_URL', $baseFolder);

define('ROOT_PATH',   dirname(__DIR__));
define('UPLOAD_PATH', ROOT_PATH . '/assets/images/products/');
define('UPLOAD_URL',  BASE_URL . 'assets/images/products/');

// --- Session Start ---
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// --- PDO Connection ---
try {
    $dsn = sprintf('mysql:host=%s;dbname=%s;charset=%s', DB_HOST, DB_NAME, DB_CHARSET);
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
} catch (PDOException $e) {
    if (DEBUG_MODE) {
        die('<pre>DB Error: ' . $e->getMessage() . '</pre>');
    }
    die('<h3>Service temporarily unavailable. Please call +254 710 774 174.</h3>');
}

// --- Helpers ---
function e(string $str): string {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

function imageUrl(?string $url): string {
    if (!$url) return '';
    if (preg_match('/^https?:\/\//i', $url)) {
        return $url;
    }
    return BASE_URL . ltrim($url, '/');
}

function getSetting(PDO $pdo, string $key, string $default = ''): string {
    static $cache = [];
    if (!isset($cache[$key])) {
        $stmt = $pdo->prepare('SELECT setting_value FROM settings WHERE setting_key = ?');
        $stmt->execute([$key]);
        $cache[$key] = $stmt->fetchColumn() ?: $default;
    }
    return $cache[$key];
}

function generateRef(): string {
    return 'NHQ-' . strtoupper(substr(uniqid(), -6)) . '-' . date('Y');
}

function formatKES(float $amount): string {
    return 'KES ' . number_format($amount, 2);
}

function slugify(string $text): string {
    return strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $text), '-'));
}

function redirect(string $url): void {
    header('Location: ' . $url);
    exit;
}

function isAdmin(): bool {
    return isset($_SESSION['admin_id']) && !empty($_SESSION['admin_id']);
}

function requireAdmin(): void {
    if (!isAdmin()) {
        redirect(BASE_URL . 'admin/login.php');
    }
}

// Cart helpers
function getCart(): array {
    return $_SESSION['quote_cart'] ?? [];
}

function cartCount(): int {
    return array_sum(array_column(getCart(), 'qty'));
}

function addToCart(int $productId, string $name, string $unit, int $qty = 1): void {
    if (!isset($_SESSION['quote_cart'])) {
        $_SESSION['quote_cart'] = [];
    }
    $key = 'p_' . $productId;
    if (isset($_SESSION['quote_cart'][$key])) {
        $_SESSION['quote_cart'][$key]['qty'] += $qty;
    } else {
        $_SESSION['quote_cart'][$key] = [
            'product_id' => $productId,
            'name'       => $name,
            'unit'       => $unit,
            'qty'        => $qty,
        ];
    }
}

function removeFromCart(string $key): void {
    unset($_SESSION['quote_cart'][$key]);
}

function clearCart(): void {
    $_SESSION['quote_cart'] = [];
}
