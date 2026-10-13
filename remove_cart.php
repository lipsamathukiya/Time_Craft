<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include("conn.php");

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

if (isset($_GET['action']) && $_GET['action'] === 'clear_all') {
    $stmt = mysqli_prepare($conn, "DELETE FROM cart WHERE user_id = ?");
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $user_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        $_SESSION['cart_msg'] = "All items have been removed from your cart.";
        $_SESSION['cart_msg_type'] = "info";
    }
} elseif (isset($_GET['id'])) {
    $cart_id = intval($_GET['id']);
    $stmt = mysqli_prepare($conn, "DELETE FROM cart WHERE id = ? AND user_id = ?");
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ii", $cart_id, $user_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        $_SESSION['cart_msg'] = "Watch removed from your cart.";
        $_SESSION['cart_msg_type'] = "info";
    }
}

header("Location: cart.php");
exit();

?>