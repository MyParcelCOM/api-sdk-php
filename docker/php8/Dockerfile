FROM php:8.3.13-cli

RUN apt-get update \
    && apt-get install -y curl zip unzip software-properties-common gettext-base iproute2 \
    && pecl install xdebug \
    && docker-php-ext-enable xdebug \
    && php -r "readfile('http://getcomposer.org/installer');" | php -- --install-dir=/usr/bin/ --filename=composer \
    && apt-get remove -y --purge software-properties-common wget \
    && apt-get -y autoremove \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* \
    && echo 'error_reporting = E_ALL' > /usr/local/etc/php/php.ini \
    && echo 'memory_limit = -1' > /usr/local/etc/php/conf.d/docker-php-memlimit.ini
