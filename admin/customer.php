<?php
date_default_timezone_set("Asia/Kolkata");
session_start();
include("conn.php");

/* =====================================================
   ADMIN LOGIN CHECK
===================================================== */
require_once(__DIR__ . "/auth_check.php");

/* =====================================================
   FETCH CUSTOMERS
===================================================== */
$qry = "SELECT * FROM reg ORDER BY id DESC";
$result = mysqli_query($conn, $qry);

if (!$result) {
    die("Database Query Error: " . mysqli_error($conn));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Customers - Time Craft Admin</title>
    <link rel="icon" type="image/png" href="../assets/img/logo/icon.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="admin.css">
</head>
<body>

<?php $active_page = 'customers'; include 'admin_sidebar.php'; ?>

<div class="admin-main">
    <div class="customer-content">
        <div class="page-header">
            <h1><i class="fa-solid fa-users"></i> View Customers</h1>
        </div>

        <div class="table-box">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Mobile</th>
                        <th>Address</th>
                        <th>Gender</th>
                        <th>User Type</th>
                        <th>Login Date</th>
                        <th>Login Time</th>
                        <th>Logout Date</th>
                        <th>Logout Time</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($result) > 0) { ?>
                        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['id']); ?></td>
                            <td class="name"><?php echo htmlspecialchars($row['name']); ?></td>
                            <td class="email"><?php echo htmlspecialchars($row['email']); ?></td>
                            <td class="phone"><?php echo htmlspecialchars($row['phone']); ?></td>
                            <td><?php echo htmlspecialchars($row['address']); ?></td>
                            <td><?php echo htmlspecialchars($row['gender']); ?></td>
                            <td class="type"><?php echo htmlspecialchars($row['u_type']); ?></td>
                            <?php 
                                $disp_login_time = "-";
                                if (!empty($row['login_time']) && $row['login_time'] != "00:00:00") {
                                    $disp_login_time = (stripos($row['login_time'], 'M') !== false) ? $row['login_time'] : date("h:i:s A", strtotime($row['login_time']));
                                }

                                $disp_logout_time = "-";
                                if (!empty($row['logout_time']) && $row['logout_time'] != "00:00:00") {
                                    $disp_logout_time = (stripos($row['logout_time'], 'M') !== false) ? $row['logout_time'] : date("h:i:s A", strtotime($row['logout_time']));
                                }
                            ?>
                            <td class="login"><?php echo !empty($row['login_date']) && $row['login_date'] != '0000-00-00' ? date("d-m-Y", strtotime($row['login_date'])) : "-"; ?></td>
                            <td class="login"><?php echo htmlspecialchars($disp_login_time); ?></td>
                            <td class="logout"><?php echo !empty($row['logout_date']) && $row['logout_date'] != '0000-00-00' ? date("d-m-Y", strtotime($row['logout_date'])) : "-"; ?></td>
                            <td class="logout"><?php echo htmlspecialchars($disp_logout_time); ?></td>
                            <td>
                                <div class="action">
                                    <a href="delete_customer.php?id=<?php echo (int)$row['id']; ?>" class="delete-btn" onclick="return confirm('Are you sure you want to delete this customer?');">
                                        <i class="fa-solid fa-trash"></i> Delete
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php } ?>
                    <?php } else { ?>
                        <tr>
                            <td colspan="12" class="no-user">No Customers Found</td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>