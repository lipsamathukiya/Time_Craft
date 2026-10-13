<?php
session_start();
include("conn.php");

/* =====================================================
   ADMIN LOGIN CHECK
===================================================== */
require_once(__DIR__ . "/auth_check.php");

/* =====================================================
   CHECK PRODUCT ID
===================================================== */
if (!isset($_GET['id'])) {
    header("Location: product.php");
    exit();
}

$id = intval($_GET['id']);

/* =====================================================
   FETCH PRODUCT
===================================================== */
$qry = "SELECT * FROM product WHERE id='$id'";
$result = mysqli_query($conn, $qry);

if (!$result || mysqli_num_rows($result) == 0) {
    echo "<script>
    alert('Product Not Found');
    window.location='product.php';
    </script>";
    exit();
}

$row = mysqli_fetch_assoc($result);

/* =====================================================
   UPDATE PRODUCT
===================================================== */
if (isset($_POST['update'])) {
    $product_name = mysqli_real_escape_string($conn, $_POST['product_name']);
    $brand = mysqli_real_escape_string($conn, $_POST['brand']);
    $collection = mysqli_real_escape_string($conn, $_POST['collection']);
    $price = (float)$_POST['price'];
    $stock = (int)$_POST['stock'];

    if ($stock < 0) {
        $stock = 0;
    }

    if (!empty($_FILES['image']['name'])) {
        $image = time() . "_" . basename($_FILES['image']['name']);
        $tmp_name = $_FILES['image']['tmp_name'];
        $upload_path = "../upload/" . $image;

        if (move_uploaded_file($tmp_name, $upload_path)) {
            if (!empty($row['image'])) {
                $old_image = "../upload/" . $row['image'];
                if (file_exists($old_image)) {
                    @unlink($old_image);
                }
            }

            $update_qry = "UPDATE product SET
                    product_name='$product_name',
                    brand='$brand',
                    collection='$collection',
                    price='$price',
                    stock='$stock',
                    image='$image'
                    WHERE id='$id'";
        } else {
            echo "<script>alert('Image Upload Failed');</script>";
            $update_qry = "";
        }
    } else {
        $update_qry = "UPDATE product SET
                product_name='$product_name',
                brand='$brand',
                collection='$collection',
                price='$price',
                stock='$stock'
                WHERE id='$id'";
    }

    if (!empty($update_qry)) {
        $update_result = mysqli_query($conn, $update_qry);
        if ($update_result) {
            echo "<script>
            alert('Product Updated Successfully');
            window.location='product.php';
            </script>";
            exit();
        } else {
            echo "<script>alert('Database Error: " . addslashes(mysqli_error($conn)) . "');</script>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product - Time Craft Admin</title>
    <link rel="icon" type="image/png" href="../assets/img/logo/icon.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="admin.css">
</head>
<body>

<?php $active_page = 'products'; include 'admin_sidebar.php'; ?>

<div class="admin-main">
    <div class="product-content">
        <div class="form-box">
            <h2><i class="fa-solid fa-pen-to-square"></i> Edit Product</h2>

            <form method="post" enctype="multipart/form-data">
                <label>Product Name</label>
                <input type="text" name="product_name" value="<?php echo htmlspecialchars($row['product_name']); ?>" required>

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
                    if (!in_array($row['brand'], $db_brands) && !empty($row['brand'])) {
                        $db_brands[] = $row['brand'];
                    }
                    if (empty($db_brands)) {
                        $db_brands = ['Rolex', 'Casio', 'Titan', 'Fossil'];
                    }
                    foreach ($db_brands as $bname) {
                        $sel = ($row['brand'] == $bname) ? 'selected' : '';
                        echo '<option value="' . htmlspecialchars($bname) . '" ' . $sel . '>' . htmlspecialchars($bname) . '</option>';
                    }
                    ?>
                </select>

                <label>Collection</label>
                <select name="collection" required>
                    <option value="">Select Collection</option>
                    <?php
                    $collections = ['Men', 'Women', 'Luxury', 'Smart', 'Sport'];
                    foreach ($collections as $col) {
                        $c_sel = ($row['collection'] == $col) ? 'selected' : '';
                        echo '<option value="' . $col . '" ' . $c_sel . '>' . $col . '</option>';
                    }
                    ?>
                </select>

                <label>Price (₹)</label>
                <input type="number" name="price" value="<?php echo htmlspecialchars($row['price']); ?>" step="0.01" min="0" required>

                <label>Stock Quantity</label>
                <input type="number" name="stock" value="<?php echo htmlspecialchars($row['stock']); ?>" min="0" required>

                <label>Current Product Image</label>
                <div style="margin-bottom: 15px;">
                    <img src="../upload/<?php echo htmlspecialchars($row['image']); ?>" alt="Current Product Image" style="width: 90px; height: 90px; object-fit: cover; border-radius: 8px; border: 1px solid #FFD700;" onerror="this.src='../assets/img/logo/logo.png';">
                </div>

                <label>Change Product Image (Optional)</label>
                <input type="file" name="image" accept="image/*">

                <div class="btn-group" style="display: flex; gap: 15px; margin-top: 10px;">
                    <button type="submit" name="update">
                        <i class="fa-solid fa-pen-to-square"></i> Update Product
                    </button>
                    <a href="product.php" style="background: #333; color: #FFD700; border: 1px solid #FFD700; padding: 12px 25px; border-radius: 8px; text-decoration: none; font-weight: bold; display: inline-flex; align-items: center; justify-content: center; width: 100%;">
                        <i class="fa-solid fa-arrow-left" style="margin-right: 8px;"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

</body>
</html>