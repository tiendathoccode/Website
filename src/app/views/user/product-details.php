<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?php echo htmlspecialchars(
      $product["product_name"],
  ); ?> - Aurrelia</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
  <link rel="stylesheet" href="/assets/css/style.css" />
  <link rel="stylesheet" href="/assets/css/product_details.css" />
</head>
<body class="bg-cream">

<nav class="navbar navbar-expand-lg py-3 sticky-top border-bottom shadow-sm" style="background-color: #fdfbf7; z-index: 1020;">
    <div class="container-fluid px-4">
        <a class="navbar-brand fs-4 fw-bold gold-text" href="/index.php?page=home" style="font-family: 'Times New Roman', serif;">AURRELIA</a>

        <!-- ICONS: luôn hiện kể cả mobile, đặt TRƯỚC nút toggler -->
        <div class="d-flex gap-3 align-items-center me-2 order-lg-last">
            <div class="d-flex align-items-center" style="position: relative;">
                <input type="text" id="navbarSearchInput" placeholder="Tìm kiếm sản phẩm..." style="
                    display: <?php echo isset($_GET['search']) && trim($_GET['search']) !== '' ? 'block' : 'none'; ?>;
                    border: none;
                    border-bottom: 1px solid #c8a165;
                    background: transparent;
                    outline: none;
                    padding: 2px 8px;
                    font-size: 13px;
                    width: 150px;
                    margin-right: 8px;
                    transition: all 0.3s ease;
                " value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>" />
                <a href="#" class="text-dark fs-6" id="navbarSearchBtn"><i class="fas fa-search"></i></a>
            </div>
            <a href="#" class="text-dark fs-6"><i class="far fa-heart"></i></a>
            <a href="/index.php?page=gio_hang" class="text-dark fs-6 position-relative" id="headerCartBtn">
                <i class="fas fa-shopping-bag"></i>
                <span id="headerCartBadge" style="
                    display:none;
                    position:absolute;
                    top:-8px; right:-10px;
                    background:#c8a165; color:#fff;
                    font-size:10px; font-weight:700;
                    min-width:17px; height:17px;
                    border-radius:50%;
                    align-items:center; justify-content:center;
                    padding:0 3px;
                ">0</span>
            </a>
            <div class="position-relative" id="userDropdownWrapper">
                <a href="#" class="text-dark fs-6" id="userIconBtn" onclick="toggleUserDropdown(event)">
                    <i class="far fa-user"></i>
                </a>
                <div id="userDropdownMenu" style="
                    display: none;
                    position: absolute;
                    top: calc(100% + 12px);
                    right: 0;
                    background: white;
                    border: 1px solid #eee;
                    border-radius: 8px;
                    box-shadow: 0 8px 24px rgba(0,0,0,0.1);
                    min-width: 200px;
                    z-index: 9999;
                    overflow: hidden;
                ">
                    <?php if (
                        isset($_SESSION["user_logged_in"]) &&
                        $_SESSION["user_logged_in"] === true
                    ): ?>
                        <div style="padding: 14px 18px; border-bottom: 1px solid #f0ece4; background: #fcf9f2;">
                            <p style="margin:0; font-size:12px; color:#888;">Xin chào,</p>
                            <p style="margin:0; font-weight:bold; font-size:14px; color:#333;">
                                <?php echo htmlspecialchars(
                                    $_SESSION["user_name"],
                                ); ?>
                            </p>
                        </div>
                        <a href="/index.php?page=profile" style="display:flex; align-items:center; gap:10px; padding:12px 18px; text-decoration:none; color:#333; font-size:13px; border-bottom:1px solid #f5f5f5;">
                            <i class="far fa-user" style="color:#bfa15f; width:16px;"></i> Thông tin cá nhân
                        </a>
                        <?php if (isset($_SESSION["user_role"]) && $_SESSION["user_role"] === "admin"): ?>
                            <a href="/index.php?page=admin_dashboard" style="display:flex; align-items:center; gap:10px; padding:12px 18px; text-decoration:none; color:#333; font-size:13px; border-bottom:1px solid #f5f5f5;">
                                <i class="fas fa-user-shield" style="color:#bfa15f; width:16px;"></i> Trang quản trị (Admin)
                            </a>
                        <?php endif; ?>
                        <a href="/index.php?page=don_hang" style="display:flex; align-items:center; gap:10px; padding:12px 18px; text-decoration:none; color:#333; font-size:13px; border-bottom:1px solid #f5f5f5;">
                            <i class="fas fa-box" style="color:#bfa15f; width:16px;"></i> Đơn hàng của tôi
                        </a>
                        <a href="/index.php?page=change_password" style="display:flex; align-items:center; gap:10px; padding:12px 18px; text-decoration:none; color:#333; font-size:13px; border-bottom:1px solid #f5f5f5;">
                            <i class="fas fa-lock" style="color:#bfa15f; width:16px;"></i> Đổi mật khẩu
                        </a>
                        <a href="/index.php?page=logout" style="display:flex; align-items:center; gap:10px; padding:12px 18px; text-decoration:none; color:#c0392b; font-size:13px;">
                            <i class="fas fa-sign-out-alt" style="color:#c0392b; width:16px;"></i> Đăng xuất
                        </a>
                    <?php else: ?>
                        <a href="/index.php?page=login" style="display:flex; align-items:center; gap:10px; padding:12px 18px; text-decoration:none; color:#333; font-size:13px; border-bottom:1px solid #f5f5f5;">
                            <i class="fas fa-sign-in-alt" style="color:#bfa15f; width:16px;"></i> Đăng nhập
                        </a>
                        <a href="/index.php?page=register" style="display:flex; align-items:center; gap:10px; padding:12px 18px; text-decoration:none; color:#333; font-size:13px;">
                            <i class="fas fa-user-plus" style="color:#bfa15f; width:16px;"></i> Tạo tài khoản
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- NÚT HAMBURGER -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- MENU: chỉ hiện khi mở collapse trên mobile -->
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mx-auto text-uppercase" style="font-size: 13px; letter-spacing: 1px;">
                <li class="nav-item"><a class="nav-link px-3" href="/index.php?page=home">Trang Sức</a></li>
                <li class="nav-item"><a class="nav-link px-3" href="/index.php?page=home&category_id=1">Trang Sức Cao Cấp</a></li>
                <li class="nav-item"><a class="nav-link px-3" href="/index.php?page=home#about-us-section">Về Chúng Tôi</a></li>
                <li class="nav-item"><a class="nav-link px-3" href="/index.php?page=home#contact-section">Liên Hệ</a></li>
            </ul>
        </div>
    </div>
</nav>

    <section id="productSection">

        <div id="productGallery">
                    <div id="mainImageWrapper" style="position:relative;">
                        <button id="prevBtn" onclick="slideCarousel(-1)" style="
                            position:absolute; left:10px; top:50%; transform:translateY(-50%);
                            background:rgba(255,255,255,0.85); border:none; border-radius:50%;
                            width:36px; height:36px; font-size:18px; cursor:pointer;
                            z-index:10; box-shadow:0 2px 8px rgba(0,0,0,0.15);
                            display:none; align-items:center; justify-content:center;
                        ">‹</button>

                        <img src="/<?php echo htmlspecialchars(
                            $product["main_image"],
                        ); ?>"
                             alt="<?php echo htmlspecialchars(
                                 $product["product_name"],
                             ); ?>"
                             id="mainProductImage" class="main-product-img"
                             style="transition: opacity 0.3s ease;"/>

                        <button id="nextBtn" onclick="slideCarousel(1)" style="
                            position:absolute; right:10px; top:50%; transform:translateY(-50%);
                            background:rgba(255,255,255,0.85); border:none; border-radius:50%;
                            width:36px; height:36px; font-size:18px; cursor:pointer;
                            z-index:10; box-shadow:0 2px 8px rgba(0,0,0,0.15);
                            display:none; align-items:center; justify-content:center;
                        ">›</button>
                    </div>

                    <div id="thumbnailStrip">
                        <button class="thumb-btn thumb-btn--active"
                                data-src="/<?php echo htmlspecialchars(
                                    $product["main_image"],
                                ); ?>"
                                data-alt="<?php echo htmlspecialchars(
                                    $product["product_name"],
                                ); ?>">
                            <img src="/<?php echo htmlspecialchars(
                                $product["main_image"],
                            ); ?>" alt="Ảnh chính"/>
                        </button>
                        <?php foreach ($extraImages as $img): ?>
                        <button class="thumb-btn"
                                data-src="/<?php echo htmlspecialchars(
                                    $img["image_url"],
                                ); ?>"
                                data-alt="<?php echo htmlspecialchars(
                                    $product["product_name"],
                                ); ?>">
                            <img src="/<?php echo htmlspecialchars(
                                $img["image_url"],
                            ); ?>" alt="Ảnh phụ"/>
                        </button>
                        <?php endforeach; ?>
                    </div>
                </div>
          <div id="productInfo">
              <h1 id="productName" class="product-title" style="font-family: 'Times New Roman', serif;">
                  <?php echo htmlspecialchars($product["product_name"]); ?>
              </h1>

              <p id="productPrice" class="product-price">
                  <?php if ($product["sale_price"] > 0): ?>
                  <span style="color:#c8a165;">
                          <?php echo number_format(
                              $product["sale_price"],
                              0,
                              ",",
                              ".",
                          ); ?>₫
                      </span>
                      <span style="text-decoration:line-through; color:#999; font-size:16px; margin-left:10px;">
                          <?php echo number_format(
                              $product["price"],
                              0,
                              ",",
                              ".",
                          ); ?>₫
                      </span>
                  <?php else: ?>
                      <?php echo number_format(
                          $product["price"],
                          0,
                          ",",
                          ".",
                      ); ?>₫
                  <?php endif; ?>
              </p>

              <p id="productDescription" class="product-desc">
                  <?php echo htmlspecialchars($product["description"]); ?>
              </p>

              <!-- Thông tin nhanh -->
              <div style="background:#fcf9f2; border-radius:8px; padding:16px 20px; margin:20px 0; font-size:13px;">
                  <div style="display:flex; justify-content:space-between; padding:8px 0; border-bottom:1px solid #f0ece4;">
                      <span style="color:#888;">Tình trạng</span>
                      <span style="font-weight:bold; color:<?php echo $product[
                          "stock_quantity"
                      ] > 0
                          ? "#27ae60"
                          : "#c0392b"; ?>;">
                          <?php echo $product["stock_quantity"] > 0
                              ? "✓ Còn hàng (" .
                                  $product["stock_quantity"] .
                                  " sản phẩm)"
                              : "✗ Hết hàng"; ?>
                      </span>
                  </div>
                  <div style="display:flex; justify-content:space-between; padding:8px 0; border-bottom:1px solid #f0ece4;">
                      <span style="color:#888;">Danh mục</span>
                      <span style="font-weight:bold;"><?php echo htmlspecialchars(
                          $product["category_name"],
                      ); ?></span>
                  </div>
                  <div style="display:flex; justify-content:space-between; padding:8px 0; border-bottom:1px solid #f0ece4;">
                      <span style="color:#888;">Mã sản phẩm</span>
                      <span style="font-weight:bold; font-family:monospace;">
                          #<?php echo str_pad(
                              $product["product_id"],
                              4,
                              "0",
                              STR_PAD_LEFT,
                          ); ?>
                      </span>
                  </div>
                  <div style="display:flex; justify-content:space-between; padding:8px 0;">
                      <span style="color:#888;">Cập nhật</span>
                      <span style="font-weight:bold;">
                          <?php echo date(
                              "d/m/Y",
                              strtotime($product["updated_at"]),
                          ); ?>
                      </span>
                  </div>
              </div>

              <!-- Nút hành động -->
              <div id="productActions">
                  <button id="btnAddToCart" class="btn-primary w-100 mb-2">
                      <i class="fas fa-shopping-bag me-2"></i>THÊM VÀO GIỎ HÀNG
                  </button>
                  <!-- Thêm nút này -->
                  <a href="/index.php?page=thanh_toan"
                     id="btnBuyNow"
                     class="btn-primary w-100 mb-2"
                     style="display:block; text-align:center; text-decoration:none; background-color:#333; padding:12px;">
                      <i class="fas fa-bolt me-2"></i>MUA NGAY
                  </a>
                  <button id="btnAddToWishlist" class="btn-secondary w-100 bg-transparent border-0 d-flex align-items-center justify-content-center gap-2">
                      <i class="far fa-heart"></i> Lưu vào yêu thích
                  </button>
              </div>

              <!-- Accordion chi tiết -->
              <div id="productAccordion">
                  <div class="accordion-item">
                      <button class="accordion-trigger" data-target="panelDetails">
                          CHI TIẾT & KÍCH THƯỚC
                          <span class="accordion-icon">+</span>
                      </button>
                      <div class="accordion-panel" id="panelDetails">
                          <p><?php echo nl2br(
                              htmlspecialchars($product["description"]),
                          ); ?></p>
                          <p>Tồn kho: <?php echo $product[
                              "stock_quantity"
                          ]; ?> sản phẩm</p>
                      </div>
                  </div>
                  <div class="accordion-item">
                      <button class="accordion-trigger" data-target="panelShipping">
                          VẬN CHUYỂN & ĐỔI TRẢ
                          <span class="accordion-icon">+</span>
                      </button>
                      <div class="accordion-panel" id="panelShipping">
                          <p>Miễn phí vận chuyển toàn quốc. Giao hàng trong 3–5 ngày làm việc. Đổi trả trong vòng 14 ngày kể từ ngày nhận hàng với sản phẩm chưa qua sử dụng.</p>
                      </div>
                  </div>
              </div>


      </div>
    </section>
  </main>

  <aside id="cartDrawer" class="cart-drawer" aria-label="Giỏ hàng" aria-hidden="true">
    <div id="cartDrawerInner">
      <div id="cartDrawerHeader">
        <h2 id="cartDrawerTitle">Giỏ Hàng Của Bạn</h2>
        <button id="btnCloseCart" aria-label="Đóng giỏ hàng" style="background:none; border:none; font-size:20px;">✕</button>
      </div>

      <div id="cartItemList">
        </div>

      <div id="cartDrawerFooter">
        <div id="cartSubtotalRow" class="d-flex justify-content-between mb-2">
          <span>Tạm tính</span>
          <span id="cartSubtotalAmount" class="fw-bold">3.450.000₫</span>
        </div>
        <p class="cart-tax-note text-muted" style="font-size: 12px;">Phí vận chuyển và thuế sẽ được tính khi thanh toán.</p>
        <button id="btnProceedToCheckout" class="btn-primary btn-checkout w-100 py-2">TIẾN HÀNH THANH TOÁN</button>
        <a href="/index.php?page=cart" style="
            display:block; text-align:center; margin-top:12px;
            font-size:11px; letter-spacing:1px; color:#7a7670;
            text-decoration:underline; text-underline-offset:3px;
        ">Xem giỏ hàng đầy đủ →</a>
      </div>
    </div>
  </aside>
  <div id="cartOverlay" class="cart-overlay"></div>

  <footer class="pt-5 pb-3 border-top mt-5" style="background-color: #fcf9f2;">
      <div class="container-fluid px-5">
          <div class="row">
              <div class="col-md-4 mb-4">
                  <h4 class="fw-bold gold-text mb-3" style="font-family: 'Times New Roman', serif;">AURRELIA</h4>
                  <p class="text-muted small w-75">Điểm đến của những tuyệt tác trang sức thủ công. Kiến tạo vẻ đẹp vượt thời gian.</p>
                  <p class="small text-muted mt-4">&copy; 2026 Aurrelia. Bản quyền thuộc về Nhóm 6.</p>
              </div>
              <div class="col-md-2 mb-4">
                  <h6 class="text-uppercase mb-3 fw-bold" style="font-size: 12px;">Công Ty</h6>
                  <ul class="list-unstyled text-muted small">
                      <li class="mb-2"><a href="#" class="text-muted text-decoration-none">Về Chúng Tôi</a></li>
                      <li class="mb-2"><a href="#" class="text-muted text-decoration-none">Bộ Sưu Tập</a></li>
                      <li class="mb-2"><a href="#" class="text-muted text-decoration-none">Tuyển Dụng</a></li>
                  </ul>
              </div>
              <div class="col-md-3 mb-4">
                  <h6 class="text-uppercase mb-3 fw-bold" style="font-size: 12px;">Hỗ Trợ Khách Hàng</h6>
                  <ul class="list-unstyled text-muted small">
                      <li class="mb-2"><a href="#" class="text-muted text-decoration-none">Giao Hàng & Đổi Trả</a></li>
                      <li class="mb-2"><a href="#" class="text-muted text-decoration-none">Chính Sách Bảo Mật</a></li>
                      <li class="mb-2"><a href="#" class="text-muted text-decoration-none">Liên Hệ</a></li>
                  </ul>
              </div>
              <div class="col-md-3 mb-4">
                  <h6 class="text-uppercase mb-3 fw-bold" style="font-size: 12px;">Kết Nối</h6>
                  <ul class="list-unstyled text-muted small">
                      <li class="mb-2"><a href="#" class="text-muted text-decoration-none">Facebook</a></li>
                      <li class="mb-2"><a href="#" class="text-muted text-decoration-none">Instagram</a></li>
                      <li class="mb-2"><a href="#" class="text-muted text-decoration-none">Pinterest</a></li>
                  </ul>
              </div>
          </div>
      </div>
  </footer>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
  // ===== CAROUSEL =====
  const allImages = [
      { src: '/<?php echo $product[
          "main_image"
      ]; ?>', alt: '<?php echo addslashes($product["product_name"]); ?>' },
      <?php foreach ($extraImages as $img): ?>
      { src: '/<?php echo htmlspecialchars(
          $img["image_url"],
      ); ?>', alt: '<?php echo addslashes($product["product_name"]); ?>' },
      <?php endforeach; ?>
  ];

  let currentIndex = 0;

  function slideCarousel(dir) {
      currentIndex = (currentIndex + dir + allImages.length) % allImages.length;
      goToSlide(currentIndex);
  }

  function goToSlide(index) {
      currentIndex = index;
      const mainImg = document.getElementById('mainProductImage');

      // Fade effect
      mainImg.style.opacity = '0';
      setTimeout(() => {
          mainImg.src = allImages[index].src;
          mainImg.alt = allImages[index].alt;
          mainImg.style.opacity = '1';
      }, 150);

      // Cập nhật thumbnail active
      document.querySelectorAll('.thumb-btn').forEach((btn, i) => {
          btn.classList.toggle('thumb-btn--active', i === index);
      });
  }

  // Gắn sự kiện click thumbnail - dùng goToSlide để đồng bộ currentIndex
  thumbnailStrip.addEventListener('click', (e) => {
      const btn = e.target.closest('.thumb-btn');
      if (!btn) return;
      const idx = [...thumbnailStrip.querySelectorAll('.thumb-btn')].indexOf(btn);
      if (idx !== -1) goToSlide(idx);
  });

  // Hiện mũi tên nếu có nhiều hơn 1 ảnh
  if (allImages.length > 1) {
      document.getElementById('prevBtn').style.display = 'flex';
      document.getElementById('nextBtn').style.display = 'flex';
  }

  // Swipe trên mobile
  let touchStartX = 0;
  document.getElementById('mainImageWrapper').addEventListener('touchstart', e => {
      touchStartX = e.touches[0].clientX;
  });
  document.getElementById('mainImageWrapper').addEventListener('touchend', e => {
      const diff = touchStartX - e.changedTouches[0].clientX;
      if (Math.abs(diff) > 50) slideCarousel(diff > 0 ? 1 : -1);
  });
  document.getElementById('btnBuyNow').addEventListener('click', function(e) {
      e.preventDefault();

      const item = {
          id:         productDetail.id,
          name:       productDetail.name,
          metal:      productDetail.metal,
          metalLabel: productDetail.metalLabels[productDetail.metal],
          price:      productDetail.price,
          image:      document.getElementById('mainProductImage').src,
          quantity:   1
      };

      // Chỉ lưu riêng sản phẩm này, không ảnh hưởng giỏ hàng
      sessionStorage.setItem('checkout_items', JSON.stringify([item]));

      window.location.href = '/index.php?page=thanh_toan';
  });
  </script>
  <script>
  const productDetail = {
      id:    '<?php echo $product["product_id"]; ?>',
      name:  '<?php echo addslashes($product["product_name"]); ?>',
      price: <?php echo $product["sale_price"] > 0
          ? $product["sale_price"]
          : $product["price"]; ?>,
      metal: 'default',
      metalLabels: { 'default': 'Mặc định' },
      images: { main: '/<?php echo $product["main_image"]; ?>' },
  };
  </script>
  <script>
  function toggleUserDropdown(e) {
      e.preventDefault();
      e.stopPropagation();
      const menu = document.getElementById('userDropdownMenu');
      menu.style.display = menu.style.display === 'none' ? 'block' : 'none';
  }

  document.addEventListener('click', function(e) {
      const wrapper = document.getElementById('userDropdownWrapper');
      if (wrapper && !wrapper.contains(e.target)) {
          document.getElementById('userDropdownMenu').style.display = 'none';
      }
  });
  </script>
  <script src="/assets/js/cart.js"></script>
  <script src="/assets/js/product_details.js"></script>
</body>
</html>
