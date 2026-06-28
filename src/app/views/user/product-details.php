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
        <a href="#" class="text-muted text-decoration-none" style="font-size: 13px;">
            <?php echo htmlspecialchars($product["category_name"]); ?>
        </a>
        <span class="text-muted mx-2">›</span>
        <span class="text-dark fw-semibold" style="font-size: 13px;">
            <?php echo htmlspecialchars($product["product_name"]); ?>
        </span>
    </nav>

    <section id="productSection">

      <div id="productGallery">
          <div id="mainImageWrapper">
              <img src="/<?php echo htmlspecialchars(
                  $product["main_image"],
              ); ?>"
                   alt="<?php echo htmlspecialchars(
                       $product["product_name"],
                   ); ?>"
                   id="mainProductImage" class="main-product-img"/>
          </div>
          <div id="thumbnailStrip">
              <!-- Ảnh chính luôn là thumbnail đầu tiên -->
              <button class="thumb-btn thumb-btn--active"
                      data-src="/<?php echo htmlspecialchars(
                          $product["main_image"],
                      ); ?>"
                      data-alt="<?php echo htmlspecialchars(
                          $product["product_name"],
                      ); ?>">
                  <img src="/<?php echo htmlspecialchars(
                      $product["main_image"],
                  ); ?>"
                       alt="Ảnh chính"/>
              </button>
              <!-- Ảnh phụ nếu có -->
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
                      <span style="color:#c0392b;">
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
                     style="display:block; text-align:center; text-decoration:none; background-color:#333; padding:12px;"
                     onclick="Cart.add({
                         id: '<?php echo $product["product_id"]; ?>',
                         name: '<?php echo addslashes(
                             $product["product_name"],
                         ); ?>',
                         metal: 'default',
                         metalLabel: 'Mặc định',
                         price: <?php echo $product["sale_price"] > 0
                             ? $product["sale_price"]
                             : $product["price"]; ?>,
                         image: '/<?php echo $product["main_image"]; ?>'
                     });">
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
  <script src="/assets/js/cart.js"></script>
  <script src="/assets/js/product_details.js"></script>
</body>
</html>
