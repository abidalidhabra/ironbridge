FROM 905666888987.dkr.ecr.us-east-1.amazonaws.com/laravel-ssl-base:latest

MAINTAINER Prashant Rajavat <prashant.rajavat1996@gmail.com>

ENV APP_ENV=dev
ENV TERM=xterm

COPY . /var/www/html
WORKDIR /var/www/html
RUN rm -rf .env
RUN cp .env.example .env
RUN composer install
# RUN php artisan migrate
RUN php artisan storage:link
RUN chown -R www-data:www-data /var/www/html

# RUN service apache2 restart