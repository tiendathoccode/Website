Bảng users sẽ lưu trữ thông tin cơ bản của tài khoản.
    Mã người dùng (user_id): Khóa chính (Primary Key), tự động tăng để phân biệt từng người.

    Thông tin cá nhân: Tên, email, số điện thoại, ảnh đại diện.

    Bảo mật: Mật khẩu (sẽ được mã hóa trước khi lưu).

    Phân quyền (role): Phân biệt admin và customer.

    Trạng thái (status): Để admin có thể khóa (locked) hoặc mở khóa (active) tài khoản.

    Thời gian: Lưu lại ngày đăng ký.

một người dùng có thể có nhiều địa chỉ", chúng ta sẽ áp dụng mối quan hệ 1 - Nhiều (One-to-Many)
    address_id: Khóa chính, tự động tăng.

    user_id: Khóa ngoại để biết địa chỉ này thuộc về tài khoản nào. Nếu tài khoản bị xóa, các địa chỉ liên quan cũng sẽ tự động xóa theo (ON DELETE CASCADE).

    receiver_name & receiver_phone: Tên và số điện thoại người nhận (đôi khi người mua muốn gửi cho người khác, không nhất thiết phải lấy tên/SĐT của chủ tài khoản).

    Cấu trúc địa chỉ chi tiết: Chia rõ thành Tỉnh/Thành phố, Quận/Huyện, Phường/Xã và Địa chỉ cụ thể (số nhà, tên đường) để sau này dễ tích hợp các đơn vị vận chuyển (Giao Hàng Tiết Kiệm, GHN, v.v.).

    is_default: Đánh dấu địa chỉ mặc định (Sử dụng kiểu TINYINT với 1 là mặc định, 0 là bình thường).

Admin cần có quyền CRUD (Thêm, Sửa, Xóa) danh mục và quản lý trạng thái hiển thị (Hiện/Ẩn). Bảng categories sẽ có các trường thông tin sau:
    category_id: Khóa chính, tự động tăng để định danh từng danh mục.

    category_name: Tên danh mục (ví dụ: "Nhẫn Kim Cương", "Dây Chuyền Bạc").

    description: Mô tả ngắn về danh mục (không bắt buộc).

    status: Trạng thái Hiện (show) hoặc Ẩn (hide) danh mục trên giao diện người dùng.

sản phẩm có rất nhiều thông tin phức tạp như: ảnh đại diện, nhiều ảnh phụ, size, màu sắc, chất liệu, v.v.

Để tối ưu cơ sở dữ liệu, chúng ta không thể nhét tất cả vào một bảng. Thay vào đó, bảng products sẽ đóng vai trò là "bảng gốc" lưu trữ các thông tin chung nhất. Các thông tin như "nhiều ảnh" hay "size/màu sắc" sẽ được tách ra các bảng phụ riêng biệt mà chúng ta sẽ tạo sau.
    product_id: Khóa chính, tự động tăng.

    category_id: Khóa ngoại liên kết với bảng categories. Để biết sản phẩm này thuộc danh mục nào.

    product_name & slug: Tên sản phẩm và đường dẫn chuẩn SEO (giống như danh mục).

    description: Mô tả chi tiết sản phẩm.

    price & sale_price: Giá bán gốc và giá khuyến mãi. (Sử dụng kiểu INT vì tiền Việt Nam Đồng không có số thập phân).

    stock_quantity: Số lượng tồn kho. (Giúp admin biết khi nào sản phẩm sắp hết hàng < 10).

    main_image: Đường dẫn tới ảnh đại diện chính của sản phẩm.

    status: Trạng thái Hiện (show) hoặc Ẩn (hide).
Lưu ý: Xử lý tồn kho (Backend): Khi làm chức năng "Quản lý đơn hàng",  nhớ viết logic trừ đi stock_quantity trong bảng này mỗi khi khách đặt hàng thành công.

Cảnh báo hết hàng: Khi truy xuất danh sách sản phẩm ở trang Admin, chỉ cần dùng lệnh điều kiện trong PHP: if ($product['stock_quantity'] < 10) thì thêm một đoạn mã CSS hoặc một nhãn (badge) màu đỏ như Sắp hết hàng vào giao diện.

Bảng product_images: Lưu trữ các hình ảnh phụ của sản phẩm (ảnh chính đã lưu ở cột main_image trong bảng products rồi).

Bảng product_attributes: Lưu trữ thông tin về size, màu sắc, và chất liệu. Tôi dùng cột attribute_type để phân loại xem nó là size, màu hay chất liệu.

Logic hiển thị Frontend (Giao diện sản phẩm): Khi người dùng vào xem trang chi tiết sản phẩm, Frontend sẽ gửi product_id lên Backend. Backend sẽ viết 3 câu query:

    Lấy thông tin gốc từ bảng products.

    Lấy danh sách ảnh từ product_images.

    Lấy danh sách thuộc tính từ product_attributes.

Thao tác thêm sản phẩm (Admin): Khi Admin bấm nút "Thêm sản phẩm", Backend sẽ INSERT dữ liệu vào bảng products trước để lấy được cái product_id vừa tạo (trong PHP PDO dùng hàm lastInsertId()). Sau đó, dùng product_id này để tiếp tục INSERT vào 2 bảng product_images và product_attributes.

orders (Bảng thông tin chung của đơn hàng): Lưu tổng tiền, trạng thái, người đặt, và địa chỉ giao hàng.

order_details (Bảng chi tiết đơn hàng): Lưu danh sách các sản phẩm có bên trong đơn hàng đó (Mua cái gì? Số lượng bao nhiêu? Giá lúc mua là bao nhiêu?).

Dành cho Backend (PDO Transactions): Khi thực hiện nút "Xác nhận và đặt hàng",  phải làm rất nhiều việc cùng lúc:

    Thêm vào bảng orders.

    Thêm vào bảng order_details.

    Trừ số lượng trong bảng products.

    Xóa sản phẩm khỏi giỏ hàng.
    Lưu ý cực kỳ quan trọng: Nếu trong 4 bước này có 1 bước bị lỗi mạng giữa chừng, dữ liệu sẽ bị hỏng. Backend phải học cách dùng PDO Transaction (beginTransaction(), commit(), rollBack()) để đảm bảo cả 4 bước đều thành công, hoặc nếu lỗi thì hủy tất cả.

Dành cho tính năng Hủy đơn: Khi cập nhật cột status thành 'cancelled', Backend phải viết thêm một lệnh UPDATE để cộng lại quantity vào stock_quantity của bảng products (Hoàn lại tồn kho tự động như tài liệu yêu cầu).

Hướng dẫn triển khai giỏ hàng
ogic "Thêm vào giỏ hàng" (Backend): Khi khách bấm nút thêm sản phẩm, đừng vội dùng lệnh INSERT ngay. Hãy dùng lệnh SELECT để kiểm tra xem: Sản phẩm này với đúng Size/Màu này đã có trong giỏ hàng của user đó chưa?

    Nếu ĐÃ CÓ:  chỉ cần dùng lệnh UPDATE để tăng quantity = quantity + 1.

    Nếu CHƯA CÓ:  dùng lệnh INSERT để tạo dòng mới.

Tính tổng tiền tự động (Frontend/Backend): Khi load trang Giỏ hàng, viết câu lệnh SQL dùng JOIN (kết nối bảng cart_items và bảng products) để lấy ra số lượng và giá hiện tại, rồi nhân chúng lên để tính ra tổng tiền.

Lúc đặt hàng xong: Nhớ dùng lệnh DELETE FROM cart_items WHERE user_id = ... để làm trống giỏ hàng sau khi hóa đơn đã được tạo thành công bên bảng orders

Bảng vouchers sẽ cần giải quyết các bài toán sau:

    Mã code: Chuỗi ký tự khách hàng sẽ nhập (Ví dụ: MUAHE50K, FREESHIP).

    Loại giảm giá: Có 2 loại cơ bản là giảm theo phần trăm (Ví dụ: giảm 10%) hoặc giảm thẳng bằng tiền mặt (Ví dụ: giảm 50.000đ).

    Điều kiện áp dụng: Đơn hàng tối thiểu bao nhiêu tiền thì mới được dùng mã này? (Ví dụ: Đơn từ 200k).

    Giới hạn số lượng: Admin tung ra bao nhiêu mã? Đã có bao nhiêu người dùng rồi? (Để tránh tình trạng 1 mã bị xài đi xài lại hàng ngàn lần).

    Thời hạn: Ngày bắt đầu và ngày kết thúc của chương trình khuyến mãi.


Tính năng áp dụng mã giảm giá là một bài test tư duy logic rất hay cho Backend. Dưới đây là các bước kiểm tra (Validate) mà nhóm cần viết bằng PHP khi khách hàng nhấn nút "Áp dụng":

    Kiểm tra tồn tại: Lệnh SELECT * FROM vouchers WHERE voucher_code = ... xem mã khách nhập có tồn tại trong CSDL không.

    Kiểm tra trạng thái & Thời gian: Mã có đang active không? Thời gian hiện tại (date('Y-m-d H:i:s') trong PHP) có nằm giữa start_date và end_date không?

    Kiểm tra lượt dùng: Số used_count đã bằng hoặc vượt quá usage_limit chưa? (Nếu rồi báo lỗi "Mã này đã hết lượt sử dụng").

    Kiểm tra đơn tối thiểu: Tổng tiền giỏ hàng hiện tại của khách có lớn hơn hoặc bằng min_order_value không?

    Tính toán số tiền được giảm: * Nếu discount_type == 'fixed': Tiền giảm = discount_value.

        Nếu discount_type == 'percent': Tiền giảm = (Tổng đơn * discount_value) / 100. (Lưu ý phải dùng hàm min() để so sánh và lấy giá trị nhỏ hơn giữa Tiền giảm vừa tính và max_discount_amount).

    Cập nhật lượt dùng: Sau khi khách hàng đặt đơn thành công (chức năng Checkout), bắt buộc phải có câu lệnh UPDATE vouchers SET used_count = used_count + 1 để trừ đi 1 lượt dùng của mã.

Bảng reviews sẽ là cầu nối giữa người dùng (user_id) và sản phẩm (product_id). Để đáp ứng đúng các yêu cầu nghiệp vụ của nhóm, bảng này cần:

    rating (Điểm số): Từ 1 đến 5 sao.

    comment (Nội dung đánh giá): Khách hàng viết cảm nhận về sản phẩm.

    status (Trạng thái kiểm duyệt): Theo mặc định khi khách vừa đăng xong, đánh giá có thể ở trạng thái chờ duyệt (pending). Admin vào xem, nếu thấy ngôn từ hợp lệ thì chuyển sang đã duyệt (approved) để hiển thị lên web, hoặc nếu spam thì chuyển sang ẩn (hidden).

Kiểm tra "Đã mua hàng" (Backend): Theo tài liệu, khách hàng chỉ được đánh giá sau khi đơn hàng hoàn thành. Vậy trước khi cho phép họ hiện form nhập đánh giá ở Frontend, Backend phải viết một câu query kiểm tra xem: User này đã từng có order_id nào chứa product_id này với trạng thái delivered (đã giao) hay chưa? Nếu chưa thì báo lỗi "cần mua sản phẩm này để đánh giá"

Hiển thị số sao trung bình (Frontend/Backend): Khi ở trang chi tiết sản phẩm, có thể dùng lệnh SQL SELECT AVG(rating) FROM reviews WHERE product_id = ... AND status = 'approved' để tính ra số sao trung bình của sản phẩm đó (ví dụ: 4.8 sao) và hiển thị lên giao diện.

Quyền Admin: Giao diện quản lý của Admin chỉ cần làm các nút đổi trạng thái (UPDATE status) hoặc nút xóa luôn (DELETE), tuyệt đối không làm form sửa nội dung comment

hướng dẫn triển khai banner faqs, contact
Mẹo sắp xếp (Backend): Khi dùng lệnh SELECT để lấy dữ liệu banners hoặc faqs ra trang chủ, hãy nhớ thêm đoạn ORDER BY display_order ASC. Câu lệnh này giúp các mục được sắp xếp đúng theo ý đồ của Admin (mục có số thứ tự nhỏ hơn sẽ xếp lên trên).

Trạng thái tin nhắn: Khi Admin nhấp vào xem chi tiết một tin nhắn liên hệ, Backend hãy tự động chạy lệnh UPDATE contacts SET is_read = 1 WHERE contact_id = ... để đổi trạng thái thành "Đã đọc", Admin không cần tự tay bấm chuyển trạng thái nữa.