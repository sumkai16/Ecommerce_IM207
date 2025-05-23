<?php
require_once '../helpers/functions.php';
require_once __DIR__ . '/../app/includes/database.php';

use Aries\MiniFrameworkStore\Includes\Database;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: cart.php');
    exit;
}

$cart_item_id = $_POST['cart_item_id'] ?? null;
$quantity = (int)($_POST['quantity'] ?? 0);

if (!$cart_item_id || $quantity < 1) {
    $_SESSION['error'] = "Invalid quantity or cart item.";
    header('Location: cart.php');
    exit;
}

$dbInstance = new Database();
$db = $dbInstance->getConnection();

try {
    $cart = new Cart($db);
    $cart->updateQuantity($cart_item_id, $quantity);
    $_SESSION['success'] = "Cart updated successfully.";
} catch (Exception $e) {
    $_SESSION['error'] = "Error updating cart: " . $e->getMessage();
}

header('Location: cart.php');
exit; 