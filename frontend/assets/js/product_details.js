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

let cartItems = [
  {
    id:       productDetail.id,
    name:     productDetail.name,
    metal:    'champagne-gold',
    metalLabel: 'Vàng Champagne',
    price:    productDetail.price,
    quantity: 1,
    image:    productDetail.images.main,
  },
];

let totalAmount = 0;          
let cartIsOpen  = false;      
let toastTimer  = null;       

const btnCartToggle = document.getElementById('btnCartToggle');
const btnCloseCart = document.getElementById('btnCloseCart');
const cartDrawer = document.getElementById('cartDrawer');
const cartOverlay = document.getElementById('cartOverlay');
const cartBadge = document.getElementById('cartBadge');
const cartItemList = document.getElementById('cartItemList');
const cartSubtotalAmount = document.getElementById('cartSubtotalAmount');
const btnAddToCart = document.getElementById('btnAddToCart');
const btnAddToWishlist = document.getElementById('btnAddToWishlist');
const btnProceedToCheckout = document.getElementById('btnProceedToCheckout');
const metalOptions = document.getElementById('metalOptions');
const selectedMetalLabel = document.getElementById('selectedMetalLabel');
const mainProductImage = document.getElementById('mainProductImage');
const thumbnailStrip = document.getElementById('thumbnailStrip');
const productAccordion = document.getElementById('productAccordion');

function updateCartSubtotal() {
  totalAmount = cartItems.reduce((sum, item) => sum + item.price * item.quantity, 0);
  cartSubtotalAmount.textContent = formatCurrency(totalAmount);
}

function formatCurrency(amount) {
  return amount.toLocaleString('vi-VN', { minimumFractionDigits: 0, maximumFractionDigits: 0 }) + '₫';
}

function renderCartItems() {
  cartItemList.innerHTML = '';

  if (cartItems.length === 0) {
    cartItemList.innerHTML = '<p style="color:var(--color-muted);font-size:12px;text-align:center;padding-top:40px;">Giỏ hàng của bạn đang trống.</p>';
    updateCartBadge();
    updateCartSubtotal();
    return;
  }

  cartItems.forEach((item, index) => {
    const cartItemEl = document.createElement('div');
    cartItemEl.classList.add('cart-item');
    cartItemEl.dataset.itemIndex = index;

    cartItemEl.innerHTML = `
      <img
        src="${item.image}"
        alt="${item.name}"
        class="cart-item-img"
      />
      <div class="cart-item-info">
        <span class="cart-item-name">${item.name}</span>
        <span class="cart-item-meta">${item.metalLabel}</span>
        <div class="qty-control">
          <button class="qty-btn" data-action="decrease" data-index="${index}" aria-label="Giảm số lượng">−</button>
          <span class="qty-value" id="qtyValue${index}">${item.quantity}</span>
          <button class="qty-btn" data-action="increase" data-index="${index}" aria-label="Tăng số lượng">+</button>
        </div>
        <button class="remove-btn" data-action="remove" data-index="${index}" aria-label="Xóa sản phẩm">🗑</button>
      </div>
      <span class="cart-item-price">${formatCurrency(item.price * item.quantity)}</span>
    `;

    cartItemList.appendChild(cartItemEl);
  });

  updateCartBadge();
  updateCartSubtotal();
}

function removeCartItem(itemIndex) {
  if (cartItems[itemIndex]) {
    cartItems.splice(itemIndex, 1);
    showToast('Đã xóa sản phẩm khỏi giỏ hàng.');
    renderCartItems();
  }
}

function updateCartBadge() {
  const totalQty = cartItems.reduce((sum, item) => sum + item.quantity, 0);
  cartBadge.textContent = totalQty;
  cartBadge.style.display = totalQty > 0 ? 'flex' : 'none';
}

function changeItemQuantity(itemIndex, delta) {
  const item = cartItems[itemIndex];
  if (!item) return;

  item.quantity += delta;

  if (item.quantity <= 0) {
    cartItems.splice(itemIndex, 1);
    showToast('Đã xóa sản phẩm khỏi giỏ hàng.');
  }

  renderCartItems();
}

function addToCart() {
  const selectedMetal      = productDetail.metal;
  const selectedMetalName  = productDetail.metalLabels[selectedMetal];
  const existingItem       = cartItems.find(i => i.id === productDetail.id && i.metal === selectedMetal);

  if (existingItem) {
    existingItem.quantity += 1;
  } else {
    cartItems.push({
      id:         productDetail.id,
      name:       productDetail.name,
      metal:      selectedMetal,
      metalLabel: selectedMetalName,
      price:      productDetail.price,
      quantity:   1,
      image:      mainProductImage.src,
    });
  }

  renderCartItems();
  openCartDrawer();
  showToast('Đã thêm vào giỏ hàng!');
}

function addToWishlist() {
  showToast('Đã lưu vào danh sách yêu thích ♡');
}

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
  if (cartIsOpen) {
    closeCartDrawer();
  } else {
    openCartDrawer();
  }
}

function selectMetal(metalKey) {
  if (!productDetail.metalLabels[metalKey]) return;

  productDetail.metal = metalKey;

  const allSwatches = metalOptions.querySelectorAll('.swatch');
  allSwatches.forEach(swatch => {
    swatch.classList.toggle('swatch--active', swatch.dataset.metal === metalKey);
  });

  selectedMetalLabel.textContent = productDetail.metalLabels[metalKey];
}

function switchMainImage(newSrc, newAlt, clickedBtn) {
  mainProductImage.src = newSrc;
  mainProductImage.alt = newAlt;

  const allThumbs = thumbnailStrip.querySelectorAll('.thumb-btn');
  allThumbs.forEach(btn => btn.classList.remove('thumb-btn--active'));
  clickedBtn.classList.add('thumb-btn--active');
}

function toggleAccordion(trigger) {
  const targetId = trigger.dataset.target;
  const panel    = document.getElementById(targetId);
  if (!panel) return;

  const isCurrentlyOpen = trigger.classList.contains('is-open');

  productAccordion.querySelectorAll('.accordion-trigger').forEach(t => {
    t.classList.remove('is-open');
    const p = document.getElementById(t.dataset.target);
    if (p) p.classList.remove('is-open');
  });

  if (!isCurrentlyOpen) {
    trigger.classList.add('is-open');
    panel.classList.add('is-open');
  }
}

function getOrCreateToast() {
  let toastEl = document.getElementById('toastNotification');
  if (!toastEl) {
    toastEl = document.createElement('div');
    toastEl.id = 'toastNotification';
    document.body.appendChild(toastEl);
  }
  return toastEl;
}

function showToast(message) {
  const toastEl = getOrCreateToast();
  toastEl.textContent = message;
  toastEl.classList.add('is-visible');

  clearTimeout(toastTimer);
  toastTimer = setTimeout(() => {
    toastEl.classList.remove('is-visible');
  }, 2600);
}

function proceedToCheckout() {
  if (cartItems.length === 0) {
    showToast('Giỏ hàng của bạn đang trống.');
    return;
  }
  showToast('Đang chuyển đến trang thanh toán…');

  // Chờ một chút để người dùng thấy thông báo, sau đó chuyển sang trang thanh toán
  setTimeout(() => {
    window.location.href = 'thanh_toan.html';
  }, 600);
}

btnCartToggle.addEventListener('click', toggleCartDrawer);
btnCloseCart.addEventListener('click', closeCartDrawer);

cartItemList.addEventListener('click', (e) => {
  const actionBtn = e.target.closest('.qty-btn, .remove-btn');
  if (!actionBtn) return;

  const itemIndex = parseInt(actionBtn.dataset.index, 10);
  const action    = actionBtn.dataset.action;

  if (action === 'increase') changeItemQuantity(itemIndex, +1);
  if (action === 'decrease') changeItemQuantity(itemIndex, -1);
  if (action === 'remove') removeCartItem(itemIndex);
});

document.addEventListener('keydown', (e) => {
  if (e.key === 'Escape' && cartIsOpen) closeCartDrawer();
});

btnAddToCart.addEventListener('click', addToCart);
btnAddToWishlist.addEventListener('click', addToWishlist);

btnProceedToCheckout.addEventListener('click', proceedToCheckout);

metalOptions.addEventListener('click', (e) => {
  const swatchBtn = e.target.closest('.swatch');
  if (swatchBtn) selectMetal(swatchBtn.dataset.metal);
});

thumbnailStrip.addEventListener('click', (e) => {
  const thumbBtn = e.target.closest('.thumb-btn');
  if (thumbBtn) switchMainImage(thumbBtn.dataset.src, thumbBtn.dataset.alt, thumbBtn);
});

productAccordion.addEventListener('click', (e) => {
  const trigger = e.target.closest('.accordion-trigger');
  if (trigger) toggleAccordion(trigger);
});

function initProductPage() {
  renderCartItems();
}

initProductPage();