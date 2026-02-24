# Usamos una imagen base que incluya Apache y PHP
FROM php:8.0-apache

# 1. Instalar dependencias del sistema y el driver PDO MySQL
RUN apt-get update && \
    apt-get install -y libpq-dev libzip-dev \
    # Instala el driver MySQL
    && docker-php-ext-install pdo_mysql
    
# Cambiamos el DocumentRoot de Apache para que apunte a la subcarpeta 'public'
RUN sed -i 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf && \
    sed -i 's!/var/www!/var/www/html/public!g' /etc/apache2/apache2.conf

# Habilitar el módulo rewrite de Apache (necesario para URLs limpias)
RUN a2enmod rewrite

# Ajustar permisos (aunque ya lo hicimos con chown, no está de más en el Dockerfile)
RUN chown -R www-data:www-data /var/www/html

# La aplicación correrá en el puerto 80 (mapeado a 8080 en docker-compose)
EXPOSE 80

# Inicia Apache (CMD por defecto)
# CMD ["apache2-foreground"]