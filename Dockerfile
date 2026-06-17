FROM php:8.3-apache

# Cài extension PHP cần cho làm việc với MySQL + các tiện ích cơ bản
RUN docker-php-ext-install pdo pdo_mysql mysqli opcache \
    && a2enmod rewrite

# Cho phép .htaccess override (cần cho rewrite rule, ví dụ framework routing)
RUN sed -i '/<Directory \/var\/www\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf

WORKDIR /var/www/html
