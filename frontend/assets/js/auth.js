document.addEventListener('DOMContentLoaded', function() {
    
    // 1. CHỨC NĂNG ẨN/HIỆN MẬT KHẨU
    const toggleIcons = document.querySelectorAll('.toggle-password');
    
    toggleIcons.forEach(function(icon) {
        icon.addEventListener('click', function() {
            // Lấy ID của ô input tương ứng với con mắt đang được click
            const targetId = this.getAttribute('data-target');
            const passwordInput = document.getElementById(targetId);

            if (passwordInput) {
                // Đổi thuộc tính type giữa 'password' và 'text'
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                
                // Đổi icon mắt (từ nhắm sang mở và ngược lại)
                this.classList.toggle('fa-eye');
                this.classList.toggle('fa-eye-slash');
            }
        });
    });

    // 2. KIỂM TRA FORM ĐĂNG NHẬP
    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        loginForm.addEventListener('submit', function(event) {
            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('password').value.trim();

            if (email === '' || password === '') {
                event.preventDefault();
                alert('Lỗi: Vui lòng nhập đầy đủ Email và Mật khẩu!');
            }
        });
    }

    // 3. KIỂM TRA FORM ĐĂNG KÝ (register.html)

    const registerForm = document.getElementById('registerForm');
    if (registerForm) {
        registerForm.addEventListener('submit', function(event) {
            const pass = document.getElementById('reg-password').value;
            const confirmPass = document.getElementById('confirm-password').value;

            // Kiểm tra mật khẩu khớp nhau
            if (pass !== confirmPass) {
                event.preventDefault(); // Ngăn không cho chuyển trang
                alert('Lỗi: Mật khẩu và Xác nhận mật khẩu không khớp!');
                
                // Xóa ô xác nhận để người dùng nhập lại
                document.getElementById('confirm-password').value = '';
                document.getElementById('confirm-password').focus();
            }
        });
    }

    // ==========================================
    // 4. KIỂM TRA FORM QUÊN MẬT KHẨU (forgot-password.html)
    // ==========================================
    const forgotPasswordForm = document.getElementById('forgotPasswordForm');
    
    if (forgotPasswordForm) {
        forgotPasswordForm.addEventListener('submit', function(event) {
            event.preventDefault(); // Chặn việc tự động reload trang
            
            const email = document.getElementById('reset-email').value.trim();

            if (email === '') {
                alert('Lỗi: Vui lòng nhập địa chỉ email!');
            } else {
                // Giả lập hiển thị thông báo thành công
                alert('Thành công! Một đường link đặt lại mật khẩu đã được gửi đến: ' + email + '\n\nVui lòng kiểm tra hộp thư của bạn.');
                
                // Tự động đẩy người dùng về lại trang đăng nhập
                window.location.href = 'login.html';
            }
        });
    }

});