FROM php:7.1-apache as prod

# install the PHP extensions we need
# Get utilities
RUN apt-get update && apt-get install -y \
	git \
	curl \
	mysql-client \
	vim \
	wget \
    nodejs \
	sendmail \
	; \
	apt-get install -y \
    m4 \
    ocaml \
    apt-utils \
    gnupg \
    zip \
    unzip \
    ; \
    set -ex; \
	\
	if command -v a2enmod; then \
		a2enmod rewrite; \
	fi; \
	\
	apt-get update; \
	apt-get install -y --no-install-recommends \
		libjpeg-dev \
		libpng-dev \
		libpq-dev \
	; \
	\
	docker-php-ext-configure gd --with-png-dir=/usr --with-jpeg-dir=/usr; \
	docker-php-ext-install -j "$(nproc)" \
		gd \
		opcache \
		pdo_mysql \
		pdo_pgsql \
		zip \
	; \
	\
	savedAptMark="$(apt-mark showmanual)"; \
    \
# reset apt-mark's "manual" list so that "purge --auto-remove" will remove all build dependencies
	apt-mark auto '.*' > /dev/null; \
	apt-mark manual $savedAptMark; \
	ldd "$(php -r 'echo ini_get("extension_dir");')"/*.so \
		| awk '/=>/ { print $3 }' \
		| sort -u \
		| xargs -r dpkg-query -S \
		| cut -d: -f1 \
		| sort -u \
		| xargs -rt apt-mark manual; \
	\
	apt-get purge -y --auto-remove -o APT::AutoRemove::RecommendsImportant=false;
# set recommended PHP.ini settings
# see https://secure.php.net/manual/en/opcache.installation.php
RUN { \
		echo 'opcache.memory_consumption=128'; \
		echo 'opcache.interned_strings_buffer=8'; \
		echo 'opcache.max_accelerated_files=4000'; \
		echo 'opcache.revalidate_freq=60'; \
		echo 'opcache.fast_shutdown=1'; \
		echo 'opcache.enable_cli=1'; \
	} > /usr/local/etc/php/conf.d/opcache-recommended.ini

# increase default memory limit when necessary (edit php-memory-limit.ini if required)
COPY ./docker/php-memory-limit.ini /usr/local/etc/php/conf.d/php-memory-limit.ini
# Get img processor used by drupal module imageapi_optimize
RUN cd /tmp && \
    git clone --recursive https://github.com/kornelski/pngquant.git && \
    cd pngquant && \
    ./configure && \
    make && \
    make install \
    ; \
    cd /tmp && \
    git clone --recursive https://github.com/tjko/jpegoptim.git && \
    cd jpegoptim && \
    ./configure && \
    make && \
    make strip && \
    make install \
    ; \
# Get composer
    cd /tmp && \
    php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" && \
	php composer-setup.php && \
	mv composer.phar /usr/local/bin/composer && \
	chown www-data:www-data -R /var/www/ && \
	php -r "unlink('composer-setup.php');" \
    ; \
# Get Drupal Console
    curl https://drupalconsole.com/installer -L -o drupal.phar && \
    mv drupal.phar /usr/local/bin/drupal && \
    chmod +x /usr/local/bin/drupal && \
    drupal init && \
    drupal self-update \
    ; \
# Get Drush
    wget -O drush.phar https://github.com/drush-ops/drush-launcher/releases/download/0.4.2/drush.phar && \
	chmod +x drush.phar && \
	mv drush.phar /usr/local/bin/drush \
	; \
# Put a turbo on composer.
    su - www-data -s /usr/local/bin/composer self-update && \
    su - www-data -s /usr/local/bin/composer global require hirak/prestissimo; \
# Install npm
    curl -sL https://deb.nodesource.com/setup_8.x | bash - ;\
    apt-get update ;\
    apt-get install -y nodejs build-essential ;\
    update-alternatives --install /usr/bin/node node /usr/bin/nodejs 10 ;\
# Delete defaut apache html dir
    rm -rf /var/www/*;\
# Clean-up
    rm -rf /tmp/*;

# Change the default Apache2 conf to target the Web/ folder instead of html/
COPY ./docker/apache-drupal.conf /etc/apache2/sites-enabled/000-default.conf

RUN line=$(head -n 1 /etc/hosts)  && \
    line2=$(echo $line | awk '{print $2}')  && \
    echo "$line $line2.localdomain" >> /etc/hosts
# Copy Drupal installation scripts, drush and config directories
COPY --chown=www-data:www-data ["./app/load.environment.php","./app/composer.json","./app/composer.lock","/var/www/"]
COPY --chown=www-data:www-data ./app/drush /var/www/drush
COPY --chown=www-data:www-data ./app/config/sync /var/www/config/sync
COPY --chown=www-data:www-data  ./app/scripts /var/www/scripts

RUN chown www-data:www-data -R /var/www
USER www-data
WORKDIR /var/www/

# Install drupal - Devel is currently used on prod : add --no-dev once removed and run again without --no-dev on DEV env.
RUN composer install && \
# Add scaffold files
    composer drupal:scaffold;\
# Clean-up unwanted files
    rm /var/www/web/core/CHANGELOG.txt;\
    rm /var/www/web/core/COPYRIGHT.txt;\
    rm /var/www/web/core/INSTALL*.txt;\
    rm /var/www/web/core/LICENSE.txt;\
    rm /var/www/web/core/MAINTAINERS.txt;\
    rm /var/www/web/core/UPDATE.txt;\
    rm /var/www/web/core/install.php;\
    rm /var/www/web/core/.env.example;\
    rm /var/www/web/core/.gitignore;

#Copy main configuration file (default local config is development.settings.local.php)
COPY --chown=www-data:www-data ["./app/web/sites/default/settings.php", "./app/web/sites/default/staging.settings.local.php", "./app/web/sites/default/production.settings.local.php", "/var/www/web/sites/default/"]
COPY --chown=www-data:www-data ./app/web/sites/default/development.settings.local.php /var/www/web/sites/default/settings.local.php

#Copy web root files
COPY --chown=www-data:www-data ["./app/web/.htaccess","./app/web/robots.txt", "/var/www/web/"]
#Copy custom modules/themes
COPY --chown=www-data:www-data ./app/web/modules/custom /var/www/web/modules/custom
COPY --chown=www-data:www-data ./app/web/themes/custom /var/www/web/themes/custom

USER root

EXPOSE 80 443 8025

# Additionnal stage for local/dev environment : add debug/testing tools
FROM prod

USER www-data
#RUN composer install #once developer tools are removed from prod DB

USER root
# Install XDebug
COPY ./docker/docker-php-ext-xdebug.ini /tmp
RUN pecl install xdebug-2.5.5 \
    && docker-php-ext-enable xdebug \
    && cat /tmp/docker-php-ext-xdebug.ini >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

# Install Robo CI.
RUN wget http://robo.li/robo.phar
RUN chmod +x robo.phar && mv robo.phar /usr/local/bin/robo

# Cp testing config
COPY --chown=www-data:www-data ./app/web/phpunit.xml /var/www/web/phpunit.xml

WORKDIR /var/www

RUN chfn -f "Benoit de Raemy" www-data

# vim:set ft=dockerfile:
