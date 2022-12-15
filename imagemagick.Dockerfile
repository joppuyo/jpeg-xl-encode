# SPDX-FileCopyrightText: 2021 Johannes Siipola
# SPDX-License-Identifier: CC0-1.0

FROM php:7.4-cli

RUN apt update && apt install -y \
    cmake pkg-config libbrotli-dev \
    libgif-dev libjpeg-dev libopenexr-dev libpng-dev libwebp-dev \
    clang git

ENV CC=clang
ENV CXX=clang++

WORKDIR /
RUN git clone https://github.com/libjxl/libjxl.git --recursive && cd /libjxl && git reset --hard tags/v0.6.1 && ./deps.sh
WORKDIR /libjxl/build
RUN cmake -DCMAKE_BUILD_TYPE=Release -DBUILD_TESTING=OFF ..
RUN cmake --build . -- -j$(nproc)
RUN cmake --install .

# Imagick

RUN apt-get update && apt-get install -y \
	libxml2 \
	libxml2-dev

RUN mkdir -p /imagick-source \
    && cd /imagick-source \
    && curl "https://imagemagick.org/archive/ImageMagick.tar.gz" -o imagemagick.tar.gz \
    && tar -xof imagemagick.tar.gz -C /imagick-source --strip-components=1 \
    && rm imagemagick.tar.gz* \
    && ./configure --with-jxl=yes \
    && make \
    && make install \
    && make clean

RUN pecl install imagick && docker-php-ext-enable imagick

# Xdebug

RUN apt-get clean
RUN apt-get update

RUN pecl install xdebug-3.0.4 && docker-php-ext-enable xdebug

# Composer

RUN apt-get install -y libzip-dev zip && docker-php-ext-install zip

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
