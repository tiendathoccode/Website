<?php
$errorMessage = $_SESSION["error_message"] ?? "";
unset($_SESSION["error_message"]);

$adminName = $_SESSION["user_name"] ?? "Admin";
$adminInitials = strtoupper(substr($adminName, 0, 1));
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aurrelia Fine Jewelry - Sửa sản phẩm</title>

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
                        <small class="text-muted tracking-wider text-uppercase font-xs">Fine Jewelry Admin</small>
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

        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-5 py-4 main-content">
            <form action="/index.php?page=admin_product_update" method="post" enctype="multipart/form-data">
                <input type="hidden" name="product_id" value="<?php echo (int) $product["product_id"]; ?>">

                <div class="d-flex justify-content-between align-items-center pt-3 pb-3 mb-4 border-bottom">
                    <div>
                        <h1 class="page-title mb-1">Sửa sản phẩm</h1>
                        <p class="text-muted mb-0 small"><?php echo htmlspecialchars($product["product_name"]); ?></p>
                    </div>
                    <div class="d-flex align-items-center gap-3">
                        <a href="/index.php?page=admin_products" class="btn btn-outline-secondary border-color text-muted font-xs px-4 py-2">Hủy bỏ</a>
                        <button type="submit" class="btn btn-gold font-xs px-4 py-2">Cập nhật sản phẩm</button>
                    </div>
                </div>

                <?php if ($errorMessage !== ""): ?>
                    <div class="alert alert-danger border-0 rounded-1 shadow-sm"><?php echo htmlspecialchars($errorMessage); ?></div>
                <?php endif; ?>

                <div class="row g-4 mb-5">
                    <div class="col-lg-8">
                        <div class="card border-0 shadow-sm p-4 mb-4">
                            <h5 class="section-title mb-4">Thông tin cơ bản</h5>

                            <div class="mb-4">
                                <label class="form-label form-label-custom">TÊN SẢN PHẨM</label>
                                <input type="text" name="product_name" class="form-control form-control-custom" value="<?php echo htmlspecialchars($product["product_name"]); ?>" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label form-label-custom">MÔ TẢ SẢN PHẨM</label>
                                <div class="rich-text-editor border rounded">
                                    <textarea name="description" class="form-control border-0 shadow-none p-3 font-xs text-muted" rows="6"><?php echo htmlspecialchars($product["description"] ?? ""); ?></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="card border-0 shadow-sm p-4">
                            <h5 class="section-title mb-4">Hình ảnh</h5>

                            <div class="d-flex align-items-center gap-3 mb-3">
                                <img src="<?php echo htmlspecialchars($product["main_image"]); ?>" class="product-thumb object-fit-cover" alt="<?php echo htmlspecialchars($product["product_name"]); ?>">
                                <div>
                                    <div class="fw-semibold small">Ảnh hiện tại</div>
                                    <div class="text-muted font-xs"><?php echo htmlspecialchars($product["main_image"]); ?></div>
                                </div>
                            </div>

                            <div class="upload-zone border-dashed rounded text-center p-5 bg-light-custom position-relative">
                                <input type="file" name="main_image" class="position-absolute w-100 h-100 top-0 start-0 opacity-0 cursor-pointer" accept="image/jpeg,image/png,image/webp">
                                <div class="upload-icon mb-3">
                                    <i class="bi bi-cloud-arrow-up fs-2 text-gold"></i>
                                </div>
                                <h6 class="mb-2 font-xs fw-bold">Chọn ảnh mới nếu muốn thay</h6>
                                <p class="text-muted font-xs mb-3">Bỏ trống nếu muốn giữ ảnh hiện tại</p>
                                <button type="button" class="btn btn-outline-secondary border-color btn-sm font-xs px-3">Chọn tệp tin</button>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="card border-0 shadow-sm p-4 mb-4">
                            <h5 class="section-title mb-4">Giá bán & Kho hàng</h5>

                            <div class="mb-4">
                                <label class="form-label form-label-custom">MÃ SKU</label>
                                <input type="text" name="sku" class="form-control form-control-custom font-monospace" value="<?php echo htmlspecialchars($product["sku"]); ?>" required>
                            </div>

                            <div class="mb-4">
                                <label class="form-label form-label-custom">GIÁ BÁN (VNĐ)</label>
                                <input type="number" name="price" min="0" step="1000" class="form-control form-control-custom" value="<?php echo (int) $product["price"]; ?>" required>
                            </div>

                            <div class="mb-4">
                                <label class="form-label form-label-custom">GIÁ KHUYẾN MÃI (VNĐ)</label>
                                <input type="number" name="sale_price" min="0" step="1000" class="form-control form-control-custom" value="<?php echo (int) ($product["sale_price"] ?? 0); ?>">
                            </div>

                            <div class="mb-4">
                                <label class="form-label form-label-custom">SỐ LƯỢNG TỒN KHO</label>
                                <input type="number" name="stock_quantity" min="0" class="form-control form-control-custom" value="<?php echo (int) $product["stock_quantity"]; ?>" required>
                            </div>

                            <div>
                                <label class="form-label form-label-custom">TRẠNG THÁI</label>
                                <select name="status" class="form-select form-control-custom">
                                    <option value="show" <?php echo $product["status"] === "show" ? "selected" : ""; ?>>Hiển thị</option>
                                    <option value="hide" <?php echo $product["status"] === "hide" ? "selected" : ""; ?>>Ẩn</option>
                                </select>
                            </div>
                        </div>

                        <div class="card border-0 shadow-sm p-4">
                            <h5 class="section-title mb-4">Phân loại hàng hóa</h5>

                            <div class="mb-4">
                                <label class="form-label form-label-custom">DANH MỤC</label>
                                <select name="category_id" class="form-select form-control-custom font-xs text-muted" required>
                                    <?php foreach ($categories as $category): ?>
                                        <?php if (($category["status"] ?? "show") === "show" || (int) $category["category_id"] === (int) $product["category_id"]): ?>
                                            <option value="<?php echo (int) $category["category_id"]; ?>" <?php echo (int) $category["category_id"] === (int) $product["category_id"] ? "selected" : ""; ?>>
                                                <?php echo htmlspecialchars($category["category_name"]); ?>
                                            </option>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="mb-2">
                                <label class="form-label form-label-custom">CHẤT LIỆU</label>
                                <input type="text" name="material" class="form-control form-control-custom" value="<?php echo htmlspecialchars($material); ?>">
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
