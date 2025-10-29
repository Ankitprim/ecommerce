<?php
include 'db_connect.php'; // This also starts the session

header('Content-Type: application/json');
$response = ['status' => 'error', 'message' => 'An unknown error occurred.'];

// Check if user is logged in and cart is not empty
if (!isset($_SESSION['user_id'])) {
    $response['message'] = 'You must be logged in to place an order.';
    echo json_encode($response);
    exit;
}

if (empty($_SESSION['cart'])) {
    $response['message'] = 'Your cart is empty.';
    echo json_encode($response);
    exit;
}

// Get form data
$user_id = $_SESSION['user_id'];
$name = $_POST['name'] ?? '';
$email = $_POST['email'] ?? '';
$phone = $_POST['phone'] ?? '';
$address = $_POST['address'] ?? '';
$payment_method = $_POST['payment_method'] ?? 'COD';

// Simple Validation
if (empty($name) || empty($email) || empty($phone) || empty($address)) {
    $response['message'] = 'Please fill in all required shipping fields.';
    echo json_encode($response);
    exit;
}

// Start a transaction
$pdo->beginTransaction();

try {
    // 1. Get product details and calculate total from DB (more secure)
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
    
    $total_amount = 0;
    foreach ($_SESSION['cart'] as $product_id => $quantity) {
        if (isset($products[$product_id])) {
            $total_amount += $products[$product_id]['price'] * $quantity;
        } else {
            throw new Exception("Product with ID $product_id not found.");
        }
    }

    // 2. Insert into 'orders' table
    $stmt = $pdo->prepare("
        INSERT INTO orders (user_id, name, email, phone, address, total_amount, payment_method, status)
        VALUES (?, ?, ?, ?, ?, ?, ?, 'Pending')
    ");
    $stmt->execute([$user_id, $name, $email, $phone, $address, $total_amount, $payment_method]);

    $order_id = $pdo->lastInsertId();

    // 3. Insert into 'order_items' table
    $stmt = $pdo->prepare("
        INSERT INTO order_items (order_id, product_id, quantity, price)
        VALUES (?, ?, ?, ?)
    ");
    foreach ($_SESSION['cart'] as $product_id => $quantity) {
        $price = $products[$product_id]['price'];
        $stmt->execute([$order_id, $product_id, $quantity, $price]);
    }

    // 4. Commit transaction
    $pdo->commit();

    // 5. Clear the cart
    $_SESSION['cart'] = [];

    $response['status'] = 'success';
    $response['message'] = 'Order placed successfully!';
    $response['order_id'] = $order_id;

} catch (Exception $e) {
    // 6. Rollback transaction on error
    $pdo->rollBack();
    $response['message'] = 'Error placing order: ' . $e->getMessage();
}

echo json_encode($response);
?>