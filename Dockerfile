FROM php:7.4-alpine

ARG DB_DATABASE
ARG DB_USERNAME
ARG DB_PASSWORD

RUN echo $DB_DATABASE

EXPOSE 8889

RUN apk add --no-cache --update --virtual .build-deps $PHPIZE_DEPS libzip-dev
RUN docker-php-ext-install pdo_mysql zip
RUN pecl install redis
RUN docker-php-ext-enable redis
RUN apk del .build-deps
RUN apk add php-zip

WORKDIR /var/www/app
COPY . /var/www/app

ENV DB_HOST=database \
    DB_DATABASE=$DB_DATABASE \
    DB_USERNAME=$DB_USERNAME \
    DB_PASSWORD=$DB_PASSWORD \
    REDIS_HOST=redis

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer  --version=1.10.22

RUN composer install --no-plugins --no-scripts

RUN mkdir -p /var/www/app/.mysql

RUN chmod +x /var/www/app/migrate.sh

ENTRYPOINT ["/var/www/app/migrate.sh"]

CMD ["php", "artisan", "serve", "--host", "0.0.0.0", "--port", "8889"]
