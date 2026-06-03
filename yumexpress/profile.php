<?php
session_start();
// Gọi file cấu hình kết nối database
include 'config.php';

// Kiểm tra xem người dùng đã đăng nhập chưa, nếu chưa thì chuyển hướng về trang đăng nhập
if (!isset($_SESSION['user'])) {
    header("Location: auth.php");
    exit();
}

$user_id = $_SESSION['user']['id'];
$msg = "";
$msg_type = "";

// Xử lý khi người dùng nhấn nút "Lưu thay đổi"
if (isset($_POST['update_profile'])) {
    $fullname = $conn->real_escape_string($_POST['fullname']);
    $email = $conn->real_escape_string($_POST['email']);
    $phone = $conn->real_escape_string($_POST['phone']);

    // Cập nhật thông tin vào bảng users
    $sql_update = "UPDATE users SET fullname = '$fullname', email = '$email', phone = '$phone' WHERE id = $user_id";
    
    if ($conn->query($sql_update)) {
        $msg = "Cập nhật thông tin thành công!";
        $msg_type = "success";
        // Cập nhật lại họ tên mới vào Session để hiển thị chính xác trên Header trang chủ
        $_SESSION['user']['fullname'] = $fullname;
    } else {
        $msg = "Có lỗi xảy ra trong quá trình lưu, vui lòng thử lại!";
        $msg_type = "error";
    }
}

// Lấy thông tin mới nhất từ Database để đổ vào các ô nhập liệu (Form)
$sql = "SELECT * FROM users WHERE id = $user_id";
$res = $conn->query($sql);
$u = $res->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thông tin cá nhân - YumExpress</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #fdfaf5; margin: 0; padding: 0; }
        .profile-container { max-width: 500px; margin: 60px auto; background: white; padding: 40px; border-radius: 16px; box-shadow: 0 4px 24px rgba(0,0,0,0.05); }
        .profile-title { font-size: 1.4rem; color: #2c3e2f; margin-bottom: 25px; font-weight: 600; text-align: center; }
        .form-group { margin-bottom: 18px; }
        .form-group label { display: block; margin-bottom: 6px; font-weight: 500; font-size: 0.9rem; color: #333; }
        .form-group input { width: 100%; padding: 10px 14px; border: 1px solid #ddd; border-radius: 8px; outline: none; font-size: 0.95rem; box-sizing: border-box; background-color: #fff; }
        .form-group input[readonly] { background-color: #f5f5f5; color: #888; cursor: not-allowed; }
        .btn-submit { width: 100%; padding: 12px; background: #2c3e2f; color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; margin-top: 10px; font-size: 1rem; }
        .btn-submit:hover { background: #1e2b21; }
        .msg { padding: 12px; border-radius: 8px; margin-bottom: 20px; font-size: 0.9rem; font-weight: 500; text-align: center; }
        .msg-error { background: #fde8e8; color: #e53e3e; border: 1px solid #f9b8b8; }
        .msg-success { background: #def7ec; color: #03543f; border: 1px solid #b3f5d4; }
        .back-home { display: inline-block; margin-top: 20px; color: #ff6b35; text-decoration: none; font-weight: 500; font-size: 0.95rem; }
    </style>
</head>
<body>

    <div class="profile-container">
        <h2 class="profile-title">👤 Thông Tin Cá Nhân</h2>

        <?php if(!empty($msg)): ?>
            <div class="msg msg-<?php echo $msg_type; ?>"><?php echo $msg; ?></div>
        <?php endif; ?>

        <form action="profile.php" method="POST">
            <div class="form-group">
                <label>Tài khoản (Không thể thay đổi)</label>
                <input type="text" value="<?php echo htmlspecialchars($u['username']); ?>" readonly>
            </div>
            
            <div class="form-group">
                <label>Họ và tên *</label>
                <input type="text" name="fullname" value="<?php echo htmlspecialchars($u['fullname']); ?>" required placeholder="Nhập họ tên của bạn...">
            </div>
            
            <div class="form-group">
                <label>Email *</label>
                <input type="email" name="email" value="<?php echo htmlspecialchars($u['email']); ?>" required placeholder="Nhập email...">
            </div>
            
            <div class="form-group">
                <label>Số điện thoại *</label>
                <input type="tel" name="phone" value="<?php echo htmlspecialchars($u['phone']); ?>" required placeholder="Nhập số điện thoại...">
            </div>

            <button type="submit" name="update_profile" class="btn-submit">Lưu thay đổi</button>
        </form>

        <div style="text-align: center;">
            <a href="index.php" class="back-home"><i class="fas fa-arrow-left"></i> Quay lại trang chủ</a>
        </div>
    </div>

</body>
</html>