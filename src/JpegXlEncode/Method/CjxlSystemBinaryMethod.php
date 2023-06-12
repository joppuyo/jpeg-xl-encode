<?php

// SPDX-FileCopyrightText: 2021 Johannes Siipola
// SPDX-License-Identifier: MIT

namespace NPX\JpegXlEncode\Method;

use Jcupitt\Vips\Config;
use Jcupitt\Vips\Image;
use PHPUnit\Exception;
use Symfony\Component\Process\Process;

class CjxlSystemBinaryMethod implements Method
{
    public static function isAvailable()
    {
        if (!function_exists('proc_open')) {
            return false;
        }

        try {
            $binary_path = self::getBinaryPath();

            self::debug('binary path', $binary_path);

            $process = new Process([$binary_path, '--version']);
            $process->run();

            self::debug('result', $process->getOutput());

            if (stripos($process->getOutput(), 'Copyright (c) the JPEG XL Project') === false) {
                return false;
            }

            preg_match("/cjxl v(\d.\d.\d)/", $process->getOutput(), $matches);

            $version = '';

            if (!empty($matches) && !empty($matches[0]) && !empty($matches[1])) {
                $version = $matches[1];
            }

            if (
                version_compare($version, '0.8.0', '>=') &&
                version_compare($version, '0.9.0', '<')
            ) {
                return true;
            }

        } catch (\Exception $exception) {
            self::debug('Ran into exception while executing cjxl binary', $exception->getMessage());
            return false;
        }

        return false;
    }

    public static function getSupportedFeatures()
    {
        return [
            'encoding' => ['lossy', 'lossless'],
            'lossy' => [
                'effort' => range(1, 9),
                'quality' => range(1, 99), //TODO: maybe test if 100% quality differs from modular?
                'progressive' => [true, false],
            ],
            'lossless' => [
                'effort' => range(1, 9),
                'quality' => [100],
                'progressive' => [true, false],
            ]
        ];
    }

    public static function supportsConfig($config)
    {
    }

    public static function encode(string $source, string $destination, array $options = [])
    {
        $flags = [];

        array_push($flags, '--lossless_jpeg=0');

        if (!empty($options['quality'])) {
            array_push($flags, '--quality', $options['quality']);
        }

        if ($options['encoding'] === 'lossless') {
            array_push($flags, '--modular=1');
        }

        if (!empty($options['progressive']) && $options['progressive'] === true) {
            array_push($flags, '--progressive');
        }

        $binary_path = self::getBinaryPath();

        $process_parameters = array_merge([$binary_path, $source, $destination], $flags);

        self::debug('process parameters', $process_parameters);

        $process = new Process($process_parameters);

        $process->run();

        self::debug('process output', $process->getOutput());
        self::debug('process error output', $process->getErrorOutput());
    }

    public static function getBinaryPath()
    {
        return 'cjxl';
    }

    private static function debug(...$params) {
        if (function_exists('codecept_debug')) {
            foreach ($params as $param) {
                codecept_debug($param);
            }
        }
    }

}