# Stage 1: Build stage
FROM php:8.3-fpm-alpine as build

RUN apk add --no-cache \
    zip \
    unzip \
    libzip-dev \
    freetype \
    libjpeg-turbo \
    libpng \
    freetype-dev \
    libjpeg-turbo-dev \
    libpng-dev \
    oniguruma-dev \
    gettext-dev \
    libxml2-dev \
    libxslt-dev \
    icu-dev \
    postgresql-dev \
    curl-dev \
    openssl-dev \
    pcre-dev \
    linux-headers \
    nodejs \
    npm \
    nginx \
    && docker-php-ext-configure zip \
    && docker-php-ext-install zip pdo pdo_mysql pdo_pgsql \
    && docker-php-ext-configure gd --with-freetype=/usr/include/ --with-jpeg=/usr/include/ \
    && docker-php-ext-install -j$(nproc) gd \
    && docker-php-ext-install bcmath exif gettext opcache intl soap xsl sockets \
    && docker-php-ext-enable gd bcmath exif gettext opcache intl soap xsl sockets

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /var/www/html

COPY . .
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/bootstrap/cache

RUN composer install --no-dev --prefer-dist \
    && npm install \
    && npm run build

RUN chown -R www-data:www-data /var/www/html/vendor \
    && chmod -R 775 /var/www/html/vendor

FROM php:8.3-fpm-alpine

RUN apk add --no-cache \
    zip \
    unzip \
    libzip-dev \
    freetype \
    libjpeg-turbo \
    libpng \
    freetype-dev \
    libjpeg-turbo-dev \
    libpng-dev \
    oniguruma-dev \
    gettext-dev \
    libxml2-dev \
    libxslt-dev \
    icu-dev \
    postgresql-dev \
    curl-dev \
    openssl-dev \
    pcre-dev \
    linux-headers \
    nginx \
    nodejs \
    npm \
    && docker-php-ext-configure zip \
    && docker-php-ext-install zip pdo pdo_mysql pdo_pgsql \
    && docker-php-ext-configure gd --with-freetype=/usr/include/ --with-jpeg=/usr/include/ \
    && docker-php-ext-install -j$(nproc) gd \
    && docker-php-ext-install bcmath exif gettext opcache intl soap xsl sockets \
    && docker-php-ext-enable gd bcmath exif gettext opcache intl soap xsl sockets \
    && rm -rf /var/cache/apk/*

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

COPY --from=build /var/www/html /var/www/html
COPY ./deploy/nginx.conf /etc/nginx/http.d/default.conf
COPY ./deploy/php.ini /usr/local/etc/php/conf.d/app.ini

WORKDIR /var/www/html

VOLUME ["/var/www/html/storage/app"]

CMD ["sh", "-c", "nginx && php-fpm"]