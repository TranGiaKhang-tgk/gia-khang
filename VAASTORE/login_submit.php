<?php
session_start();
require 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $pass = trim($_POST['password']);

    $stmt = $conn->prepare("SELECT id, username, pass, level FROM user WHERE username = ?");
    if (!$stmt) {
        die("Lỗi truy vấn SQL: " . $conn->error);
    }

    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        if ($pass === $row['pass']) {  // Nếu mật khẩu không mã hóa
            if ($row['level'] === 'admin') {
                // Gán session cho admin
                $_SESSION['admin_id'] = $row['id'];
                $_SESSION['admin_username'] = $row['username'];
                $_SESSION['admin_level'] = $row['level'];

                header("Location: quantri.php");
                exit();
            } else {
                // Gán session cho user
                
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['user_username'] = $row['username'];
                $_SESSION['user_level'] = $row['level'];
                $_SESSION['cart'] = [];
                 // Lưu ID của user vào session
            $_SESSION['current_user_id'] = $row['id'];
                header("Location: index.php");
                exit();
            }
        } else {
            $_SESSION['error'] = "Mật khẩu không chính xác!";
        }
    } else {
        $_SESSION['error'] = "Tên đăng nhập không tồn tại!";
    }

    header("Location: login.php");
    exit();
}
?>