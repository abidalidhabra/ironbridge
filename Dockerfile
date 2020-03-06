FROM 905666888987.dkr.ecr.us-east-1.amazonaws.com/laravel-ssl-base:latest

ENV APP_ENV=staging
ENV TERM=xterm

RUN apt-get update \
 && DEBIAN_FRONTEND=noninteractive apt-get install -y \
      cron \
 && apt-get clean \
 && rm -rf /var/lib/apt/lists/*

COPY . /var/www/html

WORKDIR /var/www/html

RUN rm -rf .env public/.htaccess
RUN cp .env.example .env
RUN cp -r public/htaccess_prod public/.htaccess


RUN composer install
RUN php artisan storage:link

# Add crontab file in the cron directory
ADD .docker/schedule/crontab /etc/cron.d/cron

# Give execution rights on the cron job
RUN chmod 0644 /etc/cron.d/cron

# Create the log file to be able to run tail
RUN touch /var/log/cron.log

# Changing ownership 
RUN chown -R www-data:www-data /var/www/html

# Run the command on container startup
CMD echo "cron starting..." && (cron) && : > /var/log/cron.log && apache2-foreground