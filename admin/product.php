<?php
session_start();
include("conn.php");

/* =====================================================
   ADMIN LOGIN CHECK
===================================================== */
require_once(__DIR__ . "/auth_check.php");

/* =====================================================
   DELETE PRODUCT
===================================================== */
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);

    $qry = "SELECT image FROM product WHERE id='$id'";
    $result = mysqli_query($conn, $qry);
    $row = mysqli_fetch_assoc($result);

    if ($row) {
        $image = "../upload/" . $row['image'];
        if (file_exists($image)) {
            unlink($image);
        }
    }

    $delete_query = "DELETE FROM product WHERE id='$id'";
    $delete_result = mysqli_query($conn, $delete_query);

    if ($delete_result) {
        echo "<script>alert('Product Deleted Successfully'); window.location='product.php';</script>";
        exit();
    } else {
        echo "<script>alert('Product Delete Failed'); window.location='product.php';</script>";
        exit();
    }
}

/* =====================================================
   FETCH PRODUCTS
===================================================== */
$qry = "SELECT * FROM product ORDER BY id DESC";
$result = mysqli_query($conn, $qry);

if (!$result) {
    die("Database Query Error : " . mysqli_error($conn));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Products - Time Craft Admin</title>
    <link rel="icon" type="image/png" href="../assets/img/logo/icon.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="admin.css">
</head>
<body>

<?php $active_page = 'products'; include 'admin_sidebar.php'; ?>

<div class="admin-main">
    <div class="product-content">
        <h1><i class="fa-solid fa-box"></i> View Products</h1>

        <div class="table-box">
            <?php if (mysqli_num_rows($result) > 0) { ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Image</th>
                        <th>Product Name</th>
                        <th>Brand</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['id']); ?></td>
                        <td>
                            <img src="../upload/<?php echo htmlspecialchars($row['image']); ?>" class="product-img" alt="Product Image">
                        </td>
                        <td class="product-name"><?php echo htmlspecialchars($row['product_name']); ?></td>
                        <td class="brand"><?php echo htmlspecialchars($row['brand']); ?></td>
                        <td class="price"><i class="fa-solid fa-indian-rupee-sign"></i> <?php echo number_format($row['price']); ?></td>
                        <td class="stock"><?php echo (int)$row['stock']; ?></td>
                        <td>
                            <?php if ((int)$row['stock'] > 0) { ?>
                                <span class="stock-available"><i class="fa-solid fa-circle-check"></i> Available</span>
                            <?php } else { ?>
                                <span class="stock-out"><i class="fa-solid fa-circle-xmark"></i> Out of Stock</span>
                            <?php } ?>
                        </td>
                        <td>
                            <div class="action-box">
                                <a href="edit_product.php?id=<?php echo $row['id']; ?>" class="edit-btn">
                                    <i class="fa-solid fa-pen-to-square"></i> Edit
                                </a>
                                <a href="product.php?delete=<?php echo $row['id']; ?>" class="delete-btn" onclick="return confirm('Are you sure you want to delete this product?')">
                                    <i class="fa-solid fa-trash"></i> Delete
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
            <?php } else { ?>
            <div class="no-product">
                <i class="fa-solid fa-box-open"></i> &nbsp; No Products Found
            </div>
            <?php } ?>
        </div>
    </div>
</div>

</body>
</html>