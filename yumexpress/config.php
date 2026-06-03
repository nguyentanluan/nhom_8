<?php
$host = "localhost";
$user = "root";          
$pass = "";              
$dbname = "yum_express"; 
$port = 33066; // Thêm dòng này để chỉ định đúng cổng MySQL của bạn

// Truyền thêm biến $port vào cuối câu lệnh kết nối
$conn = new mysqli($host, $user, $pass, $dbname, $port);

// Kiểm tra nếu kết nối bị lỗi thì thông báo ra màn hình
if ($conn->connect_error) {
    die("Kết nối database thất bại: " . $conn->connect_error);
}

// Cấu hình để hiển thị tiếng Việt chuẩn
$conn->set_charset("utf8mb4");
?>