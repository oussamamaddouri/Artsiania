<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
// Navigate from api/src/views/templates/ (4 levels up) to reach root config
require_once dirname(__DIR__, 4) . '/config/config.php';

$cart_item_count = 0;
if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $qty) { $cart_item_count += $qty; }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>
</head>
<body>

<header class="container">
    <nav class="navbar navbar-expand-lg navbar-light">
        <a class="navbar-brand" href="index.php">
            <img src="assets/images/logo.png" alt="Artisania" style="height: 130px;">
        </a>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mx-auto text-center">
                <li class="nav-item"><a class="nav-link px-3" href="products.php">Products</a></li>
                <li class="nav-item"><a class="nav-link px-3" href="#">About Us</a></li>
                <li class="nav-item"><a class="nav-link px-3" href="#">Contact</a></li>
            </ul>

            <ul class="navbar-nav ms-auto align-items-center">
                <li class="nav-item">
                    <a class="nav-link" href="cart.php">Cart (<?php echo $cart_item_count; ?>)</a>
                </li>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navUser" role="button" data-bs-toggle="dropdown">
                            Hi, <?php echo htmlspecialchars($_SESSION['username']); ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                             <?php if ($_SESSION['role'] === 'admin'): ?>
                                <li><a class="dropdown-item" href="admin/dashboard.php">Admin Panel</a></li>
                             <?php endif; ?>
                             <li><a class="dropdown-item text-danger" href="logout.php">Logout</a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
                    <li class="nav-item"><a class="nav-link contact-btn ms-2" href="register.php">REGISTER</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>
</header>
<main>
