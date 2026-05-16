FROM php:8.2-apache

# 1. Instalar extensiones necesarias para MariaDB/MySQL
RUN docker-php-ext-install pdo pdo_mysql

# 2. Habilitar el módulo rewrite de Apache (vital para tu .htaccess)
RUN a2enmod rewrite headers

# 3. Copiar los archivos de tu proyecto al contenedor
COPY . /var/www/html/

# 4. Configurar Apache para que apunte a la carpeta /public
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf \
    && sed -ri -e 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf

# 5. Asegurar permisos para las carpetas
RUN chown -R www-data:www-data /var/www/html

EXPOSE 8080