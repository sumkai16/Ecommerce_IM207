<?php

use Aries\MiniFrameworkStore\Includes\Database;

class Cart {
    private $db;
    private $session_id;

    public function __construct($db) {
        $this->db = $db;
        if (!isset($_SESSION['cart_session_id'])) {
            $_SESSION['cart_session_id'] = uniqid();
        }
        $this->session_id = $_SESSION['cart_session_id'];
    }

    public function addItem($product_id, $quantity = 1) {
        $user_id = isset($_SESSION['user']) ? $_SESSION['user']['id'] : null;
        
        try {
            // Check if item already exists in cart
            $stmt = $this->db->prepare("SELECT id, quantity FROM cart_items 
                WHERE (user_id = ? OR session_id = ?) AND product_id = ?");
            $stmt->execute([$user_id, $this->session_id, $product_id]);
            $existing_item = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($existing_item) {
                // Update quantity
                $stmt = $this->db->prepare("UPDATE cart_items SET quantity = quantity + ? 
                    WHERE id = ?");
                $result = $stmt->execute([$quantity, $existing_item['id']]);
                if (!$result) {
                    $errorInfo = $stmt->errorInfo();
                    error_log("Cart update error: " . implode(' | ', $errorInfo));
                }
                return $result;
            } else {
                // Add new item
                $stmt = $this->db->prepare("INSERT INTO cart_items 
                    (user_id, session_id, product_id, quantity, created_at, updated_at) 
                    VALUES (?, ?, ?, ?, NOW(), NOW())");
                $result = $stmt->execute([$user_id, $this->session_id, $product_id, $quantity]);
                if (!$result) {
                    $errorInfo = $stmt->errorInfo();
                    error_log("Cart insert error: " . implode(' | ', $errorInfo));
                }
                return $result;
            }
        } catch (PDOException $e) {
            error_log("Cart addItem error: " . $e->getMessage());
            return false;
        }
    }

    public function updateQuantity($cart_item_id, $quantity) {
        if ($quantity <= 0) {
            return $this->removeItem($cart_item_id);
        }
        
        $stmt = $this->db->prepare("UPDATE cart_items SET quantity = ?, updated_at = NOW() 
            WHERE id = ?");
        return $stmt->execute([$quantity, $cart_item_id]);
    }

    public function removeItem($cart_item_id) {
        $stmt = $this->db->prepare("DELETE FROM cart_items WHERE id = ?");
        return $stmt->execute([$cart_item_id]);
    }

    public function getItems() {
        $user_id = isset($_SESSION['user']) ? $_SESSION['user']['id'] : null;
        
        $stmt = $this->db->prepare("
            SELECT ci.*, p.name, p.price, p.image_path 
            FROM cart_items ci 
            JOIN products p ON ci.product_id = p.id 
            WHERE ci.user_id = ? OR ci.session_id = ?
        ");
        $stmt->execute([$user_id, $this->session_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTotal() {
        $items = $this->getItems();
        $total = 0;
        
        foreach ($items as $item) {
            $total += $item['price'] * $item['quantity'];
        }
        
        return $total;
    }

    public function clear() {
        $user_id = isset($_SESSION['user']) ? $_SESSION['user']['id'] : null;
        
        $stmt = $this->db->prepare("DELETE FROM cart_items 
            WHERE user_id = ? OR session_id = ?");
        return $stmt->execute([$user_id, $this->session_id]);
    }
}
