<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION["user_logged_in"]) || $_SESSION["user_logged_in"] !== true || ($_SESSION["user_role"] ?? "") !== "admin") {
    header("Location: /index.php?page=login");
    exit();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aurrelia Fine Jewelry - Tin nhắn chăm sóc khách hàng</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=Playfair+Display:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="/assets/admin/style.css">
    <link rel="icon" type="image/png" href="/favicon.png" />
    
    <style>
        /* CSS tuỳ chỉnh cho Giao diện Chat Premium */
        .chat-container {
            height: calc(100vh - 120px);
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            overflow: hidden;
            border: 1px solid #f0ece4;
        }
        
        .chat-sidebar {
            border-right: 1px solid #f0ece4;
            display: flex;
            flex-column: column;
            background: #fdfbf7;
            height: 100%;
        }

        .chat-sidebar-header {
            padding: 20px;
            border-bottom: 1px solid #f0ece4;
        }

        .chat-search {
            position: relative;
        }

        .chat-search input {
            padding-left: 35px;
            border-radius: 50px;
            border: 1px solid #e2d9c9;
            font-size: 13px;
            background-color: #fff;
            transition: all 0.2s;
        }

        .chat-search input:focus {
            border-color: #c8a165;
            box-shadow: 0 0 0 3px rgba(200, 161, 101, 0.15);
        }

        .chat-search i {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #a89f91;
            font-size: 14px;
        }

        .chat-user-list {
            flex-grow: 1;
            overflow-y: auto;
            padding: 10px 0;
        }

        .chat-user-item {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            cursor: pointer;
            transition: all 0.2s;
            border-left: 3px solid transparent;
        }

        .chat-user-item:hover {
            background-color: #f7f3eb;
        }

        .chat-user-item.active {
            background-color: #f5edd8;
            border-left-color: #c8a165;
        }

        .user-avatar-wrapper {
            position: relative;
            margin-right: 12px;
        }

        .user-avatar {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            object-fit: cover;
            background-color: #e2d9c9;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            color: #fff;
        }

        .avatar-orange { background-color: #e67e22; }
        .avatar-blue { background-color: #3498db; }
        .avatar-purple { background-color: #9b59b6; }
        .avatar-green { background-color: #2ecc71; }
        .avatar-red { background-color: #e74c3c; }
        .avatar-gold { background-color: #c8a165; }

        .chat-user-info {
            flex-grow: 1;
            min-width: 0; /* để text-overflow hoạt động */
        }

        .chat-user-name {
            font-weight: 600;
            font-size: 14px;
            color: #333;
            margin-bottom: 2px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .chat-meta-time {
            font-size: 11px;
            color: #888;
            font-weight: normal;
        }

        .chat-last-msg {
            font-size: 12px;
            color: #777;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            margin-bottom: 0;
        }

        .chat-user-item.active .chat-last-msg {
            color: #555;
        }

        .chat-user-item.unread .chat-last-msg {
            font-weight: 600;
            color: #111;
        }

        .badge-unread {
            background-color: #c8a165;
            color: white;
            font-size: 10px;
            font-weight: bold;
            min-width: 18px;
            height: 18px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-left: 5px;
        }

        /* Khung cửa sổ chat */
        .chat-workspace {
            display: flex;
            flex-direction: column;
            height: 100%;
            background: #fff;
        }

        .chat-header {
            padding: 16px 24px;
            border-bottom: 1px solid #f0ece4;
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #fdfbf7;
        }

        .chat-header-user {
            display: flex;
            align-items: center;
        }

        .chat-header-details h6 {
            margin-bottom: 2px;
            font-weight: 600;
            color: #333;
        }

        .chat-header-details span {
            font-size: 12px;
            color: #666;
        }

        .chat-messages-area {
            flex-grow: 1;
            overflow-y: auto;
            padding: 24px;
            background-color: #faf8f5;
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .message-bubble-wrapper {
            display: flex;
            max-width: 75%;
        }

        .message-bubble-wrapper.admin-sent {
            align-self: flex-end;
            flex-direction: row-reverse;
        }

        .message-bubble-wrapper.user-sent {
            align-self: flex-start;
        }

        .message-bubble {
            padding: 12px 16px;
            border-radius: 16px;
            font-size: 14px;
            line-height: 1.5;
            word-break: break-word;
        }

        .user-sent .message-bubble {
            background-color: #ffffff;
            color: #333;
            border: 1px solid #eadecb;
            border-top-left-radius: 2px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
        }

        .admin-sent .message-bubble {
            background-color: #c8a165;
            color: #ffffff;
            border-top-right-radius: 2px;
            box-shadow: 0 4px 10px rgba(200, 161, 101, 0.15);
        }

        .message-time {
            font-size: 10px;
            color: #999;
            margin-top: 4px;
            display: block;
        }

        .admin-sent .message-time {
            text-align: right;
        }

        .chat-input-area {
            padding: 16px 24px;
            border-top: 1px solid #f0ece4;
            background: #ffffff;
        }

        .chat-input-form {
            display: flex;
            gap: 12px;
            align-items: center;
        }

        .chat-input-form textarea {
            flex-grow: 1;
            border: 1px solid #eadecb;
            border-radius: 12px;
            padding: 10px 15px;
            font-size: 14px;
            resize: none;
            height: 44px;
            outline: none;
            transition: border-color 0.2s;
        }

        .chat-input-form textarea:focus {
            border-color: #c8a165;
        }

        .btn-chat-send {
            background-color: #c8a165;
            color: white;
            border: none;
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-chat-send:hover {
            background-color: #b38d54;
            transform: scale(1.02);
        }

        .btn-chat-send:active {
            transform: scale(0.98);
        }

        .chat-empty-state {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100%;
            color: #a89f91;
            padding: 40px;
            text-align: center;
            background-color: #faf8f5;
        }

        .chat-empty-state i {
            font-size: 48px;
            margin-bottom: 16px;
            color: #e2d9c9;
        }

        .chat-empty-state h5 {
            font-family: 'Playfair Display', serif;
            color: #887a66;
            margin-bottom: 8px;
        }

        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row min-vh-100">
        
        <!-- Sidebar Navigation -->
        <!-- Sidebar Navigation -->
        <nav class="col-md-3 col-lg-2 sidebar border-end p-4">
            <!-- Mobile Header with Hamburger Trigger -->
            <div class="d-flex justify-content-between align-items-center d-md-none mb-2">
                <a href="/index.php?page=admin_dashboard" class="text-decoration-none"><h3 class="brand-logo mb-0">AURRELIA</h3></a>
                <button class="btn btn-link text-dark p-0 border-0" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarMenu" aria-controls="sidebarMenu" aria-expanded="false" aria-label="Toggle navigation">
                    <i class="bi bi-list fs-2"></i>
                </button>
            </div>
            
            <!-- Collapsible Sidebar Content -->
            <div class="collapse d-md-block" id="sidebarMenu">
                <div class="position-sticky d-flex flex-column h-100 justify-content-between">
                    <div>
                        <div class="brand-zone mb-4">
                            <h3 class="brand-logo mb-1">AURRELIA</h3>
                            <small class="text-muted tracking-wider text-uppercase font-xs">Fine Jewelry Admin</small>
                        </div>
                        
                        <ul class="nav flex-column gap-2 mt-4">
                            <li class="nav-item">
                                <a class="nav-link" href="/index.php?page=admin_dashboard"><i class="bi bi-grid-1x2 me-2"></i> Dashboard</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="/index.php?page=admin_products"><i class="bi bi-gem me-2"></i> Sản Phẩm</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="/index.php?page=admin_categories"><i class="bi bi-tags me-2"></i> Danh Mục</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="/index.php?page=admin_orders"><i class="bi bi-bag me-2"></i> Đơn Hàng</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="/index.php?page=admin_users"><i class="bi bi-people me-2"></i> Người Dùng</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="/index.php?page=admin_content"><i class="bi bi-layout-text-window me-2"></i> Nội Dung</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link active" href="/index.php?page=admin_chat"><i class="bi bi-chat-dots me-2"></i> Tin nhắn</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-danger" href="/index.php?page=logout"><i class="bi bi-box-arrow-right me-2"></i> Đăng xuất</a>
                            </li>
                        </ul>         
                    </div>
                    
                    <div class="user-profile d-flex align-items-center gap-3 pt-3 border-top">
                        <div class="avatar bg-gold text-white rounded-circle d-flex align-items-center justify-content-center fw-bold">
                            <?php 
                            $words = explode(" ", $_SESSION["user_name"]);
                            $initials = "";
                            foreach ($words as $w) {
                                $initials .= mb_substr($w, 0, 1, "UTF-8");
                            }
                            echo htmlspecialchars(mb_strtoupper(mb_substr($initials, -2, 2, "UTF-8"), "UTF-8"));
                            ?>
                        </div>
                        <div>
                            <h6 class="mb-0 small fw-bold text-dark"><?php echo htmlspecialchars($_SESSION["user_name"]); ?></h6>
                            <small class="text-muted font-xs"><?php echo htmlspecialchars(ucfirst($_SESSION["user_role"])); ?></small>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Main Content Area -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4 main-content">
            
            <div class="d-flex justify-content-between align-items-center pt-3 pb-2 mb-4 border-bottom">
                <div>
                    <h1 class="page-title mb-1">Chăm sóc khách hàng</h1>
                    <p class="text-muted mb-0 small">Kênh nhắn tin trực tuyến và hỗ trợ tư vấn</p>
                </div>
            </div>

            <div class="row chat-container g-0">
                
                <!-- Hộp thoại danh sách Chat -->
                <div class="col-md-4 chat-sidebar d-flex flex-column">
                    <div class="chat-sidebar-header">
                        <div class="chat-search">
                            <i class="bi bi-search"></i>
                            <input type="text" class="form-control" id="chatSearchInput" placeholder="Tìm tên khách hàng...">
                        </div>
                    </div>
                    <div class="chat-user-list" id="chatUserList">
                        <!-- Sẽ tự động đổ dữ liệu bằng JS -->
                        <div class="text-center py-4 text-muted">
                            <div class="spinner-border spinner-border-sm text-gold mb-2" role="status"></div>
                            <p class="small mb-0">Đang tải danh sách...</p>
                        </div>
                    </div>
                </div>
                
                <!-- Hộp thoại Nội dung Chat -->
                <div class="col-md-8 chat-workspace">
                    <div id="chatContentWrapper" class="h-100 d-flex flex-column" style="display: none;">
                        <!-- Header thông tin khách hàng -->
                        <div class="chat-header">
                            <div class="chat-header-user">
                                <div class="user-avatar avatar-gold me-3" id="activeUserAvatar">US</div>
                                <div class="chat-header-details">
                                    <h6 class="mb-0" id="activeUserName">Khách hàng</h6>
                                    <span id="activeUserEmail">Email: ...</span>
                                </div>
                            </div>
                            <div class="chat-header-actions">
                                <a id="activeUserPhone" href="tel:" class="btn btn-sm btn-outline-secondary px-3 py-1 rounded-pill" style="font-size: 12px;">
                                    <i class="bi bi-telephone me-1"></i> Gọi điện
                                </a>
                            </div>
                        </div>
                        
                        <!-- Vùng hiển thị các tin nhắn -->
                        <div class="chat-messages-area" id="chatMessagesArea">
                            <!-- Đổ tin nhắn bằng JS -->
                        </div>
                        
                        <!-- Form nhập tin nhắn gửi đi -->
                        <div class="chat-input-area">
                            <form class="chat-input-form" id="chatInputForm">
                                <textarea id="chatMessageInput" placeholder="Nhập câu trả lời của bạn tại đây... (Nhấn Enter để gửi)"></textarea>
                                <button type="submit" class="btn-chat-send">
                                    <i class="bi bi-send-fill"></i>
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Khung trống khi chưa chọn cuộc hội thoại nào -->
                    <div id="chatEmptyState" class="chat-empty-state">
                        <i class="bi bi-chat-heart"></i>
                        <h5>Tư vấn Aurrelia</h5>
                        <p class="small text-muted mb-0">Chọn một cuộc trò chuyện từ danh sách bên trái để xem lịch sử và bắt đầu hỗ trợ tư vấn khách hàng.</p>
                    </div>
                </div>

            </div>

        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // JS Logic điều khiển Admin Chat
    let activeUserId = null;
    let pollInterval = null;
    let lastMessageId = 0;
    let userList = [];
    const colors = ['orange', 'blue', 'purple', 'green', 'red', 'gold'];

    function getInitials(name) {
        if (!name) return "US";
        const words = name.split(" ");
        let initials = "";
        for (let w of words) {
            if (w) initials += w[0];
        }
        return initials.substring(initials.length - 2).toUpperCase();
    }

    function getAvatarColor(userId) {
        return colors[userId % colors.length];
    }

    // Tải danh sách người dùng đã nhắn tin
    async function loadUserList(isUpdate = false) {
        try {
            const response = await fetch('/index.php?page=chat&action=list');
            const data = await response.json();
            
            if (data.status === 'success') {
                userList = data.users;
                renderUserList();
            }
        } catch (error) {
            console.error("Lỗi tải danh sách người dùng:", error);
        }
    }

    // Hiển thị danh sách khách hàng lên màn hình
    function renderUserList() {
        const searchQuery = document.getElementById('chatSearchInput').value.toLowerCase().trim();
        const listContainer = document.getElementById('chatUserList');
        listContainer.innerHTML = '';
        
        const filteredUsers = userList.filter(user => 
            user.full_name.toLowerCase().includes(searchQuery) || 
            (user.email && user.email.toLowerCase().includes(searchQuery))
        );

        if (filteredUsers.length === 0) {
            listContainer.innerHTML = `
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-chat-square-text fs-2 mb-2"></i>
                    <p class="small mb-0">Không tìm thấy cuộc trò chuyện nào</p>
                </div>
            `;
            return;
        }

        filteredUsers.forEach(user => {
            const initials = getInitials(user.full_name);
            const colorClass = 'avatar-' + getAvatarColor(user.user_id);
            const isActive = parseInt(activeUserId) === parseInt(user.user_id) ? 'active' : '';
            const isUnread = parseInt(user.unread_count) > 0 ? 'unread' : '';
            const unreadBadge = parseInt(user.unread_count) > 0 ? `<span class="badge-unread">${user.unread_count}</span>` : '';
            
            const lastMsg = user.last_message ? user.last_message : 'Bắt đầu cuộc trò chuyện...';
            const timeStr = user.formatted_time ? user.formatted_time.split(',')[0] : ''; // lấy giờ

            const item = document.createElement('div');
            item.className = `chat-user-item ${isActive} ${isUnread}`;
            item.onclick = () => selectUser(user);
            item.innerHTML = `
                <div class="user-avatar-wrapper">
                    <div class="user-avatar ${colorClass}">${initials}</div>
                </div>
                <div class="chat-user-info">
                    <div class="chat-user-name">
                        <span>${user.full_name}</span>
                        <span class="chat-meta-time">${timeStr}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mt-1">
                        <p class="chat-last-msg">${lastMsg}</p>
                        ${unreadBadge}
                    </div>
                </div>
            `;
            listContainer.appendChild(item);
        });
    }

    // Chọn cuộc trò chuyện của một khách hàng cụ thể
    function selectUser(user) {
        activeUserId = user.user_id;
        lastMessageId = 0; // Reset lastMessageId khi đổi user để tải lại toàn bộ
        
        // Cập nhật giao diện Sidebar active
        document.querySelectorAll('.chat-user-item').forEach(item => item.classList.remove('active'));
        
        // Hiển thị workspace chat
        document.getElementById('chatEmptyState').style.setProperty('display', 'none', 'important');
        const wrapper = document.getElementById('chatContentWrapper');
        wrapper.style.display = 'flex';
        
        // Hiện spinner chờ tải
        const messagesArea = document.getElementById('chatMessagesArea');
        messagesArea.innerHTML = `
            <div class="text-center py-5 text-muted">
                <div class="spinner-border spinner-border-sm text-gold mb-2" role="status"></div>
                <p class="small mb-0">Đang tải lịch sử chat...</p>
            </div>
        `;
        
        // Đổ thông tin khách hàng lên header
        document.getElementById('activeUserName').textContent = user.full_name;
        document.getElementById('activeUserEmail').textContent = `Email: ${user.email || 'Không có'} | SĐT: ${user.phone || 'Không có'}`;
        
        const avatar = document.getElementById('activeUserAvatar');
        avatar.className = `user-avatar avatar-${getAvatarColor(user.user_id)} me-3`;
        avatar.textContent = getInitials(user.full_name);
        
        const phoneLink = document.getElementById('activeUserPhone');
        if (user.phone) {
            phoneLink.href = `tel:${user.phone}`;
            phoneLink.style.display = 'inline-block';
        } else {
            phoneLink.style.display = 'none';
        }

        // Tải lịch sử tin nhắn của user này ngay lập tức
        loadChatHistory(true);
        
        // Thiết lập tự động cập nhật
        if (pollInterval) clearInterval(pollInterval);
        pollInterval = setInterval(loadChatHistory, 3000);
        
        // Cập nhật lại list user để xoá badge unread lập tức
        user.unread_count = 0;
        renderUserList();
    }

    // Tải lịch sử cuộc hội thoại
    async function loadChatHistory(scrollDown = false) {
        if (!activeUserId) return;
        
        try {
            const response = await fetch(`/index.php?page=chat&action=history&user_id=${activeUserId}&last_id=${lastMessageId}`);
            const data = await response.json();
            
            if (data.status === 'success') {
                const messagesArea = document.getElementById('chatMessagesArea');
                const messages = data.messages;
                
                if (messages.length > 0) {
                    if (lastMessageId === 0) {
                        messagesArea.innerHTML = ''; // Xoá spinner tải ban đầu
                    }
                    
                    messages.forEach(msg => {
                        const isFromAdmin = parseInt(msg.sender_id) !== parseInt(activeUserId);
                        const wrapperClass = isFromAdmin ? 'admin-sent' : 'user-sent';
                        
                        const bubble = document.createElement('div');
                        bubble.className = `message-bubble-wrapper ${wrapperClass}`;
                        bubble.innerHTML = `
                            <div class="message-bubble">
                                <div>${escapeHTML(msg.message)}</div>
                                <span class="message-time">${msg.formatted_time}</span>
                            </div>
                        `;
                        messagesArea.appendChild(bubble);
                    });
                    
                    lastMessageId = parseInt(messages[messages.length - 1].id);
                    messagesArea.scrollTop = messagesArea.scrollHeight;
                } else if (lastMessageId === 0) {
                    messagesArea.innerHTML = `
                        <div class="text-center py-5 text-muted">
                            <p class="small">Chưa có tin nhắn nào. Hãy gửi lời chào đến khách hàng!</p>
                        </div>
                    `;
                }
            }
        } catch (error) {
            console.error("Lỗi tải lịch sử chat:", error);
        }
    }

    // Tránh lỗ hổng XSS
    function escapeHTML(str) {
        return str.replace(/[&<>'"]/g, 
            tag => ({
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                "'": '&#39;',
                '"': '&quot;'
            }[tag] || tag)
        );
    }

    // Gửi tin nhắn đi
    document.getElementById('chatInputForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const input = document.getElementById('chatMessageInput');
        const sendBtn = document.querySelector('.btn-chat-send');
        const messageText = input.value.trim();
        
        if (!messageText || !activeUserId) return;
        
        input.value = '';
        
        let originalBtnHtml = '';
        if (sendBtn) {
            originalBtnHtml = sendBtn.innerHTML;
            sendBtn.innerHTML = '<i class="bi bi-arrow-repeat spin-animation" style="display:inline-block; animation: spin 1s linear infinite;"></i>';
            sendBtn.disabled = true;
        }
        
        try {
            const formData = new FormData();
            formData.append('user_id', activeUserId);
            formData.append('message', messageText);
            
            const response = await fetch('/index.php?page=chat&action=send', {
                method: 'POST',
                body: formData
            });
            
            const data = await response.json();
            if (data.status === 'success') {
                // Tải lại lịch sử và tự động cuộn xuống dưới
                loadChatHistory(true);
                // Cập nhật lại tin nhắn cuối cùng trên thanh sidebar
                loadUserList();
            } else {
                alert("Lỗi: " + data.message);
                input.value = messageText;
            }
        } catch (error) {
            console.error("Lỗi gửi tin nhắn:", error);
            input.value = messageText;
        } finally {
            if (sendBtn) {
                sendBtn.innerHTML = originalBtnHtml;
                sendBtn.disabled = false;
            }
            input.focus();
        }
    });

    // Bắt phím Enter trong Textarea để gửi tin nhắn thay vì xuống dòng (xuống dòng dùng Shift+Enter)
    document.getElementById('chatMessageInput').addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            const sendBtn = document.querySelector('.btn-chat-send');
            if (sendBtn) {
                sendBtn.click();
            }
        }
    });

    // Bắt sự kiện gõ tìm kiếm người dùng
    document.getElementById('chatSearchInput').addEventListener('input', renderUserList);

    // Khởi chạy khi tải trang
    document.addEventListener('DOMContentLoaded', () => {
        loadUserList();
        
        // Định kỳ tải danh sách người dùng mỗi 10 giây
        setInterval(() => {
            loadUserList(true);
        }, 10000);
    });
</script>
</body>
</html>
