<?php
// Hàm giả lập gửi mail để không bị lỗi Class PHPMailer
function sendYumEmail($toEmail, $subject, $body) {
    // Trả về true để mạch logic ở file forgot_password.php tiếp tục chạy
    return true; 
}
?>