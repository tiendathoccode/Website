document.addEventListener('DOMContentLoaded', function() {
    
    // 1. CHỨC NĂNG ẨN/HIỆN MẬT KHẨU
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');

    if (togglePassword && passwordInput) {
        togglePassword.addEventListener('click', function() {
            // Đổi thuộc tính type của input giữa 'password' và 'text'
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            
            // Đổi icon mắt (từ nhắm sang mở và ngược lại)
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });
    }

    // 2. KIỂM TRA (VALIDATE) FORM ĐĂNG NHẬP
    const loginForm = document.getElementById('loginForm');
    
    if (loginForm) {
        loginForm.addEventListener('submit', function(event) {
            const email = document.getElementById('email').value.trim();
            const password = passwordInput.value.trim();

            // Kiểm tra rỗng
            if (email === '' || password === '') {
                event.preventDefault(); // Chặn việc chuyển trang
                alert('Lỗi: Vui lòng nhập đầy đủ Email và Mật khẩu!');
                return;
            }

            // Nếu hợp lệ, hệ thống sẽ tự động cho form chạy action="index.html"
        });
    }
});