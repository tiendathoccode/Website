<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Chi Tiết Sản Phẩm - Aurrelia</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
  <link rel="stylesheet" href="../assets/css/style.css" />
  <link rel="stylesheet" href="../assets/css/product_details.css" />
</head>
<body class="bg-cream">

  <nav class="navbar navbar-expand-lg py-3 sticky-top border-bottom shadow-sm" style="background-color: #fdfbf7; z-index: 1020;">
      <div class="container-fluid px-5">
          <a class="navbar-brand fs-4 fw-bold gold-text" href="index.html" style="font-family: 'Times New Roman', serif;">AURRELIA</a>
          
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
                  <a href="#" class="text-dark fs-6 position-relative" id="headerCartBtn">
                      <i class="fas fa-shopping-bag"></i>
                      <span class="cart-badge-header" id="headerCartBadge" style="display:none; position: absolute; top: -8px; right: -10px; background-color: #c8a165; color: white; font-size: 10px; border-radius: 50%; padding: 2px 6px;">0</span>
                  </a>
                  <a href="login.html" class="text-dark fs-6"><i class="far fa-user"></i></a>
              </div>
          </div>
      </div>
  </nav>
  <main id="productPage" class="container-fluid px-5 my-4">

    <nav id="breadcrumb" aria-label="breadcrumb" class="mb-4">
      <a href="#" class="text-muted text-decoration-none" style="font-size: 13px;">Bộ sưu tập</a>
      <span class="text-muted mx-2">›</span>
      <a href="#" class="text-muted text-decoration-none" style="font-size: 13px;">Vòng cổ</a>
      <span class="text-muted mx-2">›</span>
      <span class="text-dark fw-semibold" style="font-size: 13px;">Eternity Drop</span>
    </nav>

    <section id="productSection">

      <div id="productGallery">
        <div id="mainImageWrapper">
          <span class="product-badge">PHIÊN BẢN GIỚI HẠN</span>
          <img
            src="https://images.unsplash.com/photo-1599643477877-530eb83abc8e?w=900&auto=format&fit=crop&q=80"
            alt="Vòng cổ Eternity Drop – ảnh chính"
            id="mainProductImage"
            class="main-product-img"
          />
        </div>
        <div id="thumbnailStrip">
          <button
            class="thumb-btn thumb-btn--active"
            data-src="https://images.unsplash.com/photo-1599643477877-530eb83abc8e?w=900&auto=format&fit=crop&q=80"
            data-alt="Vòng cổ Eternity Drop – ảnh chính"
          >
            <img src="https://images.unsplash.com/photo-1599643477877-530eb83abc8e?w=200&auto=format&fit=crop&q=80" alt="Ảnh nhỏ 1" />
          </button>
          <button
            class="thumb-btn"
            data-src="https://images.unsplash.com/photo-1515562141207-7a88fb7ce338?w=900&auto=format&fit=crop&q=80"
            data-alt="Vòng cổ Eternity Drop – khi đeo"
          >
            <img src="https://images.unsplash.com/photo-1515562141207-7a88fb7ce338?w=200&auto=format&fit=crop&q=80" alt="Ảnh nhỏ 2" />
          </button>
          <button
            class="thumb-btn"
            data-src="https://images.unsplash.com/photo-1611085583191-a3b181a88401?w=900&auto=format&fit=crop&q=80"
            data-alt="Vòng cổ Eternity Drop – trong hộp"
          >
            <img src="https://images.unsplash.com/photo-1611085583191-a3b181a88401?w=200&auto=format&fit=crop&q=80" alt="Ảnh nhỏ 3" />
          </button>
        </div>
      </div>

      <div id="productInfo">
        <h1 id="productName" class="product-title" style="font-family: 'Times New Roman', serif;">Vòng Cổ Eternity Drop</h1>
        <p id="productPrice" class="product-price">3.450.000₫</p>

        <p id="productDescription" class="product-desc">
          Được chế tác tinh xảo, Vòng Cổ Eternity Drop sở hữu viên kim cương cắt hình giọt lê hoàn hảo, treo nhẹ nhàng trên dây chuyền vàng champagne nguyên khối 18k. Mỗi đường nét được thiết kế để bắt sáng từ mọi góc nhìn, toát lên vẻ sang trọng tinh tế.
        </p>

        <div id="metalSelector" class="option-group">
          <p class="option-label">CHẤT LIỆU</p>
          <div id="metalOptions" class="metal-swatches">
            <button
              class="swatch swatch--gold swatch--active"
              data-metal="champagne-gold"
              id="swatchChampagneGold"
              aria-label="Vàng Champagne"
              title="Vàng Champagne"
            ></button>
            <button
              class="swatch swatch--white"
              data-metal="white-gold"
              id="swatchWhiteGold"
              aria-label="Vàng Trắng"
              title="Vàng Trắng"
            ></button>
            <button
              class="swatch swatch--rose"
              data-metal="rose-gold"
              id="swatchRoseGold"
              aria-label="Vàng Hồng"
              title="Vàng Hồng"
            ></button>
          </div>
          <p id="selectedMetalLabel" class="selected-metal-label">Vàng Champagne</p>
        </div>

        <div id="productActions">
          <button id="btnAddToCart" class="btn-primary w-100 mb-2">THÊM VÀO GIỎ HÀNG</button>
          <button id="btnAddToWishlist" class="btn-secondary w-100 bg-transparent border-0 d-flex align-items-center justify-content-center gap-2">
            <i class="far fa-heart"></i>
            Lưu vào yêu thích
          </button>
        </div>

        <div id="productAccordion">
          <div class="accordion-item" id="accordionDetails">
            <button class="accordion-trigger" data-target="panelDetails">
              CHI TIẾT &amp; KÍCH THƯỚC
              <span class="accordion-icon">+</span>
            </button>
            <div class="accordion-panel" id="panelDetails">
              <p>Đá quý: Kim cương cắt giọt lê, 1.2ct, độ trong VS1, màu D<br>
              Dây chuyền: Vàng champagne nguyên khối 18k, dài 42cm có thể chỉnh đến 45cm<br>
              Kích thước mặt dây: 28mm × 14mm<br>
              Trọng lượng: 4.2g</p>
            </div>
          </div>
          <div class="accordion-item" id="accordionShipping">
            <button class="accordion-trigger" data-target="panelShipping">
              VẬN CHUYỂN &amp; ĐỔI TRẢ
              <span class="accordion-icon">+</span>
            </button>
            <div class="accordion-panel" id="panelShipping">
              <p>Miễn phí vận chuyển có bảo hiểm trên toàn thế giới. Giao hàng trong 3–5 ngày làm việc, đóng gói trong hộp quà đặc trưng của Aurelia. Chấp nhận đổi trả trong vòng 14 ngày kể từ ngày giao hàng đối với sản phẩm chưa sử dụng.</p>
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
        <a href="shopping_cart.html" style="
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
  <script src="../assets/js/cart.js"></script>
  <script src="../assets/js/product_details.js"></script>
</body>
</html>