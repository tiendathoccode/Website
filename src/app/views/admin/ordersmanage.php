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
    <title>Aurrelia Fine Jewelry - Quản lý đơn hàng</title>
    
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
                            <a class="nav-link" href="/index.php?page=admin_products"><i class="bi bi-gem me-2"></i> Sản Phẩm</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="/index.php?page=admin_orders"><i class="bi bi-bag me-2"></i> Đơn Hàng</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/index.php?page=admin_users"><i class="bi bi-people me-2"></i> Người Dùng</a>
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
                    <h2 class="page-title mb-1">Quản lý đơn hàng</h2>
                    <small class="text-muted">Theo dõi, lọc và xử lý các đơn hàng cao cấp từ cơ sở dữ liệu.</small>
                </div>
            </div>

            <div class="card bg-white p-3 mb-4 shadow-sm border-0">
                <div class="row g-3 align-items-center">
                    <div class="col-12 col-md-4">
                        <div class="input-group">
                            <span class="input-group-text bg-light-custom border-end-0 text-muted"><i class="bi bi-search"></i></span>
                            <input type="text" id="searchOrders" class="form-control form-control-custom bg-light-custom border-start-0" placeholder="Tìm kiếm mã đơn hàng hoặc tên khách hàng...">
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <select id="filterPayment" class="form-select font-xs text-muted">
                            <option value="all">Tất cả thanh toán</option>
                            <option value="cod">Thanh toán khi nhận hàng (COD)</option>
                            <option value="bank_transfer">Chuyển khoản ngân hàng</option>
                        </select>
                    </div>
                    <div class="col-6 col-md-3">
                        <select id="filterFulfillment" class="form-select font-xs text-muted">
                            <option value="all">Tất cả trạng thái giao</option>
                            <option value="pending">Chờ xử lý</option>
                            <option value="processing">Đang xử lý</option>
                            <option value="shipping">Đang giao hàng</option>
                            <option value="delivered">Đã giao hàng</option>
                            <option value="cancelled">Đã hủy</option>
                            <option value="return_requested">Yêu cầu hoàn trả (Đang thu hồi)</option>
                            <option value="returned">Đã hoàn trả thành công</option>
                        </select>
                    </div>
                    <div class="col-12 col-md-2">
                        <button id="btnApplyFilters" class="btn btn-gold w-100 font-xs text-uppercase tracking-wider py-2">Áp dụng lọc</button>
                    </div>
                </div>
            </div>

            <div class="card bg-white border-0 shadow-sm overflow-hidden">
                <div class="table-responsive table-custom-wrapper">
                    <table class="table align-middle mb-0" id="ordersTable">
                        <thead class="table-light-bg text-uppercase font-xs tracking-wider text-muted">
                            <tr>
                                <th scope="col" class="ps-4" style="width: 50px;">
                                    <div class="form-check custom-checkbox">
                                        <input class="form-check-input" type="checkbox" id="selectAll">
                                    </div>
                                </th>
                                <th scope="col">Mã đơn hàng</th>
                                <th scope="col">Khách hàng</th>
                                <th scope="col">Ngày đặt</th>
                                <th scope="col">Tổng tiền</th>
                                <th scope="col">Phương thức</th>
                                <th scope="col">Trạng thái</th>
                                <th scope="col" class="text-end pe-4">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($orders)): ?>
                                <tr>
                                    <td colspan="8" class="text-center py-5 text-muted">
                                        <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                                        Không có đơn hàng nào được tìm thấy
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($orders as $o): ?>
                                    <tr class="row-hover" 
                                        data-id="<?php echo htmlspecialchars($o["order_code"]); ?>" 
                                        data-customer="<?php echo htmlspecialchars($o["full_name"]); ?>" 
                                        data-payment="<?php echo htmlspecialchars($o["payment_method"]); ?>" 
                                        data-fulfillment="<?php echo htmlspecialchars($o["status"]); ?>"
                                        data-db-id="<?php echo htmlspecialchars($o["order_id"]); ?>">
                                        <td class="ps-4">
                                            <div class="form-check custom-checkbox">
                                                <input class="form-check-input" type="checkbox">
                                            </div>
                                        </td>
                                        <td><span class="fw-bold text-dark">#<?php echo htmlspecialchars($o["order_code"]); ?></span></td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="avatar-sm bg-light-custom rounded-circle d-flex align-items-center justify-content-center fw-medium text-secondary font-xs">
                                                    <?php echo htmlspecialchars(mb_substr($o["full_name"], 0, 2, "UTF-8")); ?>
                                                </div>
                                                <div>
                                                    <div class="fw-medium text-dark small"><?php echo htmlspecialchars($o["full_name"]); ?></div>
                                                    <div class="text-muted font-xs">SĐT: <?php echo htmlspecialchars($o["receiver_phone"]); ?></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-muted font-xs"><?php echo date("d/m/Y H:i", strtotime($o["created_at"])); ?></td>
                                        <td class="font-numeric text-dark fw-bold"><?php echo number_format($o["final_amount"], 0, ',', '.'); ?>₫</td>
                                        <td>
                                            <span class="status-badge">
                                                <?php if ($o["payment_method"] === "cod"): ?>
                                                    <i class="bi bi-cash status-paid me-1"></i> COD
                                                <?php else: ?>
                                                    <i class="bi bi-credit-card status-pending me-1"></i> Chuyển khoản
                                                <?php endif; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php
                                            $statusClass = "status-pending";
                                            $statusText = "Chờ xử lý";
                                            if ($o["status"] === "processing") {
                                                $statusClass = "status-processing";
                                                $statusText = "Đang xử lý";
                                            } elseif ($o["status"] === "shipping") {
                                                $statusClass = "status-processing";
                                                $statusText = "Đang giao";
                                            } elseif ($o["status"] === "delivered") {
                                                $statusClass = "status-shipped";
                                                $statusText = "Đã giao";
                                            } elseif ($o["status"] === "cancelled") {
                                                $statusClass = "status-cancelled";
                                                $statusText = "Đã hủy";
                                            } elseif ($o["status"] === "return_requested") {
                                                $statusClass = "status-pending"; // using yellow color
                                                $statusText = "Yêu cầu hoàn trả (Đang thu hồi)";
                                            } elseif ($o["status"] === "returned") {
                                                $statusClass = "status-returned";
                                                $statusText = "Đã hoàn trả thành công";
                                            }
                                            ?>
                                            <span class="status-badge"><i class="bi bi-circle-fill font-xs <?php echo $statusClass; ?> me-1"></i> <?php echo $statusText; ?></span>
                                        </td>
                                        <td class="text-end pe-4">
                                            <button class="btn btn-outline-custom font-xs btn-update-status">CẬP NHẬT TRẠNG THÁI</button>
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

<div class="modal fade" id="updateStatusModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 400px;">
        <div class="modal-content card shadow-sm border-0 p-2">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title section-title fw-bold text-dark tracking-wider fs-5" id="modalOrderId">Cập nhật đơn hàng</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-3">
                <div class="mb-3">
                    <label class="form-label-custom">Phương thức thanh toán</label>
                    <select class="form-select form-control-custom" id="modalSelectPayment">
                        <option value="cod">Thanh toán khi nhận hàng (COD)</option>
                        <option value="bank_transfer">Chuyển khoản ngân hàng</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label-custom">Trạng thái đơn hàng</label>
                    <select class="form-select form-control-custom" id="modalSelectFulfillment">
                        <option value="pending">Chờ xử lý</option>
                        <option value="processing">Đang xử lý</option>
                        <option value="shipping">Đang giao hàng</option>
                        <option value="delivered">Đã giao hàng</option>
                        <option value="cancelled">Đã hủy</option>
                        <option value="return_requested">Yêu cầu hoàn trả (Đang thu hồi)</option>
                        <option value="returned">Đã hoàn trả thành công</option>
                    </select>
                </div>
                <div class="text-end mt-4 pt-2 border-top border-light">
                    <button type="button" class="btn btn-outline-custom py-2 px-3 me-2" data-bs-dismiss="modal">Hủy bỏ</button>
                    <button type="button" class="btn btn-gold py-2 px-4" id="btnSaveStatus">Lưu thay đổi</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="/assets/admin/ordersmanage.js"></script>
</body>
</html>
