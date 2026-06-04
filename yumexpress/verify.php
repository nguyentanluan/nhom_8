<?php
include 'config.php';

if (isset($_GET['vkey'])) {
    $vkey = $conn->real_escape_string($_GET['vkey']);
    
    $result = $conn->query("SELECT id FROM users WHERE email_verified = 0 AND vkey = '$vkey' LIMIT 1");
    
    if ($result->num_rows > 0) {
        // Cập nhật trạng thái đã xác thực
        $update = $conn->query("UPDATE users SET email_verified = 1 WHERE vkey = '$vkey'");
        if ($update) {
            echo "<div style='text-align:center; margin-top:50px; font-family:Inter, sans-serif;'>
                    <h2 style='color:green;'>🎉 Kích hoạt tài khoản thành công!</h2>
                    <p>Giờ bạn đã có thể đặt những món ăn siêu ngon tại YumExpress.</p>
                    <a href='login.php'>Đăng nhập ngay</a>
                  </div>";
        }
    } else {
        echo "<div style='text-align:center; margin-top:50px;'><h2>Liên kết không hợp lệ hoặc tài khoản đã được kích hoạt từ trước!</h2></div>";
    }
} else {
    die("Đã có lỗi xảy ra!");
}
?>