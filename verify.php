<?php 
session_start(); 
include 'config.php'; 
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tin Tức Ẩm Thực - YumExpress</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .news-title { margin: 30px 0 10px; color: #2c3e50; position: relative; padding-bottom: 10px; }
        .news-title::after { content: ''; position: absolute; bottom: 0; left: 0; width: 60px; height: 3px; background: #e74c3c; }
        .news-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 25px; margin-bottom: 5px; margin-top: 20px; }
        .news-card { background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.03); transition: 0.3s; }
        .news-card:hover { transform: translateY(-5px); box-shadow: 0 6px 20px rgba(0,0,0,0.08); }
        .news-img { height: 180px; overflow: hidden; }
        .news-img img { width: 100%; height: 100%; object-fit: cover; }
        .news-body { padding: 20px; }
        .news-date { font-size: 0.8rem; color: #999; margin-bottom: 10px; display: block; }
        .news-body h4 { font-size: 1.15rem; color: #333; margin-bottom: 10px; line-height: 1.4; }
        .news-body p { color: #666; font-size: 0.9rem; line-height: 1.5; height: 65px; overflow: hidden; }
        .btn-readmore { color: #e67e22; text-decoration: none; font-weight: 600; font-size: 0.9rem; display: inline-block; margin-top: 15px; }
    </style>
</head>
<body>

    <?php include 'header.php'; ?>

    <div class="container">
        <h2 class="news-title"><i class="far fa-newspaper"></i> Blog Tạp Chí Ẩm Thực</h2>
        <p style="color: #777;">Cập nhật các mẹo ăn uống lành mạnh, xu hướng ẩm thực hot và cẩm nang sống khỏe mỗi ngày cùng YumExpress.</p>

        <div class="news-grid">
            <div class="news-card">
                <div class="news-img">
                    <img src="https://images.unsplash.com/photo-1551024601-bec78aea704b?auto=format&fit=crop&w=500&q=80" alt="Trà sữa">
                </div>
                <div class="news-body">
                    <span class="news-date"><i class="far fa-calendar-alt"></i> 04/06/2026 - Xu hướng</span>
                    <h4>Top 5 Loại Trà Sữa Đang Làm Mưa Làm Gió Giới Trẻ Sài Thành</h4>
                    <p>Nếu bạn là một tín đồ hảo ngọt, đừng bỏ qua danh sách những món trà sữa full topping siêu đậm vị đang có lượt đặt hàng khủng nhất tuần này tại hệ thống...</p>
                    <a href="#" class="btn-readmore">Đọc thêm →</a>
                </div>
            </div>

            <div class="news-card">
                <div class="news-img">
                    <img src="https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&w=500&q=80" alt="Healthy food">
                </div>
                <div class="news-body">
                    <span class="news-date"><i class="far fa-calendar-alt"></i> 02/06/2026 - Sức khỏe</span>
                    <h4>Gợi Ý Thực Đơn 5 Ngày Ăn Eat-Clean Cho Sinh Viên Tránh Uể Oải</h4>
                    <p>Làm sao để vừa ăn ngon, đủ chất, tiết kiệm thời gian mà vẫn giữ được vóc dáng cân đối? Hãy khám phá ngay thực đơn lành mạnh dễ làm hoặc đặt trực tiếp...</p>
                    <a href="#" class="btn-readmore">Đọc thêm →</a>
                </div>
            </div>

            <div class="news-card">
                <div class="news-img">
                    <img src="https://images.unsplash.com/photo-1565299624946-b28f40a0ae38?auto=format&fit=crop&w=500&q=80" alt="Pizza">
                </div>
                <div class="news-body">
                    <span class="news-date"><i class="far fa-calendar-alt"></i> 30/05/2026 - Khuyến mãi</span>
                    <h4>Bí Kíp Săn Mã Giảm Giá 50% Khi Đặt Gà Rán Khung Giờ Vàng</h4>
                    <p>Đừng để chiếc ví "khóc thét"! Lưu ngay khung giờ từ 14h - 16h hằng ngày để nhận ưu đãi đồng giá kết hợp cùng mã giảm voucher độc quyền của YumExpress...</p>
                    <a href="#" class="btn-readmore">Đọc thêm →</a>
                </div>
            </div>
        </div>
    </div>

    <div style="margin-top: 50px;">
        <?php include 'footer.php'; ?>
    </div>

</body>
</html>