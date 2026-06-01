// ==========================================================================
// AREA 1: GLOBAL CONFIGURATION & DATA INITIALIZATION
// (Khởi tạo biến toàn cục, nạp dữ liệu từ HTML và LocalStorage)
// ==========================================================================
let products = [];
let categories = [];
const productDataElement = document.getElementById('productData');
if (productDataElement) {
    const productDataJson = productDataElement.getAttribute('data-products');
    products = JSON.parse(productDataJson);
    categories = [...new Set(products.map(p => p.category))];
}

// ĐÃ CẬP NHẬT: Thêm thuộc tính role để phân biệt tài khoản thường (user) và quản trị (admin)
let users = [
    { 
        username: "demo", 
        password: "123", 
        fullname: "Demo User",
        phone: "0123456789",
        email: "demo@yumexpress.com", 
        address: "",
        addresses: [],
        role: "user"
    },
    { 
        username: "admin", 
        password: "admin", 
        fullname: "Quản trị viên",
        phone: "0999999999",
        email: "admin@yumexpress.com", 
        address: "",
        addresses: [],
        role: "admin"
    }
];

let currentUser = null;
let cart = [];
let orders = [];
let slideInterval = null;
let currentPage = "home";
let selectedProduct = null;

// ==========================================================================
// AREA 2: CORE UTILITIES & NOTIFICATIONS
// (Các hàm tiện ích dùng chung: Lưu/Tải dữ liệu, Thông báo Toast)
// ==========================================================================
function saveToLocal() {
    localStorage.setItem("yumexpress_users", JSON.stringify(users));
    localStorage.setItem("yumexpress_orders", JSON.stringify(orders));
}

// ĐÃ CẬP NHẬT: Đảm bảo khi nạp dữ liệu từ LocalStorage luôn bổ sung đầy đủ role cho admin và demo
function loadData() {
    let storedUsers = localStorage.getItem("yumexpress_users");
    if (storedUsers) users = JSON.parse(storedUsers);
    let storedOrd = localStorage.getItem("yumexpress_orders");
    if (storedOrd) orders = JSON.parse(storedOrd);
    
    if (!users.find(u => u.username === "demo")) {
        users.push({ username: "demo", password: "123", fullname: "Demo User", phone: "0123456789", email: "demo@yumexpress.com", address: "", addresses: [], role: "user" });
    }
    if (!users.find(u => u.username === "admin")) {
        users.push({ username: "admin", password: "admin", fullname: "Quản trị viên", phone: "0999999999", email: "admin@yumexpress.com", address: "", addresses: [], role: "admin" });
    }
    updateCartCount();
}

function showToast(msg) {
    let oldToast = document.querySelector(".toast-msg");
    if (oldToast) oldToast.remove();

    let toast = document.createElement("div");
    toast.className = "toast-msg";
    toast.innerText = msg;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 2000);
}

// ==========================================================================
// AREA 3: USER AUTHENTICATION
// (Xử lý Đăng nhập, Đăng ký, Đăng xuất, Quên mật khẩu)
// ==========================================================================
function openAuthModal() { 
    let modal = document.getElementById("authModal");
    if (modal) modal.style.display = "flex";
}

function closeAuthModal() { 
    let modal = document.getElementById("authModal");
    if (modal) modal.style.display = "none";
}

function handleAuth() {
    let userInput = document.getElementById("loginUser").value.trim();
    let pass = document.getElementById("loginPass").value.trim();
    let msgDiv = document.getElementById("authMessage");
    if (!userInput || !pass) { msgDiv.innerHTML = "❌ Vui lòng nhập đầy đủ"; msgDiv.className = "auth-message error"; return; }
    
    let found = users.find(u => u.username === userInput || u.email === userInput || u.phone === userInput);
    if (found && found.password === pass) {
        currentUser = found;
        msgDiv.innerHTML = "";
        closeAuthModal();
        showToast(`Chào mừng ${found.fullname || found.username} đến với YumExpress!`);
        renderCurrentPage();
        updateUserUI();
        if (document.getElementById("rememberMe")?.checked) {
            localStorage.setItem("yumexpress_remember", JSON.stringify({ username: found.username, password: pass }));
        }
    } else {
        msgDiv.innerHTML = "❌ Sai tài khoản hoặc mật khẩu";
        msgDiv.className = "auth-message error";
    }
}

function handleRegister() {
    const fullname = document.getElementById("regFullname").value.trim();
    const phone = document.getElementById("regPhone").value.trim();
    const email = document.getElementById("regEmail").value.trim();
    const password = document.getElementById("regPassword").value;
    const confirmPassword = document.getElementById("regConfirmPassword").value;
    const address = document.getElementById("regAddress").value.trim();
    const agreeTerms = document.getElementById("agreeTerms").checked;
    const msgDiv = document.getElementById("registerMessage");
    
    if (!fullname) { msgDiv.innerHTML = "❌ Vui lòng nhập họ và tên"; msgDiv.className = "auth-message error"; return; }
    if (!phone) { msgDiv.innerHTML = "❌ Vui lòng nhập số điện thoại"; msgDiv.className = "auth-message error"; return; }
    if (!email) { msgDiv.innerHTML = "❌ Vui lòng nhập email"; msgDiv.className = "auth-message error"; return; }
    if (!password) { msgDiv.innerHTML = "❌ Vui lòng nhập mật khẩu"; msgDiv.className = "auth-message error"; return; }
    if (password !== confirmPassword) { msgDiv.innerHTML = "❌ Mật khẩu xác nhận không khớp"; msgDiv.className = "auth-message error"; return; }
    if (password.length < 6) { msgDiv.innerHTML = "❌ Mật khẩu phải có ít nhất 6 ký tự"; msgDiv.className = "auth-message error"; return; }
    if (!agreeTerms) { msgDiv.innerHTML = "❌ Vui lòng đồng ý với Điều khoản sử dụng"; msgDiv.className = "auth-message error"; return; }
    
    const existingUser = users.find(u => u.email === email || u.phone === phone);
    if (existingUser) { msgDiv.innerHTML = "❌ Email hoặc số điện thoại đã được đăng ký"; msgDiv.className = "auth-message error"; return; }
    
    let username = email.split('@')[0];
    let counter = 1;
    let originalUsername = username;
    while (users.find(u => u.username === username)) { username = originalUsername + counter; counter++; }
    
    // Tài khoản tự đăng ký mặc định nhận role: "user"
    const newUser = { username: username, password: password, fullname: fullname, phone: phone, email: email, address: address, addresses: address ? [address] : [] , role: "user" };
    users.push(newUser);
    currentUser = newUser;
    saveToLocal();
    
    msgDiv.innerHTML = "✅ Đăng ký thành công! Chào mừng " + fullname + " đến với YumExpress! 🎉";
    msgDiv.className = "auth-message success";
    
    document.getElementById("regFullname").value = "";
    document.getElementById("regPhone").value = "";
    document.getElementById("regEmail").value = "";
    document.getElementById("regPassword").value = "";
    document.getElementById("regConfirmPassword").value = "";
    document.getElementById("regAddress").value = "";
    document.getElementById("agreeTerms").checked = false;
    
    updateUserUI();
    
    setTimeout(() => { 
        closeAuthModal(); 
        showToast(`🎉 Chào mừng ${fullname} đến với YumExpress!`); 
        renderCurrentPage();
    }, 2000);
}

function handleForgotPassword() {
    const email = document.getElementById("forgotEmail").value.trim();
    const msgDiv = document.getElementById("forgotMessage");
    if (!email) { msgDiv.innerHTML = "❌ Vui lòng nhập email"; msgDiv.className = "auth-message error"; return; }
    const user = users.find(u => u.email === email);
    if (!user) { msgDiv.innerHTML = "❌ Email không tồn tại trong hệ thống"; msgDiv.className = "auth-message error"; return; }
    msgDiv.innerHTML = "✅ Link khôi phục mật khẩu đã được gửi! (Mật khẩu: " + user.password + ")";
    msgDiv.className = "auth-message success";
    setTimeout(() => { document.getElementById("closeForgotModal").click(); }, 2000);
}

// ĐÃ CẬP NHẬT: Tự động ẩn/hiện nút Admin tùy thuộc vào quyền của tài khoản đang đăng nhập
function updateUserUI() {
    let userIcon = document.getElementById("userIconBtn");
    if (userIcon) {
        if (currentUser) userIcon.innerHTML = `<i class="fas fa-user-check"></i> ${currentUser.fullname || currentUser.username}`;
        else userIcon.innerHTML = `<i class="fas fa-user-circle"></i>`;
    }

    // Kiểm tra xem nút Admin có trên giao diện HTML của bạn không
    let adminBtn = document.getElementById("adminToggleBtn");
    if (adminBtn) {
        if (currentUser && currentUser.role === "admin") {
            adminBtn.style.display = "inline-block"; // Hiện nút nếu là Admin
        } else {
            adminBtn.style.display = "none"; // Ẩn hoàn toàn nếu là khách hoặc user thường
        }
    }
}

function logout() {
    currentUser = null;
    localStorage.removeItem("yumexpress_remember");
    updateUserUI();
    renderCurrentPage();
    showToast("Đã đăng xuất khỏi YumExpress");
}

// ==========================================================================
// AREA 4: SHOPPING CART & CHECKOUT LOGIC
// (Xử lý Giỏ hàng, Cập nhật số lượng, Xóa món, Đặt hàng)
// ==========================================================================
function loadCart() {
    let saved = localStorage.getItem("yumexpress_cart");
    if (saved) cart = JSON.parse(saved);
    updateCartCount();
}

function updateCartCount() {
    let totalQty = cart.reduce((s, i) => s + i.quantity, 0);
    let cartCountElem = document.getElementById("cartCount");
    if (cartCountElem) cartCountElem.innerText = totalQty;
    localStorage.setItem("yumexpress_cart", JSON.stringify(cart));
}

function addToCart(product, qty = 1) {
    let existing = cart.find(i => i.id === product.id);
    if (existing) existing.quantity += qty;
    else cart.push({ ...product, quantity: qty });
    updateCartCount();
    showToast("Đã thêm " + product.name + " vào giỏ!");
    renderCartSidebar();
}

function renderCartSidebar() {
    let container = document.getElementById("cartItemsList");
    if (!container) return;
    if (cart.length === 0) {
        container.innerHTML = "<p style='text-align:center; color:#999;'>Giỏ hàng trống</p>";
        document.getElementById("cartTotal").innerHTML = "";
        return;
    }
    let html = "";
    let total = 0;
    cart.forEach((item, idx) => {
        let subtotal = item.price * item.quantity;
        total += subtotal;
        html += `<div class="cart-item">
            <div><strong>${item.name}</strong><br>${item.price.toLocaleString()}đ x ${item.quantity}</div>
            <div>${subtotal.toLocaleString()}đ <button class="btn-small btn-outline btn-cart-remove" data-idx="${idx}">Xóa</button></div>
        </div>`;
    });
    container.innerHTML = html;

    let paymentHtml = `
        <div class="payment-methods" style="margin-top: 15px; border-top: 1px solid #eee; padding-top: 12px; text-align: left;">
            <p style="font-weight: bold; margin-bottom: 8px; color: #333; font-size: 0.95rem;">Phương thức thanh toán:</p>
            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; margin-bottom: 6px; font-size: 0.9rem; color: #555;">
                <input type="radio" name="payMethod" value="COD" checked style="cursor: pointer;"> COD: Thanh toán khi nhận hàng
            </label>
            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; font-size: 0.9rem; color: #555;">
                <input type="radio" name="payMethod" value="Online" style="cursor: pointer;"> Online: Chuyển khoản
            </label>
        </div>
    `;
    
    document.getElementById("cartTotal").innerHTML = `<div style="font-weight: bold; font-size: 1.1rem; margin-bottom: 5px;">Tổng: ${total.toLocaleString()}đ</div>` + paymentHtml;
    
    document.querySelectorAll(".btn-cart-remove").forEach(btn => {
        btn.onclick = () => {
            let idx = parseInt(btn.getAttribute("data-idx"));
            cart.splice(idx, 1);
            updateCartCount();
            renderCartSidebar();
            saveToLocal();
        };
    });
}

function checkoutHandler() {
    if (!currentUser) {
        showToast("Vui lòng đăng nhập để thanh toán");
        openAuthModal();
        return;
    }
    if (cart.length === 0) {
        showToast("Giỏ hàng trống");
        return;
    }
    
    let selectedMethod = "COD";
    let checkedRadio = document.querySelector('input[name="payMethod"]:checked');
    if (checkedRadio) {
        selectedMethod = checkedRadio.value;
    }

    let total = cart.reduce((s, i) => s + i.price * i.quantity, 0);
    
    let newOrder = {
        id: Date.now(),
        userId: currentUser.username,
        items: cart.map(i => ({ id: i.id, name: i.name, price: i.price, quantity: i.quantity })),
        total: total,
        status: "Chờ xác nhận",
        paymentMethod: selectedMethod,
        date: new Date().toLocaleString()
    };
    orders.push(newOrder);
    cart = [];
    updateCartCount();
    renderCartSidebar();
    saveToLocal();
    showToast(`Đặt hàng thành công! Phương thức: ${selectedMethod === "COD" ? "Thanh toán khi nhận hàng" : "Chuyển khoản"}`);
    document.getElementById("cartPanel").classList.remove("open");
    renderCurrentPage();
}

// ==========================================================================
// AREA 5: PAGE ROUTING & RENDERING (SPA LOGIC)
// (Điều hướng Single Page và Khởi dựng HTML cho các trang con)
// ==========================================================================
function renderCurrentPage() {
    if (selectedProduct) renderProductDetail(selectedProduct);
    else if (currentPage === "home") renderHomePage();
    else if (currentPage === "intro") renderIntro();
    else if (currentPage === "products") renderProductList();
    else if (currentPage === "news") renderNews();
    else if (currentPage === "contact") renderContact();
    else renderHomePage();
    setActiveNav();
}

function setActiveNav() {
    document.querySelectorAll(".nav-link").forEach(link => {
        link.classList.remove("active");
        if (link.getAttribute("data-page") === currentPage) link.classList.add("active");
    });
}

function createHeroSlideshow() {
    if (slideInterval) clearInterval(slideInterval);
    const heroContainer = document.getElementById("heroSlider");
    if (!heroContainer) return;
    const oldSlides = heroContainer.querySelectorAll('.slide-bg');
    oldSlides.forEach(slide => slide.remove());
    const slides = ["https://images.unsplash.com/photo-1504674900247-0877df9cc836?w=1200","https://images.unsplash.com/photo-1565299624946-b28f40a0ae38?w=1200","https://images.unsplash.com/photo-1579871494447-9811cf80d66c?w=1200","https://images.unsplash.com/photo-1551183053-bf91a1d81141?w=1200"];
    slides.forEach((imgUrl, index) => { const slideDiv = document.createElement('div'); slideDiv.className = 'slide-bg'; slideDiv.style.backgroundImage = `url('${imgUrl}')`; slideDiv.style.opacity = index === 0 ? '1' : '0'; heroContainer.insertBefore(slideDiv, heroContainer.firstChild); });
    let slideIndex = 0; const slideElements = document.querySelectorAll('#heroSlider .slide-bg');
    if (slideElements.length > 1) { slideInterval = setInterval(() => { slideElements[slideIndex].style.opacity = '0'; slideIndex = (slideIndex + 1) % slideElements.length; slideElements[slideIndex].style.opacity = '1'; }, 4000); }
}

function productCardHtml(p) {
    let displayPrice = p.price;  
    let oldPriceHtml = p.oldPrice > 0 ? `<span class="old-price">${p.oldPrice.toLocaleString()}đ</span><span class="discount-badge">-${p.discount}%</span>` : '';
    return `<div class="product-card" onclick="viewProductDetail(${p.id})"><img src="${p.img}" alt="${p.name}" class="product-img" onerror="this.src='https://placehold.co/600x400?text=YumExpress'"><div class="product-info"><div class="product-title">${p.name}</div><div class="product-price">${displayPrice.toLocaleString()}đ ${oldPriceHtml}</div><div class="product-actions" onclick="event.stopPropagation()"><button class="btn btn-primary add-cart" data-id="${p.id}">Thêm giỏ</button><button class="btn btn-outline buy-now" data-id="${p.id}">Mua ngay</button></div></div></div>`;
}

function attachProductEvents() {
    document.querySelectorAll(".add-cart").forEach(btn => { btn.onclick = addCartHandler; });
    document.querySelectorAll(".buy-now").forEach(btn => { btn.onclick = buyNowHandler; });
}
function addCartHandler(e) { let id = parseInt(e.currentTarget.getAttribute("data-id")); let prod = products.find(p => p.id === id); if (prod) addToCart(prod, 1); }
function buyNowHandler(e) { let id = parseInt(e.currentTarget.getAttribute("data-id")); let prod = products.find(p => p.id === id); if (prod) { addToCart(prod, 1); checkoutHandler(); } }

function viewProductDetail(productId) { 
    let prod = products.find(x => x.id === productId);
    if(prod) { selectedProduct = prod; renderCurrentPage(); }
}
function goBack() { selectedProduct = null; renderCurrentPage(); }

function changeQuantity(delta) { let input = document.getElementById("detailQuantity"); if (input) { let newVal = parseInt(input.value) + delta; if (newVal >= 1) input.value = newVal; } }
function addCurrentToCart() { let qty = parseInt(document.getElementById("detailQuantity").value); addToCart(selectedProduct, qty); }
function buyNow() { let qty = parseInt(document.getElementById("detailQuantity").value); addToCart(selectedProduct, qty); checkoutHandler(); }

function renderHomePage() {
    let html = `<div class="hero" id="heroSlider"><div class="hero-overlay"></div><div class="hero-content"><h2>YumExpress - Giao đồ ăn nhanh chóng</h2><p>Sushi tươi ngon, Pizza nóng hổi, giao siêu tốc</p><button class="hero-btn" onclick="currentPage = 'products'; selectedProduct = null; renderCurrentPage();">🍕 Xem thực đơn ngay</button></div></div>
        <div class="container"><h2 class="section-title">🍣 Món nổi bật</h2><div class="product-grid" id="featuredGrid"></div></div>`;
    document.getElementById("appMain").innerHTML = html;
    createHeroSlideshow();
    let featured = products.slice(0, 4);
    let grid = document.getElementById("featuredGrid");
    if (grid) { grid.innerHTML = featured.map(p => productCardHtml(p)).join(""); attachProductEvents(); }
}

function renderProductList() {
    let html = `<div class="container"><div class="filter-bar"><input type="text" id="searchInput" placeholder="🔍 Tìm kiếm sản phẩm..."><select id="categoryFilter"><option value="">Tất cả danh mục</option>${categories.map(c => `<option>${c}</option>`).join("")}</select></div><div id="productGridContainer" class="product-grid"></div></div>`;
    document.getElementById("appMain").innerHTML = html;
    function filterProducts() { let search = document.getElementById("searchInput").value.toLowerCase(); let cat = document.getElementById("categoryFilter").value; let filtered = products.filter(p => p.name.toLowerCase().includes(search) && (cat === "" || p.category === cat)); let container = document.getElementById("productGridContainer"); if (container) { container.innerHTML = filtered.map(p => productCardHtml(p)).join(""); attachProductEvents(); } }
    document.getElementById("searchInput")?.addEventListener("input", filterProducts);
    document.getElementById("categoryFilter")?.addEventListener("change", filterProducts);
    filterProducts();
}

function renderProductDetail(product) {
    let displayPrice = product.price;  
    let oldPriceHtml = product.oldPrice > 0 ? `<span class="old-price">${product.oldPrice.toLocaleString()}đ</span><span class="discount-badge">-${product.discount}%</span>` : '';
    let html = `<div class="container"><button class="back-button btn-outline btn" onclick="goBack()">← Quay lại</button>
        <div class="product-detail"><div class="product-detail-grid">
            <div class="detail-image"><img src="${product.img}" alt="${product.name}" onerror="this.src='https://placehold.co/600x400?text=YumExpress'"></div>
            <div class="detail-info"><h1>${product.name}</h1><div class="detail-price">${displayPrice.toLocaleString()}đ ${oldPriceHtml}</div>
            <div class="detail-category">📂 Danh mục: ${product.category}</div><div class="detail-description">${product.desc}</div>
            <div class="quantity-selector"><button class="quantity-btn" onclick="changeQuantity(-1)">-</button><input type="number" id="detailQuantity" class="quantity-input" value="1" min="1"><button class="quantity-btn" onclick="changeQuantity(1)">+</button></div>
            <div class="product-actions"><button class="btn btn-primary" onclick="addCurrentToCart()">Thêm vào giỏ</button><button class="btn btn-outline" onclick="buyNow()">Mua ngay</button></div></div></div>
            <div class="reviews-section"><h3>⭐ Đánh giá (0)</h3><div class="review-item"><div class="review-rating">★★★★★</div><p>Hãy là người đầu tiên đánh giá sản phẩm này!</p></div></div>
        </div></div>`;
    document.getElementById("appMain").innerHTML = html;
}

function renderIntro() { 
    document.getElementById("appMain").innerHTML = `
        <div class="container page-animation">
            <div class="intro-header" style="text-align: center; margin-bottom: 40px; background: linear-gradient(135deg, #ff9800, #ff5722); color: white; padding: 40px; border-radius: 24px;">
                <h1 style="margin-bottom: 10px; font-size: 2.5rem;">🍕 Về YumExpress</h1>
                <p style="font-size: 1.1rem; opacity: 0.9;">Hành trình mang tinh hoa ẩm thực đến tận cửa nhà bạn chỉ trong 15 phút!</p>
            </div>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 30px; margin-bottom: 40px;">
                <div style="background: white; padding: 30px; border-radius: 24px; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
                    <h3 style="color: #ff5722; margin-bottom: 15px;"><i class="fas fa-bullseye"></i> Sứ mệnh của chúng tôi</h3>
                    <p style="color: #555; line-height: 1.6;">YumExpress ra đời với mục tiêu cách mạng hóa trải nghiệm giao đồ ăn tại Việt Nam. Chúng tôi không chỉ giao thức ăn, chúng tôi mang tới niềm vui, sự tiện lợi và những bữa ăn nóng hổi, an toàn nhất cho mọi gia đình.</p>
                </div>
                <div style="background: white; padding: 30px; border-radius: 24px; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
                    <h3 style="color: #ff5722; margin-bottom: 15px;"><i class="fas fa-shield-alt"></i> Cam kết chất lượng</h3>
                    <p style="color: #555; line-height: 1.6;">100% đối tác nhà hàng của YumExpress đều đạt chứng nhận Vệ sinh an toàn thực phẩm. Đội ngũ tài xế được đào tạo chuyên nghiệp với thùng giữ nhiệt tiêu chuẩn, đảm bảo Pizza luôn giòn rụm và Sushi luôn tươi ngon khi tới tay bạn.</p>
                </div>
            </div>
            <div style="background: white; padding: 40px; border-radius: 24px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); margin-bottom: 40px; text-align: center;">
                <h3 style="color: #333; margin-bottom: 30px;">📊 YumExpress Qua Những Con Số</h3>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 20px;">
                    <div><h2 style="color: #ff9800; font-size: 2.5rem; margin-bottom: 5px;">500+</h2><p style="color: #777;">Đối tác nhà hàng</p></div>
                    <div><h2 style="color: #ff9800; font-size: 2.5rem; margin-bottom: 5px;">10k+</h2><p style="color: #777;">Đơn hàng mỗi ngày</p></div>
                    <div><h2 style="color: #ff9800; font-size: 2.5rem; margin-bottom: 5px;">15 Phút</h2><p style="color: #777;">Thời gian giao trung bình</p></div>
                    <div><h2 style="color: #ff9800; font-size: 2.5rem; margin-bottom: 5px;">99%</h2><p style="color: #777;">Khách hàng hài lòng</p></div>
                </div>
            </div>
        </div>`; 
}

function renderNews() { 
    const articles = [
        { title: "Siêu hội Pizza: Mua 1 Tặng 1 duy nhất vào ngày thứ Tư", date: "28/05/2026", img: "https://images.unsplash.com/photo-1513104890138-7c749659a591?w=500", summary: "Tin vui cho các tín đồ đạo Pizza! Duy nhất thứ tư tuần này, khi đặt bất kỳ Pizza size L tại YumExpress, bạn sẽ được tặng ngay một Pizza đồng size..." },
        { title: "YumExpress chính thức ra mắt dịch vụ giao hàng 'Siêu tốc 15 phút'", date: "25/05/2026", img: "https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?w=500", summary: "Nhờ tối ưu hóa thuật toán định tuyến and mở rộng đội ngũ tài xế công nghệ, YumExpress cam kết các đơn hàng trong bán kính 3km sẽ được giao chỉ trong 15 phút." },
        { title: "Mách bạn 5 món ăn giải nhiệt cực đỉnh cho mùa hè oi bức", date: "20/05/2026", img: "https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=500", summary: "Nắng nóng đỉnh điểm khiến bạn mệt mỏi? Hãy cùng YumExpress điểm qua danh sách 5 món salad và sinh tố thanh mát giúp thanh lọc cơ thể ngay tức thì nhé!" }
    ];
    let articlesHtml = articles.map(art => `
        <div style="background: white; border-radius: 20px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.05); display: flex; flex-direction: column;">
            <img src="${art.img}" alt="${art.title}" style="width: 100%; height: 200px; object-fit: cover;">
            <div style="padding: 20px; flex-grow: 1; display: flex; flex-direction: column; justify-content: space-between;">
                <div>
                    <span style="color: #999; font-size: 0.85rem;"><i class="far fa-calendar-alt"></i> ${art.date}</span>
                    <h4 style="margin: 10px 0; color: #333; line-height: 1.4; font-size: 1.1rem;">${art.title}</h4>
                    <p style="color: #666; font-size: 0.9rem; line-height: 1.5; margin-bottom: 15px;">${art.summary}</p>
                </div>
                <button class="btn btn-outline" style="width: 100%; padding: 8px;" onclick="alert('Tính năng đọc chi tiết bài viết đang được phát triển!')">Đọc tiếp</button>
            </div>
        </div>`).join("");
    document.getElementById("appMain").innerHTML = `
        <div class="container page-animation">
            <h2 class="section-title">📰 Tin tức & Khuyến mãi Hot</h2>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 30px; margin-bottom: 40px;">${articlesHtml}</div>
        </div>`; 
}

function renderContact() { 
    document.getElementById("appMain").innerHTML = `
        <div class="container page-animation">
            <h2 class="section-title">📞 Liên hệ với YumExpress</h2>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 40px; margin-bottom: 40px;">
                <div style="background: white; padding: 30px; border-radius: 24px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); display: flex; flex-direction: column; justify-content: space-between;">
                    <div>
                        <h3 style="color: #333; margin-bottom: 20px;">Thông tin chi tiết</h3>
                        <div style="margin-bottom: 20px; display: flex; align-items: center; gap: 15px;">
                            <div style="background: #fff3e0; color: #ff9800; width: 45px; height: 45px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.2rem;"><i class="fas fa-map-marker-alt"></i></div>
                            <div><strong>Địa chỉ:</strong><br><span style="color:#555;">123 Đường Ẩm Thực, Quận 1, TP.HCM</span></div>
                        </div>
                        <div style="margin-bottom: 20px; display: flex; align-items: center; gap: 15px;">
                            <div style="background: #fff3e0; color: #ff9800; width: 45px; height: 45px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.2rem;"><i class="fas fa-phone-alt"></i></div>
                            <div><strong>Hotline:</strong><br><span style="color:#555;">1900 1234 (Tổng đài hỗ trợ 24/7)</span></div>
                        </div>
                        <div style="margin-bottom: 20px; display: flex; align-items: center; gap: 15px;">
                            <div style="background: #fff3e0; color: #ff9800; width: 45px; height: 45px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.2rem;"><i class="fas fa-envelope"></i></div>
                            <div><strong>Email:</strong><br><span style="color:#555;">support@yumexpress.vn</span></div>
                        </div>
                    </div>
                    <div style="border-top: 1px solid #eee; padding-top: 20px; margin-top: 20px;">
                        <h4 style="margin-bottom: 10px; color:#333;">Theo dõi chúng tôi</h4>
                        <div style="display: flex; gap: 15px; font-size: 1.5rem;">
                            <a href="#" style="color: #3b5998;"><i class="fab fa-facebook"></i></a>
                            <a href="#" style="color: #e1306c;"><i class="fab fa-instagram"></i></a>
                            <a href="#" style="color: #ff0000;"><i class="fab fa-youtube"></i></a>
                        </div>
                    </div>
                </div>
                <div style="background: white; padding: 30px; border-radius: 24px; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
                    <h3 style="color: #333; margin-bottom: 20px;">Gửi góp ý cho chúng tôi</h3>
                    <form id="contactForm" onsubmit="event.preventDefault(); showToast('✅ Gửi phản hồi thành công! Cảm ơn bạn.'); document.getElementById('contactForm').reset();">
                        <div style="margin-bottom: 15px;">
                            <label style="display:block; margin-bottom: 5px; font-weight: bold; color: #555;">Họ và tên</label>
                            <input type="text" required style="width:100%; padding: 12px; border: 1px solid #ddd; border-radius: 12px;" placeholder="Nhập tên của bạn...">
                        </div>
                        <div style="margin-bottom: 15px;">
                            <label style="display:block; margin-bottom: 5px; font-weight: bold; color: #555;">Email / Số điện thoại</label>
                            <input type="text" required style="width:100%; padding: 12px; border: 1px solid #ddd; border-radius: 12px;" placeholder="Nhập phương thức liên lạc...">
                        </div>
                        <div style="margin-bottom: 20px;">
                            <label style="display:block; margin-bottom: 5px; font-weight: bold; color: #555;">Nội dung lời nhắn</label>
                            <textarea required rows="4" style="width:100%; padding: 12px; border: 1px solid #ddd; border-radius: 12px; resize: none;" placeholder="Chúng tôi có thể giúp gì cho bạn?"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary" style="width: 100%; padding: 12px;">✉️ Gửi lời nhắn</button>
                    </form>
                </div>
            </div>
        </div>`; 
}

window.viewProductDetail = viewProductDetail;
window.goBack = goBack;
window.changeQuantity = changeQuantity;
window.addCurrentToCart = addCurrentToCart;
window.buyNow = buyNow;

// ==========================================================================
// AREA 6: ADMIN CONTROL PANEL
// (Giao diện và Logic CRUD Sản phẩm, Quản lý đơn hàng, Thống kê doanh thu)
// ==========================================================================
let isAdminMode = false;
let adminActiveTab = "products";

// ĐÃ CẬP NHẬT: Thêm bước kiểm tra quyền đăng nhập tối cao trước khi mở giao diện quản trị
function toggleAdminMode() {
    if (!currentUser || currentUser.role !== "admin") {
        showToast("❌ Bạn không có quyền truy cập vùng Quản trị!");
        return;
    }

    isAdminMode = !isAdminMode;
    if (isAdminMode) {
        if (slideInterval) clearInterval(slideInterval);
        slideInterval = null;
        document.querySelector(".main-header").style.display = "none";
        document.querySelector("footer").style.display = "none";
        document.getElementById("appMain").innerHTML = `<div style="display:flex; width:100%; min-height:100vh;">
            <div class="admin-sidebar">
                <h3>🍔 YumExpress Admin</h3>
                <ul id="adminMenu">
                    <li data-tab="products">📦 Sản phẩm</li>
                    <li data-tab="categories">🏷️ Danh mục</li>
                    <li data-tab="users">👥 Người dùng</li>
                    <li data-tab="orders">📦 Đơn hàng</li>
                    <li data-tab="stats">📊 Thống kê</li>
                    <li id="exitAdminBtn" style="color: #ff9800; margin-top: 20px;">🚪 Thoát Admin</li>
                </ul>
            </div>
            <div class="admin-content" id="adminContent"></div>
        </div>`;
        renderAdminPanel(adminActiveTab);
        document.querySelectorAll("#adminMenu li").forEach(li => { 
            if (li.id !== "exitAdminBtn") {
                li.onclick = () => { adminActiveTab = li.getAttribute("data-tab"); renderAdminPanel(adminActiveTab); };
            }
        });
        document.getElementById("exitAdminBtn").onclick = () => toggleAdminMode();
    } else {
        if (slideInterval) clearInterval(slideInterval);
        slideInterval = null;
        document.querySelector(".main-header").style.display = "block";
        document.querySelector("footer").style.display = "block";
        renderCurrentPage();
    }
}

function renderAdminPanel(tab) {
    let container = document.getElementById("adminContent");
    if (!container) return;
    if (tab === "products") {
        let html = `<h2>Quản lý sản phẩm</h2><button id="addProductBtn" class="btn btn-primary" style="margin-bottom:20px;">+ Thêm sản phẩm</button>
        <table class="admin-table"><thead><tr><th>ID</th><th>Ảnh</th><th>Tên</th><th>Giá</th><th>Danh mục</th><th>Hành động</th></tr></thead><tbody>
        ${products.map(p => `<tr><td>${p.id}</td><td><img src="${p.img}" width="50" height="40" style="object-fit:cover; border-radius:8px;"></td><td>${p.name}</td><td>${p.price.toLocaleString()}₫</td><td>${p.category}</td>
        <td><button class="btn-small btn-primary edit-prod" data-id="${p.id}">Sửa</button><button class="btn-small btn-outline del-prod" data-id="${p.id}">Xóa</button></td></tr>`).join("")}
        </tbody></table>`;
        container.innerHTML = html;
        
        document.getElementById("addProductBtn")?.addEventListener("click", () => { let newName = prompt("Tên sản phẩm mới:"); if (newName) { products.push({ id: Date.now(), name: newName, price: 50000, oldPrice: 0, discount: 0, category: "Món chính", img: "https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=500", desc: "Mô tả sản phẩm mới" }); saveToLocal(); renderAdminPanel("products"); } });
        document.querySelectorAll(".edit-prod").forEach(btn => { btn.onclick = () => { let id = parseInt(btn.dataset.id); let p = products.find(x => x.id === id); if (p) { p.name = prompt("Tên mới:", p.name) || p.name; p.price = parseInt(prompt("Giá mới (VNĐ):", p.price)) || p.price; saveToLocal(); renderAdminPanel("products"); } }; });
        document.querySelectorAll(".del-prod").forEach(btn => { btn.onclick = () => { let id = parseInt(btn.dataset.id); products = products.filter(p => p.id !== id); categories = [...new Set(products.map(p => p.category))]; saveToLocal(); renderAdminPanel("products"); }; });
    } else if (tab === "orders") {
        container.innerHTML = `<h2>Quản lý đơn hàng</h2><table class="admin-table"><thead><tr><th>ID</th><th>User</th><th>Tổng tiền</th><th>Trạng thái</th><th>PT thanh toán</th><th>Hành động</th></tr></thead><tbody>${orders.map(o => `<tr><td>${o.id}</td><td>${o.userId}</td><td>${o.total.toLocaleString()}₫</td>
            <td><select class="status-update" data-id="${o.id}"><option ${o.status === "Chờ xác nhận" ? "selected" : ""}>Chờ xác nhận</option><option ${o.status === "Đang giao" ? "selected" : ""}>Đang giao</option><option ${o.status === "Hoàn thành" ? "selected" : ""}>Hoàn thành</option></select></td><td>${o.paymentMethod}</td>
            <td><button class="btn-small update-status" data-id="${o.id}">Cập nhật</button></td></tr>`).join("")}</tbody></table>`;
        document.querySelectorAll(".update-status").forEach(btn => { btn.onclick = () => { let id = parseInt(btn.dataset.id); let select = document.querySelector(`.status-update[data-id="${id}"]`); let order = orders.find(o => o.id === id); if (order && select) order.status = select.value; saveToLocal(); renderAdminPanel("orders"); }; });
    } else if (tab === "users") { container.innerHTML = `<h2>Người dùng</h2><ul style="background:white;padding:20px;border-radius:16px;">${users.map(u => `<li>👤 ${u.fullname || u.username} - ${u.email} - ${u.phone || ''} [Quyền: ${u.role}]</li>`).join("")}</ul>`; }
    else if (tab === "categories") { let cats = [...new Set(products.map(p => p.category))]; container.innerHTML = `<h2>Danh mục</h2><ul>${cats.map(c => `<li>📁 ${c}</li>`).join("")}</ul><input id="newCat" placeholder="Tên mới"><button id="addCat" class="btn btn-primary">Thêm</button>`; document.getElementById("addCat")?.addEventListener("click", () => { let val = document.getElementById("newCat").value; if (val && !categories.includes(val)) categories.push(val); renderAdminPanel("categories"); }); }
    else if (tab === "stats") { let totalRevenue = orders.reduce((s, o) => s + o.total, 0); container.innerHTML = `<h2>Thống kê</h2><div style="background:white;padding:20px;border-radius:16px;"><p>📊 Đơn hàng: ${orders.length}</p><p>💰 Doanh thu: ${totalRevenue.toLocaleString()}₫</p><p>👥 Người dùng: ${users.length}</p><p>🍔 Sản phẩm: ${products.length}</p></div>`; }
}

// ==========================================================================
// AREA 7: EVENT LISTENERS & APP STARTUP
// (Lắng nghe sự kiện click chuột và kích hoạt ứng dụng khi load trang)
// ==========================================================================
function initEvent() {
    document.getElementById("cartIconBtn").onclick = () => { renderCartSidebar(); document.getElementById("cartPanel").classList.add("open"); };
    document.getElementById("closeCart").onclick = () => document.getElementById("cartPanel").classList.remove("open");
    document.getElementById("userIconBtn").onclick = () => { if (currentUser) { if (confirm(`Bạn muốn đăng xuất tài khoản chứ?`)) logout(); } else openAuthModal(); };
    
    // Gán sự kiện cho nút chuyển chế độ Admin
    let adminBtn = document.getElementById("adminToggleBtn");
    if (adminBtn) adminBtn.onclick = () => toggleAdminMode();
    
    document.getElementById("checkoutBtn").onclick = checkoutHandler;
    
    document.getElementById("closeModal").onclick = closeAuthModal;
    document.getElementById("submitAuthBtn").onclick = handleAuth;
    document.getElementById("registerBtn").onclick = handleRegister;
    document.getElementById("forgotPasswordBtn").onclick = () => { closeAuthModal(); document.getElementById("forgotModal").style.display = "flex"; };
    document.getElementById("closeForgotModal").onclick = () => document.getElementById("forgotModal").style.display = "none";
    document.getElementById("sendResetBtn").onclick = handleForgotPassword;
    
    document.getElementById("termsLink").onclick = (e) => { e.preventDefault(); document.getElementById("termsModal").style.display = "flex"; };
    document.getElementById("closeTermsModal").onclick = () => document.getElementById("termsModal").style.display = "none";
    document.getElementById("agreeTermsBtn").onclick = () => { document.getElementById("agreeTerms").checked = true; document.getElementById("termsModal").style.display = "none"; showToast("✅ Đã đồng ý điều khoản!"); };
    
    document.querySelectorAll(".auth-tab").forEach(btn => { btn.onclick = () => { document.querySelectorAll(".auth-tab").forEach(b => b.classList.remove("active")); btn.classList.add("active"); if (btn.getAttribute("data-tab") === "login") { document.getElementById("loginForm").classList.add("active"); document.getElementById("registerForm").classList.remove("active"); } else { document.getElementById("loginForm").classList.remove("active"); document.getElementById("registerForm").classList.add("active"); } }; });
    
    document.querySelectorAll(".toggle-password").forEach(icon => { icon.onclick = function() { const target = document.getElementById(this.getAttribute("data-target")); if (target.type === "password") { target.type = "text"; this.classList.remove("fa-eye-slash"); this.classList.add("fa-eye"); } else { target.type = "password"; this.classList.remove("fa-eye"); this.classList.add("fa-eye-slash"); } }; });
    
    document.querySelectorAll(".nav-link").forEach(link => { link.onclick = (e) => { e.preventDefault(); currentPage = link.getAttribute("data-page"); selectedProduct = null; renderCurrentPage(); }; });
    
    window.onclick = (e) => { if (e.target.classList.contains("modal")) e.target.style.display = "none"; };
    
    // Khởi động nạp dữ liệu từ local storage
    loadData(); loadCart(); renderCurrentPage(); updateUserUI();
    
    const remembered = localStorage.getItem("yumexpress_remember");
    if (remembered) { try { const { username, password } = JSON.parse(remembered); const user = users.find(u => u.username === username && u.password === password); if (user) { currentUser = user; updateUserUI(); renderCurrentPage(); } } catch(e) {} }
}

initEvent();