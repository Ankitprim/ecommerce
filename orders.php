<?php
include __DIR__ . '/partials/header.php';

// User must be logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$orders = [];

try {
    // Get all orders for the user
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->execute([$user_id]);
    $orders = $stmt->fetchAll();

    // For each order, get its items
    $order_items_stmt = $pdo->prepare("
        SELECT oi.*, p.name, p.image 
        FROM order_items oi
        JOIN products p ON oi.product_id = p.id
        WHERE oi.order_id = ?
    ");

    foreach ($orders as $key => $order) {
        $order_items_stmt->execute([$order['id']]);
        $orders[$key]['items'] = $order_items_stmt->fetchAll();
    }

} catch (PDOException $e) {
    echo "Error fetching orders: " . $e->getMessage();
}
?>

<h2 class="section-title">My Orders</h2>

<section class="orders-section" style="display: block;">

    <?php if (empty($orders)): ?>
        <p style="text-align: center; font-size: 18px;">You have not placed any orders yet.</p>
    <?php else: ?>
        <?php foreach ($orders as $order): ?>
            <div class="order-card">
                <div class="order-header">
                    <div>
                        <span class_name="order-id">Order ID: #<?php echo $order['id']; ?></span>
                        <span class="order-date">Date: <?php echo date('d M Y', strtotime($order['created_at'])); ?></span>
                    </div>
                    <div>
                        <span class="order-total">Total: ₹<?php echo number_format($order['total_amount']); ?></span>
                        <span class="order-status" style="background-color: <?php echo $order['status'] == 'Pending' ? '#f39c12' : '#27ae60'; ?>;">
                            <?php echo htmlspecialchars($order['status']); ?>
                        </span>
                    </div>
                </div>

                <div class="order-items">
                    <?php foreach ($order['items'] as $item): ?>
                        <div class="order-item">
                            <img src="<?php echo htmlspecialchars($item['image']); ?>" alt="" class="order-item-image">
                            <div class="order-item-details">
                                <div class="order-item-name"><?php echo htmlspecialchars($item['name']); ?></div>
                                <span>Qty: <?php echo $item['quantity']; ?></span>
                            </div>
                            <div class="order-item-price">₹<?php echo number_format($item['price'] * $item['quantity']); ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="order-shipping-details" style="background: #fdfdfd; padding: 10px; border-radius: 4px;">
                    <strong>Shipping to:</strong> <?php echo htmlspecialchars($order['name']); ?>, <?php echo htmlspecialchars($order['address']); ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

</section>

<?php include __DIR__ . '/partials/footer.php'; ?>