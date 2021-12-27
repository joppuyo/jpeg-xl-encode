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
[![REUSE status](https://api.reuse.software/badge/github.com/joppuyo/jpeg-xl-encode)](https://api.reuse.software/info/github.com/joppuyo/jpeg-xl-encode)
[![Active Development](https://img.shields.io/badge/Maintenance%20Level-Actively%20Developed-brightgreen.svg)](https://gist.github.com/cheerfulstoic/d107229326a01ff0f333a1d3476e068d)

A PHP library for encoding JPEG XL images. Supports JPEG and PNG input. Very much inspired by the excellent [WebP Convert](https://github.com/rosell-dk/webp-convert) library.

## Requirements

* PHP 7.2.5 or later.
* Linux, MacOS or Windows OS with x64 architecture.
* The `proc_open` function needs to be enabled in the PHP installation since the library executes the `cjxl` binary on the command line. Some web hosts may disable this function for security reasons. You can also build ImageMagick or Vips from the source with JPEG XL support and use the Imagick/Vips PHP extension to encode jxl files.

## Installation

```
composer require joppuyo/jpeg-xl-encode
```

## Usage

```php
$source = '/absolute/path/to/source.jpeg';
$destination = '/absolute/path/to/destination.jxl';
$options = [
    'encoding' => 'lossy',
    'quality' => 80,
];
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

## Methods

There's 3 different methods you can use: cjxl binary, ImageMagick extension and Vips extension.

### Cjxl binary

This method executes the cjxl binary on the command line. It's the most compatible method and it supports the most features. However, the `proc_open` function needs to be enabled in the PHP installation since the library executes the `cjxl` binary on the command line. Some web hosts may disable this function for security reasons.

### ImageMagick extension

This method uses the ImageMagick library and its PHP extension Imagick. However, ImageMagick needs to be built with JXL delegate. In practice, this means you will need to install the libjxl library on the server. Then you will need to build ImageMagick from the source with the option `--with-jxl=yes`. Lastly, you will need to install the Imagick PHP extension. The ImageMagick extension does not support progressive encoding at the time.

### Vips extension

This method uses the vips library and its PHP extension. However, vips needs to be built with JXL support. In practice, this means you will need to install the libjxl library on the server. Then you will need to build vips from the source. Lastly, you will need to install the vips PHP extension. The vips extension does not support progressive encoding at the time.

## License

MIT.

For detailed license information, see the individual file headers and [`.reuse/dep5`](.reuse/dep5).

## Maintance level

This project is under active development and it has a number of features currently under development.
