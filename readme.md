# JPEG XL Encode

[![Test](https://github.com/joppuyo/jpeg-xl-encode/actions/workflows/test.yml/badge.svg)](https://github.com/joppuyo/jpeg-xl-encode/actions/workflows/test.yml)

PHP library for encoding JPEG XL images. Very much inspired by [WebP Convert](https://github.com/rosell-dk/webp-convert).

## Requirements

* PHP 7.2.5 or later.
* Linux, MacOS or Windows OS with x64 architecture.
* Since the library executes the `cjxl` binary on the command line, the `proc_open` function needs to be enabled in the PHP installation (some hosts may disable this function for security reasons).

## Usage

```php
$source = '/absolute/path/to/source.jpeg';
$source = '/absolute/path/to/destination.jxl';
$options = [];
try {
    \Joppuyo\JpegXlEncode\Encoder::encode($source, $destination, $options);
} catch (Exception $exception) {
    error_log('something went wrong')
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

Enables progressive decoding for the image. If a web browser supports progressive rendering, the image will download perceptually faster.

In VarDCT mode, progressive decoding does not affect the file size much.

Enabling progressive decoding for Modular images is not recommended since it makes the resulting image file significantly larger. Maybe it should be enabled at a later date when there are browsers that support downloading only part of a modular progressive file.

Default value is `true` for lossy and `false` for lossless.
