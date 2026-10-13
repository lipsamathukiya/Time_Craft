<?php

session_start();

include("conn.php");

date_default_timezone_set("Asia/Kolkata");

mysqli_query($conn, "SET time_zone = '+05:30'");

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

if (!isset($_POST['place_order'])) {
    header("Location: checkout.php");
    exit();
}

$customer_name = mysqli_real_escape_string(
    $conn,
    trim($_POST['customer_name'] ?? '')
);

$email = mysqli_real_escape_string(
    $conn,
    trim($_POST['email'] ?? '')
);

$phone = mysqli_real_escape_string(
    $conn,
    trim($_POST['phone'] ?? '')
);

$address = mysqli_real_escape_string(
    $conn,
    trim($_POST['address'] ?? '')
);

$payment_method = mysqli_real_escape_string(
    $conn,
    trim($_POST['payment_method'] ?? 'Cash On Delivery')
);

if (
    empty($customer_name) ||
    empty($email) ||
    empty($phone) ||
    empty($address) ||
    empty($payment_method)
) {
    die("Please fill all details.");
}

$cart_query = mysqli_query(
    $conn,
    "
    SELECT
        cart.product_id,
        cart.qty,
        product.product_name,
        product.price
    FROM cart
    INNER JOIN product
        ON cart.product_id = product.id
    WHERE cart.user_id = '$user_id'
    "
);

if (!$cart_query) {
    die("Cart Query Error: " . mysqli_error($conn));
}

if (mysqli_num_rows($cart_query) == 0) {
    header("Location: cart.php");
    exit();
}

mysqli_begin_transaction($conn);

try {
    $inserted_order_ids = [];

    while ($cart = mysqli_fetch_assoc($cart_query)) {
        $product_id = intval($cart['product_id']);

        $product_name = mysqli_real_escape_string(
            $conn,
            $cart['product_name']
        );

        $product_price = floatval(
            $cart['price']
        );

        $quantity = intval(
            $cart['qty']
        );

        $total_price = $product_price * $quantity;

        $order_status = "Pending";

        $insert_query = "
            INSERT INTO `order`
            (
                customer_name,
                email,
                phone,
                address,
                product_name,
                product_price,
                quantity,
                total_price,
                payment_method,
                order_status
            )
            VALUES
            (
                '$customer_name',
                '$email',
                '$phone',
                '$address',
                '$product_name',
                '$product_price',
                '$quantity',
                '$total_price',
                '$payment_method',
                '$order_status'
            )
        ";

        if (!mysqli_query($conn, $insert_query)) {
            throw new Exception(
                "Order Insert Error: " . mysqli_error($conn)
            );
        }

        $inserted_order_ids[] = mysqli_insert_id($conn);

        // Update product stock safely
        mysqli_query(
            $conn, 
            "UPDATE product SET stock = GREATEST(0, stock - $quantity) WHERE id = '$product_id'"
        );
    }

    $delete_cart = mysqli_query(
        $conn,
        "
        DELETE FROM cart
        WHERE user_id = '$user_id'
        "
    );

    if (!$delete_cart) {
        throw new Exception(
            "Cart Delete Error: " . mysqli_error($conn)
        );
    }

    mysqli_commit($conn);

    // Store recent order IDs in session for the order confirmation page
    $_SESSION['last_order_ids'] = $inserted_order_ids;
    $_SESSION['order_success_flash'] = true;

    header("Location: order-success.php");
    exit();

} catch (Exception $e) {

    mysqli_rollback($conn);

    die($e->getMessage());
}

?>
