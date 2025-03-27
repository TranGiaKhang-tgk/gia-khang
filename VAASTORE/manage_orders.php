<?php
session_start();
require 'db_connect.php';

if (!isset($_SESSION['admin_id']) || $_SESSION['admin_level'] !== 'admin') {
    header("Location: login.php");
    exit();
}


// Lấy danh sách đơn hàng
$query = "SELECT orders.id, user.username, orders.total_price, orders.status, orders.created_at,  orders.payment_method
          FROM orders 
          JOIN user ON orders.user_id = user.id 
          ORDER BY orders.created_at DESC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="vi"
<head>
    <meta charset="UTF-8">
    <title>Quản lý đơn hàng</title>
</head>
<body>

<h2>Quản lý đơn hàng</h2>

<table border="1">
    <tr>
        <th>ID</th>
        <th>Người đặt</th>
        <th>Tổng tiền</th>
        <th>Phương thức thanh toán</th> 
        <th>Trạng thái</th>
        <th>Ngày tạo</th>
        <th>Hành động</th>
    </tr>
    <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?php echo $row['id']; ?></td>
            <td><?php echo htmlspecialchars($row['username']); ?></td>
            <td><?php echo number_format($row['total_price']); ?> VNĐ</td>
            <td><?php echo htmlspecialchars($row['payment_method']); ?></td>
            <td><?php echo ucfirst($row['status']); ?></td>
            <td><?php echo $row['created_at']; ?></td>
            <td>
                <form action="update_order_status.php" method="POST">
                    <input type="hidden" name="order_id" value="<?php echo $row['id']; ?>">
                    <select name="status">
                        <option value="pending" <?php if ($row['status'] == 'pending') echo 'selected'; ?>>Chờ xử lý</option>
                        <option value="confirmed" <?php if ($row['status'] == 'confirmed') echo 'selected'; ?>>Đã xác nhận</option>
                        <option value="shipping" <?php if ($row['status'] == 'shipping') echo 'selected'; ?>>Đang giao hàng</option>
                        <option value="completed" <?php if ($row['status'] == 'completed') echo 'selected'; ?>>Hoàn thành</option>
                        <option value="canceled" <?php if ($row['status'] == 'canceled') echo 'selected'; ?>>Đã hủy</option>
                    </select>
                    <button type="submit">Cập nhật</button>
                </form>
            </td>
        </tr>
    <?php endwhile; ?>
</table>

<a href="quantri.php">Quay lại trang chủ</a>

</body>
</html>
