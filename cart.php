<?php
include __DIR__ . '/partials/header.php';

$cart_items = [];
$total_price = 0;

if (!empty($_SESSION['cart'])) {
    // Get product IDs from cart
    $product_ids = array_keys($_SESSION['cart']);
    $placeholders = rtrim(str_repeat('?,', count($product_ids)), ',');

    try {
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
                    'id' => $product_id,
                    'name' => $product['name'],
                    'price' => $product['price'],
                    'image' => $product['image'],
                    'quantity' => $quantity,
                    'subtotal' => $product['price'] * $quantity
                ];
                $total_price += $product['price'] * $quantity;
            }
        }
    } catch (PDOException $e) {
        echo "Error fetching cart products: " . $e->getMessage();
    }
}
?>

<h2 class="section-title">Your Shopping Cart</h2>

<section class="cart-section" style="display: block;"> <?php if (empty($cart_items)): ?>
        <p style="text-align: center; font-size: 18px;">Your cart is empty.</p>
        <div style="text-align: center; margin-top: 20px;">
            <a href="index.php" class="cta-button">Continue Shopping</a>
        </div>

    <?php else: ?>

        <div id="cart-items-container">
            <?php foreach ($cart_items as $item): ?>
                <div class="cart-item" data-product-id="<?php echo $item['id']; ?>">
                    <img src="<?php echo htmlspecialchars($item['image']); ?>" alt="" class="cart-item-image">
                    <div class="cart-item-details">
                        <div class="cart-item-name"><?php echo htmlspecialchars($item['name']); ?></div>
                        <div class="cart-item-price">₹<?php echo number_format($item['price']); ?></div>
                    </div>
                    <div class="cart-item-quantity">
                        <button class="quantity-btn update-quantity" data-change="-1"
                            data-product-id="<?php echo $item['id']; ?>">-</button>
                        <span class="quantity"><?php echo $item['quantity']; ?></span>
                        <button class="quantity-btn update-quantity" data-change="1"
                            data-product-id="<?php echo $item['id']; ?>">+</button>
                    </div>
                    <div class="cart-item-subtotal" style="font-weight: 700; width: 100px; text-align: right;">
                        ₹<?php echo number_format($item['subtotal']); ?>
                    </div>
                    <button class="cart-item-remove" data-product-id="<?php echo $item['id']; ?>">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="cart-total">
            Total: <span id="cart-total-price">₹<?php echo number_format($total_price); ?></span>
        </div>

        <div class="cart-actions">
            <a href="index.php" class="continue-shopping" style="padding:10px 20px;">Continue Shopping</a>
            <a href="checkout.php" class="checkout-btn" style="padding:10px 20px;">Proceed to Checkout</a>
        </div>

    <?php endif; ?>
</section>

<?php include __DIR__ . '/partials/footer.php'; ?>