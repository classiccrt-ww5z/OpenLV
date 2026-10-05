FROM php:8.3-apache

# ffmpeg/ffprobe do the transcoding; the rest is what the app actually calls.
RUN apt-get update && apt-get install -y --no-install-recommends \
        ffmpeg \
        libpng-dev \
        libjpeg62-turbo-dev \
        libfreetype6-dev \
        libzip-dev \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" gd pdo_mysql

# mod_rewrite does the /user/foo -> profile.php routing, mod_headers sets the
# security headers that live in .htaccess.
RUN a2enmod rewrite headers

# The document root is app/. Everything else in this repo (Dockerfile, compose file,
# install.sh, schema.sql, .env) sits outside it and is therefore never reachable
# over HTTP, with no deny rules needed.
ENV APACHE_DOCUMENT_ROOT=/var/www/html/app

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' \
        /etc/apache2/sites-available/*.conf \
        /etc/apache2/apache2.conf \
        /etc/apache2/envvars

# Without AllowOverride All, Apache ignores .htaccess and none of the URL routing works.
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}/!g' \
        -e 's/AllowOverride None/AllowOverride All/g' \
        /etc/apache2/apache2.conf

# The templates use <? and <?= so short_open_tag has to stay on.
RUN { \
        echo 'short_open_tag = On'; \
        echo 'upload_max_filesize = 2G'; \
        echo 'post_max_size = 2G'; \
        echo 'memory_limit = 2560M'; \
        echo 'max_execution_time = 600'; \
        echo 'max_input_time = 600'; \
        echo 'expose_php = Off'; \
        echo 'session.cookie_httponly = 1'; \
        echo 'session.cookie_samesite = Lax'; \
    } > /usr/local/etc/php/conf.d/openlv.ini

WORKDIR /var/www/html/app