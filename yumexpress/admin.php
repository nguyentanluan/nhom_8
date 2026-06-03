<?php
session_start();
include 'config.php';

// KIỂM TRA QUYỀN: Nếu không phải admin, đá văng về trang chủ ngay
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

// Lấy danh sách sản phẩm
$sql = "SELECT * FROM products ORDER BY id DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý món ăn - Admin YumExpress</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body { font-family: 'Inter', sans-serif; background: #f4f7f6; padding: 20px; }
        .admin-container { max-width: 1000px; margin: 0 auto; background: white; padding: 30px; border-radius: 12px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
        .header-admin { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .btn-add { background: #27ae60; color: white; padding: 10px 20px; text-decoration: none; border-radius: 6px; font-weight: 600; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #eee; }
        th { background: #f8f9fa; color: #555; }
        .img-admin { width: 60px; height: 60px; object-fit: cover; border-radius: 4px; }
        .actions a { margin-right: 10px; text-decoration: none; font-size: 1.1rem; }
        .edit-icon { color: #3498db; }
        .delete-icon { color: #e74c3c; }
    </style>
</head>
<body>

<div class="admin-container">
    <div class="header-admin">
        <h2><i class="fas fa-tasks"></i> Quản Lý Món Ăn</h2>
        <div class="nav-right">
            <a href="add_product.php" class="btn-add"><i class="fas fa-plus"></i> Thêm món mới</a>
            <a href="index.php" style="margin-left: 15px; color: #666;">Về trang chủ</a>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Ảnh</th>
                <th>Tên món</th>
                <th>Giá</th>
                <th>Trạng thái</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = $result->fetch_assoc()): ?>
            <tr>
                <td>
                    <img src="images/<?php echo $row['image']; ?>" class="img-admin" alt="">
                </td>
                <td><strong><?php echo $row['name']; ?></strong></td>
                <td><?php echo number_format($row['price'], 0, ',', '.'); ?>đ</td>
                <td>
                    <?php echo $row['status'] == 1 ? '<span style="color:green">Đang bán</span>' : '<span style="color:red">Ẩn</span>'; ?>
                </td>
                <td class="actions">
                    <a href="edit_product.php?id=<?php echo $row['id']; ?>" class="edit-icon" title="Sửa"><i class="fas fa-edit"></i></a>
                    <a href="delete_product.php?id=<?php echo $row['id']; ?>" class="delete-icon" onclick="return confirm('bạn chắc chắn muốn xóa món này không?')" title="Xóa"><i class="fas fa-trash"></i></a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

</body>
</html>