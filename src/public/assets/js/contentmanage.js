/* =====================================================
   contentmanage.js — Quản lý Nội dung
   Modules: Banner · Review · FAQ · Messages
   Connected to PHP database APIs
   ===================================================== */

'use strict';

let banners = [];
let reviews = [];
let faqs = [];
let messages = [];

let _tempBannerImage = null;
let _editBannerId = null;
let _editFaqId = null;

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
// TAB NAVIGATION & INIT
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

    // Initial fetch
    fetchBanners();
    fetchReviews();
    fetchFaqs();
    fetchMessages();

    // Review filter
    document.getElementById('reviewFilter').addEventListener('change', renderReviews);

    // Mark all messages read
    document.getElementById('markAllReadBtn').addEventListener('click', () => {
        fetch('/index.php?page=admin_api_mark_all_read', {
            method: 'POST'
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                showToast('Đã đánh dấu tất cả tin nhắn là đã đọc');
                fetchMessages();
            } else {
                showToast(data.message, 'error');
            }
        })
        .catch(err => {
            console.error(err);
            showToast('Có lỗi xảy ra', 'error');
        });
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
    const unread = messages.filter(m => m.is_read == 0).length;
    const pBadge = document.getElementById('pendingReviewBadge');
    const mBadge = document.getElementById('unreadMsgBadge');
    if (pBadge) {
        pBadge.textContent = pending;
        pBadge.style.display = pending ? '' : 'none';
    }
    if (mBadge) {
        mBadge.textContent = unread;
        mBadge.style.display = unread ? '' : 'none';
    }
}

// ==========================================================
// DATA FETCHERS
// ==========================================================
function fetchBanners() {
    fetch('/index.php?page=admin_api_get_banners')
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') {
            banners = data.data;
            renderBanners();
        }
    })
    .catch(err => console.error(err));
}

function fetchReviews() {
    fetch('/index.php?page=admin_api_get_reviews&status=all')
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') {
            reviews = data.data;
            renderReviews();
        }
    })
    .catch(err => console.error(err));
}

function fetchFaqs() {
    fetch('/index.php?page=admin_api_get_faqs')
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') {
            faqs = data.data;
            renderFaq();
        }
    })
    .catch(err => console.error(err));
}

function fetchMessages() {
    fetch('/index.php?page=admin_api_get_messages')
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') {
            messages = data.data;
            renderMessages();
        }
    })
    .catch(err => console.error(err));
}

// ==========================================================
// MODULE 1: BANNER
// ==========================================================
function renderBanners() {
    const list = document.getElementById('bannerList');
    if (!banners.length) {
        list.innerHTML = `<div class="empty-state col-span-full"><i class="bi bi-images"></i><p>Chưa có banner nào. Nhấn "Thêm Banner" để bắt đầu.</p></div>`;
        return;
    }
    const sorted = [...banners].sort((a, b) => a.display_order - b.display_order);
    list.innerHTML = sorted.map(b => {
        const isShow = b.status === 'show';
        return `
        <div class="banner-card" draggable="true" data-id="${b.banner_id}"
             ondragstart="bannerDragStart(event, ${b.banner_id})"
             ondragover="bannerDragOver(event)"
             ondragleave="bannerDragLeave(event)"
             ondrop="bannerDrop(event, ${b.banner_id})">
            <div class="banner-img-wrap">
                ${b.image_url
                    ? `<img src="/${b.image_url}" alt="${b.title || ''}">`
                    : `<div class="banner-img-placeholder"><i class="bi bi-image"></i></div>`
                }
                <span class="banner-order-chip"># ${b.display_order}</span>
                <span class="banner-status-chip ${isShow ? 'on' : 'off'}">${isShow ? 'Hiển thị' : 'Ẩn'}</span>
            </div>
            <div class="banner-body">
                <div class="banner-title-text">${b.title || '(Không có tiêu đề)'}</div>
                <div class="banner-link-text mt-1"><i class="bi bi-link-45deg me-1"></i>${b.target_link || '—'}</div>
            </div>
            <div class="banner-actions justify-content-between">
                <div class="form-check form-switch mb-0 d-flex align-items-center gap-2">
                    <input class="form-check-input" type="checkbox" ${isShow ? 'checked' : ''}
                           onchange="toggleBanner(${b.banner_id})">
                    <label class="form-check-label font-xs text-muted">Bật/Tắt</label>
                </div>
                <div class="d-flex gap-1">
                    <button class="btn btn-icon" title="Chỉnh sửa" onclick="openBannerModal(${b.banner_id})" data-bs-toggle="modal" data-bs-target="#bannerModal">
                        <i class="bi bi-pencil"></i>
                    </button>
                    <button class="btn btn-icon text-danger" title="Xoá" onclick="confirmDelete('banner', ${b.banner_id})">
                        <i class="bi bi-trash3"></i>
                    </button>
                </div>
            </div>
        </div>
        `;
    }).join('');
}

function openBannerModal(id = null) {
    _editBannerId = id;
    _tempBannerImage = null;
    document.getElementById('bannerImageInput').value = '';
    document.getElementById('bannerPreviewWrap').classList.add('d-none');
    if (id) {
        const b = banners.find(x => x.banner_id === id);
        document.getElementById('bannerModalTitle').textContent = 'CHỈNH SỬA BANNER';
        document.getElementById('bannerId').value = b.banner_id;
        document.getElementById('bannerTitle').value = b.title || '';
        document.getElementById('bannerLink').value = b.target_link || '';
        document.getElementById('bannerOrder').value = b.display_order;
        document.getElementById('bannerActive').checked = b.status === 'show';
        if (b.image_url) {
            document.getElementById('bannerPreview').src = '/' + b.image_url;
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
    const bannerId = document.getElementById('bannerId').value;
    const title = document.getElementById('bannerTitle').value.trim();
    const link  = document.getElementById('bannerLink').value.trim();
    const order = parseInt(document.getElementById('bannerOrder').value) || 1;
    const active = document.getElementById('bannerActive').checked;
    const imageInput = document.getElementById('bannerImageInput');

    const formData = new FormData();
    if (bannerId) {
        formData.append('banner_id', bannerId);
    }
    formData.append('title', title);
    formData.append('target_link', link);
    formData.append('display_order', order);
    formData.append('status', active ? 'show' : 'hide');

    if (imageInput.files[0]) {
        formData.append('image', imageInput.files[0]);
    }

    fetch('/index.php?page=admin_api_save_banner', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') {
            showToast(data.message);
            bootstrap.Modal.getInstance(document.getElementById('bannerModal')).hide();
            fetchBanners();
        } else {
            showToast(data.message, 'error');
        }
    })
    .catch(err => {
        console.error(err);
        showToast('Có lỗi xảy ra', 'error');
    });
}

function toggleBanner(id) {
    const formData = new FormData();
    formData.append('banner_id', id);
    fetch('/index.php?page=admin_api_toggle_banner', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') {
            showToast(data.message);
            fetchBanners();
        } else {
            showToast(data.message, 'error');
        }
    })
    .catch(err => {
        console.error(err);
        showToast('Có lỗi xảy ra', 'error');
    });
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

    // Locally swap display orders
    const dragged = banners.find(x => x.banner_id === _dragBannerId);
    const target  = banners.find(x => x.banner_id === targetId);
    const tempOrder = dragged.display_order;
    dragged.display_order = target.display_order;
    target.display_order = tempOrder;

    const orders = banners.map(b => ({ id: b.banner_id, order: b.display_order }));

    fetch('/index.php?page=admin_api_reorder_banners', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ orders })
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') {
            showToast(data.message);
            fetchBanners();
        } else {
            showToast(data.message, 'error');
        }
    })
    .catch(err => {
        console.error(err);
        showToast('Có lỗi xảy ra', 'error');
    });
}

// ==========================================================
// MODULE 2: REVIEWS
// ==========================================================
function renderReviews() {
    const filter = document.getElementById('reviewFilter').value;
    const list = document.getElementById('reviewList');
    const filtered = filter === 'all' ? reviews : reviews.filter(r => r.status === filter);

    if (!filtered.length) {
        list.innerHTML = `<div class="empty-state"><i class="bi bi-star"></i><p>Không có đánh giá nào phù hợp.</p></div>`;
        updateBadges();
        return;
    }
    list.innerHTML = filtered.map(r => {
        const stars = '★'.repeat(r.rating) + '☆'.repeat(5 - r.rating);
        const statusLabel = { pending: 'Chờ duyệt', approved: 'Đã duyệt', hidden: 'Đã ẩn' }[r.status];
        const avatarBg = { approved: '#e0f0e4', pending: '#fef6e4', hidden: '#f0eeec' }[r.status];
        const avatarColor = { approved: '#287d3c', pending: '#b07a00', hidden: '#796a65' }[r.status];
        
        const initials = r.full_name.split(' ').slice(-2).map(w => w[0]).join('').slice(0, 2).toUpperCase();
        
        const d = new Date(r.created_at);
        const dateStr = !isNaN(d.getTime()) ? d.toLocaleDateString('vi-VN') : r.created_at;

        return `
            <div class="review-card status-${r.status}">
                <div class="d-flex align-items-start gap-3">
                    <div class="review-avatar" style="background-color:${avatarBg};color:${avatarColor};">${initials}</div>
                    <div class="flex-grow-1 min-w-0">
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-1">
                            <div>
                                <span class="fw-bold small">${r.full_name}</span>
                                <span class="text-muted font-xs ms-2">· ${r.product_name}</span>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <span class="review-status-badge ${r.status}">${statusLabel}</span>
                                <span class="text-muted font-xs">${dateStr}</span>
                            </div>
                        </div>
                        <div class="review-stars mb-2">${stars}</div>
                        <p class="review-text mb-3">${r.comment || ''}</p>
                        <div class="d-flex gap-2 flex-wrap">
                            ${r.status !== 'approved' ? `<button class="btn btn-sm btn-outline-custom py-1 px-2 font-xs" onclick="changeReviewStatus(${r.review_id}, 'approved')"><i class="bi bi-check2 me-1"></i>Duyệt</button>` : ''}
                            ${r.status !== 'hidden' ? `<button class="btn btn-sm btn-outline-custom py-1 px-2 font-xs" onclick="changeReviewStatus(${r.review_id}, 'hidden')"><i class="bi bi-eye-slash me-1"></i>Ẩn</button>` : ''}
                            ${r.status === 'hidden' ? `<button class="btn btn-sm btn-outline-custom py-1 px-2 font-xs" onclick="changeReviewStatus(${r.review_id}, 'approved')"><i class="bi bi-eye me-1"></i>Hiện lại</button>` : ''}
                            <button class="btn btn-sm font-xs py-1 px-2" style="color:#b84a4a;border:1px solid #f9e2e2;background:#fff;" onclick="confirmDelete('review', ${r.review_id})"><i class="bi bi-trash3 me-1"></i>Xoá</button>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }).join('');
    updateBadges();
}

function changeReviewStatus(id, status) {
    const formData = new FormData();
    formData.append('review_id', id);
    formData.append('status', status);
    fetch('/index.php?page=admin_api_update_review_status', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') {
            const msg = { approved: 'Đã duyệt đánh giá', hidden: 'Đã ẩn đánh giá' }[status];
            showToast(msg);
            fetchReviews();
        } else {
            showToast(data.message, 'error');
        }
    })
    .catch(err => {
        console.error(err);
        showToast('Có lỗi xảy ra', 'error');
    });
}

// ==========================================================
// MODULE 3: FAQ
// ==========================================================
function renderFaq() {
    const list = document.getElementById('faqList');
    if (!faqs.length) {
        list.innerHTML = `<div class="empty-state"><i class="bi bi-chat-square-text"></i><p>Chưa có câu hỏi nào. Thêm câu hỏi đầu tiên!</p></div>`;
        return;
    }
    const sorted = [...faqs].sort((a, b) => a.display_order - b.display_order);
    list.innerHTML = sorted.map((f, idx) => `
        <div class="faq-item" data-id="${f.faq_id}" draggable="true"
             ondragstart="faqDragStart(event, ${f.faq_id})"
             ondragover="faqDragOver(event)"
             ondragleave="faqDragLeave(event)"
             ondrop="faqDrop(event, ${f.faq_id})">
            <div class="faq-header" onclick="toggleFaqItem(${f.faq_id})">
                <i class="bi bi-grip-vertical faq-drag-handle me-1" onclick="event.stopPropagation()"></i>
                <span class="font-xs text-muted fw-bold me-2" style="min-width:20px;">${idx + 1}.</span>
                <span class="faq-question-text">${f.question}</span>
                <div class="d-flex align-items-center gap-2 ms-2 flex-shrink-0">
                    <button class="btn btn-icon" style="width:30px;height:30px;" title="Chỉnh sửa"
                            onclick="event.stopPropagation(); openFaqModal(${f.faq_id})"
                            data-bs-toggle="modal" data-bs-target="#faqModal">
                        <i class="bi bi-pencil" style="font-size:12px;"></i>
                    </button>
                    <button class="btn btn-icon" style="width:30px;height:30px;" title="Xoá"
                            onclick="event.stopPropagation(); confirmDelete('faq', ${f.faq_id})">
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
    if (el) {
        el.classList.toggle('open');
    }
}

function openFaqModal(id = null) {
    _editFaqId = id;
    if (id) {
        const f = faqs.find(x => x.faq_id === id);
        document.getElementById('faqModalTitle').textContent = 'CHỈNH SỬA CÂU HỎI';
        document.getElementById('faqId').value = f.faq_id;
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
    const faqId = document.getElementById('faqId').value;
    const question = document.getElementById('faqQuestion').value.trim();
    const answer   = document.getElementById('faqAnswer').value.trim();
    if (!question || !answer) {
        showToast('Vui lòng điền đầy đủ câu hỏi và câu trả lời', 'error');
        return;
    }

    const formData = new FormData();
    if (faqId) {
        formData.append('faq_id', faqId);
    }
    formData.append('question', question);
    formData.append('answer', answer);
    const existing = faqs.find(x => x.faq_id === parseInt(faqId));
    formData.append('display_order', existing ? existing.display_order : faqs.length + 1);

    fetch('/index.php?page=admin_api_save_faq', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') {
            showToast(data.message);
            bootstrap.Modal.getInstance(document.getElementById('faqModal')).hide();
            fetchFaqs();
        } else {
            showToast(data.message, 'error');
        }
    })
    .catch(err => {
        console.error(err);
        showToast('Có lỗi xảy ra', 'error');
    });
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

    const fromIdx = faqs.findIndex(x => x.faq_id === _dragFaqId);
    const toIdx   = faqs.findIndex(x => x.faq_id === targetId);
    const [removed] = faqs.splice(fromIdx, 1);
    faqs.splice(toIdx, 0, removed);

    faqs.forEach((f, idx) => {
        f.display_order = idx + 1;
    });

    const orders = faqs.map(f => ({ id: f.faq_id, order: f.display_order }));

    fetch('/index.php?page=admin_api_reorder_faqs', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ orders })
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') {
            showToast(data.message);
            fetchFaqs();
        } else {
            showToast(data.message, 'error');
        }
    })
    .catch(err => {
        console.error(err);
        showToast('Có lỗi xảy ra', 'error');
    });
}

// ==========================================================
// MODULE 4: MESSAGES
// ==========================================================
function renderMessages() {
    const list = document.getElementById('messageList');
    if (!messages.length) {
        list.innerHTML = `<div class="empty-state"><i class="bi bi-envelope"></i><p>Không có tin nhắn nào.</p></div>`;
        return;
    }
    list.innerHTML = messages.map(m => {
        const isRead = m.is_read == 1;
        const initials = m.customer_name.split(' ').slice(-2).map(w => w[0]).join('').slice(0, 2).toUpperCase();
        
        const d = new Date(m.created_at);
        const dateStr = !isNaN(d.getTime()) ? d.toLocaleString('vi-VN') : m.created_at;

        return `
        <div class="message-item ${isRead ? '' : 'unread'}" onclick="openMessage(${m.contact_id})">
            ${!isRead ? '<div class="unread-dot"></div>' : '<div style="width:8px;flex-shrink:0;"></div>'}
            <div class="msg-avatar">${initials}</div>
            <div class="flex-grow-1 min-w-0">
                <div class="d-flex align-items-center gap-2 mb-1">
                    <span class="msg-sender-name">${m.customer_name}</span>
                    <span class="msg-email">· ${m.customer_email}</span>
                </div>
                <div class="msg-preview">${m.message}</div>
            </div>
            <div class="d-flex flex-column align-items-end gap-2 flex-shrink-0">
                <span class="msg-date">${dateStr}</span>
                <div class="d-flex gap-1">
                    ${!isRead ? `<button class="btn btn-icon" style="width:28px;height:28px;" title="Đánh dấu đã đọc"
                        onclick="event.stopPropagation(); markRead(${m.contact_id})">
                        <i class="bi bi-check2" style="font-size:12px;"></i>
                    </button>` : ''}
                    <button class="btn btn-icon" style="width:28px;height:28px;" title="Xoá"
                        onclick="event.stopPropagation(); confirmDelete('message', ${m.contact_id})">
                        <i class="bi bi-trash3 text-danger" style="font-size:12px;"></i>
                    </button>
                </div>
            </div>
        </div>
        `;
    }).join('');
    updateBadges();
}

function openMessage(id) {
    const m = messages.find(x => x.contact_id === id);
    if (!m) return;
    const initials = m.customer_name.split(' ').slice(-2).map(w => w[0]).join('').slice(0, 2).toUpperCase();

    document.getElementById('msgDetailAvatar').textContent = initials;
    document.getElementById('msgDetailName').textContent = m.customer_name;
    document.getElementById('msgDetailEmail').textContent = m.customer_email;
    
    const d = new Date(m.created_at);
    const dateStr = !isNaN(d.getTime()) ? d.toLocaleString('vi-VN') : m.created_at;
    document.getElementById('msgDetailDate').textContent = dateStr;
    document.getElementById('msgDetailBody').textContent = m.message;

    const modal = new bootstrap.Modal(document.getElementById('messageModal'));
    modal.show();

    if (m.is_read == 0) {
        markRead(id);
    }
}

function markRead(id) {
    const formData = new FormData();
    formData.append('contact_id', id);
    fetch('/index.php?page=admin_api_read_message', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') {
            fetchMessages();
        }
    })
    .catch(err => console.error(err));
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
        
        let url = '';
        const formData = new FormData();
        if (type === 'banner') {
            url = '/index.php?page=admin_api_delete_banner';
            formData.append('banner_id', id);
        } else if (type === 'review') {
            url = '/index.php?page=admin_api_delete_review';
            formData.append('review_id', id);
        } else if (type === 'faq') {
            url = '/index.php?page=admin_api_delete_faq';
            formData.append('faq_id', id);
        } else if (type === 'message') {
            url = '/index.php?page=admin_api_delete_message';
            formData.append('contact_id', id);
        }

        fetch(url, {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                showToast('Đã xoá thành công');
                bootstrap.Modal.getInstance(document.getElementById('deleteModal')).hide();
                if (type === 'banner') fetchBanners();
                if (type === 'review') fetchReviews();
                if (type === 'faq') fetchFaqs();
                if (type === 'message') fetchMessages();
            } else {
                showToast(data.message, 'error');
            }
        })
        .catch(err => {
            console.error(err);
            showToast('Có lỗi xảy ra', 'error');
        });
    });
});
