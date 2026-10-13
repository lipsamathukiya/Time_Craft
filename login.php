<?php

session_start();

include("conn.php");

date_default_timezone_set("Asia/Kolkata");

$error_message = "";


/* =====================================================
   LOGIN PROCESS
===================================================== */

if (isset($_POST['login']))
{

    $email = mysqli_real_escape_string(
        $conn,
        $_POST['email']
    );

    $password = $_POST['password'];


    /* =================================================
       PASSWORD CHECK
    ================================================= */

    if (empty($password))
    {

        $error_message =
            "Please enter your password.";

    }
    else
    {

        /* =================================================
           FETCH USER
        ================================================= */

        $query =
            "SELECT *
             FROM reg
             WHERE email='$email'";

        $result =
            mysqli_query(
                $conn,
                $query
            );


        /* =================================================
           CHECK QUERY
        ================================================= */

        if (!$result)
        {

            die(
                "Database Error: "
                . mysqli_error($conn)
            );

        }


        /* =================================================
           EMAIL FOUND
        ================================================= */

        if (mysqli_num_rows($result) > 0)
        {

            $row =
                mysqli_fetch_assoc(
                    $result
                );


            /* =================================================
               PASSWORD CHECK
            ================================================= */

            if ($password == $row['pass'])
            {

                /* =================================================
                   LOGIN DATE & TIME
                ================================================= */

                $login_date =
                    date("Y-m-d");

                $login_time =
                    date("h:i:s A");

                $update_login =
                    "UPDATE reg
                     SET
                        login_date='$login_date',
                        login_time='$login_time'
                     WHERE id='".$row['id']."'";

                if (!mysqli_query(
                    $conn,
                    $update_login
                ))
                {

                    die(
                        "Login Date/Time Error: "
                        . mysqli_error($conn)
                    );

                }


                /* =================================================
                   ADMIN / USER SEPARATE SESSION & REDIRECT
                ================================================= */

                if ($row['u_type'] == "admin")
                {

                    // Isolate admin session variables so user session in another tab remains intact
                    $_SESSION['admin_id'] = $row['id'];
                    $_SESSION['admin_name'] = $row['name'];
                    $_SESSION['admin_email'] = $row['email'];
                    $_SESSION['admin_u_type'] = 'admin';
                    $_SESSION['admin_logged_in'] = true;
                    $_SESSION['u_type'] = 'admin';

                    // Ensure admin login does NOT show as logged in customer on user side
                    if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $row['id']) {
                        unset($_SESSION['user_id'], $_SESSION['uid'], $_SESSION['user_name'], $_SESSION['name'], $_SESSION['user_email']);
                    }

                    header(
                        "Location: admin/dashboard.php"
                    );

                    exit();

                }
                else
                {

                    // Set user session variables
                    $_SESSION['user_id'] = $row['id'];
                    $_SESSION['uid'] = $row['id'];
                    $_SESSION['user_name'] = $row['name'];
                    $_SESSION['name'] = $row['name'];
                    $_SESSION['email'] = $row['email'];
                    $_SESSION['user_email'] = $row['email'];
                    $_SESSION['user_logged_in'] = true;
                    $_SESSION['user_type'] = $row['u_type'];

                    // Do not overwrite u_type if an admin is already logged in on another tab
                    if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
                        $_SESSION['u_type'] = $row['u_type'];
                    }

                    // Clean admin session only if it was tied to this same user ID
                    if (isset($_SESSION['admin_id']) && $_SESSION['admin_id'] == $row['id']) {
                        unset($_SESSION['admin_id'], $_SESSION['admin_name'], $_SESSION['admin_email'], $_SESSION['admin_u_type'], $_SESSION['admin_logged_in']);
                    }

                    if (isset($_SESSION['login_redirect']) && !empty($_SESSION['login_redirect'])) {
                        $target = $_SESSION['login_redirect'];
                        unset($_SESSION['login_redirect']);
                        header("Location: " . $target);
                        exit();
                    }

                    header(
                        "Location: index.php"
                    );

                    exit();

                }

            }
            else
            {

                $error_message =
                    "Wrong Password!";

            }

        }
        else
        {

            $error_message =
                "Email not found!";

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

<title>Time Craft - Login</title>
    <link rel="icon" type="image/png" href="assets/img/logo/icon.png">

    <link rel="shortcut icon" type="image/png" href="assets/img/logo/icon.png">



<!-- =====================================================
     FONT AWESOME
===================================================== -->

<link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
>


<style>


/* =====================================================
   RESET
===================================================== */

*{

    margin:0;

    padding:0;

    box-sizing:border-box;

    font-family:
        Arial,
        Helvetica,
        sans-serif;

}


/* =====================================================
   BODY
===================================================== */

html,
body{

    width:100%;

    min-height:100%;

    overflow-x:hidden;

    color:#fff;

}


/* =====================================================
   BACKGROUND VIDEO
===================================================== */

.bg-video{

    position:fixed;

    top:0;

    left:0;

    width:100%;

    height:100%;

    object-fit:cover;

    z-index:-2;

}


/* =====================================================
   MAIN OVERLAY
===================================================== */

.overlay{

    width:100%;

    min-height:100vh;

    display:flex;

    justify-content:center;

    align-items:center;

    position:relative;

    padding:20px;

}


/* =====================================================
   DARK OVERLAY
===================================================== */

.overlay::before{

    content:"";

    position:absolute;

    top:0;

    left:0;

    width:100%;

    height:100%;

    background:
        rgba(0,0,0,.60);

    z-index:0;

}


/* =====================================================
   FORM BOX
===================================================== */

.form-box{

    position:relative;

    z-index:1;

    width:450px;

    background:
        rgba(0,0,0,.45);

    backdrop-filter:
        blur(12px);

    border:
        1px solid
        rgba(255,255,255,.20);

    padding:35px;

    border-radius:18px;

    color:#fff;

    box-shadow:
        0 0 20px
        rgba(0,0,0,.5);

    transition:.4s;

}


/* =====================================================
   FORM BOX HOVER
===================================================== */

.form-box:hover{

    transform:
        translateY(-5px);

    border:
        1px solid
        #d9ad43;

    box-shadow:
        0 0 30px
        rgba(217,173,67,.7);

}


/* =====================================================
   TITLE
===================================================== */

.title{

    color:#d9ad43;

    font-size:40px;

    text-align:center;

    font-weight:bold;

    margin-bottom:10px;

}


/* =====================================================
   SUBTITLE
===================================================== */

.subtitle{

    text-align:center;

    color:#ddd;

    margin-bottom:30px;

}


/* =====================================================
   ERROR MESSAGE
===================================================== */

.alert-msg{

    background:
        rgba(255,0,0,.15);

    border:
        1px solid
        #ff5555;

    color:#ff7777;

    padding:12px;

    border-radius:7px;

    text-align:center;

    margin-bottom:20px;

    font-size:14px;

}


/* =====================================================
   INPUT BOX
===================================================== */

.input-box{

    display:flex;

    align-items:center;

    border:
        1px solid
        #777;

    padding:12px;

    border-radius:8px;

    margin-bottom:8px;

    transition:.3s;

}


/* =====================================================
   INPUT FOCUS
===================================================== */

.input-box:focus-within{

    border-color:#d9ad43;

    box-shadow:
        0 0 8px
        rgba(217,173,67,.3);

}


/* =====================================================
   INPUT ICON
===================================================== */

.input-box i{

    color:#d9ad43;

    margin-right:12px;

    font-size:18px;

}


/* =====================================================
   INPUT
===================================================== */

.input-box input{

    width:100%;

    background:none;

    border:none;

    outline:none;

    color:#fff;

    font-size:16px;

}


/* =====================================================
   PLACEHOLDER
===================================================== */

.input-box input::placeholder{

    color:#ddd;

}



/* =====================================================
   OPTIONS
===================================================== */

.options{

    display:flex;

    align-items:center;

    margin-bottom:25px;

    font-size:14px;

}


/* =====================================================
   SHOW PASSWORD LABEL
===================================================== */

.options label{

    display:flex;

    align-items:center;

    gap:7px;

    cursor:pointer;

    color:#ddd;

}


/* =====================================================
   CHECKBOX
===================================================== */

.options input[type="checkbox"]{

    accent-color:#d9ad43;

    width:16px;

    height:16px;

    cursor:pointer;

}


/* =====================================================
   LOGIN BUTTON
===================================================== */

.login-btn{

    display:flex;

    justify-content:center;

    align-items:center;

    width:100%;

    height:50px;

    background:#d9ad43;

    color:#000;

    border:none;

    border-radius:8px;

    font-size:18px;

    font-weight:bold;

    cursor:pointer;

    transition:.3s;

}


/* =====================================================
   LOGIN BUTTON HOVER
===================================================== */

.login-btn:hover{

    background:#c79a26;

    transform:
        translateY(-3px);

    box-shadow:
        0 8px 20px
        rgba(217,173,67,.5);

}


/* =====================================================
   REGISTER
===================================================== */

.register{

    text-align:center;

    margin-top:22px;

    color:#ddd;

}


/* =====================================================
   REGISTER LINK
===================================================== */

.register a{

    color:#d9ad43;

    text-decoration:none;

    font-weight:bold;

}


/* =====================================================
   REGISTER HOVER
===================================================== */

.register a:hover{

    text-decoration:underline;

}


/* =====================================================
   MOBILE
===================================================== */

@media(max-width:576px){

    .form-box{

        width:95%;

        padding:25px 20px;

    }


    .title{

        font-size:32px;

    }

}

</style>

</head>


<body>


<!-- =====================================================
     BACKGROUND VIDEO
===================================================== -->

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


<!-- =====================================================
     MAIN OVERLAY
===================================================== -->

<div class="overlay">


    <!-- =================================================
         FORM BOX
    ================================================== -->

    <div class="form-box">


        <!-- =================================================
             TITLE
        ================================================== -->

        <h1 class="title">

            Time Craft

        </h1>


        <!-- =================================================
             SUBTITLE
        ================================================== -->

        <p class="subtitle">

            Login to your account

        </p>


        <!-- =================================================
             LOGIN FORM
        ================================================== -->

        <form
            method="POST"
            action=""
        >


            <!-- =================================================
                 ERROR MESSAGE
            ================================================== -->

            <?php

            if (!empty($error_message))
            {

                echo
                "<div class='alert-msg'>"
                .
                htmlspecialchars(
                    $error_message
                )
                .
                "</div>";

            }

            ?>


            <!-- =================================================
                 EMAIL
            ================================================== -->

            <div class="input-box">

                <i
                    class="fa-solid fa-envelope"
                ></i>

                <input
                    type="email"
                    name="email"
                    placeholder="Email"
                    required
                >

            </div>


            <!-- =================================================
                 PASSWORD
            ================================================== -->

            <div class="input-box">

                <i
                    class="fa-solid fa-lock"
                ></i>

                <input
                    type="password"
                    name="password"
                    id="passwordField"
                    placeholder="Password • 8 characters"
                    minlength="8"
                    maxlength="8"
                    required
                >

            </div>



            <!-- =================================================
                 SHOW PASSWORD
            ================================================== -->

            <div class="options">

                <label>

                    <input
                        type="checkbox"
                        id="showPassword"
                    >

                    <span>
                        Show Password
                    </span>

                </label>

            </div>


            <!-- =================================================
                 LOGIN BUTTON
            ================================================== -->

            <button
                type="submit"
                name="login"
                class="login-btn"
            >

                LOGIN NOW

            </button>


        </form>


        <!-- =================================================
             REGISTER
        ================================================== -->

        <div class="register">

            Don't have an account?

            <a href="ragister.php">

                Register

            </a>

        </div>


    </div>

</div>


<script>


/* =====================================================
   SHOW / HIDE PASSWORD
===================================================== */

document
.getElementById("showPassword")
.addEventListener(
    "change",
    function()
    {

        const password =
            document.getElementById(
                "passwordField"
            );


        if (this.checked)
        {

            password.type =
                "text";

        }
        else
        {

            password.type =
                "password";

        }

    }
);


/* =====================================================
   MAXIMUM 8 CHARACTERS
===================================================== */

document
.getElementById("passwordField")
.addEventListener(
    "input",
    function()
    {

        if (this.value.length > 8)
        {

            this.value =
                this.value.substring(
                    0,
                    8
                );

        }

    }
);


/* =====================================================
   FORM VALIDATION
===================================================== */

document
.querySelector("form")
.addEventListener(
    "submit",
    function(event)
    {

        const password =
            document.getElementById(
                "passwordField"
            ).value;


        if (password.length !== 8)
        {

            event.preventDefault();

            alert(
                "Password must be exactly 8 characters."
            );

        }

    }
);

</script>


</body>

</html>
