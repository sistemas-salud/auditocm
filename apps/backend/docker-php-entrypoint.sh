#!/bin/bash
# start php-fpm and nginx
php-fpm -D
nginx -g "daemon off;"