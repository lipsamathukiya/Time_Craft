<?php

session_start();
include("conn.php");

if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
    $user_id = intval($_SESSION['user_id']);
} elseif (isset($_SESSION['uid']) && !empty($_SESSION['uid'])) {
    $user_id = intval($_SESSION['uid']);
} else {
    $_SESSION['login_redirect'] = 'cart.php';
    header("Location: login.php");
    exit();
}

if(!isset($_GET['id']))
{
    header("Location: index.php");
    exit();
}

$product_id = intval($_GET['id']);

$qty = isset($_GET['qty']) ? intval($_GET['qty']) : 1;

if($qty < 1)
{
    $qty = 1;
}

$product_q = mysqli_query(
$conn,
"SELECT * FROM product WHERE id='$product_id'"
);

if(mysqli_num_rows($product_q) == 0)
{
    die("Product Not Found");
}

$product = mysqli_fetch_assoc($product_q);

$product_name = $product['product_name'];
$price = $product['price'];

$check = mysqli_query(
$conn,
"SELECT * FROM cart
WHERE user_id='$user_id'
AND product_id='$product_id'"
);

if(mysqli_num_rows($check) > 0)
{
    mysqli_query(
    $conn,
    "UPDATE cart
    SET qty = qty + '$qty'
    WHERE user_id='$user_id'
    AND product_id='$product_id'"
    );
}
else
{
    mysqli_query(
    $conn,
    "INSERT INTO cart
    (product_id, product_name, price, qty, user_id)
    VALUES
    ('$product_id','$product_name','$price','$qty','$user_id')"
    );
}

header("Location: cart.php");
exit();

?>