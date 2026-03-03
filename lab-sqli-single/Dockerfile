# Pakai php:8.1-apache sebagai base — sudah include PHP + Apache
FROM php:8.1-apache

ENV DEBIAN_FRONTEND=noninteractive

# Install MySQL + Supervisor
RUN apt-get update && apt-get install -y \
    default-mysql-server \
    supervisor \
    && docker-php-ext-install mysqli \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Aktifkan mod rewrite Apache
RUN a2enmod rewrite

# Konfigurasi Apache
COPY apache.conf /etc/apache2/sites-available/000-default.conf

# Copy web files
COPY web/ /var/www/html/
RUN chown -R www-data:www-data /var/www/html/ && \
    rm -f /var/www/html/index.html

# Copy init SQL & configs
COPY init.sql /docker-entrypoint-initdb.sql
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

EXPOSE 80

ENTRYPOINT ["/entrypoint.sh"]
