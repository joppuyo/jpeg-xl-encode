<!--
SPDX-FileCopyrightText: 2021 Johannes Siipola
SPDX-License-Identifier: CC0-1.0
-->

# JPEG XL Encode

[![GitHub Workflow Status](https://img.shields.io/github/workflow/status/joppuyo/jpeg-xl-encode/Test?label=tests&logo=github)](https://github.com/joppuyo/jpeg-xl-encode/actions)
[![Packagist Version](https://img.shields.io/packagist/v/joppuyo/jpeg-xl-encode)](https://packagist.org/packages/joppuyo/jpeg-xl-encode)
[![codecov](https://codecov.io/gh/joppuyo/jpeg-xl-encode/branch/main/graph/badge.svg?token=KBTKSRNEG6)](https://codecov.io/gh/joppuyo/jpeg-xl-encode)
[![Packagist PHP Version Support](https://img.shields.io/packagist/php-v/joppuyo/jpeg-xl-encode)](https://packagist.org/packages/joppuyo/jpeg-xl-encode)
[![Packagist License](https://img.shields.io/packagist/l/joppuyo/jpeg-xl-encode)](https://packagist.org/packages/joppuyo/jpeg-xl-encode)

A PHP library for encoding JPEG XL images. Supports JPEG and PNG input. Very much inspired by the excellent [WebP Convert](https://github.com/rosell-dk/webp-convert) library.

## Requirements

* PHP 7.2.5 or later.
* Linux, MacOS or Windows OS with x64 architecture.
* The `proc_open` function needs to be enabled in the PHP installation since the library executes the `cjxl` binary on the command line. Some web hosts may disable this function for security reasons.

## Installation

```
composer require joppuyo/jpeg-xl-encode
```

## Usage

```php
$source = '/absolute/path/to/source.jpeg';
$destination = '/absolute/path/to/destination.jxl';
$options = [];
try {
    \Joppuyo\JpegXlEncode\Encoder::encode($source, $destination, $options);
} catch (Exception $exception) {
    error_log('Whoops, something went wrong.');
}

```

## Options

### Encoding

`encoding`

Selects which encoding to use, `lossy` for VarDCT and `lossless` for Modular. Default is `lossy` for JPEG input and `lossless` for PNG input.

### Quality

`quality`

Image quality for lossy compression. The quality range goes from `1` to `100`. Default is `85`.

### Effort

`effort`

Controls how much time is used for image encoding. Longer encoding time means smaller files. Range is from `1` to `9` where `1` is the fastest and `9` is the slowest. Default is `7`.

### Progressive

`progressive`

Enables progressive decoding for the image. If a web browser supports progressive rendering, the image will download perceptually faster. In VarDCT mode, progressive decoding does not affect the file size much.

Enabling progressive decoding for Modular images is not recommended since it makes the resulting image file significantly larger. It could be enabled at a later date in a future version when there are browsers that support downloading only part of a modular progressive file.

Default value is `true` for lossy and `false` for lossless.
