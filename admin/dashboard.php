<?php
session_start();
include 'conn.php';

/* =====================================================
   ADMIN LOGIN CHECK
===================================================== */
require_once(__DIR__ . "/auth_check.php");

/* =====================================================
   TOTAL COUNTS
===================================================== */
$user_result = mysqli_query($conn, "SELECT COUNT(*) AS total_users FROM reg");
$total_users = ($user_result) ? (mysqli_fetch_assoc($user_result)['total_users'] ?? 0) : 0;

$product_result = mysqli_query($conn, "SELECT COUNT(*) AS total_products FROM product");
$total_products = ($product_result) ? (mysqli_fetch_assoc($product_result)['total_products'] ?? 0) : 0;

$order_result = mysqli_query($conn, "SELECT COUNT(*) AS total_orders FROM `order` WHERE COALESCE(admin_deleted, 0) = 0");
$total_orders = ($order_result) ? (mysqli_fetch_assoc($order_result)['total_orders'] ?? 0) : 0;

$brand_result = mysqli_query($conn, "SELECT COUNT(*) AS total_brands FROM brand");
$total_brands = ($brand_result) ? (mysqli_fetch_assoc($brand_result)['total_brands'] ?? 0) : 0;

$wishlist_result = mysqli_query($conn, "SELECT COUNT(*) AS total_wishlist FROM wishlist");
$total_wishlist = ($wishlist_result) ? (mysqli_fetch_assoc($wishlist_result)['total_wishlist'] ?? 0) : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Time Craft</title>
    <link rel="icon" type="image/png" href="../assets/img/logo/icon.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="admin.css">
</head>
<body>

<?php $active_page = 'dashboard'; include 'admin_sidebar.php'; ?>

<div class="admin-main">
    <div class="admin-content">
        <h1><i class="fa-solid fa-gauge"></i> Welcome Admin</h1>
        <p>Hello, <b><?php echo isset($_SESSION['admin_name']) ? htmlspecialchars($_SESSION['admin_name']) : "Admin"; ?></b></p>

        <div class="card-box">
            <!-- USERS -->
            <a href="customer.php" class="card" style="text-decoration: none; color: inherit;">
                <i class="fa-solid fa-users"></i>
                <h2><?php echo $total_users; ?></h2>
                <h3>Total Users</h3>
            </a>

            <!-- PRODUCTS -->
            <a href="product.php" class="card" style="text-decoration: none; color: inherit;">
                <i class="fa-solid fa-box"></i>
                <h2><?php echo $total_products; ?></h2>
                <h3>Total Products</h3>
            </a>

            <!-- ORDERS -->
            <a href="order.php" class="card" style="text-decoration: none; color: inherit;">
                <i class="fa-solid fa-cart-shopping"></i>
                <h2><?php echo $total_orders; ?></h2>
                <h3>Total Orders</h3>
            </a>

            <!-- BRANDS -->
            <a href="brand.php" class="card" style="text-decoration: none; color: inherit;">
                <i class="fa-solid fa-tags"></i>
                <h2><?php echo $total_brands; ?></h2>
                <h3>Total Brands</h3>
            </a>

            <!-- WISHLIST -->
			 
            <div class="card">
                <i class="fa-solid fa-heart"></i>
                <h2><?php echo $total_wishlist; ?></h2>
                <h3>Total Wishlist</h3>
            </div>
        </div>
    </div>
</div>

</body>
</html>