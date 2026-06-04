<?php
session_start();
// Gọi file kết nối database
include 'config.php';

// Kiểm tra nếu chưa đăng nhập thì bắt quay về trang auth.php
if (!isset($_SESSION['user'])) {
    header("Location: auth.php");
    exit();
}

$user_id = $_SESSION['user']['id'];
$msg = "";
$msg_type = "";

// Xử lý khi người dùng nhấn nút "Cập nhật mật khẩu"
if (isset($_POST['change_pass'])) {
    $current_pass = $_POST['current_pass'];
    $new_pass = $_POST['new_pass'];
    $confirm_pass = $_POST['confirm_pass'];

    // 1. Kiểm tra mật khẩu hiện tại có đúng với database không
    $sql = "SELECT password FROM users WHERE id = $user_id";
    $res = $conn->query($sql);
    $u = $res->fetch_assoc();

    if ($current_pass !== $u['password']) {
        $msg = "Mật khẩu hiện tại không chính xác!";
        $msg_type = "error";
    } 
    // 2. Kiểm tra mật khẩu mới và xác nhận mật khẩu có khớp nhau không
    elseif ($new_pass !== $confirm_pass) {
        $msg = "Mật khẩu mới nhập lại không trùng khớp!";
        $msg_type = "error";
    } 
    // 3. Tiến hành cập nhật nếu mọi thứ hợp lệ
    else {
        $sql_update = "UPDATE users SET password = '$new_pass' WHERE id = $user_id";
        if ($conn->query($sql_update)) {
            $msg = "Đổi mật khẩu thành công!";
            $msg_type = "success";
        } else {
            $msg = "Lỗi hệ thống, không thể đổi mật khẩu: " . $conn->error;
            $msg_type = "error";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đổi mật khẩu - YumExpress</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #fdfaf5; margin: 0; padding: 0; }
        .pass-container { max-width: 450px; margin: 80px auto; background: white; padding: 40px; border-radius: 16px; box-shadow: 0 4px 24px rgba(0,0,0,0.05); }
        .pass-title { font-size: 1.4rem; color: #2c3e2f; margin-bottom: 25px; font-weight: 600; text-align: center; }
        .form-group { margin-bottom: 18px; }
        .form-group label { display: block; margin-bottom: 6px; font-weight: 500; font-size: 0.9rem; color: #333; }
        
        /* CSS bọc ô mật khẩu để định vị con mắt */
        .password-wrapper { position: relative; width: 100%; }
        .password-wrapper input { width: 100%; padding: 10px 40px 10px 14px; border: 1px solid #ddd; border-radius: 8px; outline: none; font-size: 0.95rem; box-sizing: border-box; }
        .password-wrapper i { position: absolute; right: 15px; top: 50%; transform: translateY(-50%); cursor: pointer; color: #666; }
        
        .btn-submit { width: 100%; padding: 12px; background: #2c3e2f; color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; margin-top: 10px; font-size: 1rem; }
        .btn-submit:hover { background: #1e2b21; }
        .msg { padding: 12px; border-radius: 8px; margin-bottom: 20px; font-size: 0.9rem; font-weight: 500; text-align: center; }
        .msg-error { background: #fde8e8; color: #e53e3e; border: 1px solid #f9b8b8; }
        .msg-success { background: #def7ec; color: #03543f; border: 1px solid #b3f5d4; }
        .back-home { display: inline-block; margin-top: 20px; color: #ff6b35; text-decoration: none; font-weight: 500; font-size: 0.95rem; }
    </style>
</head>
<body>

    <div class="pass-container">
        <h2 class="pass-title">🔒 Đổi Mật Khẩu</h2>

        <?php if(!empty($msg)): ?>
            <div class="msg msg-<?php echo $msg_type; ?>"><?php echo $msg; ?></div>
        <?php endif; ?>

        <form action="change_password.php" method="POST">
            <div class="form-group">
                <label>Mật khẩu hiện tại *</label>
                <div class="password-wrapper">
                    <input type="password" id="currentPass" name="current_pass" required placeholder="Nhập mật khẩu hiện tại...">
                    <i class="fas fa-eye toggle-password" data-target="currentPass"></i>
                </div>
            </div>
            
            <div class="form-group">
                <label>Mật khẩu mới *</label>
                <div class="password-wrapper">
                    <input type="password" id="newPass" name="new_pass" required placeholder="Ít nhất 6 ký tự...">
                    <i class="fas fa-eye toggle-password" data-target="newPass"></i>
                </div>
            </div>
            
            <div class="form-group">
                <label>Xác nhận mật khẩu mới *</label>
                <div class="password-wrapper">
                    <input type="password" id="confirmPass" name="confirm_pass" required placeholder="Nhập lại mật khẩu mới...">
                    <i class="fas fa-eye toggle-password" data-target="confirmPass"></i>
                </div>
            </div>

            <button type="submit" name="change_pass" class="btn-submit">Cập nhật mật khẩu</button>
        </form>

        <div style="text-align: center;">
            <a href="index.php" class="back-home"><i class="fas fa-arrow-left"></i> Quay lại trang chủ</a>
        </div>
    </div>

    <script>
        const eyes = document.querySelectorAll('.toggle-password');
        eyes.forEach(eye => {
            eye.addEventListener('click', function() {
                const targetId = this.getAttribute('data-target');
                const input = document.getElementById(targetId);
                
                if (input) {
                    const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
                    input.setAttribute('type', type);
                    
                    this.classList.toggle('fa-eye');
                    this.classList.toggle('fa-eye-slash');
                }
            });
        });
    </script>

</body>
</html>