<?php
include 'config.php';
include 'sendmail.php';

$msg = "";

if (isset($_POST['btn_register'])) {
    $username = $conn->real_escape_string($_POST['username']);
    $email = $conn->real_escape_string($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Mã hóa pass cho an toàn
    $vkey = md5(time() . $username); // Tạo token ngẫu nhiên

    // Kiểm tra xem email đã tồn tại chưa
    $check = $conn->query("SELECT id FROM users WHERE email = '$email'");
    if ($check->num_rows > 0) {
        $msg = "<div style='color:red;'>Email này đã được sử dụng!</div>";
    } else {
        // Thêm user mới với trạng thái email_verified = 0
        $sql = "INSERT INTO users (username, email, password, vkey, email_verified, role) 
                VALUES ('$username', '$email', '$password', '$vkey', 0, 'user')";
        
        if ($conn->query($sql)) {
            // Gửi email kích hoạt
            $subject = "Xác thực tài khoản YumExpress của bạn";
            $verification_link = "http://localhost/YumExpress/verify.php?vkey=" . $vkey;
            $body = "<h3>Chào mừng bạn đến với YumExpress!</h3>
                     <p>Vui lòng click vào đường link bên dưới để kích hoạt tài khoản của bạn:</p>
                     <a href='$verification_link' style='background:#e74c3c; color:white; padding:10px 20px; text-decoration:none; border-radius:5px;'>Kích Hoạt Tài Khoản</a>";
            
            if (sendYumEmail($email, $subject, $body)) {
                $msg = "<div style='color:green;'>Đăng ký thành công! Vui lòng kiểm tra Email để kích hoạt tài khoản trước khi đăng nhập.</div>";
            } else {
                $msg = "<div style='color:orange;'>Đăng ký thành công nhưng lỗi hệ thống không thể gửi Email kích hoạt!</div>";
            }
        }
    }
}
?>