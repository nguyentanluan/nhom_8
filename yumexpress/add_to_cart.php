<?php
session_start();

// Nếu chưa có giỏ hàng trong session thì tạo mới một mảng rỗng
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = array();
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Nếu món ăn đã có trong giỏ, tăng số lượng lên 1
    if (isset($_SESSION['cart'][$id])) {
        $_SESSION['cart'][$id]++;
    } else {
        // Nếu chưa có, thêm mới với số lượng là 1
        $_SESSION['cart'][$id] = 1;
    }
}

// Quay lại trang trước đó
header("Location: " . $_SERVER['HTTP_REFERER']);
exit();
?>