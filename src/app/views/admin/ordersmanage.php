<?php
$successMessage = $_SESSION["success_message"] ?? "";
$errorMessage = $_SESSION["error_message"] ?? "";
unset($_SESSION["success_message"], $_SESSION["error_message"]);

$adminName = $_SESSION["user_name"] ?? "Admin";
$adminInitials = strtoupper(substr($adminName, 0, 1));
$keyword = $filters["keyword"] ?? "";
$selectedStatus = $filters["status"] ?? "all";
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
                        <small class="text-muted font-xs tracking-wider text-uppercase">Fine Jewelry Admin</small>
                    </div>
                    
                    <ul class="nav flex-column gap-2 mt-4">
                        <li class="nav-item">
                            <a class="nav-link" href="/index.php?page=admin_dashboard">
                                <i class="bi bi-grid-1x2 me-2"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/index.php?page=admin_products">
                                <i class="bi bi-gem me-2"></i> Sản Phẩm
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/index.php?page=admin_categories">
                                <i class="bi bi-tags me-2"></i> Danh Mục
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="/index.php?page=admin_orders">
                                <i class="bi bi-bag me-2"></i> Đơn Hàng
                            </a>
                        </li>
                    </ul>
                </div>
                
                <div class="user-profile d-flex align-items-center gap-3 pt-3 border-top">
                    <div class="avatar bg-gold text-white rounded-circle d-flex align-items-center justify-content-center fw-bold">
                        <?php echo htmlspecialchars($adminInitials); ?>
                    </div>
                    <div>
                        <h6 class="mb-0 small fw-bold text-dark"><?php echo htmlspecialchars($adminName); ?></h6>
                        <small class="text-muted font-xs">Quản trị viên</small>
                    </div>
                </div>
            </div>
        </nav>

        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-5 py-5 main-content">
            
            <div class="d-flex justify-content-between align-items-center flex-wrap mb-4 pb-3 border-bottom">
                <div>
                    <h1 class="page-title display-6 mb-1">Quản lý đơn hàng</h1>
                    <p class="text-muted mb-0">Theo dõi, lọc và xử lý các đơn hàng cao cấp.</p>
                </div>
            </div>

            <?php if ($successMessage !== ""): ?>
                <div class="alert alert-success border-0 rounded-1 shadow-sm"><?php echo htmlspecialchars($successMessage); ?></div>
            <?php endif; ?>

            <?php if ($errorMessage !== ""): ?>
                <div class="alert alert-danger border-0 rounded-1 shadow-sm"><?php echo htmlspecialchars($errorMessage); ?></div>
            <?php endif; ?>

            <form action="/index.php" method="get" class="card bg-white p-3 mb-4 shadow-sm border-0">
                <input type="hidden" name="page" value="admin_orders">
                <div class="row g-3 align-items-center">
                    <div class="col-12 col-md-5">
                        <div class="input-group">
                            <span class="input-group-text bg-light-custom border-end-0 text-muted"><i class="bi bi-search"></i></span>
                            <input type="text" name="keyword" class="form-control form-control-custom bg-light-custom border-start-0" placeholder="Tìm kiếm mã đơn hàng hoặc tên khách hàng..." value="<?php echo htmlspecialchars($keyword); ?>">
                        </div>
                    </div>
                    <div class="col-6 col-md-4">
                        <select name="status" class="form-select font-xs text-muted">
                            <option value="all" <?php echo $selectedStatus === 'all' ? 'selected' : ''; ?>>Tất cả trạng thái</option>
                            <option value="pending" <?php echo $selectedStatus === 'pending' ? 'selected' : ''; ?>>Chờ xử lý</option>
                            <option value="processing" <?php echo $selectedStatus === 'processing' ? 'selected' : ''; ?>>Đang xử lý</option>
                            <option value="shipping" <?php echo $selectedStatus === 'shipping' ? 'selected' : ''; ?>>Đang giao hàng</option>
                            <option value="delivered" <?php echo $selectedStatus === 'delivered' ? 'selected' : ''; ?>>Đã giao hàng</option>
                            <option value="cancelled" <?php echo $selectedStatus === 'cancelled' ? 'selected' : ''; ?>>Đã hủy</option>
                        </select>
                    </div>
                    <div class="col-6 col-md-3">
                        <button type="submit" class="btn btn-gold w-100 font-xs text-uppercase tracking-wider py-2">Lọc đơn hàng</button>
                    </div>
                </div>
            </form>

            <div class="card bg-white border-0 shadow-sm overflow-hidden">
                <div class="table-responsive table-custom-wrapper">
                    <table class="table align-middle mb-0" id="ordersTable">
                        <thead class="table-light-bg text-uppercase font-xs tracking-wider text-muted">
                            <tr>
                                <th scope="col" class="ps-4">Mã đơn hàng</th>
                                <th scope="col">Khách hàng</th>
                                <th scope="col">Ngày đặt</th>
                                <th scope="col">Tổng tiền</th>
                                <th scope="col">Thanh toán</th>
                                <th scope="col">Giao hàng</th>
                                <th scope="col" class="text-end pe-4">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($orders)): ?>
                                <tr>
                                    <td colspan="7" class="text-center py-5 text-muted">Không tìm thấy đơn hàng nào.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($orders as $o): ?>
                                    <?php
                                    // Map trạng thái thanh toán
                                    $paymentStatus = "Chờ thanh toán";
                                    $paymentClass = "status-pending";
                                    if ($o["status"] === "delivered") {
                                        $paymentStatus = "Đã thanh toán";
                                        $paymentClass = "status-paid";
                                    } elseif ($o["payment_method"] === "bank_transfer" && $o["status"] !== "pending" && $o["status"] !== "cancelled") {
                                        $paymentStatus = "Đã thanh toán";
                                        $paymentClass = "status-paid";
                                    }

                                    // Map trạng thái giao hàng
                                    $fulfillmentStatus = "Chờ xử lý";
                                    $fulfillmentClass = "status-pending";
                                    if ($o["status"] === "delivered") {
                                        $fulfillmentStatus = "Đã giao hàng";
                                        $fulfillmentClass = "status-shipped";
                                    } elseif ($o["status"] === "shipping") {
                                        $fulfillmentStatus = "Đang giao hàng";
                                        $fulfillmentClass = "status-shipped";
                                    } elseif ($o["status"] === "processing") {
                                        $fulfillmentStatus = "Đang xử lý";
                                        $fulfillmentClass = "status-processing";
                                    } elseif ($o["status"] === "cancelled") {
                                        $fulfillmentStatus = "Đã hủy";
                                        $fulfillmentClass = "status-processing";
                                    }

                                    $customerInitials = "";
                                    if (!empty($o["full_name"])) {
                                        $words = explode(" ", $o["full_name"]);
                                        $customerInitials = strtoupper(substr(end($words), 0, 1));
                                        if (count($words) > 1) {
                                            $customerInitials = strtoupper(substr($words[0], 0, 1)) . $customerInitials;
                                        }
                                    } else {
                                        $customerInitials = "KH";
                                    }
                                    ?>
                                    <tr class="row-hover">
                                        <td class="ps-4">
                                            <a href="/index.php?page=admin_order_detail&order_id=<?php echo $o["order_id"]; ?>" class="fw-bold text-dark text-decoration-none">
                                                #<?php echo htmlspecialchars($o["order_code"]); ?>
                                            </a>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="avatar-sm bg-gold text-white rounded-circle d-flex align-items-center justify-content-center fw-medium font-xs">
                                                    <?php echo htmlspecialchars($customerInitials); ?>
                                                </div>
                                                <div>
                                                    <div class="fw-medium text-dark small"><?php echo htmlspecialchars($o["receiver_name"]); ?></div>
                                                    <div class="text-muted font-xs"><?php echo htmlspecialchars($o["email"] ?? "Khách vãng lai"); ?></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-muted font-xs"><?php echo date("d/m/Y H:i", strtotime($o["created_at"])); ?></td>
                                        <td class="font-numeric text-dark"><?php echo number_format($o["final_amount"], 0, ",", "."); ?>đ</td>
                                        <td>
                                            <span class="status-badge"><i class="bi bi-circle-fill font-xs <?php echo $paymentClass; ?> me-1"></i> <?php echo $paymentStatus; ?></span>
                                        </td>
                                        <td>
                                            <span class="status-badge"><i class="bi bi-circle-fill font-xs <?php echo $fulfillmentClass; ?> me-1"></i> <?php echo $fulfillmentStatus; ?></span>
                                        </td>
                                        <td class="text-end pe-4">
                                            <div class="d-inline-flex gap-2">
                                                <a href="/index.php?page=admin_order_detail&order_id=<?php echo $o["order_id"]; ?>" class="btn btn-outline-custom font-xs">CHI TIẾT</a>
                                                <button class="btn btn-gold font-xs btn-update-status" 
                                                        data-id="<?php echo $o["order_id"]; ?>" 
                                                        data-code="<?php echo htmlspecialchars($o["order_code"]); ?>"
                                                        data-status="<?php echo htmlspecialchars($o["status"]); ?>">
                                                    TRẠNG THÁI
                                                </button>
                                            </div>
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
        <form action="/index.php?page=admin_order_update_status" method="post" class="modal-content border-0 rounded-1 shadow">
            <input type="hidden" name="order_id" id="modalOrderIdInput">
            <div class="modal-header border-bottom-0 pb-0 pt-4 px-4">
                <h5 class="section-title fs-5 mb-0" id="modalOrderIdText">Cập nhật trạng thái</h5>
                <button type="button" class="btn-close font-xs" data-bs-shadow="none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body px-4 pt-3 pb-4">
                <div class="mb-3">
                    <label class="form-label form-label-custom">Trạng thái đơn hàng</label>
                    <select name="status" id="modalSelectStatus" class="form-select form-control-custom">
                        <option value="pending">Chờ xử lý</option>
                        <option value="processing">Đang xử lý</option>
                        <option value="shipping">Đang giao hàng</option>
                        <option value="delivered">Đã giao hàng</option>
                        <option value="cancelled">Đã hủy đơn hàng</option>
                    </select>
                </div>
                <div class="d-flex gap-2 mt-4">
                    <button type="button" class="btn btn-light-custom text-dark font-xs flex-grow-1 border" data-bs-dismiss="modal">Hủy bỏ</button>
                    <button type="submit" class="btn btn-gold font-xs flex-grow-1 text-uppercase tracking-wider">Lưu thay đổi</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    const updateModalEl = document.getElementById("updateStatusModal");
    let updateModal = null;
    if (updateModalEl) {
        updateModal = new bootstrap.Modal(updateModalEl);
    }

    const modalOrderIdText = document.getElementById("modalOrderIdText");
    const modalOrderIdInput = document.getElementById("modalOrderIdInput");
    const modalSelectStatus = document.getElementById("modalSelectStatus");

    const updateBtns = document.querySelectorAll(".btn-update-status");
    updateBtns.forEach(btn => {
        btn.addEventListener("click", function () {
            const orderId = this.getAttribute("data-id");
            const orderCode = this.getAttribute("data-code");
            const currentStatus = this.getAttribute("data-status");

            if (modalOrderIdText) modalOrderIdText.textContent = `Cập nhật đơn #${orderCode}`;
            if (modalOrderIdInput) modalOrderIdInput.value = orderId;
            if (modalSelectStatus) modalSelectStatus.value = currentStatus;

            if (updateModal) updateModal.show();
        });
    });
});
</script>
</body>
</html>
