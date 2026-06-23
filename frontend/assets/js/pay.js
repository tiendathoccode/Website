// DỮ LIỆU GIỎ HÀNG
let cartItems = [
  {
    id: 'lumina-diamond-necklace',
    name: 'Mặt dây chuyền Lumina Diamond',
    metalLabel: 'Vàng trắng 18k',
    price: 31250000,
    quantity: 1,
    image: 'https://images.unsplash.com/photo-1599643477877-530eb83abc8e?w=200&auto=format&fit=crop&q=80',
  },
  {
    id: 'aura-hoop-earrings',
    name: 'Khuyên tai Aura Hoop',
    metalLabel: 'Vàng hồng 14k',
    price: 22250000,
    quantity: 1,
    image: 'https://images.unsplash.com/photo-1611085583191-a3b181a88401?w=200&auto=format&fit=crop&q=80',
  },
];

//  DỮ LIỆU ĐỊA CHỈ (Mock rút gọn — thực tế nên gọi API tỉnh/huyện/xã) 
const addressData = {
  hcm: {
    label: 'TP. Hồ Chí Minh',
    districts: {
      q1: { label: 'Quận 1', wards: ['Phường Bến Nghé', 'Phường Bến Thành', 'Phường Đa Kao'] },
      q7: { label: 'Quận 7', wards: ['Phường Tân Phong', 'Phường Tân Phú', 'Phường Phú Thuận'] },
      thuduc: { label: 'TP. Thủ Đức', wards: ['Phường An Phú', 'Phường Thảo Điền', 'Phường Bình Thọ'] },
    },
  },
  hn: {
    label: 'Hà Nội',
    districts: {
      hoankiem: { label: 'Hoàn Kiếm', wards: ['Phường Hàng Bạc', 'Phường Hàng Trống', 'Phường Tràng Tiền'] },
      caugiay: { label: 'Cầu Giấy', wards: ['Phường Dịch Vọng', 'Phường Nghĩa Đô', 'Phường Quan Hoa'] },
    },
  },
  dn: {
    label: 'Đà Nẵng',
    districts: {
      haichau: { label: 'Hải Châu', wards: ['Phường Thạch Thang', 'Phường Hải Châu I'] },
      sontra: { label: 'Sơn Trà', wards: ['Phường Mân Thái', 'Phường Thọ Quang'] },
    },
  },
  ct: {
    label: 'Cần Thơ',
    districts: {
      ninhkieu: { label: 'Ninh Kiều', wards: ['Phường Tân An', 'Phường An Hòa'] },
    },
  },
  hp: {
    label: 'Hải Phòng',
    districts: {
      hongbang: { label: 'Hồng Bàng', wards: ['Phường Hoàng Văn Thụ', 'Phường Quang Trung'] },
    },
  },
};

// MÃ GIẢM GIÁ (Mock — thực tế kiểm tra qua API/backend) 
const voucherCodes = {
  'AURELIA10': { type: 'percent', value: 10, maxDiscount: 2000000, minOrder: 0 },
  'FREESHIP': { type: 'fixed', value: 0, minOrder: 0 }, // chỉ tượng trưng, ship đã miễn phí sẵn
  'WELCOME500K': { type: 'fixed', value: 500000, minOrder: 5000000 },
};

let appliedDiscount = 0;
let appliedVoucherCode = null;

// THAM CHIẾU DOM 
const orderItemsList = document.getElementById('orderItemsList');
const totalsSubtotal = document.getElementById('totalsSubtotal');
const totalsDiscount = document.getElementById('totalsDiscount');
const totalsDiscountRow = document.getElementById('totalsDiscountRow');
const grandTotalAmount = document.getElementById('grandTotalAmount');

const provinceSelect = document.getElementById('province');
const districtSelect = document.getElementById('district');
const wardSelect = document.getElementById('ward');

const voucherCheckbox = document.getElementById('voucherCheckbox');
const voucherInputRow = document.getElementById('voucherInputRow');
const voucherInput = document.getElementById('voucherInput');
const btnApplyVoucher = document.getElementById('btnApplyVoucher');
const voucherMessage = document.getElementById('voucherMessage');

const paymentMethods = document.getElementById('paymentMethods');
const bankTransferInfo = document.getElementById('bankTransferInfo');
const ewalletInfo = document.getElementById('ewalletInfo');
const transferNoteCode = document.getElementById('transferNoteCode');
const fullNameInput = document.getElementById('fullName');
const phoneInput = document.getElementById('phone');

const checkoutForm = document.getElementById('checkoutForm');
const btnPlaceOrder = document.getElementById('btnPlaceOrder');

//  HÀM TIỆN ÍCH
function formatCurrency(amount) {
  return Math.round(amount).toLocaleString('vi-VN') + '₫';
}

function getSubtotal() {
  return cartItems.reduce((sum, item) => sum + item.price * item.quantity, 0);
}

function getGrandTotal() {
  return Math.max(getSubtotal() - appliedDiscount, 0);
}

// RENDER ĐƠN HÀNG 
function renderOrderItems() {
  orderItemsList.innerHTML = '';

  if (cartItems.length === 0) {
    orderItemsList.innerHTML = '<p style="color:var(--color-muted);font-size:12px;text-align:center;padding:20px 0;">Giỏ hàng của bạn đang trống.</p>';
    return;
  }

  cartItems.forEach(item => {
    const itemEl = document.createElement('div');
    itemEl.className = 'order-item';
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
    totalsDiscount.textContent = '-' + formatCurrency(appliedDiscount);
    totalsDiscountRow.classList.add('is-visible');
  } else {
    totalsDiscountRow.classList.remove('is-visible');
  }

  grandTotalAmount.textContent = formatCurrency(getGrandTotal());
}

//  ĐỊA CHỈ: TỈNH → QUẬN/HUYỆN → PHƯỜNG/XÃ 
function resetSelect(selectEl, placeholder) {
  selectEl.innerHTML = `<option value="" selected disabled>${placeholder}</option>`;
  selectEl.disabled = true;
}

function populateDistricts(provinceKey) {
  const province = addressData[provinceKey];
  resetSelect(districtSelect, 'Chọn Quận/Huyện');
  resetSelect(wardSelect, 'Chọn Phường/Xã');

  if (!province) return;

  districtSelect.disabled = false;
  Object.entries(province.districts).forEach(([key, district]) => {
    const option = document.createElement('option');
    option.value = key;
    option.textContent = district.label;
    districtSelect.appendChild(option);
  });
}

function populateWards(provinceKey, districtKey) {
  const district = addressData[provinceKey]?.districts[districtKey];
  resetSelect(wardSelect, 'Chọn Phường/Xã');

  if (!district) return;

  wardSelect.disabled = false;
  district.wards.forEach(wardName => {
    const option = document.createElement('option');
    option.value = wardName;
    option.textContent = wardName;
    wardSelect.appendChild(option);
  });
}

provinceSelect.addEventListener('change', (e) => {
  populateDistricts(e.target.value);
  clearFieldError(provinceSelect);
});

districtSelect.addEventListener('change', (e) => {
  populateWards(provinceSelect.value, e.target.value);
  clearFieldError(districtSelect);
});

wardSelect.addEventListener('change', () => clearFieldError(wardSelect));

// PHƯƠNG THỨC THANH TOÁN 
function updateTransferNote() {
  const name = fullNameInput.value.trim() || '___';
  const phone = phoneInput.value.trim() || '___';
  transferNoteCode.textContent = `AURELIA ${name} ${phone}`;
}

paymentMethods.addEventListener('change', (e) => {
  if (e.target.name !== 'paymentMethod') return;

  document.querySelectorAll('.payment-option').forEach(opt => opt.classList.remove('payment-option--active'));
  e.target.closest('.payment-option').classList.add('payment-option--active');

  bankTransferInfo.classList.toggle('is-visible', e.target.value === 'bank_transfer');
  ewalletInfo.classList.toggle('is-visible', e.target.value === 'ewallet');

  if (e.target.value === 'bank_transfer') updateTransferNote();
});

fullNameInput.addEventListener('input', updateTransferNote);
phoneInput.addEventListener('input', updateTransferNote);

//  VOUCHER 
voucherCheckbox.addEventListener('change', () => {
  voucherInputRow.classList.toggle('is-visible', voucherCheckbox.checked);
  if (!voucherCheckbox.checked) {
    voucherInput.value = '';
    appliedDiscount = 0;
    appliedVoucherCode = null;
    hideVoucherMessage();
    renderTotals();
  }
});

function showVoucherMessage(text, type) {
  voucherMessage.textContent = text;
  voucherMessage.className = 'voucher-message is-visible ' + (type === 'success' ? 'is-success' : 'is-error');
}

function hideVoucherMessage() {
  voucherMessage.className = 'voucher-message';
  voucherMessage.textContent = '';
}

function applyVoucher() {
  const code = voucherInput.value.trim().toUpperCase();

  if (!code) {
    showVoucherMessage('Vui lòng nhập mã giảm giá.', 'error');
    return;
  }

  const voucher = voucherCodes[code];
  const subtotal = getSubtotal();

  if (!voucher) {
    appliedDiscount = 0;
    appliedVoucherCode = null;
    showVoucherMessage('Mã giảm giá không tồn tại hoặc đã hết hạn.', 'error');
    renderTotals();
    return;
  }

  if (subtotal < voucher.minOrder) {
    appliedDiscount = 0;
    appliedVoucherCode = null;
    showVoucherMessage(`Đơn hàng cần tối thiểu ${formatCurrency(voucher.minOrder)} để dùng mã này.`, 'error');
    renderTotals();
    return;
  }

  let discount = 0;
  if (voucher.type === 'percent') {
    discount = (subtotal * voucher.value) / 100;
    if (voucher.maxDiscount) discount = Math.min(discount, voucher.maxDiscount);
  } else {
    discount = voucher.value;
  }

  appliedDiscount = discount;
  appliedVoucherCode = code;
  showVoucherMessage(`Áp dụng mã "${code}" thành công! Bạn được giảm ${formatCurrency(discount)}.`, 'success');
  renderTotals();
}

btnApplyVoucher.addEventListener('click', applyVoucher);
voucherInput.addEventListener('keydown', (e) => {
  if (e.key === 'Enter') {
    e.preventDefault();
    applyVoucher();
  }
});

// VALIDATE FORM 
function setFieldError(fieldEl, message) {
  const wrapper = fieldEl.closest('.form-field');
  if (!wrapper) return;
  wrapper.classList.add('has-error');

  let errorEl = wrapper.querySelector('.field-error-msg');
  if (!errorEl) {
    errorEl = document.createElement('span');
    errorEl.className = 'field-error-msg';
    wrapper.appendChild(errorEl);
  }
  errorEl.textContent = message;
}

function clearFieldError(fieldEl) {
  const wrapper = fieldEl.closest('.form-field');
  if (wrapper) wrapper.classList.remove('has-error');
}

function validateForm() {
  let isValid = true;
  const requiredFields = [
    { el: fullNameInput, message: 'Vui lòng nhập họ và tên.' },
    { el: phoneInput, message: 'Vui lòng nhập số điện thoại.' },
    { el: provinceSelect, message: 'Vui lòng chọn Tỉnh/Thành.' },
    { el: districtSelect, message: 'Vui lòng chọn Quận/Huyện.' },
    { el: wardSelect, message: 'Vui lòng chọn Phường/Xã.' },
    { el: document.getElementById('addressDetail'), message: 'Vui lòng nhập địa chỉ cụ thể.' },
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
    setFieldError(phoneInput, 'Số điện thoại không hợp lệ.');
    isValid = false;
  }

  if (cartItems.length === 0) {
    isValid = false;
  }

  return isValid;
}

//  ĐẶT HÀNG
checkoutForm.addEventListener('submit', (e) => {
  e.preventDefault();

  if (!validateForm()) {
    const firstError = checkoutForm.querySelector('.has-error');
    if (firstError) firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
    return;
  }

  const selectedPayment = checkoutForm.querySelector('input[name="paymentMethod"]:checked').value;

  btnPlaceOrder.disabled = true;
  btnPlaceOrder.textContent = 'ĐANG XỬ LÝ...';

  // Mô phỏng gọi API đặt hàng 
  setTimeout(() => {
    const orderCode = 'ORD-' + Date.now().toString().slice(-8);
    alert(
      `Đặt hàng thành công!\n\nMã đơn hàng: ${orderCode}\n` +
      `Phương thức thanh toán: ${getPaymentLabel(selectedPayment)}\n` +
      `Tổng thanh toán: ${formatCurrency(getGrandTotal())}\n\n` +
      `Cảm ơn bạn đã mua sắm tại Aurelia.`
    );

    btnPlaceOrder.disabled = false;
    btnPlaceOrder.innerHTML = `
      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="11" width="18" height="10" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
      ĐẶT HÀNG
    `;
  }, 1200);
});

function getPaymentLabel(value) {
  const labels = {
    cod: 'Thanh toán khi nhận hàng (COD)',
    bank_transfer: 'Chuyển khoản ngân hàng',
    ewallet: 'Ví điện tử MoMo / ZaloPay',
  };
  return labels[value] || value;
}

// KHỞI TẠO TRANG
function initCheckoutPage() {
  renderOrderItems();
  renderTotals();
}

initCheckoutPage();
