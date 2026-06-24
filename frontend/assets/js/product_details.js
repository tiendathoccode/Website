/**
 * product_details.js
 * Giỏ hàng riêng của trang product_details (drawer bên phải).
 * Dữ liệu được đồng bộ vào localStorage qua Cart (cart.js).
 */

// ── Dữ liệu sản phẩm ────────────────────────────────────────────────────────
const productDetail = {
  id:          'eternity-drop-necklace',
  name:        'Vòng Cổ Eternity Drop',
  price:       3450000,
  badge:       'PHIÊN BẢN GIỚI HẠN',
  metal:       'champagne-gold',
  metalLabels: {
    'champagne-gold': 'Vàng Champagne',
    'white-gold':     'Vàng Trắng',
    'rose-gold':      'Vàng Hồng',
  },
  images: {
    main:   'https://images.unsplash.com/photo-1599643477877-530eb83abc8e?w=900&auto=format&fit=crop&q=80',
    thumb1: 'https://images.unsplash.com/photo-1515562141207-7a88fb7ce338?w=900&auto=format&fit=crop&q=80',
    thumb2: 'https://images.unsplash.com/photo-1611085583191-a3b181a88401?w=900&auto=format&fit=crop&q=80',
  },
};

// ── Trạng thái ───────────────────────────────────────────────────────────────
let cartIsOpen = false;
let toastTimer = null;

// ── DOM refs ─────────────────────────────────────────────────────────────────
const btnCartToggle       = document.getElementById('btnCartToggle');
const btnCloseCart        = document.getElementById('btnCloseCart');
const cartDrawer          = document.getElementById('cartDrawer');
const cartOverlay         = document.getElementById('cartOverlay');
const cartBadge           = document.getElementById('cartBadge');
const cartItemList        = document.getElementById('cartItemList');
const cartSubtotalAmount  = document.getElementById('cartSubtotalAmount');
const btnAddToCart        = document.getElementById('btnAddToCart');
const btnAddToWishlist    = document.getElementById('btnAddToWishlist');
const btnProceedToCheckout= document.getElementById('btnProceedToCheckout');
const metalOptions        = document.getElementById('metalOptions');
const selectedMetalLabel  = document.getElementById('selectedMetalLabel');
const mainProductImage    = document.getElementById('mainProductImage');
const thumbnailStrip      = document.getElementById('thumbnailStrip');
const productAccordion    = document.getElementById('productAccordion');

// ── Render giỏ hàng (chỉ hiện sản phẩm của trang này) ─────────────────────
function getPageCartItems() {
  // Giỏ hàng drawer chỉ hiện sản phẩm đang xem (theo productDetail.id)
  // Bạn có thể đổi thành Cart.getAll() nếu muốn hiện tất cả
  return Cart.getAll().filter(i => i.id === productDetail.id);
}

function renderCartItems() {
  const items = Cart.getAll(); // Hiện toàn bộ giỏ trong drawer
  cartItemList.innerHTML = '';

  if (items.length === 0) {
    cartItemList.innerHTML = '<p style="color:var(--color-muted);font-size:12px;text-align:center;padding-top:40px;">Giỏ hàng của bạn đang trống.</p>';
    updateCartBadge();
    cartSubtotalAmount.textContent = formatVND(0);
    return;
  }

  items.forEach(item => {
    const el = document.createElement('div');
    el.classList.add('cart-item');
    el.innerHTML = `
      <img src="${item.image}" alt="${item.name}" class="cart-item-img"/>
      <div class="cart-item-info">
        <span class="cart-item-name">${item.name}</span>
        <span class="cart-item-meta">${item.metalLabel}</span>
        <div class="qty-control">
          <button class="qty-btn" data-action="decrease" data-id="${item.id}" data-metal="${item.metal}" aria-label="Giảm">−</button>
          <span class="qty-value">${item.quantity}</span>
          <button class="qty-btn" data-action="increase" data-id="${item.id}" data-metal="${item.metal}" aria-label="Tăng">+</button>
        </div>
        <button class="remove-btn" data-action="remove" data-id="${item.id}" data-metal="${item.metal}" aria-label="Xóa">🗑</button>
      </div>
      <span class="cart-item-price">${formatVND(item.price * item.quantity)}</span>
    `;
    cartItemList.appendChild(el);
  });

  updateCartBadge();
  cartSubtotalAmount.textContent = formatVND(Cart.getTotalPrice());
}

function updateCartBadge() {
  const qty = Cart.getTotalQty();
  cartBadge.textContent = qty;
  cartBadge.style.display = qty > 0 ? 'flex' : 'none';
}

// ── Thao tác giỏ hàng ────────────────────────────────────────────────────────
function addToCart() {
  const metal     = productDetail.metal;
  const metalLabel= productDetail.metalLabels[metal];
  Cart.add({
    id:         productDetail.id,
    name:       productDetail.name,
    metal,
    metalLabel,
    price:      productDetail.price,
    image:      mainProductImage.src,
  });
  renderCartItems();
  openCartDrawer();
  showToast('Đã thêm vào giỏ hàng!');
}

// ── Drawer ───────────────────────────────────────────────────────────────────
function openCartDrawer() {
  cartIsOpen = true;
  cartDrawer.classList.add('is-open');
  cartOverlay.classList.add('is-visible');
  cartDrawer.setAttribute('aria-hidden', 'false');
  document.body.style.overflow = 'hidden';
}

function closeCartDrawer() {
  cartIsOpen = false;
  cartDrawer.classList.remove('is-open');
  cartOverlay.classList.remove('is-visible');
  cartDrawer.setAttribute('aria-hidden', 'true');
  document.body.style.overflow = '';
}

function toggleCartDrawer() {
  cartIsOpen ? closeCartDrawer() : openCartDrawer();
}

// ── Metal selector ───────────────────────────────────────────────────────────
function selectMetal(metalKey) {
  if (!productDetail.metalLabels[metalKey]) return;
  productDetail.metal = metalKey;
  metalOptions.querySelectorAll('.swatch').forEach(s => {
    s.classList.toggle('swatch--active', s.dataset.metal === metalKey);
  });
  selectedMetalLabel.textContent = productDetail.metalLabels[metalKey];
}

// ── Gallery ──────────────────────────────────────────────────────────────────
function switchMainImage(src, alt, btn) {
  mainProductImage.src = src;
  mainProductImage.alt = alt;
  thumbnailStrip.querySelectorAll('.thumb-btn').forEach(b => b.classList.remove('thumb-btn--active'));
  btn.classList.add('thumb-btn--active');
}

// ── Accordion ────────────────────────────────────────────────────────────────
function toggleAccordion(trigger) {
  const panel = document.getElementById(trigger.dataset.target);
  if (!panel) return;
  const open = trigger.classList.contains('is-open');
  productAccordion.querySelectorAll('.accordion-trigger').forEach(t => {
    t.classList.remove('is-open');
    const p = document.getElementById(t.dataset.target);
    if (p) p.classList.remove('is-open');
  });
  if (!open) { trigger.classList.add('is-open'); panel.classList.add('is-open'); }
}

// ── Toast ─────────────────────────────────────────────────────────────────────
function getOrCreateToast() {
  let el = document.getElementById('toastNotification');
  if (!el) { el = document.createElement('div'); el.id = 'toastNotification'; document.body.appendChild(el); }
  return el;
}
function showToast(msg) {
  const el = getOrCreateToast();
  el.textContent = msg;
  el.classList.add('is-visible');
  clearTimeout(toastTimer);
  toastTimer = setTimeout(() => el.classList.remove('is-visible'), 2600);
}

// ── Checkout ─────────────────────────────────────────────────────────────────
function proceedToCheckout() {
  if (Cart.getTotalQty() === 0) { showToast('Giỏ hàng của bạn đang trống.'); return; }
  showToast('Đang chuyển đến trang thanh toán…');
  setTimeout(() => { window.location.href = 'pay.html'; }, 600);
}

// ── Wishlist ─────────────────────────────────────────────────────────────────
function addToWishlist() { showToast('Đã lưu vào danh sách yêu thích ♡'); }

// ── Sự kiện ──────────────────────────────────────────────────────────────────
btnCartToggle.addEventListener('click', toggleCartDrawer);
btnCloseCart.addEventListener('click', closeCartDrawer);
cartOverlay.addEventListener('click', closeCartDrawer);

cartItemList.addEventListener('click', e => {
  const btn = e.target.closest('.qty-btn, .remove-btn');
  if (!btn) return;
  const { id, metal, action } = btn.dataset;
  if (action === 'increase') Cart.updateQuantity(id, metal, +1);
  if (action === 'decrease') Cart.updateQuantity(id, metal, -1);
  if (action === 'remove')   Cart.remove(id, metal);
  renderCartItems();
});

document.addEventListener('keydown', e => { if (e.key === 'Escape' && cartIsOpen) closeCartDrawer(); });

btnAddToCart.addEventListener('click', addToCart);
btnAddToWishlist.addEventListener('click', addToWishlist);
btnProceedToCheckout.addEventListener('click', proceedToCheckout);

metalOptions.addEventListener('click', e => {
  const swatch = e.target.closest('.swatch');
  if (swatch) selectMetal(swatch.dataset.metal);
});

thumbnailStrip.addEventListener('click', e => {
  const btn = e.target.closest('.thumb-btn');
  if (btn) switchMainImage(btn.dataset.src, btn.dataset.alt, btn);
});

productAccordion.addEventListener('click', e => {
  const trigger = e.target.closest('.accordion-trigger');
  if (trigger) toggleAccordion(trigger);
});

// Lắng nghe thay đổi từ tab/trang khác
window.addEventListener('cart-updated', () => {
  renderCartItems();
});

// ── Khởi tạo ─────────────────────────────────────────────────────────────────
renderCartItems();