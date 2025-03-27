<?php
session_name(name:$username);
session_start();
require 'db_connect.php';

// Kiểm tra nếu chưa đăng nhập
if (!isset($_SESSION['user_id'])) {
    $_SESSION['error'] = "Bạn cần đăng nhập để thanh toán!";
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Truy vấn giỏ hàng của user
$query = "SELECT cart.id AS cart_id, products.name, products.price, cart.quantity
          FROM cart
          JOIN products ON cart.product_id = products.id
          WHERE cart.user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$total_price = 0;
$cart_items = [];

while ($row = $result->fetch_assoc()) {
    $cart_items[] = $row;
    $total_price += $row['price'] * $row['quantity'];
}

if (empty($cart_items)) {
    $_SESSION['error'] = "Giỏ hàng của bạn trống!";
    header("Location: cart.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Xác nhận đơn hàng</title>
</head>
<body>

<h2>Xác nhận đơn hàng</h2>

<!-- Hiển thị danh sách sản phẩm -->
<table border="1">
    <tr>
        <th>Tên sản phẩm</th>
        <th>Giá</th>
        <th>Số lượng</th>
        <th>Tổng</th>
    </tr>
    <?php foreach ($cart_items as $item): ?>
        <tr>
            <td><?php echo htmlspecialchars($item['name']); ?></td>
            <td><?php echo number_format($item['price']); ?> VNĐ</td>
            <td><?php echo $item['quantity']; ?></td>
            <td><?php echo number_format($item['price'] * $item['quantity']); ?> VNĐ</td>
        </tr>
    <?php endforeach; ?>
</table>

<h3>Tổng tiền: <?php echo number_format($total_price); ?> VNĐ</h3>

<!-- Form nhập thông tin giao hàng -->
<form action="process_checkout.php" method="POST">
    <label>Họ và tên:</label><br>
    <input type="text" name="full_name" required><br>

    <label>Số điện thoại:</label><br>
    <input type="text" name="phone" required><br>

    <label>Địa chỉ giao hàng:</label><br>
    <textarea name="address" required></textarea><br>

    <label>Phương thức thanh toán:</label><br>
    <select name="payment_method">
        <option value="COD">Thanh toán khi nhận hàng</option>
        <option value="Bank Transfer">Chuyển khoản ngân hàng</option>
    </select><br><br>

    <button type="submit">Xác nhận và thanh toán</button>
</form>

<a href="cart.php">Quay lại giỏ hàng</a>

</body>
</html>
