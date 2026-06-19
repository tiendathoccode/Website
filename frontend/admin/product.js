// Dữ liệu mẫu (Mock Data)
const productsData = [
    { name: "Aurum Solitaire", desc: "Hand-finished 18k Gold", category: "Necklace", sku: "AUR-SOL-001", price: 1250, status: "IN STOCK", image: "https://images.unsplash.com/photo-1599643478518-a784e5dc4c8f?w=150&auto=format&fit=crop&q=60", dateAdded: '2026-06-01' },
    { name: "Emerald Eternal", desc: "Emerald Cut Diamond", category: "Ring", sku: "EME-ETR-042", price: 4500, status: "LOW STOCK", image: "https://images.unsplash.com/photo-1605100804763-247f67b3557e?w=150&auto=format&fit=crop&q=60", dateAdded: '2026-06-05' },
    { name: "Lumina Drops", desc: "South Sea Pearls", category: "Earrings", sku: "LUM-DRP-881", price: 850, status: "OUT OF STOCK", image: "https://images.unsplash.com/photo-1630019852942-f89202989a59?w=150&auto=format&fit=crop&q=60", dateAdded: '2026-06-08' },
    { name: "Celestial Cuff", desc: "Rose Gold 14k", category: "Bracelets", sku: "CEL-CUF-099", price: 2100, status: "IN STOCK", image: "https://images.unsplash.com/photo-1611591437281-460bfbe1220a?w=150&auto=format&fit=crop&q=60", dateAdded: '2026-06-02' },
    { name: "Sapphire Tears", desc: "White Gold 18k", category: "Earrings", sku: "SAP-TEA-102", price: 3200, status: "IN STOCK", image: "https://images.unsplash.com/photo-1599643478518-a784e5dc4c8f?w=150&auto=format&fit=crop&q=60", dateAdded: '2026-06-09' },
    { name: "Ruby Heart", desc: "Vintage Cut", category: "Necklace", sku: "RUB-HRT-005", price: 1850, status: "LOW STOCK", image: "https://images.unsplash.com/photo-1605100804763-247f67b3557e?w=150&auto=format&fit=crop&q=60", dateAdded: '2026-06-03' }
];

// Trạng thái ứng dụng (State)
let state = {
    data: [...productsData],
    searchQuery: "",
    sortOrder: "newest",
    tempCategory: null, 
    activeCategory: null,
    currentPage: 1,
    itemsPerPage: 4
};

// Khởi tạo các Element DOM
const DOM = {
    searchInput: document.getElementById('searchInput'),
    sortSelect: document.getElementById('sortSelect'),
    filterBtns: document.querySelectorAll('.filter-item'),
    applyFiltersBtn: document.getElementById('applyFilters'),
    clearFiltersBtn: document.getElementById('clearFilters'),
    tableBody: document.getElementById('productTableBody'),
    paginationInfo: document.getElementById('paginationInfo'),
    paginationControls: document.getElementById('paginationControls')
};

// --- LOGIC XỬ LÝ DỮ LIỆU ---

function processData() {
    let filteredData = [...productsData];

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
        DOM.tableBody.innerHTML = `<tr><td colspan="6" class="text-center py-4 text-muted">No products found.</td></tr>`;
        return;
    }

    paginatedItems.forEach(item => {
        // Cấu hình nhãn trạng thái (Badge)
        let badgeClass = "badge-instock";
        if (item.status === "LOW STOCK") badgeClass = "badge-lowstock";
        if (item.status === "OUT OF STOCK") badgeClass = "badge-outofstock";

        // Định dạng tiền tệ USD vắn tắt chuẩn Luxury
        const formattedPrice = new Intl.NumberFormat('en-US', { 
            style: 'currency', 
            currency: 'USD', 
            maximumFractionDigits: 0 
        }).format(item.price);

        const row = document.createElement('tr');
        row.className = "border-bottom row-hover";
        row.innerHTML = `
            <td class="ps-4 py-3">
                <div class="d-flex align-items-center gap-3">
                    <img src="${item.image}" class="product-thumb object-fit-cover" alt="${item.name}">
                    <div>
                        <h6 class="mb-1 fw-bold text-dark font-xs">${item.name}</h6>
                        <p class="text-muted font-xs mb-0">${item.desc}</p>
                    </div>
                </div>
            </td>
            <td class="font-xs text-muted align-middle">${item.category}</td>
            <td class="font-xs text-muted font-monospace align-middle">${item.sku}</td>
            <td class="font-numeric fw-medium align-middle">${formattedPrice}</td>
            <td class="align-middle"><span class="badge badge-custom ${badgeClass}">${item.status}</span></td>
            <td class="pe-4 text-end align-middle">
                <button class="btn btn-sm btn-icon border-0" title="Edit Product"><i class="bi bi-pencil"></i></button>
            </td>
        `;
        DOM.tableBody.appendChild(row);
    });
}

function renderPagination() {
    if (!DOM.paginationInfo || !DOM.paginationControls) return;

    const totalItems = state.data.length;
    const totalPages = Math.ceil(totalItems / state.itemsPerPage);
    
    // Cập nhật dòng text hiển thị tiến trình (vd: Showing 1 to 4 of 6 results)
    const startCount = totalItems === 0 ? 0 : ((state.currentPage - 1) * state.itemsPerPage) + 1;
    const endCount = Math.min(state.currentPage * state.itemsPerPage, totalItems);
    DOM.paginationInfo.textContent = `Showing ${startCount} to ${endCount} of ${totalItems} results`;

    // Cập nhật các nút điều hướng phân trang
    DOM.paginationControls.innerHTML = "";
    
    // Nút điều hướng quay lại (Previous)
    const prevLi = document.createElement('li');
    prevLi.className = `page-item ${state.currentPage === 1 ? 'disabled' : ''}`;
    prevLi.innerHTML = `<a class="page-link border-0 ${state.currentPage === 1 ? 'text-muted' : 'text-dark'}" href="#" data-page="prev"><i class="bi bi-chevron-left"></i></a>`;
    DOM.paginationControls.appendChild(prevLi);

    // Các ô số trang cụ thể
    for (let i = 1; i <= totalPages; i++) {
        const li = document.createElement('li');
        li.className = `page-item ${i === state.currentPage ? 'active' : ''}`;
        li.innerHTML = `<a class="page-link border-0 ${i === state.currentPage ? 'rounded-1' : 'text-dark'}" href="#" data-page="${i}">${i}</a>`;
        DOM.paginationControls.appendChild(li);
    }

    // Nút điều hướng kế tiếp (Next)
    const nextLi = document.createElement('li');
    nextLi.className = `page-item ${state.currentPage === totalPages || totalPages === 0 ? 'disabled' : ''}`;
    nextLi.innerHTML = `<a class="page-link border-0 ${state.currentPage === totalPages || totalPages === 0 ? 'text-muted' : 'text-dark'}" href="#" data-page="next"><i class="bi bi-chevron-right"></i></a>`;
    DOM.paginationControls.appendChild(nextLi);
}

// --- SỰ KIỆN (EVENT LISTENERS) ---

// 1. Xử lý tìm kiếm khi nhập liệu
if (DOM.searchInput) {
    DOM.searchInput.addEventListener('input', (e) => {
        state.searchQuery = e.target.value;
        state.currentPage = 1; // Khởi tạo lại trang 1 khi lọc từ khóa mới
        processData();
    });
}

// 2. Xử lý bộ lọc sắp xếp giá/thời gian
if (DOM.sortSelect) {
    DOM.sortSelect.addEventListener('change', (e) => {
        state.sortOrder = e.target.value;
        state.currentPage = 1;
        processData();
    });
}

// 3. Chọn tạm thời danh mục lọc
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

// 4. Xác nhận áp dụng bộ lọc danh mục
if (DOM.applyFiltersBtn) {
    DOM.applyFiltersBtn.addEventListener('click', () => {
        state.activeCategory = state.tempCategory;
        state.currentPage = 1; 
        processData();
    });
}

// 5. Làm mới toàn bộ điều kiện lọc (Clear All)
if (DOM.clearFiltersBtn) {
    DOM.clearFiltersBtn.addEventListener('click', (e) => {
        e.preventDefault();
        state.tempCategory = null;
        state.activeCategory = null;
        state.searchQuery = "";
        state.sortOrder = "newest";
        state.currentPage = 1;
        
        // Hoàn tác giao diện Input/Select về mặc định
        if (DOM.searchInput) DOM.searchInput.value = "";
        if (DOM.sortSelect) DOM.sortSelect.value = "newest";
        
        DOM.filterBtns.forEach(b => {
            b.classList.remove('active');
            b.classList.add('text-muted');
        });
        
        processData();
    });
}

// 6. Nhấp chuột chuyển trang (Phân trang mượt)
if (DOM.paginationControls) {
    DOM.paginationControls.addEventListener('click', (e) => {
        e.preventDefault();
        const link = e.target.closest('a');
        
        // Chặn click nếu click ngoài nút hoặc nút đang bị khóa (disabled)
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

// Khởi chạy ứng dụng lần đầu khi tải trang
document.addEventListener('DOMContentLoaded', () => {
    processData();
});