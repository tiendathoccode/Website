// Dữ liệu User mẫu ban đầu (Mock Data)
const initialUsers = [
    { id: "USR-001", name: "Elena Vance", email: "elena@aurrelia.com", role: "Admin", status: "Active", date: "2023-01-15" },
    { id: "USR-002", name: "Marcus Cole", email: "marcus.c@aurrelia.com", role: "Manager", status: "Active", date: "2024-05-22" },
    { id: "USR-003", name: "Sophie Laurent", email: "sophie.l@aurrelia.com", role: "Staff", status: "Active", date: "2025-11-03" },
    { id: "USR-004", name: "David Chen", email: "david.c@aurrelia.com", role: "Staff", status: "Locked", date: "2026-02-18" }
];

// Khởi tạo các Element DOM
const DOM = {
    tableBody: document.getElementById('userTableBody'),
    userModalEl: document.getElementById('userModal'),
    userForm: document.getElementById('userForm'),
    modalTitle: document.getElementById('userModalTitle'),
    btnOpenAdd: document.getElementById('btnOpenAddModal'),
    
    // Inputs
    inId: document.getElementById('userId'),
    inName: document.getElementById('userName'),
    inEmail: document.getElementById('userEmail'),
    inRole: document.getElementById('userRole'),
    inStatus: document.getElementById('userStatus')
};

// Khởi tạo Bootstrap Modal
const userModal = new bootstrap.Modal(DOM.userModalEl);

// --- HÀM LẤY CHỮ CÁI ĐẦU CHO AVATAR (Giống thiết kế ở ordermanage) ---
function getInitials(name) {
    const parts = name.split(' ');
    if (parts.length >= 2) {
        return (parts[0][0] + parts[parts.length - 1][0]).toUpperCase();
    }
    return name.substring(0, 2).toUpperCase();
}

// --- LOGIC XỬ LÝ & HIỂN THỊ DỮ LIỆU ---
function loadUsers() {
    let users = JSON.parse(localStorage.getItem('aurrelia_users'));
    if (!users) {
        localStorage.setItem('aurrelia_users', JSON.stringify(initialUsers));
        users = [...initialUsers];
    }
    renderTable(users);
}

function renderTable(users) {
    DOM.tableBody.innerHTML = "";
    
    if (users.length === 0) {
        DOM.tableBody.innerHTML = `<tr><td colspan="5" class="text-center py-4 text-muted">Chưa có người dùng nào trong hệ thống.</td></tr>`;
        return;
    }

    users.forEach(user => {
        // Cấu hình nhãn trạng thái sử dụng CSS có sẵn từ style.css
        const badgeClass = user.status === "Active" ? "badge-instock" : "badge-outofstock";
        const roleIcon = user.role === "Admin" ? '<i class="bi bi-shield-lock-fill text-gold me-1"></i>' : '';
        const initials = getInitials(user.name);

        const row = document.createElement('tr');
        row.className = "border-bottom row-hover";
        row.innerHTML = `
            <td class="ps-4 py-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="avatar-sm bg-light-custom rounded-circle d-flex align-items-center justify-content-center fw-bold text-secondary">
                        ${initials}
                    </div>
                    <div>
                        <div class="fw-bold text-dark font-xs">${user.name}</div>
                        <div class="text-muted font-xs">${user.email}</div>
                    </div>
                </div>
            </td>
            <td class="font-xs text-dark align-middle fw-medium">${roleIcon} ${user.role}</td>
            <td class="align-middle">
                <span class="badge badge-custom ${badgeClass}">${user.status}</span>
            </td>
            <td class="font-xs text-muted align-middle">${user.date}</td>
            <td class="pe-4 text-end align-middle">
                <button class="btn btn-sm btn-icon border-0 edit-btn" data-id="${user.id}" title="Sửa thông tin">
                    <i class="bi bi-pencil"></i>
                </button>
                <button class="btn btn-sm btn-icon border-0 text-danger delete-btn" data-id="${user.id}" title="Xóa tài khoản">
                    <i class="bi bi-trash"></i>
                </button>
            </td>
        `;
        DOM.tableBody.appendChild(row);
    });
}

// --- SỰ KIỆN: MỞ MODAL THÊM MỚI ---
DOM.btnOpenAdd.addEventListener('click', () => {
    DOM.userForm.reset();
    DOM.inId.value = ""; // Để trống ID để biết là Thêm Mới
    DOM.modalTitle.textContent = "THÊM TÀI KHOẢN MỚI";
    userModal.show();
});

// --- SỰ KIỆN: LƯU THÔNG TIN (THÊM/SỬA) ---
DOM.userForm.addEventListener('submit', (e) => {
    e.preventDefault();
    let users = JSON.parse(localStorage.getItem('aurrelia_users')) || [];
    
    const idValue = DOM.inId.value;
    
    if (idValue === "") {
        // THÊM MỚI
        const newUser = {
            id: "USR-" + Date.now().toString().slice(-4), // Tạo ID ngẫu nhiên
            name: DOM.inName.value.trim(),
            email: DOM.inEmail.value.trim(),
            role: DOM.inRole.value,
            status: DOM.inStatus.value,
            date: new Date().toISOString().split('T')[0]
        };
        users.unshift(newUser);
        alert(`Tài khoản "${newUser.name}" đã được tạo thành công!`);
    } else {
        // CẬP NHẬT (SỬA)
        const userIndex = users.findIndex(u => u.id === idValue);
        if (userIndex !== -1) {
            users[userIndex].name = DOM.inName.value.trim();
            users[userIndex].email = DOM.inEmail.value.trim();
            users[userIndex].role = DOM.inRole.value;
            users[userIndex].status = DOM.inStatus.value;
            alert("Cập nhật thông tin thành công!");
        }
    }
    
    localStorage.setItem('aurrelia_users', JSON.stringify(users));
    userModal.hide();
    loadUsers();
});

// --- SỰ KIỆN: SỬA HOẶC XÓA TẠI BẢNG ---
DOM.tableBody.addEventListener('click', (e) => {
    let users = JSON.parse(localStorage.getItem('aurrelia_users')) || [];
    const editBtn = e.target.closest('.edit-btn');
    const deleteBtn = e.target.closest('.delete-btn');
    
    // XỬ LÝ NÚT XÓA
    if (deleteBtn) {
        const id = deleteBtn.getAttribute('data-id');
        const targetUser = users.find(u => u.id === id);
        
        if (targetUser && confirm(`Hành động này không thể hoàn tác. Bạn có chắc chắn muốn xóa tài khoản "${targetUser.name}"?`)) {
            users = users.filter(u => u.id !== id);
            localStorage.setItem('aurrelia_users', JSON.stringify(users));
            loadUsers();
        }
    }
    
    // XỬ LÝ NÚT SỬA
    if (editBtn) {
        const id = editBtn.getAttribute('data-id');
        const targetUser = users.find(u => u.id === id);
        
        if (targetUser) {
            // Đổ dữ liệu vào Modal
            DOM.inId.value = targetUser.id;
            DOM.inName.value = targetUser.name;
            DOM.inEmail.value = targetUser.email;
            DOM.inRole.value = targetUser.role;
            DOM.inStatus.value = targetUser.status;
            
            DOM.modalTitle.textContent = "CHỈNH SỬA TÀI KHOẢN";
            userModal.show();
        }
    }
});

// Khởi chạy khi load trang
document.addEventListener('DOMContentLoaded', loadUsers);