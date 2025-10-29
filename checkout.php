<?php
ob_start();
session_start();
include __DIR__ . '/partials/header.php';

// User must be logged in to check out
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php?redirect=checkout'); // Redirect to login
    exit;
}

// Cart must not be empty
if (empty($_SESSION['cart'])) {
    header('Location: cart.php'); // Redirect to cart
    exit;
}

// Fetch cart items and calculate total (same as cart.php)
$cart_items = [];
$subtotal = 0;
$shipping = 0.00; // You can add shipping logic here
$total_price = 0;

if (!empty($_SESSION['cart'])) {
    $product_ids = array_keys($_SESSION['cart']);
    $placeholders = rtrim(str_repeat('?,', count($product_ids)), ',');

    $stmt = $pdo->prepare("SELECT * FROM products WHERE id IN ($placeholders)");
    $stmt->execute($product_ids);
    $products_list = $stmt->fetchAll(PDO::FETCH_ASSOC); // <-- CHANGED FETCH MODE

    // Manually create the keyed array
    $products = [];
    foreach ($products_list as $product) {
        $products[$product['id']] = $product;
    }
    foreach ($_SESSION['cart'] as $product_id => $quantity) {
        if (isset($products[$product_id])) {
            $product = $products[$product_id];
            $cart_items[] = [
                'name' => $product['name'],
                'quantity' => $quantity,
                'price' => $product['price']
            ];
            $subtotal += $product['price'] * $quantity;
        }
    }
}
$total_price = $subtotal + $shipping;

// Get user info to pre-fill form
$stmt = $pdo->prepare("SELECT name, email FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

ob_end_flush();

?>

<h2 class="section-title">Checkout</h2>

<section class="checkout-section" style="display: block;">
    <div class="checkout-container">

        <div class="checkout-form">
            <h3>Shipping Details</h3>
            <form id="checkout-form">
                <div class="form-group">
                    <label for="name">Full Name</label>
                    <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user['name']); ?>"
                        required>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>"
                        required>
                </div>
                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="tel" id="phone" name="phone" required>
                </div>
                <div class="form-group">
                    <label for="address">Full Address (Street, City, State, ZIP)</label>
                    <input type="text" id="address" name="address" required>
                </div>

                <h3>Payment Method</h3>
                <div class="payment-method">
                    <input type="radio" id="cod" name="payment_method" value="COD" checked style="margin-right: 10px;">
                    <label for="cod" style="font-weight: 600; font-size: 18px;">
                        <i class="fas fa-wallet"></i> Cash on Delivery
                    </label>
                </div>

                <button type="submit" class="submit-order">Place Order</button>
                <div id="checkout-error" style="color: red; margin-top: 15px; display: none;"></div>
            </form>
        </div>

        <div class="order-summary">
            <h3>Order Summary</h3>
            <?php foreach ($cart_items as $item): ?>
                <div class="summary-item">
                    <span><?php echo htmlspecialchars($item['name']); ?> (x<?php echo $item['quantity']; ?>)</span>
                    <span>₹<?php echo number_format($item['price'] * $item['quantity']); ?></span>
                </div>
            <?php endforeach; ?>

            <div class="summary-item">
                <span>Subtotal</span>
                <span>₹<?php echo number_format($subtotal); ?></span>
            </div>
            <div class="summary-item">
                <span>Shipping</span>
                <span><?php echo $shipping > 0 ? '₹' . number_format($shipping) : 'FREE'; ?></span>
            </div>
            <div class="summary-total">
                <span>Total</span>
                <span>₹<?php echo number_format($total_price); ?></span>
            </div>
        </div>

    </div>
</section>

<?php include __DIR__ . '/partials/footer.php'; ?>