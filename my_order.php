<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include("conn.php");

date_default_timezone_set('Asia/Kolkata');
mysqli_query($conn, "SET time_zone = '+05:30'");

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
    $_SESSION['login_redirect'] = 'my_order.php';
    header("Location: login.php");
    exit();
}

/* =====================================================
   GET USER EMAIL
===================================================== */
$user_email = $_SESSION['user_email'] ?? ($_SESSION['email'] ?? '');
$user_name = $_SESSION['user_name'] ?? ($_SESSION['name'] ?? '');

$user_stmt = mysqli_prepare($conn, "SELECT name, email FROM reg WHERE id = ? LIMIT 1");
if ($user_stmt) {
    mysqli_stmt_bind_param($user_stmt, "i", $user_id);
    mysqli_stmt_execute($user_stmt);
    $u_res = mysqli_stmt_get_result($user_stmt);
    if ($u_res && $u_row = mysqli_fetch_assoc($u_res)) {
        $user_email = $u_row['email'];
        if (empty($user_name)) {
            $user_name = $u_row['name'];
        }
    }
    mysqli_stmt_close($user_stmt);
}

if (empty($user_email)) {
    header("Location: logout.php");
    exit();
}

/* =====================================================
   HANDLE USER CANCEL ORDER (IF PENDING)
===================================================== */
if (isset($_POST['cancel_order']) && $_POST['cancel_order'] == "1") {
    $cancel_id = intval($_POST['order_id'] ?? 0);
    if ($cancel_id > 0) {
        $chk_stmt = mysqli_prepare($conn, "SELECT id, product_name, quantity, order_status FROM `order` WHERE id = ? AND email = ? LIMIT 1");
        if ($chk_stmt) {
            mysqli_stmt_bind_param($chk_stmt, "is", $cancel_id, $user_email);
            mysqli_stmt_execute($chk_stmt);
            $chk_res = mysqli_stmt_get_result($chk_stmt);
            if ($chk_res && $ord_to_cancel = mysqli_fetch_assoc($chk_res)) {
                if (strtolower($ord_to_cancel['order_status']) === 'pending') {
                    // Update order status to Cancelled
                    $up_stmt = mysqli_prepare($conn, "UPDATE `order` SET order_status = 'Cancelled' WHERE id = ? LIMIT 1");
                    if ($up_stmt) {
                        mysqli_stmt_bind_param($up_stmt, "i", $cancel_id);
                        mysqli_stmt_execute($up_stmt);
                        mysqli_stmt_close($up_stmt);

                        // Restore stock
                        $p_name_esc = mysqli_real_escape_string($conn, $ord_to_cancel['product_name']);
                        $restore_qty = intval($ord_to_cancel['quantity']);
                        mysqli_query($conn, "UPDATE product SET stock = stock + $restore_qty WHERE TRIM(LOWER(product_name)) = TRIM(LOWER('$p_name_esc')) LIMIT 1");

                        $_SESSION['order_user_msg'] = "Order #TC-{$cancel_id} has been cancelled successfully.";
                        $_SESSION['order_user_msg_type'] = "success";
                    }
                } else {
                    $_SESSION['order_user_msg'] = "Only pending orders can be cancelled.";
                    $_SESSION['order_user_msg_type'] = "warning";
                }
            }
            mysqli_stmt_close($chk_stmt);
        }
    }
    header("Location: my_order.php");
    exit();
}

$user_msg = $_SESSION['order_user_msg'] ?? '';
$user_msg_type = $_SESSION['order_user_msg_type'] ?? '';
unset($_SESSION['order_user_msg'], $_SESSION['order_user_msg_type']);

/* =====================================================
   FETCH ORDERS WITH DETAILS & PRODUCT JOIN
===================================================== */
$filter_tab = strtolower(trim($_GET['tab'] ?? 'all'));

$order_query = "
    SELECT
        o.*,
        p.id AS product_id,
        p.image AS product_image,
        p.brand AS product_brand
    FROM `order` AS o
    LEFT JOIN product AS p
        ON TRIM(LOWER(p.product_name)) = TRIM(LOWER(o.product_name))
    WHERE o.email = ?
    ORDER BY o.id DESC
";

$order_stmt = mysqli_prepare($conn, $order_query);
$orders_list = [];
$counts = [
    'all' => 0,
    'active' => 0,
    'delivered' => 0,
    'cancelled' => 0
];

if ($order_stmt) {
    mysqli_stmt_bind_param($order_stmt, "s", $user_email);
    mysqli_stmt_execute($order_stmt);
    $order_result = mysqli_stmt_get_result($order_stmt);
    if ($order_result) {
        while ($row = mysqli_fetch_assoc($order_result)) {
            $st_clean = strtolower(trim($row['order_status'] ?? 'pending'));
            $counts['all']++;

            if (in_array($st_clean, ['pending', 'confirmed', 'shipped'])) {
                $counts['active']++;
            } elseif ($st_clean === 'delivered') {
                $counts['delivered']++;
            } elseif ($st_clean === 'cancelled') {
                $counts['cancelled']++;
            }

            // Tab filtering
            if ($filter_tab === 'active' && !in_array($st_clean, ['pending', 'confirmed', 'shipped'])) {
                continue;
            } elseif ($filter_tab === 'delivered' && $st_clean !== 'delivered') {
                continue;
            } elseif ($filter_tab === 'cancelled' && $st_clean !== 'cancelled') {
                continue;
            }

            $orders_list[] = $row;
        }
    }
    mysqli_stmt_close($order_stmt);
}

include("header.php");
?>

<!-- =====================================================
     MY ORDERS & LIVE DELIVERY TRACKER STYLES
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
    --tc-border-gold-hover: rgba(212, 175, 55, 0.65);
    --tc-border-subtle: rgba(255, 255, 255, 0.08);
    --tc-text: #ffffff;
    --tc-text-muted: #a0a0a0;
    --tc-pending: #f39c12;
    --tc-confirmed: #3498db;
    --tc-shipped: #9b59b6;
    --tc-delivered: #2ecc71;
    --tc-cancelled: #e74c3c;
}

.tc-myorders-section {
    background: radial-gradient(circle at 50% 0%, #1a170e 0%, #0a0a0a 75%);
    min-height: 85vh;
    padding: 50px 0 90px 0;
    color: var(--tc-text);
    overflow-x: hidden;
    width: 100%;
}

/* HERO */
.tc-orders-hero {
    text-align: center;
    margin-bottom: 35px;
}

.tc-orders-badge {
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

.tc-orders-title {
    font-size: 38px;
    font-weight: 800;
    color: #ffffff;
    letter-spacing: 0.5px;
    margin-bottom: 10px;
}

.tc-orders-title span {
    color: var(--tc-gold);
}

.tc-orders-desc {
    color: var(--tc-text-muted);
    font-size: 16px;
    max-width: 620px;
    margin: 0 auto;
}

/* FLASH ALERTS */
.tc-user-alert {
    max-width: 1100px;
    margin: 0 auto 25px auto;
    padding: 14px 20px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    gap: 12px;
    font-size: 14px;
    font-weight: 600;
    animation: tcSlideDown 0.3s ease;
}

.tc-user-alert.success {
    background: rgba(46, 204, 113, 0.15);
    border: 1px solid rgba(46, 204, 113, 0.4);
    color: #2ecc71;
}

.tc-user-alert.warning {
    background: rgba(243, 156, 18, 0.15);
    border: 1px solid rgba(243, 156, 18, 0.4);
    color: #f39c12;
}

/* TAB NAVIGATION */
.tc-tabs-wrap {
    display: flex;
    justify-content: center;
    gap: 10px;
    margin-bottom: 35px;
    flex-wrap: wrap;
}

.tc-tab-btn {
    background: var(--tc-card-bg);
    border: 1px solid var(--tc-border-subtle);
    color: #bbb;
    font-size: 13px;
    font-weight: 700;
    padding: 10px 22px;
    border-radius: 30px;
    text-decoration: none !important;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.25s ease;
}

.tc-tab-btn:hover {
    border-color: var(--tc-gold);
    color: #ffffff;
    transform: translateY(-2px);
}

.tc-tab-btn.active {
    background: rgba(212, 175, 55, 0.18);
    border-color: var(--tc-gold);
    color: var(--tc-gold);
    box-shadow: 0 4px 15px rgba(212, 175, 55, 0.2);
}

.tc-tab-count {
    background: rgba(255, 255, 255, 0.1);
    font-size: 11px;
    padding: 2px 8px;
    border-radius: 12px;
}

.tc-tab-btn.active .tc-tab-count {
    background: var(--tc-gold);
    color: #000;
}

/* ORDERS LIST */
.tc-orders-list {
    display: flex;
    flex-direction: column;
    gap: 30px;
    max-width: 1100px;
    margin: 0 auto;
}

/* INDIVIDUAL ORDER CARD */
.tc-order-card {
    background: var(--tc-card-bg);
    border: 1px solid var(--tc-border-gold);
    border-radius: 20px;
    padding: 28px;
    box-shadow: 0 10px 35px rgba(0, 0, 0, 0.5);
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.tc-order-card:hover {
    border-color: var(--tc-border-gold-hover);
    box-shadow: 0 12px 40px rgba(0, 0, 0, 0.7), 0 0 20px rgba(212, 175, 55, 0.12);
}

/* CARD TOP BAR */
.tc-order-topbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid var(--tc-border-subtle);
    padding-bottom: 18px;
    margin-bottom: 22px;
    flex-wrap: wrap;
    gap: 12px;
}

.tc-order-ref-group {
    display: flex;
    align-items: center;
    gap: 14px;
}

.tc-order-id-badge {
    background: rgba(212, 175, 55, 0.12);
    border: 1px solid var(--tc-border-gold);
    color: var(--tc-gold);
    font-size: 15px;
    font-weight: 800;
    padding: 5px 14px;
    border-radius: 8px;
}

.tc-order-date-text {
    font-size: 13px;
    color: var(--tc-text-muted);
}

.tc-order-status-pill {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 12px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.8px;
    padding: 6px 14px;
    border-radius: 20px;
}

.tc-order-status-pill.pending { background: rgba(243, 156, 18, 0.15); color: #f39c12; border: 1px solid rgba(243, 156, 18, 0.4); }
.tc-order-status-pill.confirmed { background: rgba(52, 152, 219, 0.15); color: #3498db; border: 1px solid rgba(52, 152, 219, 0.4); }
.tc-order-status-pill.shipped { background: rgba(155, 89, 182, 0.15); color: #9b59b6; border: 1px solid rgba(155, 89, 182, 0.4); }
.tc-order-status-pill.delivered { background: rgba(46, 204, 113, 0.15); color: #2ecc71; border: 1px solid rgba(46, 204, 113, 0.4); }
.tc-order-status-pill.cancelled { background: rgba(231, 76, 60, 0.15); color: #e74c3c; border: 1px solid rgba(231, 76, 60, 0.4); }

/* ================= LIVE DELIVERY PROGRESS TRACKER ================= */
.tc-delivery-tracker-box {
    background: var(--tc-card-inner);
    border: 1px solid var(--tc-border-subtle);
    border-radius: 14px;
    padding: 22px 25px;
    margin-bottom: 24px;
    position: relative;
}

.tc-tracker-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 22px;
}

.tc-tracker-title {
    font-size: 13px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 1px;
    color: var(--tc-gold);
    display: flex;
    align-items: center;
    gap: 8px;
}

.tc-tracker-current-location {
    font-size: 12px;
    font-weight: 700;
    color: #fff;
    background: #111;
    padding: 4px 12px;
    border-radius: 20px;
    border: 1px solid var(--tc-border-gold);
}

.tc-progress-milestones {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    position: relative;
    text-align: center;
}

.tc-progress-milestones::before {
    content: '';
    position: absolute;
    top: 18px;
    left: 8%;
    right: 8%;
    height: 3px;
    background: rgba(255, 255, 255, 0.1);
    z-index: 1;
}

.tc-milestone-step {
    position: relative;
    z-index: 2;
    display: flex;
    flex-direction: column;
    align-items: center;
}

.tc-milestone-icon {
    width: 38px;
    height: 38px;
    border-radius: 50%;
    background: #202020;
    border: 2px solid rgba(255, 255, 255, 0.15);
    color: #777;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    margin-bottom: 8px;
    transition: all 0.3s ease;
}

.tc-milestone-label {
    font-size: 12px;
    font-weight: 700;
    color: var(--tc-text-muted);
}

.tc-milestone-sub {
    font-size: 10px;
    color: #666;
    margin-top: 2px;
}

/* ACTIVE & COMPLETED STEP STATES */
.tc-milestone-step.done .tc-milestone-icon {
    background: #2ecc71;
    border-color: #2ecc71;
    color: #ffffff;
    box-shadow: 0 0 14px rgba(46, 204, 113, 0.4);
}

.tc-milestone-step.done .tc-milestone-label {
    color: #ffffff;
}

.tc-milestone-step.active .tc-milestone-icon {
    background: var(--tc-gold);
    border-color: var(--tc-gold);
    color: #0b0b0b;
    box-shadow: 0 0 16px rgba(212, 175, 55, 0.5);
    transform: scale(1.1);
}

.tc-milestone-step.active .tc-milestone-label {
    color: var(--tc-gold);
}

/* CANCELLED STATE OVERRIDE */
.tc-cancelled-timeline {
    background: rgba(231, 76, 60, 0.1);
    border: 1px solid rgba(231, 76, 60, 0.3);
    border-radius: 10px;
    padding: 14px 18px;
    display: flex;
    align-items: center;
    gap: 12px;
    color: #ff7777;
    font-size: 13px;
    font-weight: 600;
}

/* ================= ORDER BODY (ITEM + DETAILS) ================= */
.tc-order-body-grid {
    display: grid;
    grid-template-columns: 1.2fr 1fr;
    gap: 25px;
    align-items: center;
    margin-bottom: 22px;
}

.tc-order-product-card {
    display: flex;
    align-items: center;
    gap: 18px;
}

.tc-order-thumb-box {
    width: 90px;
    height: 90px;
    background: #111;
    border: 1px solid var(--tc-border-gold);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    flex-shrink: 0;
}

.tc-order-thumb {
    width: 85%;
    height: 85%;
    object-fit: contain;
    transition: transform 0.3s;
}

.tc-order-card:hover .tc-order-thumb {
    transform: scale(1.08);
}

.tc-order-product-info {
    flex: 1;
    min-width: 0;
}

.tc-order-brand {
    font-size: 11px;
    font-weight: 700;
    color: var(--tc-gold);
    text-transform: uppercase;
    letter-spacing: 1px;
}

.tc-order-name {
    font-size: 17px;
    font-weight: 800;
    color: #ffffff;
    margin: 3px 0 6px 0;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.tc-order-name a {
    color: #ffffff;
    text-decoration: none;
    transition: 0.2s;
}

.tc-order-name a:hover {
    color: var(--tc-gold);
}

.tc-order-pricing-meta {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 13px;
    color: var(--tc-text-muted);
}

.tc-order-pricing-meta strong {
    color: #fff;
}

/* SHIPPING & PAYMENT MINI-GRID */
.tc-order-meta-info {
    display: flex;
    flex-direction: column;
    gap: 8px;
    background: var(--tc-card-inner);
    border: 1px solid var(--tc-border-subtle);
    border-radius: 12px;
    padding: 14px 18px;
    font-size: 13px;
}

.tc-meta-row {
    display: flex;
    justify-content: space-between;
    gap: 10px;
}

.tc-meta-row .lbl {
    color: var(--tc-text-muted);
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 6px;
}

.tc-meta-row .val {
    color: #fff;
    font-weight: 700;
    text-align: right;
    max-width: 60%;
    word-break: break-word;
}

/* CARD FOOTER & ACTIONS */
.tc-order-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-top: 1px solid var(--tc-border-subtle);
    padding-top: 18px;
    flex-wrap: wrap;
    gap: 15px;
}

.tc-order-total-block {
    display: flex;
    align-items: center;
    gap: 10px;
}

.tc-order-total-block .lbl {
    font-size: 14px;
    color: var(--tc-text-muted);
    font-weight: 600;
}

.tc-order-total-block .val {
    font-size: 22px;
    font-weight: 900;
    color: var(--tc-gold);
}

.tc-order-action-buttons {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
}

.tc-btn-invoice {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: rgba(212, 175, 55, 0.15);
    border: 1px solid var(--tc-border-gold);
    color: var(--tc-gold) !important;
    font-size: 13px;
    font-weight: 700;
    padding: 8px 16px;
    border-radius: 8px;
    text-decoration: none !important;
    transition: all 0.2s ease;
}

.tc-btn-invoice:hover {
    background: var(--tc-gold);
    color: #0b0b0b !important;
}

.tc-btn-cancel-order {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: none;
    border: 1px solid rgba(231, 76, 60, 0.4);
    color: #ff6b6b;
    font-size: 13px;
    font-weight: 700;
    padding: 8px 16px;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.2s ease;
}

.tc-btn-cancel-order:hover {
    background: rgba(231, 76, 60, 0.15);
    color: #ff4d4f;
    border-color: #ff4d4f;
}

.tc-btn-shop-again {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: #1c1c1c;
    border: 1px solid var(--tc-border-subtle);
    color: #ffffff !important;
    font-size: 13px;
    font-weight: 700;
    padding: 8px 16px;
    border-radius: 8px;
    text-decoration: none !important;
    transition: all 0.2s ease;
}

.tc-btn-shop-again:hover {
    border-color: var(--tc-gold);
    color: var(--tc-gold) !important;
}

/* EMPTY STATE */
.tc-empty-orders-card {
    max-width: 580px;
    margin: 40px auto;
    background: #141414;
    border: 1px solid var(--tc-border-gold);
    border-radius: 20px;
    padding: 60px 30px;
    text-align: center;
    box-shadow: 0 12px 35px rgba(0, 0, 0, 0.5);
}

.tc-empty-orders-icon {
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

.tc-empty-orders-card h3 {
    font-size: 26px;
    font-weight: 800;
    color: #ffffff;
    margin-bottom: 10px;
}

.tc-empty-orders-card p {
    color: var(--tc-text-muted);
    font-size: 15px;
    margin-bottom: 25px;
}

.tc-btn-start-shopping {
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

.tc-btn-start-shopping:hover {
    background: linear-gradient(135deg, #ffc107 0%, #d4af37 100%);
    transform: translateY(-3px);
}

/* RESPONSIVE */
@media (max-width: 992px) {
    .tc-order-body-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 650px) {
    .tc-orders-title {
        font-size: 26px;
    }
    .tc-order-card {
        padding: 20px 16px;
    }
    .tc-progress-milestones {
        grid-template-columns: repeat(5, minmax(65px, 1fr));
        overflow-x: auto;
        padding-bottom: 10px;
    }
    .tc-order-product-card {
        flex-direction: column;
        align-items: flex-start;
    }
    .tc-order-footer {
        flex-direction: column;
        align-items: flex-start;
    }
    .tc-order-action-buttons {
        width: 100%;
        justify-content: flex-start;
    }
}
</style>

<!-- =====================================================
     MY ORDERS HTML
===================================================== -->
<section class="tc-myorders-section">
    <div class="container">

        <!-- ================= HERO HEADER ================= -->
        <div class="tc-orders-hero">
            <div class="tc-orders-badge">
            </div>
            <h1 class="tc-orders-title">
                My <span>Orders</span>
            </h1>
            <p class="tc-orders-desc">
                Track live delivery progress, download official tax invoices, and manage your luxury timepiece orders.
            </p>
        </div>

        <!-- ================= USER FLASH MESSAGE ================= -->
        <?php if (!empty($user_msg)) { ?>
            <div class="tc-user-alert <?php echo htmlspecialchars($user_msg_type); ?>">
                <i class="fa-solid <?php echo $user_msg_type === 'success' ? 'fa-circle-check' : 'fa-circle-exclamation'; ?>"></i>
                <span><?php echo htmlspecialchars($user_msg); ?></span>
            </div>
        <?php } ?>

        <!-- ================= TAB NAVIGATION ================= -->
        <div class="tc-tabs-wrap">
            <a href="my_order.php?tab=all" class="tc-tab-btn <?php echo $filter_tab === 'all' ? 'active' : ''; ?>">
                All Orders <span class="tc-tab-count"><?php echo $counts['all']; ?></span>
            </a>
            <a href="my_order.php?tab=active" class="tc-tab-btn <?php echo $filter_tab === 'active' ? 'active' : ''; ?>">
                <i class="fa-solid fa-truck-fast" style="color: #3498db;"></i> In Transit / Active <span class="tc-tab-count"><?php echo $counts['active']; ?></span>
            </a>
            <a href="my_order.php?tab=delivered" class="tc-tab-btn <?php echo $filter_tab === 'delivered' ? 'active' : ''; ?>">
                <i class="fa-solid fa-circle-check" style="color: #2ecc71;"></i> Delivered <span class="tc-tab-count"><?php echo $counts['delivered']; ?></span>
            </a>
            <a href="my_order.php?tab=cancelled" class="tc-tab-btn <?php echo $filter_tab === 'cancelled' ? 'active' : ''; ?>">
                <i class="fa-solid fa-ban" style="color: #e74c3c;"></i> Cancelled <span class="tc-tab-count"><?php echo $counts['cancelled']; ?></span>
            </a>
        </div>

        <!-- ================= ORDERS LIST ================= -->
        <?php if (!empty($orders_list)) { ?>

            <div class="tc-orders-list">
                <?php foreach ($orders_list as $ord) { 
                    $ord_id = intval($ord['id']);
                    $p_id = intval($ord['product_id'] ?? 0);
                    $p_name = $ord['product_name'] ?? 'Luxury Watch';
                    $p_brand = $ord['product_brand'] ?? 'TimeCraft';
                    $p_img = trim($ord['product_image'] ?? '');
                    $p_price = floatval($ord['product_price'] ?? 0);
                    $p_qty = intval($ord['quantity'] ?? 1);
                    $p_total = floatval($ord['total_price'] ?? ($p_price * $p_qty));
                    $p_address = $ord['address'] ?? 'N/A';
                    $p_payment = $ord['payment_method'] ?? 'Cash On Delivery';
                    $p_status = trim($ord['order_status'] ?? 'Pending');
                    $st_clean = strtolower($p_status);
                    
                    $ord_date_formatted = !empty($ord['order_date']) ? date("d M Y, h:i A", strtotime($ord['order_date'])) : 'Recently';

                    // Delivery Stage Evaluation
                    // 1: Pending (Order Placed)
                    // 2: Confirmed (Confirmed)
                    // 3: Packed & Quality Inspected
                    // 4: Shipped (Dispatched / On Route)
                    // 5: Delivered (At Doorstep)
                    $stage = 1;
                    $location_text = "Order Placed & Queued";

                    if ($st_clean === 'confirmed') {
                        $stage = 2;
                        $location_text = "Confirmed • Packaging in Progress";
                    } elseif ($st_clean === 'shipped') {
                        $stage = 4;
                        $location_text = "Dispatched • In Transit with Courier";
                    } elseif ($st_clean === 'delivered') {
                        $stage = 5;
                        $location_text = "Delivered to Doorstep";
                    } elseif ($st_clean === 'cancelled') {
                        $stage = 0;
                        $location_text = "Order Cancelled";
                    }
                ?>
                    <div class="tc-order-card">

                        <!-- TOP BAR: ID, DATE, STATUS -->
                        <div class="tc-order-topbar">
                            <div class="tc-order-ref-group">
                                <span class="tc-order-id-badge">
                                    <i class="fa-solid fa-receipt"></i> #TC-<?php echo $ord_id; ?>
                                </span>
                                <span class="tc-order-date-text">
                                    <i class="fa-regular fa-calendar" style="margin-right: 4px;"></i> <?php echo htmlspecialchars($ord_date_formatted); ?>
                                </span>
                            </div>

                            <span class="tc-order-status-pill <?php echo $st_clean; ?>">
                                <i class="fa-solid fa-circle" style="font-size: 7px;"></i>
                                <?php echo htmlspecialchars($p_status); ?>
                            </span>
                        </div>

                        <!-- LIVE DELIVERY TRACKER SECTION -->
                        <div class="tc-delivery-tracker-box">
                            <div class="tc-tracker-header">
                                <span class="tc-tracker-title">
                                    <i class="fa-solid fa-route"></i> Live Delivery Progress
                                </span>
                                <span class="tc-tracker-current-location">
                                    <i class="fa-solid fa-location-crosshairs" style="color: var(--tc-gold); margin-right: 4px;"></i>
                                    <?php echo htmlspecialchars($location_text); ?>
                                </span>
                            </div>

                            <?php if ($st_clean === 'cancelled') { ?>
                                <div class="tc-cancelled-timeline">
                                    <i class="fa-solid fa-ban" style="font-size: 20px;"></i>
                                    <div>
                                        <strong>This order was cancelled.</strong>
                                        <div>No further delivery actions will be performed. If you need this item, please place a new order.</div>
                                    </div>
                                </div>
                            <?php } else { ?>
                                <div class="tc-progress-milestones">
                                    <!-- STEP 1: PLACED -->
                                    <div class="tc-milestone-step <?php echo $stage >= 1 ? ($stage > 1 ? 'done' : 'active') : ''; ?>">
                                        <div class="tc-milestone-icon">
                                            <i class="fa-solid fa-cart-shopping"></i>
                                        </div>
                                        <span class="tc-milestone-label">Placed</span>
                                        <span class="tc-milestone-sub">Received</span>
                                    </div>

                                    <!-- STEP 2: CONFIRMED -->
                                    <div class="tc-milestone-step <?php echo $stage >= 2 ? ($stage > 2 ? 'done' : 'active') : ''; ?>">
                                        <div class="tc-milestone-icon">
                                            <i class="fa-solid fa-circle-check"></i>
                                        </div>
                                        <span class="tc-milestone-label">Confirmed</span>
                                        <span class="tc-milestone-sub">Verified</span>
                                    </div>

                                    <!-- STEP 3: QUALITY & PACKED -->
                                    <div class="tc-milestone-step <?php echo $stage >= 4 ? ($stage > 4 ? 'done' : 'done') : ($stage == 2 ? 'active' : ''); ?>">
                                        <div class="tc-milestone-icon">
                                            <i class="fa-solid fa-box-open"></i>
                                        </div>
                                        <span class="tc-milestone-label">Packed</span>
                                        <span class="tc-milestone-sub">Quality Check</span>
                                    </div>

                                    <!-- STEP 4: DISPATCHED / IN TRANSIT -->
                                    <div class="tc-milestone-step <?php echo $stage >= 4 ? ($stage > 4 ? 'done' : 'active') : ''; ?>">
                                        <div class="tc-milestone-icon">
                                            <i class="fa-solid fa-truck-fast"></i>
                                        </div>
                                        <span class="tc-milestone-label">Dispatched</span>
                                        <span class="tc-milestone-sub">On Route</span>
                                    </div>

                                    <!-- STEP 5: DELIVERED -->
                                    <div class="tc-milestone-step <?php echo $stage === 5 ? 'done' : ''; ?>">
                                        <div class="tc-milestone-icon">
                                            <i class="fa-solid fa-hand-holding-heart"></i>
                                        </div>
                                        <span class="tc-milestone-label">Delivered</span>
                                        <span class="tc-milestone-sub">Doorstep</span>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>

                        <!-- BODY: PRODUCT DETAILS + SHIPPING/PAYMENT -->
                        <div class="tc-order-body-grid">
                            <!-- PRODUCT INFO -->
                            <div class="tc-order-product-card">
                                <div class="tc-order-thumb-box">
                                    <img 
                                        src="upload/<?php echo htmlspecialchars($p_img); ?>" 
                                        alt="<?php echo htmlspecialchars($p_name); ?>"
                                        class="tc-order-thumb"
                                        onerror="this.onerror=null; this.src='assets/img/logo/logo.png';"
                                    >
                                </div>
                                <div class="tc-order-product-info">
                                    <span class="tc-order-brand"><?php echo htmlspecialchars($p_brand); ?></span>
                                    <h3 class="tc-order-name" title="<?php echo htmlspecialchars($p_name); ?>">
                                        <?php if ($p_id > 0) { ?>
                                            <a href="product_details.php?id=<?php echo $p_id; ?>"><?php echo htmlspecialchars($p_name); ?></a>
                                        <?php } else { ?>
                                            <?php echo htmlspecialchars($p_name); ?>
                                        <?php } ?>
                                    </h3>
                                    <div class="tc-order-pricing-meta">
                                        <span>Unit: <strong>₹<?php echo number_format($p_price); ?></strong></span>
                                        <span>&bull;</span>
                                        <span>Qty: <strong><?php echo $p_qty; ?></strong></span>
                                    </div>
                                </div>
                            </div>

                            <!-- SHIPPING & PAYMENT DETAILS -->
                            <div class="tc-order-meta-info">
                                <div class="tc-meta-row">
                                    <span class="lbl"><i class="fa-solid fa-money-bill-wave"></i> Payment</span>
                                    <span class="val"><?php echo htmlspecialchars($p_payment); ?></span>
                                </div>
                                <div class="tc-meta-row">
                                    <span class="lbl"><i class="fa-solid fa-location-dot"></i> Deliver To</span>
                                    <span class="val" title="<?php echo htmlspecialchars($p_address); ?>">
                                        <?php echo htmlspecialchars(mb_strimwidth($p_address, 0, 45, '...')); ?>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- FOOTER: TOTAL + ACTION BUTTONS -->
                        <div class="tc-order-footer">
                            <div class="tc-order-total-block">
                                <span class="lbl">Grand Total:</span>
                                <span class="val">₹<?php echo number_format($p_total); ?></span>
                            </div>

                            <div class="tc-order-action-buttons">
                                <!-- DOWNLOAD INVOICE BUTTON -->
                                <a href="invoice.php?id=<?php echo $ord_id; ?>" target="_blank" class="tc-btn-invoice">
                                    <i class="fa-solid fa-file-invoice"></i> Tax Invoice
                                </a>

                                <!-- CANCEL BUTTON (ONLY IF PENDING) -->
                                <?php if ($st_clean === 'pending') { ?>
                                    <form method="POST" action="my_order.php" style="display:inline;" onsubmit="return confirm('Are you sure you want to cancel Order #TC-<?php echo $ord_id; ?>?');">
                                        <input type="hidden" name="cancel_order" value="1">
                                        <input type="hidden" name="order_id" value="<?php echo $ord_id; ?>">
                                        <button type="submit" class="tc-btn-cancel-order">
                                            <i class="fa-solid fa-xmark"></i> Cancel Order
                                        </button>
                                    </form>
                                <?php } ?>

                                <!-- VIEW / SHOP PRODUCT AGAIN -->
                                <?php if ($p_id > 0) { ?>
                                    <a href="product_details.php?id=<?php echo $p_id; ?>" class="tc-btn-shop-again">
                                        <i class="fa-solid fa-rotate-right"></i> Buy Again
                                    </a>
                                <?php } else { ?>
                                    <a href="brand.php" class="tc-btn-shop-again">
                                        <i class="fa-solid fa-compass"></i> Explore
                                    </a>
                                <?php } ?>
                            </div>
                        </div>

                    </div>
                <?php } ?>
            </div>

        <?php } else { ?>

            <!-- EMPTY ORDERS STATE -->
            <div class="tc-empty-orders-card">
                <div class="tc-empty-orders-icon">
                    <i class="fa-solid fa-box-open"></i>
                </div>
                <h3>No Orders Found</h3>
                <p>
                    <?php if ($filter_tab !== 'all') { ?>
                        You have no <?php echo htmlspecialchars($filter_tab); ?> orders.
                    <?php } else { ?>
                        You haven't placed any watch orders yet. Discover our luxury timepiece collections and place your first order.
                    <?php } ?>
                </p>
                <a href="brand.php" class="tc-btn-start-shopping">
                    <i class="fa-solid fa-cart-shopping"></i> Start Shopping
                </a>
            </div>

        <?php } ?>

    </div>
</section>

<?php
include("footer.php");
?>