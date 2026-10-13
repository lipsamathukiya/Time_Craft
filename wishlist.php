<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include("conn.php");

/* =====================================================
   USER AUTHENTICATION (SUPPORT user_id AND uid)
   Redirect to login.php if not logged in
===================================================== */
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

/* =====================================================
   FETCH ONLY CURRENT USER'S WISHLIST PRODUCTS
===================================================== */
$tc_stmt = mysqli_prepare($conn, "
    SELECT 
        MAX(wishlist.id) AS wishlist_id,
        product.id AS product_id,
        product.product_name,
        product.brand,
        product.price,
        product.image,
        product.collection,
        product.stock
    FROM wishlist
    INNER JOIN product ON wishlist.product_id = product.id
    WHERE wishlist.user_id = ?
    GROUP BY product.id, product.product_name, product.brand, product.price, product.image, product.collection, product.stock
    ORDER BY MAX(wishlist.id) DESC
");

$tc_wishlist_count = 0;
$tc_wishlist_result = false;

if ($tc_stmt) {
    mysqli_stmt_bind_param($tc_stmt, "i", $user_id);
    mysqli_stmt_execute($tc_stmt);
    $tc_wishlist_result = mysqli_stmt_get_result($tc_stmt);
    if ($tc_wishlist_result) {
        $tc_wishlist_count = mysqli_num_rows($tc_wishlist_result);
    }
} else {
    die("Wishlist Query Error : " . mysqli_error($conn));
}

/* =====================================================
   INCLUDE HEADER (Uses unique variables to avoid collision)
===================================================== */
include("header.php");

?>

<!-- =====================================================
     WISHLIST CSS (TIME CRAFT LUXURY THEME)
===================================================== -->
<style>
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
    --tc-border-gold: rgba(212, 175, 55, 0.22);
    --tc-border-gold-hover: rgba(212, 175, 55, 0.7);
    --tc-danger: #ff4d4f;
    --tc-success: #2ecc71;
}

.tc-wishlist-section {
    background: linear-gradient(180deg, #0a0a0a 0%, #121212 100%);
    min-height: 80vh;
    padding: 50px 0 90px 0;
    color: var(--tc-text-primary);
    position: relative;
    overflow-x: hidden;
    width: 100%;
}

/* HERO */
.tc-wishlist-hero {
    text-align: center;
    margin-bottom: 35px;
    position: relative;
}

.tc-wishlist-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: rgba(212, 175, 55, 0.12);
    border: 1px solid var(--tc-border-gold);
    color: var(--tc-gold);
    font-size: 13px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1.5px;
    padding: 6px 20px;
    border-radius: 30px;
    margin-bottom: 14px;
}

.tc-wishlist-title {
    font-size: 38px;
    font-weight: 800;
    color: #ffffff;
    letter-spacing: 0.5px;
    margin-bottom: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 12px;
}

.tc-wishlist-title span {
    color: var(--tc-gold);
}

.tc-wishlist-desc {
    color: var(--tc-text-secondary);
    font-size: 16px;
    max-width: 580px;
    margin: 0 auto;
    line-height: 1.6;
}

/* TOP COUNTER BAR */
.tc-wishlist-bar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 15px;
    background: #141414;
    border: 1px solid var(--tc-border-gold);
    border-radius: 14px;
    padding: 16px 26px;
    margin-bottom: 35px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
}

.tc-wishlist-bar-left {
    display: flex;
    align-items: center;
    gap: 12px;
    font-size: 15px;
    color: #e0e0e0;
}

.tc-wishlist-count-badge {
    background: var(--tc-gold);
    color: #000000;
    font-weight: 800;
    font-size: 12px;
    padding: 4px 12px;
    border-radius: 20px;
    letter-spacing: 0.5px;
}

.tc-wishlist-back-link {
    color: var(--tc-gold);
    text-decoration: none;
    font-size: 14px;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s ease;
}

.tc-wishlist-back-link:hover {
    color: #ffffff;
    transform: translateX(-3px);
}

/* RESPONSIVE GRID (4 Desktop, 2 Tablet, 1 Mobile) */
.tc-wishlist-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 26px;
}

/* PRODUCT CARD */
.tc-wishlist-card {
    background: var(--tc-card-bg);
    border: 1px solid var(--tc-border-gold);
    border-radius: 16px;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    position: relative;
    transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1);
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.4);
}

.tc-wishlist-card:hover {
    transform: translateY(-8px);
    background: var(--tc-card-hover);
    border-color: var(--tc-border-gold-hover);
    box-shadow: 0 16px 36px rgba(212, 175, 55, 0.18);
}

.tc-card-img-box {
    position: relative;
    width: 100%;
    height: 250px;
    background: radial-gradient(circle, #222222 0%, #131313 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
    overflow: hidden;
    border-bottom: 1px solid rgba(255, 255, 255, 0.05);
}

.tc-card-img-link {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 100%;
    height: 100%;
    text-decoration: none;
}

.tc-card-img {
    max-width: 85%;
    max-height: 200px;
    object-fit: contain;
    transition: transform 0.45s ease;
    filter: drop-shadow(0 8px 18px rgba(0, 0, 0, 0.6));
}

.tc-wishlist-card:hover .tc-card-img {
    transform: scale(1.08);
}

.tc-card-badges {
    position: absolute;
    top: 14px;
    left: 14px;
    display: flex;
    flex-direction: column;
    gap: 6px;
    z-index: 2;
}

.tc-brand-tag {
    background: rgba(0, 0, 0, 0.8);
    backdrop-filter: blur(8px);
    border: 1px solid var(--tc-border-gold);
    color: var(--tc-gold);
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1.2px;
    padding: 4px 10px;
    border-radius: 6px;
}

.tc-stock-tag {
    font-size: 11px;
    font-weight: 600;
    padding: 3px 8px;
    border-radius: 6px;
    display: inline-flex;
    align-items: center;
    gap: 5px;
}

.tc-stock-tag.in-stock {
    background: rgba(46, 204, 113, 0.18);
    color: #2ecc71;
    border: 1px solid rgba(46, 204, 113, 0.45);
}

.tc-stock-tag.out-stock {
    background: rgba(231, 76, 60, 0.18);
    color: #e74c3c;
    border: 1px solid rgba(231, 76, 60, 0.45);
}

.tc-btn-remove-wishlist {
    position: absolute;
    top: 14px;
    right: 14px;
    width: 38px;
    height: 38px;
    border-radius: 50%;
    background: rgba(0, 0, 0, 0.75);
    backdrop-filter: blur(8px);
    border: 1px solid rgba(255, 255, 255, 0.18);
    color: #c0c0c0;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 15px;
    text-decoration: none !important;
    z-index: 3;
    transition: all 0.3s ease;
}

.tc-btn-remove-wishlist:hover {
    background: var(--tc-danger);
    color: #ffffff !important;
    border-color: var(--tc-danger);
    transform: rotate(90deg) scale(1.12);
    box-shadow: 0 4px 16px rgba(255, 77, 79, 0.55);
}

.tc-card-body {
    padding: 22px 20px;
    display: flex;
    flex-direction: column;
    flex-grow: 1;
    justify-content: space-between;
}

.tc-card-brand-name {
    color: var(--tc-gold);
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1.5px;
    display: block;
    margin-bottom: 4px;
}

.tc-card-title {
    font-size: 18px;
    font-weight: 700;
    line-height: 1.35;
    margin-bottom: 6px;
}

.tc-card-title a {
    color: #ffffff;
    text-decoration: none;
    transition: color 0.3s ease;
    display: -webkit-box;
    -webkit-line-clamp: 1;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.tc-card-title a:hover {
    color: var(--tc-gold);
}

.tc-card-collection {
    color: var(--tc-text-secondary);
    font-size: 13px;
    display: flex;
    align-items: center;
    gap: 6px;
    margin-bottom: 12px;
}

.tc-card-price {
    font-size: 22px;
    font-weight: 800;
    color: var(--tc-gold);
    display: flex;
    align-items: center;
    gap: 4px;
    margin-bottom: 20px;
}

.tc-card-price i {
    font-size: 18px;
}

.tc-card-actions {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-top: auto;
}

.tc-btn-add-cart {
    flex: 1;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    background: linear-gradient(135deg, #d4af37 0%, #aa8214 100%);
    color: #000000 !important;
    font-weight: 700;
    font-size: 14px;
    padding: 11px 16px;
    border-radius: 8px;
    text-decoration: none !important;
    border: none;
    transition: all 0.3s ease;
    box-shadow: 0 4px 14px rgba(212, 175, 55, 0.28);
    cursor: pointer;
}

.tc-btn-add-cart:hover {
    background: linear-gradient(135deg, #ffc107 0%, #d4af37 100%);
    color: #000000 !important;
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(255, 193, 7, 0.5);
}

.tc-btn-add-cart-disabled {
    flex: 1;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    background: #242424;
    color: #777777 !important;
    font-weight: 600;
    font-size: 14px;
    padding: 11px 16px;
    border-radius: 8px;
    border: 1px solid #383838;
    cursor: not-allowed;
    text-decoration: none !important;
}

.tc-btn-view-details {
    width: 44px;
    height: 42px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: transparent;
    border: 1px solid rgba(255, 255, 255, 0.22);
    color: #ffffff !important;
    border-radius: 8px;
    text-decoration: none !important;
    font-size: 15px;
    transition: all 0.3s ease;
    flex-shrink: 0;
}

.tc-btn-view-details:hover {
    border-color: var(--tc-gold);
    color: var(--tc-gold) !important;
    background: rgba(212, 175, 55, 0.12);
    transform: translateY(-2px);
}

/* EMPTY WISHLIST STATE */
.tc-empty-wishlist-card {
    max-width: 620px;
    margin: 40px auto 60px auto;
    background: #141414;
    border: 1px solid var(--tc-border-gold);
    border-radius: 20px;
    padding: 60px 30px;
    text-align: center;
    box-shadow: 0 12px 35px rgba(0, 0, 0, 0.5);
}

.tc-empty-icon-wrapper {
    width: 100px;
    height: 100px;
    margin: 0 auto 25px auto;
    border-radius: 50%;
    background: rgba(212, 175, 55, 0.08);
    border: 2px dashed var(--tc-border-gold);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--tc-gold);
    font-size: 44px;
    animation: tcPulse 2.5s infinite ease-in-out;
}

@keyframes tcPulse {
    0%, 100% {
        transform: scale(1);
        box-shadow: 0 0 0 0 rgba(212, 175, 55, 0.25);
    }
    50% {
        transform: scale(1.06);
        box-shadow: 0 0 24px 6px rgba(212, 175, 55, 0.2);
    }
}

.tc-empty-wishlist-card h3 {
    font-size: 28px;
    font-weight: 800;
    color: #ffffff;
    margin-bottom: 12px;
}

.tc-empty-wishlist-card p {
    color: var(--tc-text-secondary);
    font-size: 16px;
    max-width: 440px;
    margin: 0 auto 30px auto;
    line-height: 1.6;
}

.tc-empty-btn-group {
    display: flex;
    justify-content: center;
    gap: 15px;
    flex-wrap: wrap;
}

.tc-btn-gold {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    background: linear-gradient(135deg, #d4af37 0%, #aa8214 100%);
    color: #000000 !important;
    font-weight: 700;
    font-size: 15px;
    padding: 13px 28px;
    border-radius: 8px;
    text-decoration: none !important;
    transition: all 0.3s ease;
    box-shadow: 0 5px 18px rgba(212, 175, 55, 0.35);
}

.tc-btn-gold:hover {
    background: linear-gradient(135deg, #ffc107 0%, #d4af37 100%);
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(255, 193, 7, 0.5);
}

.tc-btn-outline {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    background: transparent;
    color: #ffffff !important;
    font-weight: 600;
    font-size: 15px;
    padding: 13px 28px;
    border-radius: 8px;
    border: 1px solid rgba(255, 255, 255, 0.25);
    text-decoration: none !important;
    transition: all 0.3s ease;
}

.tc-btn-outline:hover {
    border-color: var(--tc-gold);
    color: var(--tc-gold) !important;
    background: rgba(212, 175, 55, 0.1);
    transform: translateY(-3px);
}

/* RESPONSIVE BREAKPOINTS */
@media (max-width: 1200px) {
    .tc-wishlist-grid {
        grid-template-columns: repeat(3, 1fr);
        gap: 20px;
    }
}

@media (max-width: 992px) {
    .tc-wishlist-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
    }
    .tc-wishlist-title {
        font-size: 32px;
    }
    .tc-card-img-box {
        height: 230px;
    }
}

@media (max-width: 576px) {
    .tc-wishlist-section {
        padding: 30px 0 60px 0;
    }
    .tc-wishlist-grid {
        grid-template-columns: 1fr;
        gap: 18px;
    }
    .tc-wishlist-hero {
        margin-bottom: 25px;
    }
    .tc-wishlist-title {
        font-size: 26px;
    }
    .tc-wishlist-desc {
        font-size: 14px;
    }
    .tc-wishlist-bar {
        flex-direction: column;
        align-items: flex-start;
        padding: 12px 18px;
        gap: 10px;
    }
    .tc-card-img-box {
        height: 220px;
    }
    .tc-empty-wishlist-card {
        padding: 40px 20px;
        margin: 20px 0;
    }
    .tc-empty-btn-group {
        flex-direction: column;
        width: 100%;
    }
    .tc-btn-gold,
    .tc-btn-outline {
        width: 100%;
        justify-content: center;
    }
}
</style>

<!-- =====================================================
     WISHLIST PAGE HTML
===================================================== -->
<section class="tc-wishlist-section">
    <div class="container">

        <!-- ================= HERO HEADER ================= -->
        <div class="tc-wishlist-hero">
            <div class="tc-wishlist-badge">
            </div>
            <h1 class="tc-wishlist-title">
                <i class="fa-solid fa-heart" style="color: #d4af37;"></i> My <span>Wishlist</span>
            </h1>
            <p class="tc-wishlist-desc">
                Your personally saved luxury watches. Manage your favourite timepieces and add them to your cart.
            </p>
        </div>

        <?php if ($tc_wishlist_count > 0) { ?>

            <!-- ================= TOP COUNTER BAR ================= -->
            <div class="tc-wishlist-bar">
                <div class="tc-wishlist-bar-left">
                    <i class="fa-solid fa-bookmark" style="color: #d4af37;"></i>
                    <span>Saved Watches in Wishlist</span>
                    <span class="tc-wishlist-count-badge">
                        <?php echo $tc_wishlist_count; ?> <?php echo $tc_wishlist_count === 1 ? 'Item' : 'Items'; ?>
                    </span>
                </div>
                <a href="brand.php" class="tc-wishlist-back-link">
                    <i class="fa-solid fa-arrow-left"></i> Continue Shopping
                </a>
            </div>

            <!-- ================= USER WISHLIST GRID (ONLY SAVED ITEMS) ================= -->
            <div class="tc-wishlist-grid">

                <?php while ($tc_item = mysqli_fetch_assoc($tc_wishlist_result)) { 
                    $product_id = intval($tc_item['product_id']);
                    $wishlist_id = intval($tc_item['wishlist_id']);
                    $product_name = $tc_item['product_name'] ?? 'Luxury Watch';
                    $brand = $tc_item['brand'] ?? 'TimeCraft';
                    $collection = $tc_item['collection'] ?? '';
                    $price = isset($tc_item['price']) ? (float)$tc_item['price'] : 0.00;
                    $image = $tc_item['image'] ?? '';
                    $stock = isset($tc_item['stock']) ? intval($tc_item['stock']) : 0;
                ?>

                    <div class="tc-wishlist-card">
                        
                        <!-- CARD IMAGE BOX -->
                        <div class="tc-card-img-box">
                            
                            <!-- BRAND & STOCK BADGES -->
                            <div class="tc-card-badges">
                                <?php if (!empty($brand)) { ?>
                                    <span class="tc-brand-tag">
                                        <?php echo htmlspecialchars($brand); ?>
                                    </span>
                                <?php } ?>

                                <?php if ($stock > 0) { ?>
                                    <span class="tc-stock-tag in-stock">
                                        <i class="fa-solid fa-circle-check"></i> In Stock
                                    </span>
                                <?php } else { ?>
                                    <span class="tc-stock-tag out-stock">
                                        <i class="fa-solid fa-circle-xmark"></i> Out of Stock
                                    </span>
                                <?php } ?>
                            </div>

                            <!-- REMOVE FROM WISHLIST BUTTON -->
                            <a 
                                href="remove_wishlist.php?id=<?php echo $wishlist_id; ?>" 
                                class="tc-btn-remove-wishlist"
                                title="Remove from Wishlist"
                                onclick="return confirm('Remove <?php echo htmlspecialchars(addslashes($product_name)); ?> from your wishlist?');"
                            >
                                <i class="fa-solid fa-trash-can"></i>
                            </a>

                            <!-- PRODUCT IMAGE LINK -->
                            <a href="product_details.php?id=<?php echo $product_id; ?>" class="tc-card-img-link" title="<?php echo htmlspecialchars($product_name); ?>">
                                <img 
                                    src="upload/<?php echo htmlspecialchars($image); ?>" 
                                    alt="<?php echo htmlspecialchars($product_name); ?>"
                                    class="tc-card-img"
                                    onerror="this.onerror=null; this.src='assets/img/logo/logo.png';"
                                >
                            </a>

                        </div>

                        <!-- CARD BODY -->
                        <div class="tc-card-body">
                            
                            <div class="tc-card-meta">
                                <span class="tc-card-brand-name">
                                    <?php echo htmlspecialchars($brand); ?>
                                </span>

                                <h3 class="tc-card-title">
                                    <a href="product_details.php?id=<?php echo $product_id; ?>" title="<?php echo htmlspecialchars($product_name); ?>">
                                        <?php echo htmlspecialchars($product_name); ?>
                                    </a>
                                </h3>

                                <?php if (!empty($collection)) { ?>
                                    <div class="tc-card-collection">
                                        <i class="fa-solid fa-layer-group" style="color: #888888;"></i>
                                        <span><?php echo htmlspecialchars($collection); ?> Collection</span>
                                    </div>
                                <?php } ?>
                            </div>

                            <!-- PRICE -->
                            <div class="tc-card-price">
                                <i class="fa-solid fa-indian-rupee-sign"></i>
                                <span><?php echo number_format($price); ?></span>
                            </div>

                            <!-- ACTION BUTTONS -->
                            <div class="tc-card-actions">
                                <?php if ($stock > 0) { ?>
                                    <a href="add-to-cart.php?id=<?php echo $product_id; ?>" class="tc-btn-add-cart" title="Add to Cart">
                                        <i class="fa-solid fa-cart-plus"></i> Add to Cart
                                    </a>
                                <?php } else { ?>
                                    <span class="tc-btn-add-cart-disabled" title="Currently Out of Stock">
                                        <i class="fa-solid fa-circle-xmark"></i> Out of Stock
                                    </span>
                                <?php } ?>

                                <a href="product_details.php?id=<?php echo $product_id; ?>" class="tc-btn-view-details" title="View Details">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                            </div>

                        </div>

                    </div>

                <?php } ?>

            </div>

        <?php } else { ?>

            <!-- ================= EMPTY WISHLIST STATE (NO OTHER PRODUCTS) ================= -->
            <div class="tc-empty-wishlist-card">
                <div class="tc-empty-icon-wrapper">
                    <i class="fa-regular fa-heart"></i>
                </div>
                <h3>Your Wishlist is Empty</h3>
                <p>
                    You have not added any products to your wishlist yet. Explore our luxury watch collection and add your favorite timepieces.
                </p>
                <div class="tc-empty-btn-group">
                    <a href="brand.php" class="tc-btn-gold">
                        <i class="fa-solid fa-compass"></i> Explore Brands
                    </a>
                    <a href="index.php#mens" class="tc-btn-outline">
                        <i class="fa-solid fa-clock"></i> View Collections
                    </a>
                </div>
            </div>

        <?php } ?>

    </div>
</section>

<?php 

/* =====================================================
   INCLUDE FOOTER
===================================================== */
include("footer.php"); 

?>