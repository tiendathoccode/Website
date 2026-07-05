<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Giỏ Hàng – Aurrelia</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
  <link rel="stylesheet" href="/assets/css/style.css" />
  <link rel="stylesheet" href="/assets/css/shopping_cart.css" />
  <link rel="icon" type="image/png" href="/favicon.png" />
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
    <h2 class="fw-normal mb-0" style="font-family: 'Times New Roman', serif; letter-spacing: 2px;">GIỎ HÀNG CỦA BẠN</h2>
    <p class="text-muted mt-1" style="font-size:13px;" id="cartItemCount">0 sản phẩm</p>
  </div>

  <div class="container-fluid px-5 pb-5">
    <div class="row g-5">

      <div class="col-lg-8">

        <div class="cart-table-header d-none d-md-grid">
          <span>SẢN PHẨM</span>
          <span class="text-center">SỐ LƯỢNG</span>
          <span class="text-end">THÀNH TIỀN</span>
        </div>

        <div id="cartItemsContainer">
          </div>

        <div id="emptyCartMsg" class="text-center py-5" style="display:none;">
          <i class="fas fa-shopping-bag mb-3" style="font-size:48px; color:#d4c4a8;"></i>
          <h5 class="fw-normal mb-2" style="font-family:'Times New Roman', serif;">Giỏ hàng của bạn đang trống</h5>
          <p class="text-muted mb-4" style="font-size:13px;">Hãy khám phá bộ sưu tập và thêm sản phẩm yêu thích.</p>
          <a href="/index.php?page=home" class="btn-gold-outline">TIẾP TỤC MUA SẮM</a>
        </div>

        <div class="mt-4" id="continueShopping">
          <a href="/index.php?page=home" class="continue-link">
            <i class="fas fa-arrow-left me-2" style="font-size:11px;"></i>Tiếp tục mua sắm
          </a>
        </div>
      </div>

      <div class="col-lg-4">
        <div class="order-summary-card">
          <h5 class="summary-title">TÓM TẮT ĐƠN HÀNG</h5>

          <div class="summary-row">
            <span>Tạm tính</span>
            <span id="summarySubtotal">0₫</span>
          </div>
          <div class="summary-row">
            <span>Phí vận chuyển</span>
            <span class="text-success" id="summaryShipping">Miễn phí</span>
          </div>

          <div class="summary-divider"></div>

          <div class="summary-row summary-total">
            <span>Tổng cộng</span>
            <span id="summaryTotal">0₫</span>
          </div>

          <p class="summary-note">Bao gồm VAT. Phí vận chuyển và thuế sẽ được xác nhận khi thanh toán.</p>

          <button class="btn-checkout-main" id="btnCheckout">
            TIẾN HÀNH THANH TOÁN
          </button>

          <div class="payment-icons">
            <i class="fab fa-cc-visa"></i>
            <i class="fab fa-cc-mastercard"></i>
            <i class="fab fa-cc-paypal"></i>
            <i class="fas fa-university"></i>
          </div>

          <div class="promo-section">
            <p class="promo-label">MÃ GIẢM GIÁ</p>
            <div class="promo-input-row">
              <input type="text" id="promoInput" placeholder="Nhập mã khuyến mãi" class="promo-input" />
              <button class="promo-btn" id="btnApplyPromo">ÁP DỤNG</button>
            </div>
            <p id="promoMsg" class="promo-msg" style="display:none;"></p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <footer class="pt-5 pb-3 border-top mt-5" style="background-color: #fdfbf7;">
      <div class="container-fluid px-5">
          <div class="row">
              <div class="col-md-4 mb-4">
                  <h4 class="fw-bold gold-text mb-3" style="font-family: 'Times New Roman', serif;">AURELIA</h4>
                  <p class="text-muted small w-75">Điểm đến của những tuyệt tác trang sức thủ công. Kiến tạo vẻ đẹp vượt thời gian.</p>
                  <p class="small text-muted mt-4">&copy; 2026 Aurrelia. Bản quyền thuộc về Nhóm 6.</p>
              </div>
              <div class="col-md-2 mb-4">
                  <h6 class="text-uppercase mb-3 fw-bold" style="font-size: 12px;">Công Ty</h6>
                  <ul class="list-unstyled text-muted small">
                      <li class="mb-2"><a href="#" class="text-decoration-none text-muted">Về Chúng Tôi</a></li>
                      <li class="mb-2"><a href="#" class="text-decoration-none text-muted">Bộ Sưu Tập</a></li>
                      <li class="mb-2"><a href="#" class="text-decoration-none text-muted">Tuyển Dụng</a></li>
                  </ul>
              </div>
              <div class="col-md-3 mb-4">
                  <h6 class="text-uppercase mb-3 fw-bold" style="font-size: 12px;">Hỗ Trợ Khách Hàng</h6>
                  <ul class="list-unstyled text-muted small">
                      <li class="mb-2"><a href="#" class="text-decoration-none text-muted">Giao Hàng & Đổi Trả</a></li>
                      <li class="mb-2"><a href="#" class="text-decoration-none text-muted">Chính Sách Bảo Mật</a></li>
                      <li class="mb-2"><a href="#" class="text-decoration-none text-muted">Liên Hệ</a></li>
                  </ul>
              </div>
              <div class="col-md-3 mb-4">
                  <h6 class="text-uppercase mb-3 fw-bold" style="font-size: 12px;">Kết Nối</h6>
                  <ul class="list-unstyled text-muted small">
                      <li class="mb-2"><a href="#" class="text-decoration-none text-muted">Facebook</a></li>
                      <li class="mb-2"><a href="#" class="text-decoration-none text-muted">Instagram</a></li>
                      <li class="mb-2"><a href="#" class="text-decoration-none text-muted">Pinterest</a></li>
                  </ul>
              </div>
          </div>
      </div>
  </footer>
  <div id="cartToast" class="cart-toast" style="display:none;"></div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
      window.IS_LOGGED_IN = <?php echo isset($_SESSION["user_logged_in"]) &&
      $_SESSION["user_logged_in"] === true
          ? "true"
          : "false"; ?>;
    </script>
  <script src="/assets/js/cart.js"></script>
  <script src="/assets/js/shopping_cart.js"></script>
  <script src="/assets/js/chat.js"></script>
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
</body>
</html>
