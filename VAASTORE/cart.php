<?php
session_start();
require 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    $_SESSION['error'] = "Bạn cần đăng nhập để xem giỏ hàng!";
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$query = "SELECT cart.id AS cart_id, products.name, products.price, cart.quantity
          FROM cart
          JOIN products ON cart.product_id = products.id
          WHERE cart.user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Giỏ hàng của bạn</title>
</head>
<body>

<h2>Giỏ hàng của bạn</h2>

<?php if ($result->num_rows > 0): ?>
    <table border="1">
        <tr>
            <th>Tên sản phẩm</th>
            <th>Giá</th>
            <th>Số lượng</th>
            <th>Tổng</th>
            <th>Hành động</th>
        </tr>
        <?php
        $total_price = 0;
        while ($row = $result->fetch_assoc()):
            $total = $row['price'] * $row['quantity'];
            $total_price += $total;
        ?>
        <tr>
            <td><?php echo htmlspecialchars($row['name']); ?></td>
            <td><?php echo number_format($row['price']); ?> VNĐ</td>
            <td><?php echo $row['quantity']; ?></td>
            <td><?php echo number_format($total); ?> VNĐ</td>
            <td>
                <a href="remove_from_cart.php?id=<?php echo $row['cart_id']; ?>">Xóa</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
    <h3>Tổng tiền: <?php echo number_format($total_price); ?> VNĐ</h3>
    <a href="xacnhandonhang.php">Thanh toán</a>
<?php else: ?>
    <p>Giỏ hàng của bạn đang trống!</p>
<?php endif; ?>

<a href="index.php">Tiếp tục mua sắm</a>

</body>
</html>
