document.addEventListener('DOMContentLoaded', function() {
    
    // 1. TÍNH NĂNG THẢ TIM SẢN PHẨM (Dùng Delegation để nhận diện cả sản phẩm load ngầm)
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('toggle-heart')) {
            e.target.classList.toggle('far');
            e.target.classList.toggle('fas');
            e.target.style.color = e.target.classList.contains('fas') ? '#dc3545' : '';
            
            const productId = e.target.getAttribute('data-product-id');
            console.log('Đã thả/hủy tim sản phẩm ID:', productId);
        }
    });

    // 2. BỘ LỌC MỨC GIÁ DÙNG CHECKBOX (Tự động lọc ngay khi click)
    const priceCbs = document.querySelectorAll('.price-filter-cb');
    priceCbs.forEach(cb => {
        cb.addEventListener('change', function() {
            const urlParams = new URLSearchParams(window.location.search);
            if (this.checked) {
                // Chỉ cho chọn 1 checkbox tại 1 thời điểm
                priceCbs.forEach(other => {
                    if (other !== this) other.checked = false;
                });
                urlParams.set('price_range', this.value);
            } else {
                urlParams.delete('price_range');
            }
            window.location.search = urlParams.toString();
        });
    });

    // 3. NÚT ÁP DỤNG BỘ LỌC (Hỗ trợ click nút Áp dụng nếu người dùng bấm)
    const btnApplyFilterUser = document.getElementById('btnApplyFilterUser');
    if (btnApplyFilterUser) {
        btnApplyFilterUser.addEventListener('click', function() {
            const checkedCb = document.querySelector('.price-filter-cb:checked');
            const priceRange = checkedCb ? checkedCb.value : "";
            
            const urlParams = new URLSearchParams(window.location.search);
            if (priceRange) {
                urlParams.set('price_range', priceRange);
            } else {
                urlParams.delete('price_range');
            }
            window.location.search = urlParams.toString();
        });
    }

    // 4. SẮP XẾP SẢN PHẨM
    const sortSelectUser = document.getElementById('sortSelectUser');
    if (sortSelectUser) {
        sortSelectUser.addEventListener('change', function() {
            const urlParams = new URLSearchParams(window.location.search);
            urlParams.set('sort', this.value);
            window.location.search = urlParams.toString();
        });
    }

    // 5. TÌM KIẾM TRÊN HEADER BẰNG THANH NHẬP LIỆU CO GIÃN
    const searchBtn = document.getElementById('navbarSearchBtn');
    const searchInput = document.getElementById('navbarSearchInput');
    if (searchBtn && searchInput) {
        searchBtn.addEventListener('click', function(e) {
            e.preventDefault();
            if (searchInput.style.display === 'none' || searchInput.style.display === '') {
                searchInput.style.display = 'block';
                searchInput.focus();
            } else {
                const query = searchInput.value.trim();
                const urlParams = new URLSearchParams(window.location.search);
                urlParams.set('page', 'home');
                if (query) {
                    urlParams.set('search', query);
                    window.location.search = urlParams.toString();
                } else {
                    searchInput.style.display = 'none';
                }
            }
        });
        
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                const query = this.value.trim();
                const urlParams = new URLSearchParams(window.location.search);
                urlParams.set('page', 'home');
                if (query) {
                    urlParams.set('search', query);
                } else {
                    urlParams.delete('search');
                }
                window.location.search = urlParams.toString();
            }
        });
    }

    // 6. NÚT TẢI THÊM SẢN PHẨM (giữ hiệu ứng tải)
    const loadMoreBtn = document.querySelector('.btn-outline-dark.rounded-pill');
    if (loadMoreBtn && loadMoreBtn.textContent.trim() === 'TẢI THÊM SẢN PHẨM') {
        loadMoreBtn.addEventListener('click', function() {
            const originalText = this.textContent;
            this.textContent = 'ĐANG TẢI...';
            this.disabled = true;

            setTimeout(() => {
                this.textContent = originalText;
                this.disabled = false;
                console.log('Đã load xong trang tiếp theo.');
            }, 1000);
        });
    }

    // 7. HÀM PHÂN TRANG CHO MỖI DANH MỤC SẢN PHẨM (Client-side Pagination)
    function paginateCategorySection(sectionEl, itemsPerPage = 6) {
        const productGrid = sectionEl.querySelector('.row.g-4');
        if (!productGrid) return;
        
        const items = Array.from(productGrid.querySelectorAll('.product-item'));
        if (items.length <= itemsPerPage) {
            // Xóa phân trang cũ nếu có
            const oldPager = sectionEl.querySelector('.category-pagination');
            if (oldPager) oldPager.remove();
            items.forEach(item => item.style.display = 'block');
            return;
        }
        
        const totalPages = Math.ceil(items.length / itemsPerPage);
        let currentPage = 1;
        
        function showPage(page) {
            currentPage = page;
            const start = (page - 1) * itemsPerPage;
            const end = start + itemsPerPage;
            
            items.forEach((item, idx) => {
                if (idx >= start && idx < end) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
            
            updatePager();
        }
        
        // Tạo hoặc lấy thanh phân trang
        let pager = sectionEl.querySelector('.category-pagination');
        if (!pager) {
            pager = document.createElement('div');
            pager.className = 'category-pagination d-flex justify-content-center align-items-center gap-2 mt-4';
            sectionEl.appendChild(pager);
        }
        
        function updatePager() {
            pager.innerHTML = '';
            
            // Nút Trước
            const prevBtn = document.createElement('button');
            prevBtn.className = 'btn btn-sm btn-outline-secondary px-3 py-1';
            prevBtn.innerHTML = '<i class="fas fa-chevron-left"></i>';
            prevBtn.disabled = currentPage === 1;
            prevBtn.style.borderRadius = '50px';
            prevBtn.onclick = (e) => {
                e.preventDefault();
                if (currentPage > 1) {
                    showPage(currentPage - 1);
                    scrollToSectionTop();
                }
            };
            pager.appendChild(prevBtn);
            
            // Các số trang
            for (let i = 1; i <= totalPages; i++) {
                const pageBtn = document.createElement('button');
                pageBtn.className = `btn btn-sm px-3 py-1 ${i === currentPage ? 'btn-gold text-white' : 'btn-outline-secondary'}`;
                pageBtn.style.cssText = i === currentPage ? 'background-color: #c8a165; border-color: #c8a165; color: white; border-radius: 50px;' : 'border-radius: 50px;';
                pageBtn.textContent = i;
                pageBtn.onclick = (e) => {
                    e.preventDefault();
                    showPage(i);
                    scrollToSectionTop();
                };
                pager.appendChild(pageBtn);
            }
            
            // Nút Sau
            const nextBtn = document.createElement('button');
            nextBtn.className = 'btn btn-sm btn-outline-secondary px-3 py-1';
            nextBtn.innerHTML = '<i class="fas fa-chevron-right"></i>';
            nextBtn.disabled = currentPage === totalPages;
            nextBtn.style.borderRadius = '50px';
            nextBtn.onclick = (e) => {
                e.preventDefault();
                if (currentPage < totalPages) {
                    showPage(currentPage + 1);
                    scrollToSectionTop();
                }
            };
            pager.appendChild(nextBtn);
        }
        
        function scrollToSectionTop() {
            const productsSection = document.getElementById('products-section');
            if (productsSection) {
                const offset = 80;
                const bodyRect = document.body.getBoundingClientRect().top;
                const elementRect = productsSection.getBoundingClientRect().top;
                const elementPosition = elementRect - bodyRect;
                const offsetPosition = elementPosition - offset;
                window.scrollTo({
                    top: offsetPosition,
                    behavior: 'smooth'
                });
            }
        }
        
        showPage(1);
    }

    // Khởi tạo phân trang ban đầu cho tất cả các danh mục
    const allSections = document.querySelectorAll('.category-section');
    allSections.forEach(section => {
        paginateCategorySection(section, 6);
    });

    // 7b. LỌC DANH MỤC VÀ PHÂN TRANG KHI CLICK CHỌN SIDEBAR
    const categoryLinks = document.querySelectorAll('.category-link');
    categoryLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            const targetId = this.getAttribute('href');
            if (targetId && targetId.startsWith('#')) {
                e.preventDefault();
                
                // Cập nhật trạng thái active sidebar
                categoryLinks.forEach(other => {
                    other.classList.remove('active');
                    const icon = other.querySelector('.icon-active');
                    if (icon) icon.classList.add('d-none');
                });
                this.classList.add('active');
                const activeIcon = this.querySelector('.icon-active');
                if (activeIcon) activeIcon.classList.remove('d-none');

                if (targetId === '#all-sections') {
                    // Hiển thị tất cả danh mục và phân trang lại cho mỗi danh mục
                    allSections.forEach(section => {
                        section.style.display = 'block';
                        paginateCategorySection(section, 6);
                    });
                    
                    const productsSection = document.getElementById('products-section');
                    if (productsSection) {
                        const offset = 80;
                        const bodyRect = document.body.getBoundingClientRect().top;
                        const elementRect = productsSection.getBoundingClientRect().top;
                        const elementPosition = elementRect - bodyRect;
                        const offsetPosition = elementPosition - offset;
                        window.scrollTo({
                            top: offsetPosition,
                            behavior: 'smooth'
                        });
                    }
                } else {
                    // Ẩn tất cả danh mục khác, chỉ hiển thị danh mục đã chọn
                    allSections.forEach(section => {
                        if ('#' + section.id === targetId) {
                            section.style.display = 'block';
                            paginateCategorySection(section, 6);
                        } else {
                            section.style.display = 'none';
                        }
                    });

                    const targetEl = document.querySelector(targetId);
                    if (targetEl) {
                        const offset = 80;
                        const bodyRect = document.body.getBoundingClientRect().top;
                        const elementRect = targetEl.getBoundingClientRect().top;
                        const elementPosition = elementRect - bodyRect;
                        const offsetPosition = elementPosition - offset;
                        window.scrollTo({
                            top: offsetPosition,
                            behavior: 'smooth'
                        });
                    }
                }
            }
        });
    });

    // 8. SMOOTH SCROLL KHI CLICK VỀ CHÚNG TÔI / LIÊN HỆ TRÊN NAVBAR
    const navLinks = document.querySelectorAll('.navbar-nav .nav-link');
    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            const hrefVal = this.getAttribute('href');
            if (hrefVal && hrefVal.includes('#')) {
                const hashIndex = hrefVal.indexOf('#');
                const hash = hrefVal.substring(hashIndex);
                
                // Kiểm tra xem có đang ở trang chủ không
                const isHomePage = window.location.pathname === '/' || 
                                   window.location.pathname.endsWith('index.php') && (window.location.search.includes('page=home') || window.location.search === '');
                                   
                if (isHomePage) {
                    e.preventDefault();
                    const targetEl = document.querySelector(hash);
                    if (targetEl) {
                        const offset = 80;
                        const bodyRect = document.body.getBoundingClientRect().top;
                        const elementRect = targetEl.getBoundingClientRect().top;
                        const elementPosition = elementRect - bodyRect;
                        const offsetPosition = elementPosition - offset;

                        window.scrollTo({
                            top: offsetPosition,
                            behavior: 'smooth'
                        });
                    }
                }
            }
        });
    });

    // 9. SMOOTH SCROLL VÀ LỌC DANH MỤC CHO CÁC NÚT CAROUSEL BANNER
    const bannerButtons = document.querySelectorAll('.banner-section .carousel-item a');
    bannerButtons.forEach(btn => {
        btn.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            if (href && href.startsWith('#')) {
                if (href.startsWith('#category-')) {
                    e.preventDefault();
                    const targetLink = document.querySelector(`.category-link[href="${href}"]`);
                    if (targetLink) {
                        targetLink.click();
                    }
                } else if (href === '#products-section' || href === '#all-sections') {
                    e.preventDefault();
                    const allJewelryLink = document.querySelector('.category-link[href="#all-sections"]');
                    if (allJewelryLink) {
                        allJewelryLink.click();
                    }
                }
            }
        });
    });

    const btnDiscoverNow = document.getElementById('btnDiscoverNow');
    if (btnDiscoverNow) {
        btnDiscoverNow.addEventListener('click', function(e) {
            e.preventDefault();
            const targetEl = document.getElementById('products-section');
            if (targetEl) {
                const offset = 80;
                const bodyRect = document.body.getBoundingClientRect().top;
                const elementRect = targetEl.getBoundingClientRect().top;
                const elementPosition = elementRect - bodyRect;
                const offsetPosition = elementPosition - offset;

                window.scrollTo({
                    top: offsetPosition,
                    behavior: 'smooth'
                });
            }
        });
    }

    // 10. TỰ ĐỘNG LỌC VÀ CUỘN KHI TRANG TẢI CÓ SẴN HASH DANH MỤC
    const initialHash = window.location.hash;
    if (initialHash && initialHash.startsWith('#category-')) {
        setTimeout(() => {
            const targetLink = document.querySelector(`.category-link[href="${initialHash}"]`);
            if (targetLink) {
                targetLink.click();
            }
        }, 300);
    }
});