<?php
session_start();
require 'db_connect.php';

if ($_SESSION['user_id']==$row['user_id']) {
    $_SESSION['error'] = "Bạn cần đăng nhập để xem đơn hàng!";
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Lấy danh sách đơn hàng của người dùng
$query = "SELECT id, total_price, payment_method, status, created_at 
          FROM orders 
          WHERE user_id = ? 
          ORDER BY created_at DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Hàm đổi trạng thái thành màu sắc
function hienThiTrangThai($status) {
    switch ($status) {
        case 'Đang xử lý': return '<span style="color: orange;">🟡 Đang xử lý</span>';
        case 'Đã xác nhận': return '<span style="color: blue;">🔵 Đã xác nhận</span>';
        case 'Đang giao': return '<span style="color: darkorange;">🟠 Đang giao</span>';
        case 'Hoàn thành': return '<span style="color: green;">🟢 Hoàn thành</span>';
        case 'Đã hủy': return '<span style="color: red;">🔴 Đã hủy</span>';
        default: return htmlspecialchars($status);
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đơn hàng của tôi</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
            padding: 10px;
            text-align: center;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>

<h2>Danh sách đơn hàng của bạn</h2>

<?php if ($result->num_rows > 0): ?>
    <table>
        <tr>
            <th>ID Đơn hàng</th>
            <th>Ngày đặt</th>
            <th>Thanh toán</th>
            <th>Trạng thái</th>
            <th>Tổng tiền</th>
            <th>Chi tiết</th>
        </tr>
        <?php while ($order = $result->fetch_assoc()): ?>
        <tr>
            <td>#<?php echo $order['id']; ?></td>
            <td><?php echo $order['created_at']; ?></td>
            <td><?php echo htmlspecialchars($order['payment_method']); ?></td>
            <td><?php echo hienThiTrangThai($order['status']); ?></td>
            <td><?php echo number_format($order['total_price']); ?> VNĐ</td>
            <td>
                <a href="order_detail.php?id=<?php echo $order['id']; ?>">Xem</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
<?php else: ?>
    <p>Bạn chưa có đơn hàng nào!</p>
<?php endif; ?>

<a href="index.php">Quay lại trang chủ</a>

</body>
</html>

<?php
$stmt->close();
?>
