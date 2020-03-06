FROM php:7.2-apache

RUN apt-get -y update

RUN apt-get install -y \
    build-essential \
    git \
    zip \
    wget \
    g++ \
    vim \
    cron

RUN apt-get install -y libsndfile1 --no-install-recommends

RUN apt-get install -y \
    autoconf \
    g++ \
    make \
    libcurl4-openssl-dev \
    libssl-dev \
    libicu-dev \
    python-pip


RUN apt-get install -y pkg-config libsasl2-dev zlib1g-dev libpng-dev  --fix-missing

RUN apt-get install -y libjpeg62-turbo-dev libpango1.0-dev libgif-dev  --fix-missing


RUN docker-php-ext-install intl mbstring
RUN docker-php-ext-install gd

RUN docker-php-ext-install -j$(nproc) mysqli pdo pdo_mysql mbstring bcmath
RUN docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/

RUN pecl install mongodb

RUN mkdir /etc/apache2/ssl

COPY .docker/php.ini /usr/local/etc/php
COPY .docker/vhost.conf /etc/apache2/sites-available/000-default.conf
COPY .docker/cert/stageapi* /etc/apache2/ssl/

RUN a2enmod rewrite ssl

RUN apt-get install -y \
        zlib1g-dev \
        zip \
  && docker-php-ext-install zip
RUN curl -O https://getcomposer.org/composer.phar
RUN mv composer.phar /usr/local/bin/composer
RUN chmod a+x /usr/local/bin/composer

RUN mkdir /root/.composer

RUN echo "\n<FilesMatch \\.php$>\nSetHandler application/x-httpd-php\n</FilesMatch>" >> /etc/apache2/apache2.conf

# Add crontab file in the cron directory
ADD schedule/crontab /etc/cron.d/cron

# Give execution rights on the cron job
RUN chmod 0644 /etc/cron.d/cron

# Create the log file to be able to run tail
RUN touch /var/log/cron.log

EXPOSE 80 443

ENV APP_ENV=dev
ENV TERM=xterm

RUN service apache2 restart