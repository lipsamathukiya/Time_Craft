<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
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
   CURRENT URL TRACKING
===================================================== */

$current_page = basename($_SERVER['PHP_SELF']);

$current_url = $current_page;

if (!empty($_SERVER['QUERY_STRING'])) {

    $current_url .= '?' . $_SERVER['QUERY_STRING'];

}

$_SESSION['last_brand_page'] = $_SERVER['REQUEST_URI'];

/* =====================================================
   GET SELECTED BRAND
===================================================== */

$brand_filter = isset($_GET['brand'])
    ? trim($_GET['brand'])
    : '';


/* =====================================================
   FETCH ONLY SELECTED BRAND PRODUCTS
===================================================== */

$tc_p_result = false;

$product_count = 0;


if (!empty($brand_filter)) {

    $tc_p_stmt = mysqli_prepare(
        $conn,
        "SELECT *
         FROM product
         WHERE LOWER(TRIM(brand)) = LOWER(TRIM(?))
         ORDER BY id DESC"
    );


    if ($tc_p_stmt) {

        mysqli_stmt_bind_param(
            $tc_p_stmt,
            "s",
            $brand_filter
        );

        mysqli_stmt_execute(
            $tc_p_stmt
        );

        $tc_p_result = mysqli_stmt_get_result(
            $tc_p_stmt
        );

    }

}


if ($tc_p_result) {

    $product_count = mysqli_num_rows(
        $tc_p_result
    );

}


/* =====================================================
   FETCH USER WISHLIST PRODUCTS
===================================================== */

$user_wishlist_ids = [];


if ($user_id > 0) {

    $wishlist_stmt = mysqli_prepare(
        $conn,
        "SELECT product_id
         FROM wishlist
         WHERE user_id = ?"
    );


    if ($wishlist_stmt) {

        mysqli_stmt_bind_param(
            $wishlist_stmt,
            "i",
            $user_id
        );

        mysqli_stmt_execute(
            $wishlist_stmt
        );

        $wishlist_result = mysqli_stmt_get_result(
            $wishlist_stmt
        );


        if ($wishlist_result) {

            while (
                $w_row = mysqli_fetch_assoc(
                    $wishlist_result
                )
            ) {

                $user_wishlist_ids[] = intval(
                    $w_row['product_id']
                );

            }

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

    --tc-card-bg: #141414;

    --tc-card-hover: #1a1a1a;

    --tc-text-primary: #ffffff;

    --tc-text-secondary: #a8a8a8;

    --tc-border-gold:
        rgba(212, 175, 55, 0.22);

    --tc-border-gold-hover:
        rgba(212, 175, 55, 0.7);

    --tc-success: #2ecc71;

    --tc-danger: #e74c3c;

}


/* =====================================================
   CATALOG SECTION
===================================================== */

.tc-catalog-section {

    background:
        linear-gradient(
            180deg,
            #0a0a0a 0%,
            #121212 100%
        );

    min-height: 80vh;

    padding:
        50px 0 90px 0;

    color:
        var(--tc-text-primary);

    overflow-x: hidden;

    width: 100%;

}


/* =====================================================
   HERO
===================================================== */

.tc-catalog-hero {

    text-align: center;

    margin-bottom: 45px;

}


/* =====================================================
   TITLE
===================================================== */

.tc-catalog-title {

    font-size: 38px;

    font-weight: 800;

    color: #ffffff;

    letter-spacing: 0.5px;

    margin-bottom: 12px;

}


.tc-catalog-title span {

    color:
        var(--tc-gold);

}


/* =====================================================
   DESCRIPTION
===================================================== */

.tc-catalog-desc {

    color:
        var(--tc-text-secondary);

    font-size: 16px;

    max-width: 620px;

    margin:
        0 auto;

    line-height: 1.6;

}


/* =====================================================
   PRODUCT GRID
===================================================== */

.tc-catalog-grid {

    display: grid;

    grid-template-columns:
        repeat(4, 1fr);

    gap: 26px;

}


/* =====================================================
   PRODUCT CARD
===================================================== */

.tc-product-card {

    background:
        var(--tc-card-bg);

    border:
        1px solid
        var(--tc-border-gold);

    border-radius: 16px;

    overflow: hidden;

    display: flex;

    flex-direction: column;

    position: relative;

    transition:
        all 0.35s
        cubic-bezier(
            0.16,
            1,
            0.3,
            1
        );

    box-shadow:
        0 8px 24px
        rgba(
            0,
            0,
            0,
            0.4
        );

}


.tc-product-card:hover {

    transform:
        translateY(-8px);

    background:
        var(--tc-card-hover);

    border-color:
        var(--tc-border-gold-hover);

    box-shadow:
        0 16px 36px
        rgba(
            212,
            175,
            55,
            0.18
        );

}


/* =====================================================
   IMAGE BOX
===================================================== */

.tc-card-img-box {

    position: relative;

    width: 100%;

    height: 250px;

    background:
        radial-gradient(
            circle,
            #222222 0%,
            #131313 100%
        );

    display: flex;

    align-items: center;

    justify-content: center;

    padding: 20px;

    overflow: hidden;

    border-bottom:
        1px solid
        rgba(
            255,
            255,
            255,
            0.05
        );

}


/* =====================================================
   IMAGE LINK
===================================================== */

.tc-card-img-link {

    display: flex;

    align-items: center;

    justify-content: center;

    width: 100%;

    height: 100%;

    text-decoration: none;

}


/* =====================================================
   PRODUCT IMAGE
===================================================== */

.tc-card-img {

    max-width: 85%;

    max-height: 200px;

    object-fit: contain;

    transition:
        transform 0.45s ease;

    filter:
        drop-shadow(
            0 8px 18px
            rgba(
                0,
                0,
                0,
                0.6
            )
        );

}


.tc-product-card:hover
.tc-card-img {

    transform:
        scale(1.08);

}


/* =====================================================
   STOCK BADGE
===================================================== */

.tc-card-badges {

    position: absolute;

    top: 14px;

    left: 14px;

    z-index: 2;

}


/* =====================================================
   STOCK TAG
===================================================== */

.tc-stock-tag {

    font-size: 11px;

    font-weight: 700;

    padding:
        5px 10px;

    border-radius: 6px;

    display: inline-flex;

    align-items: center;

    gap: 5px;

}


/* =====================================================
   IN STOCK
===================================================== */

.tc-stock-tag.in-stock {

    background:
        rgba(
            46,
            204,
            113,
            0.16
        );

    color:
        #2ecc71;

    border:
        1px solid
        rgba(
            46,
            204,
            113,
            0.45
        );

}


/* =====================================================
   OUT OF STOCK
===================================================== */

.tc-stock-tag.out-stock {

    background:
        rgba(
            231,
            76,
            60,
            0.18
        );

    color:
        #e74c3c;

    border:
        1px solid
        rgba(
            231,
            76,
            60,
            0.45
        );

}


/* =====================================================
   WISHLIST
===================================================== */

.tc-btn-wishlist {

    position: absolute;

    top: 14px;

    right: 14px;

    width: 38px;

    height: 38px;

    border-radius: 50%;

    background:
        rgba(
            0,
            0,
            0,
            0.75
        );

    backdrop-filter:
        blur(8px);

    border:
        1px solid
        rgba(
            212,
            175,
            55,
            0.3
        );

    color:
        var(--tc-gold);

    display: flex;

    align-items: center;

    justify-content: center;

    font-size: 15px;

    text-decoration:
        none !important;

    z-index: 3;

    transition:
        all 0.3s ease;

}


.tc-btn-wishlist.added {

    background:
        rgba(
            212,
            175,
            55,
            0.2
        );

    border-color:
        var(--tc-gold);

    color:
        var(--tc-gold);

}


.tc-btn-wishlist:hover {

    background:
        var(--tc-gold);

    color:
        #000000 !important;

    transform:
        scale(1.12);

    box-shadow:
        0 4px 14px
        rgba(
            212,
            175,
            55,
            0.5
        );

}


/* =====================================================
   CARD BODY
===================================================== */

.tc-card-body {

    padding:
        22px 20px;

    display: flex;

    flex-direction: column;

    flex-grow: 1;

    justify-content:
        space-between;

}


/* =====================================================
   BRAND
===================================================== */

.tc-card-brand-name {

    color:
        var(--tc-gold);

    font-size: 12px;

    font-weight: 700;

    text-transform: uppercase;

    letter-spacing: 1.5px;

    display: block;

    margin-bottom: 5px;

}


/* =====================================================
   PRODUCT TITLE
===================================================== */

.tc-card-title {

    font-size: 18px;

    font-weight: 700;

    line-height: 1.35;

    margin-bottom: 12px;

}


.tc-card-title a {

    color: #ffffff;

    text-decoration: none;

    transition:
        color 0.3s ease;

    display: -webkit-box;

    -webkit-line-clamp: 2;

    -webkit-box-orient: vertical;

    overflow: hidden;

}


.tc-card-title a:hover {

    color:
        var(--tc-gold);

}


/* =====================================================
   PRICE
===================================================== */

.tc-card-price {

    font-size: 22px;

    font-weight: 800;

    color:
        var(--tc-gold);

    display: flex;

    align-items: center;

    gap: 4px;

    margin-bottom: 20px;

}


.tc-card-price i {

    font-size: 18px;

}


/* =====================================================
   ACTION BUTTON
===================================================== */

.tc-card-actions {

    display: flex;

    align-items: center;

    gap: 10px;

    margin-top: auto;

}


/* =====================================================
   VIEW DETAILS BUTTON
===================================================== */

.tc-btn-view {

    width: 100%;

    display: inline-flex;

    align-items: center;

    justify-content: center;

    gap: 8px;

    background:
        linear-gradient(
            135deg,
            #d4af37 0%,
            #aa8214 100%
        );

    color:
        #000000 !important;

    font-weight: 700;

    font-size: 14px;

    padding:
        11px 16px;

    border-radius: 8px;

    text-decoration:
        none !important;

    border: none;

    transition:
        all 0.3s ease;

    box-shadow:
        0 4px 14px
        rgba(
            212,
            175,
            55,
            0.28
        );

}


.tc-btn-view:hover {

    background:
        linear-gradient(
            135deg,
            #ffc107 0%,
            #d4af37 100%
        );

    color:
        #000000 !important;

    transform:
        translateY(-2px);

    box-shadow:
        0 6px 20px
        rgba(
            255,
            193,
            7,
            0.5
        );

}


/* =====================================================
   NO PRODUCTS CARD
===================================================== */

.tc-no-products-card {

    max-width: 600px;

    margin:
        40px auto;

    background:
        #141414;

    border:
        1px solid
        var(--tc-border-gold);

    border-radius: 20px;

    padding:
        60px 30px;

    text-align: center;

    box-shadow:
        0 12px 35px
        rgba(
            0,
            0,
            0,
            0.5
        );

}


.tc-no-products-card i {

    font-size: 45px;

    color:
        var(--tc-gold);

    margin-bottom: 20px;

}


.tc-no-products-card h3 {

    font-size: 26px;

    font-weight: 800;

    color: #ffffff;

    margin-bottom: 10px;

}


.tc-no-products-card p {

    color:
        var(--tc-text-secondary);

    font-size: 15px;

    margin-bottom: 0;

    line-height: 1.6;

}


/* =====================================================
   RESPONSIVE 1200px
===================================================== */

@media (max-width: 1200px) {

    .tc-catalog-grid {

        grid-template-columns:
            repeat(3, 1fr);

        gap: 20px;

    }

}


/* =====================================================
   RESPONSIVE 992px
===================================================== */

@media (max-width: 992px) {

    .tc-catalog-grid {

        grid-template-columns:
            repeat(2, 1fr);

        gap: 20px;

    }


    .tc-catalog-title {

        font-size: 32px;

    }


    .tc-card-img-box {

        height: 230px;

    }

}


/* =====================================================
   RESPONSIVE 576px
===================================================== */

@media (max-width: 576px) {

    .tc-catalog-section {

        padding:
            30px 0 60px 0;

    }


    .tc-catalog-grid {

        grid-template-columns:
            1fr;

        gap: 18px;

    }


    .tc-catalog-title {

        font-size: 26px;

    }


    .tc-catalog-desc {

        font-size: 14px;

        padding:
            0 15px;

    }


    .tc-card-img-box {

        height: 250px;

    }

}

</style>


<!-- =====================================================
     CATALOG
===================================================== -->

<section class="tc-catalog-section">

<div class="container">


<!-- =====================================================
     HERO
===================================================== -->

<div class="tc-catalog-hero">


<h1 class="tc-catalog-title">

<?php

if (!empty($brand_filter)) {

    echo htmlspecialchars(
        $brand_filter
    );

    echo " <span>Watches</span>";

} else {

    echo "Luxury ";

    echo "<span>Watch Collection</span>";

}

?>

</h1>


<p class="tc-catalog-desc">

<?php

if (!empty($brand_filter)) {

    echo "Discover our curated collection of authentic ";

    echo htmlspecialchars(
        $brand_filter
    );

    echo " luxury timepieces.";

} else {

    echo "Please select a brand to view its watches.";

}

?>

</p>


</div>


<?php

/* =====================================================
   PRODUCT FOUND
===================================================== */

if (
    !empty($brand_filter)
    &&
    $tc_p_result
    &&
    $product_count > 0
) {

?>


<!-- =====================================================
     PRODUCT GRID
===================================================== -->

<div class="tc-catalog-grid">


<?php

while (
    $p_row = mysqli_fetch_assoc(
        $tc_p_result
    )
) {

    $p_id = intval(
        $p_row['id']
    );


    $p_name =
        $p_row['product_name']
        ?? 'Luxury Watch';


    $p_brand =
        $p_row['brand']
        ?? 'TimeCraft';


    $p_price =
        isset($p_row['price'])
        ? (float)$p_row['price']
        : 0.00;


    $p_image =
        $p_row['image']
        ?? '';


    $p_stock =
        isset($p_row['stock'])
        ? intval($p_row['stock'])
        : 0;


    $is_wishlisted =
        in_array(
            $p_id,
            $user_wishlist_ids
        );

?>


<!-- =====================================================
     PRODUCT CARD
===================================================== -->

<div class="tc-product-card">


<!-- =====================================================
     IMAGE BOX
===================================================== -->

<div class="tc-card-img-box">


<!-- =====================================================
     STOCK STATUS
===================================================== -->

<div class="tc-card-badges">

<?php

if ($p_stock > 0) {

?>

<span class="tc-stock-tag in-stock">

<i class="fa-solid fa-circle-check"></i>

In Stock

</span>

<?php

} else {

?>

<span class="tc-stock-tag out-stock">

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

    class="tc-btn-wishlist added"

    title="Already in Wishlist"

>

<i
    class="fa-solid fa-heart"
    style="color:#d4af37;"
></i>

</a>


<?php

} else {

?>

<a

    href="add-to-wishlist.php?id=<?php echo $p_id; ?>"

    class="tc-btn-wishlist"

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

<a

    href="product_details.php?id=<?php echo $p_id; ?>"

    class="tc-card-img-link"

    title="<?php echo htmlspecialchars($p_name); ?>"

>

<img

    src="upload/<?php echo htmlspecialchars($p_image); ?>"

    alt="<?php echo htmlspecialchars($p_name); ?>"

    class="tc-card-img"

    onerror="
        this.onerror=null;
        this.src='assets/img/logo/logo.png';
    "

>

</a>


</div>


<!-- =====================================================
     CARD BODY
===================================================== -->

<div class="tc-card-body">


<!-- =====================================================
     BRAND
===================================================== -->

<span class="tc-card-brand-name">

<?php

echo htmlspecialchars(
    $p_brand
);

?>

</span>


<!-- =====================================================
     PRODUCT NAME
===================================================== -->

<h3 class="tc-card-title">

<a

    href="product_details.php?id=<?php echo $p_id; ?>"

    title="<?php echo htmlspecialchars($p_name); ?>"

>

<?php

echo htmlspecialchars(
    $p_name
);

?>

</a>

</h3>


<!-- =====================================================
     PRICE
===================================================== -->

<div class="tc-card-price">

<i class="fa-solid fa-indian-rupee-sign"></i>

<span>

<?php

echo number_format(
    $p_price
);

?>

</span>

</div>


<!-- =====================================================
     VIEW DETAILS ONLY
===================================================== -->

<div class="tc-card-actions">

<a

    href="product_details.php?id=<?php echo $p_id; ?>"

    class="tc-btn-view"

    title="View Details"

>

<i class="fa-solid fa-eye"></i>

View Details

</a>

</div>


</div>


</div>


<?php

}

?>


</div>


<?php

}


/* =====================================================
   NO BRAND SELECTED
===================================================== */

elseif (empty($brand_filter)) {

?>


<div class="tc-no-products-card">

<i class="fa-solid fa-tags"></i>

<h3>

No Brand Selected

</h3>

<p>

Please select a brand to view its watches.

</p>

</div>


<?php

}


/* =====================================================
   BRAND NOT FOUND
===================================================== */

else {

?>


<div class="tc-no-products-card">

<i class="fa-solid fa-clock-rotate-left"></i>

<h3>

No Watches Found

</h3>

<p>

No watches were found for

<strong style="color:#d4af37;">

<?php

echo htmlspecialchars(
    $brand_filter
);

?>

</strong>.

</p>

</div>


<?php

}

?>


</div>

</section>


<?php

/* =====================================================
   INCLUDE FOOTER
===================================================== */

include("footer.php");

?>
