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

$dbInstance = new Database();
$db = $dbInstance->getConnection();

try {
    $order = new Order($db);
    $orders = $order->getOrdersByCustomer($_SESSION['user_id']);
} catch (Exception $e) {
    $_SESSION['error'] = "Error retrieving orders: " . $e->getMessage();
    $orders = [];
}

template('header.php');
?>

<div class="container my-5">
    <div class="row">
        <div class="col-md-12">
            <h1 class="mb-4">My Orders</h1>
            
            <?php if (empty($orders)): ?>
                <div class="alert alert-info">
                    <p class="mb-0">You haven't placed any orders yet.</p>
                </div>
                <a href="../main/index.php" class="btn btn-primary">Start Shopping</a>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Order #</th>
                                <th>Date</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Payment Method</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orders as $order): ?>
                                <tr>
                                    <td>#<?php echo htmlspecialchars($order['id']); ?></td>
                                    <td><?php echo date('F j, Y', strtotime($order['created_at'])); ?></td>
                                    <td>₱<?php echo number_format($order['total'], 2); ?></td>
                                    <td>
                                        <span class="badge bg-<?php 
                                            echo match($order['status_name']) {
                                                'Pending' => 'warning',
                                                'Processing' => 'info',
                                                'Out for Delivery' => 'primary',
                                                'Delivered' => 'success',
                                                default => 'secondary'
                                            };
                                        ?>">
                                            <?php echo htmlspecialchars($order['status_name']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo htmlspecialchars($order['payment_method']); ?></td>
                                    <td>
                                        <a href="order-details.php?id=<?php echo $order['id']; ?>" 
                                           class="btn btn-sm btn-primary">
                                            View Details
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php template('footer.php'); ?> 