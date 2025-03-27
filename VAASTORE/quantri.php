<?php
session_start();

// Kiểm tra nếu không phải admin thì chuyển hướng về trang đăng nhập
if (!isset($_SESSION['admin_id']) || $_SESSION['admin_level'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Kiểm tra biến session trước khi sử dụng
$admin_username = isset($_SESSION['admin_username']) ? $_SESSION['admin_username'] : "Admin";
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang Quản Trị</title>
</head>
<body>
    <h2>Chào mừng, <?php echo htmlspecialchars($admin_username); ?>!</h2>

    <ul>
        <li><a href="manage_users.php">Quản lý người dùng</a></li>
        <li><a href="manage_products.php">Quản lý sản phẩm</a></li>
        <li><a href="manage_orders.php">Quản lý đơn hàng</a></li>
        <li><a href="logout.php">Đăng xuất</a></li>
    </ul>
</body>
</html>
