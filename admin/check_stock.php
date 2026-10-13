<?php
require_once __DIR__ . '/../conn.php';
$r = mysqli_query($conn, 'SELECT id, product_name, brand, stock FROM product');
while ($row = mysqli_fetch_assoc($r)) {
    echo "ID: " . $row['id'] . " | " . $row['brand'] . " - " . $row['product_name'] . " | Stock: " . $row['stock'] . "\n";
}

