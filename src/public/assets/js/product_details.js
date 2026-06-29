/**
 * product_details.js - Đã dọn dẹp để khớp với HTML PHP
 */

// productDetail được khai báo từ PHP trong file .php, không khai báo lại ở đây

let cartIsOpen = false;
let toastTimer = null;

// ── DOM refs ─────────────────────────────────────────────────────────────────
const btnCloseCart = document.getElementById("btnCloseCart");
const cartDrawer = document.getElementById("cartDrawer");
const cartOverlay = document.getElementById("cartOverlay");
const cartItemList = document.getElementById("cartItemList");
const cartSubtotalAmount = document.getElementById("cartSubtotalAmount");
const btnAddToCart = document.getElementById("btnAddToCart");
const btnAddToWishlist = document.getElementById("btnAddToWishlist");
const btnProceedToCheckout = document.getElementById("btnProceedToCheckout");
const mainProductImage = document.getElementById("mainProductImage");
const thumbnailStrip = document.getElementById("thumbnailStrip");
const productAccordion = document.getElementById("productAccordion");
const headerCartBadge = document.getElementById("headerCartBadge");
const headerCartBtn = document.getElementById("headerCartBtn");

// ── Badge giỏ hàng trên navbar ───────────────────────────────────────────────
function updateCartBadge() {
  const qty = Cart.getTotalQty();
  if (headerCartBadge) {
    headerCartBadge.textContent = qty;
    headerCartBadge.style.display = qty > 0 ? "inline-block" : "none";
  }
}

// ── Render giỏ hàng trong drawer ─────────────────────────────────────────────
function renderCartItems() {
  const items = Cart.getAll();
  cartItemList.innerHTML = "";

  if (items.length === 0) {
    cartItemList.innerHTML =
      '<p style="color:#888; font-size:12px; text-align:center; padding-top:40px;">Giỏ hàng của bạn đang trống.</p>';
    cartSubtotalAmount.textContent = "0₫";
    updateCartBadge();
    return;
  }

  items.forEach((item) => {
    const el = document.createElement("div");
    el.classList.add("cart-item");
    el.innerHTML = `
      <img src="${item.image}" alt="${item.name}" class="cart-item-img"/>
      <div class="cart-item-info">
        <span class="cart-item-name">${item.name}</span>
        <span class="cart-item-meta">${item.metalLabel || ""}</span>
        <div class="qty-control">
          <button class="qty-btn" data-action="decrease" data-id="${item.id}" data-metal="${item.metal}" aria-label="Giảm">−</button>
          <span class="qty-value">${item.quantity}</span>
          <button class="qty-btn" data-action="increase" data-id="${item.id}" data-metal="${item.metal}" aria-label="Tăng">+</button>
        </div>
        <button class="remove-btn" data-action="remove" data-id="${item.id}" data-metal="${item.metal}" aria-label="Xóa">🗑</button>
      </div>
      <span class="cart-item-price">${(item.price * item.quantity).toLocaleString("vi-VN")}₫</span>
    `;
    cartItemList.appendChild(el);
  });

  cartSubtotalAmount.textContent =
    Cart.getTotalPrice().toLocaleString("vi-VN") + "₫";
  updateCartBadge();
}

// ── Drawer ────────────────────────────────────────────────────────────────────
function openCartDrawer() {
  cartIsOpen = true;
  cartDrawer.classList.add("is-open");
  cartOverlay.classList.add("is-visible");
  cartDrawer.setAttribute("aria-hidden", "false");
  document.body.style.overflow = "hidden";
}

function closeCartDrawer() {
  cartIsOpen = false;
  cartDrawer.classList.remove("is-open");
  cartOverlay.classList.remove("is-visible");
  cartDrawer.setAttribute("aria-hidden", "true");
  document.body.style.overflow = "";
}

// ── Thêm vào giỏ hàng ────────────────────────────────────────────────────────
function addToCart() {
  Cart.add({
    id: productDetail.id,
    name: productDetail.name,
    metal: productDetail.metal,
    metalLabel: productDetail.metalLabels[productDetail.metal],
    price: productDetail.price,
    image: mainProductImage ? mainProductImage.src : productDetail.images.main,
  });
  renderCartItems();
  openCartDrawer();
  showToast("Đã thêm vào giỏ hàng!");
}

// ── Gallery ───────────────────────────────────────────────────────────────────
function switchMainImage(src, alt, btn) {
  if (mainProductImage) {
    mainProductImage.src = src;
    mainProductImage.alt = alt;
  }
  thumbnailStrip
    .querySelectorAll(".thumb-btn")
    .forEach((b) => b.classList.remove("thumb-btn--active"));
  btn.classList.add("thumb-btn--active");
}

// ── Accordion ─────────────────────────────────────────────────────────────────
function toggleAccordion(trigger) {
  const panel = document.getElementById(trigger.dataset.target);
  if (!panel) return;
  const isOpen = trigger.classList.contains("is-open");
  // Đóng tất cả
  productAccordion.querySelectorAll(".accordion-trigger").forEach((t) => {
    t.classList.remove("is-open");
    const p = document.getElementById(t.dataset.target);
    if (p) p.classList.remove("is-open");
  });
  // Mở cái được click nếu chưa mở
  if (!isOpen) {
    trigger.classList.add("is-open");
    panel.classList.add("is-open");
  }
}

// ── Toast ──────────────────────────────────────────────────────────────────────
function showToast(msg) {
  let el = document.getElementById("toastNotification");
  if (!el) {
    el = document.createElement("div");
    el.id = "toastNotification";
    el.style.cssText = `
      position:fixed; bottom:24px; left:50%; transform:translateX(-50%);
      background:#333; color:#fff; padding:10px 20px; border-radius:6px;
      font-size:13px; z-index:99999; opacity:0; transition:opacity .3s;
      pointer-events:none;
    `;
    document.body.appendChild(el);
  }
  el.textContent = msg;
  el.style.opacity = "1";
  clearTimeout(toastTimer);
  toastTimer = setTimeout(() => {
    el.style.opacity = "0";
  }, 2600);
}

// ── Checkout ───────────────────────────────────────────────────────────────────
function proceedToCheckout() {
  if (Cart.getTotalQty() === 0) {
    showToast("Giỏ hàng của bạn đang trống.");
    return;
  }
  window.location.href = "/index.php?page=thanh_toan";
}

// ── Wishlist ───────────────────────────────────────────────────────────────────
function addToWishlist() {
  showToast("Đã lưu vào danh sách yêu thích ♡");
}

// ── Gắn sự kiện ───────────────────────────────────────────────────────────────
// Navbar cart icon → mở drawer
if (headerCartBtn)
  headerCartBtn.addEventListener("click", (e) => {
    e.preventDefault();
    openCartDrawer();
  });

if (btnCloseCart) btnCloseCart.addEventListener("click", closeCartDrawer);
if (cartOverlay) cartOverlay.addEventListener("click", closeCartDrawer);

if (cartItemList)
  cartItemList.addEventListener("click", (e) => {
    const btn = e.target.closest(".qty-btn, .remove-btn");
    if (!btn) return;
    const { id, metal, action } = btn.dataset;
    if (action === "increase") Cart.updateQuantity(id, metal, +1);
    if (action === "decrease") Cart.updateQuantity(id, metal, -1);
    if (action === "remove") Cart.remove(id, metal);
    renderCartItems();
  });

document.addEventListener("keydown", (e) => {
  if (e.key === "Escape" && cartIsOpen) closeCartDrawer();
});

if (btnAddToCart) btnAddToCart.addEventListener("click", addToCart);
if (btnAddToWishlist) btnAddToWishlist.addEventListener("click", addToWishlist);
if (btnProceedToCheckout)
  btnProceedToCheckout.addEventListener("click", proceedToCheckout);

if (productAccordion)
  productAccordion.addEventListener("click", (e) => {
    const trigger = e.target.closest(".accordion-trigger");
    if (trigger) toggleAccordion(trigger);
  });

window.addEventListener("cart-updated", renderCartItems);

// ── Khởi tạo ──────────────────────────────────────────────────────────────────
renderCartItems();
