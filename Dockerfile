FROM php:8.1-apache

# Устанавливаем расширения для MySQL
RUN docker-php-ext-install pdo pdo_mysql

# Копируем исходный код в директорию веб-сервера
COPY .. /var/www/html/

# Разрешаем запись (если нужно для работы приложения)
RUN chmod -R 775 /var/www/html

EXPOSE 80
