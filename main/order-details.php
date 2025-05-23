<?php
require_once '../helpers/functions.php';
require_once '../classes/Order.php';
require_once __DIR__ . '/../app/includes/database.php';

use Aries\MiniFrameworkStore\Includes\Database;

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit;
}

$order_id = $_GET['id'] ?? null;
if (!$order_id) {
    header('Location: my-orders.php');
    exit;
}

$dbInstance = new Database();
$db = $dbInstance->getConnection();

try {
    $order = new Order($db);
    $orderDetails = $order->getOrder($order_id);
    $items = $order->getOrderDetails($order_id);

    // Verify that the order belongs to the logged-in user
    if ($orderDetails['customer_id'] != $_SESSION['user_id']) {
        header('Location: my-orders.php');
        exit;
    }
} catch (Exception $e) {
    $_SESSION['error'] = "Error retrieving order details: " . $e->getMessage();
    header('Location: my-orders.php');
    exit;
}

template('header.php');
?>

<div class="container my-5">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h1 class="h3 mb-0">Order Details</h1>
                        <a href="my-orders.php" class="btn btn-secondary">Back to Orders</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5>Order Information</h5>
                            <p><strong>Order Number:</strong> #<?php echo htmlspecialchars($order_id); ?></p>
                            <p><strong>Order Date:</strong> <?php echo date('F j, Y', strtotime($orderDetails['created_at'])); ?></p>
                            <p><strong>Status:</strong> 
                                <span class="badge bg-<?php 
                                    echo match($orderDetails['status_name']) {
                                        'Pending' => 'warning',
                                        'Processing' => 'info',
                                        'Out for Delivery' => 'primary',
                                        'Delivered' => 'success',
                                        default => 'secondary'
                                    };
                                ?>">
                                    <?php echo htmlspecialchars($orderDetails['status_name']); ?>
                                </span>
                            </p>
                            <p><strong>Payment Method:</strong> <?php echo htmlspecialchars($orderDetails['payment_method']); ?></p>
                        </div>
                        <div class="col-md-6">
                            <h5>Shipping Information</h5>
                            <p><strong>Name:</strong> <?php echo htmlspecialchars($orderDetails['guest_name']); ?></p>
                            <p><strong>Phone:</strong> <?php echo htmlspecialchars($orderDetails['guest_phone']); ?></p>
                            <p><strong>Address:</strong> <?php echo htmlspecialchars($orderDetails['guest_address']); ?></p>
                            <?php if ($orderDetails['email']): ?>
                                <p><strong>Email:</strong> <?php echo htmlspecialchars($orderDetails['email']); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <h5>Order Items</h5>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Price</th>
                                    <th>Quantity</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($items as $item): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <?php if ($item['image_path']): ?>
                                                <img src="<?php echo htmlspecialchars($item['image_path']); ?>" 
                                                     alt="<?php echo htmlspecialchars($item['product_name']); ?>" 
                                                     class="img-thumbnail" style="width: 50px; margin-right: 10px;">
                                            <?php endif; ?>
                                            <?php echo htmlspecialchars($item['product_name']); ?>
                                        </div>
                                    </td>
                                    <td>₱<?php echo number_format($item['price'], 2); ?></td>
                                    <td><?php echo $item['quantity']; ?></td>
                                    <td>₱<?php echo number_format($item['subtotal'], 2); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3" class="text-end"><strong>Shipping Fee:</strong></td>
                                    <td>₱<?php echo number_format($orderDetails['shipping_fee'], 2); ?></td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="text-end"><strong>Total:</strong></td>
                                    <td><strong>₱<?php echo number_format($orderDetails['total'], 2); ?></strong></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <?php if ($orderDetails['notes']): ?>
                    <div class="mt-4">
                        <h5>Order Notes</h5>
                        <p><?php echo nl2br(htmlspecialchars($orderDetails['notes'])); ?></p>
                    </div>
                    <?php endif; ?>

                    <?php if ($orderDetails['status_name'] === 'Pending'): ?>
                    <div class="mt-4">
                        <h5>Delivery Information</h5>
                        <p>Estimated delivery time: 3-5 business days.</p>
                        <p>Please prepare the exact amount upon delivery (Cash on Delivery).</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php template('footer.php'); ?> 