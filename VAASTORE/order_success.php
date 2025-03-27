<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();}
if (!isset($_SESSION['success'])) {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đặt hàng thành công</title>
</head>
<body>
    <h2><?php echo $_SESSION['success']; ?></h2>
    <p>Cảm ơn bạn đã mua hàng! Chúng tôi sẽ xử lý đơn hàng của bạn sớm nhất.</p>
    <a href="my_orders.php">Xem đơn hàng của tôi</a> | 
    <a href="index.php">Tiếp tục mua sắm</a>
</body>
</html>

<?php unset($_SESSION['success']); ?>
