<?php
session_start();
include 'config.php';

// Kiểm tra quyền Admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // Câu lệnh xóa sản phẩm theo ID
    $sql = "DELETE FROM products WHERE id = $id";

    if ($conn->query($sql) === TRUE) {
        // Xóa xong thì quay về trang quản lý
        header("Location: admin.php");
    } else {
        echo "Lỗi khi xóa: " . $conn->error;
    }
}
?>