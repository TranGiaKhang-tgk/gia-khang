<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang chủ</title>
</head>
<body>

<?php if (isset($_SESSION['user_id'])): ?>
    <!-- Nếu đã đăng nhập -->
    <h2>Chào mừng, <?php echo htmlspecialchars($_SESSION['user_username']); ?>!</h2>
    <a href="logout.php">Đăng xuất</a>
    <a href="cart.php">Xem giỏ hàng</a>
    <a href="my_orders.php">
        <button>Xem đơn hàng</button> <!-- Nút mới -->
    </a>
<?php else: ?>
    <!-- Nếu chưa đăng nhập -->
    <h2>Chào mừng đến với cửa hàng!</h2>
    <a href="login.php">Đăng nhập</a>
    <a href="register.php">Đăng ký</a>
<?php endif; ?>

<!-- Danh sách sản phẩm -->
<h2>Danh sách sản phẩm</h2>
<table border="1">
    <tr>
        <th>Tên sản phẩm</th>
        <th>Giá</th>
        <th>Mô tả</th>
        <th>Hành động</th>
    </tr>

    <?php
    require 'db_connect.php';
    $result = $conn->query("SELECT * FROM products");

    while ($row = $result->fetch_assoc()):
    ?>
        <tr>
            <td><?php echo htmlspecialchars($row['name']); ?></td>
            <td><?php echo number_format($row['price']); ?> VNĐ</td>
            <td><?php echo htmlspecialchars($row['description']); ?></td>
            <td>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <form action="themgiohang.php" method="POST">
                        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                        <input type="hidden" name="name" value="<?php echo $row['name']; ?>">
                        <input type="hidden" name="price" value="<?php echo $row['price']; ?>">
                        <input type="number" name="quantity" value="1" min="1" required>
                        <button type="submit">Thêm vào giỏ</button>
                    </form>
                <?php else: ?>
                    <p><a href="login.php">Đăng nhập để mua hàng</a></p>
                <?php endif; ?>
            </td>
        </tr>
    <?php endwhile; ?>
</table>

</body>
</html>