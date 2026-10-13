<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include("conn.php");

if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
    $user_id = intval($_SESSION['user_id']);
} elseif (isset($_SESSION['uid']) && !empty($_SESSION['uid'])) {
    $user_id = intval($_SESSION['uid']);
} else {
    $_SESSION['login_redirect'] = 'wishlist.php';
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: brand.php");
    exit();
}

$product_id = intval($_GET['id']);

$product_query = "SELECT id FROM product WHERE id = $product_id";

$product_result = mysqli_query($conn, $product_query);

if (!$product_result || mysqli_num_rows($product_result) == 0) {
    header("Location: brand.php");
    exit();
}

$check_query = "
    SELECT id
    FROM wishlist
    WHERE user_id = $user_id
    AND product_id = $product_id
    LIMIT 1
";

$check_result = mysqli_query($conn, $check_query);

if (!$check_result) {
    die("Wishlist Check Error: " . mysqli_error($conn));
}

if (mysqli_num_rows($check_result) > 0) {
    echo "<script>
        alert('Product is already in your wishlist!');
        history.back();
    </script>";
    exit();
}

$insert_query = "
    INSERT INTO wishlist (user_id, product_id)
    VALUES ($user_id, $product_id)
";

if (mysqli_query($conn, $insert_query)) {

    echo "<script>
        alert('Product added to wishlist!');
        history.back();
    </script>";

    exit();

} else {

    die("Wishlist Error: " . mysqli_error($conn));
}

?>