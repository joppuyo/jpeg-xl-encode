# SPDX-FileCopyrightText: 2021 Johannes Siipola
# SPDX-License-Identifier: CC0-1.0

FROM php:7.4-cli

RUN apt-get clean
RUN apt-get update

RUN pecl install xdebug && docker-php-ext-enable xdebug

RUN apt-get install -y libzip-dev zip && docker-php-ext-install zip

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
