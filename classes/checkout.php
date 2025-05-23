<?php include '../helpers/functions.php'; ?>
<?php template('header.php'); ?>
<?php

use Aries\MiniFrameworkStore\Models\Checkout;

$checkout = new Checkout();

$superTotal = 0;
$orderId = null;

if(isset($_SESSION['cart'])) {
    foreach($_SESSION['cart'] as $item) {
        $superTotal += $item['total'] * $item['quantity'];
    }
}

$amounLocale = 'en_PH';
$pesoFormatter = new NumberFormatter($amounLocale, NumberFormatter::CURRENCY);

if(isset($_POST['submit'])) {
    try {
        $name = $_POST['name'];
        $address = $_POST['address'];
        $phone = $_POST['phone'];

        if(isset($_SESSION['user'])) {
            $orderId = $checkout->userCheckout([
                'user_id' => $_SESSION['user']['id'],
                'total' => $superTotal
            ]);
        } else {
            $orderId = $checkout->guestCheckout([
                'name' => $name,
                'address' => $address,
                'phone' => $phone,
                'total' => $superTotal
            ]);
        }

        foreach($_SESSION['cart'] as $item) {
            $checkout->saveOrderDetails([
                'order_id' => $orderId,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
                'subtotal' => $item['total'] * $item['quantity']
            ]);
        }

        unset($_SESSION['cart']);

        echo "<script>alert('Order placed successfully!'); window.location.href='/main/order-confirmation.php?order_id=" . $orderId . "'</script>";
    } catch (Exception $e) {
        echo "<script>alert('Error placing order: " . $e->getMessage() . "');</script>";
    }
}

?>

<div class="container my-5">
<div class="row">
        <h1>Checkout</h1>
        <h2>Cart Details</h2>
        <table class="table table-bordered">
            <?php if(countCart() > 0): ?>
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($_SESSION['cart'] as $item): ?>
                    <tr>
                        <td><?php echo $item['name'] ?></td>
                        <td><?php echo $item['quantity'] ?></td>
                        <td><?php echo $pesoFormatter->formatCurrency($item['price'], 'PHP') ?></td>
                        <td><?php echo $pesoFormatter->formatCurrency($item['total'], 'PHP') ?></td>
                    </tr>
                <?php endforeach; ?>
                <tr>
                    <td colspan="3" class="text-end"><strong>Total</strong></td>
                    <td><strong><?php echo $pesoFormatter->formatCurrency($superTotal, 'PHP') ?></strong></td>
                </tr>
            </tbody>
            <?php else: ?>
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                    <tr></tr>
                <tr>
                    <td colspan="3" class="text-end"><strong>Total</strong></td>
                    <td><strong></td>
                </tr>
            </tbody>
            <?php endif; ?>
        </table>
    </div>
    <div class="row">
        <div class="col-md-12">
            <h2>Shipping Information</h2>
            <?php if(countCart() == 0): ?>
                <p>Your cart is empty.</p>
                <a href="../main/index.php" class="btn btn-primary">Continue Shopping</a>
            <?php else: ?>
                <form action="checkout.php" method="POST">
                    <div class="mb-3">
                        <label for="name" class="form-label">Full Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="phone" class="form-label">Phone Number</label>
                        <input type="text" class="form-control" id="phone" name="phone" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email (optional)</label>
                        <input type="email" class="form-control" id="email" name="email">
                    </div>
                    <div class="mb-3">
                        <label for="address" class="form-label">Address (Street, City, Province, Zip Code)</label>
                        <input type="text" class="form-control" id="address" name="address" required>
                    </div>
                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes (optional)</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label>Payment Method</label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="payment_method" id="cod" value="cod" checked>
                            <label class="form-check-label" for="cod">
                                Cash on Delivery (COD)
                            </label>
                        </div>
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="terms" name="terms" required>
                        <label class="form-check-label" for="terms">I agree to the terms and conditions</label>
                    </div>
                    <button type="submit" class="btn btn-success" name="submit">Place Order (COD)</button>
                    <a href="cart.php" class="btn btn-primary">View Cart</a>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php template('footer.php'); ?>
