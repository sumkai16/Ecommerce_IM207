<?php require_once '../templates/header.php'; ?>
<?php

require_once '../classes/Order.php';
require_once __DIR__ . '/../app/includes/database.php';
use Aries\MiniFrameworkStore\Includes\Database;
$dbInstance = new Database();
$db = $dbInstance->getConnection();

if (!isset($_GET['product_id']) || !isset($_GET['quantity'])) {
    header('Location: ../main/index.php');
    exit;
}

$product_id = $_GET['product_id'];
$quantity = (int)$_GET['quantity'];

if ($quantity < 1) {
    header('Location: ../main/index.php');
    exit;
}

// Get product details
$stmt = $db->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    header('Location: ../main/index.php');
    exit;
}

$total = $product['price'] * $quantity;

// Get user data if logged in
$user = null;
if (isset($_SESSION['user_id'])) {
    $stmt = $db->prepare("SELECT u.*, up.phone, up.address FROM users u 
        LEFT JOIN user_profiles up ON u.id = up.user_id WHERE u.id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<div class="container mt-4">
    <h2>Checkout</h2>
    
    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Shipping Information</h5>
                </div>
                <div class="card-body">
                    <form id="checkoutForm" action="process_direct_order.php" method="POST">
                        <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
                        <input type="hidden" name="quantity" value="<?php echo $quantity; ?>">
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="fullName" class="form-label">Full Name *</label>
                                <input type="text" class="form-control" id="fullName" name="fullName" 
                                       value="<?php echo $user ? htmlspecialchars($user['name']) : ''; ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label">Phone Number *</label>
                                <input type="tel" class="form-control" id="phone" name="phone" 
                                       value="<?php echo $user ? htmlspecialchars($user['phone']) : ''; ?>" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">Email (Optional)</label>
                            <input type="email" class="form-control" id="email" name="email" 
                                   value="<?php echo $user ? htmlspecialchars($user['email']) : ''; ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label for="address" class="form-label">Address *</label>
                            <textarea class="form-control" id="address" name="address" rows="3" required><?php 
                                echo $user ? htmlspecialchars($user['address']) : ''; 
                            ?></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="notes" class="form-label">Order Notes (Optional)</label>
                            <textarea class="form-control" id="notes" name="notes" rows="2" 
                                      placeholder="Special instructions for delivery..."></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="terms" required>
                                <label class="form-check-label" for="terms">
                                    I agree to the terms and conditions
                                </label>
                            </div>
                        </div>
                        
                        <input type="hidden" name="payment_method" value="COD">
                        <button type="submit" class="btn btn-primary">Place Order (COD)</button>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Order Summary</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span>
                            <?php echo htmlspecialchars($product['name']); ?> 
                            x <?php echo $quantity; ?>
                        </span>
                        <span>₱<?php echo number_format($product['price'] * $quantity, 2); ?></span>
                    </div>
                    
                    <hr>
                    
                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal:</span>
                        <span>₱<?php echo number_format($total, 2); ?></span>
                    </div>
                    
                    <div class="d-flex justify-content-between mb-2">
                        <span>Shipping:</span>
                        <span>Free</span>
                    </div>
                    
                    <hr>
                    
                    <div class="d-flex justify-content-between">
                        <strong>Total:</strong>
                        <strong>₱<?php echo number_format($total, 2); ?></strong>
                    </div>
                    
                    <div class="alert alert-info mt-3">
                        <small>
                            <i class="fas fa-info-circle"></i>
                            Please prepare the exact amount for cash on delivery.
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('checkoutForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    if (!document.getElementById('terms').checked) {
        alert('Please agree to the terms and conditions');
        return;
    }
    
    this.submit();
});
</script>

<?php require_once '../templates/footer.php'; ?> 