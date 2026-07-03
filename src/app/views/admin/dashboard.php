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
    <title>Aurrelia Fine Jewelry - Admin Dashboard</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=Playfair+Display:wght@400;500;600&display=swap" rel="stylesheet">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    
    <link rel="stylesheet" href="/assets/admin/style.css">
    <link rel="icon" type="image/png" href="/favicon.png" />
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
                            <a class="nav-link active" href="/index.php?page=admin_dashboard"><i class="bi bi-grid-1x2 me-2"></i> Dashboard</a>
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
                            <a class="nav-link" href="/index.php?page=admin_users"><i class="bi bi-people me-2"></i> Người Dùng</a>
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

        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-5 py-4 main-content">
            
            <div class="d-flex justify-content-between align-items-center pt-3 pb-2 mb-4 border-bottom">
                <div>
                    <h1 class="page-title mb-1">Tổng quan </h1>
                    <p class="text-muted mb-0 small">Phân tích và hiệu suất sơ lược</p>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <div class="dropdown">
                        <button class="btn btn-gold dropdown-toggle" type="button" id="dropdownExportReport" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-download me-2"></i>Export Report
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 p-2" aria-labelledby="dropdownExportReport" style="min-width: 280px; font-size: 13px;">
                            <li><h6 class="dropdown-header text-uppercase tracking-wider font-xs text-muted fw-bold mb-1">Chọn loại báo cáo</h6></li>
                            <li>
                                <a class="dropdown-item rounded py-2" href="#" id="btnExportSales">
                                    <i class="bi bi-graph-up-arrow me-2 text-success"></i>1. Báo cáo Doanh thu & Doanh số
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item rounded py-2" href="#" id="btnExportInventory">
                                    <i class="bi bi-box-seam me-2 text-warning"></i>2. Báo cáo Sản phẩm & Tồn kho
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="row g-4 mb-4">
                <div class="col-md-3">
                    <div class="card p-3 border-0 shadow-sm">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div class="icon-wrapper"><i class="bi bi-wallet2 text-gold"></i></div>
                            <span class="trend-badge positive">+12.5%</span>
                        </div>
                        <small class="text-muted fw-semibold font-xs tracking-wider">TỔNG DOANH THU</small>
                        <h3 class="mt-1 mb-0 font-numeric"><?php echo number_format($totalSales, 0, ',', '.'); ?>₫</h3>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card p-3 border-0 shadow-sm">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div class="icon-wrapper"><i class="bi bi-cart3 text-gold"></i></div>
                            <span class="trend-badge positive">+8.2%</span>
                        </div>
                        <small class="text-muted fw-semibold font-xs tracking-wider">TỔNG ĐƠN HÀNG</small>
                        <h3 class="mt-1 mb-0 font-numeric"><?php echo number_format($totalOrders); ?></h3>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card p-3 border-0 shadow-sm">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div class="icon-wrapper"><i class="bi bi-people text-gold"></i></div>
                            <span class="trend-badge neutral">+4</span>
                        </div>
                        <small class="text-muted fw-semibold font-xs tracking-wider">KHÁCH HÀNG MỚI</small>
                        <h3 class="mt-1 mb-0 font-numeric"><?php echo number_format($activeCustomers); ?></h3>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card p-3 border-0 shadow-sm">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div class="icon-wrapper"><i class="bi bi-box-seam text-gold"></i></div>
                            <span class="trend-badge text-danger rounded-pill px-2" style="background-color: #fee2e2;">Cần nhập</span>
                        </div>
                        <small class="text-muted fw-semibold font-xs tracking-wider">CẢNH BÁO TỒN KHO</small>
                        <h3 class="mt-1 mb-0 font-numeric"><?php echo number_format($lowStock); ?></h3>
                    </div>
                </div>
            </div>

            <div class="row g-4 mb-4">
                <div class="col-lg-8">
                    <div class="card p-4 border-0 shadow-sm h-100">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="section-title mb-0">Xu Hướng Doanh Số</h5>
                            <select class="form-select form-select-sm w-auto border-0 bg-light text-muted">
                                <option>6 tháng gần đây</option>
                                <option>3 tháng gần đây</option>
                            </select>
                        </div>
                        <div class="chart-container" style="position: relative; height:300px;">
                            <canvas id="salesTrendsChart"></canvas>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4">
                    <div class="card p-4 border-0 shadow-sm h-100 d-flex flex-column">
                        <h5 class="section-title mb-4">Đơn Hàng Gần Đây</h5>
                        <div class="activity-list d-flex flex-column gap-3 mb-4">
                            <?php if (empty($recentOrders)): ?>
                                <p class="text-muted text-center py-4">Chưa có đơn hàng nào.</p>
                            <?php else: ?>
                                <?php foreach ($recentOrders as $o): ?>
                                    <div class="activity-item d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="icon-wrapper bg-light text-gold d-flex align-items-center justify-content-center" style="width:40px; height:40px; border-radius:8px;">
                                                <i class="bi bi-bag"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-0 small fw-bold">Đơn Hàng #<?php echo htmlspecialchars($o["order_code"]); ?></h6>
                                                <small class="text-muted font-xs">Khách hàng: <?php echo htmlspecialchars($o["full_name"]); ?></small>
                                            </div>
                                        </div>
                                        <div class="text-end">
                                            <span class="d-block small fw-bold text-gold"><?php echo number_format($o["final_amount"], 0, ',', '.'); ?>₫</span>
                                            <small class="text-muted font-xs"><?php echo date("H:i d/m", strtotime($o["created_at"])); ?></small>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                        
                        <a href="/index.php?page=admin_orders" class="btn btn-link text-gold btn-sm mt-auto text-center pt-3 border-top text-decoration-none fw-medium w-100">
                            Xem tất cả đơn hàng
                        </a>                    
                    </div>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-md-6">
                    <div class="card p-4 border-0 shadow-sm">
                        <h5 class="section-title mb-4">Hiệu suất của Bộ sưu tập</h5>
                        <div class="progress-stack d-flex flex-column gap-3">
                            <div>
                                <div class="d-flex justify-content-between small mb-1">
                                    <span class="fw-medium">Bông Tai Cao Cấp</span>
                                    <span class="text-muted fw-bold">45%</span>
                                </div>
                                <div class="progress" style="height: 6px;"><div class="progress-bar bg-gold" style="width: 45%"></div></div>
                            </div>
                            <div>
                                <div class="d-flex justify-content-between small mb-1">
                                    <span class="fw-medium">Dây Chuyền Vàng</span>
                                    <span class="text-muted fw-bold">32%</span>
                                </div>
                                <div class="progress" style="height: 6px;"><div class="progress-bar bg-gold" style="width: 32%"></div></div>
                            </div>
                            <div>
                                <div class="d-flex justify-content-between small mb-1">
                                    <span class="fw-medium">Nhẫn Kim Cương</span>
                                    <span class="text-muted fw-bold">23%</span>
                                </div>
                                <div class="progress" style="height: 6px;"><div class="progress-bar bg-gold" style="width: 23%"></div></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card p-4 border-0 shadow-sm">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="section-title mb-0">Cảnh báo tồn kho</h5>
                            <i class="bi bi-exclamation-circle text-danger"></i>
                        </div>
                        <div class="alert-list d-flex flex-column gap-3">
                            <?php if (empty($lowStockProducts)): ?>
                                <p class="text-muted text-center py-4">Tất cả sản phẩm đều đủ hàng.</p>
                            <?php else: ?>
                                <?php foreach ($lowStockProducts as $p): ?>
                                    <div class="d-flex justify-content-between align-items-center p-2 bg-light rounded">
                                        <div class="d-flex align-items-center gap-2">
                                            <img src="/<?php echo htmlspecialchars($p["main_image"]); ?>" style="width:35px; height:35px; object-fit:cover;" class="rounded">
                                            <div>
                                                <h6 class="mb-0 small fw-bold"><?php echo htmlspecialchars($p["product_name"]); ?></h6>
                                                <small class="text-muted font-xs">ID: <?php echo $p["product_id"]; ?></small>
                                            </div>
                                        </div>
                                        <div class="text-end">
                                            <span class="badge bg-danger bg-opacity-10 text-danger rounded-0 small d-block">Còn <?php echo $p["stock_quantity"]; ?></span>
                                            <small class="text-muted text-uppercase font-xs">NHẬP THÊM</small>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
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
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="/assets/admin/dashboard.js"></script>

</body>
</html>
