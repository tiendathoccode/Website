<?php
$adminName = $_SESSION["user_name"] ?? "Admin";
$adminInitials = strtoupper(substr($adminName, 0, 1));

$trendLabels = !empty($salesTrend) ? array_column($salesTrend, "month_name") : ["Jan", "Feb", "Mar", "Apr", "May", "Jun"];
$trendData = !empty($salesTrend) ? array_map("intval", array_column($salesTrend, "total_sales")) : [0, 0, 0, 0, 0, 0];

$maxVal = !empty($trendData) ? max($trendData) : 0;
if ($maxVal <= 0) {
    $trendMax = 1000000;
    $trendStepSize = 200000;
} else {
    $trendMax = max(1000000, (int)(ceil($maxVal / 1000000) * 1000000));
    $trendStepSize = max(200000, (int)ceil($trendMax / 5));
}

function formatCurrencyVnd($amount)
{
    return number_format((int)$amount, 0, ",", ".") . "đ";
}

function formatGrowthPercent($value)
{
    $value = (float)$value;
    $prefix = $value > 0 ? "+" : "";
    return $prefix . number_format($value, 1) . "%";
}

function formatCustomerGrowth($value)
{
    $value = (int)$value;
    return ($value > 0 ? "+" : "") . number_format($value);
}

function growthClass($value)
{
    return (float)$value >= 0 ? "positive" : "negative";
}

function timeAgoVi($createdAt)
{
    $createdTime = strtotime($createdAt);
    $diffSecs = time() - $createdTime;

    if ($diffSecs < 60) {
        return "Vừa xong";
    }

    if ($diffSecs < 3600) {
        return floor($diffSecs / 60) . " phút trước";
    }

    if ($diffSecs < 86400) {
        return floor($diffSecs / 3600) . " giờ trước";
    }

    return date("d/m H:i", $createdTime);
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

        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-5 py-4 main-content">
            <div class="d-flex justify-content-between align-items-center pt-3 pb-2 mb-4 border-bottom">
                <div>
                    <h1 class="page-title mb-1">Tổng quan</h1>
                    <p class="text-muted mb-0 small">Phân tích và hiệu suất sơ lược</p>
                </div>
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

            <div class="row g-4 mb-4">
                <div class="col-md-3">
                    <div class="card p-3 border-0 shadow-sm">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div class="icon-wrapper"><i class="bi bi-wallet2 text-gold"></i></div>
                            <span class="trend-badge <?php echo growthClass($cardStats["revenue_growth_percent"]); ?>">
                                <?php echo formatGrowthPercent($cardStats["revenue_growth_percent"]); ?>
                            </span>
                        </div>
                        <small class="text-muted fw-semibold font-xs tracking-wider">TỔNG DOANH THU</small>
                        <h3 class="mt-1 mb-0 font-numeric"><?php echo formatCurrencyVnd($cardStats["total_revenue"]); ?></h3>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card p-3 border-0 shadow-sm">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div class="icon-wrapper"><i class="bi bi-cart3 text-gold"></i></div>
                            <span class="trend-badge <?php echo growthClass($cardStats["orders_growth_percent"]); ?>">
                                <?php echo formatGrowthPercent($cardStats["orders_growth_percent"]); ?>
                            </span>
                        </div>
                        <small class="text-muted fw-semibold font-xs tracking-wider">TỔNG ĐƠN HÀNG</small>
                        <h3 class="mt-1 mb-0 font-numeric"><?php echo number_format($cardStats["total_orders"]); ?></h3>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card p-3 border-0 shadow-sm">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div class="icon-wrapper"><i class="bi bi-people text-gold"></i></div>
                            <span class="trend-badge neutral"><?php echo formatCustomerGrowth($cardStats["new_customers_growth"]); ?></span>
                        </div>
                        <small class="text-muted fw-semibold font-xs tracking-wider">KHÁCH HÀNG MỚI</small>
                        <h3 class="mt-1 mb-0 font-numeric"><?php echo number_format($cardStats["new_customers"]); ?></h3>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card p-3 border-0 shadow-sm">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div class="icon-wrapper"><i class="bi bi-box-seam text-gold"></i></div>
                            <span class="trend-badge bg-light text-muted rounded-pill px-2">Ổn định</span>
                        </div>
                        <small class="text-muted fw-semibold font-xs tracking-wider">SẢN PHẨM ĐANG BÁN</small>
                        <h3 class="mt-1 mb-0 font-numeric"><?php echo number_format($cardStats["total_products"]); ?></h3>
                    </div>
                </div>
            </div>

            <div class="row g-4 mb-4">
                <div class="col-lg-8">
                    <div class="card p-4 border-0 shadow-sm h-100">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="section-title mb-0">Xu Hướng Doanh Số</h5>
                            <select id="salesTrendRange" class="form-select form-select-sm w-auto border-0 bg-light text-muted">
                                <option value="6">Last 6 Months</option>
                                <option value="3">Last 3 Months</option>
                            </select>
                        </div>
                        <div class="chart-container" style="position: relative; height:300px;">
                            <canvas id="salesTrendsChart"></canvas>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card p-4 border-0 shadow-sm h-100 d-flex flex-column">
                        <h5 class="section-title mb-4">Hoạt Động Gần Đây</h5>
                        <div class="activity-list d-flex flex-column gap-3 mb-4">
                            <?php if (empty($recentOrders)): ?>
                                <div class="text-center py-5 text-muted font-xs">Chưa có giao dịch gần đây.</div>
                            <?php else: ?>
                                <?php foreach (array_slice($recentOrders, 0, 2) as $ro): ?>
                                    <?php $imgUrl = !empty($ro["main_image"]) ? $ro["main_image"] : "/assets/images/sp1.png"; ?>
                                    <div class="activity-item d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="item-img bg-light rounded" style="width:45px; height:45px; background: url('<?php echo htmlspecialchars($imgUrl); ?>') center/cover;"></div>
                                            <div>
                                                <h6 class="mb-0 small fw-bold">Đơn Hàng #<?php echo htmlspecialchars($ro["order_code"]); ?></h6>
                                                <small class="text-muted font-xs">
                                                    <?php echo !empty($ro["product_name"]) ? htmlspecialchars($ro["quantity"] . 'x ' . $ro["product_name"]) : "Chi tiết đơn hàng"; ?>
                                                </small>
                                            </div>
                                        </div>
                                        <div class="text-end">
                                            <span class="d-block small fw-bold"><?php echo formatCurrencyVnd($ro["final_amount"]); ?></span>
                                            <small class="text-muted font-xs"><?php echo timeAgoVi($ro["created_at"]); ?></small>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>

                        <button class="btn btn-link text-gold btn-sm mt-auto text-center pt-3 border-top text-decoration-none fw-medium w-100" data-bs-toggle="modal" data-bs-target="#allTransactionsModal">
                            Xem tất cả giao dịch
                        </button>
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
                                    <span class="fw-medium">Ethereal Collection (Necklaces)</span>
                                    <span class="text-muted fw-bold">45%</span>
                                </div>
                                <div class="progress" style="height: 6px;"><div class="progress-bar bg-gold" style="width: 45%"></div></div>
                            </div>
                            <div>
                                <div class="d-flex justify-content-between small mb-1">
                                    <span class="fw-medium">Classic Artisanal (Rings)</span>
                                    <span class="text-muted fw-bold">32%</span>
                                </div>
                                <div class="progress" style="height: 6px;"><div class="progress-bar bg-gold" style="width: 32%"></div></div>
                            </div>
                            <div>
                                <div class="d-flex justify-content-between small mb-1">
                                    <span class="fw-medium">Heirloom Pearls (Earrings)</span>
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
                                <div class="text-center py-4 text-muted font-xs">Tất cả sản phẩm đều đủ hàng.</div>
                            <?php else: ?>
                                <?php foreach ($lowStockProducts as $lp): ?>
                                    <div class="d-flex justify-content-between align-items-center p-2 bg-light rounded">
                                        <div>
                                            <h6 class="mb-0 small fw-bold"><?php echo htmlspecialchars($lp["product_name"]); ?></h6>
                                            <small class="text-muted font-xs">SKU: <?php echo htmlspecialchars($lp["sku"] ?? "N/A"); ?></small>
                                        </div>
                                        <div class="text-end">
                                            <span class="badge bg-danger bg-opacity-10 text-danger rounded-0 small d-block">Còn <?php echo (int)$lp["stock_quantity"]; ?></span>
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

<div class="modal fade" id="allTransactionsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom p-4">
                <h5 class="modal-title section-title fs-5">Tất cả giao dịch gần đây</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="custom-scrollbar" style="max-height: 400px; overflow-y: auto; padding-right: 10px;">
                    <div class="d-flex flex-column gap-3">
                        <?php if (empty($recentOrders)): ?>
                            <div class="text-center py-4 text-muted">Chưa có giao dịch nào.</div>
                        <?php else: ?>
                            <?php foreach ($recentOrders as $rt): ?>
                                <?php $imgUrl = !empty($rt["main_image"]) ? $rt["main_image"] : "/assets/images/sp1.png"; ?>
                                <div class="activity-item d-flex justify-content-between align-items-center p-2 rounded row-hover-effect">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="item-img bg-light rounded" style="width:45px; height:45px; background: url('<?php echo htmlspecialchars($imgUrl); ?>') center/cover;"></div>
                                        <div>
                                            <h6 class="mb-0 small fw-bold">Order #<?php echo htmlspecialchars($rt["order_code"]); ?></h6>
                                            <small class="text-muted font-xs"><?php echo !empty($rt["product_name"]) ? htmlspecialchars($rt["quantity"] . 'x ' . $rt["product_name"]) : "Chi tiết đơn hàng"; ?></small>
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <span class="d-block small fw-bold text-dark"><?php echo formatCurrencyVnd($rt["final_amount"]); ?></span>
                                        <small class="text-muted font-xs"><?php echo date("d/m/Y H:i", strtotime($rt["created_at"])); ?></small>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    window.salesTrendLabels = <?php echo json_encode($trendLabels); ?>;
    window.salesTrendData = <?php echo json_encode($trendData); ?>;
    window.salesTrendMax = <?php echo (int)$trendMax; ?>;
    window.salesTrendStepSize = <?php echo (int)$trendStepSize; ?>;
    window.salesTrendFormat = "vn";
</script>
<script src="/assets/admin/dashboard.js"></script>
</body>
</html>
