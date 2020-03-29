#!/bin/bash

/usr/bin/supervisord -n -c /etc/supervisor/supervisord.conf &

echo "cron starting..." && (cron) && : > /var/log/cron.log &

apache2-foreground