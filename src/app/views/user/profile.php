<!DOCTYPE html>
<html lang="vi">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Thông tin cá nhân - Aurelia</title>
        <link rel="stylesheet" type="text/css" href="/assets/css/auth.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
        <link rel="icon" type="image/png" href="/favicon.png" />
        <style>
            .input-wrapper select {
                width: 100%;
                padding: 12px 10px;
                border: none;
                background: transparent;
                outline: none;
                color: var(--text-dark);
                appearance: none;
                -webkit-appearance: none;
                background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%237a7670' stroke-width='2'%3e%3cpath d='m6 9 6 6 6-6'/%3e%3c/svg%3e");
                background-repeat: no-repeat;
                background-position: right 15px center;
                background-size: 14px;
                padding-right: 35px;
                cursor: pointer;
            }
            .input-wrapper select:disabled {
                cursor: not-allowed;
                opacity: 0.6;
            }
        </style>
    </head>
    <body class="auth-body">
        <div class="auth-wrapper" style="max-width: 900px;">

            <div class="auth-image">
                <div class="image-text">
                    <h2>Thông tin cá nhân</h2>
                    <p>Quản lý và cập nhật thông tin liên hệ, địa chỉ giao hàng mặc định của bạn tại Aurrelia Boutique.</p>
                </div>
            </div>

            <div class="auth-form-section" style="max-height: 90vh; overflow-y: auto;">
                <h1 class="brand-logo">AURRELIA</h1>
                <h3 style="margin-bottom: 30px; font-family: var(--font-display); text-align: center;">Hồ sơ cá nhân</h3>

                <?php
                if (isset($_SESSION["success_message"])) {
                    echo '<p style="color: green; text-align: center; margin-bottom: 20px; font-size:14px; font-weight:bold;">' . htmlspecialchars($_SESSION["success_message"]) . '</p>';
                    unset($_SESSION["success_message"]);
                }
                if (isset($_SESSION["error_message"])) {
                    echo '<p style="color: red; text-align: center; margin-bottom: 20px; font-size:14px; font-weight:bold;">' . htmlspecialchars($_SESSION["error_message"]) . '</p>';
                    unset($_SESSION["error_message"]);
                }
                ?>

                <form id="profileForm" method="POST" action="/index.php?page=process_profile">
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                        <div class="input-group">
                            <label for="full_name">HỌ VÀ TÊN</label>
                            <div class="input-wrapper">
                                <i class="fas fa-user icon"></i>
                                <input type="text" id="full_name" name="full_name" value="<?php echo htmlspecialchars($user["full_name"]); ?>" required placeholder="Nguyễn Văn A">
                            </div>
                        </div>

                        <div class="input-group">
                            <label for="phone">SỐ ĐIỆN THOẠI</label>
                            <div class="input-wrapper">
                                <i class="fas fa-phone icon"></i>
                                <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($user["phone"] ?? ""); ?>" required placeholder="0901234567">
                            </div>
                        </div>
                    </div>

                    <div class="input-group">
                        <label for="email">EMAIL (TÀI KHOẢN ĐĂNG NHẬP)</label>
                        <div class="input-wrapper">
                            <i class="fas fa-envelope icon"></i>
                            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user["email"]); ?>" required placeholder="email@example.com">
                        </div>
                    </div>

                    <h4 style="margin-top:25px; margin-bottom:15px; font-family: var(--font-display); color:#bfa15f; border-bottom: 1px solid #f0ece4; padding-bottom: 5px;">ĐỊA CHỈ GIAO HÀNG MẶC ĐỊNH</h4>

                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 10px;">
                        <div class="input-group">
                            <label for="province_select">TỈNH / THÀNH PHỐ</label>
                            <div class="input-wrapper">
                                <i class="fas fa-map-marker-alt icon"></i>
                                <select id="province_select">
                                    <option value="" disabled selected>Chọn Tỉnh/Thành</option>
                                </select>
                                <input type="hidden" id="province_city" name="province_city" value="<?php echo htmlspecialchars($user["province_city"] ?? ""); ?>">
                            </div>
                        </div>

                        <div class="input-group">
                            <label for="district_select">QUẬN / HUYỆN</label>
                            <div class="input-wrapper">
                                <i class="fas fa-map icon"></i>
                                <select id="district_select" disabled>
                                    <option value="" disabled selected>Chọn Quận/Huyện</option>
                                </select>
                                <input type="hidden" id="district" name="district" value="<?php echo htmlspecialchars($user["district"] ?? ""); ?>">
                            </div>
                        </div>

                        <div class="input-group">
                            <label for="ward_select">PHƯỜNG / XÃ</label>
                            <div class="input-wrapper">
                                <i class="fas fa-directions icon"></i>
                                <select id="ward_select" disabled>
                                    <option value="" disabled selected>Chọn Phường/Xã</option>
                                </select>
                                <input type="hidden" id="ward_commune" name="ward_commune" value="<?php echo htmlspecialchars($user["ward_commune"] ?? ""); ?>">
                            </div>
                        </div>
                    </div>

                    <div class="input-group">
                        <label for="specific_address">ĐỊA CHỈ CHI TIẾT (SỐ NHÀ, TÊN ĐƯỜNG,...)</label>
                        <div class="input-wrapper">
                            <i class="fas fa-home icon"></i>
                            <input type="text" id="specific_address" name="specific_address" value="<?php echo htmlspecialchars($user["specific_address"] ?? ""); ?>" placeholder="Ví dụ: Số 12, ngõ 34">
                        </div>
                    </div>

                    <button type="submit" class="btn-primary" style="margin-top: 20px;">LƯU THÔNG TIN HỒ SƠ</button>
                </form>

                <div style="text-align: center; margin-top: 30px;">
                    <a href="/index.php?page=home" style="color: #bfa15f; text-decoration: none; font-weight: bold; font-size: 14px;">
                        Quay lại trang chủ
                    </a>
                </div>

            </div>

        </div>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                const API_BASE = "https://provinces.open-api.vn/api";
                
                const provinceSelect = document.getElementById("province_select");
                const districtSelect = document.getElementById("district_select");
                const wardSelect = document.getElementById("ward_select");
                
                const provinceHidden = document.getElementById("province_city");
                const districtHidden = document.getElementById("district");
                const wardHidden = document.getElementById("ward_commune");
                
                const savedAddress = {
                    province: provinceHidden.value,
                    district: districtHidden.value,
                    ward: wardHidden.value
                };
                
                function resetSelect(selectEl, placeholder) {
                    selectEl.innerHTML = `<option value="" selected disabled>${placeholder}</option>`;
                    selectEl.disabled = true;
                }
                
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
                
                async function loadProvinces() {
                    try {
                        const res = await fetch(`${API_BASE}/p/`);
                        const provinces = await res.json();
                        
                        provinceSelect.innerHTML = '<option value="" selected disabled>Chọn Tỉnh/Thành</option>';
                        provinces.forEach((p) => {
                            const opt = document.createElement("option");
                            opt.value = p.code;
                            opt.textContent = p.name;
                            provinceSelect.appendChild(opt);
                        });
                        
                        if (savedAddress.province) {
                            const provVal = findOptionByText(provinceSelect, savedAddress.province);
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
                        
                        districtSelect.innerHTML = '<option value="" selected disabled>Chọn Quận/Huyện</option>';
                        districtSelect.disabled = false;
                        
                        data.districts.forEach((d) => {
                            const opt = document.createElement("option");
                            opt.value = d.code;
                            opt.textContent = d.name;
                            districtSelect.appendChild(opt);
                        });
                        
                        if (savedAddress.district) {
                            const distVal = findOptionByText(districtSelect, savedAddress.district);
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
                        
                        wardSelect.innerHTML = '<option value="" selected disabled>Chọn Phường/Xã</option>';
                        wardSelect.disabled = false;
                        
                        data.wards.forEach((w) => {
                            const opt = document.createElement("option");
                            opt.value = w.code;
                            opt.textContent = w.name;
                            wardSelect.appendChild(opt);
                        });
                        
                        if (savedAddress.ward) {
                            const wardVal = findOptionByText(wardSelect, savedAddress.ward);
                            if (wardVal) {
                                wardSelect.value = wardVal;
                            }
                        }
                    } catch (e) {
                        console.error("Lỗi load phường:", e);
                        resetSelect(wardSelect, "Chọn Phường/Xã");
                    }
                }
                
                provinceSelect.addEventListener("change", async (e) => {
                    const text = provinceSelect.options[provinceSelect.selectedIndex].text;
                    provinceHidden.value = text;
                    districtHidden.value = "";
                    wardHidden.value = "";
                    savedAddress.province = text;
                    savedAddress.district = "";
                    savedAddress.ward = "";
                    await populateDistricts(e.target.value);
                });
                
                districtSelect.addEventListener("change", async (e) => {
                    const text = districtSelect.options[districtSelect.selectedIndex].text;
                    districtHidden.value = text;
                    wardHidden.value = "";
                    savedAddress.district = text;
                    savedAddress.ward = "";
                    await populateWards(e.target.value);
                });
                
                wardSelect.addEventListener("change", (e) => {
                    const text = wardSelect.options[wardSelect.selectedIndex].text;
                    wardHidden.value = text;
                    savedAddress.ward = text;
                });
                
                loadProvinces();
            });
        </script>
    </body>
</html>
