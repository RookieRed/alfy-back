FROM php:7.3-fpm-alpine as php_base

# Install Linux dependencies : composer / bash
RUN apk add --no-cache $PHPIZE_DEPS --update \
        composer \
        util-linux \
        bash \
        zlib-dev \
        libzip-dev \
        libpng-dev \
        openldap-clients \
        openldap-dev

# Install PHP dependencies : PDO drivers, zip, GD
RUN docker-php-ext-install mysqli pdo_mysql zip gd ldap

# Run composer install
RUN mkdir /web && cd /web
WORKDIR /web
COPY composer.json /web/composer.json
RUN composer install -n --no-dev

# Build target debug for dev needs
FROM php_base as php_debug

RUN apk add --no-cache $PHPIZE_DEPS --update \
    && pecl install xdebug \
    && docker-php-ext-enable xdebug

RUN composer install -n --dev

EXPOSE 8000 9000

CMD ["php", "bin/console", "server:run", "0.0.0.0:8000"]
