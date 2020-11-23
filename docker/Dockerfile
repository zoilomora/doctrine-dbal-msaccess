FROM php:7.4-cli

RUN apt-get update && \
    apt-get install -y \
        nano \
        libzip-dev \
        libssl-dev \
        mdbtools \
        odbc-mdbtools \
        unixodbc \
        unixodbc-dev && \
    docker-php-ext-configure \
        pdo_odbc --with-pdo-odbc=unixODBC,/usr && \
    docker-php-ext-install -j$(nproc) \
        zip \
        pdo_odbc

RUN docker-php-ext-configure odbc || true && \
    cd /usr/src/php/ext/odbc && \
    phpize && \
    sed -ri 's@^ *test +"\$PHP_.*" *= *"no" *&& *PHP_.*=yes *$@#&@g' configure && \
    ./configure --with-unixODBC=/usr && \
    docker-php-ext-install -j$(nproc) odbc

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin/ --filename=composer

COPY ./docker/etc/php.ini /usr/local/etc/php/php.ini
COPY ./docker/etc/odbc.ini /etc/odbc.ini

ENV PATH /var/app/bin:/var/app/vendor/bin:$PATH

WORKDIR /var/app
