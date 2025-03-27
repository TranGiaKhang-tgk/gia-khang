<?php
session_start();
require 'db_connect.php';

// Kiểm tra quyền admin (Nếu cần)
if (!isset($_SESSION['admin_id']) || $_SESSION['admin_level'] !== 'admin') {
    header("Location: login.php");
    exit();
}
// Xử lý thêm sản phẩm
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_product'])) {
    $name = trim($_POST['name']);
    $price = floatval($_POST['price']);
    $description = trim($_POST['description']);

    if (!empty($name) && $price > 0) {
        $stmt = $conn->prepare("INSERT INTO products (name, price, description) VALUES (?, ?, ?)");
        $stmt->bind_param("sds", $name, $price, $description);
        if ($stmt->execute()) {
            $_SESSION['success'] = "Thêm sản phẩm thành công!";
        } else {
            $_SESSION['error'] = "Lỗi khi thêm sản phẩm!";
        }
        $stmt->close();
    } else {
        $_SESSION['error'] = "Vui lòng nhập đầy đủ thông tin!";
    }
}

// Xử lý xóa sản phẩm
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    $stmt->bind_param("i", $delete_id);
    if ($stmt->execute()) {
        $_SESSION['success'] = "Xóa sản phẩm thành công!";
    } else {
        $_SESSION['error'] = "Lỗi khi xóa sản phẩm!";
    }
    $stmt->close();
    header("Location: manage_products.php");
    exit();
}

// Lấy danh sách sản phẩm
$result = $conn->query("SELECT * FROM products");
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý sản phẩm</title>
</head>
<body>
    <h2>Quản lý sản phẩm</h2>

    <?php
    if (isset($_SESSION['success'])) {
        echo "<p style='color:green'>" . $_SESSION['success'] . "</p>";
        unset($_SESSION['success']);
    }
    if (isset($_SESSION['error'])) {
        echo "<p style='color:red'>" . $_SESSION['error'] . "</p>";
        unset($_SESSION['error']);
    }
    ?>

    <h3>Thêm sản phẩm</h3>
    <form method="POST">
        <label>Tên sản phẩm:</label><br>
        <input type="text" name="name" required><br>

        <label>Giá:</label><br>
        <input type="number" name="price" step="0.01" required><br>

        <label>Mô tả:</label><br>
        <textarea name="description"></textarea><br>

        <button type="submit" name="add_product">Thêm</button>
    </form>

    <h3>Danh sách sản phẩm</h3>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Tên</th>
            <th>Giá</th>
            <th>Mô tả</th>
            <th>Hành động</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= htmlspecialchars($row['name']) ?></td>
                <td><?= number_format($row['price'], 2) ?> VNĐ</td>
                <td><?= htmlspecialchars($row['description']) ?></td>
                <td>
                    <a href="edit_product.php?id=<?= $row['id'] ?>">Sửa</a> |
                    <a href="manage_products.php?delete_id=<?= $row['id'] ?>" onclick="return confirm('Bạn có chắc chắn muốn xóa?');">Xóa</a>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>

    <p><a href="quantri.php">Quay lại trang quản trị</a></p>
</body>
</html>

<?php $conn->close(); ?>
