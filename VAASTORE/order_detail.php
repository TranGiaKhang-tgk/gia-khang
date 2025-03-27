<?php
session_start();
require 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    $_SESSION['error'] = "Bạn cần đăng nhập để xem chi tiết đơn hàng!";
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id'])) {
    $_SESSION['error'] = "Không tìm thấy đơn hàng!";
    header("Location: my_orders.php");
    exit();
}

$order_id = $_GET['id'];
$user_id = $_SESSION['user_id'];

// Kiểm tra đơn hàng có thuộc về user không
$order_query = "SELECT * FROM orders WHERE id = ? AND user_id = ?";
$stmt = $conn->prepare($order_query);
$stmt->bind_param("ii", $order_id, $user_id);
$stmt->execute();
$order_result = $stmt->get_result();

if ($order_result->num_rows == 0) {
    $_SESSION['error'] = "Bạn không có quyền xem đơn hàng này!";
    header("Location: my_orders.php");
    exit();
}

$order = $order_result->fetch_assoc();

// Lấy danh sách sản phẩm trong đơn hàng
$item_query = "SELECT products.name, order_items.quantity, order_items.price 
               FROM order_items 
               JOIN products ON order_items.product_id = products.id 
               WHERE order_items.order_id = ?";
$stmt = $conn->prepare($item_query);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$item_result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Chi tiết đơn hàng</title>
</head>
<body>

<h2>Chi tiết đơn hàng #<?php echo $order['id']; ?></h2>
<p>Ngày đặt: <?php echo $order['created_at']; ?></p>
<p>Thanh toán: <?php echo htmlspecialchars($order['payment_method']); ?></p>
<p>Trạng thái: <?php echo htmlspecialchars($order['status']); ?></p>

<h3>Sản phẩm:</h3>
<table border="1">
    <tr>
        <th>Tên sản phẩm</th>
        <th>Số lượng</th>
        <th>Giá</th>
        <th>Tổng</th>
    </tr>
    <?php while ($item = $item_result->fetch_assoc()): ?>
    <tr>
        <td><?php echo htmlspecialchars($item['name']); ?></td>
        <td><?php echo $item['quantity']; ?></td>
        <td><?php echo number_format($item['price']); ?> VNĐ</td>
        <td><?php echo number_format($item['quantity'] * $item['price']); ?> VNĐ</td>
    </tr>
    <?php endwhile; ?>
</table>

<h3>Tổng tiền: <?php echo number_format($order['total_price']); ?> VNĐ</h3>

<a href="my_orders.php">Quay lại danh sách đơn hàng</a>

</body>
</html>

<?php
$stmt->close();
?>
