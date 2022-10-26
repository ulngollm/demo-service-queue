FROM php:8.1-cli

RUN pecl install xdebug \
    && docker-php-ext-enable xdebug

COPY ./xdebug.ini /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

WORKDIR /usr/src/atm

CMD ["bash"]
