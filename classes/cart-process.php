<?php
   
   require 'vendor/autoload.php';

    use Aries\MiniFrameworkStore\Models\Product;

    session_start();

    $product_id = intval($_POST['productId']);
    $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;

    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    $product = new Product();
    $productDetails = $product->getById($_POST['productId']);

    // Ensure the cart only includes product ID and quantity
    $_SESSION['cart'][$product_id] = [
        'product_id' => $product_id,
        'quantity' => $quantity,
        'name' => $productDetails['name'],
        'price' => $productDetails['price'],
        'image_path' => $productDetails['image_path'],
        'total' => $productDetails['price'] * $quantity
    ];

    echo json_encode(['status' => 'success', 'message' => 'Product added to cart']);

?>