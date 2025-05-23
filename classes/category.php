<?php include '../helpers/functions.php'; ?>
<?php template('header.php'); ?>
<?php

use Aries\MiniFrameworkStore\Models\Product;

$products = new Product();
$category = $_GET['name'];

function formatCurrency($amount, $currency = 'PHP') {
    return $currency . ' ' . number_format($amount, 2);
}

$productsModel = new Product();
$products = $productsModel->getByCategory($category);
error_log("Category: " . $category);
error_log("Products count: " . count($products));

function getImageUrl($imagePath) {
    $basePath = '/Ecommerce_IM207/'; // Adjust this to your actual subfolder
    $normalizedPath = preg_replace('#^images/products/#', 'uploads/', $imagePath);
    $parts = explode('/', $normalizedPath);
    $encodedParts = array_map('rawurlencode', $parts);
    $urlPath = implode('/', $encodedParts);
    return $basePath . $urlPath;
}

?>
<div class="container my-5">
    <div class="row align-items-center">
        <div class="col-md-12">
            <h1 class="text-center"><?php echo $category ?></h1>
            <p class="text-center">Products found: <?php echo count($products); ?></p>
        </div>
    </div>
    <div class="row">
        <h2>Products</h2>
        <?php foreach($products as $product): ?>
        <div class="col-md-4">
            <div class="card">
                <img src="<?php echo getImageUrl($product['image_path']) ?>" class="card-img-top" alt="...">
                <div class="card-body">
                    <h5 class="card-title"><?php echo $product['name'] ?></h5>
        <h6 class="card-subtitle mb-2 text-body-secondary"><?php echo formatCurrency($product['price'], 'PHP') ?></h6>
                    <p class="card-text"><?php echo $product['description'] ?></p>
                    <a href="product.php?id=<?php echo $product['id'] ?>" class="btn btn-primary">View Product</a>
                    <a href="cart.php?product_id=<?php echo $product['id'] ?>" class="btn btn-success">Add to Cart</a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<?php template('footer.php'); ?>
