function showToast(message, type = 'success') {
    let container = document.getElementById('custom-toast-container');
    if (!container) {
        container = document.createElement('div');
        container.id = 'custom-toast-container';
        container.style.cssText = 'position: fixed; top: 20px; right: 20px; z-index: 99999; display: flex; flex-direction: column; gap: 10px;';
        document.body.appendChild(container);
    }
    const toast = document.createElement('div');
    toast.style.cssText = 'background: #ffffff; border-left: 4px solid #c8a165; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08); color: #333333; padding: 14px 20px; font-size: 13px; font-family: "Inter", sans-serif; border-radius: 4px; display: flex; align-items: center; gap: 10px; min-width: 280px; max-width: 380px; transition: all 0.3s ease; opacity: 1;';
    
    if (type === 'error') {
        toast.style.borderLeftColor = '#dc3545';
    } else if (type === 'success') {
        toast.style.borderLeftColor = '#198754';
    }

    let icon = '<i class="fas fa-check-circle" style="color:#198754"></i>';
    if (type === 'error') {
        icon = '<i class="fas fa-times-circle" style="color:#dc3545"></i>';
    } else if (type === 'info') {
        icon = '<i class="fas fa-info-circle" style="color:#c8a165"></i>';
    }
    
    toast.innerHTML = `${icon} <span>${message}</span>`;
    container.appendChild(toast);
    
    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateY(-20px)';
        setTimeout(() => {
            toast.remove();
        }, 300);
    }, 3000);
}

// Ưu tiên đọc từ sessionStorage (mua ngay / tick chọn)
// Nếu không có thì lấy toàn bộ giỏ hàng
let cartItems = [];
const checkoutRaw = sessionStorage.getItem("checkout_items");
if (checkoutRaw) {
  try {
    cartItems = JSON.parse(checkoutRaw);
  } catch {
    cartItems = [];
  }
} else {
  cartItems = typeof Cart !== "undefined" ? Cart.getAll() : [];
}
const orderItemsList = document.getElementById("orderItemsList");
const totalsSubtotal = document.getElementById("totalsSubtotal");
const totalsDiscount = document.getElementById("totalsDiscount");
const totalsDiscountRow = document.getElementById("totalsDiscountRow");
const grandTotalAmount = document.getElementById("grandTotalAmount");

const provinceSelect = document.getElementById("province");
const districtSelect = document.getElementById("district");
const wardSelect = document.getElementById("ward");

const voucherCheckbox = document.getElementById("voucherCheckbox");
const voucherInputRow = document.getElementById("voucherInputRow");
const voucherInput = document.getElementById("voucherInput");
const btnApplyVoucher = document.getElementById("btnApplyVoucher");
const voucherMessage = document.getElementById("voucherMessage");

const paymentMethods = document.getElementById("paymentMethods");
const bankTransferInfo = document.getElementById("bankTransferInfo");
const ewalletInfo = document.getElementById("ewalletInfo");
const transferNoteCode = document.getElementById("transferNoteCode");
const fullNameInput = document.getElementById("fullName");
const phoneInput = document.getElementById("phone");

const checkoutForm = document.getElementById("checkoutForm");
const btnPlaceOrder = document.getElementById("btnPlaceOrder");

let appliedDiscount = 0;
let appliedVoucherCode = null;
// ── ĐỊA CHỈ: GỌI API THẬT & TIỀN ĐIỀN ĐỊA CHỈ TỪ DATABASE ──────────────────────────
const API_BASE = "https://provinces.open-api.vn/api";

function resetSelect(selectEl, placeholder) {
  selectEl.innerHTML = `<option value="" selected disabled>${placeholder}</option>`;
  selectEl.disabled = true;
}

// Hàm so khớp chữ mờ (Fuzzy matching) để điền sẵn tỉnh/quận/phường
function findOptionByText(selectEl, textToFind) {
  if (!textToFind) return null;
  const cleanText = textToFind.toLowerCase().trim()
    .replace(/^(thành phố|tỉnh|quận|huyện|thị xã|phường|xã|thị trấn)\s+/i, '');
  
  for (let option of selectEl.options) {
    const optionText = option.textContent.toLowerCase().trim()
      .replace(/^(thành phố|tỉnh|quận|huyện|thị xã|phường|xã|thị trấn)\s+/i, '');
    if (optionText.includes(cleanText) || cleanText.includes(optionText)) {
      return option.value;
    }
  }
  return null;
}

// Load tỉnh/thành khi trang khởi động
async function loadProvinces() {
  try {
    const res = await fetch(`${API_BASE}/p/`);
    const provinces = await res.json();

    provinceSelect.innerHTML =
      '<option value="" selected disabled>Chọn Tỉnh/Thành</option>';
    provinces.forEach((p) => {
      const opt = document.createElement("option");
      opt.value = p.code;
      opt.textContent = p.name;
      provinceSelect.appendChild(opt);
    });

    // Tự động chọn tỉnh/thành nếu có sẵn trong DB
    if (window.savedAddress && window.savedAddress.province) {
      const provVal = findOptionByText(provinceSelect, window.savedAddress.province);
      if (provVal) {
        provinceSelect.value = provVal;
        await populateDistricts(provVal);
      }
    }
  } catch (e) {
    console.error("Lỗi load tỉnh:", e);
  }
}

async function populateDistricts(provinceCode) {
  resetSelect(districtSelect, "Đang tải...");
  resetSelect(wardSelect, "Chọn Phường/Xã");

  try {
    const res = await fetch(`${API_BASE}/p/${provinceCode}?depth=2`);
    const data = await res.json();

    districtSelect.innerHTML =
      '<option value="" selected disabled>Chọn Quận/Huyện</option>';
    districtSelect.disabled = false;

    data.districts.forEach((d) => {
      const opt = document.createElement("option");
      opt.value = d.code;
      opt.textContent = d.name;
      districtSelect.appendChild(opt);
    });

    // Tự động chọn quận/huyện nếu có sẵn trong DB
    if (window.savedAddress && window.savedAddress.district) {
      const distVal = findOptionByText(districtSelect, window.savedAddress.district);
      if (distVal) {
        districtSelect.value = distVal;
        await populateWards(distVal);
      }
    }
  } catch (e) {
    console.error("Lỗi load quận:", e);
    resetSelect(districtSelect, "Chọn Quận/Huyện");
  }
}

async function populateWards(districtCode) {
  resetSelect(wardSelect, "Đang tải...");

  try {
    const res = await fetch(`${API_BASE}/d/${districtCode}?depth=2`);
    const data = await res.json();

    wardSelect.innerHTML =
      '<option value="" selected disabled>Chọn Phường/Xã</option>';
    wardSelect.disabled = false;

    data.wards.forEach((w) => {
      const opt = document.createElement("option");
      opt.value = w.code;
      opt.textContent = w.name;
      wardSelect.appendChild(opt);
    });

    // Tự động chọn phường/xã nếu có sẵn trong DB
    if (window.savedAddress && window.savedAddress.ward) {
      const wardVal = findOptionByText(wardSelect, window.savedAddress.ward);
      if (wardVal) {
        wardSelect.value = wardVal;
      }
    }
  } catch (e) {
    console.error("Lỗi load phường:", e);
    resetSelect(wardSelect, "Chọn Phường/Xã");
  }
}

provinceSelect.addEventListener("change", (e) => {
  populateDistricts(e.target.value);
  clearFieldError(provinceSelect);
});

districtSelect.addEventListener("change", (e) => {
  populateWards(e.target.value);
  clearFieldError(districtSelect);
});

wardSelect.addEventListener("change", () => clearFieldError(wardSelect));

// MÃ GIẢM GIÁ (Mock — thực tế kiểm tra qua API/backend)
async function applyVoucher() {
  const code = voucherInput.value.trim().toUpperCase();
  if (!code) {
    showVoucherMessage("Vui lòng nhập mã giảm giá.", "error");
    return;
  }

  try {
    const form = new FormData();
    form.append("action", "check_voucher");
    form.append("code", code);
    form.append("subtotal", getSubtotal());

    const res = await fetch("/index.php?page=cart&action=check_voucher", {
      method: "POST",
      body: form,
    });
    const data = await res.json();

    if (data.status === "ok") {
      appliedDiscount = data.discount;
      appliedVoucherCode = code;
      showVoucherMessage(
        `Áp dụng mã "${code}" thành công! Giảm ${formatCurrency(data.discount)}.`,
        "success",
      );
    } else {
      appliedDiscount = 0;
      appliedVoucherCode = null;
      showVoucherMessage(data.message || "Mã không hợp lệ.", "error");
    }
  } catch (e) {
    showVoucherMessage("Lỗi kết nối, thử lại sau.", "error");
  }
  renderTotals();
}

//  HÀM TIỆN ÍCH
function formatCurrency(amount) {
  return Math.round(amount).toLocaleString("vi-VN") + "₫";
}

function getSubtotal() {
  return cartItems.reduce((sum, item) => sum + item.price * item.quantity, 0);
}

function getGrandTotal() {
  return Math.max(getSubtotal() - appliedDiscount, 0);
}

// RENDER ĐƠN HÀNG
function renderOrderItems() {
  orderItemsList.innerHTML = "";

  if (cartItems.length === 0) {
    orderItemsList.innerHTML =
      '<p style="color:var(--color-muted);font-size:12px;text-align:center;padding:20px 0;">Giỏ hàng của bạn đang trống.</p>';
    return;
  }

  cartItems.forEach((item) => {
    const itemEl = document.createElement("div");
    itemEl.className = "order-item";
    itemEl.innerHTML = `
      <img src="${item.image}" alt="${item.name}" class="order-item-img" />
      <div class="order-item-info">
        <span class="order-item-name">${item.name}</span>
        <span class="order-item-meta">${item.metalLabel}</span>
        <span class="order-item-meta">SL: ${item.quantity}</span>
      </div>
      <span class="order-item-price">${formatCurrency(item.price * item.quantity)}</span>
    `;
    orderItemsList.appendChild(itemEl);
  });
}

function renderTotals() {
  totalsSubtotal.textContent = formatCurrency(getSubtotal());

  if (appliedDiscount > 0) {
    totalsDiscount.textContent = "-" + formatCurrency(appliedDiscount);
    totalsDiscountRow.classList.add("is-visible");
  } else {
    totalsDiscountRow.classList.remove("is-visible");
  }

  grandTotalAmount.textContent = formatCurrency(getGrandTotal());
}

// PHƯƠNG THỨC THANH TOÁN
function updateTransferNote() {
  const name = fullNameInput.value.trim() || "___";
  const phone = phoneInput.value.trim() || "___";
  transferNoteCode.textContent = `AURELIA ${name} ${phone}`;
}

paymentMethods.addEventListener("change", (e) => {
  if (e.target.name !== "paymentMethod") return;

  document
    .querySelectorAll(".payment-option")
    .forEach((opt) => opt.classList.remove("payment-option--active"));
  e.target.closest(".payment-option").classList.add("payment-option--active");

  bankTransferInfo.classList.toggle(
    "is-visible",
    e.target.value === "bank_transfer",
  );
  ewalletInfo.classList.toggle("is-visible", e.target.value === "ewallet");

  if (e.target.value === "bank_transfer") updateTransferNote();
});

fullNameInput.addEventListener("input", updateTransferNote);
phoneInput.addEventListener("input", updateTransferNote);

//  VOUCHER
voucherCheckbox.addEventListener("change", () => {
  voucherInputRow.classList.toggle("is-visible", voucherCheckbox.checked);
  if (!voucherCheckbox.checked) {
    voucherInput.value = "";
    appliedDiscount = 0;
    appliedVoucherCode = null;
    hideVoucherMessage();
    renderTotals();
  }
});

function showVoucherMessage(text, type) {
  voucherMessage.textContent = text;
  voucherMessage.className =
    "voucher-message is-visible " +
    (type === "success" ? "is-success" : "is-error");
}

function hideVoucherMessage() {
  voucherMessage.className = "voucher-message";
  voucherMessage.textContent = "";
}

btnApplyVoucher.addEventListener("click", applyVoucher);
voucherInput.addEventListener("keydown", (e) => {
  if (e.key === "Enter") {
    e.preventDefault();
    applyVoucher();
  }
});

// VALIDATE FORM
function setFieldError(fieldEl, message) {
  const wrapper = fieldEl.closest(".form-field");
  if (!wrapper) return;
  wrapper.classList.add("has-error");

  let errorEl = wrapper.querySelector(".field-error-msg");
  if (!errorEl) {
    errorEl = document.createElement("span");
    errorEl.className = "field-error-msg";
    wrapper.appendChild(errorEl);
  }
  errorEl.textContent = message;
}

function clearFieldError(fieldEl) {
  const wrapper = fieldEl.closest(".form-field");
  if (wrapper) wrapper.classList.remove("has-error");
}

function validateForm() {
  let isValid = true;
  const requiredFields = [
    { el: fullNameInput, message: "Vui lòng nhập họ và tên." },
    { el: phoneInput, message: "Vui lòng nhập số điện thoại." },
    { el: provinceSelect, message: "Vui lòng chọn Tỉnh/Thành." },
    { el: districtSelect, message: "Vui lòng chọn Quận/Huyện." },
    { el: wardSelect, message: "Vui lòng chọn Phường/Xã." },
    {
      el: document.getElementById("addressDetail"),
      message: "Vui lòng nhập địa chỉ cụ thể.",
    },
  ];

  requiredFields.forEach(({ el, message }) => {
    clearFieldError(el);
    if (!el.value || !el.value.trim()) {
      setFieldError(el, message);
      isValid = false;
    }
  });

  const phoneValue = phoneInput.value.trim();
  if (phoneValue && !/^[0-9+\s]{9,15}$/.test(phoneValue)) {
    setFieldError(phoneInput, "Số điện thoại không hợp lệ.");
    isValid = false;
  }

  if (cartItems.length === 0) {
    isValid = false;
  }

  return isValid;
}

//  ĐẶT HÀNG
checkoutForm.addEventListener("submit", (e) => {
  e.preventDefault();

  if (!validateForm()) {
    const firstError = checkoutForm.querySelector(".has-error");
    if (firstError)
      firstError.scrollIntoView({ behavior: "smooth", block: "center" });
    return;
  }

  const selectedPayment = checkoutForm.querySelector(
    'input[name="paymentMethod"]:checked',
  ).value;

  btnPlaceOrder.disabled = true;
  btnPlaceOrder.textContent = "ĐANG XỬ LÝ...";

  const formData = new FormData(checkoutForm);
  formData.append("cart_items", JSON.stringify(cartItems));
  if (appliedVoucherCode) {
    formData.append("voucher_code", appliedVoucherCode);
  }

  fetch("/index.php?page=place_order", {
    method: "POST",
    body: formData,
  })
    .then((res) => res.json())
    .then((data) => {
      btnPlaceOrder.disabled = false;
      btnPlaceOrder.innerHTML = `
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="11" width="18" height="10" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
        ĐẶT HÀNG
      `;
      if (data.status === "ok") {
        if (typeof Cart !== "undefined") {
          Cart.clear();
        }
        sessionStorage.removeItem("checkout_items");

        if (selectedPayment === "bank_transfer" || selectedPayment === "ewallet") {
            // Điền thông tin vào Modal QR
            document.getElementById("qrPayAmount").textContent = formatCurrency(data.final_amount);
            document.getElementById("qrTransferNote").textContent = "AURRELIA " + data.order_code;

            if (selectedPayment === "ewallet") {
                document.getElementById("qrBankName").textContent = "Ví điện tử MoMo / ZaloPay";
                document.getElementById("qrAccountNo").textContent = "0987654321 (Ví MoMo)";
            } else {
                document.getElementById("qrBankName").textContent = "Vietcombank";
                document.getElementById("qrAccountNo").textContent = "0071000123456";
            }

            const qrModalEl = document.getElementById("paymentQrModal");
            const qrModal = new bootstrap.Modal(qrModalEl);
            qrModal.show();

            // Lắng nghe xác nhận thanh toán
            const btnConfirmPayment = document.getElementById("btnConfirmPayment");
            btnConfirmPayment.onclick = function() {
                qrModal.hide();
                showToast("Cảm ơn bạn! Đơn hàng đang được kiểm duyệt thanh toán.", "success");
                setTimeout(() => {
                    window.location.href = "/index.php?page=don_hang";
                }, 2000);
            };

            qrModalEl.addEventListener('hidden.bs.modal', function () {
                window.location.href = "/index.php?page=don_hang";
            });
        } else {
            showToast("Đặt hàng thành công! Đang chuyển hướng...", "success");
            setTimeout(() => {
                window.location.href = "/index.php?page=don_hang";
            }, 2000);
        }
      } else {
        showToast("Lỗi đặt hàng: " + data.message, "error");
      }
    })
    .catch((err) => {
      btnPlaceOrder.disabled = false;
      btnPlaceOrder.innerHTML = `
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="11" width="18" height="10" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
        ĐẶT HÀNG
      `;
      showToast("Đã xảy ra lỗi kết nối. Vui lòng thử lại.", "error");
    });
});

function getPaymentLabel(value) {
  const labels = {
    cod: "Thanh toán khi nhận hàng (COD)",
    bank_transfer: "Chuyển khoản ngân hàng",
    ewallet: "Ví điện tử MoMo / ZaloPay",
  };
  return labels[value] || value;
}

// KHỞI TẠO TRANG
function initCheckoutPage() {
  loadProvinces();
  renderOrderItems();
  renderTotals();
}

initCheckoutPage();
