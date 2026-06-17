# Môi trường lập trình web: PHP 8.3 + MySQL 8.0 + phpMyAdmin

## Cấu trúc
```
.
├── docker-compose.yml
├── Dockerfile
├── .env.example
└── src/
    └── index.php       # file test, sau này code app vào đây
```

## Cách dùng

1. Copy file env mẫu rồi sửa password tùy ý:
```bash
cp .env.example .env
tạo file .env và copy tử .env.example qua chỉ thay đổi DB_ROOT_PASSWORD và DB_PASSWORD thôi
```

2. Build và chạy:
```bash
docker compose up -d --build
```

3. Kiểm tra:
- App PHP: http://localhost:8080 (sẽ thấy dòng test kết nối MySQL)
- phpMyAdmin: http://localhost:8081 (login bằng `DB_USER`/`DB_PASSWORD` hoặc `root`/`DB_ROOT_PASSWORD` trong file `.env`)
- MySQL: expose ra `localhost:3306` nếu muốn connect bằng client ngoài (TablePlus, DBeaver...)

## Lệnh hay dùng
```bash
docker compose down              # tắt, giữ data
docker compose down -v           # tắt và xóa luôn data MySQL
docker compose logs -f php       # xem log container php
docker compose exec php bash     # vào shell container php
docker compose exec mysql mysql -uroot -p   # vào mysql cli
```

## Lưu ý
- Code PHP của bạn để vào thư mục `src/`, container tự mount nên sửa code là thấy ngay, không cần rebuild.
- Nếu cần thêm extension PHP (gd, zip, intl...) thì thêm vào `Dockerfile` rồi `docker compose up -d --build` lại.
- Nếu sau này muốn deploy thật hoặc dùng framework nặng (Laravel...) thì nên đổi sang nginx + php-fpm cho gần với production hơn, setup này dùng Apache cho đơn giản lúc học/dev.
