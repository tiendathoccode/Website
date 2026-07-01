<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Đơn Hàng Của Tôi – Aurrelia</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
  <link rel="stylesheet" href="/assets/css/style.css" />
  <style>
    .order-card-clickable {
        transition: all 0.2s ease;
    }
    .order-card-clickable:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.05) !important;
        border-color: #c8a165 !important;
    }
    /* Timeline styling */
    .timeline-steps {
        display: flex;
        justify-content: space-between;
        align-items: center;
        position: relative;
        margin: 30px 0;
        padding: 0;
        list-style: none;
    }
    .timeline-steps::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 0;
        width: 100%;
        height: 3px;
        background-color: #f0ece4;
        transform: translateY(-50%);
        z-index: 1;
    }
    .timeline-progress-line {
        position: absolute;
        top: 50%;
        left: 0;
        height: 3px;
        background-color: #c8a165;
        transform: translateY(-50%);
        z-index: 2;
        transition: width 0.4s ease;
        width: 0%;
    }
    .timeline-step {
        position: relative;
        z-index: 3;
        text-align: center;
    }
    .timeline-icon {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background-color: #fff;
        border: 3px solid #f0ece4;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 13px;
        color: #ccc;
        margin: 0 auto 8px auto;
        transition: all 0.3s ease;
    }
    .timeline-step.active .timeline-icon {
        border-color: #c8a165;
        background-color: #c8a165;
        color: #fff;
    }
    .timeline-step.completed .timeline-icon {
        border-color: #c8a165;
        background-color: #fff;
        color: #c8a165;
    }
    .timeline-label {
        font-size: 11px;
        font-weight: 600;
        color: #999;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .timeline-step.active .timeline-label {
        color: #333;
    }
    .timeline-step.completed .timeline-label {
        color: #c8a165;
    }
  </style>
</head>
<body class="bg-cream">

    <!-- ================= HEADER ================= -->
    <nav class="navbar navbar-expand-lg py-3 sticky-top border-bottom shadow-sm" style="background-color: #fdfbf7; z-index: 1020;">
        <div class="container-fluid px-4">
            <a class="navbar-brand fs-4 fw-bold gold-text" href="/index.php?page=home" style="font-family: 'Times New Roman', serif;">AURRELIA</a>

            <div class="d-flex gap-3 align-items-center me-2 order-lg-last">
                <div class="d-flex align-items-center" style="position: relative;">
                    <input type="text" id="navbarSearchInput" placeholder="Tìm kiếm sản phẩm..." style="
                        display: <?php echo isset($_GET['search']) && trim($_GET['search']) !== '' ? 'block' : 'none'; ?>;
                        border: none;
                        border-bottom: 1px solid #c8a165;
                        background: transparent;
                        outline: none;
                        padding: 2px 8px;
                        font-size: 13px;
                        width: 150px;
                        margin-right: 8px;
                        transition: all 0.3s ease;
                    " value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>" />
                    <a href="#" class="text-dark fs-6" id="navbarSearchBtn"><i class="fas fa-search"></i></a>
                </div>
                <a href="#" class="text-dark fs-6"><i class="far fa-heart"></i></a>
                <a href="/index.php?page=gio_hang" class="text-dark fs-6 position-relative" id="headerCartBtn">
                    <i class="fas fa-shopping-bag"></i>
                    <span id="headerCartBadge" style="
                        display:none;
                        position:absolute;
                        top:-8px; right:-10px;
                        background:#c8a165; color:#fff;
                        font-size:10px; font-weight:700;
                        min-width:17px; height:17px;
                        border-radius:50%;
                        align-items:center; justify-content:center;
                        padding:0 3px;
                    ">0</span>
                </a>
                <div class="position-relative" id="userDropdownWrapper">
                    <a href="#" class="text-dark fs-6" id="userIconBtn" onclick="toggleUserDropdown(event)">
                        <i class="far fa-user"></i>
                    </a>
                    <div id="userDropdownMenu" style="
                        display: none;
                        position: absolute;
                        top: calc(100% + 12px);
                        right: 0;
                        background: white;
                        border: 1px solid #eee;
                        border-radius: 8px;
                        box-shadow: 0 8px 24px rgba(0,0,0,0.1);
                        min-width: 200px;
                        z-index: 9999;
                        overflow: hidden;
                    ">
                        <?php if (isset($_SESSION["user_logged_in"]) && $_SESSION["user_logged_in"] === true): ?>
                            <div style="padding: 14px 18px; border-bottom: 1px solid #f0ece4; background: #fcf9f2;">
                                <p style="margin:0; font-size:12px; color:#888;">Xin chào,</p>
                                <p style="margin:0; font-weight:bold; font-size:14px; color:#333;">
                                    <?php echo htmlspecialchars($_SESSION["user_name"]); ?>
                                </p>
                            </div>
                            <a href="/index.php?page=profile" style="display:flex; align-items:center; gap:10px; padding:12px 18px; text-decoration:none; color:#333; font-size:13px; border-bottom:1px solid #f5f5f5;">
                                <i class="far fa-user" style="color:#bfa15f; width:16px;"></i> Thông tin cá nhân
                            </a>
                            <?php if (isset($_SESSION["user_role"]) && $_SESSION["user_role"] === "admin"): ?>
                                <a href="/index.php?page=admin_dashboard" style="display:flex; align-items:center; gap:10px; padding:12px 18px; text-decoration:none; color:#333; font-size:13px; border-bottom:1px solid #f5f5f5;">
                                    <i class="fas fa-user-shield" style="color:#bfa15f; width:16px;"></i> Trang quản trị (Admin)
                                </a>
                            <?php endif; ?>
                            <a href="/index.php?page=don_hang" style="display:flex; align-items:center; gap:10px; padding:12px 18px; text-decoration:none; color:#333; font-size:13px; border-bottom:1px solid #f5f5f5;">
                                <i class="fas fa-box" style="color:#bfa15f; width:16px;"></i> Đơn hàng của tôi
                            </a>
                            <a href="/index.php?page=change_password" style="display:flex; align-items:center; gap:10px; padding:12px 18px; text-decoration:none; color:#333; font-size:13px; border-bottom:1px solid #f5f5f5;">
                                <i class="fas fa-lock" style="color:#bfa15f; width:16px;"></i> Đổi mật khẩu
                            </a>
                            <a href="/index.php?page=logout" style="display:flex; align-items:center; gap:10px; padding:12px 18px; text-decoration:none; color:#c0392b; font-size:13px;">
                                <i class="fas fa-sign-out-alt" style="color:#c0392b; width:16px;"></i> Đăng xuất
                            </a>
                        <?php else: ?>
                            <a href="/index.php?page=login" style="display:flex; align-items:center; gap:10px; padding:12px 18px; text-decoration:none; color:#333; font-size:13px; border-bottom:1px solid #f5f5f5;">
                                <i class="fas fa-sign-in-alt" style="color:#bfa15f; width:16px;"></i> Đăng nhập
                            </a>
                            <a href="/index.php?page=register" style="display:flex; align-items:center; gap:10px; padding:12px 18px; text-decoration:none; color:#333; font-size:13px;">
                                <i class="fas fa-user-plus" style="color:#bfa15f; width:16px;"></i> Tạo tài khoản
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mx-auto text-uppercase" style="font-size: 13px; letter-spacing: 1px;">
                    <li class="nav-item"><a class="nav-link px-3" href="/index.php?page=home">Trang Sức</a></li>
                    <li class="nav-item"><a class="nav-link px-3" href="/index.php?page=home&category_id=1">Trang Sức Cao Cấp</a></li>
                    <li class="nav-item"><a class="nav-link px-3" href="/index.php?page=home#about-us-section">Về Chúng Tôi</a></li>
                    <li class="nav-item"><a class="nav-link px-3" href="/index.php?page=home#contact-section">Liên Hệ</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- ================= CONTENT ================= -->
    <div class="container my-5" style="max-width: 900px;">
        <h2 class="fw-normal mb-1" style="font-family: 'Times New Roman', serif; letter-spacing: 2px; text-align: center;">ĐƠN HÀNG CỦA BẠN</h2>
        <p class="text-muted text-center mb-4" style="font-size: 13px;">Click trực tiếp vào đơn hàng để xem chi tiết, tiến độ giao hàng và thao tác hủy/trả hàng.</p>

        <?php if (empty($orders)): ?>
            <div class="text-center py-5 bg-white rounded border shadow-sm">
                <i class="fas fa-box-open mb-3 text-muted" style="font-size:48px;"></i>
                <h5 class="fw-normal" style="font-family:'Times New Roman', serif;">Bạn chưa đặt đơn hàng nào</h5>
                <p class="text-muted mb-4" style="font-size:13px;">Hãy quay lại trang chủ và khám phá các bộ sưu tập của chúng tôi.</p>
                <a href="/index.php?page=home" class="btn btn-gold rounded-pill px-4" style="background-color: #c8a165; color: white; border: none; font-size:12px; letter-spacing:1px;">TIẾP TỤC MUA SẮM</a>
            </div>
        <?php else: ?>
            <div class="d-flex flex-column gap-3">
                <?php foreach ($orders as $o): ?>
                    <?php
                    $statusText = "Chờ xử lý";
                    $statusBadge = "bg-warning text-dark";
                    if ($o["status"] === "processing") {
                        $statusText = "Đang xử lý";
                        $statusBadge = "bg-info text-dark";
                    } elseif ($o["status"] === "shipping") {
                        $statusText = "Đang giao hàng";
                        $statusBadge = "bg-primary text-white";
                    } elseif ($o["status"] === "delivered") {
                        $statusText = "Đã giao hàng";
                        $statusBadge = "bg-success text-white";
                    } elseif ($o["status"] === "cancelled") {
                        $statusText = "Đã hủy";
                        $statusBadge = "bg-danger text-white";
                    } elseif ($o["status"] === "return_requested") {
                        $statusText = "Yêu cầu hoàn trả (Đang thu hồi)";
                        $statusBadge = "bg-warning text-dark";
                    } elseif ($o["status"] === "returned") {
                        $statusText = "Đã hoàn trả thành công";
                        $statusBadge = "bg-secondary text-white";
                    }
                    ?>
                    <div class="card border border-light-subtle shadow-sm p-4 bg-white rounded-3 order-card-clickable" data-order-id="<?php echo $o["order_id"]; ?>" style="cursor: pointer;">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 border-bottom pb-3 mb-3">
                            <div>
                                <span class="fw-bold text-dark font-numeric">Mã đơn: #<?php echo htmlspecialchars($o["order_code"]); ?></span>
                                <span class="text-muted ms-2" style="font-size:12px;"><?php echo date("d/m/Y H:i", strtotime($o["created_at"])); ?></span>
                            </div>
                            <span class="badge <?php echo $statusBadge; ?> rounded-pill px-3 py-2" style="font-size: 11px; letter-spacing: 0.5px;"><?php echo $statusText; ?></span>
                        </div>

                        <div class="row align-items-center">
                            <div class="col-md-7">
                                <p class="mb-1 text-muted" style="font-size: 13px;"><strong>Người nhận:</strong> <?php echo htmlspecialchars($o["receiver_name"]); ?> (<?php echo htmlspecialchars($o["receiver_phone"]); ?>)</p>
                                <p class="mb-0 text-muted" style="font-size: 13px;"><strong>Địa chỉ:</strong> <?php echo htmlspecialchars($o["shipping_address"]); ?></p>
                                <p class="mb-0 text-muted" style="font-size: 13px;"><strong>Thanh toán:</strong> <?php echo ($o["payment_method"] === "cod") ? "Thanh toán khi nhận hàng (COD)" : "Chuyển khoản ngân hàng"; ?></p>
                            </div>
                            <div class="col-md-5 text-md-end mt-3 mt-md-0">
                                <span class="text-muted d-block" style="font-size: 12px;">Tổng số tiền</span>
                                <h4 class="text-gold fw-bold mb-0" style="color: #c8a165; font-family:'Times New Roman', serif;"><?php echo number_format($o["final_amount"], 0, ',', '.'); ?>₫</h4>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- ================= ORDER DETAILS MODAL ================= -->
    <div class="modal fade" id="orderDetailsModal" tabindex="-1" aria-hidden="true" style="font-family: 'Inter', sans-serif;">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; background: #fff;">
                <div class="modal-header border-0 pb-0" style="position: relative; display: block; border-bottom: none;">
                    <h5 class="modal-title w-100 text-center fw-bold" style="color:#333; font-family:'Times New Roman', serif; letter-spacing:1px; font-size: 18px;">CHI TIẾT ĐƠN HÀNG</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="position: absolute; right: 15px; top: 15px; font-size:12px;"></button>
                </div>
                <div class="modal-body px-4 py-3">
                    
                    <!-- ORDER TIMELINE TRACKER -->
                    <div style="position: relative;">
                        <ul class="timeline-steps">
                            <div class="timeline-progress-line" id="timelineProgress"></div>
                            <li class="timeline-step" id="step-pending">
                                <div class="timeline-icon"><i class="fas fa-receipt"></i></div>
                                <div class="timeline-label">Chờ xử lý</div>
                            </li>
                            <li class="timeline-step" id="step-processing">
                                <div class="timeline-icon"><i class="fas fa-cog"></i></div>
                                <div class="timeline-label">Đang xử lý</div>
                            </li>
                            <li class="timeline-step" id="step-shipping">
                                <div class="timeline-icon"><i class="fas fa-truck"></i></div>
                                <div class="timeline-label">Đang giao</div>
                            </li>
                            <li class="timeline-step" id="step-completed">
                                <div class="timeline-icon" id="icon-completed"><i class="fas fa-check"></i></div>
                                <div class="timeline-label" id="label-completed">Đã giao</div>
                            </li>
                        </ul>
                    </div>

                    <div class="row g-4 mt-2">
                        <!-- Left Column: Delivery details -->
                        <div class="col-md-5 border-end">
                            <h6 class="fw-bold text-uppercase mb-3" style="font-size:12px; color:#c8a165; letter-spacing:1px;">THÔNG TIN GIAO HÀNG</h6>
                            <div class="p-3 bg-light rounded" style="font-size: 12px; line-height: 1.8;">
                                <p class="mb-2"><strong>Mã đơn hàng:</strong> <span class="font-numeric" id="detailOrderCode"></span></p>
                                <p class="mb-2"><strong>Người nhận:</strong> <span id="detailReceiverName"></span></p>
                                <p class="mb-2"><strong>Số điện thoại:</strong> <span id="detailReceiverPhone"></span></p>
                                <p class="mb-2"><strong>Địa chỉ nhận hàng:</strong> <span id="detailReceiverAddress"></span></p>
                                <p class="mb-2"><strong>Phương thức thanh toán:</strong> <span id="detailPaymentMethod"></span></p>
                                <p class="mb-0"><strong>Ngày đặt hàng:</strong> <span id="detailOrderDate"></span></p>
                            </div>
                        </div>

                        <!-- Right Column: Products List -->
                        <div class="col-md-7">
                            <h6 class="fw-bold text-uppercase mb-3" style="font-size:12px; color:#c8a165; letter-spacing:1px;">SẢN PHẨM ĐÃ MUA</h6>
                            <div id="detailProductsContainer" style="max-height: 250px; overflow-y: auto; padding-right:5px;">
                                <!-- Items rendered via JS -->
                            </div>

                            <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top">
                                <span class="fw-bold" style="font-size:13px; color:#333;">TỔNG THANH TOÁN:</span>
                                <span class="fw-bold text-gold" id="detailFinalAmount" style="font-size:18px; color:#c8a165; font-family:'Times New Roman', serif;">0₫</span>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer border-0 pt-0 d-flex gap-2">
                    <button type="button" class="btn btn-secondary py-2" data-bs-dismiss="modal" style="font-size: 13px; border-radius: 6px; min-width: 120px;">ĐÓNG</button>
                    <button type="button" id="btnCancelOrder" class="btn btn-danger py-2 border-0 flex-grow-1" style="font-size: 13px; border-radius: 6px; display:none; background-color: #dc3545;">HỦY ĐƠN HÀNG</button>
                    <button type="button" id="btnReturnOrder" class="btn btn-warning py-2 border-0 flex-grow-1 text-white" style="font-size: 13px; border-radius: 6px; display:none; background-color:#e67e22;">YÊU CẦU HOÀN HÀNG</button>
                </div>
            </div>
        </div>
    </div>

    <!-- ================= CANCEL CONFIRMATION MODAL ================= -->
    <div class="modal fade" id="cancelConfirmModal" tabindex="-1" aria-hidden="true" style="font-family: 'Inter', sans-serif; z-index: 1060;">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 380px;">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; background: #fff;">
                <div class="modal-body text-center p-4">
                    <div class="mb-3 text-danger" style="font-size: 40px;">
                        <i class="fas fa-exclamation-circle"></i>
                    </div>
                    <h6 class="fw-bold mb-2" style="color: #333; font-size: 15px;">XÁC NHẬN HỦY ĐƠN HÀNG</h6>
                    <p class="text-muted mb-3" style="font-size: 12px; line-height: 1.5;">
                        Bạn có chắc chắn muốn hủy đơn hàng này? Thao tác này sẽ không thể phục hồi lại trạng thái đơn hàng.
                    </p>
                    <div class="alert alert-warning py-2 px-3 mb-4 text-start" style="font-size: 11px; border-radius: 6px;">
                        <i class="fas fa-info-circle me-1"></i> Lưu ý: Chỉ được hủy đơn khi trạng thái là <strong>Chờ xử lý</strong> hoặc <strong>Đang xử lý</strong> (Bước 1 & 2).
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-secondary flex-grow-1 py-2" data-bs-dismiss="modal" style="font-size: 13px; border-radius: 6px;">QUAY LẠI</button>
                        <button type="button" id="btnSubmitCancel" class="btn btn-danger flex-grow-1 py-2 border-0" style="background-color: #dc3545; font-size: 13px; border-radius: 6px;">ĐỒNG Ý HỦY</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ================= RETURN CONFIRMATION MODAL ================= -->
    <div class="modal fade" id="returnConfirmModal" tabindex="-1" aria-hidden="true" style="font-family: 'Inter', sans-serif; z-index: 1060;">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 380px;">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; background: #fff;">
                <div class="modal-body text-center p-4">
                    <div class="mb-3 text-warning" style="font-size: 40px;">
                        <i class="fas fa-undo-alt"></i>
                    </div>
                    <h6 class="fw-bold mb-2" style="color: #333; font-size: 15px;">YÊU CẦU HOÀN TRẢ HÀNG</h6>
                    <p class="text-muted mb-4" style="font-size: 12px; line-height: 1.5;">
                        Bạn có chắc chắn muốn gửi yêu cầu đổi/trả cho đơn hàng này? Nhân viên CSKH sẽ liên hệ với bạn để kiểm tra tình trạng hàng hóa.
                    </p>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-secondary flex-grow-1 py-2" data-bs-dismiss="modal" style="font-size: 13px; border-radius: 6px;">QUAY LẠI</button>
                        <button type="button" id="btnSubmitReturn" class="btn btn-warning flex-grow-1 py-2 border-0 text-white" style="background-color: #e67e22; font-size: 13px; border-radius: 6px;">XÁC NHẬN YÊU CẦU</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ================= FOOTER ================= -->
    <footer class="pt-5 pb-3 border-top mt-5" style="background-color: #fdfbf7;">
        <div class="container text-center">
            <h4 class="fw-bold gold-text mb-3" style="font-family: 'Times New Roman', serif; letter-spacing: 2px;">AURRELIA</h4>
            <p class="small text-muted">&copy; 2026 Aurrelia. Bản quyền thuộc về Nhóm 6.</p>
        </div>
    </footer>

    <div id="custom-toast-container" style="position: fixed; top: 20px; right: 20px; z-index: 99999; display: flex; flex-direction: column; gap: 10px;"></div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toast helper
        function showToast(message, type = 'success') {
            const container = document.getElementById('custom-toast-container');
            const toast = document.createElement('div');
            toast.style.cssText = 'background: #ffffff; border-left: 4px solid #c8a165; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08); color: #333333; padding: 14px 20px; font-size: 13px; font-family: "Inter", sans-serif; border-radius: 4px; display: flex; align-items: center; gap: 10px; min-width: 280px; max-width: 380px; transition: all 0.3s ease; opacity: 1;';
            
            if (type === 'error') {
                toast.style.borderLeftColor = '#dc3545';
            } else if (type === 'success') {
                toast.style.borderLeftColor = '#198754';
            }

            let icon = '<i class="fas fa-check-circle" style="color:#198754"></i>';
            if (type === 'error') {
                icon = '<i class="fas fa-times-circle" style="color:#dc3545"></i>';
            } else if (type === 'info') {
                icon = '<i class="fas fa-info-circle" style="color:#c8a165"></i>';
            }
            
            toast.innerHTML = `${icon} <span>${message}</span>`;
            container.appendChild(toast);
            
            setTimeout(() => {
                toast.style.opacity = '0';
                toast.style.transform = 'translateY(-20px)';
                setTimeout(() => {
                    toast.remove();
                }, 300);
            }, 3000);
        }

        // Dropdown toggle
        function toggleUserDropdown(event) {
            event.preventDefault();
            event.stopPropagation();
            const menu = document.getElementById("userDropdownMenu");
            menu.style.display = menu.style.display === "none" ? "block" : "none";
        }
        document.addEventListener("click", () => {
            const menu = document.getElementById("userDropdownMenu");
            if (menu) menu.style.display = "none";
        });
        
        // Header badge sync
        document.addEventListener("DOMContentLoaded", () => {
            const badge = document.getElementById("headerCartBadge");
            if (badge) {
                const count = localStorage.getItem("aurrelia_cart") ? JSON.parse(localStorage.getItem("aurrelia_cart")).reduce((s, i) => s + i.quantity, 0) : 0;
                badge.textContent = count;
                badge.style.display = count > 0 ? "flex" : "none";
            }
        });

        // =====================================
        // ORDER DETAILS & TIMELINE JS
        // =====================================
        let activeOrderId = null;
        let detailsModal = null;
        let cancelConfirmModal = null;
        let returnConfirmModal = null;
        
        document.addEventListener("DOMContentLoaded", () => {
            detailsModal = new bootstrap.Modal(document.getElementById("orderDetailsModal"));
            cancelConfirmModal = new bootstrap.Modal(document.getElementById("cancelConfirmModal"));
            returnConfirmModal = new bootstrap.Modal(document.getElementById("returnConfirmModal"));
            
            // Lắng nghe click các thẻ card đơn hàng
            const orderCards = document.querySelectorAll(".order-card-clickable");
            orderCards.forEach(card => {
                card.addEventListener("click", function() {
                    const orderId = this.getAttribute("data-order-id");
                    loadOrderDetails(orderId);
                });
            });
            
            // Mở modal xác nhận Hủy đơn hàng
            document.getElementById("btnCancelOrder").addEventListener("click", () => {
                cancelConfirmModal.show();
            });
            
            // Xác nhận đồng ý Hủy
            document.getElementById("btnSubmitCancel").addEventListener("click", () => {
                cancelConfirmModal.hide();
                executeOrderAction("cancel");
            });
            
            // Mở modal xác nhận Yêu cầu hoàn hàng
            document.getElementById("btnReturnOrder").addEventListener("click", () => {
                returnConfirmModal.show();
            });
            
            // Xác nhận đồng ý hoàn hàng
            document.getElementById("btnSubmitReturn").addEventListener("click", () => {
                returnConfirmModal.hide();
                executeOrderAction("return");
            });
        });

        function loadOrderDetails(orderId) {
            activeOrderId = orderId;
            fetch(`/index.php?page=order_details&order_id=${orderId}`)
                .then(res => res.json())
                .then(data => {
                    if (data.status === "ok") {
                        const o = data.order;
                        
                        // Điền thông tin giao hàng
                        document.getElementById("detailOrderCode").textContent = o.order_code;
                        document.getElementById("detailReceiverName").textContent = o.receiver_name;
                        document.getElementById("detailReceiverPhone").textContent = o.receiver_phone;
                        document.getElementById("detailReceiverAddress").textContent = o.shipping_address;
                        document.getElementById("detailPaymentMethod").textContent = o.payment_method === "cod" ? "Thanh toán khi nhận hàng (COD)" : "Chuyển khoản ngân hàng";
                        document.getElementById("detailOrderDate").textContent = new Date(o.created_at).toLocaleString("vi-VN");
                        document.getElementById("detailFinalAmount").textContent = parseInt(o.final_amount).toLocaleString("vi-VN") + "₫";
                        
                        // Điền danh sách sản phẩm
                        const container = document.getElementById("detailProductsContainer");
                        container.innerHTML = "";
                        data.items.forEach(item => {
                            const price = parseInt(item.price);
                            const itemEl = document.createElement("div");
                            itemEl.style.cssText = "display:flex; justify-content:space-between; align-items:center; margin-bottom:12px; border-bottom:1px solid #f8f6f2; padding-bottom:12px;";
                            itemEl.innerHTML = `
                                <div style="display:flex; align-items:center; gap:12px;">
                                    <img src="${item.main_image}" alt="${item.product_name}" style="width:50px; height:50px; object-fit:cover; border-radius:6px;" />
                                    <div>
                                        <p style="margin:0; font-size:12px; font-weight:600; color:#333; line-height:1.4;">${item.product_name}</p>
                                        <p style="margin:0; font-size:11px; color:#888;">${item.selected_material || ""}</p>
                                        <p style="margin:0; font-size:11px; color:#999;">SL: ${item.quantity}</p>
                                    </div>
                                </div>
                                <span style="font-size:12px; font-weight:600; color:#333;">${(price * item.quantity).toLocaleString("vi-VN")}₫</span>
                            `;
                            container.appendChild(itemEl);
                        });
                        
                        // Thiết lập Timeline
                        setupTimeline(o.status);
                        
                        // Quản lý hiển thị nút hành động
                        const btnCancel = document.getElementById("btnCancelOrder");
                        const btnReturn = document.getElementById("btnReturnOrder");
                        
                        if (o.status === "pending" || o.status === "processing") {
                            btnCancel.style.display = "block";
                            btnReturn.style.display = "none";
                        } else if (o.status === "delivered") {
                            btnCancel.style.display = "none";
                            btnReturn.style.display = "block";
                        } else {
                            btnCancel.style.display = "none";
                            btnReturn.style.display = "none";
                        }
                        
                        detailsModal.show();
                    } else {
                        showToast(data.message, "error");
                    }
                })
                .catch(err => {
                    showToast("Lỗi kết nối máy chủ.", "error");
                });
        }

        function setupTimeline(status) {
            const steps = ["pending", "processing", "shipping", "delivered"];
            
            // Xóa sạch class cũ của các bước
            document.querySelectorAll(".timeline-step").forEach(step => {
                step.className = "timeline-step";
            });
            
            // Khôi phục nhãn gốc cho bước cuối
            document.getElementById("label-completed").textContent = "Đã giao";
            document.getElementById("icon-completed").innerHTML = '<i class="fas fa-check"></i>';
            document.getElementById("step-completed").style.display = "block";
            
            let statusIndex = steps.indexOf(status);
            let progressWidth = 0;
            
            if (status === "cancelled") {
                // Đơn hàng bị Hủy: Đặt bước cuối là Đã hủy và hiển thị màu đỏ/nổi bật
                document.getElementById("step-completed").className = "timeline-step active";
                document.getElementById("label-completed").textContent = "Đã hủy";
                document.getElementById("icon-completed").innerHTML = '<i class="fas fa-times" style="color:white;"></i>';
                document.getElementById("step-pending").className = "timeline-step completed";
                document.getElementById("step-processing").className = "timeline-step completed";
                document.getElementById("step-shipping").className = "timeline-step completed";
                progressWidth = 100;
            } else if (status === "return_requested") {
                // Đơn hàng đang thu hồi
                document.getElementById("step-completed").className = "timeline-step active";
                document.getElementById("label-completed").textContent = "Đang thu hồi";
                document.getElementById("icon-completed").innerHTML = '<i class="fas fa-shipping-fast" style="color:white;"></i>';
                document.getElementById("step-pending").className = "timeline-step completed";
                document.getElementById("step-processing").className = "timeline-step completed";
                document.getElementById("step-shipping").className = "timeline-step completed";
                progressWidth = 100;
            } else if (status === "returned") {
                // Đơn hàng hoàn trả thành công
                document.getElementById("step-completed").className = "timeline-step active";
                document.getElementById("label-completed").textContent = "Đã nhận hàng trả";
                document.getElementById("icon-completed").innerHTML = '<i class="fas fa-undo" style="color:white;"></i>';
                document.getElementById("step-pending").className = "timeline-step completed";
                document.getElementById("step-processing").className = "timeline-step completed";
                document.getElementById("step-shipping").className = "timeline-step completed";
                progressWidth = 100;
            } else {
                // Các trạng thái tiến trình bình thường
                progressWidth = statusIndex >= 0 ? (statusIndex / (steps.length - 1)) * 100 : 0;
                
                steps.forEach((step, idx) => {
                    const el = document.getElementById(`step-${step}`);
                    if (el) {
                        if (idx < statusIndex) {
                            el.className = "timeline-step completed";
                        } else if (idx === statusIndex) {
                            el.className = "timeline-step active";
                        }
                    }
                });
            }
            
            document.getElementById("timelineProgress").style.width = `${progressWidth}%`;
        }

        function executeOrderAction(action) {
            const formData = new FormData();
            formData.append("order_id", activeOrderId);
            formData.append("action", action);
            
            fetch("/index.php?page=order_action", {
                method: "POST",
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === "ok") {
                    detailsModal.hide();
                    showToast(data.message, "success");
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
                } else {
                    showToast(data.message, "error");
                }
            })
            .catch(err => {
                showToast("Lỗi kết nối máy chủ.", "error");
            });
        }
    </script>
</body>
</html>
