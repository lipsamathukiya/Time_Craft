<?php
session_start();
include("conn.php");

date_default_timezone_set("Asia/Kolkata");

if (!isset($_GET['id']) || intval($_GET['id']) <= 0) {
    die("Invalid Order ID provided.");
}

$order_id = intval($_GET['id']);

$query = mysqli_query($conn, "
    SELECT 
        o.*,
        p.brand AS product_brand,
        p.image AS product_image
    FROM `order` AS o
    LEFT JOIN product AS p 
        ON TRIM(LOWER(p.product_name)) = TRIM(LOWER(o.product_name))
    WHERE o.id = '$order_id'
    LIMIT 1
");

if (!$query || mysqli_num_rows($query) == 0) {
    die("Order Not Found.");
}

$order = mysqli_fetch_assoc($query);
$formatted_order_date = !empty($order['order_date']) ? date("d M Y, h:i A", strtotime($order['order_date'])) : 'N/A';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Time Craft - Tax Invoice #TC-<?php echo $order['id']; ?></title>
    <link rel="icon" type="image/png" href="assets/img/logo/logo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, "Segoe UI", sans-serif;
        }

        body {
            background: #f0f0f0;
            padding: 40px 15px;
            color: #222;
        }

        .invoice-card {
            max-width: 850px;
            margin: auto;
            background: #ffffff;
            border: 2px solid #d4af37;
            border-radius: 12px;
            padding: 40px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        /* HEADER */
        .invoice-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #f0f0f0;
            padding-bottom: 25px;
            margin-bottom: 25px;
        }

        .brand-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .brand-logo {
            width: 70px;
            height: 70px;
            object-fit: contain;
        }

        .brand-title h1 {
            font-size: 26px;
            font-weight: 800;
            color: #111;
            letter-spacing: 1px;
        }

        .brand-title h1 span {
            color: #d4af37;
        }

        .brand-title p {
            font-size: 13px;
            color: #777;
            font-weight: 600;
        }

        .invoice-title-block {
            text-align: right;
        }

        .invoice-title-block h2 {
            font-size: 24px;
            font-weight: 800;
            color: #d4af37;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .invoice-title-block p {
            font-size: 13px;
            color: #555;
            margin-top: 4px;
        }

        /* METADATA GRID */
        .meta-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-bottom: 30px;
        }

        .meta-box {
            background: #fdfdfd;
            border: 1px solid #eee;
            border-radius: 8px;
            padding: 16px 20px;
        }

        .meta-box h3 {
            font-size: 14px;
            font-weight: 700;
            text-transform: uppercase;
            color: #d4af37;
            margin-bottom: 10px;
            letter-spacing: 0.5px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .meta-box p {
            font-size: 13px;
            color: #444;
            line-height: 1.6;
            margin-bottom: 4px;
        }

        .meta-box p strong {
            color: #111;
        }

        /* TABLE */
        table.invoice-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }

        table.invoice-table th {
            background: #111;
            color: #d4af37;
            padding: 12px 16px;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            text-align: left;
            border: 1px solid #111;
        }

        table.invoice-table th.text-right {
            text-align: right;
        }

        table.invoice-table th.text-center {
            text-align: center;
        }

        table.invoice-table td {
            padding: 14px 16px;
            border: 1px solid #e0e0e0;
            font-size: 14px;
            color: #333;
        }

        table.invoice-table td.text-right {
            text-align: right;
        }

        table.invoice-table td.text-center {
            text-align: center;
        }

        /* TOTAL SECTION */
        .total-section {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 30px;
        }

        .total-box {
            width: 320px;
            background: #fdfdfd;
            border: 1px solid #eee;
            border-radius: 8px;
            padding: 16px 20px;
        }

        .total-line {
            display: flex;
            justify-content: space-between;
            padding: 6px 0;
            font-size: 13px;
            color: #555;
        }

        .total-line.grand {
            border-top: 2px solid #d4af37;
            margin-top: 8px;
            padding-top: 10px;
            font-size: 18px;
            font-weight: 800;
            color: #111;
        }

        .total-line.grand span.amt {
            color: #d4af37;
        }

        /* BUTTONS */
        .invoice-actions {
            text-align: center;
            padding-top: 20px;
            border-top: 1px dashed #ccc;
            display: flex;
            justify-content: center;
            gap: 15px;
            flex-wrap: wrap;
        }

        .inv-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 24px;
            border-radius: 6px;
            font-weight: 700;
            font-size: 14px;
            text-decoration: none;
            cursor: pointer;
            transition: 0.25s;
        }

        .inv-btn-print {
            background: #d4af37;
            color: #111;
            border: 1px solid #d4af37;
        }

        .inv-btn-print:hover {
            background: #111;
            color: #d4af37;
        }

        .inv-btn-back {
            background: #222;
            color: #fff;
            border: 1px solid #222;
        }

        .inv-btn-back:hover {
            background: #444;
            color: #fff;
        }

        .invoice-footer {
            margin-top: 30px;
            text-align: center;
            color: #888;
            font-size: 12px;
            line-height: 1.5;
        }

        @media print {
            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                color-adjust: exact !important;
            }
            body {
                background: #fff !important;
                padding: 10px !important;
            }
            .invoice-card {
                border: 2px solid #d4af37 !important;
                box-shadow: none !important;
                padding: 30px !important;
                border-radius: 10px !important;
            }
            .invoice-actions {
                display: none !important;
            }
            .invoice-table th {
                background: #111111 !important;
                color: #d4af37 !important;
            }
        }
    </style>
</head>
<body>

<div class="invoice-card">
    
    <!-- HEADER -->
    <div class="invoice-header">
        <div class="brand-info">
            <img src="assets/img/logo/logo.png" alt="Time Craft" class="brand-logo" onerror="this.style.display='none';">
            <div class="brand-title">
                <h1>TIME <span>CRAFT</span></h1>
                <p>Luxury Horology & Timepieces</p>
            </div>
        </div>
        <div class="invoice-title-block">
            <h2>Tax Invoice</h2>
            <p><strong>Invoice No:</strong> #TC-<?php echo htmlspecialchars($order['id']); ?></p>
            <p><strong>Date:</strong> <?php echo htmlspecialchars($formatted_order_date); ?></p>
        </div>
    </div>

    <!-- METADATA -->
    <div class="meta-grid">
        <div class="meta-box">
            <h3><i class="fa-solid fa-user"></i> Billed To</h3>
            <p><strong>Name:</strong> <?php echo htmlspecialchars($order['customer_name'] ?? 'Customer'); ?></p>
            <p><strong>Phone:</strong> <?php echo htmlspecialchars($order['phone'] ?? 'N/A'); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($order['email'] ?? 'N/A'); ?></p>
            <p><strong>Address:</strong> <?php echo nl2br(htmlspecialchars($order['address'] ?? 'N/A')); ?></p>
        </div>

        <div class="meta-box">
            <h3><i class="fa-solid fa-file-lines"></i> Order & Payment Details</h3>
            <p><strong>Order Reference:</strong> #TC-<?php echo htmlspecialchars($order['id']); ?></p>
            <p><strong>Order Status:</strong> <?php echo htmlspecialchars($order['order_status'] ?? 'Pending'); ?></p>
            <p><strong>Payment Method:</strong> <?php echo htmlspecialchars($order['payment_method'] ?? 'Cash On Delivery'); ?></p>
            <p><strong>Payment Status:</strong> <?php echo strtolower($order['order_status'] ?? '') === 'delivered' ? 'Paid' : 'Pay on Delivery'; ?></p>
        </div>
    </div>

    <!-- ITEMS TABLE -->
    <table class="invoice-table">
        <thead>
            <tr>
                <th style="width: 50px;" class="text-center">#</th>
                <th>Item Description</th>
                <th class="text-right">Unit Price</th>
                <th class="text-center" style="width: 80px;">Qty</th>
                <th class="text-right">Total (INR)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-center">1</td>
                <td>
                    <strong><?php echo htmlspecialchars($order['product_name']); ?></strong>
                    <?php if (!empty($order['product_brand'])) { ?>
                        <br><span style="font-size: 12px; color: #777;">Brand: <?php echo htmlspecialchars($order['product_brand']); ?></span>
                    <?php } ?>
                </td>
                <td class="text-right">&#8377;<?php echo number_format((float)($order['product_price'] ?? 0), 2); ?></td>
                <td class="text-center"><?php echo intval($order['quantity'] ?? 1); ?></td>
                <td class="text-right">&#8377;<?php echo number_format((float)($order['total_price'] ?? 0), 2); ?></td>
            </tr>
        </tbody>
    </table>

    <!-- TOTALS -->
    <div class="total-section">
        <div class="total-box">
            <div class="total-line">
                <span>Subtotal</span>
                <span>&#8377;<?php echo number_format((float)($order['total_price'] ?? 0), 2); ?></span>
            </div>
            <div class="total-line">
                <span>Luxury Express Shipping</span>
                <span style="color: #27ae60; font-weight: bold;">FREE</span>
            </div>
            <div class="total-line">
                <span>Taxes</span>
                <span>Included</span>
            </div>
            <div class="total-line grand">
                <span>Grand Total</span>
                <span class="amt">&#8377;<?php echo number_format((float)($order['total_price'] ?? 0), 2); ?></span>
            </div>
        </div>
    </div>

    <!-- ACTIONS -->
    <div class="invoice-actions">
        <button onclick="window.print()" class="inv-btn inv-btn-print">
            <i class="fa-solid fa-print"></i> Print / Download Invoice
        </button>
        <a href="my_order.php" class="inv-btn inv-btn-back">
            <i class="fa-solid fa-arrow-left"></i> Back to My Orders
        </a>
    </div>

    <!-- FOOTER -->
    <div class="invoice-footer">
        <p>Thank you for choosing Time Craft for your luxury timepiece selection.</p>
        <p>For support or inquiries, please contact our concierge team.</p>
    </div>

</div>

</body>
</html>
