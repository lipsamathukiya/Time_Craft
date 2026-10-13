<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (isset($_SERVER['HTTP_REFERER'])) {

    $prev_page = $_SERVER['HTTP_REFERER'];

    if (strpos($prev_page, 'brand.php') !== false) {

        $_SESSION['last_brand_page'] = $prev_page;

    }
}

include("conn.php");


/* =====================================================
   USER AUTHENTICATION
===================================================== */

$user_id = 0;

if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {

    $user_id = intval($_SESSION['user_id']);

} elseif (isset($_SESSION['uid']) && !empty($_SESSION['uid'])) {

    $user_id = intval($_SESSION['uid']);

}


/* =====================================================
   PRODUCT ID VALIDATION
===================================================== */

if (!isset($_GET['id']) || empty($_GET['id'])) {

    header("Location: brand.php");

    exit();

}

$product_id = intval($_GET['id']);


/* =====================================================
   FETCH PRODUCT DETAILS
===================================================== */

$tc_p_stmt = mysqli_prepare(
    $conn,
    "SELECT * FROM product WHERE id = ?"
);

$product = null;


if ($tc_p_stmt) {

    mysqli_stmt_bind_param(
        $tc_p_stmt,
        "i",
        $product_id
    );

    mysqli_stmt_execute(
        $tc_p_stmt
    );

    $res = mysqli_stmt_get_result(
        $tc_p_stmt
    );


    if (
        $res &&
        mysqli_num_rows($res) > 0
    ) {

        $product = mysqli_fetch_assoc(
            $res
        );

    }

}


/* =====================================================
   PRODUCT NOT FOUND
===================================================== */

if (!$product) {

    header("Location: brand.php");

    exit();

}


/* =====================================================
   PRODUCT DATA
===================================================== */

$product_name =
    $product['product_name']
    ?? 'Luxury Watch';


$brand =
    $product['brand']
    ?? 'TimeCraft';


$collection =
    $product['collection']
    ?? '';


$price =
    isset($product['price'])
    ? (float)$product['price']
    : 0.00;


$image =
    $product['image']
    ?? '';


$stock =
    isset($product['stock'])
    ? intval($product['stock'])
    : 0;


/* =====================================================
   CHECK WISHLIST
===================================================== */

$is_wishlisted = false;


if ($user_id > 0) {

    $w_check_stmt = mysqli_prepare(
        $conn,
        "SELECT id
         FROM wishlist
         WHERE user_id = ?
         AND product_id = ?
         LIMIT 1"
    );


    if ($w_check_stmt) {

        mysqli_stmt_bind_param(
            $w_check_stmt,
            "ii",
            $user_id,
            $product_id
        );

        mysqli_stmt_execute(
            $w_check_stmt
        );

        $w_check =
            mysqli_stmt_get_result(
                $w_check_stmt
            );


        if (
            $w_check &&
            mysqli_num_rows($w_check) > 0
        ) {

            $is_wishlisted = true;

        }

    }

}


/* =====================================================
   INCLUDE HEADER
===================================================== */

include("header.php");

?>


<style>

/* =====================================================
   ROOT COLORS
===================================================== */

:root {

    --tc-gold: #d4af37;

    --tc-gold-light: #f3e5ab;

    --tc-gold-dark: #b89127;

    --tc-gold-hover: #ffc107;

    --tc-dark-bg: #0a0a0a;

    --tc-card-bg: #141414;

    --tc-card-hover: #1a1a1a;

    --tc-text-primary: #ffffff;

    --tc-text-secondary: #a8a8a8;

    --tc-border-gold:
        rgba(212, 175, 55, 0.22);

    --tc-border-gold-hover:
        rgba(212, 175, 55, 0.7);

    --tc-danger: #ff4d4f;

    --tc-success: #2ecc71;

}


/* =====================================================
   MAIN SECTION
===================================================== */

.tc-details-section {

    background:
        linear-gradient(
            180deg,
            #0a0a0a 0%,
            #121212 100%
        );

    min-height: 80vh;

    padding:
        40px 0 90px 0;

    color:
        var(--tc-text-primary);

    overflow-x: hidden;

    width: 100%;

}


/* =====================================================
   BREADCRUMBS
===================================================== */

.tc-breadcrumbs {

    display: flex;

    align-items: center;

    gap: 8px;

    font-size: 14px;

    color:
        var(--tc-text-secondary);

    margin-bottom: 30px;

    flex-wrap: wrap;

}


.tc-breadcrumbs a {

    color:
        var(--tc-gold);

    text-decoration: none;

    transition:
        color 0.2s ease;

}


.tc-breadcrumbs a:hover {

    color:
        #ffffff;

}


.tc-breadcrumbs span.curr {

    color:
        #ffffff;

    font-weight: 600;

}


/* =====================================================
   DETAIL GRID
===================================================== */

.tc-detail-grid {

    display: grid;

    grid-template-columns:
        1fr 1.15fr;

    gap: 45px;

    align-items: start;

}


/* =====================================================
   GALLERY BOX
   FIXED SIZE FOR ALL WATCH IMAGES
===================================================== */

.tc-gallery-box {

    width: 100%;

    height: 460px;

    background:
        radial-gradient(
            circle,
            #252525 0%,
            #141414 100%
        );

    border:
        1px solid
        var(--tc-border-gold);

    border-radius: 20px;

    padding: 30px;

    display: flex;

    align-items: center;

    justify-content: center;

    position: relative;

    overflow: hidden;

    box-shadow:
        0 12px 35px
        rgba(
            0,
            0,
            0,
            0.5
        );

}


/* =====================================================
   PRODUCT IMAGE
   SAME SIZE / SAME POSITION
===================================================== */

.tc-gallery-img {

    width: 360px;

    height: 360px;

    max-width: 100%;

    max-height: 100%;

    object-fit: contain;

    object-position: center center;

    display: block;

    filter:
        drop-shadow(
            0 15px 30px
            rgba(
                0,
                0,
                0,
                0.8
            )
        );

    transition:
        transform 0.4s ease;

}


/* =====================================================
   IMAGE HOVER
===================================================== */

.tc-gallery-box:hover
.tc-gallery-img {

    transform:
        scale(1.05);

}


/* =====================================================
   GALLERY BADGES
===================================================== */

.tc-gallery-badges {

    position: absolute;

    top: 20px;

    left: 20px;

    display: flex;

    flex-direction: column;

    gap: 8px;

    z-index: 5;

}


/* =====================================================
   BRAND BADGE
===================================================== */

.tc-gallery-brand-badge {

    background:
        rgba(
            0,
            0,
            0,
            0.85
        );

    border:
        1px solid
        var(--tc-border-gold);

    color:
        var(--tc-gold);

    font-size: 12px;

    font-weight: 800;

    text-transform: uppercase;

    letter-spacing: 1.5px;

    padding:
        5px 12px;

    border-radius: 6px;

}


/* =====================================================
   WISHLIST BUTTON
===================================================== */

.tc-gallery-wishlist-btn {

    position: absolute;

    top: 20px;

    right: 20px;

    width: 44px;

    height: 44px;

    border-radius: 50%;

    background:
        rgba(
            0,
            0,
            0,
            0.8
        );

    border:
        1px solid
        var(--tc-border-gold);

    color:
        var(--tc-gold);

    display: flex;

    align-items: center;

    justify-content: center;

    font-size: 18px;

    text-decoration:
        none !important;

    transition:
        all 0.3s ease;

    z-index: 10;

}


.tc-gallery-wishlist-btn.added {

    background:
        rgba(
            212,
            175,
            55,
            0.2
        );

    border-color:
        var(--tc-gold);

}


.tc-gallery-wishlist-btn:hover {

    background:
        var(--tc-gold);

    color:
        #000000 !important;

    transform:
        scale(1.12);

}


/* =====================================================
   PRODUCT INFO
===================================================== */

.tc-info-box {

    background:
        var(--tc-card-bg);

    border:
        1px solid
        var(--tc-border-gold);

    border-radius: 20px;

    padding:
        36px 32px;

    box-shadow:
        0 12px 35px
        rgba(
            0,
            0,
            0,
            0.5
        );

}


/* =====================================================
   BRAND
===================================================== */

.tc-info-brand {

    color:
        var(--tc-gold);

    font-size: 13px;

    font-weight: 800;

    text-transform: uppercase;

    letter-spacing: 2px;

    display: block;

    margin-bottom: 8px;

}


/* =====================================================
   TITLE
===================================================== */

.tc-info-title {

    font-size: 32px;

    font-weight: 800;

    color:
        #ffffff;

    line-height: 1.25;

    margin-bottom: 12px;

}


/* =====================================================
   COLLECTION
===================================================== */

.tc-info-collection {

    display: inline-flex;

    align-items: center;

    gap: 6px;

    color:
        var(--tc-text-secondary);

    font-size: 14px;

    margin-bottom: 20px;

}


/* =====================================================
   PRICE BOX
===================================================== */

.tc-info-price-box {

    display: flex;

    align-items: center;

    gap: 15px;

    margin-bottom: 24px;

    padding-bottom: 20px;

    border-bottom:
        1px solid
        rgba(
            255,
            255,
            255,
            0.08
        );

}


/* =====================================================
   PRICE
===================================================== */

.tc-info-price {

    font-size: 36px;

    font-weight: 800;

    color:
        var(--tc-gold);

    display: flex;

    align-items: center;

    gap: 4px;

}


/* =====================================================
   STOCK
===================================================== */

.tc-stock-indicator {

    display: inline-flex;

    align-items: center;

    gap: 8px;

    font-size: 13px;

    font-weight: 700;

    padding:
        6px 14px;

    border-radius: 20px;

}


.tc-stock-indicator.in {

    background:
        rgba(
            46,
            204,
            113,
            0.15
        );

    color:
        #2ecc71;

    border:
        1px solid
        rgba(
            46,
            204,
            113,
            0.4
        );

}


.tc-stock-indicator.out {

    background:
        rgba(
            231,
            76,
            60,
            0.15
        );

    color:
        #e74c3c;

    border:
        1px solid
        rgba(
            231,
            76,
            60,
            0.4
        );

}


/* =====================================================
   PURCHASE FORM
===================================================== */

.tc-purchase-form {

    margin-bottom: 28px;

}


/* =====================================================
   QUANTITY
===================================================== */

.tc-qty-group {

    display: flex;

    align-items: center;

    gap: 16px;

    margin-bottom: 22px;

}


.tc-qty-label {

    font-size: 14px;

    font-weight: 700;

    color:
        #dcdcdc;

}


.tc-qty-input {

    width: 80px;

    background:
        #1c1c1c;

    border:
        1.5px solid
        var(--tc-border-gold);

    color:
        #ffffff;

    border-radius: 8px;

    padding:
        10px 12px;

    font-size: 16px;

    font-weight: 700;

    text-align: center;

    outline: none;

}


.tc-qty-input:focus {

    border-color:
        var(--tc-gold);

    box-shadow:
        0 0 10px
        rgba(
            212,
            175,
            55,
            0.3
        );

}


/* =====================================================
   BUTTON ROW
===================================================== */

.tc-btn-group-row {

    display: flex;

    align-items: center;

    gap: 14px;

    flex-wrap: wrap;

}


/* =====================================================
   ADD TO CART
===================================================== */

.tc-btn-cart-primary {

    flex: 1;

    min-width: 180px;

    display: inline-flex;

    align-items: center;

    justify-content: center;

    gap: 10px;

    background:
        linear-gradient(
            135deg,
            #d4af37 0%,
            #aa8214 100%
        );

    color:
        #000000 !important;

    font-weight: 800;

    font-size: 15px;

    padding:
        14px 24px;

    border-radius: 10px;

    border: none;

    cursor: pointer;

    text-decoration:
        none !important;

    transition:
        all 0.3s ease;

    box-shadow:
        0 5px 18px
        rgba(
            212,
            175,
            55,
            0.35
        );

}


.tc-btn-cart-primary:hover {

    background:
        linear-gradient(
            135deg,
            #ffc107 0%,
            #d4af37 100%
        );

    transform:
        translateY(-2px);

    box-shadow:
        0 8px 25px
        rgba(
            255,
            193,
            7,
            0.55
        );

}


/* =====================================================
   OUT OF STOCK BUTTON
===================================================== */

.tc-btn-cart-disabled {

    flex: 1;

    min-width: 180px;

    display: inline-flex;

    align-items: center;

    justify-content: center;

    gap: 10px;

    background:
        #242424;

    color:
        #777 !important;

    font-weight: 700;

    font-size: 15px;

    padding:
        14px 24px;

    border-radius: 10px;

    border:
        1px solid
        #383838;

    cursor:
        not-allowed;

    text-decoration:
        none !important;

}


/* =====================================================
   WISHLIST ACTION
===================================================== */

.tc-btn-wish-action {

    display: inline-flex;

    align-items: center;

    justify-content: center;

    gap: 8px;

    background:
        #1e1e1e;

    border:
        1px solid
        rgba(
            255,
            255,
            255,
            0.2
        );

    color:
        #ffffff !important;

    font-weight: 600;

    font-size: 14px;

    padding:
        14px 20px;

    border-radius: 10px;

    text-decoration:
        none !important;

    transition:
        all 0.3s ease;

}


.tc-btn-wish-action:hover {

    border-color:
        var(--tc-gold);

    color:
        var(--tc-gold) !important;

    background:
        rgba(
            212,
            175,
            55,
            0.1
        );

}


/* =====================================================
   BACK BUTTON
===================================================== */

.tc-btn-back-action {

    display: inline-flex;

    align-items: center;

    gap: 6px;

    color:
        var(--tc-text-secondary);

    font-size: 13px;

    font-weight: 600;

    text-decoration:
        none;

    margin-top: 14px;

    transition:
        color 0.2s ease;

}


.tc-btn-back-action:hover {

    color:
        var(--tc-gold);

}


/* =====================================================
   SPECIFICATIONS
===================================================== */

.tc-specs-grid {

    display: grid;

    grid-template-columns:
        1fr 1fr;

    gap: 14px;

    margin-top: 24px;

    padding-top: 22px;

    border-top:
        1px solid
        rgba(
            255,
            255,
            255,
            0.08
        );

}


.tc-spec-item {

    display: flex;

    align-items: center;

    gap: 10px;

    font-size: 13px;

    color:
        #cfcfcf;

}


.tc-spec-item i {

    color:
        var(--tc-gold);

    font-size: 16px;

    width: 20px;

}


/* =====================================================
   LOGIN POPUP OVERLAY
===================================================== */

.tc-login-popup-overlay {

    position: fixed;

    inset: 0;

    width: 100%;

    height: 100%;

    background:
        rgba(
            0,
            0,
            0,
            0.78
        );

    backdrop-filter:
        blur(8px);

    -webkit-backdrop-filter:
        blur(8px);

    display: none;

    align-items: center;

    justify-content: center;

    padding: 20px;

    z-index: 99999;

}


.tc-login-popup-overlay.show {

    display: flex;

}


/* =====================================================
   POPUP
===================================================== */

.tc-login-popup {

    width: 100%;

    max-width: 440px;

    background:
        linear-gradient(
            145deg,
            #191919 0%,
            #0f0f0f 100%
        );

    border:
        1px solid
        rgba(
            212,
            175,
            55,
            0.45
        );

    border-radius: 20px;

    padding:
        35px 30px 30px;

    position: relative;

    text-align: center;

    box-shadow:
        0 25px 80px
        rgba(
            0,
            0,
            0,
            0.75
        );

    animation:
        tcPopupIn
        0.3s
        ease;

}


@keyframes tcPopupIn {

    from {

        opacity: 0;

        transform:
            translateY(20px)
            scale(0.96);

    }

    to {

        opacity: 1;

        transform:
            translateY(0)
            scale(1);

    }

}


/* =====================================================
   POPUP CLOSE
===================================================== */

.tc-popup-close {

    position: absolute;

    top: 14px;

    right: 16px;

    width: 34px;

    height: 34px;

    border-radius: 50%;

    border:
        1px solid
        rgba(
            255,
            255,
            255,
            0.15
        );

    background:
        rgba(
            255,
            255,
            255,
            0.05
        );

    color:
        #ffffff;

    cursor:
        pointer;

    display: flex;

    align-items: center;

    justify-content: center;

    font-size: 14px;

    transition:
        all 0.25s ease;

}


.tc-popup-close:hover {

    background:
        var(--tc-gold);

    color:
        #000000;

    border-color:
        var(--tc-gold);

}


/* =====================================================
   POPUP ICON
===================================================== */

.tc-popup-icon {

    width: 70px;

    height: 70px;

    border-radius: 50%;

    margin:
        0 auto 20px;

    display: flex;

    align-items: center;

    justify-content: center;

    background:
        rgba(
            212,
            175,
            55,
            0.12
        );

    border:
        1px solid
        rgba(
            212,
            175,
            55,
            0.35
        );

    color:
        var(--tc-gold);

    font-size: 28px;

}


/* =====================================================
   POPUP TITLE
===================================================== */

.tc-popup-title {

    color:
        #ffffff;

    font-size: 25px;

    font-weight: 800;

    margin:
        0 0 10px;

}


.tc-popup-text {

    color:
        #a8a8a8;

    font-size: 14px;

    line-height: 1.6;

    margin:
        0 auto 25px;

    max-width: 340px;

}


/* =====================================================
   POPUP REGISTER BUTTON
===================================================== */

.tc-popup-register-btn {

    width: 100%;

    display: flex;

    align-items: center;

    justify-content: center;

    gap: 9px;

    padding:
        13px 18px;

    border-radius: 9px;

    background:
        linear-gradient(
            135deg,
            #d4af37 0%,
            #aa8214 100%
        );

    color:
        #000000 !important;

    font-size: 14px;

    font-weight: 800;

    text-decoration:
        none !important;

    border: none;

    transition:
        all 0.3s ease;

    margin-bottom: 11px;

}


.tc-popup-register-btn:hover {

    background:
        linear-gradient(
            135deg,
            #ffc107 0%,
            #d4af37 100%
        );

    transform:
        translateY(-2px);

    box-shadow:
        0 7px 20px
        rgba(
            212,
            175,
            55,
            0.35
        );

}


/* =====================================================
   POPUP LOGIN BUTTON
===================================================== */

.tc-popup-login-btn {

    width: 100%;

    display: flex;

    align-items: center;

    justify-content: center;

    gap: 9px;

    padding:
        13px 18px;

    border-radius: 9px;

    background:
        #1e1e1e;

    color:
        #ffffff !important;

    font-size: 14px;

    font-weight: 700;

    text-decoration:
        none !important;

    border:
        1px solid
        rgba(
            255,
            255,
            255,
            0.18
        );

    transition:
        all 0.3s ease;

}


.tc-popup-login-btn:hover {

    color:
        var(--tc-gold) !important;

    border-color:
        var(--tc-gold);

    background:
        rgba(
            212,
            175,
            55,
            0.08
        );

}


/* =====================================================
   RESPONSIVE 992px
===================================================== */

@media (max-width: 992px) {

    .tc-detail-grid {

        grid-template-columns:
            1fr;

        gap: 30px;

    }


    .tc-gallery-box {

        height: 400px;

    }


    .tc-gallery-img {

        width: 320px;

        height: 320px;

    }

}


/* =====================================================
   RESPONSIVE 576px
===================================================== */

@media (max-width: 576px) {

    .tc-details-section {

        padding:
            25px 0 60px 0;

    }


    .tc-gallery-box {

        height: 350px;

        padding: 20px;

    }


    .tc-gallery-img {

        width: 280px;

        height: 280px;

    }


    .tc-info-box {

        padding:
            24px 18px;

    }


    .tc-info-title {

        font-size: 24px;

    }


    .tc-info-price {

        font-size: 28px;

    }


    .tc-specs-grid {

        grid-template-columns:
            1fr;

    }


    .tc-btn-group-row {

        flex-direction: column;

        align-items: stretch;

    }


    .tc-btn-cart-primary {

        width: 100%;


        min-width: 0;

    }


    .tc-btn-wish-action {

        width: 100%;

    }


    .tc-login-popup {

        padding:
            30px 20px 25px;

    }


    .tc-popup-title {

        font-size: 22px;

    }

}

</style>


<!-- =====================================================
     PRODUCT DETAILS
===================================================== -->

<section class="tc-details-section">

<div class="container">


<!-- =====================================================
     BREADCRUMBS
===================================================== -->

<div class="tc-breadcrumbs">

<a href="index.php">

<i class="fa-solid fa-house"></i>

Home

</a>


<span>/</span>


<a href="brand.php">

<i class="fa-solid fa-border-all"></i>

Watches

</a>


<span>/</span>


<a href="brand.php?brand=<?php echo urlencode($brand); ?>">

<?php

echo htmlspecialchars(
    $brand
);

?>

</a>


<span>/</span>


<span class="curr">

<?php

echo htmlspecialchars(
    $product_name
);

?>

</span>

</div>


<!-- =====================================================
     DETAIL GRID
===================================================== -->

<div class="tc-detail-grid">


<!-- =====================================================
     LEFT - PRODUCT IMAGE
===================================================== -->

<div class="tc-gallery-box">


<!-- =====================================================
     BADGES
===================================================== -->

<div class="tc-gallery-badges">


<span class="tc-gallery-brand-badge">

<?php

echo htmlspecialchars(
    $brand
);

?>

</span>


<?php

if ($stock > 0) {

?>

<span
    class="tc-stock-indicator in"
    style="
        padding:4px 10px;
        font-size:11px;
    "
>

<i class="fa-solid fa-circle-check"></i>

In Stock

</span>

<?php

} else {

?>

<span
    class="tc-stock-indicator out"
    style="
        padding:4px 10px;
        font-size:11px;
    "
>

<i class="fa-solid fa-circle-xmark"></i>

Out of Stock

</span>

<?php

}

?>

</div>


<!-- =====================================================
     WISHLIST
===================================================== -->

<?php

if ($is_wishlisted) {

?>

<a

    href="wishlist.php"

    class="tc-gallery-wishlist-btn added"

    title="View in Wishlist"

>

<i class="fa-solid fa-heart"></i>

</a>


<?php

} else {

?>

<a

    href="add-to-wishlist.php?id=<?php echo $product_id; ?>"

    class="tc-gallery-wishlist-btn"

    title="Add to Wishlist"

>

<i class="fa-regular fa-heart"></i>

</a>


<?php

}

?>


<!-- =====================================================
     PRODUCT IMAGE
===================================================== -->

<img

    src="upload/<?php echo htmlspecialchars($image); ?>"

    alt="<?php echo htmlspecialchars($product_name); ?>"

    class="tc-gallery-img"

    loading="lazy"

    onerror="
        this.onerror=null;
        this.src='assets/img/logo/logo.png';
    "

>


</div>


<!-- =====================================================
     RIGHT - PRODUCT INFORMATION
===================================================== -->

<div class="tc-info-box">


<span class="tc-info-brand">

<?php

echo htmlspecialchars(
    $brand
);

?>

Horology

</span>


<h1 class="tc-info-title">

<?php

echo htmlspecialchars(
    $product_name
);

?>

</h1>


<?php

if (!empty($collection)) {

?>

<div class="tc-info-collection">

<i
    class="fa-solid fa-layer-group"
    style="color:var(--tc-gold);"
></i>

<span>

<?php

echo htmlspecialchars(
    $collection
);

?>

Collection

</span>

</div>

<?php

}

?>


<!-- =====================================================
     PRICE AND STOCK
===================================================== -->

<div class="tc-info-price-box">


<div class="tc-info-price">

<i class="fa-solid fa-indian-rupee-sign"></i>

<span>

<?php

echo number_format(
    $price
);

?>

</span>

</div>


<?php

if ($stock > 0) {

?>

<span class="tc-stock-indicator in">

<i class="fa-solid fa-circle-check"></i>

<?php

echo $stock;

?>

 In Stock

</span>

<?php

} else {

?>

<span class="tc-stock-indicator out">

<i class="fa-solid fa-circle-xmark"></i>

Out of Stock

</span>

<?php

}

?>

</div>


<!-- =====================================================
     PURCHASE FORM
===================================================== -->

<?php

if ($stock > 0) {

?>


<form

    action="add-to-cart.php"

    method="GET"

    class="tc-purchase-form"

    id="tcCartForm"

>


<input

    type="hidden"

    name="id"

    value="<?php echo $product_id; ?>"

>


<div class="tc-qty-group">


<label class="tc-qty-label">

<i
    class="fa-solid fa-cart-shopping"
    style="color:var(--tc-gold);"
></i>

Quantity:

</label>


<input

    type="number"

    name="qty"

    value="1"

    min="1"

    max="<?php echo $stock; ?>"

    class="tc-qty-input"

    required

>


</div>


<div class="tc-btn-group-row">


<!-- =====================================================
     ADD TO CART
===================================================== -->

<?php

if ($user_id > 0) {

?>

<button

    type="submit"

    class="tc-btn-cart-primary"

>

<i class="fa-solid fa-cart-plus"></i>

Add To Cart

</button>


<?php

} else {

?>

<button

    type="button"

    class="tc-btn-cart-primary"

    id="tcGuestCartButton"

>

<i class="fa-solid fa-cart-plus"></i>

Add To Cart

</button>


<?php

}

?>


<!-- =====================================================
     WISHLIST ACTION
===================================================== -->

<?php

if ($is_wishlisted) {

?>

<a

    href="wishlist.php"

    class="tc-btn-wish-action"

    style="
        border-color:var(--tc-gold);
        color:var(--tc-gold) !important;
    "

>

<i
    class="fa-solid fa-heart"
    style="color:var(--tc-gold);"
></i>

In Wishlist

</a>


<?php

} else {

?>

<a

    href="add-to-wishlist.php?id=<?php echo $product_id; ?>"

    class="tc-btn-wish-action"

>

<i class="fa-regular fa-heart"></i>

Save to Wishlist

</a>


<?php

}

?>


</div>


</form>


<?php

} else {

?>


<!-- =====================================================
     OUT OF STOCK
===================================================== -->

<div style="margin-bottom:24px;">


<span

    class="tc-btn-cart-disabled"

    style="
        display:inline-flex;
        width:100%;
        margin-bottom:12px;
    "

>

<i class="fa-solid fa-ban"></i>

Product Out of Stock

</span>


<div class="tc-btn-group-row">


<?php

if ($is_wishlisted) {

?>

<a

    href="wishlist.php"

    class="tc-btn-wish-action"

    style="
        border-color:var(--tc-gold);
        color:var(--tc-gold) !important;
    "

>

<i
    class="fa-solid fa-heart"
    style="color:var(--tc-gold);"
></i>

In Wishlist

</a>


<?php

} else {

?>

<a

    href="add-to-wishlist.php?id=<?php echo $product_id; ?>"

    class="tc-btn-wish-action"

>

<i class="fa-regular fa-heart"></i>

Save to Wishlist

</a>


<?php

}

?>


</div>


</div>


<?php

}

?>


<!-- =====================================================
     BACK TO BRAND
===================================================== -->

<a

    href="brand.php?brand=<?php echo urlencode($brand); ?>"

    class="tc-btn-back-action"

>

<i class="fa-solid fa-arrow-left"></i>

Back to <?php echo htmlspecialchars($brand); ?> Watches

</a>


<!-- =====================================================
     PRODUCT FEATURES
===================================================== -->

<div class="tc-specs-grid">


<div class="tc-spec-item">

<i class="fa-solid fa-gem"></i>

<span>

Sapphire Crystal Glass

</span>

</div>


<div class="tc-spec-item">

<i class="fa-solid fa-water"></i>

<span>

50M Water Resistant

</span>

</div>


<div class="tc-spec-item">

<i class="fa-solid fa-shield-halved"></i>

<span>

2-Year International Warranty

</span>

</div>


<div class="tc-spec-item">

<i class="fa-solid fa-truck-fast"></i>

<span>

Free Express Shipping

</span>

</div>


</div>


</div>


</div>


</div>

</section>


<!-- =====================================================
     LOGIN / REGISTER POPUP
===================================================== -->

<div

    class="tc-login-popup-overlay"

    id="tcLoginPopup"

>


<div class="tc-login-popup">


<button

    type="button"

    class="tc-popup-close"

    id="tcPopupClose"

    aria-label="Close"

>

<i class="fa-solid fa-xmark"></i>

</button>


<div class="tc-popup-icon">

<i class="fa-solid fa-user-lock"></i>

</div>


<h2 class="tc-popup-title">

Login Required

</h2>


<p class="tc-popup-text">

Please register or login to your account before adding this luxury watch to your cart.

</p>


<a

    href="ragister.php"

    class="tc-popup-register-btn"

>

<i class="fa-solid fa-user-plus"></i>

Register Now

</a>


<a

    href="login.php"

    class="tc-popup-login-btn"

>

<i class="fa-solid fa-right-to-bracket"></i>

Already have an account? Login

</a>


</div>

</div>


<!-- =====================================================
     JAVASCRIPT
===================================================== -->

<script>

document.addEventListener(
    "DOMContentLoaded",
    function () {

        const cartButton =
            document.getElementById(
                "tcGuestCartButton"
            );


        const popup =
            document.getElementById(
                "tcLoginPopup"
            );


        const closeButton =
            document.getElementById(
                "tcPopupClose"
            );


        /* =================================================
           OPEN POPUP
        ================================================= */

        if (
            cartButton &&
            popup
        ) {

            cartButton.addEventListener(
                "click",
                function () {

                    popup.classList.add(
                        "show"
                    );

                    document.body.style.overflow =
                        "hidden";

                }
            );

        }


        /* =================================================
           CLOSE POPUP
        ================================================= */

        function closePopup() {

            if (popup) {

                popup.classList.remove(
                    "show"
                );

                document.body.style.overflow =
                    "";

            }

        }


        if (closeButton) {

            closeButton.addEventListener(
                "click",
                closePopup
            );

        }


        /* =================================================
           CLOSE OUTSIDE
        ================================================= */

        if (popup) {

            popup.addEventListener(
                "click",
                function (event) {

                    if (
                        event.target === popup
                    ) {

                        closePopup();

                    }

                }
            );

        }


        /* =================================================
           ESC CLOSE
        ================================================= */

        document.addEventListener(
            "keydown",
            function (event) {

                if (
                    event.key === "Escape"
                ) {

                    closePopup();

                }

            }
        );

    }

);

</script>


<?php

/* =====================================================
   FOOTER
===================================================== */

include("footer.php");

?>
