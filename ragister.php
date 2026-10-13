<?php

session_start();

include 'conn.php';

if (isset($_POST['register'])) {

    $name = $_POST['name'];
    $email = $_POST['email'];
    $pass = $_POST['pass'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $gender = $_POST['gender'];

    $name = mysqli_real_escape_string($conn, $name);
    $email = mysqli_real_escape_string($conn, $email);
    $pass = mysqli_real_escape_string($conn, $pass);
    $phone = mysqli_real_escape_string($conn, $phone);
    $address = mysqli_real_escape_string($conn, $address);
    $gender = mysqli_real_escape_string($conn, $gender);

    $check = mysqli_query(
        $conn,
        "SELECT * FROM reg WHERE email='$email'"
    );

    if (mysqli_num_rows($check) > 0) {

        echo "<script>
                alert('Email Already Registered');
              </script>";

    } else {

        $sql = "INSERT INTO reg
        (
            name,
            email,
            pass,
            phone,
            address,
            gender,
            u_type
        )
        VALUES
        (
            '$name',
            '$email',
            '$pass',
            '$phone',
            '$address',
            '$gender',
            'user'
        )";

        if (mysqli_query($conn, $sql)) {

            echo "<script>
                    alert('Registration Successful');
                    window.location='login.php';
                  </script>";

            exit();

        } else {

            echo "<script>
                    alert('Error: " . mysqli_error($conn) . "');
                  </script>";

        }

    }

}

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta
    name="viewport"
    content="width=device-width, initial-scale=1.0"
>

<title>Register | Time Craft</title>
    <link rel="shortcut icon" type="image/png" href="assets/img/logo/icon.png">


<link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
>

<style>

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: Arial, Helvetica, sans-serif;
}

html,
body {
    width: 100%;
    height: 100%;
    overflow-x: hidden;
    color: #fff;
}

.bg-video {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    z-index: -2;
}

.overlay {
    width: 100%;
    min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    position: relative;
    padding: 50px 0;
}

.overlay::before {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,.55);
    z-index: 0;
}

.form-box {
    position: relative;
    z-index: 1;
    width: 600px;
    background: rgba(0,0,0,.35);
    backdrop-filter: blur(12px);
    border: 1px solid rgba(255,255,255,.2);
    padding: 35px;
    border-radius: 18px;
    color: #fff;
    box-shadow: 0 0 20px rgba(0,0,0,.5);
    transition: .4s;
    top: 10px;
}

.form-box:hover {
    transform: translateY(-8px) scale(1.02);
    border: 1px solid #d9ad43;
    box-shadow: 0 0 30px rgba(217,173,67,.8);
}

.title {
    color: #d9ad43;
    font-size: 40px;
    text-align: center;
    font-weight: bold;
    margin-bottom: 25px;
}

.input-box {
    display: flex;
    align-items: center;
    border: 1px solid #777;
    padding: 12px;
    border-radius: 8px;
    margin-bottom: 18px;
}

.input-box i {
    color: #d9ad43;
    margin-right: 12px;
    font-size: 18px;
}

.input-box input,
.input-box textarea,
.input-box select {
    width: 100%;
    background: none;
    border: none;
    outline: none;
    color: #fff;
    font-size: 16px;
}

.input-box textarea {
    resize: none;
    height: 70px;
}

.input-box input::placeholder,
.input-box textarea::placeholder {
    color: #ddd;
}

.input-box select option {
    background: #111;
    color: #fff;
}

.row-box {
    display: flex;
    gap: 15px;
    margin-bottom: 18px;
}

.row-box .input-box {
    flex: 1;
    margin-bottom: 0;
}

.gender-box {
    display: flex;
    align-items: center;
    gap: 15px;
    border: 1px solid #777;
    border-radius: 8px;
    padding: 12px;
}

.gender-box i {
    color: #d9ad43;
    margin-right: 5px;
}

.gender-box label {
    display: flex;
    align-items: center;
    gap: 5px;
    font-size: 15px;
    color: #fff;
}

.gender-box input[type="radio"] {
    accent-color: #d9ad43;
}

.btn-center {
    width: 100%;
    display: flex;
    justify-content: center;
    align-items: center;
    margin-top: 20px;
}

.btn-center button {
    width: 220px;
    height: 50px;
    background: #d9ad43;
    color: #000;
    border: none;
    border-radius: 8px;
    font-size: 18px;
    font-weight: bold;
    cursor: pointer;
    transition: 0.3s ease;
}

.btn-center button:hover {
    background: #c79a26;
    transform: translateY(-3px);
    box-shadow: 0 8px 20px rgba(217,173,67,0.5);
}

.login {
    text-align: center;
    margin-top: 20px;
}

.login a {
    color: #d9ad43;
    text-decoration: none;
    font-weight: bold;
}

.login a:hover {
    text-decoration: underline;
}

@media (max-width: 768px) {

    .form-box {
        width: 95%;
        padding: 20px;
    }

    .row-box {
        flex-direction: column;
        gap: 0;
    }

    .row-box .input-box {
        width: 100%;
        margin-bottom: 18px;
    }

    .gender-box {
        flex-wrap: wrap;
        gap: 10px;
    }

    .title {
        font-size: 32px;
    }

}

</style>

</head>

<body>

<?php include 'header.php'; ?>

<video
    class="bg-video"
    autoplay
    muted
    loop
    playsinline
>

    <source
        src="assets/img/video/rol.mp4"
        type="video/mp4"
    >

</video>

<div class="overlay">

<div class="form-box">

<h1 class="title">
Register
</h1>

<form
    action=""
    method="post"
>

<div class="input-box">

<i class="fa-solid fa-user"></i>

<input
    type="text"
    name="name"
    placeholder="Full Name"
    pattern="[A-Za-z]+"
    title="Only letters are allowed"
    oninput="this.value=this.value.replace(/[^A-Za-z]/g,'')"
    required
>

</div>

<div class="input-box">

<i class="fa-solid fa-envelope"></i>

<input
    type="email"
    name="email"
    placeholder="Email Address"
    required
>

</div>

<div class="row-box">

<div class="input-box">

<i class="fa-solid fa-lock"></i>

<input
    type="password"
    name="pass"
    placeholder="Password"
    minlength="8"
    maxlength="8"
    required
>

</div>

<div class="input-box gender-box">

<i class="fa-solid fa-user"></i>

<label>

<input
    type="radio"
    name="gender"
    value="Male"
    required
>

Male

</label>

<label>

<input
    type="radio"
    name="gender"
    value="Female"
>

Female

</label>

</div>

</div>

<div class="input-box">

<i class="fa-solid fa-phone"></i>

<input
    type="tel"
    name="phone"
    placeholder="Phone Number"
    pattern="[0-9]{10}"
    maxlength="10"
    inputmode="numeric"
    required
>

</div>

<div class="input-box">

<i class="fa-solid fa-location-dot"></i>

<textarea
    name="address"
    placeholder="Address"
    required
></textarea>

</div>

<div class="btn-center">

<button
    type="submit"
    name="register"
>

REGISTER NOW

</button>

</div>

</form>

</div>

</div>

<?php include 'footer.php'; ?>

</body>

</html>