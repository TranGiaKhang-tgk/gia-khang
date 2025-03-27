<?php
session_start();
require 'db_connect.php';

// Kiểm tra quyền admin
if (!isset($_SESSION['admin_id']) || $_SESSION['admin_level'] !== 'admin') {
    header("Location: login.php");
    exit();}
// Kiểm tra ID sản phẩm hợp lệ
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("ID sản phẩm không hợp lệ!");
}

$id = intval($_GET['id']);
$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();

if (!$product) {
    die("Sản phẩm không tồn tại!");
}

// Xử lý cập nhật sản phẩm
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $price = floatval($_POST['price']);
    $description = trim($_POST['description']);

    if (!empty($name) && $price > 0) {
        $stmt = $conn->prepare("UPDATE products SET name = ?, price = ?, description = ? WHERE id = ?");
        $stmt->bind_param("sdsi", $name, $price, $description, $id);
        if ($stmt->execute()) {
            $_SESSION['success'] = "Cập nhật thành công!";
            header("Location: manage_products.php");
            exit();
        } else {
            $_SESSION['error'] = "Lỗi khi cập nhật!";
        }
        $stmt->close();
    } else {
        $_SESSION['error'] = "Vui lòng nhập đầy đủ thông tin!";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chỉnh sửa sản phẩm</title>
</head>
<body>
    <h2>Chỉnh sửa sản phẩm</h2>

    <form method="POST">
        <label>Tên sản phẩm:</label><br>
        <input type="text" name="name" value="<?= htmlspecialchars($product['name']) ?>" required><br>

        <label>Giá:</label><br>
        <input type="number" name="price" value="<?= $product['price'] ?>" step="0.01" required><br>

        <label>Mô tả:</label><br>
        <textarea name="description"><?= htmlspecialchars($product['description']) ?></textarea><br>

        <button type="submit">Lưu thay đổi</button>
    </form>

    <p><a href="manage_products.php">Quay lại</a></p>
</body>
</html>

<?php $conn->close(); ?>
