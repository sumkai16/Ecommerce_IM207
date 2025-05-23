<?php
// Start output buffering at the very beginning of the file
ob_start();

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once '../templates/header.php';
require_once '../classes/Order.php';
require_once __DIR__ . '/../app/includes/database.php';
use Aries\MiniFrameworkStore\Includes\Database;
$dbInstance = new Database();
$db = $dbInstance->getConnection();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../main/index.php');
    exit;
}

// Validate required fields
$required_fields = ['fullName', 'phone', 'address', 'product_id', 'quantity'];
foreach ($required_fields as $field) {
    if (!isset($_POST[$field]) || empty($_POST[$field])) {
        $_SESSION['error'] = "Please fill in all required fields.";
        header('Location: direct-checkout.php?product_id=' . $_POST['product_id'] . '&quantity=' . $_POST['quantity']);
        exit;
    }
}

// Get product details
$stmt = $db->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$_POST['product_id']]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    $_SESSION['error'] = "Product not found.";
    header('Location: ../main/index.php');
    exit;
}

// Calculate total
$quantity = (int)$_POST['quantity'];
$total = $product['price'] * $quantity;

// Create order data
$orderData = [
    'customer_id' => isset($_SESSION['user']) ? $_SESSION['user']['id'] : null,
    'guest_name' => $_POST['fullName'],
    'guest_phone' => $_POST['phone'],
    'guest_address' => $_POST['address'],
    'email' => $_POST['email'] ?? '',
    'notes' => $_POST['notes'] ?? '',
    'payment_method' => 'COD',
    'total' => $total,
    'shipping_fee' => 0.00,
    'status_id' => 1 // Pending status
];

// Create order items
$orderItems = [
    [
        'product_id' => $product['id'],
        'quantity' => $quantity,
        'price' => $product['price']
    ]
];

try {
    $order = new Order($db);
    $orderId = $order->create($orderData, $orderItems);
    
    if ($orderId) {
        // Send order confirmation email
        $to = $_POST['email'] ?? '';
        if (filter_var($to, FILTER_VALIDATE_EMAIL)) {
            $subject = "Order Confirmation - Order #$orderId";
            $itemsList = "";
            foreach ($orderItems as $item) {
                $itemsList .= "Product ID: " . $item['product_id'] . " - Quantity: " . $item['quantity'] . " - Price: ₱" . number_format($item['price'], 2) . "\n";
            }
            $message = "Thank you for your order!\n\n";
            $message .= "Order Number: #$orderId\n";
            $message .= "Items:\n" . $itemsList . "\n";
            $message .= "Total Amount: ₱" . number_format($total, 2) . "\n";
            $message .= "Shipping Address: " . $_POST['address'] . "\n";
            $message .= "Status: Pending\n";
            $message .= "Payment Method: COD\n\n";
            $message .= "We will process your order shortly.\n";
            
            $headers = "From: no-reply@yourstore.com\r\n";
            $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
            
            mail($to, $subject, $message, $headers);
        }

        $_SESSION['success'] = "Order placed successfully! Your order number is #" . $orderId;
        header('Location: ../main/order-confirmation.php?id=' . $orderId);
        exit;
    } else {
        throw new Exception("Failed to create order");
    }
} catch (Exception $e) {
    $_SESSION['error'] = "Error placing order: " . $e->getMessage();
    header('Location: direct-checkout.php?product_id=' . $_POST['product_id'] . '&quantity=' . $_POST['quantity']);
    exit;
}

// End output buffering and flush the output
ob_end_flush();
?> 