<?php

date_default_timezone_set("Asia/Kolkata");

$host = "localhost";
$user = "root";
$password = "";
$database = "watch_store";

$conn = mysqli_connect($host, $user, $password, $database);

if (!$conn) {
    die("Database Connection Failed: " . mysqli_connect_error());
}

@mysqli_query($conn, "SET time_zone = '+05:30'");

// Auto-migrate: Add admin_deleted column if it doesn't exist
$chk_col = @mysqli_query($conn, "SHOW COLUMNS FROM `order` LIKE 'admin_deleted'");
if ($chk_col && mysqli_num_rows($chk_col) == 0) {
    @mysqli_query($conn, "ALTER TABLE `order` ADD COLUMN `admin_deleted` TINYINT(1) NOT NULL DEFAULT 0");
}

// Auto-migrate: Ensure login_time and logout_time support 12-hour format with AM/PM
$chk_type = @mysqli_query($conn, "SHOW COLUMNS FROM `reg` LIKE 'login_time'");
if ($chk_type && $row_t = mysqli_fetch_assoc($chk_type)) {
    if (stripos($row_t['Type'], 'varchar') === false) {
        @mysqli_query($conn, "ALTER TABLE `reg` MODIFY COLUMN `login_time` VARCHAR(50) NULL DEFAULT NULL, MODIFY COLUMN `logout_time` VARCHAR(50) NULL DEFAULT NULL");
    }
}

?>