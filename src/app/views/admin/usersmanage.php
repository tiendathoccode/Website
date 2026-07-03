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
    <title>Aurrelia Fine Jewelry - Quản lý người dùng</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=Playfair+Display:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="/assets/admin/style.css">
    <link rel="icon" type="image/png" href="/favicon.png" />
    <style>
        .status-badge-active {
            color: #2e7d32;
        }
        .status-badge-inactive {
            color: #c62828;
        }
    </style>
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
                            <a class="nav-link" href="/index.php?page=admin_products"><i class="bi bi-gem me-2"></i> Sản Phẩm</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/index.php?page=admin_categories"><i class="bi bi-tags me-2"></i> Danh Mục</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/index.php?page=admin_orders"><i class="bi bi-bag me-2"></i> Đơn Hàng</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="/index.php?page=admin_users"><i class="bi bi-people me-2"></i> Người Dùng</a>
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

        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4 main-content">
            
            <div class="d-flex justify-content-between align-items-center flex-wrap mb-4">
                <div>
                    <h2 class="page-title mb-1">Quản lý người dùng</h2>
                    <small class="text-muted">Cập nhật thông tin thành viên, phân quyền hoặc thay đổi trạng thái hoạt động.</small>
                </div>
            </div>

            <div class="card bg-white p-3 mb-4 shadow-sm border-0">
                <div class="row g-3 align-items-center">
                    <div class="col-12 col-md-4">
                        <div class="input-group">
                            <span class="input-group-text bg-light-custom border-end-0 text-muted"><i class="bi bi-search"></i></span>
                            <input type="text" id="searchUsers" class="form-control form-control-custom bg-light-custom border-start-0" placeholder="Tìm kiếm tên, email hoặc SĐT...">
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <select id="filterRole" class="form-select font-xs text-muted">
                            <option value="all">Tất cả vai trò</option>
                            <option value="admin">Quản trị viên (Admin)</option>
                            <option value="customer">Khách hàng</option>
                        </select>
                    </div>
                    <div class="col-6 col-md-3">
                        <select id="filterStatus" class="form-select font-xs text-muted">
                            <option value="all">Tất cả trạng thái</option>
                            <option value="active">Đang hoạt động</option>
                            <option value="locked">Đã khóa (Ban)</option>
                        </select>
                    </div>
                    <div class="col-12 col-md-2">
                        <button id="btnApplyFilters" class="btn btn-gold w-100 font-xs text-uppercase tracking-wider py-2">Áp dụng lọc</button>
                    </div>
                </div>
            </div>

            <div class="card bg-white border-0 shadow-sm overflow-hidden">
                <div class="table-responsive">
                    <table class="table align-middle mb-0" id="usersTable">
                        <thead class="bg-light-custom border-bottom">
                            <tr>
                                <th class="ps-4 font-xs text-muted text-uppercase tracking-wider py-3" style="width: 80px;">ID</th>
                                <th class="font-xs text-muted text-uppercase tracking-wider py-3">Họ và tên</th>
                                <th class="font-xs text-muted text-uppercase tracking-wider py-3">Email</th>
                                <th class="font-xs text-muted text-uppercase tracking-wider py-3">Số điện thoại</th>
                                <th class="font-xs text-muted text-uppercase tracking-wider py-3">Vai trò</th>
                                <th class="font-xs text-muted text-uppercase tracking-wider py-3">Trạng thái</th>
                                <th class="text-end pe-4 font-xs text-muted text-uppercase tracking-wider py-3" style="width: 180px;">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($users)): ?>
                                <tr>
                                    <td colspan="7" class="text-center py-5 text-muted font-xs">Không tìm thấy người dùng nào.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($users as $u): ?>
                                    <tr class="user-row" 
                                        data-id="<?php echo $u["user_id"]; ?>"
                                        data-name="<?php echo htmlspecialchars($u["full_name"]); ?>"
                                        data-email="<?php echo htmlspecialchars($u["email"]); ?>"
                                        data-phone="<?php echo htmlspecialchars($u["phone"]); ?>"
                                        data-role="<?php echo htmlspecialchars($u["role"]); ?>"
                                        data-status="<?php echo htmlspecialchars($u["status"]); ?>">
                                        <td class="ps-4 fw-semibold text-dark font-numeric">#<?php echo $u["user_id"]; ?></td>
                                        <td>
                                            <div class="fw-bold text-dark"><?php echo htmlspecialchars($u["full_name"]); ?></div>
                                        </td>
                                        <td><span class="text-muted"><?php echo htmlspecialchars($u["email"]); ?></span></td>
                                        <td><span class="text-muted font-numeric"><?php echo htmlspecialchars($u["phone"]); ?></span></td>
                                        <td>
                                            <span class="badge <?php echo $u["role"] === 'admin' ? 'bg-danger-subtle text-danger' : 'bg-primary-subtle text-primary'; ?> px-2 py-1" style="font-size: 11px;">
                                                <?php echo $u["role"] === 'admin' ? 'Admin' : 'Khách hàng'; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="status-badge">
                                                <i class="bi bi-circle-fill font-xs <?php echo $u["status"] === 'active' ? 'status-badge-active' : 'status-badge-inactive'; ?> me-1"></i>
                                                <?php echo $u["status"] === 'active' ? 'Đang hoạt động' : 'Đã khóa'; ?>
                                            </span>
                                        </td>
                                        <td class="text-end pe-4">
                                            <button class="btn btn-outline-custom font-xs btn-edit-user">CẬP NHẬT</button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</div>

<!-- ================= EDIT USER MODAL ================= -->
<div class="modal fade" id="editUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 450px;">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px;">
            <div class="modal-header border-0 pb-0" style="position: relative; display: block; border-bottom: none;">
                <h5 class="modal-title w-100 text-center fw-bold text-dark" style="font-family:'Playfair Display', serif; font-size:18px;">CẬP NHẬT NGƯỜI DÙNG</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="position: absolute; right: 15px; top: 15px; font-size:12px;"></button>
            </div>
            <div class="modal-body px-4 py-3">
                <input type="hidden" id="modalUserId">
                
                <div class="mb-3">
                    <label class="form-label-custom">Email đăng nhập (Không thể đổi)</label>
                    <input type="text" class="form-control form-control-custom bg-light" id="modalUserEmail" readonly>
                </div>
                
                <div class="mb-3">
                    <label class="form-label-custom">Họ và tên</label>
                    <input type="text" class="form-control form-control-custom" id="modalUserName" placeholder="Nhập tên đầy đủ...">
                </div>

                <div class="mb-3">
                    <label class="form-label-custom">Số điện thoại</label>
                    <input type="text" class="form-control form-control-custom" id="modalUserPhone" placeholder="Nhập số điện thoại...">
                </div>

                <div class="mb-3">
                    <label class="form-label-custom">Vai trò tài khoản</label>
                    <select class="form-select form-control-custom" id="modalUserRole">
                        <option value="customer">Khách hàng</option>
                        <option value="admin">Quản trị viên (Admin)</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label-custom">Trạng thái hoạt động</label>
                    <select class="form-select form-control-custom" id="modalUserStatus">
                        <option value="active">Đang hoạt động</option>
                        <option value="locked">Khóa tài khoản (Ban)</option>
                    </select>
                </div>
                
                <div class="text-end mt-4 pt-2 border-top border-light">
                    <button type="button" class="btn btn-outline-custom py-2 px-3 me-2" data-bs-dismiss="modal">Hủy bỏ</button>
                    <button type="button" class="btn btn-gold py-2 px-4" id="btnSaveUser">Lưu thay đổi</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Custom Toast Panel -->
<div id="custom-toast-container" style="position: fixed; top: 20px; right: 20px; z-index: 99999; display: flex; flex-direction: column; gap: 10px;"></div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="/assets/admin/usersmanage.js"></script>
</body>
</html>
