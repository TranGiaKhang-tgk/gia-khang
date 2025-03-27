<?php
$servername = "localhost";
$username = "root"; // Thay bằng user MySQL của bạn
$password = ""; // Thêm mật khẩu nếu có
$database = "user_db";

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}
?>
