<?php
session_start();
require 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    $_SESSION['error'] = "Bạn cần đăng nhập để mua hàng!";
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$product_id = $_POST['id'];
$quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;

// Kiểm tra sản phẩm đã có trong giỏ chưa
$check = $conn->prepare("SELECT id, quantity FROM cart WHERE user_id = ? AND product_id = ?");
$check->bind_param("ii", $user_id, $product_id);
$check->execute();
$result = $check->get_result();

if ($row = $result->fetch_assoc()) {
    // Nếu đã có, cập nhật số lượng
    $new_quantity = $row['quantity'] + $quantity;
    $update = $conn->prepare("UPDATE cart SET quantity = ? WHERE id = ?");
    $update->bind_param("ii", $new_quantity, $row['id']);
    $update->execute();
} else {
    // Nếu chưa có, thêm mới vào giỏ hàng
    $insert = $conn->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)");
    $insert->bind_param("iii", $user_id, $product_id, $quantity);
    $insert->execute();
}

header("Location: cart.php");
exit();
?>
