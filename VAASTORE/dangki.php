<?php
session_start();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký tài khoản</title>
</head>
<body>
    <h2>Đăng ký tài khoản</h2>
    
    <?php
    if (isset($_SESSION['error'])) {
        echo "<p style='color:red'>" . $_SESSION['error'] . "</p>";
        unset($_SESSION['error']);
    }
    if (isset($_SESSION['success'])) {
        echo "<p style='color:green'>" . $_SESSION['success'] . "</p>";
        unset($_SESSION['success']);
    }
    
    // Lấy lại dữ liệu nhập trước đó
    $username = $_SESSION['input_username'] ?? '';
    ?>

    <form action="dangki_submit.php" method="POST">
        <label>Tên đăng nhập:</label><br>
        <input type="text" name="username" value="<?php echo htmlspecialchars($username); ?>" required><br>

        <label>Mật khẩu:</label><br>
        <input type="password" name="password" required><br>

        <label>Nhập lại mật khẩu:</label><br>
        <input type="password" name="confirm_password" required><br>

        <button type="submit">Đăng ký</button>
    </form>

    <p>Đã có tài khoản? <a href="login.php">Đăng nhập</a></p>
</body>
</html>
