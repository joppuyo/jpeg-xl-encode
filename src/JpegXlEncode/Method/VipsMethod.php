<?php

namespace Joppuyo\JpegXlEncode\Method;

use Jcupitt\Vips\Config;
use Jcupitt\Vips\Image;

class VipsMethod implements Method
{
    public static function isAvailable()
    {
        // The vips extension probably will not work on Windows
        if (PHP_OS_FAMILY === 'Windows') {
            return false;
        }

        // Check if the extension is loaded at all
        if (!extension_loaded('vips')) {
            return false;
        }
        // Check if Vips library was built with jxl support Vips extension uses __call to call
        // libvips methods so there's no easy way to check if jxl functions exist in the class. So,
        // let's just create a small image and save it as jxl. If it throws an exception, Vips was
        // not built with jxl support
        try {
            $image = Image::black(1, 1);
            $image->jxlsave_buffer();
        } catch (\Exception $exception) {
            return false;
        }
        return true;
    }

    public static function getSupportedFeatures()
    {
        return [
            'encoding' => ['lossy', 'lossless'],
            'lossy' => [
                'effort' => range(1, 9),
                'quality' => range(1, 99),
                'progressive' => [false],
            ],
            'lossless' => [
                'effort' => range(1, 9),
                'quality' => [100],
                'progressive' => [false],
            ]
        ];
    }

    public static function supportsConfig($config)
    {
    }

    public static function encode(string $source, string $destination, array $options = [])
    {
        $lossless = false;

        if (!empty($options['quality']) && $options['encoding'] === 'lossy') {
            $lossless = false;
        }

        if ($options['encoding'] === 'lossless') {
            $lossless = true;
        }

        $image = \Jcupitt\Vips\Image::newFromFile($source);
        $image->writeToFile(
            $destination,
            [
                'Q' => $options['quality'],
                'lossless' => $lossless,
            ]
        );
    }
}