<?php
include __DIR__ . '/partials/header.php';

// Check if ID is set
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<p class='container'>Product not found.</p>";
    include __DIR__ . '/partials/footer.php';
    exit;
}

$product_id = (int)$_GET['id'];

try {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch();

    if (!$product) {
        echo "<p class='container'>Product not found.</p>";
        include __DIR__ . '/partials/footer.php';
        exit;
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    include __DIR__ . '/partials/footer.php';
    exit;
}
?>

<style>
    .product-page-container {
        display: flex;
        flex-wrap: wrap;
        gap: 30px;
        margin-top: 30px;
    }
    .product-page-image {
        flex: 1;
        min-width: 300px;
    }
    .product-page-image img {
        width: 100%;
        max-width: 500px;
        border-radius: 8px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    .product-page-details {
        flex: 1.5;
        min-width: 300px;
    }
    .product-page-details .product-name {
        font-size: 32px;
        color: #2c3e50;
        margin-bottom: 15px;
    }
    .product-page-details .product-price {
        font-size: 28px;
        font-weight: 700;
        color: #e74c3c;
        margin-bottom: 20px;
    }
    .product-page-details .product-description {
        font-size: 16px;
        line-height: 1.7;
        color: #333;
        margin-bottom: 30px;
    }
    .product-page-actions {
        display: flex;
        gap: 15px;
    }
    .product-page-actions .add-to-cart,
    .product-page-actions .buy-now {
        padding: 15px 30px;
        font-size: 18px;
        flex: 1;
    }
</style>

<div class="product-page-container">
    <div class="product-page-image">
        <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
    </div>
    <div class="product-page-details">
        <h1 class="product-name"><?php echo htmlspecialchars($product['name']); ?></h1>
        <div class="product-price">₹<?php echo number_format($product['price']); ?></div>
        <p class="product-description">
            <?php echo nl2br(htmlspecialchars($product['description'])); // nl2br to respect line breaks ?>
        </p>

        <div class="product-page-actions">
            <button class="add-to-cart" data-product-id="<?php echo $product['id']; ?>">
                <i class="fas fa-cart-plus"></i> Add to Cart
            </button>
            <?php
            if($product['stock']>0):
            ?>
            <button class="buy-now" data-product-id="<?php echo $product['id']; ?>">
                Buy Now
            </button>
            <?php
            else:?>
            <button class="buy-now">Out of Stock</button>
            <?php
            endif;    
             ?>
            <button id="back-to-shop-btn"> Back to shop</button>
        </div>
        <div id="add-to-cart-message" style="margin-top: 15px; color: green; font-weight: bold; display: none;">
            Product added to cart!
        </div>
    </div>
</div>


<?php include __DIR__ . '/partials/footer.php'; ?>