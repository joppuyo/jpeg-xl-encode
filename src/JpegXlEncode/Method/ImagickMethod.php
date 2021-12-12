<?php

namespace Joppuyo\JpegXlEncode\Method;

use Jcupitt\Vips\Config;
use Jcupitt\Vips\Image;

class ImagickMethod implements Method
{
    public static function isAvailable()
    {
        // Check if the extension is loaded at all
        if (!extension_loaded('imagick')) {
            return false;
        }
        $im = new \Imagick();
        if (!in_array('JXL', $im->queryFormats('JXL'))) {
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

        $quality  = $options['quality'];

        if ($options['encoding'] === 'lossless') {
            $quality = 100;
        }

        $imagick = new \Imagick($source);
        $imagick->setImageFormat('JXL');
        $imagick->setCompressionQuality($quality);
        $imagick->setOption('jxl:effort', $options['effort']);

        $imagick->writeImage($destination);

    }
}