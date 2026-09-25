# Syntax: docker build -t gudang-gadget .
FROM php:8.3-fpm-alpine

RUN apk add --no-cache nginx supervisor curl git zip unzip oniguruma-dev libzip-dev libpng-dev freetype-dev libjpeg-turbo-dev nodejs npm \
 && docker-php-ext-configure gd --with-freetype --with-jpeg \
 && docker-php-ext-install pdo_mysql mbstring zip exif opcache gd \
 && docker-php-ext-enable opcache

# composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
ENV COMPOSER_ALLOW_SUPERUSER=1

WORKDIR /var/www/html

# paket & aset frontend
COPY package.json package-lock.json ./
RUN npm install --ignore-scripts

COPY composer.json composer.lock ./
COPY --chown=www-data:www-data . .

RUN composer install --no-dev --no-interaction --no-progress --prefer-dist --optimize-autoloader \
 && npm run build \
 && chown -R www-data:www-data /var/www/html \
 && chmod -R 755 /var/www/html

# supervisord (nginx + php-fpm)
COPY docker/supervisord.conf /etc/supervisord.conf
COPY docker/nginx-default.conf.template /etc/nginx/conf.d/default.conf.template
COPY docker/entrypoint.sh /usr/local/bin/entrypoint
RUN chmod +x /usr/local/bin/entrypoint

EXPOSE 8080
ENTRYPOINT ["/usr/local/bin/entrypoint"]