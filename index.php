<?php
session_start();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

    <meta charset="UTF-8">

    <meta name="description" content="Time Craft - Premium Watches">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Time Craft </title>

    <link rel="icon" type="image/png" href="assets/img/logo/icon.png">
    <link rel="shortcut icon" type="image/png" href="assets/img/logo/icon.png">

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

</head>

<style>

/* ================================
   GLOBAL
================================ */

html,
body {
    margin: 0;
    padding: 0;
    background: #000 !important;
    color: #fff !important;
    font-family: Arial, Helvetica, sans-serif;
	    overflow-x: hidden; 
    scrollbar-width: none; 
    -ms-overflow-style: none;

}

section,
main,
.container-fluid,
.row,
.slider-area,
.popular-items,
.popular-product,
.collection,
.categories-area,
.gray-bg,
.footer-area {
    background: transparent !important;
}

/* ================================
   SECTION TITLE
================================ */

.section-title {
    color: #fff !important;
    font-size: 40px;
    font-weight: 700;
    text-transform: uppercase;
    margin: 50px 0;
}

/* ================================
   POPULAR ITEMS
================================ */

.popular-items {
    background: #000 !important;
}

.single-popular-items {
    background: #111 !important;
    border: 1px solid #333;
    border-radius: 12px;
    padding: 15px;
    transition: .4s;
    margin-bottom: 30px;
}

.single-popular-items:hover {
    border-color: #d4af37;
    transform: translateY(-5px);
    box-shadow: 0 0 20px rgba(212,175,55,.3);
}

/* ================================
   PRODUCT IMAGE
================================ */

.popular-items .popular-img {
    width: 100%;
    height: 320px;
    overflow: hidden;
    border-radius: 10px;
    position: relative;
}

.popular-items .popular-img img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
    transition: .4s;
}

.popular-items .popular-img img:hover {
    transform: scale(1.08);
}

/* ================================
   IMAGE CAP
================================ */

.popular-items .img-cap {
    position: absolute;
    left: 0;
    bottom: 0;
    width: 100%;
    background: rgba(0,0,0,.75);
    color: #fff;
    text-align: center;
    padding: 12px;
    font-size: 18px;
    font-weight: bold;
}

.popular-items .favorit-items {
    margin-top: 15px;
}

/* ================================
   BUTTON
================================ */

.btn,
.border-btn {
    background: #d4af37 !important;
    color: #000 !important;
    border: none;
    border-radius: 30px;
    padding: 12px 30px;
    font-weight: bold;
    text-decoration: none;
    transition: .3s;
}

.btn:hover,
.border-btn:hover {
    background: #fff !important;
    color: #000 !important;
}

/* ================================
   ABOUT SECTION
================================ */

.about-section {
    padding: 90px 0;
    background: #0f0f0f !important;
}

.about-section .container {
    width: 90%;
    max-width: 1200px;
    margin: auto;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 60px;
}

.about-left,
.about-right {
    flex: 1;
}

.about-left .sub-title {
    color: #d4af37;
    font-size: 15px;
    letter-spacing: 3px;
    font-weight: 600;
}

.about-left h2 {
    color: #fff;
    font-size: 42px;
    margin: 15px 0 20px;
    line-height: 1.3;
}

.about-left p {
    color: #cfcfcf;
    font-size: 17px;
    line-height: 1.8;
    margin-bottom: 20px;
}

.about-right video {
    width: 100%;
    height: 420px;
    object-fit: cover;
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(0,0,0,.4);
}

.about-left .btn {
    display: inline-block;
    padding: 14px 35px;
    background: #d4af37 !important;
    color: #000 !important;
    text-decoration: none;
    border-radius: 40px;
    font-weight: 600;
    transition: .3s;
}

.about-left .btn:hover {
    background: #fff !important;
    color: #000 !important;
}

/* ================================
   FEATURED WATCH SECTION
================================ */

.watch-section {
    padding: 100px 0;
    position: relative;
    background: #000 !important;
}

.watch-section::before {
    content: "";
    position: absolute;
    inset: 0;
    background: rgba(0,0,0,.75);
}

.watch-section .container {
    position: relative;
    z-index: 1;
}

.watch-box {
    display: grid;
    grid-template-columns: repeat(auto-fit,minmax(320px,1fr));
    gap: 40px;
}

.watch-card {
    background: #111;
    border-radius: 15px;
    overflow: hidden;
    border: 1px solid #333;
    padding: 40px;
    transition: .4s;
}

.watch-card:hover {
    transform: translateY(-8px);
    border-color: #d4af37;
    box-shadow: 0 15px 30px rgba(212,175,55,.25);
}

.watch-card h4 {
    font-size: 30px;
    color: #d4af37;
    margin-bottom: 20px;
}

.watch-card p {
    line-height: 1.8;
    color: #ddd;
    margin-bottom: 25px;
}

.product-feature {
    list-style: none;
    padding: 0;
    margin: 0 0 30px;
}

.product-feature li {
    margin-bottom: 15px;
    font-size: 17px;
}

.product-feature i {
    color: #d4af37;
    margin-right: 10px;
}

/* ================================
   BRAND / CATEGORY SECTION
================================ */

.categories-area {
    background: #111 !important;
    padding: 70px 0;
    color: #fff;
}

.single-cat {
    background: #1a1a1a;
    border-radius: 10px;
    padding: 25px;
    transition: .3s;
    border: 1px solid transparent;
}

.single-cat:hover {
    border-color: #d4af37;
    transform: translateY(-5px);
}

.cat-icon img {
    object-fit: contain;
}

.cat-cap h5,
.cat-cap p {
    color: #fff !important;
}

.cat-cap h5 {
    margin-top: 15px;
    font-size: 20px;
}

.cat-cap p {
    color: #bbb !important;
}

/* ================================
   COMMON TEXT
================================ */

h1,
h2,
h3,
h4,
h5,
h6,
p,
span,
li,
a {
    color: #fff;
}

a {
    text-decoration: none;
    transition: .3s;
}

a:hover {
    color: #d4af37 !important;
}

/* ================================
   SEARCH
================================ */

.search-model-box {
    background: rgba(0,0,0,.95);
}

.search-model-form input {
    background: #111;
    color: #fff;
    border: 1px solid #333;
}


/* ================================
   CAROUSEL
================================ */

.carousel-control-prev-icon,
.carousel-control-next-icon {
    filter: invert(1);
}

/* ================================
   RESPONSIVE
================================ */

@media(max-width: 992px) {

    .about-section .container {
        flex-direction: column;
        text-align: center;
    }

    .about-left h2 {
        font-size: 34px;
    }

    .about-right video {
        height: 300px;
    }

}

@media(max-width: 576px) {

    .section-title {
        font-size: 30px;
    }

    .popular-items .popular-img {
        height: 280px;
    }

    .about-section {
        padding: 60px 0;
    }

    .about-left h2 {
        font-size: 28px;
    }

    .watch-section {
        padding: 60px 0;
    }

    .watch-card {
        padding: 25px;
    }

    .watch-card h4 {
        font-size: 25px;
    }

}

</style>

<body>

<header>
    <?php include 'header.php'; ?>
</header>


<!-- =====================================================
     HERO SLIDER
===================================================== -->

<section id="heroSlider" class="carousel slide" data-ride="carousel">

    <div class="container-fluid">

        <div class="slider-area">

            <section
                id="heroSlider"
                class="carousel slide"
                data-ride="carousel"
                style="height:100vh;"
            >

                <!-- Indicators -->
                <ul class="carousel-indicators">
                    <li data-target="#heroSlider" data-slide-to="0" class="active"></li>
                    <li data-target="#heroSlider" data-slide-to="1"></li>
                    <li data-target="#heroSlider" data-slide-to="2"></li>
                </ul>

                <!-- SLIDE 1 - TITAN -->
                <div class="carousel-inner">

                    <div
                        class="carousel-item active"
                        style="
                            background:url('assets/img/hero/premium_photo-1728759435328-9a5a417edef7.jpg') center center/cover no-repeat;
                            height:100vh;
                        "
                    >

                        <div
                            style="
                                width:100%;
                                height:100%;
                                background:rgba(0,0,0,0.45);
                                display:flex;
                                align-items:center;
                                justify-content:center;
                                text-align:center;
                            "
                        >

                            <div>

                                <h1 style="color:#fff; font-size:65px; font-weight:700;">
                                    Titan Watch Collection
                                </h1>

                                <p style="color:#fff; font-size:20px; margin:20px 0;">
                                    Premium & Elegant Watches
                                </p>

                                <a
                                    href="brand.php?brand=Titan"
                                    class="shop-btn"
                                >
                                    Shop Now
                                </a>

                            </div>

                        </div>

                    </div>


                    <!-- SLIDE 2 - ROLEX -->
                    <div
                        class="carousel-item"
                        style="
                            background:url('assets/img/hero/b2.jfif') center center/cover no-repeat;
                            height:100vh;
                        "
                    >

                        <div
                            style="
                                width:100%;
                                height:100%;
                                background:rgba(0,0,0,0.45);
                                display:flex;
                                align-items:center;
                                justify-content:center;
                                text-align:center;
                            "
                        >

                            <div>

                                <h1 style="color:#fff; font-size:65px; font-weight:700;">
                                    Rolex Watch Style
                                </h1>

                                <p style="color:#fff; font-size:20px; margin:20px 0;">
                                    Designed for Your Lifestyle
                                </p>

                                <a
                                    href="brand.php?brand=Rolex"
                                    class="shop-btn"
                                >
                                    Shop Now
                                </a>

                            </div>

                        </div>

                    </div>


                    <!-- SLIDE 3 - CASIO -->
                    <div
                        class="carousel-item"
                        style="
                            background:url('assets/img/hero/caramel-vRcSC-UN3yI-unsplash.jpg') center center/cover no-repeat;
                            height:100vh;
                        "
                    >

                        <div
                            style="
                                width:100%;
                                height:100%;
                                background:rgba(0,0,0,0.45);
                                display:flex;
                                align-items:center;
                                justify-content:center;
                                text-align:center;
                            "
                        >

                            <div>

                                <h1 style="color:#fff; font-size:65px; font-weight:700;">
                                    Casio Brand Watches
                                </h1>

                                <p style="color:#fff; font-size:20px; margin:20px 0;">
                                    Feel the Premium Experience
                                </p>

                                <a
                                    href="brand.php?brand=Casio"
                                    class="shop-btn"
                                >
                                    Shop Now
                                </a>

                            </div>

                        </div>

                    </div>

                </div>

                <!-- Controls -->
                <a class="carousel-control-prev" href="#heroSlider" data-slide="prev">
                    <span class="carousel-control-prev-icon"></span>
                </a>

                <a class="carousel-control-next" href="#heroSlider" data-slide="next">
                    <span class="carousel-control-next-icon"></span>
                </a>

            </section>

        </div>

    </div>

</section>


<!-- =====================================================
     SHOP NOW BUTTON CSS
===================================================== -->

<style>
.shop-btn {
    display: inline-block;
    padding: 15px 40px;
    background: #d4af37;
    color: #fff;
    text-decoration: none;
    border-radius: 30px;
    transition: 0.3s ease;
}

.shop-btn:hover {
    color: #000 !important;
    text-decoration: none;
}
</style>

<!-- =====================================================
     MEN'S WATCHES
===================================================== -->

<div class="row" id="mens">

    <div class="col-12 text-center mb-5">

        <h2 class="section-title">
            Men's Watches
        </h2>

    </div>

</div>


<div class="popular-items pt-50">

    <div class="container-fluid">

        <div class="row">


            <!-- Rolex -->

            <div class="col-lg-3 col-md-6 col-sm-6">

                <div class="single-popular-items text-center wow fadeInUp">

                    <div class="popular-img">

                        <img
                            src="assets/img/rolex/boy/70437490056781.jpg"
                            alt="Rolex Men's Watch"
                        >

                        <div class="img-cap">
                            <span>Rolex</span>
                        </div>

                        <div class="favorit-items">

                            <a
                                href="brand.php?brand=Rolex"
                                class="btn"
                            >
                                Show Now
                            </a>

                        </div>

                    </div>

                </div>

            </div>


            <!-- Casio -->

            <div class="col-lg-3 col-md-6 col-sm-6">

                <div class="single-popular-items text-center wow fadeInUp">

                    <div class="popular-img">

                        <img
                            src="assets/img/casio/boy/c22.jpg"
                            alt="Casio Men's Watch"
                        >

                        <div class="img-cap">
                            <span>Casio</span>
                        </div>

                        <div class="favorit-items">

                            <a
                                href="brand.php?brand=Casio"
                                class="btn"
                            >
                                Show Now
                            </a>

                        </div>

                    </div>

                </div>

            </div>


            <!-- Titan -->

            <div class="col-lg-3 col-md-6 col-sm-6">

                <div class="single-popular-items text-center wow fadeInUp">

                    <div class="popular-img">

                        <img
                            src="assets/img/titan/boy/l3.jpg"
                            alt="Titan Men's Watch"
                        >

                        <div class="img-cap">
                            <span>Titan</span>
                        </div>

                        <div class="favorit-items">

                            <a
                                href="brand.php?brand=Titan"
                                class="btn"
                            >
                                Show Now
                            </a>

                        </div>

                    </div>

                </div>

            </div>


            <!-- Fossil -->

            <div class="col-lg-3 col-md-6 col-sm-6">

                <div class="single-popular-items text-center wow fadeInUp">

                    <div class="popular-img">

                        <img
                            src="assets/img/fosil/boy/Fossil Fenmore Chrono Silver Blue Brown Leather Bq2402 Men's Watch.jpg"
                            alt="Fossil Men's Watch"
                        >

                        <div class="img-cap">
                            <span>Fossil</span>
                        </div>

                        <div class="favorit-items">

                            <a
                                href="brand.php?brand=Fossil"
                                class="btn"
                            >
                                Show Now
                            </a>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>


<!-- =====================================================
     WOMEN'S WATCHES
===================================================== -->

<div class="row" id="womens">

    <div class="col-12 text-center mb-5">

        <h2 class="section-title">
            Women's Watches
        </h2>

    </div>

</div>


<div class="popular-items pt-50">

    <div class="container-fluid">

        <div class="row">


            <!-- Rolex -->

            <div class="col-lg-3 col-md-6 col-sm-6">

                <div class="single-popular-items text-center wow fadeInUp">

                    <div class="popular-img">

                        <img
                            src="assets/img/rolex/girl/2040762328689467.jpg"
                            alt="Rolex Women's Watch"
                        >

                        <div class="img-cap">
                            <span>Rolex</span>
                        </div>

                        <div class="favorit-items">

                            <a
                                href="brand.php?brand=Rolex"
                                class="btn"
                            >
                                Show Now
                            </a>

                        </div>

                    </div>

                </div>

            </div>


            <!-- Casio -->

            <div class="col-lg-3 col-md-6 col-sm-6">

                <div class="single-popular-items text-center wow fadeInUp">

                    <div class="popular-img">

                        <img
                            src="assets/img/casio/girl/179792210121220984.jpg"
                            alt="Casio Women's Watch"
                        >

                        <div class="img-cap">
                            <span>Casio</span>
                        </div>

                        <div class="favorit-items">

                            <a
                                href="brand.php?brand=Casio"
                                class="btn"
                            >
                                Show Now
                            </a>

                        </div>

                    </div>

                </div>

            </div>


            <!-- Titan -->

            <div class="col-lg-3 col-md-6 col-sm-6">

                <div class="single-popular-items text-center wow fadeInUp">

                    <div class="popular-img">

                        <img
                            src="assets/img/titan/girl/Add a touch of glamour to your everyday look.jpg"
                            alt="Titan Women's Watch"
                        >

                        <div class="img-cap">
                            <span>Titan</span>
                        </div>

                        <div class="favorit-items">

                            <a
                                href="brand.php?brand=Titan"
                                class="btn"
                            >
                                Show Now
                            </a>

                        </div>

                    </div>

                </div>

            </div>


            <!-- Fossil -->

            <div class="col-lg-3 col-md-6 col-sm-6">

                <div class="single-popular-items text-center wow fadeInUp">

                    <div class="popular-img">

                        <img
                            src="assets/img/fosil/girl/Fossil Accessories _ New In Box! Fossil Watch _ Color_ Gold_White _ Size_ Os.jpg"
                            alt="Fossil Women's Watch"
                        >

                        <div class="img-cap">
                            <span>Fossil</span>
                        </div>

                        <div class="favorit-items">

                            <a
                                href="brand.php?brand=Fossil"
                                class="btn"
                            >
                                Show Now
                            </a>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>


<!-- =====================================================
     LUXURY WATCHES
===================================================== -->

<div class="row" id="luxury">

    <div class="col-12 text-center mb-5">

        <h2 class="section-title">
            Luxury Watches
        </h2>

    </div>

</div>


<div class="popular-items pt-50">

    <div class="container-fluid">

        <div class="row">


            <!-- Rolex -->

            <div class="col-lg-3 col-md-6 col-sm-6">

                <div class="single-popular-items text-center wow fadeInUp">

                    <div class="popular-img">

                        <img
                            src="assets/img/rolex/girl/2040762328699060.jpg"
                            alt="Luxury Rolex"
                        >

                        <div class="img-cap">
                            <span>Rolex</span>
                        </div>

                        <div class="favorit-items">

                            <a
                                href="brand.php?brand=Rolex"
                                class="btn"
                            >
                                Show Now
                            </a>

                        </div>

                    </div>

                </div>

            </div>


            <!-- Casio -->

            <div class="col-lg-3 col-md-6 col-sm-6">

                <div class="single-popular-items text-center wow fadeInUp">

                    <div class="popular-img">

                        <img
                            src="assets/img/casio/boy/441986150955452265.jpg"
                            alt="Luxury Casio"
                        >

                        <div class="img-cap">
                            <span>Casio</span>
                        </div>

                        <div class="favorit-items">

                            <a
                                href="brand.php?brand=Casio"
                                class="btn"
                            >
                                Shop Now
                            </a>

                        </div>

                    </div>

                </div>

            </div>


            <!-- Titan -->

            <div class="col-lg-3 col-md-6 col-sm-6">

                <div class="single-popular-items text-center wow fadeInUp">

                    <div class="popular-img">

                        <img
                            src="assets/img/titan/girl/Titan Analog Green Dial Women's Watch-NM95058WM01 _ NL95058WM01_NP95058WM01.jpg"
                            alt="Luxury Titan"
                        >

                        <div class="img-cap">
                            <span>Titan</span>
                        </div>

                        <div class="favorit-items">

                            <a
                                href="brand.php?brand=Titan"
                                class="btn"
                            >
                                Shop Now
                            </a>

                        </div>

                    </div>

                </div>

            </div>


            <!-- Fossil -->

            <div class="col-lg-3 col-md-6 col-sm-6">

                <div class="single-popular-items text-center wow fadeInUp">

                    <div class="popular-img">

                        <img
                            src="assets/img/fosil/boy/men1.jpg"
                            alt="Luxury Fossil"
                        >

                        <div class="img-cap">
                            <span>Fossil</span>
                        </div>

                        <div class="favorit-items">

                            <a
                                href="brand.php?brand=Fossil"
                                class="btn"
                            >
                                Shop Now
                            </a>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>


<!-- =====================================================
     ABOUT TIMECRAFT
===================================================== -->

<section class="about-section">

    <div class="container">

        <!-- Left Content -->

        <div class="about-left">

            <span class="sub-title">
                ABOUT TIMECRAFT
            </span>

            <h2>
                Luxury Watches Crafted For Every Lifestyle
            </h2>

            <p>
                TimeCraft is your trusted destination for premium watches.
                We offer a carefully selected collection of Rolex, Casio,
                Titan, Fossil, and many more leading brands.
            </p>

            <p>
                Our goal is to deliver elegance, precision, and quality with
                every watch, helping you find the perfect timepiece for every
                occasion.
            </p>

            <a
                href="about.php"
                class="btn"
            >
                Read More
            </a>

        </div>


        <!-- Right Video -->

        <div class="about-right">

            <video
                autoplay
                muted
                loop
                playsinline
            >

                <source
                    src="assets/img/video/smart1.mp4"
                    type="video/mp4"
                >

            </video>

        </div>

    </div>

</section>


<!-- =====================================================
     FEATURED PRODUCTS
===================================================== -->

<section class="watch-section">

    <div class="container">

        <div class="watch-box">


            <!-- Men's Watch -->

            <div class="watch-card">

                <h4>
                    Luxury Men's Watches                </h4>

                <p>
                    Explore premium Rolex, Titan, Casio, Fossil, and other
                    luxury men's watches. Designed with stainless steel
                    finish, water resistance, and elegant craftsmanship
                    for business, casual, and formal occasions.                </p>

                <ul class="product-feature">

                    <li>
                        <i class="fa-solid fa-check"></i>
                        Premium Quality                    </li>

                    <li>
                        <i class="fa-solid fa-check"></i>
                        Water Resistant                    </li>

                    <li>
                        <i class="fa-solid fa-check"></i>
                        2 Year Warranty                    </li>

                    <li>
                        <i class="fa-solid fa-check"></i>
                        Free Shipping                    </li>
                </ul>

                <a
                    href="#"
                    class="btn"
                >
                    Shop Now                </a>            </div>


            <!-- Women's Watch -->

            <div class="watch-card">

                <h4>
                    Luxury Women's Watches                </h4>

                <p>
                    Discover stylish and elegant women's watches with premium
                    finishing, beautiful designs, and comfortable straps.
                    Perfect for every occasion and daily wear.                </p>

                <ul class="product-feature">

                    <li>
                        <i class="fa-solid fa-check"></i>
                        Elegant Design                    </li>

                    <li>
                        <i class="fa-solid fa-check"></i>
                        Premium Finish                    </li>

                    <li>
                        <i class="fa-solid fa-check"></i>
                        2 Year Warranty                    </li>

                    <li>
                        <i class="fa-solid fa-check"></i>
                        Free Delivery                    </li>
                </ul>

                <a
                    href="#"
                    class="btn"
                >
                    Shop Now                </a>            </div>
        </div>
    </div>

</section>


<!-- =====================================================
     BRANDS
===================================================== -->

<section class="categories-area section-padding80">

    <div class="container">

        <div class="row">


            <!-- Rolex -->

            <div class="col-lg-3 col-md-6 mb-4">

                <div class="single-cat text-center">

                    <div class="cat-icon">

                        <img src="assets/img/logo/rolex.png?v=123"
                            alt="Rolex"
                            height="120"
                            width="100%"
                        >

                    </div>

                    <div class="cat-cap">

                        <h5>
                            Rolex
                        </h5>

                        <p>
                            Luxury Swiss Watches
                        </p>

                    </div>

                </div>

            </div>


            <!-- Casio -->

            <div class="col-lg-3 col-md-6 mb-4">

                <div class="single-cat text-center">

                    <div class="cat-icon">
  <img src="assets/img/logo/casio.png?v=123"
                            alt="Casio"
                            height="120"
                            width="100%"
                        >

                    </div>

                    <div class="cat-cap">

                        <h5>
                            Casio
                        </h5>

                        <p>
                            Digital & Sports Watches
                        </p>

                    </div>

                </div>

            </div>


            <!-- Titan -->

            <div class="col-lg-3 col-md-6 mb-4">

                <div class="single-cat text-center">

                    <div class="cat-icon">

                         <img src="assets/img/logo/titan.png?v=123"
     alt="Titan"
     height="120"
     width="180">

                    </div>

                    <div class="cat-cap">

                        <h5>
                            Titan
                        </h5>

                        <p>
                            Elegant Indian Timepieces
                        </p>

                    </div>

                </div>

            </div>


            <!-- Fossil -->

            <div class="col-lg-3 col-md-6 mb-4">

                <div class="single-cat text-center">

                    <div class="cat-icon">

                       <img src="assets/img/logo/fossil.png?v=123"
                            alt="Fossil"
                            height="120"
                            width="100%"
                        >

                    </div>

                    <div class="cat-cap">

                        <h5>
                            Fossil
                        </h5>

                        <p>
                            Modern Fashion Watches
                        </p>

                    </div>

                </div>

            </div>


        </div>

    </div>

</section>


<!-- =====================================================
     FOOTER
===================================================== -->

<?php include 'footer.php'; ?>


</body>
</html>
