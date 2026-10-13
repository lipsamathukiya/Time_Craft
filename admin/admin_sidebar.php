<?php
/* =====================================================
   TIME CRAFT - SHARED ADMIN SIDEBAR (ACCORDION DROPDOWN)
===================================================== */
if (!isset($active_page)) $active_page = '';

function _sb_active($page, $active) {
    return ($page === $active) ? ' active' : '';
}
function _sb_dd_active($pages, $active) {
    return in_array($active, (array)$pages) ? ' active' : '';
}
?>

<!-- Font Awesome: Both CDN & Local Fallback for 100% Guaranteed Icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
<link rel="stylesheet" href="assets/css/fontawesome-all.min.css">
<link rel="stylesheet" href="../assets/css/fontawesome-all.min.css">

<!-- HAMBURGER (Mobile Only) -->
<button class="hamburger-btn" id="hamburgerBtn" onclick="adminSidebarToggle()" aria-label="Toggle Menu">
    <i class="fa fa-bars fas fa-solid"></i>
</button>

<!-- OVERLAY (Mobile) -->
<div class="sidebar-overlay" id="sidebarOverlay" onclick="adminSidebarClose()"></div>

<!-- SIDEBAR -->
<div class="sidebar" id="adminSidebar">

    <h2><i class="fa fa-clock fas fa-solid"></i> Time Craft</h2>

    <ul class="sidebar-nav">
        <!-- DASHBOARD -->
        <li class="<?php echo _sb_active('dashboard', $active_page); ?>">
            <a href="dashboard.php">
                <i class="fa fa-tachometer-alt fa-gauge fas fa-solid"></i>
                <span>Dashboard</span>
            </a>
        </li>

        <!-- CUSTOMER -->
        <li class="dropdown<?php echo _sb_dd_active(['customers'], $active_page); ?>">
            <a href="javascript:void(0)" onclick="toggleDropdown(this)">
                <i class="fa fa-users fas fa-solid"></i>
                <span>Customer</span>
                <i class="fa fa-chevron-right fas fa-solid arrow"></i>
            </a>
            <ul class="dropdown-menu">
                <li class="<?php echo _sb_active('customers', $active_page); ?>">
                    <a href="customer.php">
                        <i class="fa fa-eye fas fa-solid"></i>
                        <span>View Customers</span>
                    </a>
                </li>
            </ul>
        </li>

        <!-- PRODUCTS -->
        <li class="dropdown<?php echo _sb_dd_active(['products','add_product'], $active_page); ?>">
            <a href="javascript:void(0)" onclick="toggleDropdown(this)">
                <i class="fa fa-cube fa-box fas fa-solid"></i>
                <span>Products</span>
                <i class="fa fa-chevron-right fas fa-solid arrow"></i>
            </a>
            <ul class="dropdown-menu">
                <li class="<?php echo _sb_active('add_product', $active_page); ?>">
                    <a href="add-product.php">
                        <i class="fa fa-plus fas fa-solid"></i>
                        <span>Add Product</span>
                    </a>
                </li>
                <li class="<?php echo _sb_active('products', $active_page); ?>">
                    <a href="product.php">
                        <i class="fa fa-eye fas fa-solid"></i>
                        <span>View Products</span>
                    </a>
                </li>
            </ul>
        </li>

        <!-- ORDERS -->
        <li class="dropdown<?php echo _sb_dd_active(['orders'], $active_page); ?>">
            <a href="javascript:void(0)" onclick="toggleDropdown(this)">
                <i class="fa fa-shopping-cart fa-cart-shopping fas fa-solid"></i>
                <span>Orders</span>
                <i class="fa fa-chevron-right fas fa-solid arrow"></i>
            </a>
            <ul class="dropdown-menu">
                <li class="<?php echo _sb_active('orders', $active_page); ?>">
                    <a href="order.php">
                        <i class="fa fa-eye fas fa-solid"></i>
                        <span>View Orders</span>
                    </a>
                </li>
            </ul>
        </li>

        <!-- BRANDS -->
        <li class="dropdown<?php echo _sb_dd_active(['brands','add_brand'], $active_page); ?>">
            <a href="javascript:void(0)" onclick="toggleDropdown(this)">
                <i class="fa fa-tags fas fa-solid"></i>
                <span>Brands</span>
                <i class="fa fa-chevron-right fas fa-solid arrow"></i>
            </a>
            <ul class="dropdown-menu">
                <li class="<?php echo _sb_active('add_brand', $active_page); ?>">
                    <a href="add_brand.php">
                        <i class="fa fa-plus fas fa-solid"></i>
                        <span>Add Brand</span>
                    </a>
                </li>
                <li class="<?php echo _sb_active('brands', $active_page); ?>">
                    <a href="brand.php">
                        <i class="fa fa-eye fas fa-solid"></i>
                        <span>View Brand</span>
                    </a>
                </li>
            </ul>
        </li>

        <!-- WISHLIST -->
        <li>
            <a href="dashboard.php">
                <i class="fa fa-heart fas fa-solid"></i>
                <span>Wishlist</span>
            </a>
        </li>

        <!-- LOGOUT -->
        <li class="logout-li">
            <a href="logout.php">
                <i class="fa fa-sign-out-alt fa-right-from-bracket fas fa-solid"></i>
                <span>Logout</span>
            </a>
        </li>
    </ul>

</div>

<script>
function toggleDropdown(el) {
    var li = el.closest('li.dropdown');
    if (!li) return;
    var isOpen = li.classList.contains('active');
    
    // Close other dropdowns
    var allDropdowns = document.querySelectorAll('.sidebar ul.sidebar-nav > li.dropdown');
    allDropdowns.forEach(function(d) {
        if (d !== li) {
            d.classList.remove('active');
        }
    });

    // Toggle current
    if (isOpen) {
        li.classList.remove('active');
    } else {
        li.classList.add('active');
    }
}

function adminSidebarToggle() {
    var sb = document.getElementById('adminSidebar');
    var ov = document.getElementById('sidebarOverlay');
    if (sb) sb.classList.toggle('open');
    if (ov) ov.classList.toggle('active');
}
function adminSidebarClose() {
    var sb = document.getElementById('adminSidebar');
    var ov = document.getElementById('sidebarOverlay');
    if (sb) sb.classList.remove('open');
    if (ov) ov.classList.remove('active');
}
</script>