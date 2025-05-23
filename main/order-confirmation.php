<?php
require_once '../helpers/functions.php';
require_once '../classes/Order.php';
require_once __DIR__ . '/../app/includes/database.php';

use Aries\MiniFrameworkStore\Includes\Database;

if (!isset($_GET['id'])) {
    header('Location: ../main/index.php');
    exit;
}

$orderId = $_GET['id'];

$dbInstance = new Database();
$db = $dbInstance->getConnection();

try {
    $orderClass = new Order($db);
    $order = $orderClass->getOrder($orderId);
    $orderItems = $orderClass->getOrderDetails($orderId);
} catch (Exception $e) {
    $order = null;
    $orderItems = [];
}

if (!$order) {
    header('Location: ../main/index.php');
    exit;
}

template('header.php');
?>

<style>
    .confirmation-container {
        max-width: 700px;
        margin: 40px auto;
        padding: 30px;
        background: #f9f9f9;
        border-radius: 12px;
        box-shadow: 0 8px 20px rgba(0,0,0,0.1);
        text-align: center;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    .confirmation-container h1 {
        color: #28a745;
        margin-bottom: 20px;
        font-weight: 700;
    }
    .order-number {
        font-size: 1.2rem;
        margin-bottom: 25px;
        color: #333;
    }
    .order-summary {
        text-align: left;
        margin-bottom: 25px;
    }
    .order-summary h3 {
        margin-bottom: 15px;
        color: #007bff;
    }
    .order-summary table {
        width: 100%;
        border-collapse: collapse;
    }
    .order-summary th, .order-summary td {
        padding: 10px;
        border-bottom: 1px solid #ddd;
    }
    .order-summary th {
        background-color: #e9ecef;
        text-align: left;
    }
    .total-row {
        font-weight: 700;
        font-size: 1.1rem;
        background-color: #f1f3f5;
    }
    .delivery-note {
        font-style: italic;
        color: #555;
        margin-bottom: 30px;
    }
    .btn-group {
        display: flex;
        justify-content: center;
        gap: 20px;
    }
    .btn-group a {
        padding: 12px 25px;
        border-radius: 30px;
        text-decoration: none;
        font-weight: 600;
        color: white;
        transition: background-color 0.3s ease;
    }
    .btn-view-orders {
        background-color: #007bff;
    }
    .btn-view-orders:hover {
        background-color: #0056b3;
    }
    .btn-back-home {
        background-color: #6c757d;
    }
    .btn-back-home:hover {
        background-color: #495057;
    }
</style>

<div class="confirmation-container">
    <h1>Thank You for Your Order!</h1>
    <div class="order-number">Order Number: <strong>#<?php echo htmlspecialchars($order['id']); ?></strong></div>
    
    <div class="order-summary">
        <h3>Order Summary</h3>
        <table>
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Qty</th>
                    <th>Price</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orderItems as $item): ?>
                <tr>
                    <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                    <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                    <td>₱<?php echo number_format($item['price'], 2); ?></td>
                    <td>₱<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                </tr>
                <?php endforeach; ?>
                <tr class="total-row">
                    <td colspan="3">Total</td>
                    <td>₱<?php echo number_format($order['total'], 2); ?></td>
                </tr>
                <tr>
                    <td colspan="3">Shipping</td>
                    <td>Free</td>
                </tr>
            </tbody>
        </table>
    </div>
    
    <div class="delivery-note">
        Expect delivery in 3–5 days. Please prepare the payment (Cash on Delivery).
    </div>
    
    <div class="btn-group">
        <a href="../main/my-orders.php" class="btn-view-orders">View Orders</a>
        <a href="../main/index.php" class="btn-back-home">Back to Home</a>
    </div>
</div>

<?php template('footer.php'); ?>
