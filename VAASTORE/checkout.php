<?php
session_start();
require 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$total_price = $_SESSION['cart_total'];
$payment_method = "COD"; // Mặc định là thanh toán khi nhận hàng

// Lưu đơn hàng vào database
$stmt = $conn->prepare("INSERT INTO orders (user_id, total_price, payment_method, status) VALUES (?, ?, ?, 'pending')");
$stmt->bind_param("ids", $user_id, $total_price, $payment_method);
$stmt->execute();
$order_id = $stmt->insert_id;
$stmt->close();

// Lưu sản phẩm vào order_items
foreach ($_SESSION['cart'] as $product_id => $item) {
    $stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiid", $order_id, $product_id, $item['quantity'], $item['price']);
    $stmt->execute();
    $stmt->close();
}

// Xóa giỏ hàng sau khi đặt hàng thành công
unset($_SESSION['cart']);
$_SESSION['success'] = "Đơn hàng của bạn đã được đặt thành công!";
header("Location: order_success.php");
exit();
?>
