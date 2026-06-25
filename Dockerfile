FROM php:8.3-apache

# 1. Ép Apache trỏ DocumentRoot vào thư mục public
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# 2. Cài extension PHP cần thiết (Giữ nguyên code cũ của bạn)
RUN docker-php-ext-install pdo pdo_mysql mysqli opcache \
    && a2enmod rewrite

# 3. Cho phép .htaccess hoạt động (Giữ nguyên code cũ của bạn)
RUN sed -i '/<Directory \/var\/www\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf

WORKDIR /var/www/html
