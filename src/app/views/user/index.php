<!DOCTYPE html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sản phẩm - Aurrelia Jewelry</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body class="bg-cream">

    <!-- ================= HEADER ================= -->
    <nav class="navbar navbar-expand-lg py-3 sticky-top border-bottom shadow-sm" style="background-color: #fdfbf7; z-index: 1020;">
        <div class="container-fluid px-5">
            <a class="navbar-brand fs-4 fw-bold gold-text" href="index.html" style="font-family: 'Times New Roman', serif;">AURRELIA</a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mx-auto text-uppercase" style="font-size: 13px; letter-spacing: 1px;">
                    <li class="nav-item"><a class="nav-link px-3" href="#">Trang Sức</a></li>
                    <li class="nav-item"><a class="nav-link px-3" href="#">Trang Sức Cao Cấp</a></li>
                    <li class="nav-item"><a class="nav-link px-3" href="#">Về Chúng Tôi</a></li>
                    <li class="nav-item"><a class="nav-link px-3" href="#">Liên Hệ</a></li>
                </ul>
                <div class="d-flex gap-3 align-items-center">
                    <a href="#" class="text-dark fs-6"><i class="fas fa-search"></i></a>
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
                    <!-- Icon User với Dropdown -->
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
                                <!-- Đã đăng nhập -->
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
                                <a href="/index.php?page=gio_hang" style="display:flex; align-items:center; gap:10px; padding:12px 18px; text-decoration:none; color:#333; font-size:13px; border-bottom:1px solid #f5f5f5;">
                                    <i class="fas fa-box" style="color:#bfa15f; width:16px;"></i> Đơn hàng của tôi
                                </a>
                                <a href="/index.php?page=change_password" style="display:flex; align-items:center; gap:10px; padding:12px 18px; text-decoration:none; color:#333; font-size:13px; border-bottom:1px solid #f5f5f5;">
                                    <i class="fas fa-lock" style="color:#bfa15f; width:16px;"></i> Đổi mật khẩu
                                </a>
                                <a href="/index.php?page=logout" style="display:flex; align-items:center; gap:10px; padding:12px 18px; text-decoration:none; color:#c0392b; font-size:13px;">
                                    <i class="fas fa-sign-out-alt" style="color:#c0392b; width:16px;"></i> Đăng xuất
                                </a>
                            <?php else: ?>
                                <!-- Chưa đăng nhập -->
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
            </div>
        </div>
    </nav>

    <!-- ================= BANNER ================= -->
    <section class="banner-section position-relative">
        <img src="/assets/images/banner.png" alt="Bộ sưu tập Aurelia" class="w-100 object-fit-cover" style="height: 350px;">
        <div class="position-absolute top-50 start-50 translate-middle text-center text-white">
            <h1 class="fw-normal" style="font-family: 'Times New Roman', serif; letter-spacing: 4px;">BỘ SƯU TẬP AURRELIA</h1>
            <button class="btn rounded-pill px-4 py-2 mt-3 fw-bold border-0" style="background-color: #c8a165; color: white; font-size: 12px; letter-spacing: 1px;">KHÁM PHÁ NGAY</button>
        </div>
    </section>

    <!-- ================= MAIN CONTENT ================= -->
    <div class="container-fluid px-5 my-5">
        <div class="row">

            <!-- CỘT TRÁI: BỘ LỌC (SIDEBAR) -->
            <div class="col-md-3 pe-5">
                <div class="p-4 rounded-3" style="background-color: #fcf9f2;">
                    <h5 class="mb-4" style="font-family: 'Times New Roman', serif; color: #a47e4b;">BỘ LỌC</h5>

                    <div class="mb-4">
                        <ul class="list-unstyled" id="category-filter">
                            <li class="mb-3">
                                <a href="#" class="category-link active"><i class="fas fa-gem me-2 icon-active"></i>Tất cả trang sức</a>
                            </li>
                            <li class="mb-3">
                                <a href="#" class="category-link"><i class="fas fa-gem me-2 icon-active d-none"></i>Dây chuyền</a>
                            </li>
                            <li class="mb-3">
                                <a href="#" class="category-link"><i class="fas fa-gem me-2 icon-active d-none"></i>Đồng hồ</a>
                            </li>
                            <li class="mb-3">
                                <a href="#" class="category-link"><i class="fas fa-gem me-2 icon-active d-none"></i>Nhẫn</a>
                            </li>
                            <li class="mb-3">
                                <a href="#" class="category-link"><i class="fas fa-gem me-2 icon-active d-none"></i>Bông tai</a>
                            </li>
                        </ul>
                    </div>

                    <div class="mb-4">
                        <h6 class="fw-bold mb-3">Mức giá</h6>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="price1">
                            <label class="form-check-label" for="price1">Dưới 10.000.000 ₫</label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="price2" checked>
                            <label class="form-check-label" for="price2">10.000.000 ₫ - 25.000.000 ₫</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="price3">
                            <label class="form-check-label" for="price3">Trên 25.000.000 ₫</label>
                        </div>
                    </div>

                    <button class="btn btn-gold rounded-pill w-100 mb-3">Áp dụng</button>
                    <div class="text-center">
                        <a href="#" class="text-muted text-decoration-none" style="font-size: 13px;">Xóa bộ lọc</a>
                    </div>
                </div>
            </div>

            <!-- CỘT PHẢI: LƯỚI SẢN PHẨM -->
            <div class="col-md-9">
                <!-- Header Lưới -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <span class="text-muted" style="font-size: 14px;">Hiển thị 1-6 của 24 kết quả</span>
                    <div class="d-flex align-items-center">
                        <span class="me-2" style="font-size: 14px;">Sắp xếp:</span>
                        <select class="form-select form-select-sm rounded-pill border-0 bg-light" style="width: 160px;">
                            <option>Nổi bật nhất</option>
                            <option>Giá: Thấp đến Cao</option>
                            <option>Giá: Cao đến Thấp</option>
                        </select>
                    </div>
                </div>

                <!-- Lưới (Grid) -->
                <div class="row g-4" id="product-list">

                                    <?php if (!empty($products)): ?>
                                        <?php foreach (
                                            $products
                                            as $product
                                        ): ?>
                                        <div class="col-md-4 product-item">
                                            <a href="/index.php?page=chi_tiet&id=<?php echo $product[
                                                "product_id"
                                            ]; ?>"
                                               class="text-decoration-none text-dark">
                                            <div class="card border-0 bg-transparent mb-4">
                                                    <img src="/<?php echo htmlspecialchars(
                                                        $product["main_image"],
                                                    ); ?>"
                                                         class="card-img-top rounded-0"
                                                         style="height: 350px; object-fit: cover;"
                                                         alt="<?php echo htmlspecialchars(
                                                             $product[
                                                                 "product_name"
                                                             ],
                                                         ); ?>">

                                                    <div class="card-body px-0 position-relative">
                                                        <h6 class="card-title fw-bold" style="font-family: 'Times New Roman', serif;">
                                                            <?php echo htmlspecialchars(
                                                                $product[
                                                                    "product_name"
                                                                ],
                                                            ); ?>
                                                        </h6>
                                                        <p class="text-muted small mb-1">Aurrelia Collections</p>

                                                        <div class="product-price">
                                                            <?php if (
                                                                isset(
                                                                    $product[
                                                                        "sale_price"
                                                                    ],
                                                                ) &&
                                                                $product[
                                                                    "sale_price"
                                                                ] > 0
                                                            ): ?>
                                                                <span class="fw-bold text-danger me-2">
                                                                    <?php echo number_format(
                                                                        $product[
                                                                            "sale_price"
                                                                        ],
                                                                        0,
                                                                        ",",
                                                                        ".",
                                                                    ); ?> ₫
                                                                </span>
                                                                <span class="text-muted text-decoration-line-through small">
                                                                    <?php echo number_format(
                                                                        $product[
                                                                            "price"
                                                                        ],
                                                                        0,
                                                                        ",",
                                                                        ".",
                                                                    ); ?> ₫
                                                                </span>
                                                            <?php else: ?>
                                                                <span class="fw-bold">
                                                                    <?php echo number_format(
                                                                        $product[
                                                                            "price"
                                                                        ],
                                                                        0,
                                                                        ",",
                                                                        ".",
                                                                    ); ?> ₫
                                                                </span>
                                                            <?php class_exists(
                                                                "",
                                                            );endif; ?>
                                                        </div>

                                                        <i class="far fa-heart position-absolute top-0 end-0 mt-3 toggle-heart"
                                                           style="cursor: pointer;"
                                                           data-product-id="<?php echo $product[
                                                               "product_id"
                                                           ]; ?>"></i>
                                                    </div>
                                                </div>
                                               </a>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <div class="col-12 text-center my-5">
                                            <p class="text-muted">Hiện tại chưa có sản phẩm nào trong danh mục này.</p>
                                        </div>
                                    <?php endif; ?>

                                </div>

                <div class="text-center mt-5">
                    <button class="btn btn-outline-dark rounded-pill px-5 py-2">TẢI THÊM SẢN PHẨM</button>
                </div>
            </div>
        </div>
    </div>

    <!-- ================= FOOTER ================= -->
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/assets/js/cart.js"></script>
    <script src="/assets/js/main.js"></script>
    <script>
    function toggleUserDropdown(e) {
        e.preventDefault();
        e.stopPropagation();
        const menu = document.getElementById('userDropdownMenu');
        menu.style.display = menu.style.display === 'none' ? 'block' : 'none';
    }

    // Click ra ngoài thì đóng
    document.addEventListener('click', function(e) {
        const wrapper = document.getElementById('userDropdownWrapper');
        if (wrapper && !wrapper.contains(e.target)) {
            document.getElementById('userDropdownMenu').style.display = 'none';
        }
    });
    </script>
</body>
</html>
