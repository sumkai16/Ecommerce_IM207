<?php

function assets($path) {
    include 'assets/' . $path;
}

function template($path) {
    include __DIR__ . '/../templates/' . $path;
}

function countCart() {
    global $db;
    if (!isset($db)) {
        if (isset($GLOBALS['db'])) {
            $db = $GLOBALS['db'];
        } else {
            return 0;
        }
    }
    require_once __DIR__ . '/../classes/Cart.php';
    $cart = new Cart($db);
    $items = $cart->getItems();
    return count($items);
}

function isLoggedIn() {
    if(isset($_SESSION['user'])) {
        return true;
    }

    return false;
}