<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include("conn.php");

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
    header("Location: login.php");
    exit();
}

$user_name = $_SESSION['user_name'] ?? ($_SESSION['name'] ?? ($_SESSION['admin_name'] ?? ''));
$user_email = $_SESSION['user_email'] ?? ($_SESSION['email'] ?? '');

/* =====================================================
   FETCH CART DATA (SECURE WITH PREPARED STATEMENT)
===================================================== */
$tc_cart_stmt = mysqli_prepare($conn, "
    SELECT 
        cart.id AS cart_id,
        cart.qty,
        cart.product_id,
        product.product_name,
        product.brand,
        product.price,
        product.image
    FROM cart
    INNER JOIN product ON cart.product_id = product.id
    WHERE cart.user_id = ?
    ORDER BY cart.id DESC
");

$tc_cart_items = [];
$tc_subtotal = 0;
$tc_cart_count = 0;

if ($tc_cart_stmt) {
    mysqli_stmt_bind_param($tc_cart_stmt, "i", $user_id);
    mysqli_stmt_execute($tc_cart_stmt);
    $tc_cart_res = mysqli_stmt_get_result($tc_cart_stmt);
    if ($tc_cart_res) {
        while ($item = mysqli_fetch_assoc($tc_cart_res)) {
            $tc_cart_items[] = $item;
            $tc_subtotal += (float)$item['price'] * (int)$item['qty'];
            $tc_cart_count += (int)$item['qty'];
        }
    }
}

/* =====================================================
   INCLUDE HEADER (Using unique variables to avoid collision)
===================================================== */
include("header.php");

?>

<!-- =====================================================
     CHECKOUT CSS (TIME CRAFT LUXURY THEME)
===================================================== -->
<style>
:root {
    --tc-gold: #d4af37;
    --tc-gold-light: #f3e5ab;
    --tc-gold-dark: #b89127;
    --tc-gold-hover: #ffc107;
    --tc-dark-bg: #0a0a0a;
    --tc-card-bg: #141414;
    --tc-card-hover: #1a1a1a;
    --tc-text-primary: #ffffff;
    --tc-text-secondary: #a8a8a8;
    --tc-border-gold: rgba(212, 175, 55, 0.22);
    --tc-border-gold-hover: rgba(212, 175, 55, 0.7);
    --tc-danger: #ff4d4f;
    --tc-success: #2ecc71;
}

.tc-checkout-section {
    background: linear-gradient(180deg, #0a0a0a 0%, #121212 100%);
    min-height: 80vh;
    padding: 50px 0 90px 0;
    color: var(--tc-text-primary);
    overflow-x: hidden;
    width: 100%;
}

/* HERO */
.tc-checkout-hero {
    text-align: center;
    margin-bottom: 40px;
}

.tc-checkout-badge {
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

.tc-checkout-title {
    font-size: 38px;
    font-weight: 800;
    color: #ffffff;
    letter-spacing: 0.5px;
    margin-bottom: 10px;
}

.tc-checkout-title span {
    color: var(--tc-gold);
}

.tc-checkout-desc {
    color: var(--tc-text-secondary);
    font-size: 16px;
    max-width: 580px;
    margin: 0 auto;
}

/* 2-COLUMN CHECKOUT GRID */
.tc-checkout-grid {
    display: grid;
    grid-template-columns: 1.15fr 1fr;
    gap: 35px;
    align-items: start;
}

/* CARD BOX */
.tc-box {
    background: var(--tc-card-bg);
    border: 1px solid var(--tc-border-gold);
    border-radius: 18px;
    padding: 32px 28px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
}

.tc-box-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 24px;
    padding-bottom: 16px;
    border-bottom: 1px solid var(--tc-border-gold);
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

.tc-item-count-tag {
    background: rgba(212, 175, 55, 0.15);
    color: var(--tc-gold);
    border: 1px solid var(--tc-border-gold);
    font-size: 12px;
    font-weight: 700;
    padding: 3px 10px;
    border-radius: 20px;
}

/* ORDER SUMMARY ITEM LIST */
.tc-summary-list {
    display: flex;
    flex-direction: column;
    gap: 18px;
    max-height: 420px;
    overflow-y: auto;
    padding-right: 6px;
    margin-bottom: 24px;
}

.tc-summary-list::-webkit-scrollbar {
    width: 5px;
}
.tc-summary-list::-webkit-scrollbar-thumb {
    background: var(--tc-gold);
    border-radius: 10px;
}
.tc-summary-list::-webkit-scrollbar-track {
    background: #1a1a1a;
}

.tc-summary-item {
    display: flex;
    align-items: center;
    gap: 16px;
    background: #1a1a1a;
    border: 1px solid rgba(255, 255, 255, 0.08);
    border-radius: 12px;
    padding: 14px;
    transition: all 0.3s ease;
}

.tc-summary-item:hover {
    border-color: var(--tc-border-gold);
    transform: translateX(4px);
}

.tc-summary-img-box {
    width: 75px;
    height: 75px;
    background: radial-gradient(circle, #2a2a2a 0%, #171717 100%);
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    padding: 6px;
    border: 1px solid rgba(212, 175, 55, 0.2);
}

.tc-summary-img {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
}

.tc-summary-info {
    flex: 1;
    min-width: 0;
}

.tc-summary-name {
    font-size: 16px;
    font-weight: 700;
    color: #ffffff;
    margin-bottom: 4px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.tc-summary-meta {
    font-size: 13px;
    color: var(--tc-text-secondary);
    display: flex;
    align-items: center;
    gap: 10px;
}

.tc-summary-meta .price {
    color: var(--tc-gold);
    font-weight: 700;
}

.tc-summary-total-box {
    text-align: right;
}

.tc-summary-total {
    font-size: 16px;
    font-weight: 800;
    color: var(--tc-gold);
}

.tc-btn-remove-checkout {
    background: transparent;
    border: none;
    color: #777;
    font-size: 14px;
    margin-top: 4px;
    display: inline-block;
    cursor: pointer;
    transition: all 0.2s ease;
}

.tc-btn-remove-checkout:hover {
    color: var(--tc-danger);
    transform: scale(1.15);
}

/* PRICE BREAKDOWN */
.tc-breakdown {
    background: #181818;
    border: 1px solid var(--tc-border-gold);
    border-radius: 12px;
    padding: 18px 20px;
}

.tc-breakdown-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-size: 14px;
    color: var(--tc-text-secondary);
    margin-bottom: 10px;
}

.tc-breakdown-row span.val {
    color: #ffffff;
    font-weight: 600;
}

.tc-breakdown-row.free-badge span.val {
    color: var(--tc-success);
    font-weight: 700;
}

.tc-breakdown-divider {
    height: 1px;
    background: rgba(255, 255, 255, 0.1);
    margin: 14px 0;
}

.tc-breakdown-total {
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-size: 18px;
    font-weight: 800;
    color: #ffffff;
}

.tc-breakdown-total span.total-val {
    font-size: 24px;
    color: var(--tc-gold);
}

/* TRUST BADGES */
.tc-trust-badges {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 10px;
    margin-top: 20px;
    padding-top: 16px;
    border-top: 1px solid rgba(255, 255, 255, 0.08);
}

.tc-trust-item {
    text-align: center;
    font-size: 12px;
    color: var(--tc-text-secondary);
}

.tc-trust-item i {
    color: var(--tc-gold);
    font-size: 18px;
    margin-bottom: 4px;
    display: block;
}

/* FORM STYLES */
.tc-form-group {
    margin-bottom: 20px;
}

.tc-form-label {
    display: block;
    font-size: 14px;
    font-weight: 700;
    color: #e0e0e0;
    margin-bottom: 8px;
}

.tc-form-label span.req {
    color: var(--tc-danger);
}

.tc-input-wrapper {
    position: relative;
    display: flex;
    align-items: center;
}

.tc-input-wrapper i.input-icon {
    position: absolute;
    left: 14px;
    color: var(--tc-gold);
    font-size: 15px;
    pointer-events: none;
}

.tc-form-input,
.tc-form-textarea {
    width: 100%;
    background: #1c1c1c;
    border: 1px solid rgba(255, 255, 255, 0.15);
    color: #ffffff;
    border-radius: 8px;
    padding: 12px 14px 12px 42px;
    font-size: 14px;
    outline: none;
    transition: all 0.3s ease;
}

.tc-form-textarea {
    min-height: 90px;
    resize: vertical;
}

.tc-form-input:focus,
.tc-form-textarea:focus {
    border-color: var(--tc-gold);
    box-shadow: 0 0 10px rgba(212, 175, 55, 0.25);
    background: #222222;
}

/* PAYMENT METHOD CARD (COD ONLY) */
.tc-payment-card {
    position: relative;
    background: rgba(212, 175, 55, 0.08);
    border: 1.5px solid var(--tc-gold);
    border-radius: 10px;
    padding: 15px 18px;
    display: flex;
    align-items: center;
    gap: 12px;
}

.tc-payment-card-info {
    font-size: 14px;
    font-weight: 700;
    color: #ffffff;
    display: flex;
    align-items: center;
    gap: 8px;
}

.tc-payment-card-info i {
    color: var(--tc-gold);
    font-size: 18px;
}

/* PLACE ORDER BUTTON */
.tc-btn-place-order {
    width: 100%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    background: linear-gradient(135deg, #d4af37 0%, #aa8214 100%);
    color: #000000 !important;
    font-weight: 800;
    font-size: 16px;
    padding: 15px 24px;
    border-radius: 10px;
    border: none;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 6px 20px rgba(212, 175, 55, 0.35);
    margin-top: 10px;
}

.tc-btn-place-order:hover {
    background: linear-gradient(135deg, #ffc107 0%, #d4af37 100%);
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(255, 193, 7, 0.55);
}

.tc-back-cart-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    color: var(--tc-text-secondary);
    text-decoration: none;
    font-size: 14px;
    font-weight: 600;
    margin-top: 18px;
    transition: all 0.3s ease;
}

.tc-back-cart-btn:hover {
    color: var(--tc-gold);
}

/* EMPTY CART BOX */
.tc-empty-cart-card {
    max-width: 580px;
    margin: 40px auto;
    background: #141414;
    border: 1px solid var(--tc-border-gold);
    border-radius: 20px;
    padding: 60px 30px;
    text-align: center;
    box-shadow: 0 12px 35px rgba(0, 0, 0, 0.5);
}

.tc-empty-cart-icon {
    width: 90px;
    height: 90px;
    margin: 0 auto 20px;
    border-radius: 50%;
    background: rgba(212, 175, 55, 0.08);
    border: 2px dashed var(--tc-border-gold);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--tc-gold);
    font-size: 40px;
}

.tc-empty-cart-card h3 {
    font-size: 26px;
    font-weight: 800;
    color: #ffffff;
    margin-bottom: 10px;
}

.tc-empty-cart-card p {
    color: var(--tc-text-secondary);
    font-size: 15px;
    margin-bottom: 25px;
}

.tc-btn-explore-watches {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    background: linear-gradient(135deg, #d4af37 0%, #aa8214 100%);
    color: #000000 !important;
    font-weight: 700;
    font-size: 14px;
    padding: 12px 28px;
    border-radius: 8px;
    text-decoration: none !important;
    transition: all 0.3s ease;
    box-shadow: 0 5px 18px rgba(212, 175, 55, 0.35);
}

.tc-btn-explore-watches:hover {
    background: linear-gradient(135deg, #ffc107 0%, #d4af37 100%);
    transform: translateY(-3px);
}

/* RESPONSIVE */
@media (max-width: 992px) {
    .tc-checkout-grid {
        grid-template-columns: 1fr;
        gap: 30px;
    }
    .tc-checkout-title {
        font-size: 30px;
    }
}

@media (max-width: 576px) {
    .tc-checkout-section {
        padding: 30px 0 60px 0;
    }
    .tc-checkout-title {
        font-size: 25px;
    }
    .tc-box {
        padding: 22px 18px;
    }
    .tc-trust-badges {
        grid-template-columns: 1fr;
        gap: 12px;
    }
}
</style>

<!-- =====================================================
     CHECKOUT HTML
===================================================== -->
<section class="tc-checkout-section">
    <div class="container">

        <!-- ================= HERO HEADER ================= -->
        <div class="tc-checkout-hero">
            <div class="tc-checkout-badge">
            </div>
            <h1 class="tc-checkout-title">
                Complete Your <span>Order</span>
            </h1>
            <p class="tc-checkout-desc">
                Review your luxury watch selection and provide your shipping details to complete your order.
            </p>
        </div>

        <?php if (!empty($tc_cart_items)) { ?>

            <div class="tc-checkout-grid">

                <!-- ================= LEFT COLUMN: ORDER SUMMARY ================= -->
                <div class="tc-box">
                    <div class="tc-box-header">
                        <h2 class="tc-box-title">
                            <i class="fa-solid fa-bag-shopping"></i> Order Summary
                        </h2>
                        <span class="tc-item-count-tag">
                            <?php echo $tc_cart_count; ?> <?php echo $tc_cart_count === 1 ? 'Watch' : 'Watches'; ?>
                        </span>
                    </div>

                    <!-- SUMMARY LIST -->
                    <div class="tc-summary-list">
                        <?php foreach ($tc_cart_items as $c_item) { 
                            $c_id = intval($c_item['cart_id']);
                            $c_name = $c_item['product_name'];
                            $c_brand = $c_item['brand'] ?? 'TimeCraft';
                            $c_price = (float)$c_item['price'];
                            $c_qty = (int)$c_item['qty'];
                            $c_total = $c_price * $c_qty;
                            $c_img = $c_item['image'];
                        ?>
                            <div class="tc-summary-item">
                                <div class="tc-summary-img-box">
                                    <img 
                                        src="upload/<?php echo htmlspecialchars($c_img); ?>" 
                                        alt="<?php echo htmlspecialchars($c_name); ?>"
                                        class="tc-summary-img"
                                        onerror="this.onerror=null; this.src='assets/img/logo/logo.png';"
                                    >
                                </div>
                                <div class="tc-summary-info">
                                    <div class="tc-summary-name" title="<?php echo htmlspecialchars($c_name); ?>">
                                        <?php echo htmlspecialchars($c_name); ?>
                                    </div>
                                    <div class="tc-summary-meta">
                                        <span><?php echo htmlspecialchars($c_brand); ?></span>
                                        <span>&bull;</span>
                                        <span class="price"><i class="fa-solid fa-indian-rupee-sign"></i> <?php echo number_format($c_price); ?></span>
                                        <span>&bull;</span>
                                        <span>Qty: <?php echo $c_qty; ?></span>
                                    </div>
                                </div>
                                <div class="tc-summary-total-box">
                                    <div class="tc-summary-total">
                                        <i class="fa-solid fa-indian-rupee-sign"></i> <?php echo number_format($c_total); ?>
                                    </div>
                                    <a 
                                        href="remove-checkout.php?id=<?php echo $c_id; ?>" 
                                        class="tc-btn-remove-checkout"
                                        title="Remove Item"
                                        onclick="return confirm('Remove <?php echo htmlspecialchars(addslashes($c_name)); ?> from your order?');"
                                    >
                                        <i class="fa-solid fa-trash-can"></i>
                                    </a>
                                </div>
                            </div>
                        <?php } ?>
                    </div>

                    <!-- BREAKDOWN -->
                    <div class="tc-breakdown">
                        <div class="tc-breakdown-row">
                            <span>Subtotal</span>
                            <span class="val"><i class="fa-solid fa-indian-rupee-sign"></i> <?php echo number_format($tc_subtotal); ?></span>
                        </div>
                        <div class="tc-breakdown-row free-badge">
                            <span>Luxury Express Delivery</span>
                            <span class="val"><i class="fa-solid fa-check"></i> FREE</span>
                        </div>
                        <div class="tc-breakdown-divider"></div>
                        <div class="tc-breakdown-total">
                            <span>Grand Total</span>
                            <span class="total-val"><i class="fa-solid fa-indian-rupee-sign"></i> <?php echo number_format($tc_subtotal); ?></span>
                        </div>
                    </div>

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
                            <i class="fa-solid fa-lock"></i>
                            <span>Secure Checkout</span>
                        </div>
                    </div>
                </div>

                <!-- ================= RIGHT COLUMN: BILLING & SHIPPING ================= -->
                <div class="tc-box">
                    <div class="tc-box-header">
                        <h2 class="tc-box-title">
                            <i class="fa-solid fa-truck-ramp-box"></i> Shipping & Payment
                        </h2>
                    </div>

                    <form action="place_order.php" method="POST" id="checkoutForm">

                        <!-- CUSTOMER NAME -->
                        <div class="tc-form-group">
                            <label class="tc-form-label">
                                Full Name <span class="req">*</span>
                            </label>
                            <div class="tc-input-wrapper">
                                <i class="fa-solid fa-user input-icon"></i>
                                <input 
                                    type="text" 
                                    name="customer_name" 
                                    class="tc-form-input" 
                                    placeholder="Enter your full name" 
                                    value="<?php echo htmlspecialchars($user_name); ?>" 
                                    required
                                >
                            </div>
                        </div>

                        <!-- EMAIL -->
                        <div class="tc-form-group">
                            <label class="tc-form-label">
                                Email Address <span class="req">*</span>
                            </label>
                            <div class="tc-input-wrapper">
                                <i class="fa-solid fa-envelope input-icon"></i>
                                <input 
                                    type="email" 
                                    name="email" 
                                    class="tc-form-input" 
                                    placeholder="Enter your email address" 
                                    value="<?php echo htmlspecialchars($user_email); ?>" 
                                    required
                                >
                            </div>
                        </div>

                        <!-- PHONE -->
                        <div class="tc-form-group">
                            <label class="tc-form-label">
                                Phone Number <span class="req">*</span>
                            </label>
                            <div class="tc-input-wrapper">
                                <i class="fa-solid fa-phone input-icon"></i>
                                <input 
                                    type="tel" 
                                    name="phone" 
                                    class="tc-form-input" 
                                    placeholder="Enter 10-digit mobile number" 
                                    required
                                >
                            </div>
                        </div>

                        <!-- DELIVERY ADDRESS -->
                        <div class="tc-form-group">
                            <label class="tc-form-label">
                                Delivery Address <span class="req">*</span>
                            </label>
                            <div class="tc-input-wrapper">
                                <i class="fa-solid fa-location-dot input-icon" style="top: 18px;"></i>
                                <textarea 
                                    name="address" 
                                    class="tc-form-textarea" 
                                    placeholder="Enter complete shipping address (House/Flat No, Street, City, Pincode)" 
                                    required
                                ></textarea>
                            </div>
                        </div>

                        <!-- PAYMENT METHOD (CASH ON DELIVERY ONLY) -->
                        <div class="tc-form-group">
                            <label class="tc-form-label">
                                Payment Method
                            </label>
                            <input type="hidden" name="payment_method" value="Cash On Delivery">
                            <div class="tc-payment-card">
                                <i class="fa-solid fa-circle-check" style="color: var(--tc-gold); font-size: 18px;"></i>
                                <div class="tc-payment-card-info">
                                    <i class="fa-solid fa-money-bill-wave"></i> Cash On Delivery (Pay on delivery)
                                </div>
                            </div>
                        </div>

                        <!-- SUBMIT BUTTON -->
                        <button type="submit" name="place_order" class="tc-btn-place-order">
                            <i class="fa-solid fa-lock"></i> Place Order (<i class="fa-solid fa-indian-rupee-sign"></i> <?php echo number_format($tc_subtotal); ?>)
                        </button>

                    </form>

                    <!-- BACK TO CART -->
                    <a href="cart.php" class="tc-back-cart-btn">
                        <i class="fa-solid fa-arrow-left"></i> Return to Shopping Cart
                    </a>
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
                    You have no items in your cart to checkout. Please explore our luxury watch catalog and add your favorite timepieces.
                </p>
                <a href="brand.php" class="tc-btn-explore-watches">
                    <i class="fa-solid fa-compass"></i> Explore Luxury Watches
                </a>
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