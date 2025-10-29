<?php
include 'db_connect.php'; // This also starts the session

header('Content-Type: application/json');

// Initialize cart if it doesn't exist
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$response = ['status' => 'error', 'message' => 'Invalid action.'];
$action = $_POST['action'] ?? null;
$product_id = (int) ($_POST['product_id'] ?? 0);

if ($product_id > 0) {
    switch ($action) {

        // --- ADD TO CART ---
        case 'add':
            $quantity = (int) ($_POST['quantity'] ?? 1);
            if (isset($_SESSION['cart'][$product_id])) {
                $_SESSION['cart'][$product_id] += $quantity;
            } else {
                $_SESSION['cart'][$product_id] = $quantity;
            }
            $response = ['status' => 'success', 'message' => 'Product added to cart.'];
            break;

        // --- UPDATE QUANTITY ---
        case 'update':
            $quantity = (int) ($_POST['quantity'] ?? 1);
            if ($quantity > 0) {
                $_SESSION['cart'][$product_id] = $quantity;
                $response = ['status' => 'success', 'message' => 'Cart updated.'];
            } else {
                // If quantity is 0 or less, remove it
                unset($_SESSION['cart'][$product_id]);
                $response = ['status' => 'success', 'message' => 'Item removed.'];
            }
            break;

        // --- REMOVE FROM CART ---
        case 'remove':
            unset($_SESSION['cart'][$product_id]);
            $response = ['status' => 'success', 'message' => 'Item removed from cart.'];
            break;
    }
}

// Calculate new total cart count for the header
$cart_count = 0;
if (!empty($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $quantity) {
        $cart_count += $quantity;
    }
}
$response['cart_count'] = $cart_count;

// Recalculate cart totals for cart page
$total_price = 0;
if (!empty($_SESSION['cart'])) {
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
        foreach ($_SESSION['cart'] as $pid => $quantity) {
            if (isset($products[$pid])) {
                $total_price += $products[$pid]['price'] * $quantity;
            }
        }
    } catch (PDOException $e) {
        // Handle DB error
    }
}
$response['total_price_formatted'] = '₹' . number_format($total_price);


echo json_encode($response);
?>