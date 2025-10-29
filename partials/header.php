<?php
// We include db_connect.php which also starts the session
include_once __DIR__ . '/../php/db_connect.php';

// Fetch categories for the dropdown
try {
    $stmt = $pdo->query("SELECT * FROM categories");
    $categories = $stmt->fetchAll();
} catch (PDOException $e) {
    $categories = []; // Handle error gracefully
}

// Get cart count from session
$cart_count = 0;
if (!empty($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $quantity) {
        $cart_count += $quantity;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-commerce - Luxury Gems & Collectibles</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
</head>

<body>

    <header>
        <div class="header-container">
            <a href="index.php" class="logo">
                <i class="fas fa-gem"></i>
                <span>E-commerce</span>
            </a>

            <nav>
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li class="category-dropdown">
                        <a href="#">Categories <i class="fas fa-chevron-down fa-xs"></i></a>
                        <div class="category-dropdown-content">
                            <?php foreach ($categories as $category): ?>
                                <a
                                    href="index.php?cat_id=<?php echo $category['id']; ?>&cat_name=<?php echo htmlspecialchars($category['name']); ?>"><?php echo ucfirst(htmlspecialchars($category['name'])); ?></a>
                            <?php endforeach; ?>
                        </div>
                    </li>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li><a href="orders.php">My Orders</a></li>
                    <?php endif; ?>
                    <li>
                        <form class="search-bar" action="index.php" method="GET">
                            <input type="text" name="search" placeholder="Search products..."
                                value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
                            <button type="submit"> <abbr title="search"> <i class="fas fa-search"></i></abbr></button>
                        </form>
                    </li>
                    <li>
                        <a href="cart.php" class="cart-icon">
                            <abbr title="cart"><i class="fas fa-shopping-cart"></i></abbr>
                            <span class="cart-count"><?php echo $cart_count; ?></span>
                        </a>
                    </li>
                    <li>
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <a href="logout.php" style=" font-size: 22px"><abbr title="logout"> <i
                                        class="fa-solid fa-right-from-bracket"></i> </abbr></a>
                        <?php else: ?>
                            <a href="login.php" style=" font-size: 22px;"> <abbr title="login"> <i
                                        class="fa-solid fa-right-to-bracket"></i> </abbr></a>
                        <?php endif; ?>
                    </li>

                </ul>
            </nav>
            <div class="header-actions">
                <button class="mobile-menu-btn"><i class="fas fa-bars"></i></button>
            </div>
        </div>
    </header>

    <div class="mobile-nav">
        <button class="mobile-nav-close"><i class="fas fa-times"></i></button>

        <ul>
            <li>
                <form class="search-bar" action="index.php" method="GET">
                    <input type="text" name="search" placeholder="Search products..."
                        value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
                    <button type="submit"> <abbr title="search"> <i class="fas fa-search"></i></abbr></button>
                </form>
            </li>
            <li><a href="index.php">Home</a></li>
            <li class="category-dropdown">
                <a href="#">Categories <i class="fas fa-chevron-down fa-xs"></i></a>
                <div class="category-dropdown-content">
                    <?php foreach ($categories as $category): ?>
                        <a
                            href="index.php?cat_id=<?php echo $category['id']; ?>&cat_name=<?php echo htmlspecialchars($category['name']); ?>"><?php echo ucfirst(htmlspecialchars($category['name'])); ?></a>
                    <?php endforeach; ?>
                </div>
            </li>
            <?php if (isset($_SESSION['user_id'])): ?>
                <li><a href="orders.php">My Orders</a></li>
            <?php endif; ?>
            
            <li>
                <a href="cart.php" class="cart-icon">
                    Cart
                    <span class="cart-count"><?php echo $cart_count; ?></span>
                </a>
            </li>
            <li>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="logout.php" style=" font-size: 22px">Logout</a>
                <?php else: ?>
                    <a href="login.php" style=" font-size: 22px;">Login</a>
                <?php endif; ?>
            </li>

        </ul>
    </div>

    <main>