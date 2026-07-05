<!DOCTYPE html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sản phẩm - Aurrelia Jewelry</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="icon" type="image/png" href="/favicon.png">
    <script>
        window.USER_LOGGED_IN = <?php echo (isset($_SESSION["user_logged_in"]) && $_SESSION["user_logged_in"] === true) ? 'true' : 'false'; ?>;
    </script>
</head>
<body class="bg-cream">

    <!-- ================= HEADER ================= -->
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

    <!-- ================= BANNER CAROUSEL ================= -->
    <style>
        .banner-section .carousel-item {
            height: 500px;
        }
        @media (max-width: 991.98px) {
            .banner-section .carousel-item {
                height: 380px;
            }
            .banner-section .carousel-item h1 {
                font-size: 2rem !important;
            }
        }
        @media (max-width: 575.98px) {
            .banner-section .carousel-item {
                height: 280px;
            }
            .banner-section .carousel-item h1 {
                font-size: 1.5rem !important;
                letter-spacing: 2px !important;
            }
            .banner-section .carousel-item a {
                margin-top: 15px !important;
                padding: 6px 16px !important;
                font-size: 11px !important;
            }
        }
        /* Sleek indicators */
        .banner-section .carousel-indicators [data-bs-target] {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background-color: rgba(255, 255, 255, 0.4);
            border: none;
            margin: 0 5px;
            transition: all 0.2s ease;
        }
        .banner-section .carousel-indicators .active {
            background-color: #c8a165;
            transform: scale(1.2);
        }
    </style>

    <section class="banner-section">
        <?php if (!empty($bannersList)): ?>
            <div id="homepageCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel" data-bs-interval="5000">
                <!-- Indicators -->
                <div class="carousel-indicators mb-3">
                    <?php foreach ($bannersList as $index => $banner): ?>
                        <button type="button" data-bs-target="#homepageCarousel" data-bs-slide-to="<?php echo $index; ?>" 
                                class="<?php echo $index === 0 ? 'active' : ''; ?>" aria-current="<?php echo $index === 0 ? 'true' : 'false'; ?>" 
                                aria-label="Slide <?php echo $index + 1; ?>"></button>
                    <?php endforeach; ?>
                </div>
                
                <!-- Slides -->
                <div class="carousel-inner">
                    <?php foreach ($bannersList as $index => $banner): ?>
                        <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?> position-relative" style="background-color: #000;">
                            <img src="/<?php echo htmlspecialchars($banner['image_url']); ?>" 
                                 alt="<?php echo htmlspecialchars($banner['title']); ?>" 
                                 class="w-100 h-100 object-fit-cover" 
                                 style="opacity: 0.85;">
                            <div class="carousel-caption d-flex flex-column justify-content-center align-items-center h-100" style="bottom: 0;">
                                <h1 class="fw-normal text-white text-uppercase tracking-wide px-3 text-center" 
                                    style="font-family: 'Times New Roman', serif; font-size: 2.8rem; text-shadow: 0 4px 15px rgba(0,0,0,0.6); letter-spacing: 4px;">
                                    <?php echo htmlspecialchars($banner['title']); ?>
                                </h1>
                                <a href="<?php echo htmlspecialchars($banner['target_link'] ?? '#products-section'); ?>" 
                                   class="btn rounded-pill px-4 py-2 mt-4 fw-bold border-0 shadow-lg" 
                                   style="background-color: #c8a165; color: white; font-size: 13px; letter-spacing: 1px; text-decoration: none; transition: transform 0.2s ease;">
                                    KHÁM PHÁ NGAY
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <!-- Controls -->
                <button class="carousel-control-prev" type="button" data-bs-target="#homepageCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Trước</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#homepageCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Sau</span>
                </button>
            </div>
        <?php else: ?>
            <!-- Fallback static banner if database has no active banners -->
            <div class="position-relative" style="height: 500px;">
                <img src="/assets/images/banner.png" alt="Bộ sưu tập Aurelia" class="w-100 h-100 object-fit-cover">
                <div class="position-absolute top-50 start-50 translate-middle text-center text-white">
                    <h1 class="fw-normal" style="font-family: 'Times New Roman', serif; letter-spacing: 4px;">BỘ SƯU TẬP AURRELIA</h1>
                    <a href="#products-section" class="btn rounded-pill px-4 py-2 mt-3 fw-bold border-0" id="btnDiscoverNow" style="background-color: #c8a165; color: white; font-size: 12px; letter-spacing: 1px; text-decoration: none;">KHÁM PHÁ NGAY</a>
                </div>
            </div>
        <?php endif; ?>
    </section>

    <!-- ================= MAIN CONTENT ================= -->
    <div class="container-fluid px-5 my-5" id="products-section">
        <div class="row">

            <!-- CỘT TRÁI: BỘ LỌC (SIDEBAR) -->
            <div class="col-md-3 pe-5">
                <div class="p-4 rounded-3" style="background-color: #fcf9f2;">
                    <h5 class="mb-4" style="font-family: 'Times New Roman', serif; color: #a47e4b;">BỘ LỌC</h5>

                    <?php $catGet = isset($_GET['category_id']) ? (int)$_GET['category_id'] : 0; ?>
                    <div class="mb-4">
                        <ul class="list-unstyled" id="category-filter">
                            <li class="mb-3">
                                <a href="#all-sections" class="category-link active"><i class="fas fa-gem me-2 icon-active"></i>Tất cả trang sức</a>
                            </li>
                            <?php if (!empty($categoriesList)): ?>
                                <?php foreach ($categoriesList as $cat): ?>
                                    <li class="mb-3">
                                        <a href="#category-<?php echo $cat['category_id']; ?>" class="category-link">
                                            <i class="fas fa-gem me-2 icon-active d-none"></i>
                                            <?php echo htmlspecialchars($cat['category_name']); ?>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </ul>
                     </div>

                    <?php $priceGet = isset($_GET['price_range']) ? $_GET['price_range'] : ""; ?>
                    <div class="mb-4">
                        <h6 class="fw-bold mb-3">Mức giá</h6>
                        <div class="form-check mb-2">
                            <input class="form-check-input price-filter-cb" type="checkbox" id="price1" value="under10m" <?php echo $priceGet === 'under10m' ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="price1">Dưới 10.000.000 ₫</label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input price-filter-cb" type="checkbox" id="price2" value="10m-25m" <?php echo $priceGet === '10m-25m' ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="price2">10.000.000 ₫ - 25.000.000 ₫</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input price-filter-cb" type="checkbox" id="price3" value="over25m" <?php echo $priceGet === 'over25m' ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="price3">Trên 25.000.000 ₫</label>
                        </div>
                    </div>

                    <button class="btn btn-gold rounded-pill w-100 mb-3" id="btnApplyFilterUser">Áp dụng</button>
                    <div class="text-center">
                        <a href="/index.php?page=home" class="text-muted text-decoration-none" style="font-size: 13px;">Xóa bộ lọc</a>
                    </div>
                </div>
            </div>

            <!-- CỘT PHẢI: LƯỚI SẢN PHẨM -->
            <div class="col-md-9">
                <!-- Header Lưới -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <span class="text-muted" style="font-size: 14px;">Hiển thị <?php echo count($products); ?> kết quả</span>
                    <div class="d-flex align-items-center">
                        <span class="me-2" style="font-size: 14px;">Sắp xếp:</span>
                        <?php $sortGet = isset($_GET['sort']) ? $_GET['sort'] : "newest"; ?>
                        <select id="sortSelectUser" class="form-select form-select-sm rounded-pill border-0 bg-light" style="width: 160px;">
                            <option value="newest" <?php echo $sortGet === 'newest' ? 'selected' : ''; ?>>Nổi bật nhất</option>
                            <option value="price_asc" <?php echo $sortGet === 'price_asc' ? 'selected' : ''; ?>>Giá: Thấp đến Cao</option>
                            <option value="price_desc" <?php echo $sortGet === 'price_desc' ? 'selected' : ''; ?>>Giá: Cao đến Thấp</option>
                        </select>
                    </div>
                </div>

                <!-- Lưới (Grid) -->
                <div class="row g-4" id="all-sections">
                    <?php
                    $groupedProducts = [];
                    if (!empty($products)) {
                        foreach ($products as $product) {
                            $catId = $product["category_id"] ?? 0;
                            $catName = $product["category_name"] ?? "Trang Sức Khác";
                            $groupedProducts[$catId]["name"] = $catName;
                            $groupedProducts[$catId]["products"][] = $product;
                        }
                    }
                    ?>

                    <?php if (!empty($groupedProducts)): ?>
                        <?php foreach ($groupedProducts as $catId => $group): ?>
                            <div id="category-<?php echo $catId; ?>" class="category-section mb-5 pt-4">
                                <h4 class="mb-4 text-uppercase border-bottom pb-2" style="font-family: 'Times New Roman', serif; letter-spacing: 2px; color: #a47e4b;">
                                    <?php echo htmlspecialchars($group["name"]); ?>
                                </h4>
                                <div class="row g-4">
                                    <?php foreach ($group["products"] as $product): ?>
                                        <div class="col-md-4 product-item">
                                            <a href="/index.php?page=chi_tiet&id=<?php echo $product["product_id"]; ?>" class="text-decoration-none text-dark">
                                                <div class="card border-0 bg-transparent mb-4">
                                                    <img src="/<?php echo htmlspecialchars($product["main_image"]); ?>"
                                                         class="card-img-top rounded-0"
                                                         style="height: 350px; object-fit: cover;"
                                                         alt="<?php echo htmlspecialchars($product["product_name"]); ?>">

                                                    <div class="card-body px-0 position-relative">
                                                        <h6 class="card-title fw-bold" style="font-family: 'Times New Roman', serif;">
                                                            <?php echo htmlspecialchars($product["product_name"]); ?>
                                                        </h6>
                                                        <p class="text-muted small mb-1">Aurrelia Collections</p>

                                                        <div class="product-price">
                                                            <?php if (isset($product["sale_price"]) && $product["sale_price"] > 0): ?>
                                                                <span class="fw-bold me-2" style="color:#c8a165;">
                                                                    <?php echo number_format($product["sale_price"], 0, ",", "."); ?> ₫
                                                                </span>
                                                                <span class="text-muted text-decoration-line-through small">
                                                                    <?php echo number_format($product["price"], 0, ",", "."); ?> ₫
                                                                </span>
                                                            <?php else: ?>
                                                                <span class="fw-bold">
                                                                    <?php echo number_format($product["price"], 0, ",", "."); ?> ₫
                                                                </span>
                                                            <?php endif; ?>
                                                        </div>

                                                        <i class="far fa-heart position-absolute top-0 end-0 mt-3 toggle-heart"
                                                           style="cursor: pointer;"
                                                           data-product-id="<?php echo $product["product_id"]; ?>"></i>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="col-12 text-center my-5">
                            <p class="text-muted">Hiện tại chưa có sản phẩm nào phù hợp.</p>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="text-center mt-5">
                    <button class="btn btn-outline-dark rounded-pill px-5 py-2">TẢI THÊM SẢN PHẨM</button>
                </div>
            </div>
        </div>
    </div>

    <!-- ================= VỀ CHÚNG TÔI ================= -->
    <section id="about-us-section" class="py-5" style="background-color: #fdfaf5; border-top: 1px solid #f2e9dc;">
        <div class="container py-4" style="max-width: 800px; text-align: center;">
            <h3 class="mb-3 text-uppercase tracking-wider" style="font-family: 'Times New Roman', serif; color: #a47e4b; letter-spacing: 3px;">Về Chúng Tôi</h3>
            <div class="mb-4" style="width: 60px; height: 1px; background-color: #c8a165; margin: 0 auto;"></div>
            <p class="lead" style="font-style: italic; color: #555; font-size: 16px; line-height: 1.8;">
                "Nơi chế tác và lưu giữ những báu vật của thời gian."
            </p>
            <p class="text-muted" style="font-size: 14px; line-height: 1.8; text-align: justify; margin-top: 20px;">
                Chào mừng bạn đến với **Aurrelia Fine Jewelry** – thương hiệu trang sức thủ công cao cấp được thành lập với sứ mệnh mang đến sự quý phái và nét duyên dáng vĩnh cửu. Mỗi thiết kế của chúng tôi đều trải qua hàng trăm giờ chế tác tỉ mỉ từ những thợ kim hoàn tài hoa nhất. Từ những viên kim cương Solitaire lấp lánh đến chất vàng nguyên khối 18k, Aurrelia biến những nguyên liệu tinh túy nhất của đất trời thành các tuyệt tác nghệ thuật đồng hành cùng những khoảnh khắc trọng đại của cuộc đời bạn.
            </p>
        </div>
    </section>

    <!-- ================= LIÊN HỆ ================= -->
    <section id="contact-section" class="py-5" style="background-color: #ffffff; border-top: 1px solid #f2e9dc;">
        <div class="container py-4" style="max-width: 900px;">
            <h3 class="mb-3 text-uppercase tracking-wider text-center" style="font-family: 'Times New Roman', serif; color: #a47e4b; letter-spacing: 3px;">Liên Hệ</h3>
            <div class="mb-5" style="width: 60px; height: 1px; background-color: #c8a165; margin: 0 auto;"></div>
            <div class="row g-4 text-center">
                <div class="col-md-4">
                    <div class="p-4 border rounded shadow-sm bg-light-custom h-100">
                        <i class="fas fa-map-marker-alt fs-3 mb-3 text-gold" style="color:#c8a165;"></i>
                        <h6 class="fw-bold mb-2" style="font-size:14px;">ĐỊA CHỈ SHOWROOM</h6>
                        <p class="text-muted small mb-0">Số 1 Đại Cồ Việt, Phường Bách Khoa, Quận Hai Bà Trưng, Hà Nội</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-4 border rounded shadow-sm bg-light-custom h-100">
                        <i class="fas fa-phone-alt fs-3 mb-3 text-gold" style="color:#c8a165;"></i>
                        <h6 class="fw-bold mb-2" style="font-size:14px;">ĐƯỜNG DÂY NÓNG</h6>
                        <p class="text-muted small mb-1">1900 123 456</p>
                        <p class="text-muted small mb-0">Hotline: 0987 654 321</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-4 border rounded shadow-sm bg-light-custom h-100">
                        <i class="fas fa-envelope fs-3 mb-3 text-gold" style="color:#c8a165;"></i>
                        <h6 class="fw-bold mb-2" style="font-size:14px;">HÒM THƯ ĐIỆN TỬ</h6>
                        <p class="text-muted small mb-1">contact@aurrelia.vn</p>
                        <p class="text-muted small mb-0">support@aurrelia.vn</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

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
    <script src="/assets/js/chat.js"></script>
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
