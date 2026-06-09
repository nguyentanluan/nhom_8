<header class="main-header">
    <div class="container header-inner">
        <div class="logo"><h1>Yum<span>Express</span></h1></div>
        
        <ul class="nav-links">
    <li><a href="index.php" class="nav-link">Trang chủ</a></li>
    <li><a href="about.php" class="nav-link">Giới thiệu</a></li>
    <li><a href="index.php#danh-sach-mon" class="nav-link">Sản phẩm</a></li>
    <li><a href="news.php" class="nav-link">Tin tức</a></li>
    <li><a href="contact.php" class="nav-link">Liên hệ</a></li>
</ul>
        <div class="header-actions">
            <form action="index.php" method="GET" class="search-box">
                <input type="text" name="search" placeholder="Tìm món ăn..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
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
                        <a href="profile.php" style="display: block; padding: 8px 16px; color: #333; text-decoration: none; font-size: 0.9rem;">Thông tin cá nhân</a>
                        <a href="change_password.php" style="display: block; padding: 8px 16px; color: #333; text-decoration: none; font-size: 0.9rem;">Đổi mật khẩu</a>
                        
                        <?php if ($_SESSION['user']['role'] == 'admin'): ?>
                            <hr style="border: 0; border-top: 1px solid #eee; margin: 5px 0;">
                            <a href="admin.php" style="display: block; padding: 8px 16px; color: #ff6b35; text-decoration: none; font-size: 0.9rem; font-weight: 600;">Trang Quản Trị</a>
                        <?php endif; ?>
                        
                        <hr style="border: 0; border-top: 1px solid #eee; margin: 5px 0;">
                        <a href="logout.php" style="display: block; padding: 8px 16px; color: #e53e3e; text-decoration: none; font-size: 0.9rem;">Đăng xuất</a>
                    </div>
                </div>

                <script>
                    // Script xử lý bật tắt menu dropdown tài khoản khi click
                    document.addEventListener('DOMContentLoaded', function() {
                        const trigger = document.getElementById('userDropdownTrigger');
                        const dropdown = document.querySelector('.user-dropdown');

                        if(trigger && dropdown) {
                            trigger.addEventListener('click', function(e) {
                                e.stopPropagation();
                                dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
                            });

                            window.addEventListener('click', function() {
                                dropdown.style.display = 'none';
                            });
                        }
                    });
                </script>

            <?php else: ?>
                <a href="auth.php" class="btn-login-trigger" style="text-decoration: none; display: inline-block;">Đăng nhập</a>
            <?php endif; ?>
        </div>
    </div>
</header>