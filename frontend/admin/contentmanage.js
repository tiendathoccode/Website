/* =====================================================
   contentmanage.js — Quản lý Nội dung
   Modules: Banner · Review · FAQ · Messages
   ===================================================== */

'use strict';

// =====================
// TOAST HELPER
// =====================
function showToast(message, type = 'success') {
    let container = document.getElementById('toastContainer');
    if (!container) {
        container = document.createElement('div');
        container.id = 'toastContainer';
        container.className = 'toast-container-custom';
        document.body.appendChild(container);
    }
    const toast = document.createElement('div');
    const icon = type === 'success' ? 'bi-check-circle-fill' : 'bi-x-circle-fill';
    toast.className = `toast-custom toast-${type}`;
    toast.innerHTML = `<i class="bi ${icon}"></i><span>${message}</span>`;
    container.appendChild(toast);
    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transition = 'opacity 0.3s ease';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

// =====================
// TAB NAVIGATION
// =====================
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.content-tab-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('.content-tab-btn').forEach(b => b.classList.remove('active'));
            document.querySelectorAll('.content-tab-panel').forEach(p => p.classList.remove('active'));
            btn.classList.add('active');
            document.getElementById('tab-' + btn.dataset.tab).classList.add('active');
        });
    });

    renderBanners();
    renderReviews();
    renderFaq();
    renderMessages();

    // Review filter
    document.getElementById('reviewFilter').addEventListener('change', renderReviews);

    // Mark all messages read
    document.getElementById('markAllReadBtn').addEventListener('click', () => {
        messages.forEach(m => m.read = true);
        renderMessages();
        updateBadges();
        showToast('Đã đánh dấu tất cả tin nhắn là đã đọc');
    });

    // Banner image preview
    document.getElementById('bannerImageInput').addEventListener('change', function () {
        const file = this.files[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = e => {
            const preview = document.getElementById('bannerPreview');
            const wrap = document.getElementById('bannerPreviewWrap');
            preview.src = e.target.result;
            wrap.classList.remove('d-none');
            _tempBannerImage = e.target.result;
        };
        reader.readAsDataURL(file);
    });
});

// =====================
// BADGE COUNTERS
// =====================
function updateBadges() {
    const pending = reviews.filter(r => r.status === 'pending').length;
    const unread = messages.filter(m => !m.read).length;
    const pBadge = document.getElementById('pendingReviewBadge');
    const mBadge = document.getElementById('unreadMsgBadge');
    pBadge.textContent = pending;
    pBadge.style.display = pending ? '' : 'none';
    mBadge.textContent = unread;
    mBadge.style.display = unread ? '' : 'none';
}

// ==========================================================
// MODULE 1: BANNER
// ==========================================================
let banners = [
    { id: 1, title: 'Bộ sưu tập Ethereal', link: '/products?col=ethereal', order: 1, active: true, image: null },
    { id: 2, title: 'Nhẫn Kim cương Classic', link: '/products?col=classic', order: 2, active: true, image: null },
    { id: 3, title: 'Khuyến mãi Hè 2026', link: '/sale', order: 3, active: false, image: null },
];
let _nextBannerId = 4;
let _tempBannerImage = null;
let _editBannerId = null;

function renderBanners() {
    const list = document.getElementById('bannerList');
    if (!banners.length) {
        list.innerHTML = `<div class="empty-state col-span-full"><i class="bi bi-images"></i><p>Chưa có banner nào. Nhấn "Thêm Banner" để bắt đầu.</p></div>`;
        return;
    }
    const sorted = [...banners].sort((a, b) => a.order - b.order);
    list.innerHTML = sorted.map(b => `
        <div class="banner-card" draggable="true" data-id="${b.id}"
             ondragstart="bannerDragStart(event, ${b.id})"
             ondragover="bannerDragOver(event)"
             ondragleave="bannerDragLeave(event)"
             ondrop="bannerDrop(event, ${b.id})">
            <div class="banner-img-wrap">
                ${b.image
                    ? `<img src="${b.image}" alt="${b.title}">`
                    : `<div class="banner-img-placeholder"><i class="bi bi-image"></i></div>`
                }
                <span class="banner-order-chip"># ${b.order}</span>
                <span class="banner-status-chip ${b.active ? 'on' : 'off'}">${b.active ? 'Hiển thị' : 'Ẩn'}</span>
            </div>
            <div class="banner-body">
                <div class="banner-title-text">${b.title || '(Không có tiêu đề)'}</div>
                <div class="banner-link-text mt-1"><i class="bi bi-link-45deg me-1"></i>${b.link || '—'}</div>
            </div>
            <div class="banner-actions justify-content-between">
                <div class="form-check form-switch mb-0 d-flex align-items-center gap-2">
                    <input class="form-check-input" type="checkbox" ${b.active ? 'checked' : ''}
                           onchange="toggleBanner(${b.id})">
                    <label class="form-check-label font-xs text-muted">Bật/Tắt</label>
                </div>
                <div class="d-flex gap-1">
                    <button class="btn btn-icon" title="Chỉnh sửa" onclick="openBannerModal(${b.id})">
                        <i class="bi bi-pencil"></i>
                    </button>
                    <button class="btn btn-icon text-danger" title="Xoá" onclick="confirmDelete('banner', ${b.id})">
                        <i class="bi bi-trash3"></i>
                    </button>
                </div>
            </div>
        </div>
    `).join('');
}

function openBannerModal(id = null) {
    _editBannerId = id;
    _tempBannerImage = null;
    document.getElementById('bannerImageInput').value = '';
    document.getElementById('bannerPreviewWrap').classList.add('d-none');
    if (id) {
        const b = banners.find(x => x.id === id);
        document.getElementById('bannerModalTitle').textContent = 'CHỈNH SỬA BANNER';
        document.getElementById('bannerId').value = b.id;
        document.getElementById('bannerTitle').value = b.title;
        document.getElementById('bannerLink').value = b.link;
        document.getElementById('bannerOrder').value = b.order;
        document.getElementById('bannerActive').checked = b.active;
        if (b.image) {
            document.getElementById('bannerPreview').src = b.image;
            document.getElementById('bannerPreviewWrap').classList.remove('d-none');
        }
    } else {
        document.getElementById('bannerModalTitle').textContent = 'THÊM BANNER MỚI';
        document.getElementById('bannerId').value = '';
        document.getElementById('bannerTitle').value = '';
        document.getElementById('bannerLink').value = '';
        document.getElementById('bannerOrder').value = banners.length + 1;
        document.getElementById('bannerActive').checked = true;
    }
}

function saveBanner() {
    const title = document.getElementById('bannerTitle').value.trim();
    const link  = document.getElementById('bannerLink').value.trim();
    const order = parseInt(document.getElementById('bannerOrder').value) || 1;
    const active = document.getElementById('bannerActive').checked;

    if (_editBannerId) {
        const b = banners.find(x => x.id === _editBannerId);
        b.title = title;
        b.link  = link;
        b.order = order;
        b.active = active;
        if (_tempBannerImage) b.image = _tempBannerImage;
        showToast('Đã cập nhật banner thành công');
    } else {
        banners.push({ id: _nextBannerId++, title, link, order, active, image: _tempBannerImage });
        showToast('Đã thêm banner mới');
    }
    bootstrap.Modal.getInstance(document.getElementById('bannerModal')).hide();
    renderBanners();
}

function toggleBanner(id) {
    const b = banners.find(x => x.id === id);
    b.active = !b.active;
    renderBanners();
    showToast(b.active ? 'Banner đã được bật' : 'Banner đã được ẩn');
}

// Drag & Drop banners
let _dragBannerId = null;

function bannerDragStart(e, id) {
    _dragBannerId = id;
    e.currentTarget.classList.add('dragging');
    e.dataTransfer.effectAllowed = 'move';
}

function bannerDragOver(e) {
    e.preventDefault();
    e.currentTarget.classList.add('drag-over');
    e.dataTransfer.dropEffect = 'move';
}

function bannerDragLeave(e) {
    e.currentTarget.classList.remove('drag-over');
}

function bannerDrop(e, targetId) {
    e.preventDefault();
    e.currentTarget.classList.remove('drag-over');
    if (_dragBannerId === targetId) return;
    const dragged = banners.find(x => x.id === _dragBannerId);
    const target  = banners.find(x => x.id === targetId);
    [dragged.order, target.order] = [target.order, dragged.order];
    renderBanners();
}

// ==========================================================
// MODULE 2: REVIEWS
// ==========================================================
let reviews = [
    { id: 1, name: 'Nguyễn Thị Mai', avatar: 'NM', rating: 5, product: 'Moonstone Solitaire Ring', date: '22/06/2026', text: 'Chiếc nhẫn đẹp hơn rất nhiều so với ảnh, chất lượng xuất sắc! Giao hàng nhanh, đóng gói sang trọng. Sẽ tiếp tục ủng hộ Aurrelia.', status: 'approved' },
    { id: 2, name: 'Trần Quang Khải', avatar: 'TK', rating: 4, product: 'Petite Diamond Hoops', date: '24/06/2026', text: 'Bông tai rất tinh tế, vợ tôi rất thích. Giao hàng đúng hẹn. Trừ một sao vì hộp quà hơi nhỏ.', status: 'approved' },
    { id: 3, name: 'Lê Minh Hoàng', avatar: 'LH', rating: 2, product: 'Gold Layered Bracelet', date: '26/06/2026', text: 'Vòng tay bị oxy hoá sau 2 tuần dùng, màu chuyển sang vàng sẫm. Khá thất vọng với chất lượng.', status: 'pending' },
    { id: 4, name: 'Phạm Thuý Vy', avatar: 'PV', rating: 5, product: 'Ethereal Pearl Necklace', date: '27/06/2026', text: 'Món quà hoàn hảo nhất tôi từng nhận! Sản phẩm đẹp không tả nổi, cảm ơn Aurrelia rất nhiều.', status: 'pending' },
    { id: 5, name: 'Đỗ Văn Bình', avatar: 'DB', rating: 1, product: 'Classic Signet Ring', date: '25/06/2026', text: 'Giao hàng trễ 1 tuần, không có thông báo. Sản phẩm ổn nhưng dịch vụ cần cải thiện.', status: 'hidden' },
    { id: 6, name: 'Hoàng Kim Liên', avatar: 'HL', rating: 3, product: 'Sapphire Drop Earrings', date: '28/06/2026', text: 'Bông tai đẹp nhưng đá hơi nhỏ hơn tôi tưởng. Nhìn chung là đáng tiền.', status: 'pending' },
];

function renderReviews() {
    const filter = document.getElementById('reviewFilter').value;
    const list = document.getElementById('reviewList');
    const filtered = filter === 'all' ? reviews : reviews.filter(r => r.status === filter);

    if (!filtered.length) {
        list.innerHTML = `<div class="empty-state"><i class="bi bi-star"></i><p>Không có đánh giá nào phù hợp.</p></div>`;
        return;
    }
    list.innerHTML = filtered.map(r => {
        const stars = '★'.repeat(r.rating) + '☆'.repeat(5 - r.rating);
        const statusLabel = { pending: 'Chờ duyệt', approved: 'Đã duyệt', hidden: 'Đã ẩn' }[r.status];
        const avatarBg = { approved: '#e0f0e4', pending: '#fef6e4', hidden: '#f0eeec' }[r.status];
        const avatarColor = { approved: '#287d3c', pending: '#b07a00', hidden: '#796a65' }[r.status];
        return `
            <div class="review-card status-${r.status}">
                <div class="d-flex align-items-start gap-3">
                    <div class="review-avatar" style="background-color:${avatarBg};color:${avatarColor};">${r.avatar}</div>
                    <div class="flex-grow-1 min-w-0">
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-1">
                            <div>
                                <span class="fw-bold small">${r.name}</span>
                                <span class="text-muted font-xs ms-2">· ${r.product}</span>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <span class="review-status-badge ${r.status}">${statusLabel}</span>
                                <span class="text-muted font-xs">${r.date}</span>
                            </div>
                        </div>
                        <div class="review-stars mb-2">${stars}</div>
                        <p class="review-text mb-3">${r.text}</p>
                        <div class="d-flex gap-2 flex-wrap">
                            ${r.status !== 'approved' ? `<button class="btn btn-sm btn-outline-custom py-1 px-2 font-xs" onclick="changeReviewStatus(${r.id}, 'approved')"><i class="bi bi-check2 me-1"></i>Duyệt</button>` : ''}
                            ${r.status !== 'hidden' ? `<button class="btn btn-sm btn-outline-custom py-1 px-2 font-xs" onclick="changeReviewStatus(${r.id}, 'hidden')"><i class="bi bi-eye-slash me-1"></i>Ẩn</button>` : ''}
                            ${r.status === 'hidden' ? `<button class="btn btn-sm btn-outline-custom py-1 px-2 font-xs" onclick="changeReviewStatus(${r.id}, 'approved')"><i class="bi bi-eye me-1"></i>Hiện lại</button>` : ''}
                            <button class="btn btn-sm font-xs py-1 px-2" style="color:#b84a4a;border:1px solid #f9e2e2;background:#fff;" onclick="confirmDelete('review', ${r.id})"><i class="bi bi-trash3 me-1"></i>Xoá</button>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }).join('');
    updateBadges();
}

function changeReviewStatus(id, status) {
    const r = reviews.find(x => x.id === id);
    r.status = status;
    renderReviews();
    const msg = { approved: 'Đã duyệt đánh giá', hidden: 'Đã ẩn đánh giá' }[status];
    showToast(msg);
}

// ==========================================================
// MODULE 3: FAQ
// ==========================================================
let faqs = [
    { id: 1, question: 'Chính sách đổi trả của Aurrelia Fine Jewelry là gì?', answer: 'Aurrelia chấp nhận đổi trả trong vòng 30 ngày kể từ ngày nhận hàng với điều kiện sản phẩm còn nguyên trạng, chưa qua sử dụng và kèm đầy đủ hộp quà và chứng nhận. Vui lòng liên hệ đội ngũ hỗ trợ để được hướng dẫn quy trình đổi trả.' },
    { id: 2, question: 'Trang sức Aurrelia có được làm từ vật liệu thật không?', answer: 'Tất cả trang sức Aurrelia được chế tác từ vàng 18K, bạc Sterling 925 và đá quý thiên nhiên được kiểm định chất lượng quốc tế. Mỗi sản phẩm đều kèm theo giấy chứng nhận xác thực vật liệu.' },
    { id: 3, question: 'Thời gian giao hàng mất bao lâu?', answer: 'Giao hàng nội thành TP.HCM và Hà Nội: 1–2 ngày làm việc. Các tỉnh thành khác: 3–5 ngày làm việc. Giao hàng quốc tế: 7–14 ngày làm việc. Đơn hàng từ 5 triệu đồng được miễn phí vận chuyển toàn quốc.' },
    { id: 4, question: 'Tôi có thể đặt hàng cá nhân hoá theo yêu cầu không?', answer: 'Có! Aurrelia cung cấp dịch vụ trang sức cá nhân hoá bao gồm khắc tên, ngày tháng, ký hiệu đặc biệt. Thời gian chế tác thêm từ 5–10 ngày làm việc. Vui lòng liên hệ qua email hoặc hotline để được tư vấn chi tiết.' },
];
let _nextFaqId = 5;
let _editFaqId = null;

function renderFaq() {
    const list = document.getElementById('faqList');
    if (!faqs.length) {
        list.innerHTML = `<div class="empty-state"><i class="bi bi-chat-square-text"></i><p>Chưa có câu hỏi nào. Thêm câu hỏi đầu tiên!</p></div>`;
        return;
    }
    list.innerHTML = faqs.map((f, idx) => `
        <div class="faq-item" data-id="${f.id}" draggable="true"
             ondragstart="faqDragStart(event, ${f.id})"
             ondragover="faqDragOver(event)"
             ondragleave="faqDragLeave(event)"
             ondrop="faqDrop(event, ${f.id})">
            <div class="faq-header" onclick="toggleFaqItem(${f.id})">
                <i class="bi bi-grip-vertical faq-drag-handle me-1" onclick="event.stopPropagation()"></i>
                <span class="font-xs text-muted fw-bold me-2" style="min-width:20px;">${idx + 1}.</span>
                <span class="faq-question-text">${f.question}</span>
                <div class="d-flex align-items-center gap-2 ms-2 flex-shrink-0">
                    <button class="btn btn-icon" style="width:30px;height:30px;" title="Chỉnh sửa"
                            onclick="event.stopPropagation(); openFaqModal(${f.id})"
                            data-bs-toggle="modal" data-bs-target="#faqModal">
                        <i class="bi bi-pencil" style="font-size:12px;"></i>
                    </button>
                    <button class="btn btn-icon" style="width:30px;height:30px;" title="Xoá"
                            onclick="event.stopPropagation(); confirmDelete('faq', ${f.id})">
                        <i class="bi bi-trash3 text-danger" style="font-size:12px;"></i>
                    </button>
                    <i class="bi bi-chevron-down faq-chevron"></i>
                </div>
            </div>
            <div class="faq-answer-wrap">
                <p class="faq-answer-text">${f.answer}</p>
            </div>
        </div>
    `).join('');
}

function toggleFaqItem(id) {
    const el = document.querySelector(`.faq-item[data-id="${id}"]`);
    el.classList.toggle('open');
}

function openFaqModal(id = null) {
    _editFaqId = id;
    if (id) {
        const f = faqs.find(x => x.id === id);
        document.getElementById('faqModalTitle').textContent = 'CHỈNH SỬA CÂU HỎI';
        document.getElementById('faqId').value = f.id;
        document.getElementById('faqQuestion').value = f.question;
        document.getElementById('faqAnswer').value = f.answer;
    } else {
        document.getElementById('faqModalTitle').textContent = 'THÊM CÂU HỎI MỚI';
        document.getElementById('faqId').value = '';
        document.getElementById('faqQuestion').value = '';
        document.getElementById('faqAnswer').value = '';
    }
}

function saveFaq() {
    const question = document.getElementById('faqQuestion').value.trim();
    const answer   = document.getElementById('faqAnswer').value.trim();
    if (!question || !answer) {
        showToast('Vui lòng điền đầy đủ câu hỏi và câu trả lời', 'error');
        return;
    }
    if (_editFaqId) {
        const f = faqs.find(x => x.id === _editFaqId);
        f.question = question;
        f.answer   = answer;
        showToast('Đã cập nhật câu hỏi');
    } else {
        faqs.push({ id: _nextFaqId++, question, answer });
        showToast('Đã thêm câu hỏi mới');
    }
    bootstrap.Modal.getInstance(document.getElementById('faqModal')).hide();
    renderFaq();
}

// Drag & Drop FAQ
let _dragFaqId = null;

function faqDragStart(e, id) {
    _dragFaqId = id;
    e.currentTarget.classList.add('dragging');
    e.dataTransfer.effectAllowed = 'move';
}

function faqDragOver(e) {
    e.preventDefault();
    e.currentTarget.classList.add('drag-over');
}

function faqDragLeave(e) {
    e.currentTarget.classList.remove('drag-over');
}

function faqDrop(e, targetId) {
    e.preventDefault();
    e.currentTarget.classList.remove('drag-over');
    if (_dragFaqId === targetId) return;
    const fromIdx = faqs.findIndex(x => x.id === _dragFaqId);
    const toIdx   = faqs.findIndex(x => x.id === targetId);
    const [removed] = faqs.splice(fromIdx, 1);
    faqs.splice(toIdx, 0, removed);
    renderFaq();
}

// ==========================================================
// MODULE 4: MESSAGES
// ==========================================================
let messages = [
    { id: 1, name: 'Nguyễn Thị Hoa', email: 'hoa.nguyen@gmail.com', date: '28/06/2026 — 09:14', body: 'Xin chào Aurrelia, tôi muốn hỏi về chính sách bảo hành cho nhẫn vàng 18K. Cụ thể sản phẩm Classic Signet Ring có được bảo hành trong bao lâu? Nếu bị trầy xước nhẹ thì có được đánh bóng miễn phí không ạ?', read: false },
    { id: 2, name: 'Trần Minh Đức', email: 'duc.tran@company.vn', date: '28/06/2026 — 11:30', body: 'Kính gửi đội ngũ Aurrelia, chúng tôi đang tìm kiếm đối tác cung cấp trang sức cao cấp cho sự kiện Gala Dinner của công ty vào tháng 8. Bên bạn có hỗ trợ gói quà doanh nghiệp và in ấn logo không?', read: false },
    { id: 3, name: 'Lê Bảo Châu', email: 'baochau@email.com', date: '27/06/2026 — 15:45', body: 'Tôi đặt đơn hàng #AUR-2026-00891 từ 5 ngày trước nhưng chưa nhận được. Tracking code cũng không cập nhật. Mong bên bạn kiểm tra giúp tôi.', read: false },
    { id: 4, name: 'Phạm Quốc Tuấn', email: 'tuan.p@hotmail.com', date: '27/06/2026 — 08:22', body: 'Cho tôi hỏi bộ sưu tập Ethereal Collection có ra mẫu mới nào trong tháng 7 không? Tôi muốn mua một dây chuyền làm quà sinh nhật cho mẹ vào cuối tháng.', read: true },
    { id: 5, name: 'Hoàng Thị Yến', email: 'yen.hoang@gmail.com', date: '26/06/2026 — 20:10', body: 'Chào Aurrelia! Tôi muốn đặt khắc chữ lên vòng tay pearl. Có thể khắc được tiếng Việt có dấu không? Và chi phí thêm là bao nhiêu ạ?', read: true },
    { id: 6, name: 'Vũ Thanh Hằng', email: 'hang.vu@email.vn', date: '26/06/2026 — 14:05', body: 'Tôi nhận được đơn hàng nhưng hộp quà bị móp ở góc. Sản phẩm bên trong vẫn ổn nhưng đây là quà tặng nên tôi hơi thất vọng. Bên bạn có thể gửi hộp đổi không?', read: false },
];

function renderMessages() {
    const list = document.getElementById('messageList');
    if (!messages.length) {
        list.innerHTML = `<div class="empty-state"><i class="bi bi-envelope"></i><p>Không có tin nhắn nào.</p></div>`;
        return;
    }
    list.innerHTML = messages.map(m => `
        <div class="message-item ${m.read ? '' : 'unread'}" onclick="openMessage(${m.id})">
            ${!m.read ? '<div class="unread-dot"></div>' : '<div style="width:8px;flex-shrink:0;"></div>'}
            <div class="msg-avatar">${m.name.split(' ').slice(-2).map(w => w[0]).join('').slice(0,2).toUpperCase()}</div>
            <div class="flex-grow-1 min-w-0">
                <div class="d-flex align-items-center gap-2 mb-1">
                    <span class="msg-sender-name">${m.name}</span>
                    <span class="msg-email">· ${m.email}</span>
                </div>
                <div class="msg-preview">${m.body}</div>
            </div>
            <div class="d-flex flex-column align-items-end gap-2 flex-shrink-0">
                <span class="msg-date">${m.date}</span>
                <div class="d-flex gap-1">
                    ${!m.read ? `<button class="btn btn-icon" style="width:28px;height:28px;" title="Đánh dấu đã đọc"
                        onclick="event.stopPropagation(); markRead(${m.id})">
                        <i class="bi bi-check2" style="font-size:12px;"></i>
                    </button>` : ''}
                    <button class="btn btn-icon" style="width:28px;height:28px;" title="Xoá"
                        onclick="event.stopPropagation(); confirmDelete('message', ${m.id})">
                        <i class="bi bi-trash3 text-danger" style="font-size:12px;"></i>
                    </button>
                </div>
            </div>
        </div>
    `).join('');
    updateBadges();
}

function openMessage(id) {
    const m = messages.find(x => x.id === id);
    m.read = true;
    renderMessages();

    const initials = m.name.split(' ').slice(-2).map(w => w[0]).join('').slice(0,2).toUpperCase();
    document.getElementById('msgDetailAvatar').textContent = initials;
    document.getElementById('msgDetailName').textContent = m.name;
    document.getElementById('msgDetailEmail').textContent = m.email;
    document.getElementById('msgDetailDate').textContent = m.date;
    document.getElementById('msgDetailBody').textContent = m.body;

    const modal = new bootstrap.Modal(document.getElementById('messageModal'));
    modal.show();
}

function markRead(id) {
    const m = messages.find(x => x.id === id);
    m.read = true;
    renderMessages();
    showToast('Đã đánh dấu đã đọc');
}

// ==========================================================
// SHARED: CONFIRM DELETE MODAL
// ==========================================================
let _pendingDelete = { type: null, id: null };

const DELETE_CONFIG = {
    banner:  { title: 'Xoá Banner?',         desc: 'Banner này sẽ bị xoá vĩnh viễn.' },
    review:  { title: 'Xoá Đánh giá?',       desc: 'Đánh giá này sẽ bị xoá vĩnh viễn và không thể khôi phục.' },
    faq:     { title: 'Xoá Câu hỏi FAQ?',    desc: 'Câu hỏi và câu trả lời sẽ bị xoá vĩnh viễn.' },
    message: { title: 'Xoá Tin nhắn?',       desc: 'Tin nhắn này sẽ bị xoá vĩnh viễn.' },
};

function confirmDelete(type, id) {
    _pendingDelete = { type, id };
    const cfg = DELETE_CONFIG[type];
    document.getElementById('deleteModalTitle').textContent = cfg.title;
    document.getElementById('deleteModalDesc').textContent  = cfg.desc;
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}

document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('confirmDeleteBtn').addEventListener('click', () => {
        const { type, id } = _pendingDelete;
        if (type === 'banner')  banners  = banners.filter(x => x.id !== id);
        if (type === 'review')  reviews  = reviews.filter(x => x.id !== id);
        if (type === 'faq')     faqs     = faqs.filter(x => x.id !== id);
        if (type === 'message') messages = messages.filter(x => x.id !== id);

        const renderMap = { banner: renderBanners, review: renderReviews, faq: renderFaq, message: renderMessages };
        renderMap[type]();

        bootstrap.Modal.getInstance(document.getElementById('deleteModal')).hide();
        showToast('Đã xoá thành công');
    });
});
