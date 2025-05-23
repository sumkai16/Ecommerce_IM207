<?php
require_once '../classes/Order.php';
require_once __DIR__ . '/../app/includes/database.php';

use Aries\MiniFrameworkStore\Includes\Database;

if (!isset($_GET['id'])) {
    header('Location: ../main/index.php');
    exit;
}

$orderId = (int)$_GET['id'];

$dbInstance = new Database();
$db = $dbInstance->getConnection();

try {
    $order = new Order($db);
    $orderDetails = $order->getOrder($orderId);
    $orderItems = $order->getOrderDetails($orderId);
} catch (Exception $e) {
    $_SESSION['error'] = "Error retrieving order details: " . $e->getMessage();
    header('Location: ../main/index.php');
    exit;
}

require_once '../templates/header.php';
?>

<div class="container my-5">
    <h2>Thank you for your order!</h2>
    <p>Your order number is <strong>#<?php echo htmlspecialchars($orderId); ?></strong>.</p>

    <h4>Order Summary</h4>
    <ul class="list-group mb-3">
        <?php foreach ($orderItems as $item): ?>
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <?php echo htmlspecialchars($item['product_name']); ?> x <?php echo $item['quantity']; ?>
                <span>₱<?php echo number_format($item['price'] * $item['quantity'], 2); ?></span>
            </li>
        <?php endforeach; ?>
        <li class="list-group-item d-flex justify-content-between">
            <strong>Subtotal</strong>
            <strong>₱<?php echo number_format($orderDetails['total'], 2); ?></strong>
        </li>
        <li class="list-group-item d-flex justify-content-between">
            <strong>Shipping</strong>
            <strong>Free</strong>
        </li>
        <li class="list-group-item d-flex justify-content-between">
            <strong>Total</strong>
            <strong>₱<?php echo number_format($orderDetails['total'], 2); ?></strong>
        </li>
    </ul>

    <div class="alert alert-info">
        <strong>Delivery Note:</strong> Expect delivery in 3–5 days. Prepare the payment.
    </div>

    <a href="../main/my-orders.php" class="btn btn-primary me-2">View Orders</a>
    <a href="../main/index.php" class="btn btn-secondary">Back to Home</a>
</div>

<?php require_once '../templates/footer.php'; ?>
