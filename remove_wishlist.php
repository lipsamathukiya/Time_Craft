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

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $wishlist_id = intval($_GET['id']);
    
    // Secure deletion with prepared statement
    $stmt = mysqli_prepare($conn, "DELETE FROM wishlist WHERE id = ? AND user_id = ?");
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ii", $wishlist_id, $user_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
} elseif (isset($_GET['product_id']) && !empty($_GET['product_id'])) {
    $product_id = intval($_GET['product_id']);
    
    // Secure deletion by product_id if passed
    $stmt = mysqli_prepare($conn, "DELETE FROM wishlist WHERE product_id = ? AND user_id = ?");
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ii", $product_id, $user_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
}

header("Location: wishlist.php");
exit();

?>
