/**
 * Aurrelia Jewelry Chat Integration
 * Dynamic floating chat widget for users to message admins.
 */

(function() {
    // 1. Khai báo các biến trạng thái
    let isChatOpen = false;
    let pollInterval = null;
    let unreadPollInterval = null;
    let lastMessageCount = 0;
    let lastMessageId = 0;
    const isUserLoggedIn = (typeof window.USER_LOGGED_IN !== 'undefined' && (window.USER_LOGGED_IN === true || window.USER_LOGGED_IN === 'true')) || 
                           (typeof window.IS_LOGGED_IN !== 'undefined' && (window.IS_LOGGED_IN === true || window.IS_LOGGED_IN === 'true'));

    // 2. Chèn CSS Stylesheet cho Chat Widget
    const injectStyles = () => {
        const style = document.createElement('style');
        style.innerHTML = `
            /* Floating Action Button (FAB) */
            #aurrelia-chat-fab {
                position: fixed;
                bottom: 24px;
                right: 24px;
                width: 60px;
                height: 60px;
                border-radius: 50%;
                background-color: #c8a165;
                color: #ffffff;
                box-shadow: 0 4px 16px rgba(200, 161, 101, 0.4);
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                z-index: 9999;
                transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
                border: 2px solid #fff;
            }

            #aurrelia-chat-fab:hover {
                transform: scale(1.08) translateY(-2px);
                background-color: #bfa15f;
                box-shadow: 0 6px 20px rgba(200, 161, 101, 0.5);
            }

            #aurrelia-chat-fab i {
                font-size: 24px;
                transition: transform 0.3s;
            }

            #aurrelia-chat-fab.open i {
                transform: rotate(90deg);
            }

            /* Badge thông báo tin nhắn chưa đọc */
            #aurrelia-chat-badge {
                position: absolute;
                top: -3px;
                right: -3px;
                background-color: #e74c3c;
                color: #fff;
                font-size: 11px;
                font-weight: bold;
                width: 20px;
                height: 20px;
                border-radius: 50%;
                display: none;
                align-items: center;
                justify-content: center;
                border: 1.5px solid #fff;
                box-shadow: 0 2px 5px rgba(0,0,0,0.2);
            }

            /* Pulse Animation */
            .aurrelia-chat-pulse {
                position: absolute;
                width: 100%;
                height: 100%;
                border-radius: 50%;
                background: rgba(200, 161, 101, 0.4);
                animation: aurrelia-pulse-key 2s infinite;
                z-index: -1;
                pointer-events: none;
            }

            @keyframes aurrelia-pulse-key {
                0% { transform: scale(1.0); opacity: 1; }
                100% { transform: scale(1.4); opacity: 0; }
            }

            /* Chat Window Container */
            #aurrelia-chat-window {
                position: fixed;
                bottom: 96px;
                right: 24px;
                width: 360px;
                height: 500px;
                max-height: calc(100vh - 120px);
                background: #ffffff;
                border-radius: 18px;
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.12);
                display: flex;
                flex-direction: column;
                z-index: 10000;
                overflow: hidden;
                opacity: 0;
                transform: translateY(20px) scale(0.95);
                transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.15);
                pointer-events: none;
                border: 1px solid #f2edd8;
            }

            #aurrelia-chat-window.open {
                opacity: 1;
                transform: translateY(0) scale(1);
                pointer-events: auto;
            }

            /* Header */
            .aurrelia-chat-header {
                background: linear-gradient(135deg, #c8a165 0%, #bfa15f 100%);
                color: #ffffff;
                padding: 16px 20px;
                display: flex;
                align-items: center;
                justify-content: space-between;
                box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            }

            .aurrelia-chat-brand {
                display: flex;
                align-items: center;
                gap: 12px;
            }

            .aurrelia-chat-logo {
                width: 34px;
                height: 34px;
                border-radius: 50%;
                background: rgba(255, 255, 255, 0.2);
                display: flex;
                align-items: center;
                justify-content: center;
                font-weight: bold;
                border: 1px solid rgba(255,255,255,0.4);
                font-size: 14px;
            }

            .aurrelia-chat-title h6 {
                margin: 0;
                font-weight: 600;
                font-size: 14px;
                letter-spacing: 0.5px;
            }

            .aurrelia-chat-title span {
                font-size: 11px;
                opacity: 0.85;
                display: flex;
                align-items: center;
                gap: 4px;
            }

            .aurrelia-chat-status-dot {
                width: 6px;
                height: 6px;
                background-color: #2ecc71;
                border-radius: 50%;
                display: inline-block;
                animation: status-blink 1.5s infinite;
            }

            @keyframes status-blink {
                0% { opacity: 0.4; }
                50% { opacity: 1; }
                100% { opacity: 0.4; }
            }

            .aurrelia-chat-close-btn {
                background: none;
                border: none;
                color: #ffffff;
                cursor: pointer;
                font-size: 18px;
                padding: 0;
                opacity: 0.8;
                transition: opacity 0.2s;
            }

            .aurrelia-chat-close-btn:hover {
                opacity: 1;
            }

            /* Body/Messages Area */
            .aurrelia-chat-body {
                flex-grow: 1;
                overflow-y: auto;
                padding: 20px;
                background-color: #fdfbf7;
                display: flex;
                flex-direction: column;
                gap: 12px;
            }

            .aurrelia-msg-row {
                display: flex;
                max-width: 85%;
            }

            .aurrelia-msg-row.admin {
                align-self: flex-start;
            }

            .aurrelia-msg-row.user {
                align-self: flex-end;
                flex-direction: row-reverse;
            }

            .aurrelia-msg-bubble {
                padding: 10px 14px;
                border-radius: 14px;
                font-size: 13.5px;
                line-height: 1.4;
                word-break: break-word;
                box-shadow: 0 1px 3px rgba(0,0,0,0.02);
            }

            .aurrelia-msg-row.admin .aurrelia-msg-bubble {
                background-color: #ffffff;
                color: #333333;
                border: 1px solid #eadecb;
                border-top-left-radius: 2px;
            }

            .aurrelia-msg-row.user .aurrelia-msg-bubble {
                background-color: #c8a165;
                color: #ffffff;
                border-top-right-radius: 2px;
            }

            .aurrelia-msg-time {
                font-size: 9.5px;
                color: #999;
                margin-top: 4px;
                display: block;
            }

            .aurrelia-msg-row.user .aurrelia-msg-time {
                text-align: right;
            }

            /* Footer Input Area */
            .aurrelia-chat-footer {
                padding: 12px 16px;
                border-top: 1px solid #f2edd8;
                background: #ffffff;
            }

            .aurrelia-chat-form {
                display: flex;
                gap: 10px;
                align-items: center;
            }

            .aurrelia-chat-form input {
                flex-grow: 1;
                border: 1px solid #eadecb;
                border-radius: 20px;
                padding: 8px 16px;
                font-size: 13px;
                outline: none;
                transition: border-color 0.2s;
                height: 38px;
            }

            .aurrelia-chat-form input:focus {
                border-color: #c8a165;
            }

            .aurrelia-chat-send-btn {
                background-color: #c8a165;
                color: white;
                border: none;
                width: 38px;
                height: 38px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                transition: all 0.2s;
                padding: 0;
            }

            .aurrelia-chat-send-btn:hover {
                background-color: #b38d54;
                transform: scale(1.05);
            }

            /* Non-logged in State View */
            .aurrelia-chat-unauth {
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                height: 100%;
                padding: 30px;
                text-align: center;
                color: #887a66;
            }

            .aurrelia-chat-unauth i {
                font-size: 40px;
                color: #e2d9c9;
                margin-bottom: 16px;
            }

            .aurrelia-chat-unauth p {
                font-size: 13px;
                line-height: 1.6;
                color: #666;
                margin-bottom: 20px;
            }

            .aurrelia-chat-login-btn {
                background-color: #c8a165;
                color: #ffffff;
                border: none;
                padding: 10px 24px;
                border-radius: 50px;
                font-size: 13px;
                font-weight: 600;
                text-decoration: none;
                transition: background-color 0.2s;
                box-shadow: 0 4px 10px rgba(200, 161, 101, 0.2);
            }

            .aurrelia-chat-login-btn:hover {
                background-color: #b38d54;
                color: #ffffff;
            }
        `;
        document.head.appendChild(style);
    };

    // 3. Khởi tạo và chèn HTML cho Chat Widget
    const initDOM = () => {
        // Tạo FAB
        const fab = document.createElement('div');
        fab.id = 'aurrelia-chat-fab';
        fab.innerHTML = `
            <div class="aurrelia-chat-pulse"></div>
            <i class="fas fa-comments" id="aurrelia-fab-icon"></i>
            <span id="aurrelia-chat-badge">0</span>
        `;
        document.body.appendChild(fab);

        // Tạo Chat Window
        const chatWindow = document.createElement('div');
        chatWindow.id = 'aurrelia-chat-window';
        
        let bodyContent = '';
        let footerContent = '';

        if (isUserLoggedIn) {
            bodyContent = `
                <div class="aurrelia-chat-body" id="aurreliaChatBody">
                    <div class="text-center py-4 text-muted">
                        <div class="spinner-border spinner-border-sm text-warning" role="status"></div>
                        <p class="small mt-2 mb-0">Đang tải tin nhắn...</p>
                    </div>
                </div>
            `;
            footerContent = `
                <div class="aurrelia-chat-footer">
                    <form class="aurrelia-chat-form" id="aurreliaChatForm">
                        <input type="text" id="aurreliaChatInput" placeholder="Nhập tin nhắn..." autocomplete="off">
                        <button type="submit" class="aurrelia-chat-send-btn">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </form>
                </div>
            `;
        } else {
            bodyContent = `
                <div class="aurrelia-chat-body">
                    <div class="aurrelia-chat-unauth">
                        <i class="fas fa-lock"></i>
                        <h6 class="fw-bold mb-2">Trò chuyện với Aurrelia</h6>
                        <p>Xin chào quý khách! Vui lòng đăng nhập tài khoản để nhắn tin trực tuyến với đội ngũ chuyên viên tư vấn Aurrelia Fine Jewelry.</p>
                        <a href="/index.php?page=login" class="aurrelia-chat-login-btn">Đăng nhập ngay</a>
                    </div>
                </div>
            `;
            footerContent = '';
        }

        chatWindow.innerHTML = `
            <div class="aurrelia-chat-header">
                <div class="aurrelia-chat-brand">
                    <div class="aurrelia-chat-logo">AR</div>
                    <div class="aurrelia-chat-title">
                        <h6>Aurrelia Jewelry</h6>
                        <span><span class="aurrelia-chat-status-dot"></span>Chuyên viên hỗ trợ trực tuyến</span>
                    </div>
                </div>
                <button class="aurrelia-chat-close-btn" id="aurreliaChatCloseBtn">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            ${bodyContent}
            ${footerContent}
        `;
        document.body.appendChild(chatWindow);
    };

    // 4. Tránh XSS bằng cách mã hóa HTML
    const escapeHTML = (str) => {
        return str.replace(/[&<>'"]/g, 
            tag => ({
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                "'": '&#39;',
                '"': '&quot;'
            }[tag] || tag)
        );
    };

    // 5. Tải lịch sử tin nhắn của người dùng
    const loadMessages = async (forceScroll = false) => {
        if (!isUserLoggedIn) return;

        try {
            const response = await fetch(`/index.php?page=chat&action=history&last_id=${lastMessageId}`);
            const data = await response.json();

            if (data.status === 'success') {
                const chatBody = document.getElementById('aurreliaChatBody');
                if (!chatBody) return;

                const messages = data.messages;
                if (messages.length > 0) {
                    if (lastMessageId === 0) {
                        chatBody.innerHTML = ''; // Xoá spinner tải ban đầu
                    }

                    messages.forEach(msg => {
                        const isAdminMsg = parseInt(msg.sender_id) === 1;
                        const rowClass = isAdminMsg ? 'admin' : 'user';

                        const row = document.createElement('div');
                        row.className = `aurrelia-msg-row ${rowClass}`;
                        row.innerHTML = `
                            <div class="aurrelia-msg-bubble">
                                <div>${escapeHTML(msg.message)}</div>
                                <span class="aurrelia-msg-time">${msg.formatted_time.split(',')[0]}</span>
                            </div>
                        `;
                        chatBody.appendChild(row);
                    });

                    lastMessageId = parseInt(messages[messages.length - 1].id);
                    chatBody.scrollTop = chatBody.scrollHeight;
                } else if (lastMessageId === 0) {
                    chatBody.innerHTML = `
                        <div class="text-center py-5 text-muted" style="margin-top:20px;">
                            <i class="fas fa-heart fs-2 text-warning mb-2" style="opacity:0.3;"></i>
                            <p class="small mb-0">Hãy bắt đầu câu chuyện với chuyên viên tư vấn Aurrelia của bạn nhé!</p>
                        </div>
                    `;
                }
            }
        } catch (error) {
            console.error('Aurrelia Chat error loading messages:', error);
        }
    };

    // 6. Gửi tin nhắn đi
    const sendMessage = async (e) => {
        if (e) {
            e.preventDefault();
            e.stopPropagation();
        }
        const input = document.getElementById('aurreliaChatInput');
        const sendBtn = document.querySelector('.aurrelia-chat-send-btn');
        if (!input) return;

        const text = input.value.trim();
        if (!text) return;

        // Lưu lại tin nhắn và xoá thanh nhập liệu ngay lập tức để tạo cảm giác phản hồi nhanh (optimistic UI)
        input.value = '';
        
        // Hiển thị trạng thái đang gửi tin nhắn trên nút gửi
        let originalBtnHtml = '';
        if (sendBtn) {
            originalBtnHtml = sendBtn.innerHTML;
            sendBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            sendBtn.disabled = true;
        }

        try {
            const formData = new FormData();
            formData.append('message', text);

            const response = await fetch('/index.php?page=chat&action=send', {
                method: 'POST',
                body: formData
            });

            const data = await response.json();
            if (data.status === 'success') {
                loadMessages(true);
            } else {
                alert("Lỗi: " + data.message);
                input.value = text; // Khôi phục lại tin nhắn cũ nếu lỗi
            }
        } catch (error) {
            console.error('Aurrelia Chat error sending message:', error);
            input.value = text; // Khôi phục lại tin nhắn cũ nếu lỗi
        } finally {
            if (sendBtn) {
                sendBtn.innerHTML = originalBtnHtml;
                sendBtn.disabled = false;
            }
            input.focus();
        }
    };

    // 7. Đọc số tin nhắn chưa đọc
    const checkUnreadCount = async () => {
        if (!isUserLoggedIn || isChatOpen) return;

        try {
            const response = await fetch('/index.php?page=chat&action=unread_count');
            const data = await response.json();

            if (data.status === 'success') {
                const badge = document.getElementById('aurrelia-chat-badge');
                if (!badge) return;

                const count = parseInt(data.unread_count);
                if (count > 0) {
                    badge.textContent = count;
                    badge.style.display = 'flex';
                } else {
                    badge.style.display = 'none';
                }
            }
        } catch (error) {
            console.error('Aurrelia Chat error checking unread:', error);
        }
    };

    // 8. Đóng/Mở Chat Window
    const toggleChat = () => {
        const fab = document.getElementById('aurrelia-chat-fab');
        const chatWindow = document.getElementById('aurrelia-chat-window');
        const fabIcon = document.getElementById('aurrelia-fab-icon');
        const badge = document.getElementById('aurrelia-chat-badge');

        isChatOpen = !isChatOpen;

        if (isChatOpen) {
            fab.classList.add('open');
            chatWindow.classList.add('open');
            fabIcon.className = 'fas fa-times';
            badge.style.display = 'none'; // Xóa badge khi đã mở

            // Kích hoạt việc load tin nhắn tức thì và bắt đầu polling
            if (isUserLoggedIn) {
                loadMessages(true);
                if (pollInterval) clearInterval(pollInterval);
                pollInterval = setInterval(loadMessages, 3000);
            }
        } else {
            fab.classList.remove('open');
            chatWindow.classList.remove('open');
            fabIcon.className = 'fas fa-comments';

            // Dừng polling để tối ưu hoá tài nguyên
            if (pollInterval) {
                clearInterval(pollInterval);
                pollInterval = null;
            }

            // Gọi kiểm tra tin nhắn chưa đọc định kỳ
            checkUnreadCount();
        }
    };

    // 9. Lắp ghép tất cả sự kiện và khởi chạy
    const bootstrap = () => {
        injectStyles();
        initDOM();

        // Gắn sự kiện click
        document.getElementById('aurrelia-chat-fab').addEventListener('click', toggleChat);
        document.getElementById('aurreliaChatCloseBtn').addEventListener('click', toggleChat);

        if (isUserLoggedIn) {
            const form = document.getElementById('aurreliaChatForm');
            if (form) {
                form.addEventListener('submit', sendMessage);
            }
            // Định kỳ check tin nhắn chưa đọc khi chat đang đóng
            checkUnreadCount();
            unreadPollInterval = setInterval(checkUnreadCount, 10000);
        }
    };

    // Chạy khi DOM đã sẵn sàng
    if (document.readyState === 'complete' || document.readyState === 'interactive') {
        bootstrap();
    } else {
        document.addEventListener('DOMContentLoaded', bootstrap);
    }
})();
