<?php
require_once 'includes/header.php';
require_once 'classes/Cart.php';
require_once 'classes/Order.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: cart.php');
    exit;
}

// Validate required fields
$required_fields = ['fullName', 'phone', 'address'];
foreach ($required_fields as $field) {
    if (empty($_POST[$field])) {
        $_SESSION['error'] = "Please fill in all required fields.";
        header('Location: checkout.php');
        exit;
    }
}

try {
    $cart = new Cart($db);
    $items = $cart->getItems();
    
    if (empty($items)) {
        throw new Exception("Your cart is empty.");
    }
    
    $order = new Order($db);
    
    // Prepare order data
    $order_data = [
        'customer_id' => $_SESSION['user_id'] ?? null,
        'guest_name' => $_POST['fullName'],
        'guest_phone' => $_POST['phone'],
        'guest_address' => $_POST['address'],
        'email' => $_POST['email'] ?? null,
        'notes' => $_POST['notes'] ?? null,
        'total' => $cart->getTotal(),
        'shipping_fee' => 0, // Free shipping
        'payment_method' => 'COD'
    ];
    
    // Create the order
    $order_id = $order->create($order_data, $items);
    
    // Clear the cart
    $cart->clear();
    
    // Redirect to order confirmation
    header("Location: order_confirmation.php?id=" . $order_id);
    exit;
    
} catch (Exception $e) {
    $_SESSION['error'] = $e->getMessage();
    header('Location: checkout.php');
    exit;
} 