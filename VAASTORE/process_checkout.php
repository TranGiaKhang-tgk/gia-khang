<?php

session_start();
require 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    $_SESSION['error'] = "Bạn cần đăng nhập để thanh toán!";
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$payment_method = $_POST['payment_method'] ?? 'COD'; // Mặc định thanh toán khi nhận hàng

// Lấy thông tin giỏ hàng của user
$query = "SELECT cart.product_id, products.price, cart.quantity 
          FROM cart 
          JOIN products ON cart.product_id = products.id 
          WHERE cart.user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $_SESSION['error'] = "Giỏ hàng của bạn đang trống!";
    header("Location: cart.php");
    exit();
}

// Tính tổng tiền
$total_price = 0;
$cart_items = [];

while ($row = $result->fetch_assoc()) {
    $total_price += $row['price'] * $row['quantity'];
    $cart_items[] = $row;
}

$stmt->close();

// Lưu đơn hàng vào bảng `orders`
$order_query = "INSERT INTO orders (user_id, total_price, payment_method, status) VALUES (?, ?, ?, 'pending')";
$stmt = $conn->prepare($order_query);
$stmt->bind_param("ids", $user_id, $total_price, $payment_method);
$stmt->execute();
$order_id = $stmt->insert_id; // Lấy ID của đơn hàng vừa tạo
$stmt->close();

// Lưu chi tiết đơn hàng vào `order_items`
$order_item_query = "INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($order_item_query);

foreach ($cart_items as $item) {
    $stmt->bind_param("iiid", $order_id, $item['product_id'], $item['quantity'], $item['price']);
    $stmt->execute();
}

$stmt->close();

// Xóa giỏ hàng sau khi đặt hàng thành công
$delete_cart_query = "DELETE FROM cart WHERE user_id = ?";
$stmt = $conn->prepare($delete_cart_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->close();

// Chuyển hướng đến trang xác nhận đặt hàng thành công
$_SESSION['success'] = "Đơn hàng của bạn đã được đặt thành công!";
header("Location: order_success.php");
exit();
?>
