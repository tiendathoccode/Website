function showToast(message, type = 'success') {
    let container = document.getElementById('custom-toast-container');
    if (!container) {
        container = document.createElement('div');
        container.id = 'custom-toast-container';
        container.style.cssText = 'position: fixed; top: 20px; right: 20px; z-index: 99999; display: flex; flex-direction: column; gap: 10px;';
        document.body.appendChild(container);
    }
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

document.addEventListener("DOMContentLoaded", function () {
    const searchInput = document.getElementById("searchOrders");
    const filterPayment = document.getElementById("filterPayment");
    const filterFulfillment = document.getElementById("filterFulfillment");
    const btnApplyFilters = document.getElementById("btnApplyFilters");
    const selectAllCheckbox = document.getElementById("selectAll");
    const tableRows = document.querySelectorAll("#ordersTable tbody tr");

    // Khởi tạo Bootstrap Modal đối với mục Thay đổi trạng thái
    const updateModalEl = document.getElementById("updateStatusModal");
    let updateModal = null;
    
    if (updateModalEl) {
        updateModal = new bootstrap.Modal(updateModalEl);
    }
    
    const modalOrderIdText = document.getElementById("modalOrderId");
    const modalSelectPayment = document.getElementById("modalSelectPayment");
    const modalSelectFulfillment = document.getElementById("modalSelectFulfillment");
    const btnSaveStatus = document.getElementById("btnSaveStatus");

    let currentRowTarget = null; // Biến tạm lưu trữ dòng đang chọn chỉnh sửa

    // ==========================================
    // 1. TÌM KIẾM VÀ BỘ LỌC ĐƠN HÀNG (FILTERS)
    // ==========================================
    function filterOrders() {
        if (!searchInput) return;
        
        const searchText = searchInput.value.toLowerCase().trim();
        const paymentValue = filterPayment ? filterPayment.value : "all";
        const fulfillmentValue = filterFulfillment ? filterFulfillment.value : "all";

        tableRows.forEach(row => {
            const orderId = (row.getAttribute("data-id") || "").toLowerCase();
            const customerName = (row.getAttribute("data-customer") || "").toLowerCase();
            const rowPayment = row.getAttribute("data-payment");
            const rowFulfillment = row.getAttribute("data-fulfillment");

            // Kiểm tra điều kiện Tìm kiếm
            const matchesSearch = orderId.includes(searchText) || customerName.includes(searchText);
            
            // Kiểm tra điều kiện Trạng thái thanh toán
            const matchesPayment = (paymentValue === "all") || (rowPayment === paymentValue);
            
            // Kiểm tra điều kiện Trạng thái vận chuyển
            const matchesFulfillment = (fulfillmentValue === "all") || (rowFulfillment === fulfillmentValue);

            // Hiển thị hoặc ẩn dòng
            if (matchesSearch && matchesPayment && matchesFulfillment) {
                row.style.setProperty("display", "", "important");
            } else {
                row.style.setProperty("display", "none", "important");
                const rowCb = row.querySelector(".form-check-input");
                if (rowCb) rowCb.checked = false;
            }
        });
        
        if (selectAllCheckbox) selectAllCheckbox.checked = false;
    }

    if (btnApplyFilters) {
        btnApplyFilters.addEventListener("click", filterOrders);
    }

    if (searchInput) {
        searchInput.addEventListener("input", filterOrders);
    }

    // ==========================================
    // 2. XỬ LÝ SỰ KIỆN CẬP NHẬT TRẠNG THÁI (UPDATE STATUS)
    // ==========================================
    tableRows.forEach(row => {
        const updateBtn = row.querySelector(".btn-update-status");
        if (updateBtn) {
            updateBtn.addEventListener("click", function () {
                if (!updateModal) return;
                
                currentRowTarget = row; 
                
                const orderId = row.getAttribute("data-id");
                const currentPayment = row.getAttribute("data-payment");
                const currentFulfillment = row.getAttribute("data-fulfillment");

                // Điền thông tin cũ vào Modal trước khi hiển thị
                if (modalOrderIdText) modalOrderIdText.textContent = `Cập nhật đơn hàng ${orderId}`;
                if (modalSelectPayment) modalSelectPayment.value = currentPayment;
                if (modalSelectFulfillment) modalSelectFulfillment.value = currentFulfillment;

                updateModal.show();
            });
        }
    });

    // Xử lý sự kiện lưu dữ liệu từ Modal vào Database qua AJAX
    if (btnSaveStatus) {
        btnSaveStatus.addEventListener("click", function () {
            if (!currentRowTarget || !updateModal) return;

            const orderDbId = currentRowTarget.getAttribute("data-db-id");
            const newPayment = modalSelectPayment.value;
            const newFulfillment = modalSelectFulfillment.value;

            const formData = new FormData();
            formData.append("order_id", orderDbId);
            formData.append("status", newFulfillment);
            formData.append("payment_method", newPayment);

            fetch("/index.php?page=admin_api_update_order", {
                method: "POST",
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === "ok") {
                    updateModal.hide();
                    showToast(data.message, "success");
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
                } else {
                    showToast("Lỗi cập nhật: " + data.message, "error");
                }
            })
            .catch(err => {
                showToast("Đã xảy ra lỗi kết nối.", "error");
            });
        });
    }

    // ==========================================
    // 3. TÍNH NĂNG CHỌN TẤT CẢ (SELECT ALL CHECKBOX)
    // ==========================================
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener("change", function () {
            const checkboxes = document.querySelectorAll("#ordersTable tbody .form-check-input");
            checkboxes.forEach(cb => {
                const parentRow = cb.closest("tr");
                if (parentRow && parentRow.style.display !== "none") {
                    cb.checked = selectAllCheckbox.checked;
                }
            });
        });
    }
});
