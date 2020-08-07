#!/bin/bash

/usr/bin/supervisord -n -c /etc/supervisor/supervisord.conf &

# echo "cron starting..." && (cron) && : > /var/log/cron.log &

crontab /etc/cron.d/cron

cron -f

apache2-foreground