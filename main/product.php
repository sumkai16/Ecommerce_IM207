<?php include '../helpers/functions.php'; ?>
<?php

if(!isset($_GET['id'])) {
    header('Location: index.php');
    exit();
}

?>
<?php template('header.php'); ?>
<?php

use Aries\MiniFrameworkStore\Models\Product;

$productId = $_GET['id'];
$products = new Product();
$product = $products->getById($productId);

function getImageUrl($imagePath) {
    $basePath = '/Ecommerce_IM207/'; // Adjust this to your actual subfolder
    $normalizedPath = preg_replace('#^images/products/#', 'uploads/', $imagePath);
    $parts = explode('/', $normalizedPath);
    $encodedParts = array_map('rawurlencode', $parts);
    $urlPath = implode('/', $encodedParts);
    return $basePath . $urlPath;
}

$amounLocale = 'en_PH';
$pesoFormatter = new NumberFormatter($amounLocale, NumberFormatter::CURRENCY);

$imageUrl = getImageUrl($product['image_path']);

?>

<div class="container my-5">
    <div class="row">
        <div class="col-md-6">
            <img src="<?php echo $imageUrl ?>" alt="Product Image" class="img-fluid">
        </div>
        <div class="col-md-6">
            <h1><?php echo $product['name'] ?></h1>
            <h4 class="text-body-secondary"><?php echo $formattedAmount = $pesoFormatter->formatCurrency($product['price'], 'PHP'); ?></h4>
            <p><?php echo $product['description']; ?></p>
            <div class="d-flex gap-3">
                <?php if (isset($_SESSION['user'])): ?>
                    <a href="..//classes/cart-process.php" class="btn btn-success add-to-cart" data-productid="<?php echo $product['id'] ?>" data-quantity="1">Add to Cart</a>
                    <a href="..//classes/direct-checkout.php?product_id=<?php echo $product['id']; ?>&quantity=1" class="btn btn-warning">Buy Now</a>
                <?php else: ?>
                    <a href="../auth/login.php" class="btn btn-primary">Login to Purchase</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php template('footer.php'); ?>
