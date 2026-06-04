<?php
session_start();
include 'config.php';

// Kiểm tra quyền Admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

$msg = "";

// 1. LẤY THÔNG TIN CŨ CỦA MỚN ĂN ĐỂ ĐIỀN VÀO FORM (BAO GỒM DISCOUNT VÀ STATUS)
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $sql_old = "SELECT * FROM products WHERE id = $id";
    $result_old = $conn->query($sql_old);
    
    if ($result_old && $result_old->num_rows > 0) {
        $product = $result_old->fetch_assoc();
    } else {
        die("Không tìm thấy món ăn này!");
    }
} else {
    header("Location: admin.php");
    exit();
}

// 2. XỬ LÝ KHI ADMIN BẤM NÚT CẬP NHẬT
if (isset($_POST['btn_update'])) {
    $name = $conn->real_escape_string($_POST['name']);
    $price = floatval($_POST['price']);
    $discount = intval($_POST['discount']); // Nhận giá trị giảm giá mới
    $description = $conn->real_escape_string($_POST['description']);
    $status = intval($_POST['status']); // Nhận trạng thái mới (1, 2, hoặc 3)
    
    // Mặc định giữ lại tên ảnh cũ nếu không chọn ảnh mới
    $image = $product['image']; 
    
    // Nếu Admin có chọn file ảnh mới
    if (!empty($_FILES['image']['name'])) {
        $image = $_FILES['image']['name'];
        $target = "images/" . basename($image);
        move_uploaded_file($_FILES['image']['tmp_name'], $target);
    }

    // Câu lệnh cập nhật SQL (Đã bổ sung cột discount và cập nhật status)
    $sql_update = "UPDATE products 
                   SET name = '$name', 
                       price = '$price', 
                       discount = '$discount', 
                       description = '$description', 
                       image = '$image', 
                       status = '$status' 
                   WHERE id = $id";

    if ($conn->query($sql_update) === TRUE) {
        $msg = "<div style='color:green; padding:10px; font-weight:600;'><i class='fas fa-check-circle'></i> Cập nhật món ăn thành công!</div>";
        // Tải lại thông tin mới để hiển thị lên form luôn
        $product['name'] = $name;
        $product['price'] = $price;
        $product['discount'] = $discount;
        $product['description'] = $description;
        $product['image'] = $image;
        $product['status'] = $status;
    } else {
        $msg = "<div style='color:red; padding:10px;'>Lỗi cập nhật: " . $conn->error . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Sửa món ăn - YumExpress</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body { font-family: 'Inter', sans-serif; background: #f4f7f6; padding: 40px; }
        .form-container { max-width: 600px; margin: 0 auto; background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        h2 { color: #2c3e2f; margin-bottom: 20px; border-bottom: 2px solid #3498db; padding-bottom: 10px; display: flex; align-items: center; gap: 10px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: 600; }
        input[type="text"], input[type="number"], textarea, select { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; box-sizing: border-box; font-family: inherit; }
        .btn-submit { background: #3498db; color: white; border: none; padding: 12px 25px; border-radius: 6px; cursor: pointer; font-weight: 600; width: 100%; font-size: 1rem; transition: 0.2s; }
        .btn-submit:hover { background: #2980b9; }
        .btn-back { display: inline-block; margin-top: 15px; color: #666; text-decoration: none; font-size: 0.95rem; }
        .btn-back:hover { color: #333; text-decoration: underline; }
        .current-img { width: 80px; height: 80px; object-fit: cover; border-radius: 6px; margin-top: 5px; display: block; border: 1px solid #eee; }
    </style>
</head>
<body>

<div class="form-container">
    <h2><i class="fas fa-edit" style="color: #3498db;"></i> Chỉnh Sửa Món Ăn</h2>
    <?php echo $msg; ?>

    <form action="" method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label>Tên món ăn:</label>
            <input type="text" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" required>
        </div>
        
        <div class="form-group">
            <label>Giá bán gốc (VNĐ):</label>
            <input type="number" name="price" value="<?php echo $product['price']; ?>" required>
        </div>

        <div class="form-group">
            <label>Giảm giá (%):</label>
            <input type="number" name="discount" min="0" max="100" value="<?php echo isset($product['discount']) ? $product['discount'] : 0; ?>" placeholder="Nhập từ 0 đến 100">
        </div>
        
        <div class="form-group">
            <label>Hình ảnh hiện tại:</label>
            <?php if(!empty($product['image'])): ?>
                <img src="images/<?php echo $product['image']; ?>" class="current-img" alt="Ảnh hiện tại">
            <?php else: ?>
                <span style="color: #999; font-size: 0.9rem;">Chưa có ảnh</span>
            <?php endif; ?>
            <label style="margin-top: 10px; font-size: 0.9rem; color: #666; font-weight: normal;">Chọn ảnh khác (nếu muốn đổi):</label>
            <input type="file" name="image">
        </div>
        
        <div class="form-group">
            <label>Mô tả món ăn:</label>
            <textarea name="description" rows="4"><?php echo htmlspecialchars($product['description']); ?></textarea>
        </div>
        
        <div class="form-group">
            <label>Trạng thái kinh doanh:</label>
            <select name="status">
                <option value="1" <?php echo ($product['status'] == 1) ? 'selected' : ''; ?>>🟢 Hiển thị (Đang bán)</option>
                <option value="2" <?php echo ($product['status'] == 2) ? 'selected' : ''; ?>>🟡 Cảnh báo (Gần hết hàng)</option>
                <option value="3" <?php echo ($product['status'] == 3) ? 'selected' : ''; ?>>🔴 Tạm khóa (Hết hàng)</option>
            </select>
        </div>
        
        <button type="submit" name="btn_update" class="btn-submit">Cập nhật thay đổi</button>
    </form>
    <a href="admin.php" class="btn-back">← Quay lại trang quản lý</a>
</div>

</body>
</html>