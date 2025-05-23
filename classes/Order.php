<?php

class Order {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function create($data, $cart_items) {
        try {
            $this->db->beginTransaction();

            // Validate required data
            if (!isset($data['total']) || !is_numeric($data['total'])) {
                throw new Exception("Invalid order total");
            }

            // Create order
            $stmt = $this->db->prepare("
                INSERT INTO orders (
                    customer_id, guest_name, guest_phone, guest_address, 
                    email, notes, total, shipping_fee, payment_method,
                    status_id, created_at, updated_at
                ) VALUES (
                    :customer_id, :guest_name, :guest_phone, :guest_address,
                    :email, :notes, :total, :shipping_fee, :payment_method,
                    :status_id, NOW(), NOW()
                )
            ");

            $stmt->execute([
                ':customer_id' => $data['customer_id'],
                ':guest_name' => $data['guest_name'],
                ':guest_phone' => $data['guest_phone'],
                ':guest_address' => $data['guest_address'],
                ':email' => $data['email'],
                ':notes' => $data['notes'],
                ':total' => $data['total'],
                ':shipping_fee' => $data['shipping_fee'],
                ':payment_method' => $data['payment_method'],
                ':status_id' => $data['status_id']
            ]);

            $order_id = $this->db->lastInsertId();

            // Create order details
            $stmt = $this->db->prepare("
                INSERT INTO order_details (
                    order_id, product_id, quantity, price, subtotal
                ) VALUES (
                    :order_id, :product_id, :quantity, :price, :subtotal
                )
            ");

            foreach ($cart_items as $item) {
                if (!isset($item['product_id']) || !isset($item['quantity']) || !isset($item['price'])) {
                    throw new Exception("Invalid cart item data");
                }

                $subtotal = $item['price'] * $item['quantity'];
                $stmt->execute([
                    ':order_id' => $order_id,
                    ':product_id' => $item['product_id'],
                    ':quantity' => $item['quantity'],
                    ':price' => $item['price'],
                    ':subtotal' => $subtotal
                ]);
            }

            $this->db->commit();
            return $order_id;

        } catch (Exception $e) {
            $this->db->rollBack();
            throw new Exception("Failed to create order: " . $e->getMessage());
        }
    }

    public function getOrder($order_id) {
        try {
            $stmt = $this->db->prepare("
                SELECT o.*, os.name as status_name
                FROM orders o
                JOIN order_statuses os ON o.status_id = os.id
                WHERE o.id = :order_id
            ");
            $stmt->execute([':order_id' => $order_id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            throw new Exception("Failed to retrieve order: " . $e->getMessage());
        }
    }

    public function getOrderDetails($order_id) {
        try {
            $stmt = $this->db->prepare("
                SELECT od.*, p.name as product_name, p.image_path
                FROM order_details od
                JOIN products p ON od.product_id = p.id
                WHERE od.order_id = :order_id
            ");
            $stmt->execute([':order_id' => $order_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            throw new Exception("Failed to retrieve order details: " . $e->getMessage());
        }
    }

    public function updateStatus($order_id, $status_id) {
        try {
            $stmt = $this->db->prepare("
                UPDATE orders 
                SET status_id = :status_id, updated_at = NOW()
                WHERE id = :order_id
            ");
            return $stmt->execute([
                ':status_id' => $status_id,
                ':order_id' => $order_id
            ]);
        } catch (Exception $e) {
            throw new Exception("Failed to update order status: " . $e->getMessage());
        }
    }

    public function getOrdersByCustomer($customer_id) {
        try {
            $stmt = $this->db->prepare("
                SELECT o.*, os.name as status_name
                FROM orders o
                JOIN order_statuses os ON o.status_id = os.id
                WHERE o.customer_id = :customer_id
                ORDER BY o.created_at DESC
            ");
            $stmt->execute([':customer_id' => $customer_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            throw new Exception("Failed to retrieve customer orders: " . $e->getMessage());
        }
    }

    public function getAllOrders($limit = 10, $offset = 0) {
        try {
            $stmt = $this->db->prepare("
                SELECT o.*, os.name as status_name
                FROM orders o
                JOIN order_statuses os ON o.status_id = os.id
                ORDER BY o.created_at DESC
                LIMIT :limit OFFSET :offset
            ");
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            throw new Exception("Failed to retrieve orders: " . $e->getMessage());
        }
    }

    public function getTotalOrders() {
        try {
            $stmt = $this->db->query("SELECT COUNT(*) FROM orders");
            return $stmt->fetchColumn();
        } catch (Exception $e) {
            throw new Exception("Failed to get total orders: " . $e->getMessage());
        }
    }
} 