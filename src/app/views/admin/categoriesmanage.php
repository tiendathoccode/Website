<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION["user_logged_in"]) || $_SESSION["user_logged_in"] !== true || ($_SESSION["user_role"] ?? "") !== "admin") {
    header("Location: /index.php?page=login");
    exit();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aurrelia Fine Jewelry - Quản lý danh mục</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,400..900;1,400..900&display=swap" rel="stylesheet">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="/favicon.png" />
    
    <link rel="stylesheet" href="/assets/admin/style.css">
    <style>
        .table-custom {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 18px rgba(0,0,0,0.02);
            border: 1px solid var(--border-color);
        }
        .table-custom th {
            background-color: #faf6f0;
            color: #4a3e3d;
            font-weight: 600;
            padding: 15px 20px;
            font-size: 13px;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }
        .table-custom td {
            padding: 15px 20px;
            vertical-align: middle;
            font-size: 14px;
        }
        .toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
        }
        .badge-show {
            background-color: rgba(79, 122, 82, 0.1);
            color: #4f7a52;
            font-weight: 500;
            padding: 5px 12px;
            border-radius: 50px;
            font-size: 12px;
        }
        .badge-hide {
            background-color: rgba(179, 64, 58, 0.1);
            color: #b3403a;
            font-weight: 500;
            padding: 5px 12px;
            border-radius: 50px;
            font-size: 12px;
        }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row min-vh-100">
        
        <!-- SIDEBAR -->
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
                            <a class="nav-link" href="/index.php?page=admin_products"><i class="bi bi-gem me-2"></i> Sản Phẩm</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="/index.php?page=admin_categories"><i class="bi bi-tags me-2"></i> Danh Mục</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/index.php?page=admin_orders"><i class="bi bi-bag me-2"></i> Đơn Hàng</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/index.php?page=admin_users"><i class="bi bi-people me-2"></i> Người Dùng</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/index.php?page=admin_content"><i class="bi bi-layout-text-window me-2"></i> Nội Dung</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/index.php?page=admin_chat"><i class="bi bi-chat-dots me-2"></i> Tin nhắn</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-danger" href="/index.php?page=logout"><i class="bi bi-box-arrow-right me-2"></i> Đăng xuất</a>
                        </li>
                    </ul>         
                </div>
                
                <div class="user-profile d-flex align-items-center gap-3 pt-3 border-top">
                    <div class="avatar bg-gold text-white rounded-circle d-flex align-items-center justify-content-center fw-bold">
                        <?php 
                        $words = explode(" ", $_SESSION["user_name"]);
                        $initials = "";
                        foreach ($words as $w) {
                            $initials .= mb_substr($w, 0, 1, "UTF-8");
                        }
                        echo htmlspecialchars(mb_strtoupper(mb_substr($initials, -2, 2, "UTF-8"), "UTF-8"));
                        ?>
                    </div>
                    <div>
                        <h6 class="mb-0 small fw-bold text-dark"><?php echo htmlspecialchars($_SESSION["user_name"]); ?></h6>
                        <small class="text-muted font-xs"><?php echo htmlspecialchars(ucfirst($_SESSION["user_role"])); ?></small>
                    </div>
                </div>
            </div>
        </nav>

        <!-- MAIN CONTENT -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-5 py-4 main-content">
            
            <div class="d-flex justify-content-between align-items-end border-bottom pb-4 mb-5">
                <div>
                    <h1 class="page-title display-6 mb-2">Quản lý danh mục</h1>
                    <p class="text-muted mb-0">Quản lý các danh mục sản phẩm thời trang cao cấp của Aurrelia Fine Jewelry.</p>
                </div>
                <div>
                    <button class="btn btn-gold text-nowrap py-2 px-4" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                        <i class="bi bi-plus-lg me-2"></i> THÊM DANH MỤC MỚI
                    </button>
                </div>
            </div>

            <!-- BẢNG DANH MỤC -->
            <div class="table-responsive table-custom">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th style="width: 80px;">ID</th>
                            <th style="width: 250px;">Tên Danh Mục</th>
                            <th>Mô Tả</th>
                            <th style="width: 150px;">Trạng Thái</th>
                            <th style="width: 180px;">Ngày Tạo</th>
                            <th style="width: 150px; text-align: center;">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($categories)): ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">Chưa có danh mục nào.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($categories as $cat): ?>
                                <tr>
                                    <td class="fw-semibold">#<?php echo $cat["category_id"]; ?></td>
                                    <td class="fw-semibold text-dark"><?php echo htmlspecialchars($cat["category_name"]); ?></td>
                                    <td class="text-muted text-truncate" style="max-width: 300px;"><?php echo htmlspecialchars($cat["description"] ?? "Chưa có mô tả"); ?></td>
                                    <td>
                                        <?php if ($cat["status"] === "show"): ?>
                                            <span class="badge-show"><i class="bi bi-eye-fill me-1"></i> Hiển thị</span>
                                        <?php else: ?>
                                            <span class="badge-hide"><i class="bi bi-eye-slash-fill me-1"></i> Đang ẩn</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-muted small"><?php echo date("d/m/Y H:i", strtotime($cat["created_at"])); ?></td>
                                    <td style="text-align: center;">
                                        <div class="d-flex justify-content-center gap-2">
                                            <button class="btn btn-sm btn-outline-secondary px-3 btn-edit-cat" 
                                                    data-id="<?php echo $cat["category_id"]; ?>"
                                                    data-name="<?php echo htmlspecialchars($cat["category_name"]); ?>"
                                                    data-desc="<?php echo htmlspecialchars($cat["description"] ?? ""); ?>"
                                                    data-status="<?php echo $cat["status"]; ?>">
                                                <i class="bi bi-pencil-square"></i> Sửa
                                            </button>
                                            <button class="btn btn-sm btn-outline-danger px-3 btn-delete-cat" 
                                                    data-id="<?php echo $cat["category_id"]; ?>">
                                                <i class="bi bi-trash"></i> Xóa
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        </main>
    </div>
</div>

<!-- MODAL THÊM MỚI DANH MỤC -->
<div class="modal fade" id="addCategoryModal" tabindex="-1" aria-labelledby="addCategoryModalLabel" aria-hidden="true" style="font-family: 'Inter', sans-serif;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-bottom-0 pb-0" style="padding: 24px 24px 0;">
                <h5 class="modal-title fw-bold" id="addCategoryModalLabel" style="font-family: 'Playfair Display', serif; color: #4a3e3d;">Thêm Danh Mục Mới</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addCategoryForm">
                <div class="modal-body" style="padding: 24px;">
                    <div class="mb-3">
                        <label for="add_cat_name" class="form-label small fw-semibold text-muted">Tên Danh Mục <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="add_cat_name" name="category_name" required placeholder="Nhẫn, Dây Chuyền, Vòng Cổ...">
                    </div>
                    <div class="mb-3">
                        <label for="add_cat_desc" class="form-label small fw-semibold text-muted">Mô Tả</label>
                        <textarea class="form-control" id="add_cat_desc" name="description" rows="3" placeholder="Mô tả sơ lược về danh mục trang sức này..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="add_cat_status" class="form-label small fw-semibold text-muted">Trạng Thái Hiển Thị</label>
                        <select class="form-select" id="add_cat_status" name="status">
                            <option value="show" selected>Hiển thị trên Store</option>
                            <option value="hide">Ẩn trên Store</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0" style="padding: 0 24px 24px;">
                    <button type="button" class="btn btn-outline-secondary px-4 py-2" data-bs-dismiss="modal" style="font-size: 13px;">HỦY BỎ</button>
                    <button type="submit" class="btn btn-gold px-4 py-2" style="font-size: 13px;">THÊM MỚI</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL SỬA DANH MỤC -->
<div class="modal fade" id="editCategoryModal" tabindex="-1" aria-labelledby="editCategoryModalLabel" aria-hidden="true" style="font-family: 'Inter', sans-serif;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-bottom-0 pb-0" style="padding: 24px 24px 0;">
                <h5 class="modal-title fw-bold" id="editCategoryModalLabel" style="font-family: 'Playfair Display', serif; color: #4a3e3d;">Chỉnh Sửa Danh Mục</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editCategoryForm">
                <input type="hidden" id="edit_cat_id" name="category_id">
                <div class="modal-body" style="padding: 24px;">
                    <div class="mb-3">
                        <label for="edit_cat_name" class="form-label small fw-semibold text-muted">Tên Danh Mục <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit_cat_name" name="category_name" required placeholder="Nhẫn, Dây Chuyền, Vòng Cổ...">
                    </div>
                    <div class="mb-3">
                        <label for="edit_cat_desc" class="form-label small fw-semibold text-muted">Mô Tả</label>
                        <textarea class="form-control" id="edit_cat_desc" name="description" rows="3" placeholder="Mô tả sơ lược về danh mục trang sức này..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="edit_cat_status" class="form-label small fw-semibold text-muted">Trạng Thái Hiển Thị</label>
                        <select class="form-select" id="edit_cat_status" name="status">
                            <option value="show">Hiển thị trên Store</option>
                            <option value="hide">Ẩn trên Store</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0" style="padding: 0 24px 24px;">
                    <button type="button" class="btn btn-outline-secondary px-4 py-2" data-bs-dismiss="modal" style="font-size: 13px;">HỦY BỎ</button>
                    <button type="submit" class="btn btn-gold px-4 py-2" style="font-size: 13px;">LƯU THAY ĐỔI</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- TOAST NOTIFICATION CONTAINER -->
<div class="toast-container">
    <div id="liveToast" class="toast align-items-center text-white border-0 shadow-lg" role="alert" aria-live="assertive" aria-atomic="true" style="border-radius: 8px;">
        <div class="d-flex">
            <div class="toast-body d-flex align-items-center gap-2" style="font-size: 14px; padding: 12px 16px;">
                <i class="bi fs-5" id="toastIcon"></i>
                <span id="toastMessage"></span>
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Hàm hiển thị Toast
    function showToast(message, type = "success") {
        const toastEl = document.getElementById('liveToast');
        const toastMessage = document.getElementById('toastMessage');
        const toastIcon = document.getElementById('toastIcon');
        
        toastMessage.textContent = message;
        toastEl.className = 'toast align-items-center text-white border-0 shadow-lg';
        
        if (type === "success") {
            toastEl.classList.add('bg-success');
            toastIcon.className = 'bi bi-check-circle-fill';
        } else if (type === "error") {
            toastEl.classList.add('bg-danger');
            toastIcon.className = 'bi bi-exclamation-triangle-fill';
        } else {
            toastEl.classList.add('bg-info');
            toastIcon.className = 'bi bi-info-circle-fill';
        }
        
        const toast = new bootstrap.Toast(toastEl);
        toast.show();
    }

    // 1. Gắn sự kiện sửa danh mục
    document.querySelectorAll('.btn-edit-cat').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const name = this.getAttribute('data-name');
            const desc = this.getAttribute('data-desc');
            const status = this.getAttribute('data-status');
            
            document.getElementById('edit_cat_id').value = id;
            document.getElementById('edit_cat_name').value = name;
            document.getElementById('edit_cat_desc').value = desc;
            document.getElementById('edit_cat_status').value = status;
            
            const editModal = new bootstrap.Modal(document.getElementById('editCategoryModal'));
            editModal.show();
        });
    });

    // 2. Gửi form thêm danh mục qua AJAX
    document.getElementById('addCategoryForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        
        fetch('/index.php?page=admin_api_add_category', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'ok') {
                showToast(data.message, 'success');
                setTimeout(() => location.reload(), 1500);
            } else {
                showToast(data.message, 'error');
            }
        })
        .catch(err => {
            console.error(err);
            showToast('Lỗi kết nối máy chủ.', 'error');
        });
    });

    // 3. Gửi form sửa danh mục qua AJAX
    document.getElementById('editCategoryForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        
        fetch('/index.php?page=admin_api_edit_category', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'ok') {
                showToast(data.message, 'success');
                setTimeout(() => location.reload(), 1500);
            } else {
                showToast(data.message, 'error');
            }
        })
        .catch(err => {
            console.error(err);
            showToast('Lỗi kết nối máy chủ.', 'error');
        });
    });

    // 4. Xóa danh mục qua AJAX
    document.querySelectorAll('.btn-delete-cat').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            if (confirm('Bạn có chắc chắn muốn xóa danh mục này? Điều này có thể ảnh hưởng đến hiển thị sản phẩm.')) {
                const formData = new FormData();
                formData.append('category_id', id);
                
                fetch('/index.php?page=admin_api_delete_category', {
                    method: 'POST',
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'ok') {
                        showToast(data.message, 'success');
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        showToast(data.message, 'error');
                    }
                })
                .catch(err => {
                    console.error(err);
                    showToast('Lỗi kết nối máy chủ.', 'error');
                });
            }
        });
    });
</script>

</body>
</html>
