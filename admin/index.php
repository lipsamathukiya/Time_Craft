<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$is_admin = false;
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    $is_admin = true;
} elseif (isset($_SESSION['admin_u_type']) && $_SESSION['admin_u_type'] === "admin") {
    $is_admin = true;
} elseif (isset($_SESSION['u_type']) && $_SESSION['u_type'] === "admin") {
    $is_admin = true;
}

if ($is_admin) {
    header("Location: dashboard.php");
} else {
    header("Location: ../login.php");
}
exit();
?>