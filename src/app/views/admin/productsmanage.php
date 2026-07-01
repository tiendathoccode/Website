<?php
$successMessage = $_SESSION["success_message"] ?? "";
$errorMessage = $_SESSION["error_message"] ?? "";
unset($_SESSION["success_message"], $_SESSION["error_message"]);

$adminName = $_SESSION["user_name"] ?? "Admin";
$adminInitials = strtoupper(substr($adminName, 0, 1));
$keyword = $filters["keyword"] ?? "";
$selectedCategoryId = (int) ($filters["category_id"] ?? 0);
$selectedSort = $filters["sort"] ?? "newest";
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aurrelia Fine Jewelry - Quản lý sản phẩm</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=Playfair+Display:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="/assets/admin/style.css">
</head>
<body>

<div class="container-fluid">
    <div class="row min-vh-100">
        <nav class="col-md-3 col-lg-2 d-md-block sidebar border-end p-4">
            <div class="position-sticky d-flex flex-column h-100 justify-content-between">
                <div>
                    <div class="brand-zone mb-4">
                        <h3 class="brand-logo mb-1">AURRELIA</h3>
                        <small class="text-muted tracking-wider font-xs text-uppercase">Fine Jewelry Admin</small>
                    </div>

                    <ul class="nav flex-column gap-2 mt-4">
                        <li class="nav-item">
                            <a class="nav-link" href="/index.php?page=admin_dashboard"><i class="bi bi-grid-1x2 me-2"></i> Dashboard</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="/index.php?page=admin_products"><i class="bi bi-gem me-2"></i> Sản Phẩm</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/index.php?page=admin_categories"><i class="bi bi-tags me-2"></i> Danh Mục</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/index.php?page=admin_orders"><i class="bi bi-bag me-2"></i> Đơn Hàng</a>
                        </li>
                    </ul>
                </div>

                <div class="user-profile d-flex align-items-center gap-3 pt-3 border-top">
                    <div class="avatar bg-gold text-white rounded-circle d-flex align-items-center justify-content-center fw-bold"><?php echo htmlspecialchars($adminInitials); ?></div>
                    <div>
                        <h6 class="mb-0 small fw-bold text-dark"><?php echo htmlspecialchars($adminName); ?></h6>
                        <small class="text-muted font-xs">Quản trị viên</small>
                    </div>
                </div>
            </div>
        </nav>

        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-5 py-5 main-content">
            <div class="d-flex justify-content-between align-items-end border-bottom pb-4 mb-5">
                <div>
                    <h1 class="page-title display-6 mb-2">Quản lý sản phẩm</h1>
                    <p class="text-muted mb-0">Quản lý bộ sưu tập, giá bán, tồn kho và trạng thái hiển thị.</p>
                </div>
                <div>
                    <a href="/index.php?page=admin_product_create" class="btn btn-gold text-nowrap py-2 px-4">
                        <i class="bi bi-plus-lg me-2"></i> THÊM SẢN PHẨM MỚI
                    </a>
                </div>
            </div>

            <?php if ($successMessage !== ""): ?>
                <div class="alert alert-success border-0 rounded-1 shadow-sm"><?php echo htmlspecialchars($successMessage); ?></div>
            <?php endif; ?>

            <?php if ($errorMessage !== ""): ?>
                <div class="alert alert-danger border-0 rounded-1 shadow-sm"><?php echo htmlspecialchars($errorMessage); ?></div>
            <?php endif; ?>

            <form action="/index.php" method="get" class="row g-5">
                <input type="hidden" name="page" value="admin_products">

                <aside class="col-lg-3">
                    <div class="filter-sidebar bg-white p-4 border rounded-1">
                        <h5 class="section-title mb-1">Lọc theo</h5>
                        <p class="text-muted font-xs mb-4">Thu hẹp phạm vi tìm kiếm</p>

                        <div class="mb-4">
                            <label class="form-label-custom" for="category_id">Danh mục</label>
                            <select class="form-select form-control-custom" id="category_id" name="category_id">
                                <option value="0">Tất cả danh mục</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?php echo (int) $category["category_id"]; ?>" <?php echo $selectedCategoryId === (int) $category["category_id"] ? "selected" : ""; ?>>
                                        <?php echo htmlspecialchars($category["category_name"]); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <hr class="my-4 border-color">

                        <div class="d-flex flex-column gap-2">
                            <a href="/index.php?page=admin_products" class="text-center text-muted font-xs py-2 text-decoration-none">Xóa tất cả</a>
                            <button class="btn btn-apply-filter w-100 py-2 fw-medium text-uppercase font-xs tracking-wider" type="submit">Áp dụng bộ lọc</button>
                        </div>
                    </div>
                </aside>

                <section class="col-lg-9">
                    <div class="d-flex justify-content-between align-items-center mb-4 gap-4">
                        <div class="position-relative flex-grow-1" style="max-width: 550px;">
                            <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                            <input
                                type="text"
                                name="keyword"
                                class="form-control bg-white ps-5 border py-2 font-xs"
                                placeholder="Tìm kiếm sản phẩm theo tên..."
                                value="<?php echo htmlspecialchars($keyword); ?>"
                            >
                        </div>

                        <div class="d-flex align-items-center gap-2 flex-shrink-0">
                            <select name="sort" class="form-select border bg-white font-xs text-muted py-2 px-3" style="min-width: 180px;">
                                <option value="newest" <?php echo $selectedSort === "newest" ? "selected" : ""; ?>>Sắp xếp: Mới nhất</option>
                                <option value="price_asc" <?php echo $selectedSort === "price_asc" ? "selected" : ""; ?>>Giá: Thấp đến Cao</option>
                                <option value="price_desc" <?php echo $selectedSort === "price_desc" ? "selected" : ""; ?>>Giá: Cao đến Thấp</option>
                            </select>
                            <button class="btn border bg-white py-2 px-3" type="submit"><i class="bi bi-filter-right"></i></button>
                        </div>
                    </div>

                    <div class="table-responsive bg-white border rounded-1">
                        <table class="table align-middle mb-0">
                            <thead class="table-light-bg font-xs tracking-wider text-muted text-uppercase">
                                <tr>
                                    <th class="ps-4 py-3" style="width: 40%;">Sản phẩm</th>
                                    <th class="py-3">Danh mục</th>
                                    <th class="py-3">Mã</th>
                                    <th class="py-3">Giá</th>
                                    <th class="py-3">Trạng thái</th>
                                    <th class="pe-4 py-3 text-end">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($products)): ?>
                                    <tr>
                                        <td colspan="6" class="text-center py-5 text-muted">Chưa có sản phẩm nào.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($products as $product): ?>
                                        <?php
                                        $productId = (int) $product["product_id"];
                                        $status = $product["status"];
                                        $nextStatus = $status === "show" ? "hide" : "show";
                                        $stockQuantity = (int) $product["stock_quantity"];
                                        $stockLabel = $stockQuantity <= 0 ? "HẾT HÀNG" : ($stockQuantity <= 3 ? "SẮP HẾT" : "CÒN HÀNG");
                                        $stockClass = $stockQuantity <= 0 ? "badge-outofstock" : ($stockQuantity <= 3 ? "badge-lowstock" : "badge-instock");
                                        ?>
                                        <tr class="border-bottom row-hover">
                                            <td class="ps-4 py-3">
                                                <div class="d-flex align-items-center gap-3">
                                                    <img src="<?php echo htmlspecialchars($product["main_image"]); ?>" class="product-thumb object-fit-cover" alt="<?php echo htmlspecialchars($product["product_name"]); ?>">
                                                    <div>
                                                        <h6 class="mb-1 fw-bold text-dark font-xs"><?php echo htmlspecialchars($product["product_name"]); ?></h6>
                                                        <p class="text-muted font-xs mb-0 text-truncate" style="max-width: 220px;"><?php echo htmlspecialchars($product["description"] ?? ""); ?></p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="font-xs text-muted align-middle"><?php echo htmlspecialchars($product["category_name"]); ?></td>
                                            <td class="font-xs text-muted font-monospace align-middle"><?php echo htmlspecialchars($product["sku"] ?: "SP-" . str_pad((string) $productId, 4, "0", STR_PAD_LEFT)); ?></td>
                                            <td class="font-numeric fw-medium align-middle"><?php echo number_format((int) $product["price"], 0, ",", "."); ?>đ</td>
                                            <td class="align-middle">
                                                <span class="badge badge-custom <?php echo $status === "show" ? $stockClass : "badge-outofstock"; ?>">
                                                    <?php echo $status === "show" ? $stockLabel : "ĐANG ẨN"; ?>
                                                </span>
                                            </td>
                                            <td class="pe-4 text-end align-middle">
                                                <a class="btn btn-sm btn-icon border-0" href="/index.php?page=admin_product_edit&id=<?php echo $productId; ?>" title="Sửa sản phẩm">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <form action="/index.php?page=admin_product_toggle" method="post" class="d-inline">
                                                    <input type="hidden" name="product_id" value="<?php echo $productId; ?>">
                                                    <input type="hidden" name="status" value="<?php echo htmlspecialchars($nextStatus); ?>">
                                                    <button class="btn btn-sm btn-icon border-0 <?php echo $nextStatus === "show" ? "text-success" : "text-danger"; ?>" type="submit" title="<?php echo $nextStatus === "show" ? "Hiện sản phẩm" : "Ẩn sản phẩm"; ?>">
                                                        <i class="bi <?php echo $nextStatus === "show" ? "bi-eye" : "bi-eye-slash"; ?>"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <span class="text-muted font-xs">Tổng số <?php echo count($products); ?> sản phẩm</span>
                    </div>
                </section>
            </form>

            <footer class="d-flex justify-content-between text-muted font-xs mt-5 pt-4 border-top">
                <span>&copy; 2026 Aurelia Fine Jewelry. All rights reserved.</span>
                <div class="d-flex gap-3">
                    <a href="#" class="text-muted text-decoration-none">Internal Wiki</a>
                    <a href="#" class="text-muted text-decoration-none">Tech Support</a>
                    <a href="#" class="text-muted text-decoration-none">Privacy Policy</a>
                </div>
            </footer>
        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
