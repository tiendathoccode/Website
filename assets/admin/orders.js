document.addEventListener("DOMContentLoaded", function () {
    // Khai báo các phần tử DOM quan trọng
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

            // Kiểm tra điều kiện Tìm kiếm (Khớp mã ID hoặc Tên khách hàng)
            const matchesSearch = orderId.includes(searchText) || customerName.includes(searchText);
            
            // Kiểm tra điều kiện Trạng thái thanh toán
            const matchesPayment = (paymentValue === "all") || (rowPayment === paymentValue);
            
            // Kiểm tra điều kiện Trạng thái vận chuyển
            const matchesFulfillment = (fulfillmentValue === "all") || (rowFulfillment === fulfillmentValue);

            // Hiển thị hoặc ẩn dòng dựa vào tập hợp điều kiện
            if (matchesSearch && matchesPayment && matchesFulfillment) {
                row.style.setProperty("display", "", "important");
            } else {
                row.style.setProperty("display", "none", "important");
                // Hủy check nếu hàng đó bị ẩn đi bởi bộ lọc
                const rowCb = row.querySelector(".form-check-input");
                if (rowCb) rowCb.checked = false;
            }
        });
        
        // Cập nhật lại trạng thái nút Select All tổng sau khi lọc
        if (selectAllCheckbox) selectAllCheckbox.checked = false;
    }

    // Sự kiện lắng nghe khi click nút "Apply Filters"
    if (btnApplyFilters) {
        btnApplyFilters.addEventListener("click", filterOrders);
    }

    // Hỗ trợ tìm kiếm nhanh thời gian thực khi đang gõ chữ
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
                
                currentRowTarget = row; // Gán hàng hiện tại vào biến mục tiêu
                
                const orderId = row.getAttribute("data-id");
                const currentPayment = row.getAttribute("data-payment");
                const currentFulfillment = row.getAttribute("data-fulfillment");

                // Điền thông tin cũ vào Modal trước khi hiển thị
                if (modalOrderIdText) modalOrderIdText.textContent = `Update Order ${orderId}`;
                if (modalSelectPayment) modalSelectPayment.value = currentPayment;
                if (modalSelectFulfillment) modalSelectFulfillment.value = currentFulfillment;

                // Mở Modal
                updateModal.show();
            });
        }
    });

    // Xử lý sự kiện lưu dữ liệu từ Modal
    if (btnSaveStatus) {
        btnSaveStatus.addEventListener("click", function () {
            if (!currentRowTarget || !updateModal) return;

            const newPayment = modalSelectPayment.value;
            const newFulfillment = modalSelectFulfillment.value;

            // Cập nhật lại các thuộc tính data-* trên thẻ tr
            currentRowTarget.setAttribute("data-payment", newPayment);
            currentRowTarget.setAttribute("data-fulfillment", newFulfillment);

            // Tối ưu nâng cao: Tìm phần tử chứa badge thay vì gán cứng index cells tránh lỗi giao diện
            const paymentCell = currentRowTarget.querySelector(".status-payment-container") || currentRowTarget.cells[5];
            const fulfillmentCell = currentRowTarget.querySelector(".status-fulfillment-container") || currentRowTarget.cells[6];

            // Cập nhật giao diện cột "Payment Status"
            if (paymentCell) {
                if (newPayment === "Paid") {
                    paymentCell.innerHTML = `<span class="status-badge"><i class="bi bi-circle-fill font-xs status-paid me-1"></i> Paid</span>`;
                } else {
                    paymentCell.innerHTML = `<span class="status-badge"><i class="bi bi-circle-fill font-xs status-pending me-1"></i> Pending</span>`;
                }
            }

            // Cập nhật giao diện cột "Fulfillment Status"
            if (fulfillmentCell) {
                if (newFulfillment === "Shipped") {
                    fulfillmentCell.innerHTML = `<span class="status-badge"><i class="bi bi-circle-fill font-xs status-shipped me-1"></i> Shipped</span>`;
                } else {
                    fulfillmentCell.innerHTML = `<span class="status-badge"><i class="bi bi-circle-fill font-xs status-processing me-1"></i> Processing</span>`;
                }
            }

            // Đóng modal sau khi hoàn tất thành công
            updateModal.hide();
            
            // Chạy lại bộ lọc tự động để cập nhật trạng thái hiển thị chuẩn xác nhất
            filterOrders();
        });
    }

    // ==========================================
    // 3. TÍNH NĂNG CHỌN TẤT CẢ (SELECT ALL CHECKBOX)
    // ==========================================
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener("change", function () {
            const checkboxes = document.querySelectorAll("#ordersTable tbody .form-check-input");
            checkboxes.forEach(cb => {
                // Chỉ check các hàng đang được hiển thị thực tế (không bị ẩn bởi bộ lọc tìm kiếm)
                const parentRow = cb.closest("tr");
                if (parentRow && parentRow.style.display !== "none") {
                    cb.checked = selectAllCheckbox.checked;
                }
            });
        });
    }
});