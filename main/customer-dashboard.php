<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'customer') {
    header('Location: ../auth/login.php');
    exit;
}

include '../helpers/functions.php';
template('header.php');

use Aries\MiniFrameworkStore\Models\Checkout;

$orders = new Checkout();
$userId = $_SESSION['user']['id'];

?>

<div class="container my-5">
    <h2>My Orders</h2>
    <p>Here are your past orders:</p>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Product</th>
                <th>Quantity</th>
                <th>Total Price</th>
                <th>Order Date</th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($orders->getOrdersByUserId($userId) as $order) {
                echo '<tr>';
                echo '<td>' . htmlspecialchars($order['id']) . '</td>';
                echo '<td>' . htmlspecialchars($order['product_name']) . '</td>';
                echo '<td>' . htmlspecialchars($order['quantity']) . '</td>';
                echo '<td>' . htmlspecialchars($order['total_price']) . '</td>';
                echo '<td>' . htmlspecialchars($order['order_date']) . '</td>';
                echo '</tr>';
            }
            ?>
        </tbody>
    </table>
</div>

<?php template('footer.php'); ?>
