// CHUYỂN ĐỔI TAB CHỨC NĂNG
function switchTab(tabId) {
    document.querySelectorAll('.tab-panel').forEach(panel => {
        panel.classList.remove('active');
    });
    document.querySelectorAll('.menu-btn').forEach(btn => {
        btn.classList.remove('active');
    });

    document.getElementById(tabId).classList.add('active');
    
    const eventBtn = Array.from(document.querySelectorAll('.menu-btn')).find(btn => btn.getAttribute('onclick').includes(tabId));
    if(eventBtn) eventBtn.classList.add('active');
}

// XEM TRƯỚC ẢNH ĐẠI DIỆN
function previewImage(event) {
    const reader = new FileReader();
    reader.onload = function() {
        const output = document.getElementById('sidebar-avatar');
        output.src = reader.result;
    }
    reader.readAsDataURL(event.target.files[0]);
}

// LƯU THÔNG TIN HỒ SƠ
function saveProfile(event) {
    event.preventDefault();
    const nameInput = document.getElementById('fullName').value;
    document.getElementById('sidebar-name').innerText = nameInput;
    alert('Đã cập nhật hồ sơ cá nhân.');
}

// CHỌN SAO ĐÁNH GIÁ SẢN PHẨM
function rateStar(stars) {
    const starSpans = document.querySelectorAll('.star-rating span');
    starSpans.forEach((span, index) => {
        if (index < stars) {
            span.classList.add('selected');
        } else {
            span.classList.remove('selected');
        }
    });
}

// THIẾT LẬP ĐỊA CHỈ MẶC ĐỊNH
function setDefaultAddress(button) {
    const currentCard = button.closest('.address-card');

    // 1. Xóa huy hiệu Mặc định cũ
    const currentBadges = document.querySelectorAll('#addressList .badge-default');
    currentBadges.forEach(badge => badge.remove());

    // 2. Trả lại nút "Đặt làm mặc định" cho các thẻ khác dựa trên bộ class sang trọng mới
    const allCards = document.querySelectorAll('#addressList .address-card');
    allCards.forEach(card => {
        if (!card.querySelector('.btn-set-default') && card !== currentCard) {
            const newBtn = document.createElement('button');
            // Sử dụng class CSS thay vì inline style để giữ chuẩn phong cách Luxury
            newBtn.className = 'btn-action btn-set-default'; 
            newBtn.innerText = 'Đặt làm mặc định';
            newBtn.onclick = function() { setDefaultAddress(this); };
            
            const actionsDiv = card.querySelector('.address-actions');
            const deleteBtn = actionsDiv.querySelector('.btn-delete');
            actionsDiv.insertBefore(newBtn, deleteBtn);
        }
    });

    // 3. Gắn huy hiệu "Mặc định" mới
    const strongTag = currentCard.querySelector('strong');
    const defaultBadge = document.createElement('span');
    defaultBadge.className = 'badge-default';
    defaultBadge.innerText = 'Mặc định';
    strongTag.after(defaultBadge);

    // 4. Xóa nút Đặt mặc định ở thẻ hiện tại
    button.remove();
}

// XÓA ĐỊA CHỈ
function deleteAddress(button) {
    const currentCard = button.closest('.address-card');
    
    if (currentCard.querySelector('.badge-default')) {
        alert('Không thể xóa địa chỉ mặc định. Vui lòng thiết lập địa chỉ khác làm mặc định trước.');
        return;
    }

    const confirmDelete = confirm('Bạn có chắc chắn muốn xóa địa chỉ này?');
    if (confirmDelete) {
        currentCard.remove();
    }
}

// THÊM ĐỊA CHỈ MỚI
function addAddress(event) {
    event.preventDefault();

    const name = document.getElementById('addrName').value.trim();
    const phone = document.getElementById('addrPhone').value.trim();
    const detail = document.getElementById('addrDetail').value.trim();

    const addressList = document.getElementById('addressList');
    const newCard = document.createElement('div');
    newCard.className = 'address-card';

    // Đổ cấu trúc HTML mới bám sát giao diện thẻ của phong cách sang trọng
    newCard.innerHTML = `
        <div class="address-info">
            <strong>${name}</strong>
            <p>${phone}</p>
            <p>${detail}</p>
        </div>
        <div class="address-actions">
            <button class="btn-action btn-set-default" onclick="setDefaultAddress(this)">Đặt làm mặc định</button>
            <button class="btn-action btn-delete" onclick="deleteAddress(this)">Xóa</button>
        </div>
    `;

    addressList.appendChild(newCard);
    document.getElementById('addressForm').reset();
    alert('Đã thêm địa chỉ mới vào sổ địa chỉ.');
}