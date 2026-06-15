# Stage 1: instalar dependências com Composer
FROM composer:2 AS deps
WORKDIR /app
COPY composer.json ./
RUN composer install --no-dev --optimize-autoloader --no-interaction --ignore-platform-reqs

# Stage 2: imagem final leve
FROM php:8.2-apache

# Instalar só o necessário
RUN apt-get update && apt-get install -y --no-install-recommends \
    libzip-dev \
    && docker-php-ext-install pdo pdo_mysql zip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Habilitar mod_rewrite
RUN a2enmod rewrite

# DocumentRoot para /var/www/html/public
RUN sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/000-default.conf \
    && echo '<Directory /var/www/html/public>\n    AllowOverride All\n    Require all granted\n</Directory>' \
       >> /etc/apache2/apache2.conf

WORKDIR /var/www/html

# Copiar vendor do stage anterior
COPY --from=deps /app/vendor ./vendor

# Copiar código
COPY src/     ./src/
COPY public/  ./public/

RUN chown -R www-data:www-data /var/www/html

EXPOSE 80
