<?php 
// Khởi động session ở ngay đầu file để kiểm tra trạng thái đăng nhập
session_start(); 

// 1. Gọi kết nối database
include 'config.php'; 

// 2. XỬ LÝ LOGIC TÌM KIẾM
$search = "";
if (isset($_GET['search'])) {
    $search = $conn->real_escape_string($_GET['search']);
    // Câu lệnh lấy sản phẩm có lọc theo tên
    $sql = "SELECT * FROM products WHERE status = 1 AND name LIKE '%$search%'";
} else {
    // Nếu không tìm kiếm thì lấy hết sản phẩm như bình thường
    $sql = "SELECT * FROM products WHERE status = 1";
}

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>YumExpress - Đồ Ăn Ngon Giao Tận Nơi</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

    <header class="main-header">
        <div class="container header-inner">
            <div class="logo"><h1>Yum<span>Express</span></h1></div>
            <ul class="nav-links">
                <li><a href="index.php" class="nav-link active">Trang chủ</a></li>
                <li><a href="#" class="nav-link">Giới thiệu</a></li>
                <li><a href="#" class="nav-link">Sản phẩm</a></li>
                <li><a href="#" class="nav-link">Tin tức</a></li>
                <li><a href="#" class="nav-link">Liên hệ</a></li>
            </ul>
            <div class="header-actions">
                
                <form action="index.php" method="GET" class="search-box">
                    <input type="text" name="search" placeholder="Tìm món ăn..." value="<?php echo htmlspecialchars($search); ?>">
                    <button type="submit"><i class="fas fa-search"></i></button>
                </form>

                <a href="cart.php" class="cart-icon-btn" style="text-decoration: none; display: inline-flex; align-items: center; justify-content: center;">
                    <i class="fas fa-shopping-cart"></i>
                    <span class="cart-count">
                        <?php echo isset($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0; ?>
                    </span>
                </a>

                <?php if (isset($_SESSION['user'])): ?>
                    <div class="user-logged" style="position: relative; display: inline-block; margin-left: 15px;">
                        <span style="font-weight: 600; color: #2c3e2f; cursor: pointer;" id="userDropdownTrigger">
                            <i class="fas fa-user-circle"></i> Hi, <?php echo $_SESSION['user']['fullname']; ?>
                        </span>
                        
                        <div class="user-dropdown" style="display: none; position: absolute; right: 0; background: white; box-shadow: 0 4px 12px rgba(0,0,0,0.1); border-radius: 8px; width: 180px; margin-top: 10px; padding: 10px 0; z-index: 100;">
                            <a href="#" style="display: block; padding: 8px 16px; color: #333; text-decoration: none; font-size: 0.9rem;">Thông tin cá nhân</a>
                            <a href="#" style="display: block; padding: 8px 16px; color: #333; text-decoration: none; font-size: 0.9rem;">Đổi mật khẩu</a>
                            
                            <?php if ($_SESSION['user']['role'] == 'admin'): ?>
                                <hr style="border: 0; border-top: 1px solid #eee; margin: 5px 0;">
                                <a href="admin.php" style="display: block; padding: 8px 16px; color: #ff6b35; text-decoration: none; font-size: 0.9rem; font-weight: 600;">Trang Quản Trị</a>
                            <?php endif; ?>
                            
                            <hr style="border: 0; border-top: 1px solid #eee; margin: 5px 0;">
                            <a href="logout.php" style="display: block; padding: 8px 16px; color: #e53e3e; text-decoration: none; font-size: 0.9rem;">Đăng xuất</a>
                        </div>
                    </div>

                    <script>
                        const trigger = document.getElementById('userDropdownTrigger');
                        const dropdown = document.querySelector('.user-dropdown');

                        trigger.addEventListener('click', function(e) {
                            e.stopPropagation();
                            dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
                        });

                        window.addEventListener('click', function() {
                            dropdown.style.display = 'none';
                        });
                    </script>

                <?php else: ?>
                    <a href="auth.php" class="btn-login-trigger" style="text-decoration: none; display: inline-block;">Đăng nhập</a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <div class="container">
        <div class="main-banner">
            <div class="banner-content">
                <h2>Siêu Tiệc YumExpress</h2>
                <p>Giảm ngay 30% cho đơn hàng đầu tiên của bạn!</p>
            </div>
        </div>
    </div>

    <div class="container main-layout">
        <aside class="sidebar">
            <h3 class="sidebar-title">Danh Mục Món</h3>
            <ul class="sidebar-menu">
                <li><a href="index.php" class="<?php echo empty($search) ? 'active' : ''; ?>"><i class="fas fa-utensils"></i> Tất cả món</a></li>
                <li><a href="#"><i class="fas fa-drumstick-bite"></i> Gà Rán Sốt Cay</a></li>
                <li><a href="#"><i class="fas fa-glass-tea"></i> Trà Sữa Đậm Vị</a></li>
                <li><a href="#"><i class="fas fa-hamburger"></i> Thức Ăn Nhanh</a></li>
            </ul>
        </aside>

        <main class="main-content">
            <?php if (!empty($search)): ?>
                <h2 class="title">Kết quả tìm kiếm cho: "<?php echo htmlspecialchars($search); ?>"</h2>
            <?php else: ?>
                <h2 class="title">Món Ăn Nổi Bật</h2>
            <?php endif; ?>

            <div class="product-grid">
                <?php 
                if ($result && $result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        ?>
                        <div class="product-card">
                            <div class="product-img">
                                <?php if(!empty($row['image'])): ?>
                                    <img src="images/<?php echo $row['image']; ?>" alt="<?php echo $row['name']; ?>" class="food-img">
                                <?php else: ?>
                                    <div class="placeholder-img">🍕 YumExpress</div>
                                <?php endif; ?>
                            </div>
                            <div class="product-info">
                                <h3><?php echo $row['name']; ?></h3>
                                <p class="desc"><?php echo htmlspecialchars($row['description']); ?></p>
                                <div class="price-row">
                                    <span class="price"><?php echo number_format($row['price'], 0, ',', '.'); ?>đ</span>
                                    
                                    <a href="add_to_cart.php?id=<?php echo $row['id']; ?>" class="btn-add" style="text-decoration: none; display: inline-block; text-align: center; line-height: normal;">Đặt mua</a>
                                </div>
                            </div>
                        </div>
                        <?php 
                    }
                } else {
                    echo "<p style='grid-column: 1/-1; text-align: center; padding: 40px;'>Rất tiếc, YumExpress không tìm thấy món phù hợp với từ khóa này!</p>";
                }
                ?>
            </div>
        </main>
    </div>

    <footer>
        <div class="container">
            <p>&copy; 2026 YumExpress - Dự án Thiết kế & Phát triển Website Nhóm 8. <br> Sinh viên thực hiện: Trần Minh Khang</p>
        </div>
    </footer>

</body>
</html>