#!/bin/bash

/usr/bin/supervisord -n -c /etc/supervisor/supervisord.conf &

cron &

apache2-foreground