<?php
require_once '../includes/header.php';
require_once '../classes/Cart.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

if (!isset($_POST['item_id'])) {
    echo json_encode(['success' => false, 'message' => 'Missing item ID']);
    exit;
}

try {
    $cart = new Cart($db);
    $success = $cart->removeItem($_POST['item_id']);
    
    echo json_encode(['success' => $success]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
} 