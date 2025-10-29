<?php
include __DIR__ . '/partials/header.php';

// Handle Search
$search_query = "";
$sql = "SELECT * FROM products";
$params = [];

if (!empty($_GET['search'])) {
    $search_query = $_GET['search'];
    $sql .= " WHERE name LIKE ? OR description LIKE ?";
    $params[] = "%$search_query%";
    $params[] = "%$search_query%";
}
$cate_name = false;
if (isset($_GET['cat_id'])) {
    $cate_id = $_GET['cat_id'];
    $cate_name = $_GET['cat_name'];
    $sql .= " WHERE category_id  = ? ";
    $params[] = $cate_id;
}

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $products = $stmt->fetchAll();
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    $products = [];
}
?>

<section class="hero">
    <h1>Welcome to My Shop</h1>
    <p>Discover the best products at unbeatable prices. Fresh deals every week.</p>
    <a href="#products" class="cta-button">Shop Now</a>
</section>

<section id="products">
    <h2 class="section-title">
        <?php
        if ($search_query) {
            echo "Result for " . htmlspecialchars($search_query);
        } elseif ($cate_name) {
            echo "Featured " . ucfirst(htmlspecialchars($cate_name));
        } else {
            echo "Featured Products";
        }

        ?>
    </h2>

    <div class="products-grid">
        <?php if (empty($products)): ?>
            <p style="text-align: center; grid-column: 1 / -1;">No products found.</p>
        <?php else: ?>
            <?php foreach ($products as $product): ?>
                <div class="product-card">
                    <a href="product.php?id=<?php echo $product['id']; ?>" class="product-link">
                        <img src="<?php echo htmlspecialchars($product['image']); ?>"
                            alt="<?php echo htmlspecialchars($product['name']); ?>" class="product-image">
                        <div class="product-info">
                            <h3 class="product-name"><?php echo htmlspecialchars($product['name']); ?></h3>
                            <p class="product-description">
                                <?php echo htmlspecialchars(substr($product['description'], 0, 60)) . '...'; ?>
                            </p>
                            <div class="product-price">₹<?php echo number_format($product['price']); ?></div>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</section>

<?php include __DIR__ . '/partials/footer.php'; ?>