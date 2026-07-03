SET NAMES 'utf8mb4';
SET CHARACTER SET utf8mb4;
SET COLLATION_CONNECTION = 'utf8mb4_unicode_ci';

-- Các câu lệnh CREATE TABLE users của bạn nằm ở dưới...
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

    -- THÊM 2 TRƯỜNG PHỤC VỤ CHỨC NĂNG QUÊN MẬT KHẨU TẠI ĐÂY
    reset_token VARCHAR(255) DEFAULT NULL,
    reset_token_expiry DATETIME DEFAULT NULL,

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
    status ENUM('pending', 'processing', 'shipping', 'delivered', 'cancelled', 'return_requested', 'returned') DEFAULT 'pending',

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
INSERT INTO users (user_id, full_name, email, phone, password, role, status) VALUES
(1, 'Quản Trị Viên', 'admin@aurrelia.local', '0901234567', '$2y$10$D9arBnnCCmcCb1oj6dNkgOXtipipRyas1XUmTqSpbCupBGvRuJsEO', 'admin', 'active'),
(2, 'Đạt Đủng Đỉnh', 'test123@gmail.com', '0912345678', '$2y$10$D9arBnnCCmcCb1oj6dNkgOXtipipRyas1XUmTqSpbCupBGvRuJsEO', 'customer', 'active'),
(3, 'Bảo chó điên', 'gicungduoc@gmail.com', '0987654321', '$2y$10$D9arBnnCCmcCb1oj6dNkgOXtipipRyas1XUmTqSpbCupBGvRuJsEO', 'customer', 'active'),
(4, 'Trân Trang Trải', 'huyentran@gmail.com', '0987654321', '$2y$10$D9arBnnCCmcCb1oj6dNkgOXtipipRyas1XUmTqSpbCupBGvRuJsEO', 'customer', 'active'),
(5, 'Luân Lẳng Lơ', 'duyluan@gmail.com', '0987654321', '$2y$10$D9arBnnCCmcCb1oj6dNkgOXtipipRyas1XUmTqSpbCupBGvRuJsEO', 'customer', 'active'),
(6, 'Thuấn Thì Thầm', 'thuan@gmail.com', '0987654321', '$2y$10$D9arBnnCCmcCb1oj6dNkgOXtipipRyas1XUmTqSpbCupBGvRuJsEO', 'customer', 'active'),
(7, 'Trời lạnh rồi', 'bang@gmail.com', '0987654321', '$2y$10$D9arBnnCCmcCb1oj6dNkgOXtipipRyas1XUmTqSpbCupBGvRuJsEO', 'customer', 'active');



INSERT INTO categories (category_name, description, status) VALUES
('Nhẫn Kim Cương', 'Bộ sưu tập nhẫn đính hôn', 'show'),
('Dây Chuyền Vàng', 'Dây chuyền vàng 18K', 'show'),
('Vòng Tay Tinh Tế', 'Vòng tay đính đá', 'show'),
('Bông Tai Cao Cấp', 'Bộ sưu tập khuyên tai, bông tai Aurrelia sang trọng', 'show');

-- ==========================================
-- 2. DỮ LIỆU CHUẨN CÚ PHÁP 39 SẢN PHẨM BÔNG TAI (ĐÃ FIX LỖI NHÁY ĐƠN)
-- ==========================================
INSERT INTO products (category_id, product_name, description, price, sale_price, stock_quantity, main_image, status) VALUES
(4, 'Bông tai bạc đính đá hình giọt nước', 'Bông tai bạc đính đá hình giọt nước mang phong cách thanh lịch và sang trọng, là điểm nhấn hoàn hảo cho mọi trang phục.', 450000, 399000, 100, 'assets/images/product/image_2076968380_1.jpg', 'show'),
(4, 'Bông tai ngọc trai nhân tạo phong cách Hàn Quốc', 'Mang đến vẻ đẹp ngọt ngào và nữ tính, bông tai ngọc trai nhân tạo phong cách Hàn Quốc là lựa chọn lý tưởng cho các cô nàng.', 250000, 199000, 150, 'assets/images/product/image_2076968380_2.jpg', 'show'),
(4, 'Khuyên tai khoen tròn mạ vàng tối giản', 'Khuyên tai khoen tròn mạ vàng sở hữu thiết kế tối giản nhưng không kém phần thời thượng. Sản phẩm phù hợp cho cả nam và nữ.', 180000, 0, 200, 'assets/images/product/image_2076968380_3.jpg', 'show'),
(4, 'Bông tai tua rua dáng dài dự tiệc sang trọng', 'Lấp lánh và nổi bật, bông tai tua rua dáng dài là phụ kiện không thể thiếu trong các bữa tiệc sang trọng.', 650000, 550000, 50, 'assets/images/product/image_2076968380_4.jpg', 'show'),
(4, 'Khuyên tai nụ đính đá kim cương nhân tạo', 'Khuyên tai nụ đính đá kim cương nhân tạo tuy nhỏ nhắn nhưng sở hữu sức hút mãnh liệt nhờ viên đá được cắt gọt tinh xảo.', 350000, 299000, 120, 'assets/images/product/image_2076968380_5.jpg', 'show'),
(4, 'Bông tai hình ngôi sao đính đá pha lê lấp lánh', 'Thiết kế hình ngôi sao độc đáo kết hợp với đá pha lê lấp lánh mang lại vẻ đẹp trẻ trung, năng động nhưng không kém phần cuốn hút.', 290000, 249000, 80, 'assets/images/product/image_2076968380_6.jpg', 'show'),
(4, 'Khuyên tai kẹp vành tai cá tính không cần bấm lỗ', 'Giải pháp hoàn hảo cho những ai yêu thích khuyên vành tai nhưng ngại bấm lỗ. Thiết kế kẹp chắc chắn, chất liệu an toàn.', 150000, 120000, 300, 'assets/images/product/image_2076968380_7.jpg', 'show'),
(4, 'Bông tai bạc ý hình trái tim ngọt ngào', 'Biểu tượng của tình yêu và sự lãng mạn, bông tai bạc ý hình trái tim sở hữu đường nét mềm mại, tinh tế.', 320000, 0, 90, 'assets/images/product/image_2076968380_8.jpg', 'show'),
(4, 'Bông tai gỗ handmade phong cách vintage độc đáo', 'Được làm thủ công từ chất liệu gỗ tự nhiên kết hợp với họa tiết độc đáo, bông tai mang đậm phong cách vintage, mộc mạc.', 120000, 99000, 60, 'assets/images/product/image_2076968380_9.jpg', 'show'),
(4, 'Khuyên tai dáng dài đính đá cubic zirconia cao cấp', 'Khuyên tai dáng dài kết hợp đá Cubic Zirconia cao cấp mang lại vẻ đẹp lộng lẫy và kiêu sa. Từng viên đá được đính kết tỉ mỉ.', 780000, 699000, 40, 'assets/images/product/image_2076968380_10.jpg', 'show'),
(4, 'Bông tai giọt nước đính đá Topaz xanh dịu', 'Màu xanh đại dương tôn lên vẻ quý phái cho quý cô tiệc đêm. Chất liệu hợp kim mạ bạc chống xỉn màu tối đa.', 490000, 420000, 85, 'assets/images/product/image_2076968380_11.jpg', 'show'),
(4, 'Khuyên nụ ngọc trai đính đá hạt dưa tinh xảo', 'Sự kết hợp phá cách giữa nét cổ điển của ngọc trai và sự sắc sảo của đá hạt dưa CZ bao quanh vành tai.', 380000, 0, 70, 'assets/images/product/image_2076968380_12.jpg', 'show'),
(4, 'Bông tai khoen xích bạc cá tính Urban', 'Phong cách đường phố hiện đại, phóng khoáng, thích hợp mix-match với các trang phục oversized năng động.', 210000, 175000, 110, 'assets/images/product/image_2076968380_13.jpg', 'show'),
(4, 'Bông tai đính đá Emerald dáng lửng quyến rũ', 'Sắc xanh lục bảo ngọc tạo cảm giác huyền bí, sang trọng làm sáng bừng khuôn mặt góc cạnh dưới ánh đèn.', 720000, 650000, 45, 'assets/images/product/image_2076968380_14.jpg', 'show'),
(4, 'Khuyên tai cỏ bốn lá may mắn đính đá Opal', 'Mặt đá lấp lánh đổi màu theo góc nhìn mang lại năng lượng tích cực và sự may mắn cho chủ sở hữu.', 310000, 260000, 95, 'assets/images/product/image_2076968380_15.jpg', 'show'),
(4, 'Bông tai hình trăng khuyết đính pha lê đêm', 'Thiết kế bất đối xứng độc đáo giữa mặt trăng và vì sao, tạo điểm nhấn nghệ thuật đầy chất thơ lãng mạn.', 340000, 289000, 130, 'assets/images/product/image_2076968380_16.jpg', 'show'),
(4, 'Khuyên kẹp vành đính đá chuỗi xích đôi', 'Không cần bấm lỗ vẫn cực ngầu. Dây xích mảnh nối từ nụ tai lên vành tai tạo nét nổi loạn ngầm tinh tế.', 220000, 180000, 140, 'assets/images/product/image_2076968380_17.jpg', 'show'),
(4, 'Bông tai trái tim đan dây bạc lồng ghép', 'Cấu trúc hình khối lập thể đan xen tỉ mỉ thể hiện tình yêu bền chặt gắn kết không thể tách rời.', 290000, 0, 105, 'assets/images/product/image_2076968380_18.jpg', 'show'),
(4, 'Bông tai mây tre đan Bohemian đính vỏ sò biển', 'Chất liệu tự nhiên thân thiện môi trường dành riêng cho những chuyến du lịch biển phóng khoáng ngày hè.', 160000, 125000, 75, 'assets/images/product/image_2076968380_19.jpg', 'show'),
(4, 'Khuyên tai tua rua kim loại ánh kim khiêu vũ', 'Chuyển động nhẹ nhàng theo từng bước đi của bạn, bắt trọn mọi luồng sáng trong khán phòng dạ tiệc.', 820000, 750000, 35, 'assets/images/product/image_2076968380_20.jpg', 'show'),
(4, 'Bông tai hạt cườm sắc màu Geometric phong cách Ý', 'Những mảng màu tương phản hình học mang đậm tính nghệ thuật đương đại đương thời.', 530000, 460000, 65, 'assets/images/product/image_2076968380_21.jpg', 'show'),
(4, 'Khuyên tai nụ tròn đính đá thạch anh hồng', 'Sắc hồng thạch anh thanh tú đem lại vẻ đẹp dịu dàng, hỗ trợ năng lượng tình duyên viên mãn.', 410000, 0, 80, 'assets/images/product/image_2076968380_22.jpg', 'show'),
(4, 'Bông tai khoen dẹt mạ vàng hồng Retro', 'Bản phối hoàn hảo giữa nét cổ điển thập niên 90 và hơi thở hiện đại tối giản quyến rũ.', 260000, 210000, 160, 'assets/images/product/image_2076968380_23.jpg', 'show'),
(4, 'Bông tai lông vũ đính đá lông công quyền lực', 'Biểu tượng của sự kiêu sa, lộng lẫy tột đỉnh dành riêng cho những sàn diễn thời trang ấn tượng.', 890000, 790000, 25, 'assets/images/product/image_2076968380_24.jpg', 'show'),
(4, 'Khuyên tai xỏ hoa mai năm cánh đính ngọc trai', 'Nét đẹp thuần khiết Á Đông, phù hợp cho trang phục áo dài truyền thống hoặc váy lụa thướt tha.', 360000, 299000, 115, 'assets/images/product/image_2076968380_25.jpg', 'show'),
(4, 'Bông tai vòng tròn xoắn ốc khảm xà cừ', 'Lớp xà cừ tự nhiên óng ánh đổi màu liên tục dưới ánh sáng mặt trời cực kỳ bắt mắt.', 330000, 275000, 90, 'assets/images/product/image_2076968380_26.jpg', 'show'),
(4, 'Khuyên kẹp vành tai thánh giá Chrome Hearts cá tính', 'Chất liệu bạc thái hun đen cá tính, mang đậm dấu ấn bụi bặm Gothic đầy ma mị.', 190000, 160000, 210, 'assets/images/product/image_2076968380_27.jpg', 'show'),
(4, 'Bông tai nút thắt vô cực may mắn Aurrelia', 'Biểu tượng của sự vô tận về tài lộc và tình yêu trường tồn mãi theo thời gian.', 300000, 0, 125, 'assets/images/product/image_2076968380_28.jpg', 'show'),
(4, 'Bông tai đất sét nung họa tiết hoa tulip', 'Sản phẩm thủ công độc bản nghệ thuật mang hơi hướng nhẹ nhàng đồng quê Pháp.', 140000, 110000, 55, 'assets/images/product/image_2076968380_29.jpg', 'show'),
(4, 'Khuyên tai dáng dài giọt sương đính Sapphire đen', 'Sự sang trọng lạnh lùng đầy quyền lực từ viên Sapphire đen nguyên khối cắt facet đa tầng.', 950000, 850000, 20, 'assets/images/product/image_2076968380_30.jpg', 'show'),
(4, 'Bông tai khối vuông đính pha lê lập thể', 'Thiết kế tối giản cấu trúc mang tính kỹ thuật cao, tôn cá tính mạnh mẽ cho người sở hữu.', 470000, 399000, 70, 'assets/images/product/image_2076968380_31.jpg', 'show'),
(4, 'Khuyên nụ hoa hướng dương mạ vàng 18K', 'Luôn hướng về phía mặt trời, mang lại diện mạo rạng rỡ tươi trẻ tràn đầy năng lượng ngày mới.', 390000, 0, 85, 'assets/images/product/image_2076968380_32.jpg', 'show'),
(4, 'Bông tai khoen oval xước mờ phong cách Minimalism', 'Bề mặt kim loại được xử lý đánh nhám xước mờ độc đáo, không bám vân tay, thanh lịch tuyệt đối.', 240000, 195000, 180, 'assets/images/product/image_2076968380_33.jpg', 'show'),
(4, 'Bông tai xích bản lớn đính đá viền Hip-hop', 'Phụ kiện nổi bật mang đậm dấu ấn phong cách đường phố cá tính mạnh mẽ phá cách.', 680000, 590000, 40, 'assets/images/product/image_2076968380_34.jpg', 'show'),
(4, 'Khuyên tai lông vũ bạc ta chạm khắc thủ công', 'Từng sợi lông vũ được nghệ nhân chạm khắc tinh xảo sống động chân thực trên chất liệu bạc ta.', 370000, 315000, 100, 'assets/images/product/image_2076968380_35.jpg', 'show'),
(4, 'Bông tai hình bướm uyên ương khảm đá Aquamarine', 'Sắc xanh ngọc biển dịu mát kết hợp tạo hình cánh bướm dập dờn bay lượn nhẹ nhàng thanh thoát.', 420000, 360000, 90, 'assets/images/product/image_2076968380_36.jpg', 'show'),
(4, 'Khuyên kẹp vành đính đá Garnet đỏ rực quyền quý', 'Sắc đỏ thẫm của đá Garnet tự nhiên mang lại vẻ sang quý đầy quyến rũ cuốn hút bí ẩn.', 230000, 190000, 150, 'assets/images/product/image_2076968380_37.jpg', 'show'),
(4, 'Bông tai hoa tuyết đính kim cương nhân tạo tuyết rơi', 'Thiết kế lộng lẫy lấy cảm hứng từ những tinh thể hoa tuyết mùa đông lấp lánh rực rỡ.', 350000, 0, 135, 'assets/images/product/image_2076968380_38.jpg', 'show'),
(4, 'Bông tai tua dài hạt ngọc trai nước ngọt', 'Những viên ngọc trai nước ngọt tự nhiên dáng dài buông lơi tự nhiên quý phái.', 790000, 699000, 30, 'assets/images/product/image_2076968380_39.jpg', 'show'),
(1, 'Nhẫn kim cương vàng trắng 18K Solitaire', 'Nhẫn kim cương Solitaire vàng trắng 18K mang vẻ đẹp cổ điển, tôn lên sự lấp lánh tuyệt đối của viên kim cương chủ.', 15000000, 13900000, 20, 'assets/images/product/nhan_1.jpg', 'show'),
(1, 'Nhẫn kim cương Halo đính đá tấm lấp lánh', 'Thiết kế Halo kiêu sa với vòng đá tấm bao quanh viên đá chủ, mang lại cảm giác lộng lẫy và tỏa sáng rực rỡ.', 22000000, 19900000, 15, 'assets/images/product/nhan_2.jpg', 'show'),
(1, 'Nhẫn cưới kim cương đính đôi Eternity', 'Vòng nhẫn Eternity biểu tượng của tình yêu vĩnh cửu đính kết kim cương CZ tinh xảo chạy dọc thân nhẫn.', 8500000, 7900000, 30, 'assets/images/product/nhan_3.jpg', 'show'),
(1, 'Nhẫn nam kim cương bản lớn quyền lực', 'Kiến tạo phong thái đĩnh đạc và uy nghiêm cho quý ông thành đạt với thiết kế bản dày mạnh mẽ khảm kim cương.', 35000000, 32000000, 10, 'assets/images/product/nhan_4.jpg', 'show'),
(1, 'Nhẫn kim cương vương miện Queen cao cấp', 'Thiết kế lấy cảm hứng từ vương miện hoàng gia quý phái, đính đá saphire kết hợp kim cương tinh tuyển.', 18500000, 16900000, 25, 'assets/images/product/nhan_5.jpg', 'show'),
(2, 'Dây chuyền vàng ý 18K mặt cỏ bốn lá', 'Dây chuyền vàng Ý cao cấp với mặt cỏ bốn lá may mắn khảm xà cừ óng ánh, tôn vinh nét duyên dáng thanh lịch.', 5500000, 4900000, 40, 'assets/images/product/daychuyen_1.jpg', 'show'),
(2, 'Dây chuyền vàng hồng mặt trăng khuyết Opal', 'Sự pha trộn lãng mạn giữa chất vàng hồng ấm áp và đá Opal ngũ sắc óng ánh kỳ ảo dưới mọi góc nhìn.', 6800000, 5990000, 25, 'assets/images/product/daychuyen_2.jpg', 'show'),
(2, 'Dây chuyền chocker xích mảnh mạ vàng sang trọng', 'Phong cách chocker ôm sát cổ tinh tế, thích hợp cho những buổi tiệc tối quyến rũ gợi cảm.', 3200000, 2800000, 50, 'assets/images/product/daychuyen_3.jpg', 'show'),
(2, 'Kiềng cổ vàng nguyên khối hoa văn cổ điển', 'Tuyệt tác chế tác thủ công từ vàng ta với họa tiết uốn lượn phượng hoàng cổ điển kiêu sa quý phái.', 45000000, 42000000, 5, 'assets/images/product/daychuyen_4.jpg', 'show'),
(2, 'Dây chuyền mặt đá Topaz xanh ngọc biển trời', 'Đá Topaz xanh dương thiên nhiên làm dịu mát tâm hồn, tỏa sáng rạng ngời lấp lánh tựa pha lê.', 8900000, 7900000, 18, 'assets/images/product/daychuyen_5.jpg', 'show'),
(3, 'Vòng tay bạc khảm đá Cubic Zirconia lấp lánh', 'Chiếc lắc tay thanh mảnh khảm đá Cz đa tầng lấp lánh, là điểm nhấn duyên dáng cho đôi tay ngọc ngà.', 1200000, 990000, 60, 'assets/images/product/vongtay_1.jpg', 'show'),
(3, 'Vòng tay lu thống vàng ngọc bích phong thủy', 'Ngọc bích thiên nhiên kết hợp lu thống vàng 24k mang lại tài lộc và sự bình an vĩnh cửu cho chủ nhân.', 12500000, 11500000, 15, 'assets/images/product/vongtay_2.jpg', 'show'),
(3, 'Lắc tay xích bản dẹt phong cách Ý thời thượng', 'Thiết kế xích bản dẹt mạ vàng hồng mạnh mẽ cá tính mang đậm dấu ấn phong cách thời trang Milan.', 4500000, 3900000, 35, 'assets/images/product/vongtay_3.jpg', 'show'),
(3, 'Vòng tay ngọc trai nước ngọt thiên nhiên quyến rũ', 'Chuỗi ngọc trai nước ngọt trắng tròn đều đính khóa bạc đính đá lấp lánh, vẻ đẹp thuần khiết Á Đông.', 5800000, 5200000, 22, 'assets/images/product/vongtay_4.jpg', 'show'),
(3, 'Lắc tay Charm may mắn đính đá nhiều màu', 'Bộ sưu tập charm phong phú từ cỏ bốn lá, chìa khóa tình yêu đến ngôi sao lấp lánh rực rỡ.', 2400000, 1990000, 45, 'assets/images/product/vongtay_5.jpg', 'show');


INSERT INTO vouchers (voucher_code, discount_type, discount_value, max_discount_amount, min_order_value, usage_limit, used_count, start_date, end_date, status) VALUES
('AURRELIA20', 'percent', 20, 100000, 300000, 50, 0, '2026-01-01 00:00:00', '2026-12-31 23:59:59', 'active'),
('SALE50K', 'fixed', 50000, NULL, 200000, 30, 0, '2026-06-01 00:00:00', '2026-06-30 23:59:59', 'active'),
('VIP15', 'percent', 15, 150000, 500000, 20, 0, '2026-01-01 00:00:00', '2026-12-31 23:59:59', 'active'),
('WELCOME10', 'percent', 10, 50000, 0, 100, 0, '2026-01-01 00:00:00', '2026-12-31 23:59:59', 'active'),
('FLASH25', 'percent', 25, 200000, 1000000, 10, 0, '2026-06-15 00:00:00', '2026-06-20 23:59:59', 'active');

INSERT INTO user_addresses (user_id, receiver_name, receiver_phone, province_city, district, ward_commune, specific_address, is_default) VALUES
(2, 'Trần Tiến Đạt', '0912345678', 'TP HCM', 'Thuận An', 'Phường An Phú', 'Số 123, ngõ 456, đường Trần Duy Hưng', 1),
(2, 'Trần Tiến Đạt', '0912345679', 'Hà Nội', 'Quận Hoàn Kiếm', 'Phường Hàng Bạc', 'Số 45, phố Hàng Bạc', 0),
(3, 'Khách Hàng Mẫu', '0987654321', 'TP. Hồ Chí Minh', 'Quận 1', 'Phường Bến Nghé', '123 Đường Lê Lợi', 1),
(3, 'Khách Hàng Mẫu', '0987654322', 'TP. Hồ Chí Minh', 'Quận 7', 'Phường Tân Phú', 'Số 78, đường Nguyễn Thị Thập', 0);


-- ==========================================
-- 5. DỮ LIỆU ĐƠN HÀNG (ORDERS)
-- ==========================================
INSERT INTO orders (user_id, order_code, receiver_name, receiver_phone, shipping_address, total_amount, discount_amount, final_amount, payment_method, status, created_at) VALUES
(2, 'ORD-20260601-001', 'Trần Tiến Đạt', '0912345678', 'Số 123, ngõ 456, đường Trần Duy Hưng, Phường Dịch Vọng, Quận Cầu Giấy, Hà Nội', 990000, 99000, 891000, 'cod', 'delivered', '2026-06-01 10:30:00'),
(2, 'ORD-20260605-002', 'Trần Tiến Đạt', '0912345678', 'Số 123, ngõ 456, đường Trần Duy Hưng, Phường Dịch Vọng, Quận Cầu Giấy, Hà Nội', 450000, 0, 450000, 'bank_transfer', 'shipping', '2026-06-05 14:20:00'),
(3, 'ORD-20260610-003', 'Khách Hàng Mẫu', '0987654321', '123 Đường Lê Lợi, Phường Bến Nghé, Quận 1, TP. Hồ Chí Minh', 780000, 78000, 702000, 'cod', 'processing', '2026-06-10 09:15:00'),
(3, 'ORD-20260615-004', 'Khách Hàng Mẫu', '0987654321', '123 Đường Lê Lợi, Phường Bến Nghé, Quận 1, TP. Hồ Chí Minh', 250000, 0, 250000, 'cod', 'pending', '2026-06-15 16:45:00'),
(2, 'ORD-20260620-005', 'Trần Tiến Đạt', '0912345678', 'Số 45, phố Hàng Bạc, Phường Hàng Bạc, Quận Hoàn Kiếm, Hà Nội', 1370000, 200000, 1170000, 'bank_transfer', 'pending', '2026-06-20 11:00:00');


-- ==========================================
-- 6. DỮ LIỆU CHI TIẾT ĐƠN HÀNG (ORDER_DETAILS)
-- ==========================================
INSERT INTO order_details (order_id, product_id, quantity, price, selected_size, selected_color, selected_material) VALUES
-- Đơn hàng 1 (đã giao)
(1, 1, 1, 450000, 'One Size', 'Bạc', 'Bạc 925'),
(1, 2, 2, 250000, 'One Size', 'Trắng', 'Hợp kim mạ bạc'),

-- Đơn hàng 2 (đang giao)
(2, 8, 1, 320000, 'One Size', 'Bạc', 'Bạc 925'),

-- Đơn hàng 3 (đang xử lý)
(3, 10, 1, 780000, 'One Size', 'Bạc', 'Hợp kim mạ bạc'),

-- Đơn hàng 4 (chờ xác nhận)
(4, 2, 1, 250000, 'One Size', 'Trắng', 'Hợp kim'),

-- Đơn hàng 5 (chờ xác nhận)
(5, 16, 1, 340000, 'One Size', 'Bạc', 'Hợp kim mạ bạc'),
(5, 25, 1, 360000, 'One Size', 'Vàng hồng', 'Hợp kim mạ vàng'),
(5, 30, 1, 950000, 'One Size', 'Đen', 'Bạc 925');


-- ==========================================
-- 7. DỮ LIỆU GIỎ HÀNG (CART_ITEMS)
-- ==========================================
INSERT INTO cart_items (user_id, product_id, quantity, selected_size, selected_color, selected_material) VALUES
-- Giỏ hàng của user 2 (Trần Tiến Đạt)
(2, 4, 1, 'One Size', 'Bạc', 'Hợp kim mạ bạc'),
(2, 7, 2, 'One Size', 'Bạc', 'Hợp kim mạ bạc'),
(2, 15, 1, 'One Size', 'Trắng', 'Bạc 925'),

-- Giỏ hàng của user 3 (Khách Hàng Mẫu)
(3, 6, 1, 'One Size', 'Vàng hồng', 'Hợp kim mạ vàng'),
(3, 12, 1, 'One Size', 'Trắng', 'Hợp kim'),
(3, 20, 1, 'One Size', 'Bạc', 'Hợp kim mạ bạc');


-- ==========================================
-- 8. DỮ LIỆU ĐÁNH GIÁ SẢN PHẨM (REVIEWS)
-- ==========================================
INSERT INTO reviews (user_id, product_id, rating, comment, status, created_at) VALUES
(2, 1, 5, 'Bông tai rất đẹp, chất lượng tốt, đeo lên nhẹ và sang trọng. Giao hàng nhanh!', 'approved', '2026-06-05 15:30:00'),
(2, 2, 4, 'Sản phẩm đẹp nhưng giá hơi cao so với chất lượng. Tuy nhiên vẫn ưng lắm!', 'approved', '2026-06-08 10:20:00'),
(3, 10, 5, 'Khuyên tai sang trọng lộng lẫy, đúng như hình. Sẽ ủng hộ shop tiếp!', 'approved', '2026-06-12 08:45:00'),
(3, 8, 4, 'Mẫu mã đẹp, màu sắc chuẩn. Đeo thoải mái, không bị dị ứng.', 'pending', '2026-06-16 14:00:00');


-- 9. DỮ LIỆU BANNER
-- ==========================================
INSERT INTO banners (title, image_url, target_link, display_order, status) VALUES
('Bộ sưu tập Bông tai mới', 'assets/images/banner/banner-1.jpg', '/products?category=4', 1, 'show'),
('Sale hè lên đến 50%', 'assets/images/banner/banner-2.jpg', '/vouchers', 2, 'show'),
('Nhẫn cưới Aurrelia', 'assets/images/banner/banner-3.jpg', '/products?category=1', 3, 'show'),
('Phụ kiện cao cấp', 'assets/images/banner/banner-4.jpg', '/products', 4, 'show');


-- ==========================================
-- 10. DỮ LIỆU FAQ
-- ==========================================
INSERT INTO faqs (question, answer, display_order, status) VALUES
('Trang sức Aurrelia có đảm bảo chất lượng không?', 'Aurrelia cam kết tất cả sản phẩm đều được làm từ chất liệu cao cấp, có chứng nhận kiểm định và bảo hành chính hãng 12 tháng.', 1, 'show'),
('Tôi có thể đổi trả sản phẩm trong bao lâu?', 'Quý khách có thể đổi trả sản phẩm trong vòng 7 ngày kể từ ngày nhận hàng với điều kiện sản phẩm còn nguyên tem mác và chưa qua sử dụng.', 2, 'show'),
('Aurrelia có giao hàng toàn quốc không?', 'Aurrelia giao hàng toàn quốc với đơn vị vận chuyển uy tín. Phí ship sẽ được tính dựa trên khoảng cách và trọng lượng đơn hàng.', 3, 'show'),
('Làm thế nào để biết size trang sức phù hợp?', 'Bạn có thể tham khảo bảng size chi tiết trên từng sản phẩm hoặc liên hệ hotline để được tư vấn trực tiếp.', 4, 'show');


-- ==========================================
-- 11. DỮ LIỆU LIÊN HỆ (CONTACTS)
-- ==========================================
INSERT INTO contacts (customer_name, customer_email, message, is_read, created_at) VALUES
('Nguyễn Thị Hoa', 'hoa.nguyen@gmail.com', 'Cho tôi hỏi sản phẩm bông tai mã VT001 còn hàng không ạ? Tôi muốn mua làm quà tặng.', 1, '2026-06-02 08:30:00'),
('Lê Văn Minh', 'minh.le@gmail.com', 'Shop có nhận đặt làm trang sức theo yêu cầu không? Tôi muốn đặt 1 cặp nhẫn cưới theo thiết kế riêng.', 0, '2026-06-07 11:20:00'),
('Phạm Thanh Tú', 'tu.pham@gmail.com', 'Tôi đã đặt hàng đơn mã ORD-20260605-002 nhưng chưa thấy cập nhật trạng thái. Mong shop hỗ trợ sớm.', 0, '2026-06-09 16:15:00');


-- Thêm ảnh phụ cho sản phẩm #38 (Bông tai hoa tuyết)
INSERT INTO product_images (product_id, image_url) VALUES
(38, 'assets/images/product/image_2076968380_4.jpg'),
(38, 'assets/images/product/image_2076968380_5.jpg'),
(39, 'assets/images/product/image_2076968380_1.jpg'),
(39, 'assets/images/product/image_2076968380_2.jpg'),
(39, 'assets/images/product/image_2076968380_3.jpg');


-- =============================================================================
-- 12. VIEWS, PROCEDURES, FUNCTIONS & TRIGGERS (ĐẢM BẢO TOÀN VẸN DỮ LIỆU)
-- =============================================================================

DROP VIEW IF EXISTS v_product_details;
DROP VIEW IF EXISTS v_user_profiles;
DROP TRIGGER IF EXISTS trg_update_stock_after_order;
DROP TRIGGER IF EXISTS trg_check_stock_before_order;
DROP PROCEDURE IF EXISTS sp_get_monthly_revenue;
DROP FUNCTION IF EXISTS fn_get_total_spent;

-- [VIEW 1]: Chi tiết sản phẩm kết hợp tên danh mục
CREATE VIEW v_product_details AS
SELECT p.*, c.category_name
FROM products p
LEFT JOIN categories c ON p.category_id = c.category_id;

-- [VIEW 2]: Thông tin khách hàng kèm địa chỉ giao hàng mặc định
CREATE VIEW v_user_profiles AS
SELECT u.user_id, u.full_name, u.email, u.phone,
       ua.receiver_name, ua.receiver_phone, ua.province_city, ua.district, ua.ward_commune, ua.specific_address
FROM users u
LEFT JOIN user_addresses ua ON u.user_id = ua.user_id AND ua.is_default = 1;

-- [TRIGGER 1]: Kiểm tra số lượng sản phẩm tồn kho trước khi đặt hàng
DELIMITER //
CREATE TRIGGER trg_check_stock_before_order
BEFORE INSERT ON order_details
FOR EACH ROW
BEGIN
    DECLARE current_stock INT;
    SELECT stock_quantity INTO current_stock FROM products WHERE product_id = NEW.product_id;
    IF current_stock < NEW.quantity THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Số lượng sản phẩm trong kho không đủ để đáp ứng đơn hàng!';
    END IF;
END //
DELIMITER ;

-- [TRIGGER 2]: Tự động trừ số lượng sản phẩm tồn kho sau khi thêm chi tiết đơn hàng
DELIMITER //
CREATE TRIGGER trg_update_stock_after_order
AFTER INSERT ON order_details
FOR EACH ROW
BEGIN
    UPDATE products
    SET stock_quantity = stock_quantity - NEW.quantity
    WHERE product_id = NEW.product_id;
END //
DELIMITER ;

-- [PROCEDURE]: Báo cáo doanh thu hàng tháng từ các đơn hàng thành công
DELIMITER //
CREATE PROCEDURE sp_get_monthly_revenue()
BEGIN
    SELECT DATE_FORMAT(created_at, '%Y-%m') AS month, SUM(final_amount) AS revenue
    FROM orders
    WHERE status != 'cancelled'
    GROUP BY DATE_FORMAT(created_at, '%Y-%m')
    ORDER BY month ASC;
END //
DELIMITER ;

-- [FUNCTION]: Tính tổng số tiền một người dùng đã thanh toán hoàn thành
DELIMITER //
CREATE FUNCTION fn_get_total_spent(p_user_id INT)
RETURNS INT
DETERMINISTIC
BEGIN
    DECLARE total INT DEFAULT 0;
    SELECT COALESCE(SUM(final_amount), 0) INTO total
    FROM orders
    WHERE user_id = p_user_id AND status = 'delivered';
    RETURN total;
END //
DELIMITER ;
