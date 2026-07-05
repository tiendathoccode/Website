document.addEventListener("DOMContentLoaded", function () {
  // ==========================================
  // HELPER: Hiển thị thông báo lỗi inline
  // ==========================================
  function showError(formEl, message) {
    // Xóa thông báo cũ nếu có
    const old = formEl.querySelector(".js-error-msg");
    if (old) old.remove();

    const div = document.createElement("div");
    div.className = "js-error-msg";
    div.style.cssText = `
      background: #fde8e8;
      color: #c0392b;
      border: 1px solid #f5c6c6;
      border-radius: 6px;
      padding: 10px 14px;
      margin-bottom: 14px;
      font-size: 13px;
      text-align: center;
    `;
    div.textContent = message;

    // Chèn lên đầu form
    formEl.insertBefore(div, formEl.firstChild);

    // Tự động ẩn sau 4 giây
    setTimeout(() => div.remove(), 4000);
  }

  // 1. CHỨC NĂNG ẨN/HIỆN MẬT KHẨU
  const toggleIcons = document.querySelectorAll(".toggle-password");
  toggleIcons.forEach(function (icon) {
    icon.addEventListener("click", function () {
      const targetId = this.getAttribute("data-target");
      const passwordInput = document.getElementById(targetId);
      if (passwordInput) {
        const type =
          passwordInput.getAttribute("type") === "password"
            ? "text"
            : "password";
        passwordInput.setAttribute("type", type);
        this.classList.toggle("fa-eye");
        this.classList.toggle("fa-eye-slash");
      }
    });
  });

  // 2. KIỂM TRA FORM ĐĂNG NHẬP
  const loginForm = document.getElementById("loginForm");
  if (loginForm) {
    loginForm.addEventListener("submit", function (event) {
      const email = document.getElementById("email").value.trim();
      const password = document.getElementById("password").value.trim();
      if (email === "" || password === "") {
        event.preventDefault();
        showError(this, "Vui lòng nhập đầy đủ Email và Mật khẩu!");
      }
    });
  }

  // 3. KIỂM TRA FORM ĐĂNG KÝ
  const registerForm = document.getElementById("registerForm");
  if (registerForm) {
    registerForm.addEventListener("submit", function (event) {
      const pass = document.getElementById("reg-password").value;
      const confirmPass = document.getElementById("confirm-password").value;
      if (pass !== confirmPass) {
        event.preventDefault();
        showError(this, "Mật khẩu và Xác nhận mật khẩu không khớp!");
        document.getElementById("confirm-password").value = "";
        document.getElementById("confirm-password").focus();
      }
    });
  }

  // 4. KIỂM TRA FORM QUÊN MẬT KHẨU
  const forgotPasswordForm = document.getElementById("forgotPasswordForm");
  if (forgotPasswordForm) {
    forgotPasswordForm.addEventListener("submit", function (event) {
      const email = document.getElementById("reset-email").value.trim();
      if (email === "") {
        event.preventDefault();
        showError(this, "Vui lòng nhập địa chỉ email!");
      }
    });
  }
});
