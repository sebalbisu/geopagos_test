FROM php:8.3.9-fpm-bullseye as base

RUN mv /usr/local/etc/php/php.ini-development /usr/local/etc/php/php.ini

# packages
RUN apt update && \
    apt install -y \
    git \
    less \
    zip \
    curl \
    libcurl4-openssl-dev \
    libxml2-dev \
    libpng-dev libjpeg-dev libxpm-dev libfreetype6-dev \
    libpq-dev \
    nano && \
    apt update

# extensions
RUN pecl install xdebug redis
RUN docker-php-ext-configure gd --with-freetype=/usr/include/ --with-jpeg=/usr/include/
RUN docker-php-ext-install pdo pdo_mysql pdo_pgsql gd
RUN docker-php-ext-enable xdebug redis

# composer
RUN curl https://raw.githubusercontent.com/composer/getcomposer.org/master/web/installer | php
RUN chmod a+x ./composer.phar | mv ./composer.phar /usr/local/bin/composer

# phpunit
# RUN mkdir /usr/local/src/phpunit
# RUN composer require --working-dir=/usr/local/src/phpunit phpunit/phpunit
# RUN ln -s /usr/local/src/phpunit/vendor/bin/phpunit /usr/local/bin/

# config
RUN printf 'xdebug.mode=debug,develop\n\
    xdebug.xdebug.cli_color=1\n\
    xdebug.start_with_request=yes\n\
    xdebug.client_port=9003\n\
    xdebug.show_exception_trace=0\n\
    xdebug.show_error_trace=0\n\
    xdebug.log_level=0\n\
    ' >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini