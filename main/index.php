<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}



include '../helpers/functions.php';
template('header.php');

use Aries\MiniFrameworkStore\Models\Product;

$products = new Product();

function formatCurrency($amount, $currency = 'PHP') {
    return $currency . ' ' . number_format($amount, 2);
}

function getImageUrl($imagePath) {
    $basePath = '/Ecommerce_IM207/'; // Adjust this to your actual subfolder
    $normalizedPath = preg_replace('#^images/products/#', 'uploads/', $imagePath);
    $parts = explode('/', $normalizedPath);
    $encodedParts = array_map('rawurlencode', $parts);
    $urlPath = implode('/', $encodedParts);
    return $basePath . $urlPath;
}


function fileExists($imagePath) {
    // Remove leading slash if present
    if (substr($imagePath, 0, 1) === '/') {
        $imagePath = substr($imagePath, 1);
    }
    // Convert web path to filesystem path
    $fullPath = __DIR__ . '/../' . str_replace('/', DIRECTORY_SEPARATOR, $imagePath);
    return file_exists($fullPath);
}

?>

<div class="content-wrap">
  <div class="container">
    
  </div>

  <div class="container my-5" style="background-color: #f0e6f7; padding: 20px; border-radius: 15px;">
      <div class="row align-items-center">
          <div class="col-md-12">
              <h1 class="text-center">Welcome to the Myntraa</h1>
              <p class="text-center">Your mantra for modern shopping.</p>
          </div>
      </div>
      <div class="row">
          <h2>Products</h2>
          <?php foreach($products->getAll() as $product): ?>
          <div class="col-md-4">
              <div class="card">
                  <?php
                      $imageUrl = getImageUrl($product['image_path']);
                      // Temporarily disable fileExists check to test image display
                      // if (!fileExists($imageUrl)) {
                      //     $imageUrl = 'assets/images/fallback-product.png'; // fallback image path
                      // }
                  ?>
                  <?php
                      // Debug: output image URL
                      echo '<!-- Image URL: ' . htmlspecialchars($imageUrl) . ' -->';
                  ?>
                  <img src="<?php echo $imageUrl ?>" class="card-img-top" alt="<?php echo htmlspecialchars($product['name']) ?>">
                  <div class="card-body">
                      <h5 class="card-title"><?php echo $product['name'] ?></h5>
                      <h6 class="card-subtitle mb-2 text-body-secondary"><?php echo $formattedAmount = formatCurrency($product['price'], 'PHP') ?></h6>
                      <p class="card-text"><?php echo $product['description'] ?></p>
          <a href="product.php?id=<?php echo $product['id'] ?>" class="btn btn-primary btn-sm shadow-sm">View Product</a>
          <a href="#" class="btn btn-success btn-sm shadow-sm add-to-cart" data-productid="<?php echo $product['id'] ?>" data-quantity=
          "1">Add to Cart</a>
                  </div>
              </div>
          </div>
          <?php endforeach; ?>
      </div>
  </div>
</div>

<?php template('footer.php'); ?>
