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
RUN git clone https://github.com/libjxl/libjxl.git --recursive && cd /libjxl && git reset --hard tags/v0.6.1
WORKDIR /libjxl/build
RUN cmake -DCMAKE_BUILD_TYPE=Release -DBUILD_TESTING=OFF ..
RUN cmake --build . -- -j$(nproc)
RUN cmake --install .

# Vips
RUN apt-get update && apt-get install -y \
	build-essential \
	wget \
	pkg-config

# stuff we need to build our own libvips ... this is a pretty random selection
# of dependencies, you'll want to adjust these
RUN apt-get install -y \
	glib-2.0-dev \
	libexpat-dev \
	librsvg2-dev \
	libpng-dev \
	libgif-dev \
	libjpeg-dev \
	libexif-dev \
	liblcms2-dev \
	liborc-dev

ARG VIPS_VERSION=8.12.1
ARG VIPS_URL=https://github.com/libvips/libvips/releases/download

RUN cd /usr/local/src \
	&& wget ${VIPS_URL}/v${VIPS_VERSION}/vips-${VIPS_VERSION}.tar.gz \
	&& tar xf vips-${VIPS_VERSION}.tar.gz \
	&& cd vips-${VIPS_VERSION} \
	&& ./configure \
	&& make \
	&& make install

RUN PKG_CONFIG_PATH=/usr/local/lib/pkgconfig pecl install vips && docker-php-ext-enable vips

# Xdebug

RUN apt-get clean
RUN apt-get update

RUN pecl install xdebug && docker-php-ext-enable xdebug

# Composer

RUN apt-get install -y libzip-dev zip && docker-php-ext-install zip

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
