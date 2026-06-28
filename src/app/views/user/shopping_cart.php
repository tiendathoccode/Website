<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Giỏ Hàng – Aurrelia</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
  <link rel="stylesheet" href="../assets/css/style.css" />
  <link rel="stylesheet" href="/assets/css/shopping_cart.css" />
</head>
<body class="bg-cream">

  <nav class="navbar navbar-expand-lg py-3 sticky-top border-bottom shadow-sm" style="background-color: #fdfbf7; z-index: 1020;">
      <div class="container-fluid px-5">
          <a class="navbar-brand fs-4 fw-bold gold-text" href="index.html" style="font-family: 'Times New Roman', serif;">AURELIA</a>

          <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
              <span class="navbar-toggler-icon"></span>
          </button>

          <div class="collapse navbar-collapse" id="navbarNav">
              <ul class="navbar-nav mx-auto text-uppercase" style="font-size: 13px; letter-spacing: 1px;">
                  <li class="nav-item"><a class="nav-link px-3 text-dark fw-semibold" href="#">Trang Sức</a></li>
                  <li class="nav-item"><a class="nav-link px-3 text-dark fw-semibold" href="#">Trang Sức Cao Cấp</a></li>
                  <li class="nav-item"><a class="nav-link px-3 text-dark fw-semibold" href="#">Về Chúng Tôi</a></li>
                  <li class="nav-item"><a class="nav-link px-3 text-dark fw-semibold" href="#">Liên Hệ</a></li>
              </ul>
              <div class="d-flex gap-3 align-items-center">
                  <a href="#" class="text-dark fs-6"><i class="fas fa-search"></i></a>
                  <a href="#" class="text-dark fs-6"><i class="far fa-heart"></i></a>
                  <a href="shopping_cart.html" class="text-dark fs-6 position-relative" id="headerCartBtn">
                      <i class="fas fa-shopping-bag"></i>
                      <span class="cart-badge-header" id="headerCartBadge" style="display:none; position: absolute; top: -8px; right: -10px; background-color: #c8a165; color: white; font-size: 10px; border-radius: 50%; padding: 2px 6px;">0</span>
                  </a>
                  <a href="login.html" class="text-dark fs-6"><i class="far fa-user"></i></a>
              </div>
          </div>
      </div>
  </nav>
  <div class="container-fluid px-5 pt-4 pb-2">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb" style="font-size: 13px;">
        <li class="breadcrumb-item"><a href="index.html" class="text-muted text-decoration-none">Trang chủ</a></li>
        <li class="breadcrumb-item active text-dark">Giỏ hàng</li>
      </ol>
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
          <a href="index.html" class="btn-gold-outline">TIẾP TỤC MUA SẮM</a>
        </div>

        <div class="mt-4" id="continueShopping">
          <a href="index.html" class="continue-link">
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
  <script src="../assets/js/cart.js"></script>
  <script src="../assets/js/shopping_cart.js"></script>
</body>
</html>
