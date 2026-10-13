<?php
session_start();
include("conn.php");

/* =====================================================
   ADMIN LOGIN CHECK
===================================================== */
require_once(__DIR__ . "/auth_check.php");

/* =====================================================
   ADD BRAND
===================================================== */
if (isset($_POST['submit'])) {
    $b_name = mysqli_real_escape_string($conn, $_POST['brand_name']);
    $b_des = mysqli_real_escape_string($conn, $_POST['brand_description']);

    $dup = mysqli_query($conn, "SELECT * FROM brand WHERE `Brand Name`='$b_name'");

    if (mysqli_num_rows($dup) > 0) {
        echo "<script>alert('Brand Already Exists');</script>";
    } else {
        $qry = "INSERT INTO brand (`Brand Name`, `Brand Description`) VALUES ('$b_name', '$b_des')";
        $result = mysqli_query($conn, $qry);

        if ($result) {
            echo "<script>alert('Brand Added Successfully'); window.location='brand.php';</script>";
            exit();
        } else {
            echo "<script>alert('Brand Add Failed');</script>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Brand - Time Craft Admin</title>
    <link rel="icon" type="image/png" href="../assets/img/logo/icon.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="admin.css">
</head>
<body>

<?php $active_page = 'add_brand'; include 'admin_sidebar.php'; ?>

<div class="admin-main">
    <div class="brand-content">
        <div class="container">
            <h2><i class="fa-solid fa-plus"></i> Add Brand</h2>

            <form method="post">
                <label>Brand Name</label>
                <input type="text" name="brand_name" placeholder="Enter Brand Name" required>

                <label>Brand Description</label>
                <textarea name="brand_description" rows="4" placeholder="Enter Description"></textarea>

                <div class="btn-group">
                    <button type="submit" name="submit" class="submit-btn">
                        <i class="fa-solid fa-plus"></i> Submit
                    </button>
                    <button type="reset" class="reset-btn">
                        <i class="fa-solid fa-rotate-left"></i> Reset
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

</body>
</html>