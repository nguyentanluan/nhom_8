<?php
session_start();
include 'config.php';

// 1. Kiểm tra xem người dùng đã đăng nhập chưa
if (!isset($_SESSION['user'])) {
    echo "<script>
        alert('Khang ơi, bạn phải đăng nhập thì mới thanh toán được nha!');
        window.location.href = 'auth.php';
    </script>";
    exit();
}

// 2. Kiểm tra giỏ hàng có trống không
if (empty($_SESSION['cart'])) {
    echo "<script>
        alert('Giỏ hàng trống không hà, mua đồ đã rồi mới thanh toán được nè!');
        window.location.href = 'index.php';
    </script>";
    exit();
}

// 3. Lấy thông tin khách hàng từ Session và tính tổng tiền
$user_id = $_SESSION['user']['id'];
$total_price = 0;

foreach ($_SESSION['cart'] as $id => $quantity) {
    $id = $conn->real_escape_string($id);
    $sql = "SELECT price FROM products WHERE id = $id";
    $res = $conn->query($sql);
    if ($res && $res->num_rows > 0) {
        $product = $res->fetch_assoc();
        $total_price += $product['price'] * $quantity;
    }
}

// 4. TIẾN HÀNH LƯU VÀO DATABASE
// Bật chế độ Transaction để đảm bảo an toàn dữ liệu (lỗi ở đâu là hủy toàn bộ)
$conn->begin_transaction();

try {
    // Bước A: Chèn dữ liệu chung vào bảng `orders`
    $sql_order = "INSERT INTO orders (user_id, total_price, status, created_at) VALUES ($user_id, $total_price, 'pending', NOW())";
    $conn->query($sql_order);
    
    // Lấy ra ID của đơn hàng vừa mới chèn tự động tăng
    $order_id = $conn->insert_id;

    // Bước B: Duyệt giỏ hàng và chèn chi tiết từng món vào bảng `order_details`
    foreach ($_SESSION['cart'] as $product_id => $quantity) {
        $product_id = $conn->real_escape_string($product_id);
        
        // Lấy lại giá sản phẩm tại thời điểm mua
        $sql_p = "SELECT price FROM products WHERE id = $product_id";
        $res_p = $conn->query($sql_p);
        $p_data = $res_p->fetch_assoc();
        $price = $p_data['price'];

        $sql_detail = "INSERT INTO order_details (order_id, product_id, quantity, price) VALUES ($order_id, $product_id, $quantity, $price)";
        $conn->query($sql_detail);
    }

    // Nếu mọi thứ chạy mượt mà thì xác nhận lưu vĩnh viễn vào MySQL
    $conn->commit();

    // Bước C: Xóa giỏ hàng sau khi đã thanh toán thành công
    unset($_SESSION['cart']);

    echo "<script>
        alert('🎉 Đặt hàng thành công rồi Khang ơi! Đơn hàng của bạn đã được ghi nhận.');
        window.location.href = 'index.php';
    </script>";

} catch (Exception $e) {
    // Nếu có lỗi xảy ra, hoàn tác lại toàn bộ để tránh lỗi dữ liệu rác
    $conn->rollback();
    echo "Có lỗi xảy ra khi đặt hàng: " . $e->getMessage();
}
?>