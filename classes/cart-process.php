<?php
session_start();

header('Content-Type: application/json');

// Disable error display to prevent breaking JSON response
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(0);

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

// Check if user is logged in
if (!isset($_SESSION['user'])) {
    echo json_encode(['status' => 'error', 'message' => 'You must be logged in to add items to the cart']);
    exit;
}

// Database connection
require_once __DIR__ . '/../app/includes/database.php';
require_once __DIR__ . '/CartClass.php';

use Aries\MiniFrameworkStore\Includes\Database;

$dbInstance = new Database();
$db = $dbInstance->getConnection();

try {
    $cart = new Cart($db);
    $userId = isset($_SESSION['user']) ? $_SESSION['user']['id'] : null;
    $result = $cart->addItem($productId, $quantity);
    // ob_clean(); // Removed to avoid clearing output buffer prematurely
    $cartCount = count($cart->getItems());
    if ($result) {
        echo json_encode(['status' => 'success', 'message' => 'Product added to cart', 'userId' => $userId, 'cartCount' => $cartCount]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to add product to cart', 'userId' => $userId, 'cartCount' => $cartCount]);
    }
} catch (Exception $e) {
    // ob_clean(); // Removed to avoid clearing output buffer prematurely
    error_log($e->getMessage());
    error_log($e->getTraceAsString());
    echo json_encode(['status' => 'error', 'message' => 'An error occurred while processing your request.']);
    exit;
}
