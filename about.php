<?php include 'header.php'; ?>

<style>

body{
    background:#000;
    font-family:Arial,sans-serif;
}

.about-area{
    background:linear-gradient(180deg,#000,#111,#000);
    padding:80px 0;
    color:#fff;
}

/* Video */

.video-box{
    width:100%;
    height:420px;
    overflow:hidden;
    border-radius:25px;
    box-shadow:0 15px 40px rgba(212,175,55,.30);
}

.video-box video{
    width:100%;
    height:100%;
    object-fit:cover;
    filter:brightness(.75);
    transition:.5s;
}

.video-box:hover video{
    transform:scale(1.05);
}

/* Image */

.about-img{
    overflow:hidden;
    border-radius:25px;
    box-shadow:0 15px 40px rgba(212,175,55,.25);
}

.about-img img{
    width:100%;
    height:420px;
    object-fit:cover;
    transition:.5s;
}

.about-img:hover img{
    transform:scale(1.08);
}

/* Content */

.about-content{
    padding:30px;
}

.about-content h2{
    color:#d4af37;
    font-size:40px;
    font-weight:700;
    text-transform:uppercase;
    margin-bottom:25px;
}

.about-content p{
    color:#ddd;
    font-size:17px;
    line-height:32px;
    margin-bottom:15px;
}

.about-btn{
    display:inline-block;
    margin-top:20px;
    background:#d4af37;
    color:#000;
    padding:14px 35px;
    border-radius:50px;
    text-decoration:none;
    font-weight:bold;
    transition:.3s;
}

.about-btn:hover{
    background:#fff;
    color:#000;
}

/* Space Between Rows */

.row{
    margin-bottom:60px;
}

/* Mobile */

@media(max-width:768px){

    .video-box{
        height:280px;
        margin-bottom:25px;
    }

    .about-img img{
        height:280px;
        margin-bottom:25px;
    }

    .about-content{
        padding:20px 10px;
        text-align:center;
    }

    .about-content h2{
        font-size:28px;
    }

    .about-content p{
        font-size:15px;
        line-height:28px;
    }

    .about-btn{
        padding:12px 28px;
    }

    .row{
        margin-bottom:40px;
    }
</style>



<div class="container">

    <div class="row align-items-center mb-5">

        <div class="col-lg-6">
            <div class="video-box">
                <video autoplay muted loop playsinline>
                    <source src="assets/img/video/rolex.mp4" type="video/mp4">
                </video>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="about-content">
                <h2>Luxury Watches</h2>

                <p>
                    Experience premium luxury watches crafted with
                    elegance, precision and timeless style.
                </p>

<a href="brand.php?brand=Rolex" class="about-btn">
    Explore Collection
</a>
</div>
        </div>

    </div>


<div class="row align-items-center">

    <div class="col-lg-6">
        <div class="about-content">

<h2>Titan Collection</h2>

<p>
    Discover the Titan Collection, where elegant design meets
    reliable craftsmanship to create watches for every occasion.
</p>

<p>
    From classic to modern styles, Titan watches are designed
    to add sophistication and timeless style to your personality.
</p>

<a href="brand.php?brand=Titan" class="about-btn">
    Shop Now
</a>
        </div>
    </div>

    <!-- Right Side Image -->
    <div class="col-lg-6">
        <div class="about-img">
            <img src="assets/img/hero/b2.jpg" alt="Luxury Watch">
        </div>
    </div>

</div>
</div>
</section>
<?php include 'footer.php';?>