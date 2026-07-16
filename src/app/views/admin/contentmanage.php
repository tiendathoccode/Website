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
    <title>Aurrelia Fine Jewelry - Quản lý Nội dung</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=Playfair+Display:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="/assets/admin/style.css">
    <link rel="stylesheet" href="/assets/css/contentmanage.css">
    <link rel="icon" type="image/png" href="/favicon.png" />
</head>
<body>
<div class="container-fluid">
    <div class="row min-vh-100">

        <!-- SIDEBAR -->
        <!-- Sidebar Navigation -->
        <nav class="col-md-3 col-lg-2 sidebar border-end p-4">
            <!-- Mobile Header with Hamburger Trigger -->
            <div class="d-flex justify-content-between align-items-center d-md-none mb-2">
                <a href="/index.php?page=admin_dashboard" class="text-decoration-none"><h3 class="brand-logo mb-0">AURRELIA</h3></a>
                <button class="btn btn-link text-dark p-0 border-0" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarMenu" aria-controls="sidebarMenu" aria-expanded="false" aria-label="Toggle navigation">
                    <i class="bi bi-list fs-2"></i>
                </button>
            </div>
            
            <!-- Collapsible Sidebar Content -->
            <div class="collapse d-md-block" id="sidebarMenu">
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
                                <a class="nav-link" href="/index.php?page=admin_users"><i class="bi bi-people me-2"></i> Người Dùng</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link active" href="/index.php?page=admin_content"><i class="bi bi-layout-text-window me-2"></i> Nội Dung</a>
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
            </div>
        </nav>

        <!-- MAIN CONTENT -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-5 py-4 main-content">

            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center pt-3 pb-2 mb-4 border-bottom">
                <div>
                    <h1 class="page-title mb-1">Quản lý Nội dung</h1>
                    <p class="text-muted mb-0 small">Banner · Đánh giá · FAQ · Tin nhắn</p>
                </div>
            </div>

            <!-- Tab Navigation -->
            <ul class="nav content-tabs gap-1 mb-4" id="contentTabs">
                <li class="nav-item">
                    <button class="content-tab-btn active" data-tab="banners">
                        <i class="bi bi-images me-2"></i>Banner
                    </button>
                </li>
                <li class="nav-item">
                    <button class="content-tab-btn" data-tab="reviews">
                        <i class="bi bi-star me-2"></i>Đánh giá
                        <span class="tab-badge" id="pendingReviewBadge">3</span>
                    </button>
                </li>
                <li class="nav-item">
                    <button class="content-tab-btn" data-tab="faq">
                        <i class="bi bi-chat-square-text me-2"></i>FAQ
                    </button>
                </li>
                <li class="nav-item">
                    <button class="content-tab-btn" data-tab="messages">
                        <i class="bi bi-envelope me-2"></i>Tin nhắn
                        <span class="tab-badge" id="unreadMsgBadge">5</span>
                    </button>
                </li>
            </ul>

            <!-- ===== TAB: BANNER ===== -->
            <div id="tab-banners" class="content-tab-panel active">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h5 class="section-title mb-0">Banner Trang Chủ</h5>
                        <small class="text-muted font-xs">Kéo & thả để sắp xếp thứ tự hiển thị</small>
                    </div>
                    <button class="btn btn-gold py-2 px-4" data-bs-toggle="modal" data-bs-target="#bannerModal" onclick="openBannerModal()">
                        <i class="bi bi-plus-lg me-2"></i>Thêm Banner
                    </button>
                </div>

                <div class="banner-grid" id="bannerList">
                    <!-- Rendered by JS -->
                </div>
            </div>

            <!-- ===== TAB: REVIEWS ===== -->
            <div id="tab-reviews" class="content-tab-panel">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h5 class="section-title mb-0">Quản lý Đánh giá</h5>
                        <small class="text-muted font-xs">Duyệt, ẩn hoặc xoá đánh giá từ khách hàng</small>
                    </div>
                    <div class="d-flex gap-2">
                        <select class="form-select form-select-sm border bg-white font-xs" id="reviewFilter" style="min-width:140px;">
                            <option value="all">Tất cả</option>
                            <option value="pending">Chờ duyệt</option>
                            <option value="approved">Đã duyệt</option>
                            <option value="hidden">Đã ẩn</option>
                        </select>
                    </div>
                </div>

                <div class="d-flex flex-column gap-3" id="reviewList">
                    <!-- Rendered by JS -->
                </div>
            </div>

            <!-- ===== TAB: FAQ ===== -->
            <div id="tab-faq" class="content-tab-panel">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h5 class="section-title mb-0">Câu hỏi Thường gặp (FAQ)</h5>
                        <small class="text-muted font-xs">Hiển thị dạng accordion ở trang người dùng</small>
                    </div>
                    <button class="btn btn-gold py-2 px-4" data-bs-toggle="modal" data-bs-target="#faqModal" onclick="openFaqModal()">
                        <i class="bi bi-plus-lg me-2"></i>Thêm câu hỏi
                    </button>
                </div>

                <div class="d-flex flex-column gap-2" id="faqList">
                    <!-- Rendered by JS -->
                </div>
            </div>

            <!-- ===== TAB: MESSAGES ===== -->
            <div id="tab-messages" class="content-tab-panel">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h5 class="section-title mb-0">Tin nhắn Liên hệ</h5>
                        <small class="text-muted font-xs">Xem và quản lý tin nhắn từ khách hàng</small>
                    </div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-outline-custom py-2 px-3 font-xs" id="markAllReadBtn">
                            <i class="bi bi-check2-all me-1"></i>Đánh dấu tất cả đã đọc
                        </button>
                    </div>
                </div>

                <div class="d-flex flex-column gap-2" id="messageList">
                    <!-- Rendered by JS -->
                </div>
            </div>

            <!-- Footer -->
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

<!-- ===========================
     MODAL: BANNER ADD / EDIT
=========================== -->
<div class="modal fade" id="bannerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content card shadow-sm border-0 p-2">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title page-title fw-bold text-dark tracking-wider fs-5" id="bannerModalTitle">THÊM BANNER MỚI</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="bannerId">
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label-custom">Tiêu đề banner</label>
                        <input type="text" class="form-control-custom w-100" id="bannerTitle" placeholder="VD: Bộ sưu tập Hè 2026">
                    </div>
                    <div class="col-12">
                        <label class="form-label-custom">Ảnh banner</label>
                        <div class="upload-zone rounded-1 p-4 text-center" id="bannerUploadZone" onclick="document.getElementById('bannerImageInput').click()">
                            <i class="bi bi-cloud-arrow-up fs-2 text-muted d-block mb-2"></i>
                            <p class="mb-1 small fw-medium">Nhấn để chọn ảnh</p>
                            <p class="font-xs text-muted mb-0">PNG, JPG, WEBP — tối đa 5MB</p>
                            <input type="file" id="bannerImageInput" accept="image/*" class="d-none">
                        </div>
                        <div id="bannerPreviewWrap" class="mt-2 d-none">
                            <img id="bannerPreview" src="" alt="" class="img-fluid rounded-1" style="max-height:160px;object-fit:cover;width:100%;">
                        </div>
                    </div>
                    <div class="col-md-8">
                        <label class="form-label-custom">Link đích (URL)</label>
                        <input type="text" class="form-control-custom w-100" id="bannerLink" placeholder="VD: /products?collection=summer">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label-custom">Thứ tự hiển thị</label>
                        <input type="number" class="form-control-custom w-100" id="bannerOrder" min="1" placeholder="1">
                    </div>
                    <div class="col-12">
                        <div class="d-flex align-items-center gap-3 mt-1">
                            <label class="form-label-custom mb-0">Trạng thái</label>
                            <div class="form-check form-switch mb-0">
                                <input class="form-check-input" type="checkbox" id="bannerActive" checked>
                                <label class="form-check-label font-xs" for="bannerActive">Hiển thị</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="text-end mt-4 pt-2 border-top border-light">
                    <button type="button" class="btn btn-outline-custom py-2 px-3 me-2" data-bs-dismiss="modal">Hủy</button>
                    <button type="button" class="btn btn-gold py-2 px-4" onclick="saveBanner()">Lưu Banner</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ===========================
     MODAL: FAQ ADD / EDIT
=========================== -->
<div class="modal fade" id="faqModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content card shadow-sm border-0 p-2">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title page-title fw-bold text-dark tracking-wider fs-5" id="faqModalTitle">THÊM CÂU HỎI MỚI</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="faqId">
                <div class="mb-3">
                    <label class="form-label-custom">Câu hỏi</label>
                    <input type="text" class="form-control-custom w-100" id="faqQuestion" placeholder="VD: Chính sách đổi trả của Aurrelia là gì?">
                </div>
                <div class="mb-3">
                    <label class="form-label-custom">Câu trả lời</label>
                    <textarea class="form-control-custom w-100" id="faqAnswer" rows="5" placeholder="Nhập câu trả lời chi tiết..."></textarea>
                </div>
                <div class="text-end mt-4 pt-2 border-top border-light">
                    <button type="button" class="btn btn-outline-custom py-2 px-3 me-2" data-bs-dismiss="modal">Hủy</button>
                    <button type="button" class="btn btn-gold py-2 px-4" onclick="saveFaq()">Lưu câu hỏi</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ===========================
     MODAL: MESSAGE DETAIL
=========================== -->
<div class="modal fade" id="messageModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content card shadow-sm border-0 p-2">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title section-title fs-5">Chi tiết Tin nhắn</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex align-items-center gap-3 mb-4 pb-3 border-bottom">
                    <div class="avatar-lg bg-gold text-white rounded-circle d-flex align-items-center justify-content-center fw-bold fs-5" id="msgDetailAvatar">MN</div>
                    <div>
                        <h6 class="mb-0 fw-bold" id="msgDetailName">Nguyen Thi Mai</h6>
                        <small class="text-muted" id="msgDetailEmail">mai.nguyen@email.com</small>
                    </div>
                    <small class="text-muted ms-auto font-xs" id="msgDetailDate">28/06/2026 — 14:32</small>
                </div>
                <p class="text-dark" style="line-height:1.7;" id="msgDetailBody"></p>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-outline-custom py-2 px-3" data-bs-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>

<!-- ===========================
     MODAL: CONFIRM DELETE
=========================== -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content card shadow-sm border-0 p-2">
            <div class="modal-body text-center py-4">
                <div class="delete-icon-wrap mx-auto mb-3">
                    <i class="bi bi-trash3 fs-2 text-danger"></i>
                </div>
                <h6 class="fw-bold mb-1" id="deleteModalTitle">Xác nhận xoá?</h6>
                <p class="text-muted small mb-0" id="deleteModalDesc">Hành động này không thể hoàn tác.</p>
            </div>
            <div class="modal-footer border-0 pt-0 justify-content-center gap-2">
                <button type="button" class="btn btn-outline-custom py-2 px-3" data-bs-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-danger py-2 px-3" id="confirmDeleteBtn">Xoá</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="/assets/js/contentmanage.js"></script>
</body>
</html>
