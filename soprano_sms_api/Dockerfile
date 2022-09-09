# Install project dependacny, then will copy only vendor
FROM composer as builder
WORKDIR /app/
COPY composer.* ./
RUN composer install --ignore-platform-reqs

# Its the final image
FROM alpine:3.16
RUN apk update && apk upgrade
RUN apk add bash
RUN apk add nginx
RUN apk add php8 php8-fpm php8-mbstring php8-opcache
RUN apk add php8-gd php8-zlib php8-curl
COPY --from=builder /app/vendor /var/www/html/vendor
COPY server/nginx/conf.d /etc/nginx
COPY server/nginx/default.conf /etc/nginx/http.d
COPY server/php /etc/php8
COPY . /var/www/html
# will live php-fpm socket
RUN mkdir /var/run/php
EXPOSE 80
STOPSIGNAL SIGTERM
CMD ["/bin/bash", "-c", "php-fpm8 && chmod 777 /var/run/php/php8-fpm.sock && chmod 755 /var/www/html/public* && nginx -g 'daemon off;'"]