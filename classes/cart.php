<?php include 'helpers/functions.php'; ?>
<?php template('header.php'); ?>
<?php

if(isset($_GET['remove'])) {
    $productId = $_GET['remove'];
    if(isset($_SESSION['cart'][$productId])) {
        unset($_SESSION['cart'][$productId]);
        echo "<script>alert('Product removed from cart');</script>";
    }
}

$amounLocale = 'en_PH';
$pesoFormatter = new NumberFormatter($amounLocale, NumberFormatter::CURRENCY);

?>

<div class="container my-5">
    <div class="row">
        <div class="col-md-12">
            <h1>Cart</h1>
            <?php if(countCart() == 0): ?>
                <p>Your cart is empty.</p>
                <a href="index.php" class="btn btn-primary">Continue Shopping</a>
            <?php else: ?>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Quantity</th>
                            <th>Price</th>
                            <th>Subtotal</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($_SESSION['cart'] as $item): ?>
                            <tr>
                                <td><?php echo $item['name'] ?></td>
                                <td><?php echo $item['quantity'] ?></td>
                                <td><?php echo $pesoFormatter->formatCurrency($item['price'], 'PHP') ?></td>
                                <td><?php echo $pesoFormatter->formatCurrency($item['total'], 'PHP') ?></td>
                                <td><a href="cart.php?remove=<?php echo $item['product_id'] ?>" class="btn btn-danger">Remove</a></td>
                                <?php $superTotal = isset($superTotal) ? $superTotal + $item['total'] : $item['total']; ?>
                            </tr>
                        <?php endforeach; ?>
                        <tr>
                            <td colspan="3" class="text-end"><strong>Total</strong></td>
                            <td colspan="2"><strong><?php echo $pesoFormatter->formatCurrency($superTotal, 'PHP') ?></strong></td>
                    </tbody>
                </table>

                <a href="checkout.php" class="btn btn-success">Checkout</a>
                <a href="index.php" class="btn btn-primary">Continue Shopping</a>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php template('footer.php'); ?>