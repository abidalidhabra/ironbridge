FROM 905666888987.dkr.ecr.us-east-1.amazonaws.com/laravel-ssl-base:prod

ENV APP_ENV=dev
ENV TERM=xterm

COPY . /var/www/html

WORKDIR /var/www/html

RUN rm -rf .env
RUN cp .env.example .env

RUN composer install
RUN php artisan storage:link
RUN chown -R www-data:www-data /var/www/html