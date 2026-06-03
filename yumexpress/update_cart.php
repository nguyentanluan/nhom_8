<?php
session_start();

if (isset($_GET['id']) && isset($_GET['action'])) {
    $id = $_GET['id'];
    $action = $_GET['action'];

    if ($action == 'add') {
        $_SESSION['cart'][$id]++;
    } elseif ($action == 'minus') {
        $_SESSION['cart'][$id]--;
        // Nếu số lượng về 0 thì xóa luôn món đó
        if ($_SESSION['cart'][$id] <= 0) {
            unset($_SESSION['cart'][$id]);
        }
    } elseif ($action == 'delete') {
        // Xóa hẳn món ăn khỏi giỏ
        unset($_SESSION['cart'][$id]);
    }
}

// Quay lại trang giỏ hàng
header("Location: cart.php");
exit();
?>