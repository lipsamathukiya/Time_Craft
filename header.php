<?php

if (session_status() === PHP_SESSION_NONE)
{
    session_start();
}

include("conn.php");

?>

<meta name="description" content="Time Craft - Premium Watches">

<meta
    name="viewport"
    content="width=device-width, initial-scale=1"
>

<title>Time Craft </title>

<link rel="manifest" href="site.webmanifest">

<link
    rel="icon"
    type="image/png"
    href="assets/img/logo/icon.png"
>

<link rel="stylesheet" href="assets/css/bootstrap.min.css">
<link rel="stylesheet" href="assets/css/owl.carousel.min.css">
<link rel="stylesheet" href="assets/css/slicknav.css">
<link rel="stylesheet" href="assets/css/flaticon.css">
<link rel="stylesheet" href="assets/css/progressbar_barfiller.css">
<link rel="stylesheet" href="assets/css/gijgo.css">
<link rel="stylesheet" href="assets/css/animate.min.css">
<link rel="stylesheet" href="assets/css/animated-headline.css">
<link rel="stylesheet" href="assets/css/magnific-popup.css">
<link rel="stylesheet" href="assets/css/fontawesome-all.min.css">
<link rel="stylesheet" href="assets/css/themify-icons.css">
<link rel="stylesheet" href="assets/css/slick.css">
<link rel="stylesheet" href="assets/css/nice-select.css">
<link rel="stylesheet" href="assets/css/style.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.1.0/css/all.min.css" integrity="sha512-59mflmEHdfG3+iNat6Jg0HZPgYcAhZ7q0VO+q50sfkQDMBpoaSJfjJU2HuH5aBeMZyTwfKB54G8hratLP3Jsow==" crossorigin="anonymous" referrerpolicy="no-referrer" />

<style>

.header-area,
.main-header,
.header-sticky,
.header-bottom
{
    background:#000 !important;
}


.header-area
{
    position:relative;
    z-index:9999;
}


.header-area .container-fluid
{
    padding-left:30px;
    padding-right:30px;
}


.header-area .row
{
    display:flex !important;
    align-items:center !important;
}


.header-left
{
    display:flex !important;
    align-items:center !important;
    width:100%;
}


.logo
{
    flex-shrink:0;
    margin-right:50px;
}


.logo img
{
    width:180px !important;
    height:80px !important;
    object-fit:contain !important;
    display:block;
}


.main-menu
{
    display:flex !important;
    justify-content:center !important;
    flex:1;
}


.main-menu nav
{
    width:100%;
}


.main-menu > nav > ul
{
    display:flex !important;
    align-items:center !important;
    justify-content:center !important;
    width:100%;
    margin:0 !important;
    padding:0 !important;
    list-style:none !important;
}


.main-menu > nav > ul > li
{
    position:relative !important;
    display:block !important;
    margin:0 9px !important;
    padding:0 !important;
    list-style:none !important;
}


.main-menu > nav > ul > li > a
{
    display:block !important;
    padding:30px 5px !important;
    color:#fff !important;
    font-size:15px !important;
    font-weight:600 !important;
    text-decoration:none !important;
    white-space:nowrap;
    transition:0.3s;
}


.main-menu > nav > ul > li > a:hover
{
    color:#d4af37 !important;
}


.main-menu ul li ul.submenu
{
    position:absolute !important;
    top:100% !important;
    left:0 !important;
    width:220px !important;
    margin:0 !important;
    padding:8px 0 !important;
    background:#111 !important;
    border:1px solid #333 !important;
    box-shadow:0 8px 25px rgba(0,0,0,0.6);
    display:none !important;
    z-index:99999 !important;
    list-style:none !important;
}


.main-menu ul li:hover > ul.submenu
{
    display:block !important;
}


.main-menu ul li ul.submenu li
{
    display:block !important;
    width:100% !important;
    margin:0 !important;
    padding:0 !important;
}


.main-menu ul li ul.submenu li a
{
    display:block !important;
    width:100% !important;
    padding:10px 16px !important;
    color:#fff !important;
    font-size:14px !important;
    font-weight:500 !important;
    background:transparent !important;
    text-decoration:none !important;
    white-space:nowrap;
    transition:0.3s;
}


.main-menu ul li ul.submenu li a:hover
{
    background:#d4af37 !important;
    color:#000 !important;
}


.main-menu ul li ul.submenu hr
{
    border:0 !important;
    border-top:1px solid #444 !important;
    margin:5px 10px !important;
}


.header-icons
{
    display:flex !important;
    align-items:center !important;
    justify-content:flex-end !important;
    gap:18px !important;
    width:100%;
}


.icon-btn
{
    position:relative !important;
    display:flex !important;
    align-items:center !important;
    justify-content:center !important;
    width:32px !important;
    height:32px !important;
    color:#fff !important;
    font-size:18px !important;
    text-decoration:none !important;
    transition:0.3s;
}


.icon-btn:hover
{
    color:#d4af37 !important;
    transform:translateY(-2px);
}


.icon-btn.cart span
{
    position:absolute !important;
    top:-6px !important;
    right:-8px !important;
    width:18px !important;
    height:18px !important;
    display:flex !important;
    align-items:center !important;
    justify-content:center !important;
    border-radius:50% !important;
    background:#d4af37 !important;
    color:#000 !important;
    font-size:10px !important;
    font-weight:bold !important;
}


.welcome-user
{
    color:#d4af37 !important;
}


.mobile-menu-toggle
{
    display:none;
    width:45px;
    height:45px;
    border:1px solid #d4af37;
    border-radius:5px;
    background:#111;
    color:#fff;
    font-size:22px;
    align-items:center;
    justify-content:center;
    cursor:pointer;
    transition:0.3s;
}


.mobile-menu-toggle:hover
{
    background:#d4af37;
    color:#000;
}


.mobile-menu
{
    display:none;
    width:100%;
    background:#000;
    border-top:1px solid #333;
    padding:10px 20px 20px;
}


.mobile-menu.active
{
    display:block;
}


.mobile-menu > ul
{
    list-style:none;
    margin:0;
    padding:0;
    width:100%;
}


.mobile-menu > ul > li
{
    width:100%;
    border-bottom:1px solid #222;
}


.mobile-menu > ul > li > a
{
    display:flex;
    align-items:center;
    justify-content:space-between;
    width:100%;
    padding:15px 5px;
    color:#fff;
    font-size:15px;
    font-weight:600;
    text-decoration:none;
    transition:0.3s;
}


.mobile-menu > ul > li > a:hover
{
    color:#d4af37;
}


.mobile-dropdown
{
    display:none;
    list-style:none;
    margin:0;
    padding:5px 0 10px 15px;
    background:#111;
}


.mobile-dropdown.active
{
    display:block;
}


.mobile-dropdown li
{
    border-bottom:1px solid #222;
}


.mobile-dropdown li:last-child
{
    border-bottom:none;
}


.mobile-dropdown li a
{
    display:block;
    padding:11px 10px;
    color:#ccc;
    font-size:14px;
    text-decoration:none;
    transition:0.3s;
}


.mobile-dropdown li a:hover
{
    color:#d4af37;
    padding-left:15px;
}


.mobile-menu-icons
{
    display:flex;
    align-items:center;
    justify-content:center;
    gap:25px;
    padding:20px 0 5px;
    border-top:1px solid #333;
    margin-top:10px;
}


.mobile-menu-icons .icon-btn
{
    width:40px !important;
    height:40px !important;
    font-size:19px !important;
}


@media (max-width:991px)
{

    .header-area .container-fluid
    {
        padding-left:15px;
        padding-right:15px;
    }


    .header-area .row
    {
        min-height:90px;
    }


    .main-menu
    {
        display:none !important;
    }


    .header-left
    {
        width:auto !important;
    }


    .logo
    {
        margin-right:0;
    }


    .logo img
    {
        width:155px !important;
        height:70px !important;
    }


    .header-icons
    {
        display:none !important;
    }


    .mobile-menu-toggle
    {
        display:flex;
    }


    .mobile-header-right
    {
        display:flex;
        align-items:center;
        justify-content:flex-end;
    }

}


@media (max-width:575px)
{

    .header-area .row
    {
        min-height:75px;
    }


    .logo img
    {
        width:135px !important;
        height:60px !important;
    }


    .mobile-menu
    {
        padding-left:15px;
        padding-right:15px;
    }

}


@media (min-width:1200px)
{

    .logo
    {
        margin-right:70px;
    }


    .main-menu > nav > ul > li
    {
        margin:0 11px !important;
    }

}

</style>


<header class="header-area">

    <div class="main-header">

        <div class="header-bottom">

            <div class="container-fluid">

                <div class="row align-items-center">

                    <div class="col-xl-9 col-lg-9 col-md-8 col-sm-8 col-8">

                        <div class="header-left">

                            <div class="logo">

                                <a href="index.php">

                                    <img
                                        src="assets/img/logo/logo.png"
                                        alt="Time Craft Logo"
                                    >

                                </a>

                            </div>


                            <div class="main-menu">

                                <nav>

                                    <ul id="navigation">


                                        <li>

                                            <a href="index.php">
                                                Home
                                            </a>

                                        </li>


                                        <li>

                                            <a href="#">
                                                Collections
                                            </a>


                                            <ul class="submenu">


                                                <li>

                                                    <a href="index.php#mens">
                                                        Men's Collection
                                                    </a>

                                                </li>


                                                <li>

                                                    <a href="index.php#womens">
                                                        Women's Collection
                                                    </a>

                                                </li>


                                                <li>

                                                    <a href="index.php#luxury">
                                                        Luxury Collection
                                                    </a>

                                                </li>




                                            </ul>

                                        </li>


                                        <li>

                                            <a href="#">
                                                Brands
                                            </a>


                                            <ul class="submenu">


                                                <?php

                                                $qry = "SELECT * FROM brand";

                                                $result =
                                                    mysqli_query(
                                                        $conn,
                                                        $qry
                                                    );


                                                if ($result)
                                                {

                                                    while (
                                                        $row =
                                                        mysqli_fetch_assoc(
                                                            $result
                                                        )
                                                    )
                                                    {

                                                        $brand_name =
                                                            $row['Brand Name'];

                                                ?>

                                                        <li>

                                                            <a
                                                                href="brand.php?brand=<?php echo urlencode($brand_name); ?>"
                                                            >

                                                                <?php

                                                                echo htmlspecialchars(
                                                                    $brand_name
                                                                );

                                                                ?>

                                                            </a>

                                                        </li>

                                                <?php

                                                    }

                                                }

                                                ?>


                                            </ul>

                                        </li>


                                        <li>

                                            <a href="about.php">
                                                About
                                            </a>

                                        </li>


                                        <?php

                                        if (
                                            isset($_SESSION['user_id']) &&
                                            !empty($_SESSION['user_id'])
                                        )
                                        {

                                            $username =
                                                $_SESSION['user_name']
                                                ?? ($_SESSION['name'] ?? 'User');

                                        ?>


                                            <li>

                                                <a href="#">

                                                    Welcome,
                                                    <?php

                                                    echo htmlspecialchars(
                                                        $username
                                                    );

                                                    ?>

                                                </a>

                                            </li>


                                            <li>

                                                <a href="logout.php">
                                                    Logout
                                                </a>

                                            </li>


                                        <?php

                                        }
                                        else
                                        {

                                        ?>


                                            <li>

                                                <a href="login.php">
                                                    Login
                                                </a>

                                            </li>


                                        <?php

                                        }

                                        ?>


                                    </ul>

                                </nav>

                            </div>

                        </div>

                    </div>


                    <div class="col-xl-3 col-lg-3 col-md-4 col-sm-4 col-4">


                        <div class="mobile-header-right">


                            <div class="header-icons">


                                <a
                                    href="wishlist.php"
                                    class="icon-btn"
                                    title="Wishlist"
                                >

                                    <i class="far fa-heart"></i>

                                </a>
								<a
                                    href="contact.php"
                                    class="icon-btn"
                                    title="contact"
                                >

                                  <i class="fa-solid fa-phone"></i>

                                </a>


                                <a
                                    href="my_order.php"
                                    class="icon-btn"
                                    title="My Orders"
                                >

                                    <i class="fas fa-shopping-bag"></i>

                                </a>


                                <a
                                    href="cart.php"
                                    class="icon-btn cart"
                                    title="Cart"
                                >

                                    <i class="fas fa-shopping-cart"></i>

                                </a>


                            </div>


                            <button
                                type="button"
                                class="mobile-menu-toggle"
                                id="mobileMenuToggle"
                                aria-label="Open Menu"
                                aria-expanded="false"
                            >

                                <i class="fas fa-bars"></i>

                            </button>


                        </div>

                    </div>

                </div>


                <div
                    class="mobile-menu"
                    id="mobileMenu"
                >


                    <ul>


                        <li>

                            <a href="index.php">

                                <span>
                                    Home
                                </span>

                            </a>

                        </li>


                        <li>


                            <a
                                href="#"
                                class="mobile-dropdown-toggle"
                                data-target="collectionsMenu"
                            >

                                <span>
                                    Collections
                                </span>

                                <i class="fas fa-chevron-down"></i>

                            </a>


                            <ul
                                class="mobile-dropdown"
                                id="collectionsMenu"
                            >


                                <li>

                                    <a href="index.php#mens">
                                        Men's Collection
                                    </a>

                                </li>


                                <li>

                                    <a href="index.php#womens">
                                        Women's Collection
                                    </a>

                                </li>


                                <li>

                                    <a href="index.php#luxury">
                                        Luxury Collection
                                    </a>

                                </li>


                                <li>

                                    <a href="index.php#smart">
                                        Smart Collection
                                    </a>

                                </li>


                                <li>

                                    <a href="index.php#sports">
                                        Sports Collection
                                    </a>

                                </li>


                            </ul>

                        </li>


                        <li>


                            <a
                                href="#"
                                class="mobile-dropdown-toggle"
                                data-target="brandsMenu"
                            >

                                <span>
                                    Brands
                                </span>

                                <i class="fas fa-chevron-down"></i>

                            </a>


                            <ul
                                class="mobile-dropdown"
                                id="brandsMenu"
                            >


                                <li>

                                    <a href="brand.php?brand=Rolex">
                                        Rolex
                                    </a>

                                </li>


                                <li>

                                    <a href="brand.php?brand=Casio">
                                        Casio
                                    </a>

                                </li>


                                <li>

                                    <a href="brand.php?brand=Titan">
                                        Titan
                                    </a>

                                </li>


                                <li>

                                    <a href="brand.php?brand=Fossil">
                                        Fossil
                                    </a>

                                </li>


                                <?php

                                $mobile_qry =
                                    "SELECT * FROM brand";

                                $mobile_result =
                                    mysqli_query(
                                        $conn,
                                        $mobile_qry
                                    );


                                if ($mobile_result)
                                {

                                    while (
                                        $mobile_row =
                                        mysqli_fetch_assoc(
                                            $mobile_result
                                        )
                                    )
                                    {

                                        $mobile_brand =
                                            $mobile_row['Brand Name'];

                                ?>


                                        <li>

                                            <a
                                                href="brand.php?brand=<?php echo urlencode($mobile_brand); ?>"
                                            >

                                                <?php

                                                echo htmlspecialchars(
                                                    $mobile_brand
                                                );

                                                ?>

                                            </a>

                                        </li>


                                <?php

                                    }

                                }

                                ?>


                            </ul>

                        </li>


                        <li>

                            <a href="about.php">

                                <span>
                                    About
                                </span>

                            </a>

                        </li>


                        <?php

                        if (
                            isset($_SESSION['user_id']) &&
                            !empty($_SESSION['user_id'])
                        )
                        {

                            $mobile_username =
                                $_SESSION['user_name']
                                ?? ($_SESSION['name'] ?? 'User');

                        ?>


                            <li>

                                <a href="#">

                                    <span>

                                        Welcome,

                                        <?php

                                        echo htmlspecialchars(
                                            $mobile_username
                                        );

                                        ?>

                                    </span>

                                </a>

                            </li>


                            <li>

                                <a href="logout.php">

                                    <span>
                                        Logout
                                    </span>

                                </a>

                            </li>


                        <?php

                        }
                        else
                        {

                        ?>


                            <li>

                                <a href="ragister.php">

                                    <span>
                                        Register
                                    </span>

                                </a>

                            </li>


                            <li>

                                <a href="login.php">

                                    <span>
                                        Login
                                    </span>

                                </a>

                            </li>


                        <?php

                        }

                        ?>


                    </ul>


                    <div class="mobile-menu-icons">


                        <a
                            href="wishlist.php"
                            class="icon-btn"
                            title="Wishlist"
                        >

                            <i class="far fa-heart"></i>

                        </a>


                        <a
                            href="orders.php"
                            class="icon-btn"
                            title="My Orders"
                        >

                            <i class="fas fa-shopping-bag"></i>

                        </a>


                        <a
                            href="cart.php"
                            class="icon-btn cart"
                            title="Cart"
                        >

                            <i class="fas fa-shopping-cart"></i>

                            <span>
                                0
                            </span>

                        </a>


                    </div>


                </div>

            </div>

        </div>

    </div>

</header>


<script>

document.addEventListener(
    "DOMContentLoaded",
    function ()
    {


        const toggleButton =
            document.getElementById(
                "mobileMenuToggle"
            );


        const mobileMenu =
            document.getElementById(
                "mobileMenu"
            );


        toggleButton.addEventListener(
            "click",
            function ()
            {


                mobileMenu.classList.toggle(
                    "active"
                );


                const isOpen =
                    mobileMenu.classList.contains(
                        "active"
                    );


                toggleButton.setAttribute(
                    "aria-expanded",
                    isOpen
                );


                if (isOpen)
                {

                    toggleButton.innerHTML =
                        '<i class="fas fa-times"></i>';

                }
                else
                {

                    toggleButton.innerHTML =
                        '<i class="fas fa-bars"></i>';

                }


            }
        );


        const dropdownButtons =
            document.querySelectorAll(
                ".mobile-dropdown-toggle"
            );


        dropdownButtons.forEach(
            function (button)
            {


                button.addEventListener(
                    "click",
                    function (e)
                    {


                        e.preventDefault();


                        const targetId =
                            button.getAttribute(
                                "data-target"
                            );


                        const target =
                            document.getElementById(
                                targetId
                            );


                        target.classList.toggle(
                            "active"
                        );


                        const arrow =
                            button.querySelector(
                                "i"
                            );


                        if (
                            target.classList.contains(
                                "active"
                            )
                        )
                        {

                            arrow.classList.remove(
                                "fa-chevron-down"
                            );


                            arrow.classList.add(
                                "fa-chevron-up"
                            );

                        }
                        else
                        {

                            arrow.classList.remove(
                                "fa-chevron-up"
                            );


                            arrow.classList.add(
                                "fa-chevron-down"
                            );

                        }


                    }
                );


            }
        );


        const mobileLinks =
            mobileMenu.querySelectorAll(
                "a:not(.mobile-dropdown-toggle)"
            );


        mobileLinks.forEach(
            function (link)
            {


                link.addEventListener(
                    "click",
                    function ()
                    {


                        mobileMenu.classList.remove(
                            "active"
                        );


                        toggleButton.setAttribute(
                            "aria-expanded",
                            "false"
                        );


                        toggleButton.innerHTML =
                            '<i class="fas fa-bars"></i>';


                    }
                );


            }
        );


    }
);

</script>