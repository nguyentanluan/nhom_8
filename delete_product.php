<?php 
session_start(); 
include 'config.php'; 

$contact_msg = "";
// Xử lý khi khách bấm nút gửi góp ý (giả lập lưu hoặc thông báo thành công)
if (isset($_POST['btn_contact'])) {
    $contact_msg = "<div style='background:#d4edda; color:#155724; padding:12px; border-radius:6px; margin-bottom:20px; font-weight:600;'>🎉 Cảm ơn bạn! Ý kiến đóng góp đã được gửi tới YumExpress thành công!</div>";
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liên Hệ Với YumExpress</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .contact-layout { display: grid; grid-template-columns: 1fr 1.2fr; gap: 40px; margin-top: 30px; margin-bottom: 5px; }
        .contact-info-box { background: #34495e; color: white; padding: 40px; border-radius: 12px; }
        .contact-info-box h3 { color: #feb47b; margin-bottom: 25px; font-size: 1.6rem; }
        .info-item { display: flex; align-items: flex-start; gap: 15px; margin-bottom: 20px; }
        .info-item i { font-size: 1.3rem; color: #feb47b; margin-top: 3px; }
        .info-item p { line-height: 1.5; font-size: 0.95rem; }
        .contact-form-box { background: white; padding: 40px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .contact-form-box h3 { color: #2c3e50; margin-bottom: 20px; }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px; }
        .form-group-contact { margin-bottom: 15px; }
        .form-group-contact label { display: block; margin-bottom: 5px; font-weight: 600; font-size: 0.9rem; color: #555; }
        .form-group-contact input, .form-group-contact textarea { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; box-sizing: border-box; font-family: inherit; }
        .btn-send { background: #e74c3c; color: white; border: none; padding: 12px 30px; border-radius: 6px; font-weight: 600; cursor: pointer; transition: 0.2s; }
        .btn-send:hover { background: #c0392b; }
        .map-section { margin-top: 40px; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
    </style>
</head>
<body>

    <?php include 'header.php'; ?>

    <div class="container" style="margin-top: 20px;">
        <?php echo $contact_msg; ?>
        
        <div class="contact-layout">
            <div class="contact-info-box">
                <h3>Thông Tin Liên Hệ</h3>
                <p style="margin-bottom: 30px; color: #bbb; line-height: 1.6;">Nếu bạn có bất kỳ câu hỏi, khiếu nại về đơn hàng hoặc muốn hợp tác kinh doanh mở gian hàng cùng YumExpress, hãy liên hệ với chúng tôi qua các kênh sau:</p>
                
                <div class="info-item">
                    <i class="fas fa-map-marker-alt"></i>
                    <div>
                        <strong>Địa chỉ văn phòng:</strong>
                        <p>Khu Công nghệ cao, Long Thạnh Mỹ, Quận 9, Thành phố Hồ Chí Minh, Việt Nam.</p>
                    </div>
                </div>

                <div class="info-item">
                    <i class="fas fa-phone-alt"></i>
                    <div>
                        <strong>Tổng đài hỗ trợ (24/7):</strong>
                        <p>1900 8888 (Cước gọi 1.000đ/phút)</p>
                    </div>
                </div>

                <div class="info-item">
                    <i class="fas fa-envelope"></i>
                    <div>
                        <strong>Email tiếp nhận:</strong>
                        <p>support@yumexpress.vn</p>
                    </div>
                </div>
            </div>

            <div class="contact-form-box">
                <h3>Gửi Ý Kiến Phản Hồi</h3>
                <form action="" method="POST">
                    <div class="form-row">
                        <div class="form-group-contact">
                            <label>Họ và tên của bạn:</label>
                            <input type="text" name="txt_name" required placeholder="Nguyễn Văn A">
                        </div>
                        <div class="form-group-contact">
                            <label>Số điện thoại:</label>
                            <input type="text" name="txt_phone" required placeholder="0901234567">
                        </div>
                    </div>
                    <div class="form-group-contact">
                        <label>Địa chỉ Email:</label>
                        <input type="email" name="txt_email" placeholder="name@example.com">
                    </div>
                    <div class="form-group-contact">
                        <label>Lời nhắn / Đóng góp ý kiến:</label>
                        <textarea name="txt_content" rows="5" required placeholder="Nhập nội dung bạn muốn gửi tới YumExpress..."></textarea>
                    </div>
                    <button type="submit" name="btn_contact" class="btn-send"><i class="far fa-paper-plane"></i> Gửi Tin Nhắn</button>
                </form>
            </div>
        </div>

        <div class="map-section">
            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3918.485466723223!2d106.78449337583857!3d10.850632357819198!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1m3!2s1d0!2s!5e0!3m2!1svi!2s!4v1710000000000!5m2!1svi!2s" width="100%" height="350" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
        </div>
    </div>

    <div style="margin-top: 50px;">
        <?php include 'footer.php'; ?>
    </div>

</body>
</html>