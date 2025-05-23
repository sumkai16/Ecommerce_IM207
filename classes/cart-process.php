<?php
session_start();

header('Content-Type: application/json');

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
    exit;
}

$productId = $_POST['productId'] ?? null;
$quantity = intval($_POST['quantity'] ?? 1);

if (!$productId || $quantity < 1) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid product ID or quantity']);
    exit;
}

// Database connection
require_once __DIR__ . '/../app/includes/database.php';
require_once __DIR__ . '/Cart.php';

use Aries\MiniFrameworkStore\Includes\Database;

$dbInstance = new Database();
$db = $dbInstance->getConnection();

try {
    $cart = new Cart($db);
    $userId = isset($_SESSION['user']) ? $_SESSION['user']['id'] : null;
    $result = $cart->addItem($productId, $quantity);
    ob_clean();
    $cartCount = count($cart->getItems());
    if ($result) {
        echo json_encode(['status' => 'success', 'message' => 'Product added to cart', 'userId' => $userId, 'cartCount' => $cartCount]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to add product to cart', 'userId' => $userId, 'cartCount' => $cartCount]);
    }
} catch (Exception $e) {
    ob_clean();
    error_log($e->getMessage());
    error_log($e->getTraceAsString());
    echo json_encode(['status' => 'error', 'message' => 'An error occurred while processing your request.']);
    exit;
}
