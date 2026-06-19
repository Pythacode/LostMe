FROM php:8.2-apache

RUN docker-php-ext-install pdo pdo_mysql mysqli curl

COPY . /var/www/html/

COPY entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

EXPOSE 10000

ENTRYPOINT ["/entrypoint.sh"]
