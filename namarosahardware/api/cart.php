<?php
// api/cart.php — AJAX endpoint for quote cart management
require_once __DIR__ . '/../includes/db.php';

header('Content-Type: application/json');
header('X-Content-Type-Options: nosniff');

// Only accept POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

$raw    = file_get_contents('php://input');
$data   = json_decode($raw, true);
$action = $data['action'] ?? '';

switch ($action) {

    case 'add':
        $productId = (int)($data['product_id'] ?? 0);
        $qty       = max(1, (int)($data['qty'] ?? 1));

        if (!$productId) {
            echo json_encode(['success' => false, 'error' => 'Invalid product']);
            exit;
        }

        // Verify product exists and is in stock
        $stmt = $pdo->prepare('SELECT id, name, unit, in_stock FROM products WHERE id = ?');
        $stmt->execute([$productId]);
        $product = $stmt->fetch();

        if (!$product) {
            echo json_encode(['success' => false, 'error' => 'Product not found']);
            exit;
        }

        if (!$product['in_stock']) {
            echo json_encode(['success' => false, 'error' => 'Product is out of stock']);
            exit;
        }

        addToCart($productId, $product['name'], $product['unit'], $qty);

        echo json_encode([
            'success'    => true,
            'cart_count' => cartCount(),
            'message'    => $product['name'] . ' added to quote list'
        ]);
        break;

    case 'remove':
        $key = $data['key'] ?? '';
        if ($key) {
            removeFromCart($key);
        }
        echo json_encode([
            'success'    => true,
            'cart_count' => cartCount()
        ]);
        break;

    case 'update':
        $key = $data['key'] ?? '';
        $qty = max(1, (int)($data['qty'] ?? 1));
        if ($key && isset($_SESSION['quote_cart'][$key])) {
            $_SESSION['quote_cart'][$key]['qty'] = $qty;
        }
        echo json_encode([
            'success'    => true,
            'cart_count' => cartCount()
        ]);
        break;

    case 'clear':
        clearCart();
        echo json_encode(['success' => true, 'cart_count' => 0]);
        break;

    case 'count':
        echo json_encode(['success' => true, 'cart_count' => cartCount()]);
        break;

    default:
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Unknown action']);
}
