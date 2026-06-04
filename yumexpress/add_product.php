<?php
session_start();
include 'config.php';

// Kiểm tra quyền Admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

$msg = "";

if (isset($_POST['btn_save'])) {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $description = $_POST['description'];
    $status = $_POST['status'];
    
    // XỬ LÝ UPLOAD HÌNH ẢNH
    $image = $_FILES['image']['name'];
    $target = "images/" . basename($image);

    $sql = "INSERT INTO products (name, price, description, image, status) 
            VALUES ('$name', '$price', '$description', '$image', '$status')";

    if ($conn->query($sql) === TRUE) {
        // Di chuyển file ảnh vào thư mục images
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
            $msg = "<div style='color:green; padding:10px;'>Thêm món mới thành công!</div>";
        } else {
            $msg = "<div style='color:orange; padding:10px;'>Đã lưu data nhưng upload ảnh thất bại.</div>";
        }
    } else {
        $msg = "<div style='color:red; padding:10px;'>Lỗi: " . $conn->error . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thêm món mới - YumExpress</title>
    <style>
        body { font-family: 'Inter', sans-serif; background: #f4f7f6; padding: 40px; }
        .form-container { max-width: 600px; margin: 0 auto; background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        h2 { color: #2c3e2f; margin-bottom: 20px; border-bottom: 2px solid #ff6b35; padding-bottom: 10px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: 600; }
        input[type="text"], input[type="number"], textarea, select { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; box-sizing: border-box; }
        .btn-submit { background: #ff6b35; color: white; border: none; padding: 12px 25px; border-radius: 6px; cursor: pointer; font-weight: 600; width: 100%; }
        .btn-back { display: inline-block; margin-top: 15px; color: #666; text-decoration: none; }
    </style>
</head>
<body>

<div class="form-container">
    <h2>Thêm Món Ăn Mới</h2>
    <?php echo $msg; ?>

    <!-- Lưu ý: Phải có enctype="multipart/form-data" mới upload được file -->
    <form action="" method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label>Tên món ăn:</label>
            <input type="text" name="name" required placeholder="Ví dụ: Gà Rán Sốt Hàn Quốc">
        </div>
        <div class="form-group">
            <label>Giá bán (VNĐ):</label>
            <input type="number" name="price" required placeholder="Ví dụ: 59000">
        </div>
        <div class="form-group">
            <label>Hình ảnh:</label>
            <input type="file" name="image" required>
        </div>
        <div class="form-group">
            <label>Mô tả món ăn:</label>
            <textarea name="description" rows="4" placeholder="Mô tả ngắn gọn về món ăn..."></textarea>
        </div>
        <div class="form-group">
            <label>Trạng thái:</label>
            <select name="status">
                <option value="1">Hiển thị (Đang bán)</option>
                <option value="0">Ẩn (Hết hàng)</option>
            </select>
        </div>
        <button type="submit" name="btn_save" class="btn-submit">Lưu món ăn</button>
    </form>
    <a href="admin.php" class="btn-back">← Quay lại danh sách</a>
</div>

</body>
</html>