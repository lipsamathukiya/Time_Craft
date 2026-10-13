<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include("conn.php");

date_default_timezone_set("Asia/Kolkata");

/* =====================================================
   USER AUTHENTICATION (SUPPORT user_id AND uid)
===================================================== */
$user_id = 0;
if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
    $user_id = intval($_SESSION['user_id']);
} elseif (isset($_SESSION['uid']) && !empty($_SESSION['uid'])) {
    $user_id = intval($_SESSION['uid']);
}

if ($user_id <= 0) {
    $_SESSION['login_redirect'] = 'cart.php';
    header("Location: login.php");
    exit();
}

/* =====================================================
   CART ACTION HANDLERS (UPDATE QTY / CLEAR / REMOVE)
===================================================== */

// 1. UPDATE QUANTITY VIA GET / POST
if (isset($_GET['action']) && $_GET['action'] === 'update_qty') {
    $cart_id = intval($_GET['id'] ?? 0);
    $change = $_GET['change'] ?? '';
    $set_qty = isset($_GET['set_qty']) ? intval($_GET['set_qty']) : null;

    if ($cart_id > 0) {
        $item_q = mysqli_query($conn, "
            SELECT cart.id, cart.qty, cart.product_id, product.stock, product.product_name 
            FROM cart 
            INNER JOIN product ON cart.product_id = product.id 
            WHERE cart.id = '$cart_id' AND cart.user_id = '$user_id'
            LIMIT 1
        ");

        if ($item_q && mysqli_num_rows($item_q) > 0) {
            $item = mysqli_fetch_assoc($item_q);
            $current_qty = intval($item['qty']);
            $max_stock = intval($item['stock']);
            $product_name = $item['product_name'];

            if ($set_qty !== null) {
                $new_qty = $set_qty;
            } elseif ($change === 'plus') {
                $new_qty = $current_qty + 1;
            } elseif ($change === 'minus') {
                $new_qty = $current_qty - 1;
            } else {
                $new_qty = $current_qty;
            }

            if ($new_qty <= 0) {
                mysqli_query($conn, "DELETE FROM cart WHERE id = '$cart_id' AND user_id = '$user_id'");
                $_SESSION['cart_msg'] = "'{$product_name}' removed from your cart.";
                $_SESSION['cart_msg_type'] = "info";
            } elseif ($new_qty > $max_stock) {
                mysqli_query($conn, "UPDATE cart SET qty = '$max_stock' WHERE id = '$cart_id'");
                $_SESSION['cart_msg'] = "Maximum available stock for '{$product_name}' is {$max_stock}.";
                $_SESSION['cart_msg_type'] = "warning";
            } else {
                mysqli_query($conn, "UPDATE cart SET qty = '$new_qty' WHERE id = '$cart_id'");
                $_SESSION['cart_msg'] = "Updated quantity for '{$product_name}'.";
                $_SESSION['cart_msg_type'] = "success";
            }
        }
    }

    header("Location: cart.php");
    exit();
}

// 2. CLEAR ENTIRE CART
if (isset($_GET['action']) && $_GET['action'] === 'clear_all') {
    mysqli_query($conn, "DELETE FROM cart WHERE user_id = '$user_id'");
    $_SESSION['cart_msg'] = "All timepieces removed from your cart.";
    $_SESSION['cart_msg_type'] = "info";
    header("Location: cart.php");
    exit();
}

// 3. REMOVE SINGLE ITEM
if (isset($_GET['action']) && $_GET['action'] === 'remove') {
    $cart_id = intval($_GET['id'] ?? 0);
    if ($cart_id > 0) {
        mysqli_query($conn, "DELETE FROM cart WHERE id = '$cart_id' AND user_id = '$user_id'");
        $_SESSION['cart_msg'] = "Watch removed from your cart.";
        $_SESSION['cart_msg_type'] = "info";
    }
    header("Location: cart.php");
    exit();
}

// Flash Message Extraction
$cart_msg = $_SESSION['cart_msg'] ?? '';
$cart_msg_type = $_SESSION['cart_msg_type'] ?? '';
unset($_SESSION['cart_msg'], $_SESSION['cart_msg_type']);

/* =====================================================
   FETCH CART DATA WITH STOCK & BRAND
===================================================== */
$tc_cart_stmt = mysqli_prepare($conn, "
    SELECT 
        cart.id AS cart_id,
        cart.qty,
        cart.product_id,
        product.product_name,
        product.brand,
        product.price,
        product.image,
        product.stock,
        product.collection
    FROM cart
    INNER JOIN product ON cart.product_id = product.id
    WHERE cart.user_id = ?
    ORDER BY cart.id DESC
");

$tc_cart_items = [];
$tc_grand_total = 0;
$tc_total_qty = 0;

if ($tc_cart_stmt) {
    mysqli_stmt_bind_param($tc_cart_stmt, "i", $user_id);
    mysqli_stmt_execute($tc_cart_stmt);
    $tc_cart_res = mysqli_stmt_get_result($tc_cart_stmt);
    if ($tc_cart_res) {
        while ($item = mysqli_fetch_assoc($tc_cart_res)) {
            $tc_cart_items[] = $item;
            $tc_grand_total += (float)$item['price'] * (int)$item['qty'];
            $tc_total_qty += (int)$item['qty'];
        }
    }
    mysqli_stmt_close($tc_cart_stmt);
}

/* =====================================================
   CONTINUE SHOPPING URL
===================================================== */
$continue_url = "brand.php";
if (isset($_SESSION['last_shop_page']) && !empty($_SESSION['last_shop_page'])) {
    $continue_url = $_SESSION['last_shop_page'];
} elseif (isset($_SESSION['last_brand_page']) && !empty($_SESSION['last_brand_page'])) {
    $continue_url = $_SESSION['last_brand_page'];
}

include("header.php");
?>

<!-- =====================================================
     MY CART CSS (TIME CRAFT LUXURY THEME)
===================================================== -->
<style>
:root {
    --tc-gold: #d4af37;
    --tc-gold-light: #f3e5ab;
    --tc-gold-dark: #b89127;
    --tc-gold-hover: #ffc107;
    --tc-dark-bg: #0a0a0a;
    --tc-card-bg: #141414;
    --tc-card-inner: #1c1c1c;
    --tc-border-gold: rgba(212, 175, 55, 0.22);
    --tc-border-gold-hover: rgba(212, 175, 55, 0.6);
    --tc-border-subtle: rgba(255, 255, 255, 0.08);
    --tc-text-primary: #ffffff;
    --tc-text-secondary: #a8a8a8;
    --tc-danger: #ff4d4f;
    --tc-success: #2ecc71;
    --tc-warning: #f39c12;
}

.tc-cart-section {
    background: radial-gradient(circle at 50% 0%, #17150f 0%, #0a0a0a 70%);
    min-height: 85vh;
    padding: 50px 0 90px 0;
    color: var(--tc-text-primary);
    overflow-x: hidden;
    width: 100%;
}

/* HERO */
.tc-cart-hero {
    text-align: center;
    margin-bottom: 35px;
}

.tc-cart-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: rgba(212, 175, 55, 0.12);
    border: 1px solid var(--tc-border-gold);
    color: var(--tc-gold);
    font-size: 13px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1.5px;
    padding: 6px 20px;
    border-radius: 30px;
    margin-bottom: 14px;
}

.tc-cart-title {
    font-size: 38px;
    font-weight: 800;
    color: #ffffff;
    letter-spacing: 0.5px;
    margin-bottom: 10px;
}

.tc-cart-title span {
    color: var(--tc-gold);
}

.tc-cart-desc {
    color: var(--tc-text-secondary);
    font-size: 16px;
    max-width: 580px;
    margin: 0 auto;
}

/* FLASH ALERTS */
.tc-cart-alert {
    max-width: 1100px;
    margin: 0 auto 25px auto;
    padding: 14px 20px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    font-size: 14px;
    font-weight: 600;
    animation: tcSlideDown 0.3s ease;
}

.tc-cart-alert.success {
    background: rgba(46, 204, 113, 0.15);
    border: 1px solid rgba(46, 204, 113, 0.4);
    color: #2ecc71;
}

.tc-cart-alert.warning {
    background: rgba(243, 156, 18, 0.15);
    border: 1px solid rgba(243, 156, 18, 0.4);
    color: #f39c12;
}

.tc-cart-alert.error {
    background: rgba(255, 77, 79, 0.15);
    border: 1px solid rgba(255, 77, 79, 0.4);
    color: #ff4d4f;
}

.tc-cart-alert.info {
    background: rgba(52, 152, 219, 0.15);
    border: 1px solid rgba(52, 152, 219, 0.4);
    color: #3498db;
}

@keyframes tcSlideDown {
    from { opacity: 0; transform: translateY(-8px); }
    to { opacity: 1; transform: translateY(0); }
}

/* 2-COLUMN CART GRID */
.tc-cart-grid {
    display: grid;
    grid-template-columns: 1.45fr 1fr;
    gap: 32px;
    align-items: start;
}

/* BOX CARD */
.tc-box {
    background: var(--tc-card-bg);
    border: 1px solid var(--tc-border-gold);
    border-radius: 18px;
    padding: 28px 24px;
    box-shadow: 0 10px 35px rgba(0, 0, 0, 0.5);
    position: relative;
}

.tc-box-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid var(--tc-border-subtle);
    padding-bottom: 18px;
    margin-bottom: 22px;
}

.tc-box-title {
    font-size: 20px;
    font-weight: 800;
    color: #ffffff;
    display: flex;
    align-items: center;
    gap: 10px;
    margin: 0;
}

.tc-box-title i {
    color: var(--tc-gold);
}

.tc-header-actions-group {
    display: flex;
    align-items: center;
    gap: 12px;
}

.tc-item-count-badge {
    background: rgba(212, 175, 55, 0.12);
    border: 1px solid var(--tc-border-gold);
    color: var(--tc-gold);
    font-size: 12px;
    font-weight: 700;
    padding: 4px 14px;
    border-radius: 20px;
}

.tc-btn-clear-cart {
    background: none;
    border: 1px solid rgba(255, 77, 79, 0.3);
    color: #ff6b6b;
    font-size: 12px;
    font-weight: 600;
    padding: 4px 12px;
    border-radius: 6px;
    text-decoration: none !important;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    transition: 0.2s;
}

.tc-btn-clear-cart:hover {
    background: rgba(255, 77, 79, 0.15);
    border-color: #ff4d4f;
    color: #ff4d4f;
}

/* CART ITEMS LIST */
.tc-cart-items-list {
    display: flex;
    flex-direction: column;
    gap: 16px;
}

.tc-cart-item-card {
    background: var(--tc-card-inner);
    border: 1px solid var(--tc-border-subtle);
    border-radius: 14px;
    padding: 18px 20px;
    display: grid;
    grid-template-columns: 85px 1fr auto;
    gap: 18px;
    align-items: center;
    transition: all 0.25s ease;
}

.tc-cart-item-card:hover {
    border-color: var(--tc-border-gold-hover);
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.4);
}

/* THUMBNAIL */
.tc-cart-img-box {
    width: 85px;
    height: 85px;
    background: #111;
    border: 1px solid var(--tc-border-gold);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    flex-shrink: 0;
}

.tc-cart-img {
    width: 85%;
    height: 85%;
    object-fit: contain;
    transition: transform 0.3s ease;
}

.tc-cart-item-card:hover .tc-cart-img {
    transform: scale(1.08);
}

/* INFO */
.tc-cart-info {
    min-width: 0;
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.tc-cart-brand {
    font-size: 11px;
    font-weight: 700;
    color: var(--tc-gold);
    text-transform: uppercase;
    letter-spacing: 1.2px;
}

.tc-cart-name {
    font-size: 16px;
    font-weight: 700;
    color: #ffffff;
    margin: 0;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.tc-cart-name a {
    color: #ffffff;
    text-decoration: none;
    transition: color 0.2s;
}

.tc-cart-name a:hover {
    color: var(--tc-gold);
}

.tc-cart-meta {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 13px;
    color: var(--tc-text-secondary);
    margin-top: 2px;
    flex-wrap: wrap;
}

.tc-stock-tag {
    font-size: 11px;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}

.tc-stock-tag.in-stock { color: #2ecc71; }
.tc-stock-tag.low-stock { color: #f39c12; }
.tc-stock-tag.out-stock { color: #ff4d4f; }

/* QUANTITY CONTROLS */
.tc-qty-controls {
    display: inline-flex;
    align-items: center;
    background: #111;
    border: 1px solid var(--tc-border-gold);
    border-radius: 8px;
    padding: 2px;
    margin-top: 6px;
}

.tc-qty-btn {
    width: 28px;
    height: 28px;
    background: transparent;
    border: none;
    color: var(--tc-gold);
    font-size: 13px;
    font-weight: bold;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    border-radius: 6px;
    text-decoration: none !important;
    transition: all 0.2s;
}

.tc-qty-btn:hover:not(.disabled) {
    background: var(--tc-gold);
    color: #000;
}

.tc-qty-btn.disabled {
    opacity: 0.3;
    cursor: not-allowed;
    pointer-events: none;
}

.tc-qty-value {
    min-width: 32px;
    text-align: center;
    font-size: 13px;
    font-weight: 800;
    color: #ffffff;
}

/* TOTAL & REMOVE BOX */
.tc-cart-right-box {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 10px;
}

.tc-cart-item-total {
    font-size: 18px;
    font-weight: 800;
    color: var(--tc-gold);
    white-space: nowrap;
}

.tc-btn-remove-cart {
    background: none;
    border: none;
    color: #888;
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    text-decoration: none !important;
    transition: 0.2s;
    padding: 4px 6px;
    border-radius: 4px;
}

.tc-btn-remove-cart:hover {
    color: var(--tc-danger);
    background: rgba(255, 77, 79, 0.1);
}

/* BREAKDOWN & SUMMARY */
.tc-breakdown {
    background: var(--tc-card-inner);
    border: 1px solid var(--tc-border-subtle);
    border-radius: 14px;
    padding: 20px;
    margin-bottom: 22px;
}

.tc-breakdown-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 8px 0;
    font-size: 14px;
    color: var(--tc-text-secondary);
}

.tc-breakdown-row .val {
    color: #ffffff;
    font-weight: 700;
}

.tc-breakdown-row.free-badge {
    color: #2ecc71;
    font-weight: 700;
}

.tc-breakdown-row.free-badge .val {
    color: #2ecc71;
}

.tc-breakdown-divider {
    height: 1px;
    background: var(--tc-border-subtle);
    margin: 12px 0;
}

.tc-breakdown-total {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 6px 0 2px 0;
    font-size: 17px;
    font-weight: 800;
    color: #ffffff;
}

.tc-breakdown-total .total-val {
    font-size: 24px;
    font-weight: 900;
    color: var(--tc-gold);
}

/* CHECKOUT BUTTON */
.tc-btn-checkout {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    width: 100%;
    background: linear-gradient(135deg, #d4af37 0%, #aa8214 100%);
    color: #000000 !important;
    font-size: 16px;
    font-weight: 800;
    padding: 16px 28px;
    border-radius: 10px;
    text-decoration: none !important;
    transition: all 0.3s ease;
    box-shadow: 0 6px 22px rgba(212, 175, 55, 0.35);
    border: none;
    cursor: pointer;
}

.tc-btn-checkout:hover {
    background: linear-gradient(135deg, #ffc107 0%, #d4af37 100%);
    transform: translateY(-2px);
    box-shadow: 0 10px 28px rgba(212, 175, 55, 0.5);
}

.tc-btn-continue-shop {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    width: 100%;
    background: transparent;
    color: #ffffff !important;
    font-size: 14px;
    font-weight: 700;
    padding: 12px 20px;
    border-radius: 10px;
    border: 1px solid rgba(255, 255, 255, 0.15);
    text-decoration: none !important;
    margin-top: 12px;
    transition: all 0.3s ease;
}

.tc-btn-continue-shop:hover {
    border-color: var(--tc-gold);
    color: var(--tc-gold) !important;
    background: rgba(212, 175, 55, 0.08);
}

/* TRUST BADGES */
.tc-trust-badges {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 10px;
    margin-top: 24px;
    padding-top: 20px;
    border-top: 1px solid var(--tc-border-subtle);
}

.tc-trust-item {
    text-align: center;
    font-size: 11px;
    color: var(--tc-text-secondary);
}

.tc-trust-item i {
    color: var(--tc-gold);
    font-size: 18px;
    margin-bottom: 5px;
    display: block;
}

/* EMPTY CART BOX */
.tc-empty-cart-card {
    max-width: 620px;
    margin: 40px auto;
    background: #141414;
    border: 1px solid var(--tc-border-gold);
    border-radius: 20px;
    padding: 60px 30px;
    text-align: center;
    box-shadow: 0 12px 35px rgba(0, 0, 0, 0.5);
}

.tc-empty-cart-icon {
    width: 95px;
    height: 95px;
    margin: 0 auto 24px;
    border-radius: 50%;
    background: rgba(212, 175, 55, 0.08);
    border: 2px dashed var(--tc-border-gold);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--tc-gold);
    font-size: 42px;
    animation: tcPulse 2.5s infinite ease-in-out;
}

@keyframes tcPulse {
    0%, 100% {
        transform: scale(1);
        box-shadow: 0 0 0 0 rgba(212, 175, 55, 0.25);
    }
    50% {
        transform: scale(1.06);
        box-shadow: 0 0 24px 6px rgba(212, 175, 55, 0.2);
    }
}

.tc-empty-cart-card h3 {
    font-size: 28px;
    font-weight: 800;
    color: #ffffff;
    margin-bottom: 12px;
}

.tc-empty-cart-card p {
    color: var(--tc-text-secondary);
    font-size: 15px;
    line-height: 1.6;
    margin-bottom: 30px;
    max-width: 480px;
    margin-left: auto;
    margin-right: auto;
}

.tc-empty-actions-group {
    display: flex;
    justify-content: center;
    gap: 12px;
    flex-wrap: wrap;
}

.tc-btn-explore-watches {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    background: linear-gradient(135deg, #d4af37 0%, #aa8214 100%);
    color: #000000 !important;
    font-weight: 800;
    font-size: 14px;
    padding: 13px 28px;
    border-radius: 8px;
    text-decoration: none !important;
    transition: all 0.3s ease;
    box-shadow: 0 5px 18px rgba(212, 175, 55, 0.35);
}

.tc-btn-explore-watches:hover {
    background: linear-gradient(135deg, #ffc107 0%, #d4af37 100%);
    transform: translateY(-3px);
}

.tc-btn-explore-outline {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: #1c1c1c;
    border: 1px solid var(--tc-border-gold);
    color: var(--tc-gold) !important;
    font-weight: 700;
    font-size: 13px;
    padding: 12px 20px;
    border-radius: 8px;
    text-decoration: none !important;
    transition: 0.2s;
}

.tc-btn-explore-outline:hover {
    background: rgba(212, 175, 55, 0.15);
    color: #fff !important;
}

/* RESPONSIVE */
@media (max-width: 992px) {
    .tc-cart-grid {
        grid-template-columns: 1fr;
        gap: 30px;
    }
    .tc-cart-title {
        font-size: 30px;
    }
}

@media (max-width: 576px) {
    .tc-cart-section {
        padding: 30px 0 60px 0;
    }
    .tc-cart-title {
        font-size: 22px;
    }
    .tc-box {
        padding: 16px 12px;
    }
    .tc-cart-item-card {
        grid-template-columns: 70px 1fr;
        gap: 12px;
    }
    .tc-cart-right-box {
        grid-column: span 2;
        flex-direction: row;
        justify-content: space-between;
        align-items: center;
        padding-top: 10px;
        border-top: 1px solid var(--tc-border-subtle);
        width: 100%;
    }
    .tc-trust-badges {
        grid-template-columns: 1fr;
        gap: 12px;
    }
    .tc-empty-actions-group {
        flex-direction: column;
        align-items: stretch;
    }
    .tc-btn-explore-watches,
    .tc-btn-explore-outline {
        justify-content: center;
        width: 100%;
    }
    .tc-cart-name {
        font-size: 14px;
    }
    .tc-empty-cart-card {
        padding: 40px 20px;
    }
    .tc-empty-cart-card h3 {
        font-size: 22px;
    }
    .tc-box-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 10px;
    }
}
</style>

<!-- =====================================================
     MY CART HTML
===================================================== -->
<section class="tc-cart-section">
    <div class="container">

        <!-- ================= HERO HEADER ================= -->
        <div class="tc-cart-hero">
            <div class="tc-cart-badge">
                <i class="fa-solid fa-cart-shopping"></i> My Shopping Cart
            </div>
            <h1 class="tc-cart-title">
                My <span>Cart</span>
            </h1>
            <p class="tc-cart-desc">
                Review your selected handcrafted timepieces before proceeding to secure checkout.
            </p>
        </div>

        <!-- ================= FLASH MESSAGE ================= -->
        <?php if (!empty($cart_msg)) { ?>
            <div class="tc-cart-alert <?php echo htmlspecialchars($cart_msg_type); ?>">
                <span>
                    <i class="fa-solid <?php echo $cart_msg_type === 'success' ? 'fa-circle-check' : ($cart_msg_type === 'warning' ? 'fa-triangle-exclamation' : 'fa-circle-info'); ?>" style="margin-right: 8px;"></i>
                    <?php echo htmlspecialchars($cart_msg); ?>
                </span>
                <button type="button" onclick="this.parentElement.style.display='none';" style="background:none; border:none; color:inherit; cursor:pointer; font-size:16px;">&times;</button>
            </div>
        <?php } ?>

        <?php if (!empty($tc_cart_items)) { ?>

            <div class="tc-cart-grid">

                <!-- ================= LEFT COLUMN: CART ITEMS ================= -->
                <div class="tc-box">
                    <div class="tc-box-header">
                        <h2 class="tc-box-title">
                            <i class="fa-solid fa-list-check"></i> Cart Items
                        </h2>
                        <div class="tc-header-actions-group">
                            <span class="tc-item-count-badge">
                                <?php echo $tc_total_qty; ?> <?php echo $tc_total_qty === 1 ? 'Watch' : 'Watches'; ?>
                            </span>
                            <a 
                                href="cart.php?action=clear_all" 
                                class="tc-btn-clear-cart"
                                onclick="return confirm('Are you sure you want to empty your entire cart?');"
                                title="Remove all items"
                            >
                                <i class="fa-solid fa-trash-can"></i> Clear All
                            </a>
                        </div>
                    </div>

                    <div class="tc-cart-items-list">
                        <?php foreach ($tc_cart_items as $c_item) { 
                            $c_id = intval($c_item['cart_id']);
                            $p_id = intval($c_item['product_id']);
                            $c_name = $c_item['product_name'];
                            $c_brand = $c_item['brand'] ?? 'TimeCraft';
                            $c_price = (float)$c_item['price'];
                            $c_qty = (int)$c_item['qty'];
                            $c_stock = intval($c_item['stock'] ?? 10);
                            $c_total = $c_price * $c_qty;
                            $c_img = $c_item['image'];
                        ?>
                            <div class="tc-cart-item-card">
                                <!-- WATCH IMAGE -->
                                <div class="tc-cart-img-box">
                                    <a href="product_details.php?id=<?php echo $p_id; ?>">
                                        <img 
                                            src="upload/<?php echo htmlspecialchars($c_img); ?>" 
                                            alt="<?php echo htmlspecialchars($c_name); ?>"
                                            class="tc-cart-img"
                                            onerror="this.onerror=null; this.src='assets/img/logo/logo.png';"
                                        >
                                    </a>
                                </div>

                                <!-- WATCH DETAILS -->
                                <div class="tc-cart-info">
                                    <span class="tc-cart-brand">
                                        <?php echo htmlspecialchars($c_brand); ?>
                                    </span>
                                    <h3 class="tc-cart-name" title="<?php echo htmlspecialchars($c_name); ?>">
                                        <a href="product_details.php?id=<?php echo $p_id; ?>">
                                            <?php echo htmlspecialchars($c_name); ?>
                                        </a>
                                    </h3>
                                    <div class="tc-cart-meta">
                                        <span>Unit: <strong style="color: #fff;"><i class="fa-solid fa-indian-rupee-sign"></i> <?php echo number_format($c_price); ?></strong></span>
                                        <span>&bull;</span>
                                        <?php if ($c_stock > 5) { ?>
                                            <span class="tc-stock-tag in-stock"><i class="fa-solid fa-circle-check"></i> In Stock</span>
                                        <?php } elseif ($c_stock > 0) { ?>
                                            <span class="tc-stock-tag low-stock"><i class="fa-solid fa-clock"></i> Only <?php echo $c_stock; ?> left</span>
                                        <?php } else { ?>
                                            <span class="tc-stock-tag out-stock"><i class="fa-solid fa-circle-xmark"></i> Out of Stock</span>
                                        <?php } ?>
                                    </div>

                                    <!-- QUANTITY SELECTOR CONTROLS -->
                                    <div class="tc-qty-controls">
                                        <a 
                                            href="cart.php?action=update_qty&id=<?php echo $c_id; ?>&change=minus" 
                                            class="tc-qty-btn"
                                            title="<?php echo $c_qty === 1 ? 'Remove from cart' : 'Decrease Quantity'; ?>"
                                            <?php if ($c_qty === 1) { ?>
                                                onclick="return confirm('Remove <?php echo htmlspecialchars(addslashes($c_name)); ?> from your cart?');"
                                            <?php } ?>
                                        >
                                            <i class="fa-solid fa-minus"></i>
                                        </a>

                                        <span class="tc-qty-value"><?php echo $c_qty; ?></span>

                                        <a 
                                            href="cart.php?action=update_qty&id=<?php echo $c_id; ?>&change=plus" 
                                            class="tc-qty-btn <?php echo ($c_qty >= $c_stock) ? 'disabled' : ''; ?>"
                                            title="<?php echo ($c_qty >= $c_stock) ? 'Maximum stock reached' : 'Increase Quantity'; ?>"
                                        >
                                            <i class="fa-solid fa-plus"></i>
                                        </a>
                                    </div>
                                </div>

                                <!-- TOTAL & REMOVE -->
                                <div class="tc-cart-right-box">
                                    <div class="tc-cart-item-total">
                                        <i class="fa-solid fa-indian-rupee-sign"></i> <?php echo number_format($c_total); ?>
                                    </div>
                                    <a 
                                        href="cart.php?action=remove&id=<?php echo $c_id; ?>" 
                                        class="tc-btn-remove-cart"
                                        title="Remove Item"
                                        onclick="return confirm('Remove <?php echo htmlspecialchars(addslashes($c_name)); ?> from your cart?');"
                                    >
                                        <i class="fa-solid fa-trash-can"></i> Remove
                                    </a>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>

                <!-- ================= RIGHT COLUMN: SUMMARY & CHECKOUT ================= -->
                <div class="tc-box">
                    <div class="tc-box-header">
                        <h2 class="tc-box-title">
                            <i class="fa-solid fa-receipt"></i> Order Summary
                        </h2>
                    </div>

                    <div class="tc-breakdown">
                        <div class="tc-breakdown-row">
                            <span>Subtotal (<?php echo $tc_total_qty; ?> <?php echo $tc_total_qty === 1 ? 'item' : 'items'; ?>)</span>
                            <span class="val"><i class="fa-solid fa-indian-rupee-sign"></i> <?php echo number_format($tc_grand_total); ?></span>
                        </div>
                        <div class="tc-breakdown-row free-badge">
                            <span>Luxury Express Delivery</span>
                            <span class="val"><i class="fa-solid fa-check"></i> FREE</span>
                        </div>
                        <div class="tc-breakdown-row">
                            <span>Estimated Delivery</span>
                            <span class="val">2 - 4 Business Days</span>
                        </div>
                        <div class="tc-breakdown-row">
                            <span>Applicable Taxes</span>
                            <span class="val">Included</span>
                        </div>

                        <div class="tc-breakdown-divider"></div>

                        <div class="tc-breakdown-total">
                            <span>Grand Total</span>
                            <span class="total-val"><i class="fa-solid fa-indian-rupee-sign"></i> <?php echo number_format($tc_grand_total); ?></span>
                        </div>
                    </div>

                    <!-- CTA BUTTONS -->
                    <a href="checkout.php" class="tc-btn-checkout">
                        <i class="fa-solid fa-lock"></i> Proceed to Checkout <i class="fa-solid fa-arrow-right"></i>
                    </a>

                    <a href="<?php echo htmlspecialchars($continue_url); ?>" class="tc-btn-continue-shop">
                        <i class="fa-solid fa-compass"></i> Continue Shopping
                    </a>

                    <!-- TRUST BADGES -->
                    <div class="tc-trust-badges">
                        <div class="tc-trust-item">
                            <i class="fa-solid fa-award"></i>
                            <span>100% Authentic</span>
                        </div>
                        <div class="tc-trust-item">
                            <i class="fa-solid fa-truck-fast"></i>
                            <span>Free Shipping</span>
                        </div>
                        <div class="tc-trust-item">
                            <i class="fa-solid fa-shield-halved"></i>
                            <span>2-Yr Warranty</span>
                        </div>
                    </div>
                </div>

            </div>

        <?php } else { ?>

            <!-- EMPTY CART STATE -->
            <div class="tc-empty-cart-card">
                <div class="tc-empty-cart-icon">
                    <i class="fa-solid fa-cart-shopping"></i>
                </div>
                <h3>Your Cart is Empty</h3>
                <p>
                    You have not added any luxury timepieces to your cart yet. Explore our handcrafted collections and add your favorite timepieces.
                </p>
                <div class="tc-empty-actions-group">
                    <a href="index.php" class="tc-btn-explore-watches">
                        <i class="fa-solid fa-house"></i>
                        Go to Home
                    </a>
                </div>
            </div>

        <?php } ?>

    </div>
</section>

<?php 
/* =====================================================
   INCLUDE FOOTER
===================================================== */
include("footer.php"); 
?>