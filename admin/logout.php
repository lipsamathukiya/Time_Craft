<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include("conn.php");

date_default_timezone_set("Asia/Kolkata");

$admin_id = 0;
if (isset($_SESSION['admin_id']) && !empty($_SESSION['admin_id'])) {
    $admin_id = (int)$_SESSION['admin_id'];
} elseif (isset($_SESSION['user_id']) && isset($_SESSION['u_type']) && $_SESSION['u_type'] === 'admin') {
    $admin_id = (int)$_SESSION['user_id'];
}

if ($admin_id > 0) {
    $logout_date = date("Y-m-d");
    $logout_time = date("h:i:s A");

    $sql = "UPDATE reg
            SET
                logout_date = '$logout_date',
                logout_time = '$logout_time'
            WHERE id = $admin_id";

    @mysqli_query($conn, $sql);
}

// Clear ONLY admin-related session variables so user session in another tab stays logged in!
unset($_SESSION['admin_id']);
unset($_SESSION['admin_name']);
unset($_SESSION['admin_email']);
unset($_SESSION['admin_u_type']);
unset($_SESSION['admin_logged_in']);
unset($_SESSION['admin_msg']);
unset($_SESSION['admin_msg_type']);

// If user session is active, reset u_type to 'user', else unset u_type
if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
    $_SESSION['u_type'] = $_SESSION['user_type'] ?? 'user';
} else {
    unset($_SESSION['u_type']);
}

header("Location: ../login.php");
exit();
?>