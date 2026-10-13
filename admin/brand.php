<?php
session_start();
include("conn.php");

/* =====================================================
   ADMIN LOGIN CHECK
===================================================== */
require_once(__DIR__ . "/auth_check.php");

/* =====================================================
   DELETE BRAND
===================================================== */
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $query = "DELETE FROM brand WHERE id='$id'";

    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Brand Deleted Successfully'); window.location='brand.php';</script>";
        exit();
    } else {
        echo mysqli_error($conn);
    }
}

/* =====================================================
   FETCH BRANDS
===================================================== */
$query = "SELECT * FROM brand ORDER BY id DESC";
$result = mysqli_query($conn, $query);

if (!$result) {
    die("Database Error : " . mysqli_error($conn));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Brands - Time Craft Admin</title>
    <link rel="icon" type="image/png" href="../assets/img/logo/icon.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="admin.css">
</head>
<body>

<?php $active_page = 'brands'; include 'admin_sidebar.php'; ?>

<div class="admin-main">
    <div class="brand-content">
        <div class="title">
            <h1><i class="fa-solid fa-tags"></i> View Brands</h1>
            <p>Our Watch Brands</p>
        </div>

        <?php if (mysqli_num_rows($result) > 0) { ?>
        <div class="brand-grid">
            <?php while ($row = mysqli_fetch_assoc($result)) {
                $brand_name = strtolower(trim($row['Brand Name']));
            ?>
            <div class="brand-card">
                <div class="brand-icon">
                    <?php
                    if ($brand_name == 'rolex') {
                        echo '<i class="fa-solid fa-crown"></i>';
                    } elseif ($brand_name == 'casio') {
                        echo '<i class="fa-solid fa-stopwatch"></i>';
                    } elseif ($brand_name == 'titan') {
                        echo '<i class="fa-solid fa-gem"></i>';
                    } elseif ($brand_name == 'fossil') {
                        echo '<i class="fa-solid fa-clock"></i>';
                    } else {
                        echo '<i class="fa-solid fa-tag"></i>';
                    }
                    ?>
                </div>

                <h2><?php echo htmlspecialchars($row['Brand Name'], ENT_QUOTES, 'UTF-8'); ?></h2>
                <p><?php echo htmlspecialchars($row['Brand Description'], ENT_QUOTES, 'UTF-8'); ?></p>

                <div class="buttons">
                    <a href="edit_brand.php?id=<?php echo $row['id']; ?>" class="edit-btn">
                        <i class="fa-solid fa-pen-to-square"></i> Edit
                    </a>
                    <a href="brand.php?delete=<?php echo $row['id']; ?>" class="delete-btn" onclick="return confirm('Are you sure you want to delete this brand?');">
                        <i class="fa-solid fa-trash"></i> Delete
                    </a>
                </div>
            </div>
            <?php } ?>
        </div>
        <?php } else { ?>
        <div class="no-brand">
            <h2>No Brands Found</h2>
            <p>No watch brands are available.</p>
        </div>
        <?php } ?>
    </div>
</div>

</body>
</html>