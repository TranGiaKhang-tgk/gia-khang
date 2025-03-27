<?php
session_start();
require 'db_connect.php'; // Kết nối database

// Kiểm tra nếu user không phải admin thì chặn truy cập
if (!isset($_SESSION['admin_id']) || $_SESSION['admin_level'] !== 'admin') {
    header("Location: login.php");
    exit();
}
// Xử lý xóa user nếu có yêu cầu
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    if ($delete_id > 0) {
        $stmt = $conn->prepare("DELETE FROM user WHERE id = ?");
        $stmt->bind_param("i", $delete_id);
        if ($stmt->execute()) {
            $_SESSION['success'] = "Xóa người dùng thành công!";
        } else {
            $_SESSION['error'] = "Lỗi khi xóa!";
        }
        $stmt->close();
        header("Location: manage_users.php");
        exit();
    }
}

// Lấy danh sách người dùng từ database
$result = $conn->query("SELECT id, username, level FROM user");

?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý người dùng</title>
</head>
<body>
    <h2>Danh sách người dùng</h2>

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

    <table border="1">
        <tr>
            <th>ID</th>
            <th>Tên đăng nhập</th>
            <th>Vai trò</th>
            <th>Hành động</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= htmlspecialchars($row['username']) ?></td>
                <td><?= $row['level'] ?></td>
                <td>
                    <?php if ($row['level'] !== 'admin'): // Không cho xóa admin ?>
                        <a href="manage_users.php?delete_id=<?= $row['id'] ?>" onclick="return confirm('Bạn có chắc chắn muốn xóa?');">
                            Xóa
                        </a>
                    <?php else: ?>
                        (Admin)
                    <?php endif; ?>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>

    <p><a href="quantri.php">Quay lại trang quản trị</a></p>
</body>
</html>

<?php $conn->close(); ?>
