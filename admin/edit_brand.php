<?php
session_start();
include("conn.php");

/* =====================================================
   ADMIN LOGIN CHECK
===================================================== */
require_once(__DIR__ . "/auth_check.php");

if (!isset($_GET['id'])) {
    header("Location: brand.php");
    exit();
}

$id = intval($_GET['id']);

$qry = "SELECT * FROM brand WHERE id='$id'";
$result = mysqli_query($conn, $qry);

if (!$result || mysqli_num_rows($result) == 0) {
    echo "<script>
    alert('Brand Not Found');
    window.location='brand.php';
    </script>";
    exit();
}

$row = mysqli_fetch_assoc($result);

if (isset($_POST['update'])) {
    $brand_name = mysqli_real_escape_string($conn, $_POST['brand_name']);
    $brand_description = mysqli_real_escape_string($conn, $_POST['brand_description']);

    $update = "UPDATE brand 
               SET `Brand Name`='$brand_name',
                   `Brand Description`='$brand_description'
               WHERE id='$id'";

    $res = mysqli_query($conn, $update);

    if ($res) {
        echo "<script>
        alert('Brand Updated Successfully');
        window.location='brand.php';
        </script>";
        exit();
    } else {
        echo "<script>alert('Error updating brand: " . addslashes(mysqli_error($conn)) . "');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Brand - Time Craft Admin</title>
    <link rel="icon" type="image/png" href="../assets/img/logo/icon.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="admin.css">
</head>
<body>

<?php $active_page = 'brands'; include 'admin_sidebar.php'; ?>

<div class="admin-main">
    <div class="brand-content">
        <div class="container">
            <h2><i class="fa-solid fa-pen-to-square"></i> Edit Brand</h2>

            <form method="post">
                <label>Brand Name</label>
                <input
                    type="text"
                    name="brand_name"
                    value="<?php echo htmlspecialchars($row['Brand Name']); ?>"
                    required
                >

                <label>Brand Description</label>
                <textarea
                    name="brand_description"
                    rows="5"
                ><?php echo htmlspecialchars($row['Brand Description']); ?></textarea>

                <div class="btn-group">
                    <button type="submit" name="update" class="submit-btn">
                        <i class="fa-solid fa-pen-to-square"></i> Update Brand
                    </button>
                    <a href="brand.php" class="reset-btn" style="text-align: center; text-decoration: none; display: inline-block;">
                        <i class="fa-solid fa-arrow-left"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

</body>
</html>