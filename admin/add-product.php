<?php
session_start();
include("conn.php");

/* =====================================================
   ADMIN LOGIN CHECK
===================================================== */
require_once(__DIR__ . "/auth_check.php");

/* =====================================================
   ADD PRODUCT
===================================================== */
if (isset($_POST['submit'])) {
    $product_name = mysqli_real_escape_string($conn, $_POST['product_name']);
    $brand = mysqli_real_escape_string($conn, $_POST['brand']);
    $collection = mysqli_real_escape_string($conn, $_POST['collection']);
    $price = (float) $_POST['price'];
    $stock = (int) $_POST['stock'];

    $image = time() . "_" . basename($_FILES['image']['name']);
    $tmp_name = $_FILES['image']['tmp_name'];

    move_uploaded_file($tmp_name, "../upload/" . $image);

    $qry = "INSERT INTO product (product_name, brand, collection, price, stock, image)
            VALUES ('$product_name', '$brand', '$collection', '$price', '$stock', '$image')";

    $result = mysqli_query($conn, $qry);

    if ($result) {
        echo "<script>alert('Product Added Successfully'); window.location='product.php';</script>";
        exit();
    } else {
        echo "<script>alert('Product Add Failed');</script>";
        echo mysqli_error($conn);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product - Time Craft Admin</title>
    <link rel="icon" type="image/png" href="../assets/img/logo/icon.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="admin.css">
</head>
<body>

<?php $active_page = 'add_product'; include 'admin_sidebar.php'; ?>

<div class="admin-main">
    <div class="product-content">
        <div class="form-box">
            <h2><i class="fa-solid fa-plus"></i> Add Product</h2>

            <form method="post" enctype="multipart/form-data">
                <label>Product Name</label>
                <input type="text" name="product_name" placeholder="Product Name" required>

                <label>Brand</label>
                <select name="brand" required>
                    <option value="">Select Brand</option>
                    <?php
                    $b_res = mysqli_query($conn, "SELECT `Brand Name` FROM brand ORDER BY `Brand Name` ASC");
                    $db_brands = [];
                    if ($b_res && mysqli_num_rows($b_res) > 0) {
                        while ($b_row = mysqli_fetch_assoc($b_res)) {
                            $db_brands[] = $b_row['Brand Name'];
                        }
                    }
                    if (empty($db_brands)) {
                        $db_brands = ['Rolex', 'Casio', 'Titan', 'Fossil'];
                    }
                    foreach ($db_brands as $bname) {
                        echo '<option value="' . htmlspecialchars($bname) . '">' . htmlspecialchars($bname) . '</option>';
                    }
                    ?>
                </select>

                <label>Collection</label>
                <select name="collection" required>
                    <option value="">Select Collection</option>
                    <option value="Men">Men</option>
                    <option value="Women">Women</option>
                    <option value="Luxury">Luxury</option>
                    <option value="Smart">Smart</option>
                    <option value="Sport">Sport</option>
                </select>

                <label>Price (₹)</label>
                <input type="number" name="price" placeholder="Price" min="0" required>

                <label>Stock Quantity</label>
                <input type="number" name="stock" placeholder="Stock Quantity" min="0" required>

                <label>Product Image</label>
                <input type="file" name="image" accept="image/*" required>

                <button type="submit" name="submit">
                    <i class="fa-solid fa-plus"></i> Add Product
                </button>
            </form>
        </div>
    </div>
</div>

</body>
</html>