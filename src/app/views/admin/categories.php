<?php
$successMessage = $_SESSION["success_message"] ?? "";
$errorMessage = $_SESSION["error_message"] ?? "";
unset($_SESSION["success_message"], $_SESSION["error_message"]);

$adminName = $_SESSION["user_name"] ?? "Admin";
$adminInitials = strtoupper(substr($adminName, 0, 1));
$isEditing = !empty($editingCategory);
$formAction = $isEditing ? "/index.php?page=admin_category_update" : "/index.php?page=admin_category_store";
$formTitle = $isEditing ? "Chỉnh sửa danh mục" : "Thêm danh mục mới";
$submitLabel = $isEditing ? "Cập nhật danh mục" : "Thêm danh mục";
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aurrelia Fine Jewelry - Quản lý danh mục</title>

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
                            <a class="nav-link" href="/index.php?page=admin_products"><i class="bi bi-gem me-2"></i> Sản Phẩm</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="/index.php?page=admin_categories"><i class="bi bi-tags me-2"></i> Danh Mục</a>
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
                    <h1 class="page-title display-6 mb-2">Quản lý danh mục</h1>
                    <p class="text-muted mb-0">Tạo và kiểm soát các nhóm sản phẩm dùng cho phần quản lý trang sức.</p>
                </div>
            </div>

            <?php if ($successMessage !== ""): ?>
                <div class="alert alert-success border-0 rounded-1 shadow-sm"><?php echo htmlspecialchars($successMessage); ?></div>
            <?php endif; ?>

            <?php if ($errorMessage !== ""): ?>
                <div class="alert alert-danger border-0 rounded-1 shadow-sm"><?php echo htmlspecialchars($errorMessage); ?></div>
            <?php endif; ?>

            <div class="row g-4">
                <section class="col-lg-4">
                    <div class="card p-4 border-0 shadow-sm">
                        <h5 class="section-title mb-4"><?php echo htmlspecialchars($formTitle); ?></h5>

                        <form action="<?php echo htmlspecialchars($formAction); ?>" method="post">
                            <?php if ($isEditing): ?>
                                <input type="hidden" name="category_id" value="<?php echo (int) $editingCategory["category_id"]; ?>">
                            <?php endif; ?>

                            <div class="mb-3">
                                <label class="form-label-custom" for="category_name">Tên danh mục</label>
                                <input
                                    class="form-control-custom w-100"
                                    id="category_name"
                                    name="category_name"
                                    type="text"
                                    value="<?php echo htmlspecialchars($editingCategory["category_name"] ?? ""); ?>"
                                    required
                                >
                            </div>

                            <div class="mb-3">
                                <label class="form-label-custom" for="description">Mô tả</label>
                                <textarea class="form-control-custom w-100" id="description" name="description" rows="4"><?php echo htmlspecialchars($editingCategory["description"] ?? ""); ?></textarea>
                            </div>

                            <div class="mb-4">
                                <label class="form-label-custom" for="status">Trạng thái</label>
                                <?php $currentStatus = $editingCategory["status"] ?? "show"; ?>
                                <select class="form-select form-control-custom" id="status" name="status">
                                    <option value="show" <?php echo $currentStatus === "show" ? "selected" : ""; ?>>Hiển thị</option>
                                    <option value="hide" <?php echo $currentStatus === "hide" ? "selected" : ""; ?>>Ẩn</option>
                                </select>
                            </div>

                            <div class="d-flex gap-2">
                                <button class="btn btn-gold py-2 px-4" type="submit"><?php echo htmlspecialchars($submitLabel); ?></button>
                                <?php if ($isEditing): ?>
                                    <a class="btn btn-outline-custom py-2 px-3" href="/index.php?page=admin_categories">Hủy</a>
                                <?php endif; ?>
                            </div>
                        </form>
                    </div>
                </section>

                <section class="col-lg-8">
                    <div class="table-responsive bg-white border rounded-1">
                        <table class="table align-middle mb-0">
                            <thead class="table-light-bg font-xs tracking-wider text-muted text-uppercase">
                                <tr>
                                    <th class="ps-4 py-3">ID</th>
                                    <th class="py-3">Danh mục</th>
                                    <th class="py-3">Mô tả</th>
                                    <th class="py-3">Trạng thái</th>
                                    <th class="pe-4 py-3 text-end">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($categories)): ?>
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-5">Chưa có danh mục nào.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($categories as $category): ?>
                                        <?php
                                        $categoryId = (int) $category["category_id"];
                                        $status = $category["status"];
                                        $nextStatus = $status === "show" ? "hide" : "show";
                                        ?>
                                        <tr class="row-hover">
                                            <td class="ps-4 font-numeric"><?php echo $categoryId; ?></td>
                                            <td class="fw-semibold"><?php echo htmlspecialchars($category["category_name"]); ?></td>
                                            <td class="text-muted"><?php echo htmlspecialchars($category["description"] ?? ""); ?></td>
                                            <td>
                                                <span class="badge badge-custom <?php echo $status === "show" ? "badge-instock" : "badge-outofstock"; ?>">
                                                    <?php echo $status === "show" ? "HIỂN THỊ" : "ẨN"; ?>
                                                </span>
                                            </td>
                                            <td class="pe-4 text-end">
                                                <div class="d-inline-flex gap-2">
                                                    <a class="btn btn-sm btn-icon" href="/index.php?page=admin_categories&edit_id=<?php echo $categoryId; ?>" title="Sửa danh mục">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    <form action="/index.php?page=admin_category_toggle" method="post">
                                                        <input type="hidden" name="category_id" value="<?php echo $categoryId; ?>">
                                                        <input type="hidden" name="status" value="<?php echo htmlspecialchars($nextStatus); ?>">
                                                        <button class="btn btn-sm btn-icon <?php echo $nextStatus === "show" ? "text-success" : "text-danger"; ?>" type="submit" title="<?php echo $nextStatus === "show" ? "Hiện danh mục" : "Ẩn danh mục"; ?>">
                                                            <i class="bi <?php echo $nextStatus === "show" ? "bi-eye" : "bi-eye-slash"; ?>"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </section>
            </div>

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
