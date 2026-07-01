-- Test seed data for local development only.
-- Run this after importing database/database.sql.
-- Login admin:
--   email: admin@example.com
--   password: admin123456

INSERT INTO users (full_name, email, phone, password, role, status)
VALUES
    (
        'Admin Test',
        'admin@example.com',
        '0900000000',
        '$2y$10$5jcCaUc/zbkbdFbZeNZa8uPLPo7TuApwgBjOGzdoggPlRl8qGCE.q',
        'admin',
        'active'
    )
ON DUPLICATE KEY UPDATE
    full_name = VALUES(full_name),
    phone = VALUES(phone),
    role = 'admin',
    status = 'active';

INSERT INTO categories (category_name, description, status)
VALUES
    ('Rings', 'Test category for rings', 'show'),
    ('Necklaces', 'Test category for necklaces', 'show'),
    ('Earrings', 'Test category for earrings', 'show')
ON DUPLICATE KEY UPDATE
    description = VALUES(description),
    status = VALUES(status);

INSERT INTO products
    (category_id, product_name, sku, description, price, sale_price, stock_quantity, main_image, status)
SELECT
    c.category_id,
    'Aurelia Test Ring',
    'TEST-RING-001',
    'Sample product for testing admin product list and edit form.',
    1200000,
    990000,
    8,
    '/assets/images/sp1.png',
    'show'
FROM categories c
WHERE c.category_name = 'Rings'
ON DUPLICATE KEY UPDATE
    category_id = VALUES(category_id),
    product_name = VALUES(product_name),
    description = VALUES(description),
    price = VALUES(price),
    sale_price = VALUES(sale_price),
    stock_quantity = VALUES(stock_quantity),
    main_image = VALUES(main_image),
    status = VALUES(status);

INSERT INTO products
    (category_id, product_name, sku, description, price, sale_price, stock_quantity, main_image, status)
SELECT
    c.category_id,
    'Aurelia Test Necklace',
    'TEST-NECK-001',
    'Sample necklace product for testing filters.',
    1800000,
    0,
    2,
    '/assets/images/sp1.png',
    'show'
FROM categories c
WHERE c.category_name = 'Necklaces'
ON DUPLICATE KEY UPDATE
    category_id = VALUES(category_id),
    product_name = VALUES(product_name),
    description = VALUES(description),
    price = VALUES(price),
    sale_price = VALUES(sale_price),
    stock_quantity = VALUES(stock_quantity),
    main_image = VALUES(main_image),
    status = VALUES(status);

DELETE pa
FROM product_attributes pa
INNER JOIN products p ON p.product_id = pa.product_id
WHERE p.sku IN ('TEST-RING-001', 'TEST-NECK-001');

INSERT INTO product_attributes (product_id, attribute_type, attribute_value)
SELECT product_id, 'material', 'Gold 18K'
FROM products
WHERE sku = 'TEST-RING-001';

INSERT INTO product_attributes (product_id, attribute_type, attribute_value)
SELECT product_id, 'material', 'Silver'
FROM products
WHERE sku = 'TEST-NECK-001';

DELETE pi
FROM product_images pi
INNER JOIN products p ON p.product_id = pi.product_id
WHERE p.sku IN ('TEST-RING-001', 'TEST-NECK-001');

INSERT INTO product_images (product_id, image_url)
SELECT product_id, main_image
FROM products
WHERE sku IN ('TEST-RING-001', 'TEST-NECK-001');

-- Seed users (customers) for orders
INSERT INTO users (full_name, email, phone, password, role, status)
VALUES
    (
        'Evelyn Thorne',
        'evelyn@example.com',
        '0911222333',
        '$2y$10$5jcCaUc/zbkbdFbZeNZa8uPLPo7TuApwgBjOGzdoggPlRl8qGCE.q',
        'customer',
        'active'
    ),
    (
        'Liam Sterling',
        'liam@sterling.com',
        '0988777666',
        '$2y$10$5jcCaUc/zbkbdFbZeNZa8uPLPo7TuApwgBjOGzdoggPlRl8qGCE.q',
        'customer',
        'active'
    )
ON DUPLICATE KEY UPDATE
    full_name = VALUES(full_name),
    phone = VALUES(phone),
    role = 'customer',
    status = 'active';

-- Seed orders
INSERT INTO orders (user_id, order_code, receiver_name, receiver_phone, shipping_address, total_amount, discount_amount, final_amount, payment_method, status)
SELECT 
    u.user_id, 
    'AUR-1024', 
    'Evelyn Thorne', 
    '0911222333', 
    '123 Luxury Road, District 1, HCMC', 
    4250000, 
    250000, 
    4000000, 
    'bank_transfer', 
    'delivered'
FROM users u WHERE u.email = 'evelyn@example.com'
ON DUPLICATE KEY UPDATE order_code = order_code;

INSERT INTO orders (user_id, order_code, receiver_name, receiver_phone, shipping_address, total_amount, discount_amount, final_amount, payment_method, status)
SELECT 
    u.user_id, 
    'AUR-1025', 
    'Liam Sterling', 
    '0988777666', 
    '456 Gold Street, District 3, HCMC', 
    8900000, 
    0, 
    8900000, 
    'cod', 
    'pending'
FROM users u WHERE u.email = 'liam@sterling.com'
ON DUPLICATE KEY UPDATE order_code = order_code;

-- Seed order_details
DELETE FROM order_details WHERE order_id IN (SELECT order_id FROM orders WHERE order_code IN ('AUR-1024', 'AUR-1025'));

INSERT INTO order_details (order_id, product_id, quantity, price, selected_size, selected_color, selected_material)
SELECT 
    o.order_id,
    p.product_id,
    2,
    1200000,
    'Size 10',
    'Gold',
    'Gold 18K'
FROM orders o, products p
WHERE o.order_code = 'AUR-1024' AND p.sku = 'TEST-RING-001';

INSERT INTO order_details (order_id, product_id, quantity, price, selected_size, selected_color, selected_material)
SELECT 
    o.order_id,
    p.product_id,
    1,
    1800000,
    'Standard',
    'Silver',
    'Silver'
FROM orders o, products p
WHERE o.order_code = 'AUR-1025' AND p.sku = 'TEST-NECK-001';

-- Extra dashboard sample data across months
INSERT INTO users (full_name, email, phone, password, role, status, created_at)
VALUES
    ('Sophia Laurent', 'sophia@example.com', '0901111222', '$2y$10$5jcCaUc/zbkbdFbZeNZa8uPLPo7TuApwgBjOGzdoggPlRl8qGCE.q', 'customer', 'active', '2026-02-12 09:10:00'),
    ('Mia Hart', 'mia@example.com', '0902222333', '$2y$10$5jcCaUc/zbkbdFbZeNZa8uPLPo7TuApwgBjOGzdoggPlRl8qGCE.q', 'customer', 'active', '2026-03-18 14:25:00'),
    ('Noah King', 'noah@example.com', '0903333444', '$2y$10$5jcCaUc/zbkbdFbZeNZa8uPLPo7TuApwgBjOGzdoggPlRl8qGCE.q', 'customer', 'active', '2026-04-08 11:40:00'),
    ('Ava Stone', 'ava@example.com', '0904444555', '$2y$10$5jcCaUc/zbkbdFbZeNZa8uPLPo7TuApwgBjOGzdoggPlRl8qGCE.q', 'customer', 'active', '2026-05-21 16:05:00')
ON DUPLICATE KEY UPDATE
    full_name = VALUES(full_name),
    phone = VALUES(phone),
    role = 'customer',
    status = 'active',
    created_at = VALUES(created_at);

INSERT INTO orders
    (user_id, order_code, receiver_name, receiver_phone, shipping_address, total_amount, discount_amount, final_amount, payment_method, status, created_at)
SELECT u.user_id, 'AUR-1026', 'Sophia Laurent', '0901111222', '15 Pearl Avenue, District 1, HCMC', 2500000, 0, 2500000, 'bank_transfer', 'delivered', '2026-02-15 10:20:00'
FROM users u WHERE u.email = 'sophia@example.com'
ON DUPLICATE KEY UPDATE
    total_amount = VALUES(total_amount),
    discount_amount = VALUES(discount_amount),
    final_amount = VALUES(final_amount),
    payment_method = VALUES(payment_method),
    status = VALUES(status),
    created_at = VALUES(created_at);

INSERT INTO orders
    (user_id, order_code, receiver_name, receiver_phone, shipping_address, total_amount, discount_amount, final_amount, payment_method, status, created_at)
SELECT u.user_id, 'AUR-1027', 'Mia Hart', '0902222333', '82 Diamond Lane, District 2, HCMC', 3600000, 200000, 3400000, 'cod', 'delivered', '2026-03-12 13:45:00'
FROM users u WHERE u.email = 'mia@example.com'
ON DUPLICATE KEY UPDATE
    total_amount = VALUES(total_amount),
    discount_amount = VALUES(discount_amount),
    final_amount = VALUES(final_amount),
    payment_method = VALUES(payment_method),
    status = VALUES(status),
    created_at = VALUES(created_at);

INSERT INTO orders
    (user_id, order_code, receiver_name, receiver_phone, shipping_address, total_amount, discount_amount, final_amount, payment_method, status, created_at)
SELECT u.user_id, 'AUR-1028', 'Noah King', '0903333444', '19 Gold Garden, District 7, HCMC', 5200000, 0, 5200000, 'bank_transfer', 'delivered', '2026-04-20 17:30:00'
FROM users u WHERE u.email = 'noah@example.com'
ON DUPLICATE KEY UPDATE
    total_amount = VALUES(total_amount),
    discount_amount = VALUES(discount_amount),
    final_amount = VALUES(final_amount),
    payment_method = VALUES(payment_method),
    status = VALUES(status),
    created_at = VALUES(created_at);

INSERT INTO orders
    (user_id, order_code, receiver_name, receiver_phone, shipping_address, total_amount, discount_amount, final_amount, payment_method, status, created_at)
SELECT u.user_id, 'AUR-1029', 'Ava Stone', '0904444555', '44 Ruby Street, District 3, HCMC', 4300000, 300000, 4000000, 'cod', 'delivered', '2026-05-11 09:55:00'
FROM users u WHERE u.email = 'ava@example.com'
ON DUPLICATE KEY UPDATE
    total_amount = VALUES(total_amount),
    discount_amount = VALUES(discount_amount),
    final_amount = VALUES(final_amount),
    payment_method = VALUES(payment_method),
    status = VALUES(status),
    created_at = VALUES(created_at);

INSERT INTO orders
    (user_id, order_code, receiver_name, receiver_phone, shipping_address, total_amount, discount_amount, final_amount, payment_method, status, created_at)
SELECT u.user_id, 'AUR-1030', 'Evelyn Thorne', '0911222333', '123 Luxury Road, District 1, HCMC', 6800000, 500000, 6300000, 'bank_transfer', 'delivered', '2026-06-24 18:10:00'
FROM users u WHERE u.email = 'evelyn@example.com'
ON DUPLICATE KEY UPDATE
    total_amount = VALUES(total_amount),
    discount_amount = VALUES(discount_amount),
    final_amount = VALUES(final_amount),
    payment_method = VALUES(payment_method),
    status = VALUES(status),
    created_at = VALUES(created_at);

DELETE FROM order_details WHERE order_id IN (
    SELECT order_id FROM orders WHERE order_code IN ('AUR-1026', 'AUR-1027', 'AUR-1028', 'AUR-1029', 'AUR-1030')
);

INSERT INTO order_details (order_id, product_id, quantity, price, selected_size, selected_color, selected_material)
SELECT o.order_id, p.product_id, 1, 1200000, 'Size 8', 'Gold', 'Gold 18K'
FROM orders o, products p
WHERE o.order_code IN ('AUR-1026', 'AUR-1028', 'AUR-1030') AND p.sku = 'TEST-RING-001';

INSERT INTO order_details (order_id, product_id, quantity, price, selected_size, selected_color, selected_material)
SELECT o.order_id, p.product_id, 1, 1800000, 'Standard', 'Silver', 'Silver'
FROM orders o, products p
WHERE o.order_code IN ('AUR-1027', 'AUR-1029', 'AUR-1030') AND p.sku = 'TEST-NECK-001';

-- Current month dashboard samples
INSERT INTO users (full_name, email, phone, password, role, status, created_at)
VALUES
    ('Isabella Reed', 'isabella@example.com', '0905555666', '$2y$10$5jcCaUc/zbkbdFbZeNZa8uPLPo7TuApwgBjOGzdoggPlRl8qGCE.q', 'customer', 'active', '2026-07-01 08:30:00'),
    ('Lucas Grey', 'lucas@example.com', '0906666777', '$2y$10$5jcCaUc/zbkbdFbZeNZa8uPLPo7TuApwgBjOGzdoggPlRl8qGCE.q', 'customer', 'active', '2026-07-01 10:15:00')
ON DUPLICATE KEY UPDATE
    full_name = VALUES(full_name),
    phone = VALUES(phone),
    role = 'customer',
    status = 'active',
    created_at = VALUES(created_at);

INSERT INTO orders
    (user_id, order_code, receiver_name, receiver_phone, shipping_address, total_amount, discount_amount, final_amount, payment_method, status, created_at)
SELECT u.user_id, 'AUR-1031', 'Isabella Reed', '0905555666', '7 Sapphire Plaza, District 1, HCMC', 7800000, 300000, 7500000, 'bank_transfer', 'delivered', '2026-07-01 09:25:00'
FROM users u WHERE u.email = 'isabella@example.com'
ON DUPLICATE KEY UPDATE
    total_amount = VALUES(total_amount),
    discount_amount = VALUES(discount_amount),
    final_amount = VALUES(final_amount),
    payment_method = VALUES(payment_method),
    status = VALUES(status),
    created_at = VALUES(created_at);

INSERT INTO orders
    (user_id, order_code, receiver_name, receiver_phone, shipping_address, total_amount, discount_amount, final_amount, payment_method, status, created_at)
SELECT u.user_id, 'AUR-1032', 'Lucas Grey', '0906666777', '91 Opal Road, District 4, HCMC', 2900000, 0, 2900000, 'cod', 'processing', '2026-07-01 11:05:00'
FROM users u WHERE u.email = 'lucas@example.com'
ON DUPLICATE KEY UPDATE
    total_amount = VALUES(total_amount),
    discount_amount = VALUES(discount_amount),
    final_amount = VALUES(final_amount),
    payment_method = VALUES(payment_method),
    status = VALUES(status),
    created_at = VALUES(created_at);

DELETE FROM order_details WHERE order_id IN (
    SELECT order_id FROM orders WHERE order_code IN ('AUR-1031', 'AUR-1032')
);

INSERT INTO order_details (order_id, product_id, quantity, price, selected_size, selected_color, selected_material)
SELECT o.order_id, p.product_id, 2, 1200000, 'Size 9', 'Gold', 'Gold 18K'
FROM orders o, products p
WHERE o.order_code = 'AUR-1031' AND p.sku = 'TEST-RING-001';

INSERT INTO order_details (order_id, product_id, quantity, price, selected_size, selected_color, selected_material)
SELECT o.order_id, p.product_id, 1, 1800000, 'Standard', 'Silver', 'Silver'
FROM orders o, products p
WHERE o.order_code IN ('AUR-1031', 'AUR-1032') AND p.sku = 'TEST-NECK-001';

INSERT INTO users (full_name, email, phone, password, role, status, created_at)
VALUES
    ('Emma Vale', 'emma@example.com', '0907777888', '$2y$10$5jcCaUc/zbkbdFbZeNZa8uPLPo7TuApwgBjOGzdoggPlRl8qGCE.q', 'customer', 'active', '2026-07-01 13:20:00'),
    ('Henry Bloom', 'henry@example.com', '0908888999', '$2y$10$5jcCaUc/zbkbdFbZeNZa8uPLPo7TuApwgBjOGzdoggPlRl8qGCE.q', 'customer', 'active', '2026-07-01 15:45:00')
ON DUPLICATE KEY UPDATE
    full_name = VALUES(full_name),
    phone = VALUES(phone),
    role = 'customer',
    status = 'active',
    created_at = VALUES(created_at);

INSERT INTO orders
    (user_id, order_code, receiver_name, receiver_phone, shipping_address, total_amount, discount_amount, final_amount, payment_method, status, created_at)
SELECT u.user_id, 'AUR-1033', 'Emma Vale', '0907777888', '22 Crystal Walk, District 5, HCMC', 6500000, 300000, 6200000, 'bank_transfer', 'delivered', '2026-07-01 14:10:00'
FROM users u WHERE u.email = 'emma@example.com'
ON DUPLICATE KEY UPDATE
    total_amount = VALUES(total_amount),
    discount_amount = VALUES(discount_amount),
    final_amount = VALUES(final_amount),
    payment_method = VALUES(payment_method),
    status = VALUES(status),
    created_at = VALUES(created_at);

INSERT INTO orders
    (user_id, order_code, receiver_name, receiver_phone, shipping_address, total_amount, discount_amount, final_amount, payment_method, status, created_at)
SELECT u.user_id, 'AUR-1034', 'Henry Bloom', '0908888999', '63 Amber House, District 10, HCMC', 3300000, 0, 3300000, 'cod', 'shipping', '2026-07-01 16:30:00'
FROM users u WHERE u.email = 'henry@example.com'
ON DUPLICATE KEY UPDATE
    total_amount = VALUES(total_amount),
    discount_amount = VALUES(discount_amount),
    final_amount = VALUES(final_amount),
    payment_method = VALUES(payment_method),
    status = VALUES(status),
    created_at = VALUES(created_at);

DELETE FROM order_details WHERE order_id IN (
    SELECT order_id FROM orders WHERE order_code IN ('AUR-1033', 'AUR-1034')
);

INSERT INTO order_details (order_id, product_id, quantity, price, selected_size, selected_color, selected_material)
SELECT o.order_id, p.product_id, 1, 1200000, 'Size 7', 'Gold', 'Gold 18K'
FROM orders o, products p
WHERE o.order_code IN ('AUR-1033', 'AUR-1034') AND p.sku = 'TEST-RING-001';

INSERT INTO order_details (order_id, product_id, quantity, price, selected_size, selected_color, selected_material)
SELECT o.order_id, p.product_id, 2, 1800000, 'Standard', 'Silver', 'Silver'
FROM orders o, products p
WHERE o.order_code = 'AUR-1033' AND p.sku = 'TEST-NECK-001';
