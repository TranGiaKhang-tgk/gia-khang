<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'db_connect.php'; // Kết nối database

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    // Lưu lại username để hiển thị lại nếu có lỗi
    $_SESSION['input_username'] = $username;

    if (empty($username) || empty($password) || empty($confirm_password)) {
        $_SESSION['error'] = "Vui lòng nhập đầy đủ thông tin!";
        header("Location: dangki.php");
        exit();
    }

    if ($password !== $confirm_password) {
        $_SESSION['error'] = "Mật khẩu nhập lại không khớp!";
        header("Location: dangki.php");
        exit();
    }

    // Kiểm tra username đã tồn tại chưa
    $stmt = $conn->prepare("SELECT id FROM user WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $_SESSION['error'] = "Tên đăng nhập đã tồn tại!";
        header("Location: dangki.php");
        exit();
    }

    $stmt->close();

    // Thêm user mới vào database
    $stmt = $conn->prepare("INSERT INTO user (username, pass, level) VALUES (?, ?, 'user')");
    $stmt->bind_param("ss", $username, $password);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Đăng ký thành công! Vui lòng đăng nhập.";
        unset($_SESSION['input_username']); // Xóa dữ liệu nhập trước đó sau khi thành công
    
        echo "<p style='color: green; text-align: center;'>Đăng ký thành công! Vui lòng đợi...</p>";
        echo '<meta http-equiv="refresh" content="2;url=login.php">';
        exit();
    } else {
        $_SESSION['error'] = "Lỗi khi đăng ký!";
        header("Location: dangki.php");
        exit();
    }

    $stmt->close();
    $conn->close();
}
?>
