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

// Khởi tạo các Element DOM
const DOM = {
    searchInput: document.getElementById('searchInput'),
    sortSelect: document.getElementById('sortSelect'),
    filterBtns: document.querySelectorAll('.filter-item'),
    applyFiltersBtn: document.getElementById('applyFilters'),
    clearFiltersBtn: document.getElementById('clearFilters'),
    tableBody: document.getElementById('productTableBody'),
    paginationInfo: document.getElementById('paginationInfo'),
    paginationControls: document.getElementById('paginationControls'),
    addProductForm: document.getElementById('addProductForm'),
    editModalElement: document.getElementById('editProductModal'),
    editProductForm: document.getElementById('editProductForm'),
    editOldSku: document.getElementById('editOldSku'),
    editName: document.getElementById('editProductName'),
    editDesc: document.getElementById('editProductDesc'),
    editCategory: document.getElementById('editProductCategory'),
    editSku: document.getElementById('editProductSku'),
    editPrice: document.getElementById('editProductPrice'),
    editStock: document.getElementById('editProductStock')
};

// Trạng thái ứng dụng (State)
let state = {
    data: window.productsData || [],
    searchQuery: "",
    sortOrder: "newest",
    tempCategory: null, 
    activeCategory: null,
    currentPage: 1,
    itemsPerPage: 8
};

// --- LOGIC XỬ LÝ DỮ LIỆU ---
function processData() {
    let filteredData = [...(window.productsData || [])]; 

    // 1. Lọc theo danh mục (Category)
    if (state.activeCategory) {
        filteredData = filteredData.filter(item => item.category === state.activeCategory);
    }

    // 2. Tìm kiếm (Search)
    if (state.searchQuery) {
        const query = state.searchQuery.toLowerCase().trim();
        filteredData = filteredData.filter(item => 
            item.name.toLowerCase().includes(query) || 
            item.sku.toLowerCase().includes(query)
        );
    }

    // 3. Sắp xếp (Sort)
    if (state.sortOrder === 'price-asc') {
        filteredData.sort((a, b) => a.price - b.price);
    } else if (state.sortOrder === 'price-desc') {
        filteredData.sort((a, b) => b.price - a.price);
    } else {
        // Mặc định là Newest (Mới nhất)
        filteredData.sort((a, b) => new Date(b.dateAdded) - new Date(a.dateAdded));
    }

    state.data = filteredData;
    render();
}

// --- LOGIC RENDER GIAO DIỆN ---
function render() {
    renderTable();
    renderPagination();
}

function renderTable() {
    if (!DOM.tableBody) return;
    DOM.tableBody.innerHTML = "";
    
    // Tính toán vị trí phân trang
    const startIndex = (state.currentPage - 1) * state.itemsPerPage;
    const endIndex = startIndex + state.itemsPerPage;
    const paginatedItems = state.data.slice(startIndex, endIndex);

    if (paginatedItems.length === 0) {
        DOM.tableBody.innerHTML = `
            <tr>
                <td colspan="6" class="text-center py-5 text-muted">
                    <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                    Không có sản phẩm nào được tìm thấy
                </td>
            </tr>
        `;
        return;
    }

    paginatedItems.forEach(item => {
        let statusBadge = "";
        if (item.status === "IN STOCK") {
            statusBadge = `<span class="badge bg-success bg-opacity-10 text-success rounded-0 font-xs">IN STOCK</span>`;
        } else if (item.status === "LOW STOCK") {
            statusBadge = `<span class="badge bg-warning bg-opacity-10 text-warning rounded-0 font-xs">LOW STOCK (${item.stock})</span>`;
        } else {
            statusBadge = `<span class="badge bg-danger bg-opacity-10 text-danger rounded-0 font-xs">OUT OF STOCK</span>`;
        }

        const row = document.createElement("tr");
        row.innerHTML = `
            <td class="ps-4">
                <div class="d-flex align-items-center gap-3">
                    <img src="${item.image}" alt="${item.name}" class="rounded border" style="width: 50px; height: 50px; object-fit: cover;">
                    <div>
                        <h6 class="mb-1 fw-bold text-dark font-xs">${item.name}</h6>
                        <p class="text-muted mb-0 font-xs text-truncate" style="max-width: 250px;">${item.desc}</p>
                    </div>
                </div>
            </td>
            <td class="font-xs text-muted">${item.category}</td>
            <td class="font-xs font-numeric text-muted">${item.sku}</td>
            <td class="font-xs fw-bold font-numeric text-dark">${item.price.toLocaleString('vi-VN')}₫</td>
            <td>${statusBadge}</td>
            <td class="pe-4 text-end">
                <div class="d-flex justify-content-end gap-2">
                    <button class="btn btn-outline-custom p-2 edit-btn" data-id="${item.id}" title="Edit product"><i class="bi bi-pencil"></i></button>
                    <button class="btn btn-outline-danger p-2 delete-btn" data-id="${item.id}" data-name="${item.name}" title="Delete product"><i class="bi bi-trash"></i></button>
                </div>
            </td>
        `;
        DOM.tableBody.appendChild(row);
    });
}

function renderPagination() {
    if (!DOM.paginationControls || !DOM.paginationInfo) return;
    
    DOM.paginationControls.innerHTML = "";
    
    const totalItems = state.data.length;
    const totalPages = Math.ceil(totalItems / state.itemsPerPage);

    if (totalItems === 0) {
        DOM.paginationInfo.textContent = "Hiển thị 0 của 0 sản phẩm";
        return;
    }

    const startNum = (state.currentPage - 1) * state.itemsPerPage + 1;
    const endNum = Math.min(state.currentPage * state.itemsPerPage, totalItems);
    DOM.paginationInfo.textContent = `Hiển thị từ ${startNum} đến ${endNum} trong tổng số ${totalItems} kết quả`;

    if (totalPages <= 1) return;

    // Nút Previous
    const prevLi = document.createElement("li");
    prevLi.className = `page-item ${state.currentPage === 1 ? 'disabled' : ''}`;
    prevLi.innerHTML = `<a class="page-link" href="#" data-page="prev"><i class="bi bi-chevron-left"></i></a>`;
    DOM.paginationControls.appendChild(prevLi);

    // Các nút trang số
    for (let i = 1; i <= totalPages; i++) {
        const pageLi = document.createElement("li");
        pageLi.className = `page-item ${state.currentPage === i ? 'active' : ''}`;
        pageLi.innerHTML = `<a class="page-link" href="#" data-page="${i}">${i}</a>`;
        DOM.paginationControls.appendChild(pageLi);
    }

    // Nút Next
    const nextLi = document.createElement("li");
    nextLi.className = `page-item ${state.currentPage === totalPages ? 'disabled' : ''}`;
    nextLi.innerHTML = `<a class="page-link" href="#" data-page="next"><i class="bi bi-chevron-right"></i></a>`;
    DOM.paginationControls.appendChild(nextLi);
}

// Lắng nghe sự kiện Tìm kiếm
if (DOM.searchInput) {
    DOM.searchInput.addEventListener("input", (e) => {
        state.searchQuery = e.target.value;
        state.currentPage = 1;
        processData();
    });
}

// Lắng nghe sự kiện Sắp xếp
if (DOM.sortSelect) {
    DOM.sortSelect.addEventListener("change", (e) => {
        state.sortOrder = e.target.value;
        state.currentPage = 1;
        processData();
    });
}

// Chọn bộ lọc danh mục
DOM.filterBtns.forEach(btn => {
    btn.addEventListener('click', (e) => {
        DOM.filterBtns.forEach(b => {
            b.classList.remove('active');
            b.classList.add('text-muted');
        });
        
        const targetBtn = e.currentTarget;
        targetBtn.classList.add('active');
        targetBtn.classList.remove('text-muted');
        
        state.tempCategory = targetBtn.getAttribute('data-category');
    });
});

// Áp dụng bộ lọc
if (DOM.applyFiltersBtn) {
    DOM.applyFiltersBtn.addEventListener('click', () => {
        state.activeCategory = state.tempCategory;
        state.currentPage = 1; 
        processData();
    });
}

// Xóa tất cả bộ lọc
if (DOM.clearFiltersBtn) {
    DOM.clearFiltersBtn.addEventListener('click', (e) => {
        e.preventDefault();
        state.tempCategory = null;
        state.activeCategory = null;
        state.searchQuery = "";
        state.sortOrder = "newest";
        state.currentPage = 1;
        
        if (DOM.searchInput) DOM.searchInput.value = "";
        if (DOM.sortSelect) DOM.sortSelect.value = "newest";
        
        DOM.filterBtns.forEach(b => {
            b.classList.remove('active');
            b.classList.add('text-muted');
        });
        
        processData();
    });
}

// Xử lý chuyển trang phân trang
if (DOM.paginationControls) {
    DOM.paginationControls.addEventListener('click', (e) => {
        e.preventDefault();
        const link = e.target.closest('a');
        if (!link || link.parentElement.classList.contains('disabled')) return;

        const pageAction = link.getAttribute('data-page');
        const totalPages = Math.ceil(state.data.length / state.itemsPerPage);

        if (pageAction === 'prev' && state.currentPage > 1) {
            state.currentPage--;
        } else if (pageAction === 'next' && state.currentPage < totalPages) {
            state.currentPage++;
        } else if (!isNaN(pageAction)) {
            state.currentPage = parseInt(pageAction);
        }

        render();
    });
}

// Xử lý Chỉnh sửa & Xóa sản phẩm trực tiếp từ DB qua AJAX
if (DOM.tableBody) {
    const editModal = DOM.editModalElement ? new bootstrap.Modal(DOM.editModalElement) : null;

    DOM.tableBody.addEventListener('click', (e) => {
        const editBtn = e.target.closest('.edit-btn');
        const deleteBtn = e.target.closest('.delete-btn');

        // NÚT XÓA SẢN PHẨM KHỎI DATABASE
        if (deleteBtn) {
            const productId = deleteBtn.getAttribute('data-id');
            const productName = deleteBtn.getAttribute('data-name');

            if (confirm(`Bạn có chắc chắn muốn xóa sản phẩm "${productName}" khỏi hệ thống?`)) {
                const formData = new FormData();
                formData.append("product_id", productId);

                fetch("/index.php?page=admin_api_delete_product", {
                    method: "POST",
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    if (data.status === "ok") {
                        showToast(data.message, "success");
                        setTimeout(() => {
                            location.reload();
                        }, 1500);
                    } else {
                        showToast("Lỗi xóa sản phẩm: " + data.message, "error");
                    }
                })
                .catch(err => {
                    showToast("Đã xảy ra lỗi kết nối.", "error");
                });
            }
        }

        // NÚT SỬA SẢN PHẨM (MỞ MODAL)
        if (editBtn) {
            const productId = editBtn.getAttribute('data-id');
            const targetProduct = (window.productsData || []).find(p => p.id == productId);

            if (targetProduct && editModal) {
                DOM.editOldSku.value = targetProduct.id; // Lưu id sản phẩm cần sửa
                DOM.editName.value = targetProduct.name;
                DOM.editDesc.value = targetProduct.desc || "";
                DOM.editCategory.value = targetProduct.category_id; // category_id tương ứng
                DOM.editSku.value = targetProduct.sku;
                DOM.editPrice.value = targetProduct.price;
                DOM.editStock.value = targetProduct.stock;

                editModal.show();
            }
        }
    });

    // Form submit cập nhật sản phẩm vào DB
    if (DOM.editProductForm) {
        DOM.editProductForm.addEventListener('submit', (e) => {
            e.preventDefault();

            const productId = DOM.editOldSku.value;
            const formData = new FormData();
            formData.append("product_id", productId);
            formData.append("name", DOM.editName.value.trim());
            formData.append("category_id", DOM.editCategory.value);
            formData.append("price", DOM.editPrice.value);
            formData.append("stock", DOM.editStock.value);
            formData.append("description", DOM.editDesc.value.trim());

            fetch("/index.php?page=admin_api_edit_product", {
                method: "POST",
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === "ok") {
                    editModal.hide();
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
}

// Khởi chạy
document.addEventListener('DOMContentLoaded', () => {
    processData();
});
