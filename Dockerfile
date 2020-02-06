FROM 905666888987.dkr.ecr.us-east-1.amazonaws.com/laravel-ssl-base:latest

ENV APP_ENV=staging
ENV TERM=xterm

COPY . /var/www/html

WORKDIR /var/www/html

RUN rm -rf .env public/.htaccess
RUN cp .env.example .env
RUN cp -r public/htaccess_prod public/.htaccess


RUN composer install
RUN php artisan storage:link
RUN chown -R www-data:www-data /var/www/html