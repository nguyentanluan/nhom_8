<?php 
// Khởi động session ở ngay đầu file để kiểm tra trạng thái đăng nhập
session_start(); 

// 1. Gọi kết nối database
include 'config.php'; 

// 2. XỬ LÝ LOGIC TÌM KIẾM & LỌC DANH MỤC TỰ ĐỘNG (ĐỘNG 100%)
$search = "";
$category_title = "Món Ăn Nổi Bật"; // Tiêu đề hiển thị mặc định

if (isset($_GET['search'])) {
    $search = $conn->real_escape_string($_GET['search']);
    // Câu lệnh lấy sản phẩm có lọc theo tên
    $sql = "SELECT * FROM products WHERE status IN (1, 2, 3) AND name LIKE '%$search%'";
    $category_title = "Kết quả tìm kiếm cho: \"" . htmlspecialchars($search) . "\"";
} elseif (isset($_GET['cat_id'])) {
    // Ép kiểu số nguyên cho an toàn dữ liệu
    $cat_id = intval($_GET['cat_id']); 
    
    // Câu lệnh lấy sản phẩm lọc theo id danh mục động
    $sql = "SELECT * FROM products WHERE status IN (1, 2, 3) AND category_id = $cat_id";
    
    // Lấy tên danh mục từ bảng categories để hiển thị lên tiêu đề chính
    $res_name = $conn->query("SELECT name FROM categories WHERE id = $cat_id");
    if ($res_name && $row_name = $res_name->fetch_assoc()) {
        $category_title = $row_name['name'];
    }
} else {
    // Nếu không tìm kiếm và không chọn danh mục thì lấy hết sản phẩm như bình thường
    $sql = "SELECT * FROM products WHERE status IN (1, 2, 3)";
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

    <?php include 'header.php'; ?>

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
                <li>
                    <a href="index.php" class="<?php echo !isset($_GET['cat_id']) ? 'active' : ''; ?>">
                        <i class="fas fa-utensils"></i> Tất cả món
                    </a>
                </li>
                
                <?php 
                // VÒNG LẶP ĐỘNG: Tự động lấy toàn bộ danh mục hiện có trong bảng categories ra sidebar
                $sql_cate = "SELECT * FROM categories"; 
                $result_cate = $conn->query($sql_cate);
                
                if ($result_cate && $result_cate->num_rows > 0) {
                    while($cat = $result_cate->fetch_assoc()) {
                        $active_class = (isset($_GET['cat_id']) && $_GET['cat_id'] == $cat['id']) ? 'active' : '';
                        
                        $icon = "fas fa-chevron-right";
                        if (stripos($cat['name'], 'Gà') !== false) $icon = "fas fa-drumstick-bite";
                        if (stripos($cat['name'], 'Trà') !== false || stripos($cat['name'], 'Sữa') !== false) $icon = "fas fa-glass-tea";
                        if (stripos($cat['name'], 'Nhanh') !== false || stripos($cat['name'], 'Burger') !== false) $icon = "fas fa-hamburger";
                        
                        echo "<li>
                                <a href='index.php?cat_id={$cat['id']}' class='{$active_class}'>
                                    <i class='{$icon}'></i> {$cat['name']}
                                </a>
                              </li>";
                    }
                }
                ?>
            </ul>
        </aside>

        <main class="main-content" id="danh-sach-mon">
            <h2 class="title"><?php echo $category_title; ?></h2>

            <div class="product-grid">
                <?php 
                if ($result && $result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        // Tính toán giá giảm (Bọc kiểm tra an toàn dữ liệu nếu chưa có cột discount)
                        $original_price = $row['price'];
                        $discount = isset($row['discount']) ? intval($row['discount']) : 0;
                        $current_price = $original_price - ($original_price * $discount / 100);
                        ?>
                        <div class="product-card" style="position: relative; <?php echo ($row['status'] == 3) ? 'opacity: 0.65;' : ''; ?>">
                            
                            <?php if ($discount > 0): ?>
                                <div style="position: absolute; top: 12px; left: 12px; background: #e74c3c; color: white; padding: 4px 8px; font-weight: 700; border-radius: 6px; font-size: 0.8rem; z-index: 5; box-shadow: 0 2px 6px rgba(231,76,60,0.3);">
                                    🔥 -<?php echo $discount; ?>%
                                </div>
                            <?php endif; ?>

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
                                
                                <?php if ($row['status'] == 2): ?>
                                    <p style="color: #f39c12; font-size: 0.8rem; margin: 4px 0; font-weight: 600;"><i class="fas fa-exclamation-circle"></i> Món này sắp hết!</p>
                                <?php elseif ($row['status'] == 3): ?>
                                    <p style="color: #e74c3c; font-size: 0.8rem; margin: 4px 0; font-weight: 600;"><i class="fas fa-times-circle"></i> Tạm hết hàng</p>
                                <?php endif; ?>

                                <div class="price-row">
                                    <div class="price-box" style="display: flex; flex-direction: column;">
                                        <?php if ($discount > 0): ?>
                                            <span style="text-decoration: line-through; color: #aaa; font-size: 0.8rem; margin-bottom: -2px; font-weight: 400;">
                                                <?php echo number_format($original_price, 0, ',', '.'); ?>đ
                                            </span>
                                            <span class="price" style="color: #e74c3c; font-weight: 700;">
                                                <?php echo number_format($current_price, 0, ',', '.'); ?>đ
                                            </span>
                                        <?php else: ?>
                                            <span class="price"><?php echo number_format($original_price, 0, ',', '.'); ?>đ</span>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <?php if ($row['status'] == 3): ?>
                                        <button class="btn-add" style="background: #bdc3c7; color: white; cursor: not-allowed; border: none; padding: 8px 12px; border-radius: 6px; font-size: 0.9rem;" disabled>Hết hàng</button>
                                    <?php else: ?>
                                        <a href="add_to_cart.php?id=<?php echo $row['id']; ?>" class="btn-add" style="text-decoration: none; display: inline-block; text-align: center; line-height: normal;">Đặt mua</a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <?php 
                    }
                } else {
                    echo "<p style='grid-column: 1/-1; text-align: center; padding: 40px;'>Rất tiếc, YumExpress không tìm thấy món phù hợp với danh mục hoặc từ khóa này!</p>";
                }
                ?>
            </div>
        </main>
    </div>

    <footer>
        <div class="container">
            <p>&copy; 2026 YumExpress - Dự án Thiết kế & Phát triển Website Nhóm 8.</p>
        </div>
    </footer>

</body>
</html>