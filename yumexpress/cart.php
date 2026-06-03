<?php
session_start();
include 'config.php';
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giỏ hàng của bạn - YumExpress</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #fdfaf5; }
        .cart-table { width: 100%; border-collapse: collapse; margin-top: 20px; background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .cart-table th, .cart-table td { padding: 15px; border-bottom: 1px solid #eee; text-align: left; }
        .cart-table th { background-color: #f9f9f9; color: #666; font-weight: 600; }
        .food-img-cart { width: 60px; height: 60px; object-fit: cover; border-radius: 8px; }
        .quantity-control { display: flex; align-items: center; gap: 10px; }
        .quantity-control a { text-decoration: none; font-size: 1.1rem; transition: 0.2s; }
        .quantity-control a:hover { transform: scale(1.1); }
        .total-price { font-size: 1.5rem; color: #ff6b35; font-weight: bold; text-align: right; margin-top: 20px; }
        .btn-checkout { background: #2c3e2f; color: white; padding: 12px 30px; border-radius: 8px; text-decoration: none; font-weight: 600; display: inline-block; float: right; margin-top: 20px; box-shadow: 0 4px 10px rgba(44,62,47,0.2); }
    </style>
</head>
<body>
    <div class="container" style="padding-top: 50px; max-width: 1000px; margin: 0 auto;">
        <h2>🛒 Giỏ Hàng Của Bạn</h2>
        <table class="cart-table">
            <thead>
                <tr>
                    <th>Hình ảnh</th>
                    <th>Món ăn</th>
                    <th>Giá</th>
                    <th>Số lượng</th>
                    <th>Thành tiền</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $total = 0;
                if (!empty($_SESSION['cart'])) {
                    foreach ($_SESSION['cart'] as $id => $quantity) {
                        $id = $conn->real_escape_string($id);
                        $sql = "SELECT * FROM products WHERE id = $id";
                        $res = $conn->query($sql);
                        $product = $res->fetch_assoc();
                        $subtotal = $product['price'] * $quantity;
                        $total += $subtotal;
                        ?>
                        <tr>
                            <td>
                                <?php if(!empty($product['image'])): ?>
                                    <img src="images/<?php echo $product['image']; ?>" class="food-img-cart" alt="">
                                <?php else: ?>
                                    <div style="font-size:1.5rem;">🍕</div>
                                <?php endif; ?>
                            </td>
                            <td style="font-weight: 600; color: #2c3e2f;"><?php echo htmlspecialchars($product['name']); ?></td>
                            <td style="color: #ff6b35; font-weight: 500;"><?php echo number_format($product['price'], 0, ',', '.'); ?>đ</td>
                            <td>
                                <div class="quantity-control">
                                    <a href="update_cart.php?id=<?php echo $id; ?>&action=minus" style="color: #e53e3e;"><i class="fas fa-minus-circle"></i></a>
                                    <span style="font-weight: bold; min-width: 20px; text-align: center;"><?php echo $quantity; ?></span>
                                    <a href="update_cart.php?id=<?php echo $id; ?>&action=add" style="color: #2c3e2f;"><i class="fas fa-plus-circle"></i></a>
                                </div>
                            </td>
                            <td style="color: #ff6b35; font-weight: bold;"><?php echo number_format($subtotal, 0, ',', '.'); ?>đ</td>
                            <td>
                                <a href="update_cart.php?id=<?php echo $id; ?>&action=delete" style="color: #e53e3e; font-size: 1.1rem;" onclick="return confirm('Khang muốn xóa món này ra khỏi giỏ hàng hả?')">
                                    <i class="fas fa-trash-alt"></i>
                                </a>
                            </td>
                        </tr>
                        <?php
                    }
                } else {
                    echo "<tr><td colspan='6' style='text-align: center; padding: 40px; color: #999;'>Giỏ hàng trống không hà Khang ơi!</td></tr>";
                }
                ?>
            </tbody>
        </table>
        
        <div class="total-price">Tổng cộng: <?php echo number_format($total, 0, ',', '.'); ?>đ</div>
        
        <div style="margin-top: 20px; overflow: hidden;">
            <a href="index.php" style="text-decoration: none; color: #666; display: inline-block; margin-top: 25px;">← Tiếp tục mua món khác</a>
            
            <?php if ($total > 0): ?>
                <a href="checkout.php" class="btn-checkout">Xác nhận thanh toán <i class="fas fa-chevron-right"></i></a>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>