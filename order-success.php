<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include("conn.php");

$continue_shopping = "brand.php";
if (isset($_SESSION['last_shop_page']) && !empty($_SESSION['last_shop_page'])) {
    $continue_shopping = $_SESSION['last_shop_page'];
} elseif (isset($_SESSION['last_brand_page']) && !empty($_SESSION['last_brand_page'])) {
    $continue_shopping = $_SESSION['last_brand_page'];
}

include("header.php");
?>

<!-- =====================================================
     MINIMALIST LUXURY ORDER CONFIRMED POPUP
===================================================== -->
<style>
:root {
    --tc-gold: #d4af37;
    --tc-gold-light: #f3e5ab;
    --tc-gold-dark: #b89127;
    --tc-gold-glow: rgba(212, 175, 55, 0.4);
    --tc-dark-bg: #0a0a0a;
    --tc-card-bg: #141414;
    --tc-border-gold: rgba(212, 175, 55, 0.3);
    --tc-text-main: #ffffff;
    --tc-text-muted: #a0a0a0;
    --tc-success: #2ecc71;
}

.tc-success-modal-section {
    background: radial-gradient(circle at 50% 40%, #1c190f 0%, #0a0a0a 75%);
    min-height: 80vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 60px 20px;
}

/* POPUP MODAL CARD */
.tc-success-modal-card {
    background: var(--tc-card-bg);
    border: 2px solid var(--tc-gold);
    border-radius: 24px;
    max-width: 520px;
    width: 100%;
    padding: 50px 35px;
    text-align: center;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.8), 0 0 35px rgba(212, 175, 55, 0.25);
    position: relative;
    animation: tcModalPop 0.45s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;
}

@keyframes tcModalPop {
    0% { transform: scale(0.85); opacity: 0; }
    100% { transform: scale(1); opacity: 1; }
}

/* GLOWING ANIMATED SUCCESS ICON */
.tc-modal-icon-wrap {
    position: relative;
    width: 95px;
    height: 95px;
    margin: 0 auto 24px auto;
}

.tc-modal-icon {
    width: 95px;
    height: 95px;
    border-radius: 50%;
    background: linear-gradient(135deg, #d4af37 0%, #aa8214 100%);
    color: #0a0a0a;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 45px;
    box-shadow: 0 0 35px var(--tc-gold-glow);
}

.tc-modal-ring {
    position: absolute;
    top: -7px;
    left: -7px;
    right: -7px;
    bottom: -7px;
    border: 2px dashed var(--tc-gold);
    border-radius: 50%;
    animation: tcSpin 25s linear infinite;
    opacity: 0.85;
}

@keyframes tcSpin {
    100% { transform: rotate(360deg); }
}

.tc-modal-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: rgba(46, 204, 113, 0.15);
    border: 1px solid rgba(46, 204, 113, 0.4);
    color: #2ecc71;
    font-size: 13px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1.5px;
    padding: 5px 18px;
    border-radius: 30px;
    margin-bottom: 15px;
}

.tc-modal-title {
    font-size: 32px;
    font-weight: 800;
    color: #ffffff;
    margin-bottom: 12px;
}

.tc-modal-title span {
    color: var(--tc-gold);
}

.tc-modal-desc {
    color: var(--tc-text-muted);
    font-size: 15px;
    line-height: 1.6;
    margin-bottom: 35px;
    max-width: 440px;
    margin-left: auto;
    margin-right: auto;
}

/* ACTION BUTTONS */
.tc-modal-actions {
    display: flex;
    flex-direction: column;
    gap: 14px;
}

.tc-btn-myorders {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    background: linear-gradient(135deg, #d4af37 0%, #aa8214 100%);
    color: #000000 !important;
    font-size: 15px;
    font-weight: 800;
    padding: 15px 24px;
    border-radius: 10px;
    text-decoration: none !important;
    transition: all 0.3s ease;
    box-shadow: 0 6px 22px rgba(212, 175, 55, 0.35);
}

.tc-btn-myorders:hover {
    background: linear-gradient(135deg, #ffc107 0%, #d4af37 100%);
    transform: translateY(-2px);
    box-shadow: 0 10px 28px rgba(212, 175, 55, 0.5);
}

.tc-btn-shopping {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    background: transparent;
    color: #ffffff !important;
    font-size: 14px;
    font-weight: 700;
    padding: 13px 20px;
    border-radius: 10px;
    border: 1px solid rgba(255, 255, 255, 0.2);
    text-decoration: none !important;
    transition: all 0.3s ease;
}

.tc-btn-shopping:hover {
    border-color: var(--tc-gold);
    color: var(--tc-gold) !important;
    background: rgba(212, 175, 55, 0.08);
}

@media (max-width: 576px) {
    .tc-success-modal-card {
        padding: 40px 22px;
    }
    .tc-modal-title {
        font-size: 27px;
    }
}
</style>

<!-- =====================================================
     ORDER CONFIRMED POPUP HTML
===================================================== -->
<section class="tc-success-modal-section">
    <div class="tc-success-modal-card">

        <!-- ANIMATED SUCCESS ICON -->
        <div class="tc-modal-icon-wrap">
            <div class="tc-modal-ring"></div>
            <div class="tc-modal-icon">
                <i class="fa-solid fa-check"></i>
            </div>
        </div>

        <div class="tc-modal-badge">
            <i class="fa-solid fa-circle-check"></i> Order Placed
        </div>

        <h1 class="tc-modal-title">
            Order <span>Confirmed!</span>
        </h1>

        <p class="tc-modal-desc">
            Thank you for shopping with Time Craft. Your order has been placed successfully.
        </p>

        <!-- ACTION BUTTONS -->
        <div class="tc-modal-actions">
            <a href="my_order.php" class="tc-btn-myorders">
                <i class="fa-solid fa-truck-fast"></i> View in My Orders
            </a>

            <a href="<?php echo htmlspecialchars($continue_shopping); ?>" class="tc-btn-shopping">
                <i class="fa-solid fa-compass"></i> Continue Shopping
            </a>
        </div>

    </div>
</section>

<?php
include("footer.php");
?>