/**
 * gio_hang.js — Trang giỏ hàng tổng
 * Hiển thị toàn bộ sản phẩm trong localStorage cart
 */

// ── DOM refs ─────────────────────────────────────────────────────────────────
const cartItemsContainer = document.getElementById("cartItemsContainer");
const emptyCartMsg = document.getElementById("emptyCartMsg");
const continueShopping = document.getElementById("continueShopping");
const cartItemCount = document.getElementById("cartItemCount");
const summarySubtotal = document.getElementById("summarySubtotal");
const summaryTotal = document.getElementById("summaryTotal");
const btnCheckout = document.getElementById("btnCheckout");
const headerCartBadge = document.getElementById("headerCartBadge");
const promoInput = document.getElementById("promoInput");
const btnApplyPromo = document.getElementById("btnApplyPromo");
const promoMsg = document.getElementById("promoMsg");
const cartToast = document.getElementById("cartToast");

let toastTimer = null;
let discount = 0; // phần trăm giảm giá

// ── Mã giảm giá demo ─────────────────────────────────────────────────────────
const PROMO_CODES = {
  AURRELIA10: 10,
  VIP20: 20,
  NHOM6: 15,
};

// ── Render ───────────────────────────────────────────────────────────────────
function render() {
  const items = Cart.getAll();
  cartItemsContainer.innerHTML = "";

  updateHeaderBadge();
  updateSummary(items);

  if (items.length === 0) {
    emptyCartMsg.style.display = "block";
    continueShopping.style.display = "none";
    cartItemCount.textContent = "0 sản phẩm";
    btnCheckout.disabled = true;
    return;
  }

  emptyCartMsg.style.display = "none";
  continueShopping.style.display = "block";
  btnCheckout.disabled = false;

  const totalItems = items.reduce((s, i) => s + i.quantity, 0);
  cartItemCount.textContent = `${totalItems} sản phẩm`;

  items.forEach((item) => {
    const row = document.createElement("div");
    row.classList.add("gh-cart-item");
    row.innerHTML = `
      <input type="checkbox" class="gh-item-checkbox"
        data-id="${item.id}" data-metal="${item.metal}"
        style="margin-right:12px; width:16px; height:16px; accent-color:#c8a165; cursor:pointer;"
        checked />
      <div class="gh-item-left">
        <img src="${item.image}" alt="${item.name}" class="gh-item-img" />
        <div class="gh-item-details">
          <span class="gh-item-name">${item.name}</span>
          <span class="gh-item-meta">${item.metalLabel || ""}</span>
          <span class="gh-item-unit-price">${formatVND(item.price)} / sản phẩm</span>
          <button class="gh-remove-btn"
            data-action="remove"
            data-id="${item.id}"
            data-metal="${item.metal}">
            Xóa
          </button>
        </div>
      </div>

      <div class="gh-qty-control">
        <button class="gh-qty-btn"
          data-action="decrease"
          data-id="${item.id}"
          data-metal="${item.metal}">−</button>
        <span class="gh-qty-value">${item.quantity}</span>
        <button class="gh-qty-btn"
          data-action="increase"
          data-id="${item.id}"
          data-metal="${item.metal}">+</button>
      </div>

      <span class="gh-item-price">${formatVND(item.price * item.quantity)}</span>
    `;
    cartItemsContainer.appendChild(row);
  });
}

function updateSummary(items) {
  const subtotal = Cart.getTotalPrice();
  const discountAmt = Math.round((subtotal * discount) / 100);
  const total = subtotal - discountAmt;

  summarySubtotal.textContent = formatVND(subtotal);
  summaryTotal.textContent = formatVND(total);
}

function updateHeaderBadge() {
  const qty = Cart.getTotalQty();
  headerCartBadge.textContent = qty;
  headerCartBadge.style.display = qty > 0 ? "flex" : "none";
}

// ── Toast ─────────────────────────────────────────────────────────────────────
function showToast(msg) {
  cartToast.textContent = msg;
  cartToast.classList.add("show");
  cartToast.style.display = "block";
  clearTimeout(toastTimer);
  toastTimer = setTimeout(() => {
    cartToast.classList.remove("show");
    setTimeout(() => (cartToast.style.display = "none"), 300);
  }, 2500);
}

// ── Sự kiện: thao tác với item ────────────────────────────────────────────────
cartItemsContainer.addEventListener("click", (e) => {
  const btn = e.target.closest("[data-action]");
  if (!btn) return;

  const { action, id, metal } = btn.dataset;

  if (action === "increase") {
    Cart.updateQuantity(id, metal, +1);
    render();
  }
  if (action === "decrease") {
    Cart.updateQuantity(id, metal, -1);
    render();
  }
  if (action === "remove") {
    Cart.remove(id, metal);
    showToast("Đã xóa sản phẩm khỏi giỏ hàng.");
    render();
  }
});

// ── Checkout ──────────────────────────────────────────────────────────────────
btnCheckout.addEventListener("click", () => {
  if (Cart.getTotalQty() === 0) {
    showToast("Giỏ hàng của bạn đang trống.");
    return;
  }

  // Lấy các item được tick
  const checkedBoxes = document.querySelectorAll(".gh-item-checkbox:checked");
  if (checkedBoxes.length === 0) {
    showToast("Vui lòng chọn ít nhất 1 sản phẩm.");
    return;
  }

  const allItems = Cart.getAll();
  const selectedItems = [];

  checkedBoxes.forEach((cb) => {
    const item = allItems.find(
      (i) => i.id === cb.dataset.id && i.metal === cb.dataset.metal,
    );
    if (item) selectedItems.push(item);
  });

  // Lưu vào sessionStorage để trang thanh toán đọc
  sessionStorage.setItem("checkout_items", JSON.stringify(selectedItems));

  showToast("Đang chuyển đến trang thanh toán…");
  setTimeout(() => {
    window.location.href = "/index.php?page=thanh_toan";
  }, 700);
});

// ── Mã khuyến mãi ─────────────────────────────────────────────────────────────
btnApplyPromo.addEventListener("click", () => {
  const code = promoInput.value.trim().toUpperCase();
  promoMsg.style.display = "block";

  if (PROMO_CODES[code] !== undefined) {
    discount = PROMO_CODES[code];
    promoMsg.textContent = `✓ Áp dụng thành công! Giảm ${discount}%`;
    promoMsg.className = "promo-msg success";
    updateSummary(Cart.getAll());
  } else if (!code) {
    promoMsg.textContent = "Vui lòng nhập mã khuyến mãi.";
    promoMsg.className = "promo-msg error";
  } else {
    discount = 0;
    promoMsg.textContent = "Mã không hợp lệ hoặc đã hết hạn.";
    promoMsg.className = "promo-msg error";
    updateSummary(Cart.getAll());
  }
});

promoInput.addEventListener("keydown", (e) => {
  if (e.key === "Enter") btnApplyPromo.click();
});

// ── Lắng nghe cập nhật ───────────────────────────────────────────────────────

// Cùng tab
window.addEventListener("cart-updated", render);

// Tab khác thay đổi localStorage
window.addEventListener("storage", (e) => {
  if (e.key === "aurrelia_cart") render();
});

// Khi user quay lại tab này từ tab product_details
document.addEventListener("visibilitychange", () => {
  if (document.visibilityState === "visible") render();
});

// Khi cửa sổ được focus lại
window.addEventListener("focus", render);

// ── Khởi tạo ─────────────────────────────────────────────────────────────────
render();
