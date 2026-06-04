<?php 
session_start(); 
include 'config.php'; 
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giới thiệu về YumExpress</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .about-hero { background: linear-gradient(135deg, #ff7e5f, #feb47b); color: white; padding: 60px 20px; text-align: center; border-radius: 12px; margin-bottom: 40px; }
        .about-section { display: grid; grid-template-columns: 1fr 1fr; gap: 40px; align-items: center; margin-bottom: 5px; padding: 20px 0; }
        .about-img img { width: 100%; border-radius: 12px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        .about-text h3 { color: #e74c3c; margin-bottom: 15px; font-size: 1.8rem; }
        .about-text p { line-height: 1.6; color: #555; margin-bottom: 15px; }
        .features-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin: 40px 0; }
        .feature-box { background: white; padding: 30px; border-radius: 10px; text-align: center; box-shadow: 0 4px 10px rgba(0,0,0,0.03); }
        .feature-box i { font-size: 2.5rem; color: #e67e22; margin-bottom: 15px; }
        .team-section { text-align: center; margin-top: 50px; }
        .team-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-top: 30px; }
        .team-card { background: white; padding: 20px; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.03); border-top: 4px solid #e74c3c; }
        .team-card h4 { margin: 10px 0 5px; color: #333; }
        .team-card p { color: #777; font-size: 0.9rem; }
    </style>
</head>
<body>

    <?php include 'header.php'; ?>

    <div class="container" style="margin-top: 30px;">
        <div class="about-hero">
            <h1>Câu Chuyện YumExpress</h1>
            <p>Mang hương vị yêu thương, giao tận tay bạn chỉ trong 15 phút!</p>
        </div>

        <div class="about-section">
            <div class="about-text">
                <h3>Chúng tôi là ai?</h3>
                <p>Khởi nguồn từ niềm đam mê mãnh liệt với ẩm thực đường phố Việt Nam và sự thấu hiểu nhịp sống bận rộn hiện đại, **YumExpress** được thành lập vào năm 2026 với sứ mệnh kết nối những tâm hồn ăn uống.</p>
                <p>Chúng tôi không chỉ đơn thuần là một website đặt đồ ăn trực tuyến, mà còn là người bạn đồng hành tin cậy trong từng bữa ăn của bạn, từ bữa sáng vội vã, bữa trưa văn phòng cho đến siêu tiệc gà rán liên hoan cuối tuần.</p>
            </div>
            <div class="about-img">
                <img src="https://images.unsplash.com/photo-1504674900247-0877df9cc836?auto=format&fit=crop&w=600&q=80" alt="Ẩm thực">
            </div>
        </div>

        <div class="features-grid">
            <div class="feature-box">
                <i class="fas fa-shipping-fast"></i>
                <h4>Giao Hàng Siêu Tốc</h4>
                <p>Đội ngũ shipper phủ rộng khắp các quận, đảm bảo món ăn đến tay bạn vẫn còn nóng hổi và thơm phức.</p>
            </div>
            <div class="feature-box">
                <i class="fas fa-heart"></i>
                <h4>An Toàn Thực Phẩm</h4>
                <p>Tất cả đối tác nhà hàng của YumExpress đều có chứng nhận vệ sinh an toàn và nguyên liệu sạch 100%.</p>
            </div>
            <div class="feature-box">
                <i class="fas fa-percentage"></i>
                <h4>Ưu Đãi Mỗi Ngày</h4>
                <p>Săn deal hot giảm giá từ 30% - 50% cùng hàng ngàn mã freeship tung ra vào các khung giờ vàng.</p>
            </div>
        </div>

        <div class="team-section">
            <h3>Đội Ngũ Phát Triển (Nhóm 8)</h3>
            <p style="color: #666;">Những con người đứng sau hệ thống đặt và quản lý món ăn YumExpress</p>
            <div class="team-grid">
                <div class="team-card">
                    <i class="fas fa-user-circle style" style="font-size: 3rem; color: #aaa;"></i>
                    <h4>Trần Minh Khang</h4>
                    <p>Trưởng Nhóm / Front-End Developer</p>
                </div>
                <div class="team-card">
                    <i class="fas fa-user-circle style" style="font-size: 3rem; color: #aaa;"></i>
                    <h4>Nguyễn Văn A</h4>
                    <p>Back-End Developer / Database</p>
                </div>
                <div class="team-card">
                    <i class="fas fa-user-circle style" style="font-size: 3rem; color: #aaa;"></i>
                    <h4>Lê Thị B</h4>
                    <p>UI/UX Designer & Content</p>
                </div>
            </div>
        </div>
    </div>

    <div style="margin-top: 50px;">
        <?php include 'footer.php'; ?>
    </div>

</body>
</html>