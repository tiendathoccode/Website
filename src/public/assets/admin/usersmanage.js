document.addEventListener("DOMContentLoaded", () => {
    const editModal = new bootstrap.Modal(document.getElementById("editUserModal"));
    const btnSaveUser = document.getElementById("btnSaveUser");
    const searchInput = document.getElementById("searchUsers");
    const filterRole = document.getElementById("filterRole");
    const filterStatus = document.getElementById("filterStatus");
    const btnApplyFilters = document.getElementById("btnApplyFilters");
    
    // Modal input elements
    const modalUserId = document.getElementById("modalUserId");
    const modalUserEmail = document.getElementById("modalUserEmail");
    const modalUserName = document.getElementById("modalUserName");
    const modalUserPhone = document.getElementById("modalUserPhone");
    const modalUserRole = document.getElementById("modalUserRole");
    const modalUserStatus = document.getElementById("modalUserStatus");

    // Lắng nghe nút Áp dụng lọc
    btnApplyFilters.addEventListener("click", applyFilters);
    searchInput.addEventListener("keypress", (e) => {
        if (e.key === "Enter") applyFilters();
    });

    // Lắng nghe nút Sửa trong bảng
    document.querySelectorAll(".btn-edit-user").forEach(btn => {
        btn.addEventListener("click", function() {
            const row = this.closest(".user-row");
            
            modalUserId.value = row.dataset.id;
            modalUserEmail.value = row.dataset.email;
            modalUserName.value = row.dataset.name;
            modalUserPhone.value = row.dataset.phone;
            modalUserRole.value = row.dataset.role;
            modalUserStatus.value = row.dataset.status;
            
            editModal.show();
        });
    });

    // Lắng nghe nút Lưu trong Modal
    btnSaveUser.addEventListener("click", () => {
        const userId = modalUserId.value;
        const fullName = modalUserName.value.trim();
        const phone = modalUserPhone.value.trim();
        const role = modalUserRole.value;
        const status = modalUserStatus.value;

        if (!fullName || !phone) {
            showToast("Vui lòng điền đầy đủ họ tên và số điện thoại.", "error");
            return;
        }

        btnSaveUser.disabled = true;
        btnSaveUser.textContent = "Đang lưu...";

        const formData = new FormData();
        formData.append("user_id", userId);
        formData.append("full_name", fullName);
        formData.append("phone", phone);
        formData.append("role", role);
        formData.append("status", status);

        fetch("/index.php?page=admin_api_update_user", {
            method: "POST",
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            btnSaveUser.disabled = false;
            btnSaveUser.textContent = "Lưu thay đổi";

            if (data.status === "ok") {
                editModal.hide();
                showToast(data.message, "success");
                setTimeout(() => {
                    location.reload();
                }, 1000);
            } else {
                showToast(data.message, "error");
            }
        })
        .catch(err => {
            btnSaveUser.disabled = false;
            btnSaveUser.textContent = "Lưu thay đổi";
            showToast("Lỗi kết nối máy chủ.", "error");
        });
    });

    // Hàm áp dụng bộ lọc dòng trên bảng
    function applyFilters() {
        const query = searchInput.value.toLowerCase().trim();
        const role = filterRole.value;
        const status = filterStatus.value;
        
        let visibleCount = 0;
        const rows = document.querySelectorAll(".user-row");

        rows.forEach(row => {
            const name = row.dataset.name.toLowerCase();
            const email = row.dataset.email.toLowerCase();
            const phone = row.dataset.phone;
            const rRole = row.dataset.role;
            const rStatus = row.dataset.status;

            // Kiểm tra khớp từ khóa
            const matchesQuery = !query || name.includes(query) || email.includes(query) || phone.includes(query);
            
            // Kiểm tra khớp vai trò
            const matchesRole = role === "all" || rRole === role;
            
            // Kiểm tra khớp trạng thái
            const matchesStatus = status === "all" || rStatus === status;

            if (matchesQuery && matchesRole && matchesStatus) {
                row.style.display = "";
                visibleCount++;
            } else {
                row.style.display = "none";
            }
        });

        // Xử lý thông báo rỗng nếu không có dòng nào khớp
        let emptyRow = document.getElementById("empty-filter-row");
        if (visibleCount === 0) {
            if (!emptyRow) {
                emptyRow = document.createElement("tr");
                emptyRow.id = "empty-filter-row";
                emptyRow.innerHTML = `<td colspan="7" class="text-center py-5 text-muted font-xs">Không tìm thấy người dùng nào khớp với bộ lọc.</td>`;
                document.querySelector("#usersTable tbody").appendChild(emptyRow);
            } else {
                emptyRow.style.display = "";
            }
        } else {
            if (emptyRow) emptyRow.style.display = "none";
        }
    }
});

// Toast Notification Helper
function showToast(message, type = "success") {
    const container = document.getElementById("custom-toast-container");
    const toast = document.createElement("div");
    toast.style.cssText = `
        background: #ffffff; 
        border-left: 4px solid #c8a165; 
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08); 
        color: #333333; 
        padding: 14px 20px; 
        font-size: 13px; 
        font-family: "Inter", sans-serif; 
        border-radius: 4px; 
        display: flex; 
        align-items: center; 
        gap: 10px; 
        min-width: 280px; 
        max-width: 380px; 
        transition: all 0.3s ease; 
        opacity: 1;
    `;
    
    if (type === "error") {
        toast.style.borderLeftColor = "#dc3545";
    } else if (type === "success") {
        toast.style.borderLeftColor = "#198754";
    }

    let icon = '<i class="bi bi-check-circle-fill" style="color:#198754; font-size:16px;"></i>';
    if (type === "error") {
        icon = '<i class="bi bi-x-circle-fill" style="color:#dc3545; font-size:16px;"></i>';
    } else if (type === "info") {
        icon = '<i class="bi bi-info-circle-fill" style="color:#c8a165; font-size:16px;"></i>';
    }
    
    toast.innerHTML = `${icon} <span>${message}</span>`;
    container.appendChild(toast);
    
    setTimeout(() => {
        toast.style.opacity = "0";
        toast.style.transform = "translateY(-20px)";
        setTimeout(() => {
            toast.remove();
        }, 300);
    }, 3000);
}
