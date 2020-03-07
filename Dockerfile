FROM 905666888987.dkr.ecr.us-east-1.amazonaws.com/laravel-ssl-base:latest

ENV APP_ENV=staging
ENV TERM=xterm

COPY --chown=www-data:www-data . /var/www/html

WORKDIR /var/www/html

RUN rm -rf .env public/.htaccess
RUN cp .env.example .env
RUN cp -r public/htaccess_prod public/.htaccess


RUN composer install
RUN php artisan storage:link

# Changing ownership 
RUN chown -R www-data:www-data /var/www/html

RUN chmod -R 0777 /var/www/html/storage/logs

# Run the command on container startup
CMD echo "cron starting..." && (cron) && : > /var/log/cron.log && apache2-foreground