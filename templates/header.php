<?php include_once dirname(__DIR__) . '/helpers/functions.php'; ?>

<?php

require __DIR__ . '/../vendor/autoload.php';

use Aries\MiniFrameworkStore\Models\Category;

$categories = new Category();

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Get user role safely
$userRole = isset($_SESSION['user']) && isset($_SESSION['user']['role']) ? $_SESSION['user']['role'] : null;

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Myntraa</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body class="store-body d-flex flex-column min-vh-100">
    <nav class="navbar navbar-expand-lg bg-body-tertiary">
    <div class="container">
        <a class="navbar-brand" href="../main/index.php">Myntraa</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link active" aria-current="page" href="../main/index.php">Home</a>
                </li>
                <?php if ($userRole === 'admin'): ?>
                <li class="nav-item">
                    <a class="nav-link" href="../classes/add-product.php">Add Product</a>
                </li>
                <?php endif; ?>
                <?php if (isset($_SESSION['user'])): ?>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Categories
                    </a>
                <ul class="dropdown-menu">
<?php foreach($categories->getAll() as $category): ?>
    <li><a class="dropdown-item" href="../classes/category.php?name=<?php echo urlencode($category['name']);  ?>"><?php echo $category['name']; ?></a></li>
<?php endforeach; ?>
                </ul>
                </li>
                <?php endif; ?>
                <?php if ($userRole === 'admin'): ?>
                <li class="nav-item">
                    <a class="nav-link" href="../main/dashboard.php">Review Checkouts</a>
                </li>
                <?php elseif ($userRole === 'customer'): ?>
                <li class="nav-item">
                    <a class="nav-link" href="../main/customer-dashboard.php">My Checkouts</a>
                </li>
                <?php endif; ?>
            </ul>
            <?php
            $currentScript = basename($_SERVER['SCRIPT_NAME']);
            if ($currentScript !== 'order-confirmation.php'):
            ?>
            <a class="icon-link" href="../classes/cart.php">
                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" fill="#000000" version="1.1" id="Capa_1" width="20px" height="20px" viewBox="0 0 902.86 902.86" xml:space="preserve">
                    <g>
                        <g>
                            <path d="M671.504,577.829l110.485-432.609H902.86v-68H729.174L703.128,179.2L0,178.697l74.753,399.129h596.751V577.829z     M685.766,247.188l-67.077,262.64H131.199L81.928,246.756L685.766,247.188z"/>
                            <path d="M578.418,825.641c59.961,0,108.743-48.783,108.743-108.744s-48.782-108.742-108.743-108.742H168.717    c-59.961,0-108.744,48.781-108.744,108.742s48.782,108.744,108.744,108.744c59.962,0,108.743-48.783,108.743-108.744    c0-14.4-2.821-28.152-7.927-40.742h208.069c-5.107,12.59-7.928,26.342-7.928,40.742    C469.675,776.858,518.457,825.641,578.418,825.641z M209.46,716.897c0,22.467-18.277,40.744-40.743,40.744    c-22.466,0-40.744-18.277-40.744-40.744c0-22.465,18.277-40.742,40.744-40.742C191.183,676.155,209.46,694.432,209.46,716.897z     M619.162,716.897c0,22.467-18.277,40.744-40.743,40.744s-40.743-18.277-40.743-40.744c0-22.465,18.277-40.742,40.743-40.742    S619.162,694.432,619.162,716.897z"/>
                        </g>
                    </g>
                </svg>
                <span class="badge bg-success"><?php echo countCart(); ?></span>
            </a>
            <?php endif; ?>
            <ul class="navbar-nav mb-2 mb-lg-0">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Hello, <?php echo isset($_SESSION['user']) ? $_SESSION['user']['name'] : 'Guest'; ?>
                    </a>
                    <ul class="dropdown-menu">
                        <?php if (isset($_SESSION['user'])): ?>
                        <li><a class="dropdown-item" href="../main/my-account.php">My Account</a></li>
                        <li><a class="dropdown-item" href="../auth/logout.php">Logout</a></li>
                        <?php if ($userRole === 'admin'): ?>
                        <li><a class="dropdown-item" href="../main/dashboard.php">Review Checkouts</a></li>
                        <?php endif; ?>
                    <?php else: ?>
                        <li><a class="dropdown-item" href="../auth/login.php">Login</a></li>
                        <li><a class="dropdown-item" href="../auth/register.php">Register</a></li>
                    <?php endif; ?>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
    </nav>
