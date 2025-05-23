<?php include_once '../helpers/functions.php'; ?>
<?php template('header.php'); ?>
<?php

require_once '../helpers/functions.php';
require_once __DIR__ . '/../app/includes/database.php';

use Aries\MiniFrameworkStore\Includes\Database;

$dbInstance = new Database();
$db = $dbInstance->getConnection();

if(isset($_GET['remove'])) {
    $productId = $_GET['remove'];
    if(isset($_SESSION['cart'][$productId])) {
        unset($_SESSION['cart'][$productId]);
        echo "<script>alert('Product removed from cart');</script>";
    }
}

$amounLocale = 'en_PH';
$pesoFormatter = new NumberFormatter($amounLocale, NumberFormatter::CURRENCY);

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

// Initialize cart
$cart = new Cart($db);

// Handle remove action
if(isset($_GET['remove'])) {
    $cart_item_id = $_GET['remove'];
    $cart->removeItem($cart_item_id);
    $_SESSION['success'] = "Product removed from cart";
    header('Location: cart.php');
    exit;
}

// Get cart items
$cart_items = $cart->getItems();
$total = $cart->getTotal();

?>

<div class="container my-5">
    <div class="row">
        <div class="col-md-12">
            <h1 class="mb-4">Shopping Cart</h1>
            
            <?php if(empty($cart_items)): ?>
                <div class="alert alert-info">
                    <p class="mb-0">Your cart is empty.</p>
                </div>
                <a href="../main/index.php" class="btn btn-primary">Continue Shopping</a>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
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
                            <?php foreach($cart_items as $item): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <?php if($item['image_path']): ?>
                                                <?php 
                                                    $normalized_path = str_replace('\\', '/', $item['image_path']);
                                                    $path_parts = explode('/', $normalized_path);
                                                    $encoded_parts = array_map('rawurlencode', $path_parts);
                                                    $encoded_path = implode('/', $encoded_parts);
                                                ?>
                                                <img src="<?php echo htmlspecialchars('/Ecommerce_IM207/' . $encoded_path); ?>" 
                                                     alt="<?php echo htmlspecialchars($item['name']); ?>" 
                                                     class="img-thumbnail" style="width: 50px; margin-right: 10px;">
                                            <?php endif; ?>
                                            <?php echo htmlspecialchars($item['name']); ?>
                                        </div>
                                    </td>
                                    <td>
                                        <form action="update-cart.php" method="POST" class="d-flex align-items-center">
                                            <input type="hidden" name="cart_item_id" value="<?php echo $item['id']; ?>">
                                            <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" 
                                                   min="1" class="form-control" style="width: 70px;">
                                            <button type="submit" class="btn btn-sm btn-outline-primary ms-2">Update</button>
                                        </form>
                                    </td>
                                    <td>₱<?php echo number_format($item['price'], 2); ?></td>
                                    <td>₱<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                                    <td>
                                        <a href="cart.php?remove=<?php echo $item['id']; ?>" 
                                           class="btn btn-sm btn-danger"
                                           onclick="return confirm('Are you sure you want to remove this item?')">
                                            Remove
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" class="text-end"><strong>Total:</strong></td>
                                <td colspan="2"><strong>₱<?php echo number_format($total, 2); ?></strong></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div class="d-flex justify-content-between mt-4">
                    <a href="../main/index.php" class="btn btn-primary">Continue Shopping</a>
                    <a href="../classes/checkout.php" class="btn btn-success">Proceed to Checkout</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php template('footer.php'); ?>