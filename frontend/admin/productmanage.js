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

// --- LOGIC XỬ LÝ DỮ LIỆU ---

function processData() {
    let localData = JSON.parse(localStorage.getItem('luxury_products'));
    if (!localData) {
        localStorage.setItem('luxury_products', JSON.stringify(productsData));
        localData = [...productsData];
    }
    
    let filteredData = [...localData]; 

    // 1. Lọc theo danh mục (Category) - Giữ nguyên phía dưới
    if (state.activeCategory) {
        filteredData = filteredData.filter(item => item.category === state.activeCategory);
    }

    // 2. Tìm kiếm (Search) - Giữ nguyên
    if (state.searchQuery) {
        const query = state.searchQuery.toLowerCase().trim();
        filteredData = filteredData.filter(item => 
            item.name.toLowerCase().includes(query) || 
            item.sku.toLowerCase().includes(query)
        );
    }

    // 3. Sắp xếp (Sort) - Giữ nguyên
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
                        <p class="text-muted font-xs mb-0 text-truncate" style="max-width: 180px;">${item.desc}</p>
                    </div>
                </div>
            </td>
            <td class="font-xs text-muted align-middle">${item.category}</td>
            <td class="font-xs text-muted font-monospace align-middle">${item.sku}</td>
            <td class="font-numeric fw-medium align-middle">${formattedPrice}</td>
            <td class="align-middle"><span class="badge badge-custom ${badgeClass}">${item.status}</span></td>
            
            <td class="pe-4 text-end align-middle">
                <button class="btn btn-sm btn-icon border-0 edit-btn" data-sku="${item.sku}" title="Edit Product">
                    <i class="bi bi-pencil"></i>
                </button>
                <button class="btn btn-sm btn-icon border-0 text-danger delete-btn" data-sku="${item.sku}" title="Delete Product">
                    <i class="bi bi-trash"></i>
                </button>
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

// 7. Xử lý khi người dùng thêm sản phẩm mới (Publish Product)
if (DOM.addProductForm) {
    DOM.addProductForm.addEventListener('submit', (e) => {
        e.preventDefault(); // CHẶN LỖI 405 

        const formData = new FormData(DOM.addProductForm);
        const priceValue = parseFloat(formData.get('price')) || 0;
        const stockValue = parseInt(formData.get('stock_quantity')) || 0;

        let statusValue = "IN STOCK";
        if (stockValue === 0) statusValue = "OUT OF STOCK";
        else if (stockValue <= 3) statusValue = "LOW STOCK";

        // Hàm phụ để lưu sản phẩm sau khi đã có đường link ảnh
        const saveProduct = (imageUrl) => {
            const newProduct = {
                name: formData.get('product_name'),
                desc: formData.get('description') || "No description.",
                category: formData.get('category'),
                sku: formData.get('sku').toUpperCase(),
                price: priceValue,
                status: statusValue,
                image: imageUrl, 
                dateAdded: new Date().toISOString().split('T')[0]
            };

            let currentProducts = JSON.parse(localStorage.getItem('luxury_products')) || productsData;
            currentProducts.unshift(newProduct);
            localStorage.setItem('luxury_products', JSON.stringify(currentProducts));

            DOM.addProductForm.reset();
            alert(`Product "${newProduct.name}" published successfully!`);
            
            if(DOM.tableBody) {
                state.currentPage = 1;
                processData();
            } else {
                window.location.href = "index.html"; 
            }
        };

       
        const imageFile = formData.get('product_images[]'); 

        if (imageFile && imageFile.size > 0) {
            const reader = new FileReader();
            reader.onload = function(event) {
                const base64Image = event.target.result;
                saveProduct(base64Image); // Gọi hàm lưu với ảnh thật
            };
            reader.readAsDataURL(imageFile);
        } else {
            const defaultImage = "https://st2.depositphotos.com/1561359/12101/v/950/depositphotos_121012076-stock-illustration-blank-photo-icon.jpg";
            saveProduct(defaultImage);
        }
    });
}
// 8. Xử lý tính năng Chỉnh sửa (Form Modal) và Xóa sản phẩm trực tiếp trên bảng
if (DOM.tableBody) {
    // Khởi tạo Bootstrap Modal Instance để điều khiển ẩn hiện qua JS
    const editModal = DOM.editModalElement ? new bootstrap.Modal(DOM.editModalElement) : null;

    DOM.tableBody.addEventListener('click', (e) => {
        const editBtn = e.target.closest('.edit-btn');
        const deleteBtn = e.target.closest('.delete-btn');
        let currentProducts = JSON.parse(localStorage.getItem('luxury_products')) || productsData;

        //NÚT XÓA
        if (deleteBtn) {
            const sku = deleteBtn.getAttribute('data-sku');
            const targetProduct = currentProducts.find(p => p.sku === sku);

            if (targetProduct && confirm(`Bạn có chắc chắn muốn xóa sản phẩm "${targetProduct.name}" khỏi hệ thống?`)) {
                currentProducts = currentProducts.filter(p => p.sku !== sku);
                localStorage.setItem('luxury_products', JSON.stringify(currentProducts));
                processData();
            }
        }

        // NÚT SỬA 
        if (editBtn) {
            const sku = editBtn.getAttribute('data-sku');
            const targetProduct = currentProducts.find(p => p.sku === sku);

            if (targetProduct && editModal) {
                // Đổ toàn bộ thông tin sản phẩm hiện tại vào các ô input trên Form
                DOM.editOldSku.value = targetProduct.sku; // Giữ lại SKU gốc để định vị
                DOM.editName.value = targetProduct.name;
                DOM.editDesc.value = targetProduct.desc || "";
                DOM.editCategory.value = targetProduct.category;
                DOM.editSku.value = targetProduct.sku;
                DOM.editPrice.value = targetProduct.price;
                
                // Gán tạm số lượng kho dựa trên nhãn trạng thái hiện tại
                if (targetProduct.status === "OUT OF STOCK") DOM.editStock.value = 0;
                else if (targetProduct.status === "LOW STOCK") DOM.editStock.value = 2;
                else DOM.editStock.value = 15;

                // Kích hoạt hiển thị Form Modal lên màn hình
                editModal.show();
            }
        }
    });

    
    if (DOM.editProductForm) {
        DOM.editProductForm.addEventListener('submit', (e) => {
            e.preventDefault();

            let currentProducts = JSON.parse(localStorage.getItem('luxury_products')) || productsData;
            const oldSku = DOM.editOldSku.value;
            
            // Tìm đúng vị trí sản phẩm cũ dựa trên SKU gốc
            const productIndex = currentProducts.findIndex(p => p.sku === oldSku);

            if (productIndex !== -1) {
                const stockCount = parseInt(DOM.editStock.value) || 0;
                let statusValue = "IN STOCK";
                if (stockCount === 0) statusValue = "OUT OF STOCK";
                else if (stockCount <= 3) statusValue = "LOW STOCK";

                // Cập nhật đồng loạt toàn bộ các trường dữ liệu mới
                currentProducts[productIndex] = {
                    ...currentProducts[productIndex], // Giữ lại các trường không sửa (ví dụ: image, dateAdded)
                    name: DOM.editName.value.trim(),
                    desc: DOM.editDesc.value.trim() || "No description.",
                    category: DOM.editCategory.value,
                    sku: DOM.editSku.value.trim().toUpperCase(),
                    price: parseFloat(DOM.editPrice.value) || 0,
                    status: statusValue
                };

                // Lưu lại vào bộ nhớ trình duyệt
                localStorage.setItem('luxury_products', JSON.stringify(currentProducts));
                
                // Đóng form modal và làm mới bảng dữ liệu hiển thị
                editModal.hide();
                processData();
                
                alert("Product details updated successfully!");
            }
        });
    }
}
// Khởi chạy ứng dụng lần đầu khi tải trang
document.addEventListener('DOMContentLoaded', () => {
    processData();
});
