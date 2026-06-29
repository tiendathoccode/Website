CREATE TABLE users (
    -- Khóa chính, tự động tăng
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    
    -- Thông tin cá nhân cơ bản
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE, -- UNIQUE để đảm bảo mỗi email chỉ đăng ký 1 tài khoản
    phone VARCHAR(20),
    avatar VARCHAR(255), -- Lưu đường dẫn đến file ảnh (ví dụ: uploads/avatars/user1.jpg)
    
    -- Mật khẩu cần độ dài lớn để lưu chuỗi đã mã hóa (ví dụ dùng hàm password_hash() trong PHP)
    password VARCHAR(255) NOT NULL, 
    reset_token VARCHAR(255),
    reset_token_expire DATETIME,
    
    -- Phân quyền và Trạng thái
    role ENUM('customer', 'admin') DEFAULT 'customer',
    status ENUM('active', 'locked') DEFAULT 'active',
    
    -- Tự động lưu thời gian tạo và cập nhật
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE user_addresses (
    -- Khóa chính của bảng địa chỉ
    address_id INT AUTO_INCREMENT PRIMARY KEY,
    
    -- Khóa ngoại liên kết với bảng users
    user_id INT NOT NULL,
    
    -- Thông tin người nhận hàng
    receiver_name VARCHAR(100) NOT NULL,
    receiver_phone VARCHAR(20) NOT NULL,
    
    -- Cấu trúc địa chỉ phân cấp để dễ quản lý và giao hàng
    province_city VARCHAR(100) NOT NULL, -- Tỉnh / Thành phố
    district VARCHAR(100) NOT NULL,      -- Quận / Huyện
    ward_commune VARCHAR(100) NOT NULL,   -- Phường / Xã
    specific_address VARCHAR(255) NOT NULL, -- Số nhà, tên đường, thôn/xóm...
    
    -- Trạng thái địa chỉ mặc định (1: Mặc định, 0: Bình thường)
    is_default TINYINT(1) DEFAULT 0,
    
    -- Thời gian lưu trữ
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Định nghĩa khóa ngoại và thiết lập xóa tự động nếu user bị xóa
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE categories (
    -- Khóa chính của bảng danh mục
    category_id INT AUTO_INCREMENT PRIMARY KEY,
    
    -- Tên danh mục và Slug tối ưu URL (Không được trùng nhau)
    category_name VARCHAR(100) NOT NULL UNIQUE,    
    -- Mô tả chi tiết về danh mục (Có thể để trống)
    description TEXT,
    
    -- Trạng thái hiển thị trên Website (Mặc định là hiển thị 'show')
    status ENUM('show', 'hide') DEFAULT 'show',
    
    -- Thời gian tạo và cập nhật danh mục
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE products (
    -- Khóa chính của bảng sản phẩm
    product_id INT AUTO_INCREMENT PRIMARY KEY,
    
    -- Khóa ngoại liên kết với danh mục
    category_id INT NOT NULL,
    
    -- Thông tin cơ bản của sản phẩm
    product_name VARCHAR(255) NOT NULL,
    sku VARCHAR(100) UNIQUE,
    description TEXT,
    
    -- Giá cả (dùng INT vì tiền VNĐ thường là số chẵn lớn, ví dụ: 500000)
    price INT NOT NULL,
    sale_price INT DEFAULT 0, -- Giá khuyến mãi, mặc định là 0 nếu không có
    
    -- Tồn kho và Ảnh đại diện
    stock_quantity INT DEFAULT 0,
    main_image VARCHAR(255) NOT NULL,
    
    -- Trạng thái hiển thị
    status ENUM('show', 'hide') DEFAULT 'show',
    
    -- Thời gian tạo và cập nhật
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Ràng buộc khóa ngoại: Nếu danh mục bị xóa, bạn có thể thiết lập cấm xóa (RESTRICT) 
    -- hoặc tự động xóa sản phẩm (CASCADE). Ở đây tôi dùng CASCADE để đồng bộ.
    FOREIGN KEY (category_id) REFERENCES categories(category_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE product_images (
    -- Khóa chính
    image_id INT AUTO_INCREMENT PRIMARY KEY,
    
    -- Khóa ngoại liên kết với sản phẩm
    product_id INT NOT NULL,
    
    -- Đường dẫn lưu file ảnh (Ví dụ: uploads/products/nhan_goc_nghieng.jpg)
    image_url VARCHAR(255) NOT NULL,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    -- Nếu xóa sản phẩm, các ảnh phụ này cũng tự động bị xóa khỏi CSDL
    FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE product_attributes (
    -- Khóa chính
    attribute_id INT AUTO_INCREMENT PRIMARY KEY,
    
    -- Khóa ngoại liên kết với sản phẩm
    product_id INT NOT NULL,
    
    -- Loại thuộc tính (Chỉ cho phép chọn 1 trong 3 loại này)
    attribute_type ENUM('size', 'color', 'material') NOT NULL,
    
    -- Giá trị của thuộc tính (Ví dụ: 'Size 10', 'Vàng 18K', 'Màu Bạc')
    attribute_value VARCHAR(100) NOT NULL,
    
    -- Nếu xóa sản phẩm, các thuộc tính này cũng tự động bị xóa
    FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE orders (
    -- Khóa chính
    order_id INT AUTO_INCREMENT PRIMARY KEY,
    
    -- Khóa ngoại: Ai là người đặt?
    user_id INT NOT NULL,
    
    -- Mã đơn hàng ngẫu nhiên (Ví dụ: ORD-171829) để dễ tìm kiếm thay vì dùng số 1, 2, 3
    order_code VARCHAR(50) NOT NULL UNIQUE,
    
    -- Lưu cứng thông tin giao hàng tại thời điểm đặt (Tránh việc mất dữ liệu nếu user xóa địa chỉ gốc)
    receiver_name VARCHAR(100) NOT NULL,
    receiver_phone VARCHAR(20) NOT NULL,
    shipping_address VARCHAR(255) NOT NULL,
    
    -- Tiền bạc (Dùng INT vì VNĐ không có thập phân)
    total_amount INT NOT NULL,       -- Tổng tiền ban đầu
    discount_amount INT DEFAULT 0,   -- Số tiền được giảm (nếu xài mã giảm giá)
    final_amount INT NOT NULL,       -- Số tiền thực tế khách phải trả
    
    -- Phương thức thanh toán
    payment_method ENUM('cod', 'bank_transfer') DEFAULT 'cod',
    
    -- Trạng thái đơn hàng đúng như tài liệu nhóm đã chốt
    status ENUM('pending', 'processing', 'shipping', 'delivered', 'cancelled') DEFAULT 'pending',
    
    -- Thời gian đặt hàng
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE order_details (
    -- Khóa chính
    order_detail_id INT AUTO_INCREMENT PRIMARY KEY,
    
    -- Liên kết với đơn hàng nào?
    order_id INT NOT NULL,
    
    -- Liên kết với sản phẩm nào?
    product_id INT NOT NULL,
    
    -- Số lượng và Giá mua TẠI THỜI ĐIỂM ĐẶT HÀNG
    quantity INT NOT NULL,
    price INT NOT NULL,
    
    -- Lưu lại các lựa chọn của khách (vd: Size 10, Màu Bạc)
    selected_size VARCHAR(50),
    selected_color VARCHAR(50),
    selected_material VARCHAR(50),
    
    -- Thiết lập xóa tự động: Nếu Admin xóa Đơn hàng, các Chi tiết đơn hàng cũng sẽ biến mất
    FOREIGN KEY (order_id) REFERENCES orders(order_id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(product_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE cart_items (
    -- Khóa chính
    cart_id INT AUTO_INCREMENT PRIMARY KEY,
    
    -- Khóa ngoại: Của ai và Sản phẩm nào?
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    
    -- Số lượng sản phẩm khách muốn mua
    quantity INT NOT NULL DEFAULT 1,
    
    -- Lưu lại các lựa chọn của khách (vd: Size 10, Màu Bạc)
    selected_size VARCHAR(50),
    selected_color VARCHAR(50),
    selected_material VARCHAR(50),
    
    -- Thời gian thêm vào giỏ
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Xóa tự động: Nếu tài khoản bị xóa hoặc sản phẩm bị Admin xóa, nó sẽ tự bay màu khỏi giỏ hàng
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE vouchers (
    -- Khóa chính
    voucher_id INT AUTO_INCREMENT PRIMARY KEY,
    
    -- Tên mã giảm giá mà khách hàng sẽ nhập (Viết hoa, không dấu, không khoảng trắng)
    voucher_code VARCHAR(50) NOT NULL UNIQUE,
    
    -- Loại giảm giá: theo phần trăm ('percent') hoặc giảm tiền mặt ('fixed')
    discount_type ENUM('percent', 'fixed') NOT NULL,
    
    -- Giá trị giảm (Ví dụ: 10 cho percent là 10%, 50000 cho fixed là 50.000đ)
    discount_value INT NOT NULL,
    
    -- Mức giảm tối đa (Dành cho loại percent: Ví dụ giảm 20% nhưng TỐI ĐA chỉ được 50.000đ)
    max_discount_amount INT, 
    
    -- Điều kiện áp dụng: Đơn hàng tối thiểu bao nhiêu thì được dùng
    min_order_value INT DEFAULT 0,
    
    -- Quản lý số lượng mã: Tổng số mã phát hành và số mã đã được sử dụng
    usage_limit INT NOT NULL,  
    used_count INT DEFAULT 0,  
    
    -- Thời gian áp dụng mã
    start_date DATETIME NOT NULL,
    end_date DATETIME NOT NULL,
    
    -- Trạng thái: Admin có thể bật/tắt thủ công kể cả khi mã chưa hết hạn
    status ENUM('active', 'inactive') DEFAULT 'active',
    
    -- Thời gian tạo bảng ghi
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE reviews (
    -- Khóa chính
    review_id INT AUTO_INCREMENT PRIMARY KEY,
    
    -- Khóa ngoại: Ai là người đánh giá và đánh giá sản phẩm nào?
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    
    -- Số sao đánh giá (Chỉ cho phép từ 1 đến 5)
    rating TINYINT NOT NULL CHECK (rating >= 1 AND rating <= 5),
    
    -- Nội dung bình luận của khách hàng
    comment TEXT,
    
    -- Trạng thái kiểm duyệt của Admin ('pending': chờ duyệt, 'approved': đã duyệt, 'hidden': bị ẩn)
    status ENUM('pending', 'approved', 'hidden') DEFAULT 'pending',
    
    -- Thời gian đánh giá
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Ràng buộc: Xóa user hoặc xóa sản phẩm thì đánh giá cũng bay theo
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE banners (
    banner_id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255),               -- Tiêu đề của banner (có thể để trống)
    image_url VARCHAR(255) NOT NULL,  -- Đường dẫn file ảnh upload
    target_link VARCHAR(255),         -- Link đích khi khách click vào banner
    display_order INT DEFAULT 0,      -- Thứ tự hiển thị (số nhỏ hiện trước)
    status ENUM('show', 'hide') DEFAULT 'show', -- Bật/tắt banner
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE faqs (
    faq_id INT AUTO_INCREMENT PRIMARY KEY,
    question VARCHAR(255) NOT NULL,   -- Nội dung câu hỏi
    answer TEXT NOT NULL,             -- Nội dung câu trả lời
    display_order INT DEFAULT 0,      -- Thứ tự sắp xếp các câu hỏi
    status ENUM('show', 'hide') DEFAULT 'show',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE contacts (
    contact_id INT AUTO_INCREMENT PRIMARY KEY,
    customer_name VARCHAR(100) NOT NULL,
    customer_email VARCHAR(150) NOT NULL,
    message TEXT NOT NULL,            -- Nội dung khách gửi
    is_read TINYINT(1) DEFAULT 0,     -- Trạng thái: 0 là chưa đọc, 1 là đã đọc
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
