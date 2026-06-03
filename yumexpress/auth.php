<?php
// 1. Khởi động Session để lưu trạng thái đăng nhập
session_start();

// 2. Gọi file kết nối database của bạn vào
include 'config.php';

$error = "";
$success = "";

// 3. XỬ LÝ ĐĂNG KÝ (Khi người dùng nhấn nút Đăng ký)
if (isset($_POST['register'])) {
    $username = $_POST['reg_user'];
    $password = $_POST['reg_pass'];
    $confirm_pass = $_POST['reg_confirm_pass'];
    $fullname = $_POST['reg_name'];
    $email = $_POST['reg_email'];
    $phone = $_POST['reg_phone'];

    // Kiểm tra mật khẩu nhập lại có khớp không
    if ($password !== $confirm_pass) {
        $error = "Mật khẩu xác nhận không trùng khớp!";
    } else {
        // Kiểm tra xem tài khoản hoặc email đã tồn tại chưa
        $check_sql = "SELECT id FROM users WHERE username = '$username' OR email = '$email'";
        $check_result = $conn->query($check_sql);

        if ($check_result && $check_result->num_rows > 0) {
            $error = "Tài khoản hoặc Email này đã được sử dụng!";
        } else {
            // Chèn tài khoản mới vào database (Mật khẩu để thuần theo data cũ, sau này nâng cấp mã hóa sau)
            $ins_sql = "INSERT INTO users (username, password, fullname, email, phone, role) 
                        VALUES ('$username', '$password', '$fullname', '$email', '$phone', 'customer')";
            
            if ($conn->query($ins_sql) === TRUE) {
                $success = "Đăng ký thành công! Vui lòng chuyển sang Đăng nhập.";
            } else {
                $error = "Lỗi hệ thống, không thể đăng ký: " . $conn->error;
            }
        }
    }
}

// 4. XỬ LÝ ĐĂNG NHẬP (Khi người dùng nhấn nút Đăng nhập)
if (isset($_POST['login'])) {
    $username = $_POST['login_user'];
    $password = $_POST['login_pass'];

    $login_sql = "SELECT * FROM users WHERE username = '$username' AND password = '$password' AND status = 1";
    $login_result = $conn->query($login_sql);

    if ($login_result && $login_result->num_rows > 0) {
        $user_data = $login_result->fetch_assoc();
        
        // Lưu thông tin vào Session để toàn bộ website biết bạn đã đăng nhập
        $_SESSION['user'] = [
            'id' => $user_data['id'],
            'username' => $user_data['username'],
            'fullname' => $user_data['fullname'],
            'role' => $user_data['role']
        ];

        // Đăng nhập xong thì đá bay người dùng về lại trang chủ index.php
        header("Location: index.php");
        exit();
    } else {
        $error = "Sai tài khoản, mật khẩu hoặc tài khoản của bạn đang bị khóa!";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>YumExpress - Đăng Nhập / Đăng Ký</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <style>
        /* CSS riêng cho trang Auth bám sát style gọn gàng của Khang */
        .auth-container { max-width: 900px; margin: 60px auto; display: flex; background: white; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 24px rgba(0,0,0,0.05); }
        .auth-box-side { flex: 1; padding: 40px; }
        .auth-box-side:first-child { border-right: 1px solid #f0f0f0; }
        .auth-title { font-size: 1.4rem; color: #2c3e2f; margin-bottom: 20px; font-weight: 600; text-align: center;}
        .form-group { margin-bottom: 16px; }
        .form-group label { display: block; margin-bottom: 6px; font-weight: 500; font-size: 0.9rem; }
        .form-group input { width: 100%; padding: 10px 14px; border: 1px solid #ddd; border-radius: 8px; outline: none; font-size: 0.95rem; }
        .btn-submit { width: 100%; padding: 12px; background: #ff6b35; color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; margin-top: 10px; }
        .btn-submit.btn-secondary { background: #2c3e2f; }
        .msg { padding: 10px; border-radius: 6px; margin-bottom: 15px; font-size: 0.9rem; font-weight: 500; text-align: center; }
        .msg-error { background: #fde8e8; color: #e53e3e; }
        .msg-success { background: #def7ec; color: #03543f; }
    </style>
</head>
<body>

    <div class="container">
        <p style="margin-top: 30px;"><a href="index.php" style="color: #ff6b35; font-weight: 500;"><i class="fas fa-arrow-left"></i> Quay lại trang chủ</a></p>
        
        <div class="auth-container">
            <div class="auth-box-side">
                <h2 class="auth-title">Đăng Nhập</h2>
                
                <?php if(!empty($error) && isset($_POST['login'])): ?>
                    <div class="msg msg-error"><?php echo $error; ?></div>
                <?php endif; ?>

                <form action="auth.php" method="POST">
                    <div class="form-group">
                        <label>Tài khoản</label>
                        <input type="text" name="login_user" required placeholder="Nhập tài khoản của bạn...">
                    </div>
                    <div class="form-group">
                        <label>Mật khẩu</label>
                        <input type="password" name="login_pass" required placeholder="Nhập mật khẩu...">
                    </div>
                    <button type="submit" name="login" class="btn-submit">Đăng nhập</button>
                </form>
            </div>

            <div class="auth-box-side">
                <h2 class="auth-title">Đăng Ký Tài Khoản</h2>

                <?php if(!empty($error) && isset($_POST['register'])): ?>
                    <div class="msg msg-error"><?php echo $error; ?></div>
                <?php endif; ?>
                <?php if(!empty($success)): ?>
                    <div class="msg msg-success"><?php echo $success; ?></div>
                <?php endif; ?>

                <form action="auth.php" method="POST">
                    <div class="form-group">
                        <label>Tài khoản *</label>
                        <input type="text" name="reg_user" required placeholder="Ít nhất 3 ký tự...">
                    </div>
                    <div class="form-group">
                        <label>Họ và tên *</label>
                        <input type="text" name="reg_name" required placeholder="Nhập họ tên...">
                    </div>
                    <div class="form-group">
                        <label>Email *</label>
                        <input type="email" name="reg_email" required placeholder="Ví dụ: name@gmail.com...">
                    </div>
                    <div class="form-group">
                        <label>Số điện thoại *</label>
                        <input type="tel" name="reg_phone" required placeholder="Nhập số điện thoại...">
                    </div>
                    <div class="form-group">
                        <label>Mật khẩu *</label>
                        <input type="password" name="reg_pass" required placeholder="Ít nhất 6 ký tự...">
                    </div>
                    <div class="form-group">
                        <label>Xác nhận mật khẩu *</label>
                        <input type="password" name="reg_confirm_pass" required placeholder="Nhập lại mật khẩu...">
                    </div>
                    <button type="submit" name="register" class="btn-submit btn-secondary">Đăng ký ngay</button>
                </form>
            </div>
        </div>
    </div>

</body>
</html>