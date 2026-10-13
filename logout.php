<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include("conn.php");

date_default_timezone_set("Asia/Kolkata");

if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
    $user_id = (int)$_SESSION['user_id'];
    $logout_date = date("Y-m-d");
    $logout_time = date("h:i:s A");
    $sql = "UPDATE reg
            SET
                logout_date = '$logout_date',
                logout_time = '$logout_time'
            WHERE id = $user_id";
    @mysqli_query($conn, $sql);
}

// Clear ONLY user-related session variables so admin session in another tab stays logged in!
unset($_SESSION['user_id']);
unset($_SESSION['uid']);
unset($_SESSION['user_name']);
unset($_SESSION['name']);
unset($_SESSION['email']);
unset($_SESSION['user_email']);
unset($_SESSION['user_logged_in']);
unset($_SESSION['user_type']);
unset($_SESSION['cart_msg']);
unset($_SESSION['cart_msg_type']);
unset($_SESSION['login_redirect']);
unset($_SESSION['order_user_msg']);
unset($_SESSION['order_user_msg_type']);
unset($_SESSION['last_shop_page']);
unset($_SESSION['last_brand_page']);
unset($_SESSION['last_order_ids']);
unset($_SESSION['order_success_flash']);

// If admin session is active, keep u_type as 'admin', otherwise unset
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    unset($_SESSION['u_type']);
}

header("Location: index.php");
exit();
?>