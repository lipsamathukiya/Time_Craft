<?php
session_start();

include(__DIR__ . "/conn.php");

date_default_timezone_set("Asia/Kolkata");
mysqli_query($conn, "SET time_zone = '+05:30'");

/* =====================================================
   ADMIN LOGIN CHECK
===================================================== */
require_once(__DIR__ . "/auth_check.php");

$message = $_SESSION['admin_msg'] ?? '';
$message_type = $_SESSION['admin_msg_type'] ?? '';
unset($_SESSION['admin_msg'], $_SESSION['admin_msg_type']);

/* =====================================================
   ACTION: QUICK STATUS UPDATE
===================================================== */
if (isset($_POST['quick_status_update']) && $_POST['quick_status_update'] == "1") {
    $order_id = intval($_POST['order_id'] ?? 0);
    $new_status = trim($_POST['order_status'] ?? '');

    $allowed_statuses = ['Pending', 'Confirmed', 'Shipped', 'Delivered', 'Cancelled'];

    if ($order_id > 0 && in_array($new_status, $allowed_statuses)) {
        $stmt = mysqli_prepare($conn, "UPDATE `order` SET order_status = ? WHERE id = ? LIMIT 1");
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "si", $new_status, $order_id);
            if (mysqli_stmt_execute($stmt)) {
                $_SESSION['admin_msg'] = "Order #TC-{$order_id} status updated to '{$new_status}'.";
                $_SESSION['admin_msg_type'] = "success";
            } else {
                $_SESSION['admin_msg'] = "Failed to update status: " . mysqli_stmt_error($stmt);
                $_SESSION['admin_msg_type'] = "error";
            }
            mysqli_stmt_close($stmt);
        }
    } else {
        $_SESSION['admin_msg'] = "Invalid order ID or status provided.";
        $_SESSION['admin_msg_type'] = "error";
    }

    header("Location: order.php");
    exit();
}

/* =====================================================
   ACTION: FULL ORDER UPDATE
===================================================== */
if (isset($_POST['update_order']) && $_POST['update_order'] == "1") {
    $update_id = intval($_POST['order_id'] ?? 0);
    $customer_name = trim($_POST['customer_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $product_name = trim($_POST['product_name'] ?? '');
    $product_price = floatval($_POST['product_price'] ?? 0);
    $quantity = intval($_POST['quantity'] ?? 1);
    $total_price = floatval($_POST['total_price'] ?? 0);
    $payment_method = trim($_POST['payment_method'] ?? 'Cash On Delivery');
    $order_status = trim($_POST['order_status'] ?? 'Pending');
    $address = trim($_POST['address'] ?? '');

    if ($total_price <= 0 && $product_price > 0 && $quantity > 0) {
        $total_price = $product_price * $quantity;
    }

    if ($update_id <= 0 || empty($product_name) || empty($email)) {
        $_SESSION['admin_msg'] = "Please fill all required fields correctly.";
        $_SESSION['admin_msg_type'] = "error";
    } else {
        $stmt = mysqli_prepare($conn, "
            UPDATE `order`
            SET 
                customer_name = ?,
                email = ?,
                phone = ?,
                product_name = ?,
                product_price = ?,
                quantity = ?,
                total_price = ?,
                payment_method = ?,
                order_status = ?,
                address = ?
            WHERE id = ?
            LIMIT 1
        ");

        if ($stmt) {
            mysqli_stmt_bind_param(
                $stmt,
                "ssssdidsssi",
                $customer_name,
                $email,
                $phone,
                $product_name,
                $product_price,
                $quantity,
                $total_price,
                $payment_method,
                $order_status,
                $address,
                $update_id
            );

            if (mysqli_stmt_execute($stmt)) {
                $_SESSION['admin_msg'] = "Order #TC-{$update_id} updated successfully!";
                $_SESSION['admin_msg_type'] = "success";
            } else {
                $_SESSION['admin_msg'] = "Update failed: " . mysqli_stmt_error($stmt);
                $_SESSION['admin_msg_type'] = "error";
            }
            mysqli_stmt_close($stmt);
        }
    }

    header("Location: order.php");
    exit();
}

/* =====================================================
   ACTION: DELETE ORDER
===================================================== */
if (isset($_POST['delete_order']) && $_POST['delete_order'] == "1") {
    $delete_id = intval($_POST['order_id'] ?? 0);

    if ($delete_id > 0) {
        $stmt = mysqli_prepare($conn, "UPDATE `order` SET admin_deleted = 1 WHERE id = ? LIMIT 1");
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "i", $delete_id);
            if (mysqli_stmt_execute($stmt)) {
                $_SESSION['admin_msg'] = "Order #TC-{$delete_id} deleted from admin panel (retained on customer side).";
                $_SESSION['admin_msg_type'] = "success";
            } else {
                $_SESSION['admin_msg'] = "Delete failed: " . mysqli_stmt_error($stmt);
                $_SESSION['admin_msg_type'] = "error";
            }
            mysqli_stmt_close($stmt);
        }
    } else {
        $_SESSION['admin_msg'] = "Invalid order ID.";
        $_SESSION['admin_msg_type'] = "error";
    }

    header("Location: order.php");
    exit();
}

/* =====================================================
   FETCH STATS
===================================================== */
$stat_total_orders = 0;
$stat_total_revenue = 0;
$stat_pending = 0;
$stat_confirmed = 0;
$stat_shipped = 0;
$stat_delivered = 0;
$stat_cancelled = 0;

$count_res = mysqli_query($conn, "
    SELECT 
        order_status,
        COUNT(*) AS count_val,
        SUM(total_price) AS sum_val
    FROM `order`
    WHERE COALESCE(admin_deleted, 0) = 0
    GROUP BY order_status
");

if ($count_res) {
    while ($row = mysqli_fetch_assoc($count_res)) {
        $st = strtolower(trim($row['order_status']));
        $cnt = intval($row['count_val']);
        $sum = floatval($row['sum_val']);

        $stat_total_orders += $cnt;
        $stat_total_revenue += $sum;

        if ($st == 'pending') $stat_pending += $cnt;
        elseif ($st == 'confirmed') $stat_confirmed += $cnt;
        elseif ($st == 'shipped') $stat_shipped += $cnt;
        elseif ($st == 'delivered') $stat_delivered += $cnt;
        elseif ($st == 'cancelled') $stat_cancelled += $cnt;
    }
}

/* =====================================================
   FETCH ALL ORDERS FOR TABLE
===================================================== */
$order_query = "
    SELECT 
        o.*,
        p.image AS product_image,
        p.brand AS product_brand
    FROM `order` AS o
    LEFT JOIN product AS p 
        ON TRIM(LOWER(p.product_name)) = TRIM(LOWER(o.product_name))
    WHERE COALESCE(o.admin_deleted, 0) = 0
    ORDER BY o.id DESC
";

$order_result = mysqli_query($conn, $order_query);
$orders_list = [];
if ($order_result) {
    while ($row = mysqli_fetch_assoc($order_result)) {
        $orders_list[] = $row;
    }
}

/* Check if specific order is opened for editing */
$edit_order = null;
if (isset($_GET['edit']) && intval($_GET['edit']) > 0) {
    $edit_id = intval($_GET['edit']);
    foreach ($orders_list as $o_item) {
        if (intval($o_item['id']) === $edit_id) {
            $edit_order = $o_item;
            break;
        }
    }
    if (!$edit_order) {
        $res_e = mysqli_query($conn, "SELECT * FROM `order` WHERE id = '$edit_id' AND COALESCE(admin_deleted, 0) = 0 LIMIT 1");
        if ($res_e && mysqli_num_rows($res_e) > 0) {
            $edit_order = mysqli_fetch_assoc($res_e);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Total Orders Management - Time Craft Admin</title>
    <link rel="icon" type="image/png" href="../assets/img/logo/icon.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="admin.css">
    <style>

        :root {
            --tc-gold: #d4af37;
            --tc-gold-light: #f3e5ab;
            --tc-gold-dark: #b89127;
            --tc-gold-hover: #ffc107;
            --tc-dark-1: #0a0a0a;
            --tc-dark-2: #121212;
            --tc-dark-3: #1a1a1a;
            --tc-card-bg: #141414;
            --tc-card-border: rgba(212, 175, 55, 0.22);
            --tc-border-subtle: rgba(255, 255, 255, 0.08);
            --tc-text: #ffffff;
            --tc-text-muted: #a0a0a0;
            --tc-pending: #f39c12;
            --tc-confirmed: #3498db;
            --tc-shipped: #9b59b6;
            --tc-delivered: #2ecc71;
            --tc-cancelled: #e74c3c;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, Helvetica, sans-serif;
        }

        html, body {
            background: #0d0d0d;
            color: var(--tc-text);
            min-height: 100vh;
        }

        /* ================= FIXED SIDEBAR ================= */
        .sidebar {
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            width: 250px;
            height: 100vh;
            background: #111111;
            border-right: 1px solid rgba(212, 175, 55, 0.25);
            padding: 25px 18px;
            z-index: 9999;
            overflow-y: auto;
            transition: all 0.3s ease;
        }

        .sidebar h2 {
            color: var(--tc-gold);
            text-align: center;
            font-size: 24px;
            font-weight: 800;
            margin-bottom: 30px;
            letter-spacing: 1px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .sidebar ul {
            list-style: none;
        }

        .sidebar ul li {
            margin-bottom: 8px;
            position: relative;
        }

        .sidebar ul li a {
            display: flex;
            align-items: center;
            gap: 12px;
            color: #ccc;
            text-decoration: none;
            padding: 12px 16px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.25s ease;
        }

        .sidebar ul li a i {
            width: 20px;
            font-size: 16px;
            color: #888;
            transition: 0.25s;
        }

        .sidebar ul li a:hover,
        .sidebar ul li.active > a {
            background: rgba(212, 175, 55, 0.15);
            color: var(--tc-gold);
            border: 1px solid rgba(212, 175, 55, 0.3);
        }

        .sidebar ul li a:hover i,
        .sidebar ul li.active > a i {
            color: var(--tc-gold);
        }

        .sidebar .dropdown-menu {
            display: none;
            list-style: none;
            padding-left: 28px;
            margin-top: 4px;
        }

        .sidebar .dropdown-menu li a {
            padding: 8px 12px;
            font-size: 13px;
        }

        .sidebar .dropdown.open .dropdown-menu {
            display: block;
        }

        .sidebar .arrow {
            margin-left: auto;
            font-size: 11px;
            transition: transform 0.25s;
        }

        .sidebar .dropdown.open .arrow {
            transform: rotate(180deg);
        }

        /* ================= MAIN CONTENT AREA ================= */
        .admin-main {
            margin-left: 250px;
            width: calc(100% - 250px);
            padding: 22px 18px 60px 18px;
            transition: all 0.3s ease;
        }

        /* TOP BAR */
        .admin-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            flex-wrap: wrap;
            gap: 15px;
        }

        .admin-title h1 {
            font-size: 28px;
            font-weight: 800;
            color: #ffffff;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .admin-title h1 span {
            color: var(--tc-gold);
        }

        .admin-title p {
            color: var(--tc-text-muted);
            font-size: 14px;
            margin-top: 4px;
        }

        .admin-header-actions {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        /* NOTIFICATION MESSAGE */
        .tc-alert {
            padding: 14px 20px;
            border-radius: 10px;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 14px;
            font-weight: 600;
            animation: fadeIn 0.3s ease;
        }

        .tc-alert.success {
            background: rgba(46, 204, 113, 0.15);
            border: 1px solid rgba(46, 204, 113, 0.5);
            color: #2ecc71;
        }

        .tc-alert.error {
            background: rgba(231, 76, 60, 0.15);
            border: 1px solid rgba(231, 76, 60, 0.5);
            color: #e74c3c;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-5px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* ================= STATISTICS GRID ================= */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(6, 1fr);
            gap: 16px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: var(--tc-card-bg);
            border: 1px solid var(--tc-card-border);
            border-radius: 14px;
            padding: 18px 16px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.4);
            display: flex;
            flex-direction: column;
            gap: 8px;
            position: relative;
            overflow: hidden;
            transition: transform 0.25s, border-color 0.25s;
        }

        .stat-card:hover {
            transform: translateY(-3px);
            border-color: var(--tc-gold);
        }

        .stat-card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .stat-card-title {
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: var(--tc-text-muted);
        }

        .stat-card-icon {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
        }

        .stat-card.total .stat-card-icon { background: rgba(212, 175, 55, 0.15); color: var(--tc-gold); }
        .stat-card.revenue .stat-card-icon { background: rgba(46, 204, 113, 0.15); color: #2ecc71; }
        .stat-card.pending .stat-card-icon { background: rgba(243, 156, 18, 0.15); color: #f39c12; }
        .stat-card.confirmed .stat-card-icon { background: rgba(52, 152, 219, 0.15); color: #3498db; }
        .stat-card.shipped .stat-card-icon { background: rgba(155, 89, 182, 0.15); color: #9b59b6; }
        .stat-card.delivered .stat-card-icon { background: rgba(46, 204, 113, 0.15); color: #2ecc71; }

        .stat-card-value {
            font-size: 24px;
            font-weight: 800;
            color: #ffffff;
        }

        .stat-card.total .stat-card-value { color: var(--tc-gold); }
        .stat-card.revenue .stat-card-value { color: #2ecc71; font-size: 20px; }

        /* ================= EDIT ORDER INLINE PANEL ================= */
        .edit-panel {
            background: #181818;
            border: 2px solid var(--tc-gold);
            border-radius: 14px;
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: 0 10px 35px rgba(212, 175, 55, 0.15);
        }

        .edit-panel-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid rgba(212, 175, 55, 0.2);
            padding-bottom: 15px;
            margin-bottom: 20px;
        }

        .edit-panel-title {
            font-size: 18px;
            font-weight: 800;
            color: var(--tc-gold);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .edit-form-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
        }

        .edit-form-group.full {
            grid-column: span 3;
        }

        .edit-form-group label {
            display: block;
            font-size: 12px;
            font-weight: 700;
            color: #ccc;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 6px;
        }

        .edit-form-input, .edit-form-select, .edit-form-textarea {
            width: 100%;
            background: #111;
            border: 1px solid #333;
            border-radius: 7px;
            padding: 10px 12px;
            color: #ffffff;
            font-size: 13px;
            outline: none;
            transition: border-color 0.2s;
        }

        .edit-form-input:focus, .edit-form-select:focus, .edit-form-textarea:focus {
            border-color: var(--tc-gold);
        }

        .edit-form-textarea {
            min-height: 80px;
            resize: vertical;
        }

        .edit-form-actions {
            display: flex;
            gap: 12px;
            margin-top: 20px;
        }

        .btn-filter-submit {
            background: var(--tc-gold);
            color: #0b0b0b;
            font-weight: 700;
            font-size: 13px;
            padding: 10px 18px;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: 0.2s;
        }

        .btn-filter-submit:hover {
            background: #ffc107;
            transform: translateY(-1px);
        }

        .btn-filter-reset {
            background: #252525;
            color: #ccc;
            font-weight: 600;
            font-size: 13px;
            padding: 10px 14px;
            border-radius: 8px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: 0.2s;
        }

        .btn-filter-reset:hover {
            background: #333;
            color: #fff;
        }

        /* ================= ORDERS DATA TABLE ================= */
        .table-card {
            background: var(--tc-card-bg);
            border: 1px solid var(--tc-card-border);
            border-radius: 14px;
            overflow: hidden;
            box-shadow: 0 10px 35px rgba(0, 0, 0, 0.5);
        }

        .table-card-header {
            padding: 18px 24px;
            background: #181818;
            border-bottom: 1px solid var(--tc-card-border);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
        }

        .table-card-title {
            font-size: 17px;
            font-weight: 800;
            color: #ffffff;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .table-card-title span {
            color: var(--tc-gold);
        }

        .btn-table-action {
            background: #202020;
            color: #ccc;
            border: 1px solid rgba(255, 255, 255, 0.1);
            padding: 7px 14px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 700;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            cursor: pointer;
            transition: 0.2s;
        }

        .btn-table-action:hover {
            background: var(--tc-gold);
            color: #0b0b0b;
            border-color: var(--tc-gold);
        }

        .table-responsive {
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: thin;
            scrollbar-color: var(--tc-gold) #1a1a1a;
            padding-bottom: 8px;
        }

        .table-responsive::-webkit-scrollbar {
            height: 8px;
        }

        .table-responsive::-webkit-scrollbar-track {
            background: #141414;
            border-radius: 4px;
        }

        .table-responsive::-webkit-scrollbar-thumb {
            background: var(--tc-gold);
            border-radius: 4px;
        }

        .table-responsive::-webkit-scrollbar-thumb:hover {
            background: #e6c34e;
        }

        table.tc-table {
            width: 100%;
            min-width: 1150px;
            border-collapse: collapse;
            text-align: left;
            font-size: 12px;
        }

        table.tc-table th:last-child,
        table.tc-table td:last-child {
            text-align: center;
            white-space: nowrap;
        }

        table.tc-table th {
            background: #161616;
            color: var(--tc-gold);
            padding: 10px 10px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-size: 10px;
            border-bottom: 1px solid rgba(212, 175, 55, 0.2);
            white-space: nowrap;
        }

        table.tc-table td {
            padding: 10px 10px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            vertical-align: middle;
            color: #ddd;
        }

        table.tc-table tbody tr {
            transition: background 0.2s;
        }

        table.tc-table tbody tr:hover {
            background: rgba(212, 175, 55, 0.04);
        }

        /* TABLE CELLS */
        .order-id-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(212, 175, 55, 0.12);
            border: 1px solid rgba(212, 175, 55, 0.3);
            color: var(--tc-gold);
            font-weight: 800;
            padding: 4px 8px;
            border-radius: 6px;
            font-size: 12px;
            white-space: nowrap;
        }

        .customer-cell {
            display: flex;
            flex-direction: column;
            gap: 2px;
            min-width: 120px;
        }

        .customer-name {
            font-weight: 700;
            color: #ffffff;
            font-size: 13px;
        }

        .customer-contact {
            font-size: 11px;
            color: var(--tc-text-muted);
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .customer-contact a {
            color: #999;
            text-decoration: none;
            transition: 0.2s;
        }

        .customer-contact a:hover {
            color: var(--tc-gold);
        }

        .product-cell {
            display: flex;
            align-items: center;
            gap: 8px;
            min-width: 150px;
        }

        .product-thumb-box {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            background: #202020;
            border: 1px solid rgba(212, 175, 55, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            flex-shrink: 0;
        }

        .product-thumb {
            width: 80%;
            height: 80%;
            object-fit: contain;
        }

        .product-title-wrap {
            display: flex;
            flex-direction: column;
        }

        .product-brand-tag {
            font-size: 10px;
            text-transform: uppercase;
            color: var(--tc-gold);
            font-weight: 700;
            letter-spacing: 0.5px;
        }

        .product-title-text {
            font-weight: 700;
            color: #ffffff;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 110px;
        }

        .price-text {
            font-weight: 700;
            color: #ffffff;
            white-space: nowrap;
        }

        .price-text.total {
            color: var(--tc-gold);
            font-weight: 800;
            font-size: 14px;
        }

        .qty-badge {
            background: #222;
            border: 1px solid #444;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 700;
            color: #fff;
        }

        .payment-badge {
            font-size: 11px;
            font-weight: 600;
            color: #bbb;
            background: #1a1a1a;
            padding: 4px 8px;
            border-radius: 6px;
            border: 1px solid rgba(255, 255, 255, 0.08);
            white-space: nowrap;
        }

        /* STATUS BADGE */
        .status-pill {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 4px 10px;
            border-radius: 20px;
            white-space: nowrap;
        }

        .status-pill.pending { background: rgba(243, 156, 18, 0.15); color: #f39c12; border: 1px solid rgba(243, 156, 18, 0.4); }
        .status-pill.confirmed { background: rgba(52, 152, 219, 0.15); color: #3498db; border: 1px solid rgba(52, 152, 219, 0.4); }
        .status-pill.shipped { background: rgba(155, 89, 182, 0.15); color: #9b59b6; border: 1px solid rgba(155, 89, 182, 0.4); }
        .status-pill.delivered { background: rgba(46, 204, 113, 0.15); color: #2ecc71; border: 1px solid rgba(46, 204, 113, 0.4); }
        .status-pill.cancelled { background: rgba(231, 76, 60, 0.15); color: #e74c3c; border: 1px solid rgba(231, 76, 60, 0.4); }

        /* QUICK STATUS SELECT */
        .quick-status-form {
            margin-top: 6px;
        }

        .quick-status-select {
            background: #111;
            border: 1px solid #444;
            color: #bbb;
            font-size: 10px;
            font-weight: 700;
            padding: 2px 6px;
            border-radius: 4px;
            cursor: pointer;
            outline: none;
            transition: 0.2s;
        }

        .quick-status-select:hover, .quick-status-select:focus {
            border-color: var(--tc-gold);
            color: #fff;
        }

        /* ACTIONS CELL */
        .actions-group {
            display: flex;
            align-items: center;
            gap: 6px;
            white-space: nowrap;
        }

        .btn-action {
            width: 32px;
            height: 32px;
            border-radius: 6px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            font-size: 13px;
            border: none;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-action.view {
            background: rgba(52, 152, 219, 0.15);
            color: #3498db;
            border: 1px solid rgba(52, 152, 219, 0.3);
        }

        .btn-action.view:hover {
            background: #3498db;
            color: #fff;
        }

        .btn-action.edit {
            background: rgba(212, 175, 55, 0.15);
            color: var(--tc-gold);
            border: 1px solid rgba(212, 175, 55, 0.3);
        }

        .btn-action.edit:hover {
            background: var(--tc-gold);
            color: #0b0b0b;
        }

        .btn-action.invoice {
            background: rgba(46, 204, 113, 0.15);
            color: #2ecc71;
            border: 1px solid rgba(46, 204, 113, 0.3);
        }

        .btn-action.invoice:hover {
            background: #2ecc71;
            color: #fff;
        }

        .btn-action.delete {
            background: rgba(231, 76, 60, 0.15);
            color: #e74c3c;
            border: 1px solid rgba(231, 76, 60, 0.3);
        }

        .btn-action.delete:hover {
            background: #e74c3c;
            color: #fff;
        }

        /* EMPTY STATE */
        .empty-orders {
            text-align: center;
            padding: 60px 20px;
        }

        .empty-orders i {
            font-size: 50px;
            color: #444;
            margin-bottom: 15px;
        }

        .empty-orders h3 {
            font-size: 20px;
            color: #fff;
            margin-bottom: 8px;
        }

        .empty-orders p {
            color: #888;
            font-size: 14px;
        }

        /* ================= MODAL STYLING ================= */
        .tc-modal-backdrop {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            z-index: 99999;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .tc-modal-backdrop.show {
            display: flex;
        }

        .tc-modal-card {
            background: #141414;
            border: 1px solid var(--tc-gold);
            border-radius: 16px;
            width: 100%;
            max-width: 600px;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 15px 50px rgba(0, 0, 0, 0.8);
            padding: 28px;
            position: relative;
            animation: modalPop 0.25s ease forwards;
        }

        @keyframes modalPop {
            from { transform: scale(0.92); opacity: 0; }
            to { transform: scale(1); opacity: 1; }
        }

        .tc-modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            padding-bottom: 15px;
            margin-bottom: 20px;
        }

        .tc-modal-title {
            font-size: 19px;
            font-weight: 800;
            color: #ffffff;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .tc-modal-title span {
            color: var(--tc-gold);
        }

        .tc-modal-close {
            background: none;
            border: none;
            color: #888;
            font-size: 20px;
            cursor: pointer;
            transition: 0.2s;
        }

        .tc-modal-close:hover {
            color: #fff;
        }

        .tc-modal-row {
            display: flex;
            justify-content: space-between;
            padding: 9px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            font-size: 13px;
        }

        .tc-modal-row .lbl {
            color: #888;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 11px;
        }

        .tc-modal-row .val {
            color: #fff;
            font-weight: 700;
            text-align: right;
            max-width: 65%;
        }

        .tc-modal-row .val.gold {
            color: var(--tc-gold);
        }

        /* ================= RESPONSIVE ================= */
        @media (max-width: 1200px) {
            .stats-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        @media (max-width: 992px) {
            .sidebar {
                width: 220px;
            }
            .admin-main {
                margin-left: 220px;
                width: calc(100% - 220px);
                padding: 20px;
            }
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            .sidebar {
                position: static !important;
                width: 100%;
                height: auto;
            }
            .admin-main {
                margin-left: 0;
                width: 100%;
                padding: 15px;
            }
            .stats-grid {
                grid-template-columns: 1fr;
            }
            .edit-form-grid {
                grid-template-columns: 1fr;
            }
            .edit-form-group.full {
                grid-column: auto;
            }
        }

        @media print {
            .sidebar, .admin-header-actions, .actions-group, .stats-grid, .quick-status-form {
                display: none !important;
            }
            .admin-main {
                margin-left: 0 !important;
                width: 100% !important;
                padding: 0 !important;
            }
            body {
                background: #fff;
                color: #000;
            }
            table.tc-table th, table.tc-table td {
                color: #000 !important;
                border: 1px solid #ccc !important;
            }
        }
    
    </style>
</head>
<body>

<?php $active_page = 'orders'; include 'admin_sidebar.php'; ?>

<div class="admin-main">

        <!-- HEADER TITLE -->
        <div class="admin-header">
            <div class="admin-title">
                <h1><i class="fa-solid fa-bag-shopping" style="color: var(--tc-gold);"></i> Total <span>Orders</span></h1>
                <p>Manage, track, edit, and fulfill all customer watch orders.</p>
            </div>
            <div class="admin-header-actions">
                <button onClick="window.print();" class="btn-table-action">
                    <i class="fa-solid fa-print"></i> Print Orders
                </button>
                <button onClick="exportTableToCSV('timecraft_orders.csv')" class="btn-table-action">
                    <i class="fa-solid fa-file-csv"></i> Export CSV
                </button>
            </div>
        </div>

        <!-- NOTIFICATIONS -->
        <?php if (!empty($message)) { ?>
            <div class="tc-alert <?php echo htmlspecialchars($message_type); ?>">
                <i class="fa-solid <?php echo $message_type === 'success' ? 'fa-circle-check' : 'fa-triangle-exclamation'; ?>"></i>
                <span><?php echo htmlspecialchars($message); ?></span>
            </div>
        <?php } ?>

        <!-- ================= STATS CARDS ================= -->
        <div class="stats-grid">
            <div class="stat-card total">
                <div class="stat-card-header">
                    <span class="stat-card-title">Total Orders</span>
                    <div class="stat-card-icon"><i class="fa-solid fa-cubes"></i></div>
                </div>
                <div class="stat-card-value"><?php echo number_format($stat_total_orders); ?></div>
            </div>

            <div class="stat-card revenue">
                <div class="stat-card-header">
                    <span class="stat-card-title">Total Value</span>
                    <div class="stat-card-icon"><i class="fa-solid fa-indian-rupee-sign"></i></div>
                </div>
                <div class="stat-card-value">₹<?php echo number_format($stat_total_revenue); ?></div>
            </div>

            <div class="stat-card pending">
                <div class="stat-card-header">
                    <span class="stat-card-title">Pending</span>
                    <div class="stat-card-icon"><i class="fa-solid fa-hourglass-half"></i></div>
                </div>
                <div class="stat-card-value"><?php echo number_format($stat_pending); ?></div>
            </div>

            <div class="stat-card confirmed">
                <div class="stat-card-header">
                    <span class="stat-card-title">Confirmed</span>
                    <div class="stat-card-icon"><i class="fa-solid fa-circle-check"></i></div>
                </div>
                <div class="stat-card-value"><?php echo number_format($stat_confirmed); ?></div>
            </div>

            <div class="stat-card shipped">
                <div class="stat-card-header">
                    <span class="stat-card-title">Shipped</span>
                    <div class="stat-card-icon"><i class="fa-solid fa-truck-fast"></i></div>
                </div>
                <div class="stat-card-value"><?php echo number_format($stat_shipped); ?></div>
            </div>

            <div class="stat-card delivered">
                <div class="stat-card-header">
                    <span class="stat-card-title">Delivered</span>
                    <div class="stat-card-icon"><i class="fa-solid fa-circle-check"></i></div>
                </div>
                <div class="stat-card-value"><?php echo number_format($stat_delivered); ?></div>
            </div>
        </div>

        <!-- ================= EDIT ORDER PANEL (IF EDIT REQUESTED) ================= -->
        <?php if ($edit_order) { ?>
            <div class="edit-panel" id="editOrderSection">
                <div class="edit-panel-header">
                    <div class="edit-panel-title">
                        <i class="fa-solid fa-pen-to-square"></i> Edit Order #TC-<?php echo htmlspecialchars($edit_order['id']); ?>
                    </div>
                    <a href="order.php" class="btn-table-action"><i class="fa-solid fa-xmark"></i> Close Edit</a>
                </div>

                <form method="POST" action="order.php">
                    <input type="hidden" name="update_order" value="1">
                    <input type="hidden" name="order_id" value="<?php echo htmlspecialchars($edit_order['id']); ?>">

                    <div class="edit-form-grid">
                        <div class="edit-form-group">
                            <label>Customer Name</label>
                            <input type="text" name="customer_name" class="edit-form-input" required value="<?php echo htmlspecialchars($edit_order['customer_name'] ?? ''); ?>">
                        </div>

                        <div class="edit-form-group">
                            <label>Email Address</label>
                            <input type="email" name="email" class="edit-form-input" required value="<?php echo htmlspecialchars($edit_order['email'] ?? ''); ?>">
                        </div>

                        <div class="edit-form-group">
                            <label>Phone Number</label>
                            <input type="text" name="phone" class="edit-form-input" required value="<?php echo htmlspecialchars($edit_order['phone'] ?? ''); ?>">
                        </div>

                        <div class="edit-form-group">
                            <label>Product Name</label>
                            <input type="text" name="product_name" class="edit-form-input" required value="<?php echo htmlspecialchars($edit_order['product_name'] ?? ''); ?>">
                        </div>

                        <div class="edit-form-group">
                            <label>Unit Price (₹)</label>
                            <input type="number" step="0.01" name="product_price" class="edit-form-input" required value="<?php echo htmlspecialchars($edit_order['product_price'] ?? 0); ?>" oninput="calculateEditTotal()">
                        </div>

                        <div class="edit-form-group">
                            <label>Quantity</label>
                            <input type="number" min="1" name="quantity" class="edit-form-input" required value="<?php echo htmlspecialchars($edit_order['quantity'] ?? 1); ?>" oninput="calculateEditTotal()">
                        </div>

                        <div class="edit-form-group">
                            <label>Total Price (₹)</label>
                            <input type="number" step="0.01" name="total_price" id="edit_total_price" class="edit-form-input" required value="<?php echo htmlspecialchars($edit_order['total_price'] ?? 0); ?>">
                        </div>

                        <div class="edit-form-group">
                            <label>Payment Method</label>
                            <select name="payment_method" class="edit-form-select">
                                <option value="Cash On Delivery" <?php echo ($edit_order['payment_method'] ?? '') === 'Cash On Delivery' ? 'selected' : ''; ?>>Cash On Delivery</option>
                                <option value="Online / Card" <?php echo ($edit_order['payment_method'] ?? '') === 'Online / Card' ? 'selected' : ''; ?>>Online / Card</option>
                            </select>
                        </div>

                        <div class="edit-form-group">
                            <label>Order Status</label>
                            <select name="order_status" class="edit-form-select">
                                <?php foreach (['Pending', 'Confirmed', 'Shipped', 'Delivered', 'Cancelled'] as $st_opt) { ?>
                                    <option value="<?php echo $st_opt; ?>" <?php echo strtolower($edit_order['order_status'] ?? '') === strtolower($st_opt) ? 'selected' : ''; ?>>
                                        <?php echo $st_opt; ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="edit-form-group full">
                            <label>Delivery Shipping Address</label>
                            <textarea name="address" class="edit-form-textarea" required><?php echo htmlspecialchars($edit_order['address'] ?? ''); ?></textarea>
                        </div>
                    </div>

                    <div class="edit-form-actions">
                        <button type="submit" class="btn-filter-submit"><i class="fa-solid fa-floppy-disk"></i> Save Changes</button>
                        <a href="order.php" class="btn-filter-reset"><i class="fa-solid fa-xmark"></i> Cancel</a>
                    </div>
                </form>
            </div>
        <?php } ?>

        <!-- ================= ORDERS TABLE ================= -->
        <div class="table-card">
            <div class="table-card-header">
                <div class="table-card-title">
                    <i class="fa-solid fa-list-check" style="color: var(--tc-gold);"></i> All Customer Orders (<span><?php echo count($orders_list); ?></span>)
                </div>
            </div>

            <div class="table-responsive">
                <?php if (!empty($orders_list)) { ?>
                    <table class="tc-table" id="ordersTable">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Customer Details</th>
                                <th>Product / Watch</th>
                                <th>Unit Price</th>
                                <th>Qty</th>
                                <th>Grand Total</th>
                                <th>Payment</th>
                                <th>Status & Quick Update</th>
                                <th>Order Date</th>
                                <th style="text-align: center;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orders_list as $ord) { 
                                $ord_id = intval($ord['id']);
                                $c_name = $ord['customer_name'] ?? 'Customer';
                                $c_email = $ord['email'] ?? 'N/A';
                                $c_phone = $ord['phone'] ?? 'N/A';
                                $c_address = $ord['address'] ?? '';
                                $p_name = $ord['product_name'] ?? 'TimeCraft Watch';
                                $p_brand = $ord['product_brand'] ?? 'TimeCraft';
                                $p_img = trim($ord['product_image'] ?? '');
                                $p_price = floatval($ord['product_price'] ?? 0);
                                $p_qty = intval($ord['quantity'] ?? 1);
                                $p_total = floatval($ord['total_price'] ?? 0);
                                $pm = $ord['payment_method'] ?? 'COD';
                                $st = $ord['order_status'] ?? 'Pending';
                                $st_lower = strtolower($st);
                                $ord_date = !empty($ord['order_date']) ? date("d M Y, h:i A", strtotime($ord['order_date'])) : 'N/A';
                            ?>
                                <tr>
                                    <!-- ORDER ID -->
                                    <td>
                                        <span class="order-id-badge">
                                            #TC-<?php echo $ord_id; ?>
                                        </span>
                                    </td>

                                    <!-- CUSTOMER INFO -->
                                    <td>
                                        <div class="customer-cell">
                                            <span class="customer-name"><?php echo htmlspecialchars($c_name); ?></span>
                                            <span class="customer-contact">
                                                <i class="fa-solid fa-phone" style="font-size: 10px; color: var(--tc-gold);"></i> 
                                                <a href="tel:<?php echo htmlspecialchars($c_phone); ?>"><?php echo htmlspecialchars($c_phone); ?></a>
                                            </span>
                                            <span class="customer-contact">
                                                <i class="fa-solid fa-envelope" style="font-size: 10px; color: #3498db;"></i> 
                                                <a href="mailto:<?php echo htmlspecialchars($c_email); ?>"><?php echo htmlspecialchars($c_email); ?></a>
                                            </span>
                                        </div>
                                    </td>

                                    <!-- PRODUCT INFO -->
                                    <td>
                                        <div class="product-cell">
                                            <div class="product-thumb-box">
                                                <img 
                                                    src="../upload/<?php echo htmlspecialchars($p_img); ?>" 
                                                    alt="<?php echo htmlspecialchars($p_name); ?>"
                                                    class="product-thumb"
                                                    onerror="this.onerror=null; this.src='../assets/img/logo/logo.png';"
                                                >
                                            </div>
                                            <div class="product-title-wrap">
                                                <span class="product-brand-tag"><?php echo htmlspecialchars($p_brand); ?></span>
                                                <span class="product-title-text" title="<?php echo htmlspecialchars($p_name); ?>">
                                                    <?php echo htmlspecialchars($p_name); ?>
                                                </span>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- PRICE -->
                                    <td>
                                        <span class="price-text">₹<?php echo number_format($p_price); ?></span>
                                    </td>

                                    <!-- QUANTITY -->
                                    <td>
                                        <span class="qty-badge"><?php echo $p_qty; ?></span>
                                    </td>

                                    <!-- GRAND TOTAL -->
                                    <td>
                                        <span class="price-text total">₹<?php echo number_format($p_total); ?></span>
                                    </td>

                                    <!-- PAYMENT -->
                                    <td>
                                        <span class="payment-badge">
                                            <i class="fa-solid fa-money-bill-wave" style="color: var(--tc-gold); margin-right: 4px;"></i>
                                            <?php echo htmlspecialchars($pm); ?>
                                        </span>
                                    </td>

                                    <!-- STATUS WITH QUICK UPDATE -->
                                    <td>
                                        <span class="status-pill <?php echo $st_lower; ?>">
                                            <i class="fa-solid fa-circle" style="font-size: 6px;"></i>
                                            <?php echo htmlspecialchars($st); ?>
                                        </span>

                                        <form method="POST" action="order.php" class="quick-status-form">
                                            <input type="hidden" name="quick_status_update" value="1">
                                            <input type="hidden" name="order_id" value="<?php echo $ord_id; ?>">
                                            <select name="order_status" class="quick-status-select" onChange="this.form.submit()">
                                                <option value="" disabled selected>Quick Change</option>
                                                <option value="Pending">Pending</option>
                                                <option value="Confirmed">Confirmed</option>
                                                <option value="Shipped">Shipped</option>
                                                <option value="Delivered">Delivered</option>
                                                <option value="Cancelled">Cancelled</option>
                                            </select>
                                        </form>
                                    </td>

                                    <!-- ORDER DATE -->
                                    <td>
                                        <span style="font-size: 12px; color: #ccc; white-space: nowrap;">
                                            <?php echo htmlspecialchars($ord_date); ?>
                                        </span>
                                    </td>

                                    <!-- ACTIONS -->
                                    <td>
                                        <div class="actions-group">
                                            <!-- VIEW MODAL BUTTON -->
                                            <button 
                                                type="button" 
                                                class="btn-action view" 
                                                title="View Full Order Details"
                                                onclick='openViewModal(<?php echo json_encode([
                                                    "id" => $ord_id,
                                                    "customer_name" => $c_name,
                                                    "email" => $c_email,
                                                    "phone" => $c_phone,
                                                    "address" => $c_address,
                                                    "product_name" => $p_name,
                                                    "product_brand" => $p_brand,
                                                    "product_price" => number_format($p_price),
                                                    "quantity" => $p_qty,
                                                    "total_price" => number_format($p_total),
                                                    "payment_method" => $pm,
                                                    "order_status" => $st,
                                                    "order_date" => $ord_date,
                                                    "product_image" => $p_img
                                                ]); ?>)'
                                            >
                                                <i class="fa-solid fa-eye"></i>
                                            </button>

                                            <!-- PRINT INVOICE -->
                                            <a 
                                                href="../invoice.php?id=<?php echo $ord_id; ?>" 
                                                target="_blank" 
                                                class="btn-action invoice" 
                                                title="Print Invoice"
                                            >
                                                <i class="fa-solid fa-file-invoice"></i>
                                            </a>

                                            <!-- EDIT ORDER -->
                                            <a 
                                                href="order.php?edit=<?php echo $ord_id; ?>#editOrderSection" 
                                                class="btn-action edit" 
                                                title="Edit Order"
                                            >
                                                <i class="fa-solid fa-pen"></i>
                                            </a>

                                            <!-- DELETE ORDER -->
                                            <form method="POST" action="order.php" style="display: inline;" onSubmit="return confirm('Are you sure you want to delete Order #TC-<?php echo $ord_id; ?> from the admin table? (It will remain visible to the customer)');">
                                                <input type="hidden" name="delete_order" value="1">
                                                <input type="hidden" name="order_id" value="<?php echo $ord_id; ?>">
                                                <button type="submit" class="btn-action delete" title="Delete Order">
                                                    <i class="fa-solid fa-trash-can"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                <?php } else { ?>
                    <div class="empty-orders">
                        <i class="fa-solid fa-box-open"></i>
                        <h3>No Orders Found</h3>
                        <p>There are currently no customer orders in the system.</p>
                    </div>
                <?php } ?>
            </div>
        </div>

    </div>

    <!-- =====================================================
         VIEW ORDER DETAILS MODAL
    ===================================================== -->
    <div class="tc-modal-backdrop" id="viewOrderModal" onClick="closeViewModal(event)">
        <div class="tc-modal-card" onClick="event.stopPropagation()">
            <div class="tc-modal-header">
                <div class="tc-modal-title">
                    <i class="fa-solid fa-receipt" style="color: var(--tc-gold);"></i> Order #<span id="modalOrderId"></span>
                </div>
                <button class="tc-modal-close" onClick="closeViewModalDirect()">&times;</button>
            </div>

            <div class="tc-modal-body">
                <div class="tc-modal-row">
                    <span class="lbl">Order Date</span>
                    <span class="val" id="modalOrderDate"></span>
                </div>
                <div class="tc-modal-row">
                    <span class="lbl">Customer Name</span>
                    <span class="val" id="modalCustomerName"></span>
                </div>
                <div class="tc-modal-row">
                    <span class="lbl">Phone Number</span>
                    <span class="val" id="modalCustomerPhone"></span>
                </div>
                <div class="tc-modal-row">
                    <span class="lbl">Email Address</span>
                    <span class="val" id="modalCustomerEmail"></span>
                </div>
                <div class="tc-modal-row">
                    <span class="lbl">Delivery Address</span>
                    <span class="val" id="modalAddress"></span>
                </div>
                <div class="tc-modal-row">
                    <span class="lbl">Watch / Product</span>
                    <span class="val" id="modalProductName"></span>
                </div>
                <div class="tc-modal-row">
                    <span class="lbl">Brand</span>
                    <span class="val" id="modalProductBrand"></span>
                </div>
                <div class="tc-modal-row">
                    <span class="lbl">Unit Price</span>
                    <span class="val" id="modalProductPrice"></span>
                </div>
                <div class="tc-modal-row">
                    <span class="lbl">Quantity</span>
                    <span class="val" id="modalQuantity"></span>
                </div>
                <div class="tc-modal-row">
                    <span class="lbl">Payment Method</span>
                    <span class="val" id="modalPaymentMethod"></span>
                </div>
                <div class="tc-modal-row">
                    <span class="lbl">Order Status</span>
                    <span class="val gold" id="modalOrderStatus"></span>
                </div>
                <div class="tc-modal-row" style="border-top: 1px solid rgba(212, 175, 55, 0.3); margin-top: 10px; padding-top: 14px;">
                    <span class="lbl" style="font-size: 13px; color: #fff;">Grand Total</span>
                    <span class="val gold" style="font-size: 18px;" id="modalTotalPrice"></span>
                </div>

                <div style="margin-top: 25px; display: flex; gap: 10px;">
                    <a id="modalInvoiceLink" href="#" target="_blank" class="btn-filter-submit" style="flex: 1; justify-content: center;">
                        <i class="fa-solid fa-file-invoice"></i> Open Tax Invoice
                    </a>
                    <button class="btn-filter-reset" onClick="closeViewModalDirect()" style="flex: 0.6; justify-content: center;">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- =====================================================
         SCRIPTS
    ===================================================== -->
    <script>
        

        function calculateEditTotal() {
            const price = parseFloat(document.querySelector('input[name="product_price"]')?.value || 0);
            const qty = parseInt(document.querySelector('input[name="quantity"]')?.value || 1);
            const totalField = document.getElementById('edit_total_price');
            if (totalField) {
                totalField.value = (price * qty).toFixed(2);
            }
        }

        function openViewModal(data) {
            document.getElementById('modalOrderId').innerText = 'TC-' + data.id;
            document.getElementById('modalOrderDate').innerText = data.order_date;
            document.getElementById('modalCustomerName').innerText = data.customer_name;
            document.getElementById('modalCustomerPhone').innerText = data.phone;
            document.getElementById('modalCustomerEmail').innerText = data.email;
            document.getElementById('modalAddress').innerText = data.address;
            document.getElementById('modalProductName').innerText = data.product_name;
            document.getElementById('modalProductBrand').innerText = data.product_brand;
            document.getElementById('modalProductPrice').innerText = '₹' + data.product_price;
            document.getElementById('modalQuantity').innerText = data.quantity;
            document.getElementById('modalPaymentMethod').innerText = data.payment_method;
            document.getElementById('modalOrderStatus').innerText = data.order_status;
            document.getElementById('modalTotalPrice').innerText = '₹' + data.total_price;
            document.getElementById('modalInvoiceLink').href = '../invoice.php?id=' + data.id;

            document.getElementById('viewOrderModal').classList.add('show');
        }

        function closeViewModal(event) {
            if (event.target.id === 'viewOrderModal') {
                closeViewModalDirect();
            }
        }

        function closeViewModalDirect() {
            document.getElementById('viewOrderModal').classList.remove('show');
        }

        function exportTableToCSV(filename) {
            const rows = document.querySelectorAll("#ordersTable tr");
            let csv = [];
            for (let i = 0; i < rows.length; i++) {
                let row = [], cols = rows[i].querySelectorAll("td, th");
                for (let j = 0; j < cols.length - 1; j++) { // exclude actions
                    let text = cols[j].innerText.replace(/(\r\n|\n|\r)/gm, " ").replace(/"/g, '""').trim();
                    row.push('"' + text + '"');
                }
                csv.push(row.join(","));
            }
            const csvFile = new Blob([csv.join("\n")], { type: "text/csv" });
            const downloadLink = document.createElement("a");
            downloadLink.download = filename;
            downloadLink.href = window.URL.createObjectURL(csvFile);
            downloadLink.style.display = "none";
            document.body.appendChild(downloadLink);
            downloadLink.click();
            document.body.removeChild(downloadLink);
        }
    </script>
</body>
</html>