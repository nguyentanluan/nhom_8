<?php
session_start();
include 'config.php';
include 'sendmail.php';

$msg = "";
$debug_otp = ""; // Biến bổ sung để in mã OTP lên màn hình cho bạn dễ lấy

// Giai đoạn 1: Khách nhập Email để nhận OTP
if (isset($_POST['btn_send_otp'])) {
    $email = $conn->real_escape_string($_POST['email']);
    $res = $conn->query("SELECT * FROM users WHERE email = '$email'");

    if ($res->num_rows > 0) {
        $otp = rand(100000, 999999); // Tạo mã OTP 6 số ngẫu nhiên
        $conn->query("UPDATE users SET otp_code = '$otp' WHERE email = '$email'");

        $subject = "Mã OTP khôi phục mật khẩu YumExpress";
        $body = "<h3>Yêu cầu cấp lại mật khẩu</h3>
                 <p>Mã OTP của bạn là: <strong style='font-size:1.5rem; color:#e74c3c;'>$otp</strong></p>
                 <p>Mã này có hiệu lực trong vòng 10 phút. Tuyệt đối không chia sẻ mã này cho bất kỳ ai.</p>";

        if (sendYumEmail($email, $subject, $body)) {
            $_SESSION['reset_email'] = $email; // Lưu email vào session để dùng ở bước sau
            $_SESSION['otp_sent'] = true;
            
            // 🔥 ĐÃ THÊM: Hiển thị mã trực tiếp lên màn hình để Khang lấy test luôn
            $msg = "<div style='color:green; background: #def7ec; padding: 10px; border-radius: 5px; margin-bottom: 15px; font-weight: 500;'>
                        🎉 Mã OTP đã được tạo thành công!<br>
                        👉 <strong>Mã OTP test của bạn là: <span style='color: #e74c3c; font-size: 1.2rem;'>$otp</span></strong> (In ra để test trên Localhost)
                    </div>";
        } else {
            $msg = "<div style='color:red;'>Có lỗi xảy ra trong quá trình gửi mã OTP!</div>";
        }
    } else {
        $msg = "<div style='color:red;'>Email này không tồn tại trên hệ thống YumExpress!</div>";
    }
}

// Giai đoạn 2: Khách nhập mã OTP và mật khẩu mới để xác nhận
if (isset($_POST['btn_verify_otp'])) {
    $email = $_SESSION['reset_email'];
    $otp_input = $conn->real_escape_string($_POST['otp_code']);
    $new_pass = $_POST['new_password']; // Mật khẩu mới dạng thường

    // Kiểm tra mã OTP có trùng khớp với mã trong database không
    $res = $conn->query("SELECT * FROM users WHERE email = '$email' AND otp_code = '$otp_input'");

    if ($res->num_rows > 0) {
        // Cập nhật mật khẩu mới (Lưu dạng thường giống file change_password.php của bạn)
        $conn->query("UPDATE users SET password = '$new_pass', otp_code = NULL WHERE email = '$email'");
        
        // Xóa các session bổ trợ khôi phục mật khẩu
        unset($_SESSION['reset_email']);
        unset($_SESSION['otp_sent']);

        $msg = "<div style='color:green; background: #def7ec; padding: 10px; border-radius: 5px; margin-bottom: 15px;'>🔒 Đổi mật khẩu thành công! Bạn có thể quay lại trang đăng nhập.</div>";
    } else {
        $msg = "<div style='color:red; background: #fde8e8; padding: 10px; border-radius: 5px; margin-bottom: 15px;'>❌ Mã OTP nhập vào không chính xác hoặc đã hết hạn!</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Khôi Phục Mật Khẩu - YumExpress</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #fdfaf5; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .box-forgot { background: white; padding: 40px; border-radius: 16px; box-shadow: 0 4px 24px rgba(0,0,0,0.05); width: 100%; max-width: 400px; }
        .box-forgot h2 { font-size: 1.4rem; color: #2c3e2f; margin-bottom: 20px; font-weight: 600; text-align: center; margin-top: 0; }
        label { display: block; margin-bottom: 6px; font-weight: 500; font-size: 0.9rem; color: #333; }
        input { width: 100%; padding: 11px 14px; margin-bottom: 18px; border: 1px solid #ddd; border-radius: 8px; box-sizing: border-box; outline: none; font-size: 0.95rem; }
        input:focus { border-color: #2c3e2f; }
        button { width: 100%; background: #2c3e2f; color: white; border: none; padding: 12px; border-radius: 8px; font-weight: 600; cursor: pointer; font-size: 1rem; margin-top: 5px; }
        button:hover { background: #1e2b21; }
        .back-login { display: inline-block; margin-top: 20px; color: #ff6b35; text-decoration: none; font-weight: 500; font-size: 0.95rem; }
        .back-login:hover { text-decoration: underline; }
    </style>
</head>
<body>

<div class="box-forgot">
    <h2>🔒 Khôi Phục Mật Khẩu</h2>
    
    <?php echo $msg; ?>

    <?php if (!isset($_SESSION['otp_sent'])): ?>
        <form action="" method="POST">
            <p style="color:#666; font-size:0.9rem; line-height: 1.5; margin-bottom: 20px;">Nhập email tài khoản YumExpress của bạn để hệ thống cấp mã xác minh OTP.</p>
            <div class="form-group">
                <label>Email đăng ký *</label>
                <input type="email" name="email" required placeholder="Nhập email của bạn...">
            </div>
            <button type="submit" name="btn_send_otp">Gửi mã OTP</button>
        </form>
    <?php else: ?>
        <form action="" method="POST">
            <p style="color:#666; font-size:0.9rem; line-height: 1.5; margin-bottom: 20px;">Vui lòng kiểm tra mã OTP được in phía trên và điền thông tin thay đổi mật khẩu.</p>
            
            <div class="form-group">
                <label>Nhập mã OTP 6 số *</label>
                <input type="text" name="otp_code" required placeholder="Nhập 6 số mã OTP...">
            </div>

            <div class="form-group">
                <label>Mật khẩu hoàn toàn mới *</label>
                <input type="password" name="new_password" required placeholder="Nhập mật khẩu mới...">
            </div>
            
            <button type="submit" name="btn_verify_otp">Xác nhận đổi mật khẩu</button>
        </form>
    <?php endif; ?>

    <div style="text-align: center;">
        <a href="auth.php" class="back-login"><i class="fas fa-arrow-left"></i> Quay lại trang đăng nhập</a>
    </div>
</div>

</body>
</html>