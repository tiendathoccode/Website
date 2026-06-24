document.addEventListener('DOMContentLoaded', function() {

    // ── CART BADGE (đồng bộ từ localStorage) ────────────────────────────────
    function updateHeaderCartBadge() {
        const badge = document.getElementById('headerCartBadge');
        if (!badge) return;
        const qty = Cart.getTotalQty();
        badge.textContent = qty;
        badge.style.display = qty > 0 ? 'flex' : 'none';
    }
    updateHeaderCartBadge();
    window.addEventListener('storage', e => { if (e.key === 'aurrelia_cart') updateHeaderCartBadge(); });
    window.addEventListener('cart-updated', updateHeaderCartBadge);

    // 1. TÍNH NĂNG THẢ TIM SẢN PHẨM (Dùng Delegation để nhận diện cả sản phẩm load ngầm)
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('toggle-heart')) {
            e.target.classList.toggle('far');
            e.target.classList.toggle('fas');
            e.target.style.color = e.target.classList.contains('fas') ? '#dc3545' : '';
            
            // Lấy ID sản phẩm để Backend biết tim nào được bấm
            const productId = e.target.getAttribute('data-product-id');
            console.log('Đã thả/hủy tim sản phẩm ID:', productId);
        }
    });

    // 2. NÚT XÓA BỘ LỌC
    const clearFilterBtn = document.querySelector('.filter-clear');
    if (clearFilterBtn) {
        clearFilterBtn.addEventListener('click', function(e) {
            e.preventDefault();
            const checkboxes = document.querySelectorAll('.form-check-input');
            checkboxes.forEach(cb => cb.checked = false);
            console.log('Đã xóa cấu hình lọc.');
        });
    }

    // 3. NÚT ÁP DỤNG BỘ LỌC
    const applyFilterBtn = document.querySelector('.btn-gold');
    if (applyFilterBtn) {
        applyFilterBtn.addEventListener('click', function() {
            // Nơi Backend bắt sự kiện để lọc mảng dữ liệu
            console.log('Đang gọi API lọc sản phẩm...');
        });
    }

    // 4. NÚT TẢI THÊM SẢN PHẨM
    const loadMoreBtn = document.querySelector('.btn-outline-dark.rounded-pill');
    if (loadMoreBtn && loadMoreBtn.textContent.trim() === 'TẢI THÊM SẢN PHẨM') {
        loadMoreBtn.addEventListener('click', function() {
            const originalText = this.textContent;
            this.textContent = 'ĐANG TẢI...';
            this.disabled = true;

            // Giả lập thời gian tải. Backend sẽ nhét API fetch vào đây.
            setTimeout(() => {
                this.textContent = originalText;
                this.disabled = false;
                console.log('Đã load xong trang tiếp theo.');
            }, 1000);
        });
    }

});