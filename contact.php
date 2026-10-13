<?php include 'header.php'; ?>
<style>
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:Arial, Helvetica, sans-serif;
}

body{
    background:#000;
}

/* Contact Section */
.contact-section{
    width:100%;
    padding:80px 8%;
    background:#000;
}

/* Heading */
.title{
    text-align:center;
    margin-bottom:50px;
}

.title h1{
    color:#d4af37;
    font-size:32px;
    font-weight:600;
    letter-spacing:3px;
    text-transform:uppercase;
    margin-bottom:15px;
    text-shadow:0 0 10px rgba(212,175,55,0.5);
}

.title p{
    color:#ccc;
    font-size:17px;
    line-height:28px;
}
/* Main Box */
.contact-box{
    max-width:1100px;
    margin:auto;
    display:grid;
    grid-template-columns:1fr 1fr;
    background:#111;
    border-radius:18px;
    overflow:hidden;
    border:1px solid #2f2f2f;
    box-shadow:0 0 30px rgba(212,175,55,.18);
}

/* Left */
.contact-info{
    background:#151515;
    padding:50px;
}

.contact-info h2{
    color:#fff;
    margin-bottom:15px;
}

.contact-info>p{
    color:#bdbdbd;
    line-height:28px;
    margin-bottom:35px;
}

.info{
    display:flex;
    gap:18px;
    margin-bottom:25px;
}

.info i{
    width:50px;
    height:50px;
    background:#d4af37;
    color:#000;
    border-radius:50%;
    display:flex;
    justify-content:center;
    align-items:center;
    font-size:18px;
}

.info span{
    color:#d4af37;
    font-weight:bold;
}

.info p{
    color:#fff;
    margin-top:5px;
}

/* Right */
.contact-form{
    background:#1b1b1b;
    padding:50px;
}

.contact-form h3{
    color:#fff;
    margin-bottom:30px;
    font-size:30px;
}

.contact-form input,
.contact-form textarea{
    width:100%;
    padding:15px;
    margin-bottom:18px;
    background:#262626;
    border:1px solid #444;
    color:#fff;
    border-radius:8px;
    outline:none;
    transition:.3s;
}

.contact-form input:focus,
.contact-form textarea:focus{
    border-color:#d4af37;
}

.contact-form textarea{
    height:150px;
    resize:none;
}

.contact-form button{
    width:100%;
    padding:15px;
    background:#d4af37;
    color:#000;
    border:none;
    border-radius:8px;
    font-size:17px;
    font-weight:bold;
    cursor:pointer;
    transition:.3s;
}

.contact-form button:hover{
    background:#fff;
}

/* Responsive */
@media(max-width:768px){

.contact-box{
    grid-template-columns:1fr;
}

.contact-info,
.contact-form{
    padding:30px;
}

.title h2{
    font-size:35px;
}

}
</style>
<?php

include("conn.php");

if(isset($_POST['send']))
{
    $name = $_POST['name'];
    $email = $_POST['email'];
    $subject = $_POST['subject'];
    $message = $_POST['message'];

    $query = "INSERT INTO contact (name, email, subject, message)
              VALUES ('$name', '$email', '$subject', '$message')";

    if(mysqli_query($conn, $query))
    {
        echo "<script>
                alert('Message Sent Successfully');
                window.location.href='contact.php';
              </script>";
    }
    else
    {
        echo "Error: " . mysqli_error($conn);
    }
}

?>
<link rel="stylesheet" href="contact.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

<section class="contact-section">

    <div class="title">
        <h1>CONTACT US</h1>
      
        <p>
            Have questions about our premium watch collection?
            We'd love to hear from you.
        </p>
    </div>

    <div class="contact-box">

        <!-- Left Side -->

        <div class="contact-info">

            <h2>TimeCraft Watches</h2>

            <p>
                Our customer support team is available to assist you with
                product inquiries, orders and services.
            </p>

            <div class="info">
                <i class="fa-solid fa-phone"></i>

                <div>
                    <span>Phone</span>
                    <p>+91 8923546978</p>
                </div>

            </div>

            <div class="info">

                <i class="fa-solid fa-envelope"></i>

                <div>
                    <span>Email</span>
                    <p>timecraft@gmail.com</p>
                </div>

            </div>

            <div class="info">

                <i class="fa-solid fa-location-dot"></i>

                <div>
                    <span>Address</span>
                    <p>Junagadh, Gujarat</p>
                </div>

            </div>

            <div class="info">

                <i class="fa-solid fa-clock"></i>

                <div>
                    <span>Working Hours</span>
                    <p>Monday - Saturday<br>9:00 AM - 8:00 PM</p>
                </div>

            </div>

        </div>

        <!-- Right Side -->

        <div class="contact-form">

            <h3>Send Message</h3>

            <form action="" method="post">

                <input
                type="text"
                name="name"
                placeholder="Your Name"
                required>

                <input
                type="email"
                name="email"
                placeholder="Your Email"
                required>

                <input
                type="text"
                name="subject"
                placeholder="Subject"
                required>

                <textarea
                name="message"
                placeholder="Write Your Message..."
                required></textarea>

                <button type="submit" name="send">
                    Send Message
                </button>

            </form>

        </div>

    </div>

</section>

<?php include 'footer.php'; ?>