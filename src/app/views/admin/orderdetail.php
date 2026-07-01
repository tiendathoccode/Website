<?php
$successMessage = $_SESSION["success_message"] ?? "";
$errorMessage = $_SESSION["error_message"] ?? "";
unset($_SESSION["success_message"], $_SESSION["error_message"]);

$adminName = $_SESSION["user_name"] ?? "Admin";
$adminInitials = strtoupper(substr($adminName, 0, 1));

// Định nghĩa nhãn trạng thái và màu sắc tương ứng
$statusLabels = [
    "pending" => "Chờ xử lý",
    "processing" => "Đang xử lý",
    "shipping" => "Đang giao hàng",
    "delivered" => "Đã giao hàng",
    "cancelled" => "Đã hủy"
];

$statusColors = [
    "pending" => "text-warning bg-warning-subtle border-warning",
    "processing" => "text-info bg-info-subtle border-info",
    "shipping" => "text-primary bg-primary-subtle border-primary",
    "delivered" => "text-success bg-success-subtle border-success",
    "cancelled" => "text-danger bg-danger-subtle border-danger"
];
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aurrelia Fine Jewelry - Chi tiết đơn hàng #<?php echo htmlspecialchars($order["order_code"]); ?></title>
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=Playfair+Display:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="/assets/admin/style.css">
    <style>
        .badge-status {
            font-size: 12px;
            font-weight: 600;
            padding: 6px 16px;
            border-radius: 50px;
            border: 1px solid;
            display: inline-block;
        }
        .order-product-img {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 4px;
            border: 1px solid var(--border-color);
        }
        .detail-card {
            background: #ffffff;
            border-radius: 4px;
            border: 1px solid var(--border-color);
            padding: 24px;
            margin-bottom: 24px;
        }
        .info-label {
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            color: var(--text-muted);
            margin-bottom: 4px;
            letter-spacing: 0.05em;
        }
        .info-value {
            font-size: 14px;
            color: var(--text-dark);
            margin-bottom: 16px;
        }
        .info-value:last-child {
            margin-bottom: 0;
        }
        .btn-back {
            color: var(--text-muted);
            font-weight: 500;
            transition: all 0.2s;
        }
        .btn-back:hover {
            color: var(--text-dark);
            text-decoration: none;
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
            
            <div class="mb-4">
                <a href="/index.php?page=admin_orders" class="btn-back d-inline-flex align-items-center gap-2 text-decoration-none">
                    <i class="bi bi-arrow-left"></i> Quay lại danh sách đơn hàng
                </a>
            </div>

            <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4 pb-3 border-bottom">
                <div>
                    <h1 class="page-title display-6 mb-1">Chi tiết đơn hàng #<?php echo htmlspecialchars($order["order_code"]); ?></h1>
                    <p class="text-muted mb-0">Ngày đặt hàng: <?php echo date("d/m/Y H:i", strtotime($order["created_at"])); ?></p>
                </div>
                <div>
                    <span class="badge-status <?php echo $statusColors[$order["status"]] ?? "text-secondary bg-secondary-subtle border-secondary"; ?>">
                        <?php echo htmlspecialchars($statusLabels[$order["status"]] ?? "Không xác định"); ?>
                    </span>
                </div>
            </div>

            <?php if ($successMessage !== ""): ?>
                <div class="alert alert-success border-0 rounded-1 shadow-sm mb-4"><?php echo htmlspecialchars($successMessage); ?></div>
            <?php endif; ?>

            <?php if ($errorMessage !== ""): ?>
                <div class="alert alert-danger border-0 rounded-1 shadow-sm mb-4"><?php echo htmlspecialchars($errorMessage); ?></div>
            <?php endif; ?>

            <div class="row g-4">
                
                <!-- Cột trái: Chi tiết sản phẩm & Cập nhật trạng thái -->
                <div class="col-lg-8">
                    
                    <!-- Bảng sản phẩm -->
                    <div class="detail-card">
                        <h5 class="section-title mb-4 pb-2 border-bottom">Sản phẩm đã mua</h5>
                        <div class="table-responsive">
                            <table class="table align-middle mb-0">
                                <thead class="table-light-bg text-uppercase font-xs tracking-wider text-muted">
                                    <tr>
                                        <th scope="col" style="width: 50%;">Sản phẩm</th>
                                        <th scope="col" class="text-center">Đơn giá</th>
                                        <th scope="col" class="text-center">Số lượng</th>
                                        <th scope="col" class="text-end">Thành tiền</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($orderDetails as $item): ?>
                                        <?php 
                                        $subtotal = $item["price"] * $item["quantity"]; 
                                        ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center gap-3">
                                                    <img src="<?php echo htmlspecialchars($item["main_image"]); ?>" alt="<?php echo htmlspecialchars($item["product_name"]); ?>" class="order-product-img">
                                                    <div>
                                                        <div class="fw-semibold text-dark small"><?php echo htmlspecialchars($item["product_name"]); ?></div>
                                                        <div class="text-muted font-xs">SKU: <?php echo htmlspecialchars($item["sku"]); ?></div>
                                                        <?php if (!empty($item["selected_size"]) || !empty($item["selected_color"]) || !empty($item["selected_material"])): ?>
                                                            <div class="text-gold font-xs mt-1">
                                                                Tùy chọn: 
                                                                <?php 
                                                                $opts = [];
                                                                if (!empty($item["selected_size"])) $opts[] = "Kích cỡ: " . $item["selected_size"];
                                                                if (!empty($item["selected_color"])) $opts[] = "Màu: " . $item["selected_color"];
                                                                if (!empty($item["selected_material"])) $opts[] = "Chất liệu: " . $item["selected_material"];
                                                                echo htmlspecialchars(implode(" | ", $opts));
                                                                ?>
                                                            </div>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-center font-numeric"><?php echo number_format($item["price"], 0, ",", "."); ?>đ</td>
                                            <td class="text-center font-numeric"><?php echo (int)$item["quantity"]; ?></td>
                                            <td class="text-end font-numeric fw-semibold text-dark"><?php echo number_format($subtotal, 0, ",", "."); ?>đ</td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Form cập nhật nhanh trạng thái -->
                    <div class="detail-card">
                        <h5 class="section-title mb-4 pb-2 border-bottom">Xử lý đơn hàng</h5>
                        <form action="/index.php?page=admin_order_update_status" method="post" class="row align-items-end g-3">
                            <input type="hidden" name="order_id" value="<?php echo $order["order_id"]; ?>">
                            <input type="hidden" name="redirect" value="detail">
                            
                            <div class="col-md-8">
                                <label class="form-label form-label-custom">Thay đổi trạng thái đơn hàng</label>
                                <select name="status" class="form-select form-control-custom">
                                    <option value="pending" <?php echo $order["status"] === "pending" ? "selected" : ""; ?>>Chờ xử lý</option>
                                    <option value="processing" <?php echo $order["status"] === "processing" ? "selected" : ""; ?>>Đang xử lý</option>
                                    <option value="shipping" <?php echo $order["status"] === "shipping" ? "selected" : ""; ?>>Đang giao hàng</option>
                                    <option value="delivered" <?php echo $order["status"] === "delivered" ? "selected" : ""; ?>>Đã giao hàng</option>
                                    <option value="cancelled" <?php echo $order["status"] === "cancelled" ? "selected" : ""; ?>>Đã hủy đơn hàng</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-gold w-100 font-xs text-uppercase tracking-wider py-2">Cập nhật</button>
                            </div>
                        </form>
                    </div>

                </div>

                <!-- Cột phải: Thông tin khách hàng & Tổng hợp thanh toán -->
                <div class="col-lg-4">
                    
                    <!-- Thông tin khách hàng & Người nhận -->
                    <div class="detail-card">
                        <h5 class="section-title mb-4 pb-2 border-bottom">Thông tin giao hàng</h5>
                        
                        <div class="info-label">Khách hàng đặt</div>
                        <div class="info-value">
                            <div class="fw-semibold"><?php echo htmlspecialchars($order["full_name"] ?? "Khách vãng lai"); ?></div>
                            <div class="text-muted font-xs"><?php echo htmlspecialchars($order["email"] ?? "Không có email"); ?></div>
                        </div>

                        <div class="info-label">Người nhận hàng</div>
                        <div class="info-value">
                            <div class="fw-semibold"><?php echo htmlspecialchars($order["receiver_name"]); ?></div>
                            <div class="text-dark"><i class="bi bi-telephone me-1 text-muted"></i> <?php echo htmlspecialchars($order["receiver_phone"]); ?></div>
                        </div>

                        <div class="info-label">Địa chỉ giao hàng</div>
                        <div class="info-value text-dark font-xs">
                            <i class="bi bi-geo-alt me-1 text-muted"></i> <?php echo htmlspecialchars($order["shipping_address"]); ?>
                        </div>
                    </div>

                    <!-- Tóm tắt thanh toán -->
                    <div class="detail-card">
                        <h5 class="section-title mb-4 pb-2 border-bottom">Tóm tắt thanh toán</h5>
                        
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Tổng tiền hàng:</span>
                            <span class="font-numeric text-dark"><?php echo number_format($order["total_amount"], 0, ",", "."); ?>đ</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Giảm giá:</span>
                            <span class="font-numeric text-danger">-<?php echo number_format($order["discount_amount"], 0, ",", "."); ?>đ</span>
                        </div>
                        <div class="d-flex justify-content-between mb-3 pb-3 border-bottom">
                            <span class="text-muted">Phương thức:</span>
                            <span class="text-dark fw-medium text-uppercase font-xs"><?php echo $order["payment_method"] === "bank_transfer" ? "Chuyển khoản" : "COD (Tiền mặt)"; ?></span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fw-bold text-dark fs-6">Thành tiền:</span>
                            <span class="font-numeric text-gold fs-5 fw-bold"><?php echo number_format($order["final_amount"], 0, ",", "."); ?>đ</span>
                        </div>
                    </div>

                </div>

            </div>

        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
