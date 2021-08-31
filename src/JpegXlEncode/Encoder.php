<?php

// SPDX-FileCopyrightText: 2021 Johannes Siipola
// SPDX-License-Identifier: MIT

namespace Joppuyo\JpegXlEncode;

use ImageMimeTypeGuesser\ImageMimeTypeGuesser;
use Symfony\Component\Process\Process;

class Encoder {
    /*
     * Convert a JPEG or PNG file to JPEG XL
     * @throws \Exception
     * @return  void
     */
    public static function encode(string $source, string $destination, array $options = []) {

        $defaultOptions = [
            'effort' => 7,
            'lossless' => [
                'encoding' => 'lossless', // Modular
                'quality' => 100,
                'progressive' => false,
            ],
            'lossy' => [
                'encoding' => 'lossy', // VarDCT
                'quality' => 85,
                'progressive' => true,
            ]
        ];

        if (!file_exists($source)) {
            throw new \Exception('File does not exist.');
        }

        $mime = ImageMimeTypeGuesser::detect($source);

        if (is_null($mime) || $mime === false || !in_array($mime, ['image/jpeg', 'image/png'])) {
            throw new \Exception('Invalid MIME type. Must be one of the following: image/jpeg, image/png.');
        }

        // If source is JPEG, we use lossy encoding by default
        if (empty($options['encoding']) && $mime === 'image/jpeg') {
            $options['encoding'] = 'lossy';
        }

        // If source is PNG, we use lossless encoding by default
        if (empty($options['encoding']) && $mime === 'image/png') {
            $options['encoding'] = 'lossless';
        }

        // Set default options for lossy
        if ($options['encoding'] === 'lossy') {
            $defaultOptions = array_merge($defaultOptions['lossy'], $defaultOptions);
        }

        // Set default options for lossless
        if ($options['encoding'] === 'lossless') {
            $defaultOptions = array_merge($defaultOptions['lossless'], $defaultOptions);
        }

        $options = array_merge($defaultOptions, $options);

        // TODO: Let's allow quality now only in VarDCT mode to keep things simple
        if ($options['encoding'] === 'lossless') {
            $options['quality'] = 100;
        }

        $flags = [];

        // TODO: validate is numeric and in range
        if (!empty($options['quality']) && $options['encoding'] === 'lossy') {
            array_push($flags, '--quality', $options['quality']);
        }

        if ($options['encoding'] === 'lossless') {
            array_push($flags, '--modular');
        }

        if (!empty($options['progressive']) && $options['progressive'] === true) {
            array_push($flags, '--progressive');
        }

        $binary_path = self::getBinaryPath();
        self::ensure_permissions($binary_path);

        $process_parameters = array_merge([$binary_path, $source, $destination], $flags);

        self::debug('process parameters', $process_parameters);

        $process = new Process($process_parameters);

        $process->run();

        self::debug('process output', $process->getOutput());
        self::debug('process error output', $process->getErrorOutput());
    }

    public static function getBinaryPath()
    {
        if (PHP_OS_FAMILY === 'Darwin') {
            return __DIR__ . '/../../bin/cjxl-v0-5-0-macos-x64-static';
        }
        if (PHP_OS_FAMILY === 'Linux') {
            return __DIR__ . '/../../bin/cjxl-v0-5-0-linux-x64-static';
        }
        if (PHP_OS_FAMILY === 'Windows') {
            return __DIR__ . '/../../bin/cjxl-v0-5-0-windows-x64-static.exe';
        }
        throw new \Exception('Could not find binary suitable for the current operating system.');
    }

    private static function debug(...$params) {
        if (function_exists('codecept_debug')) {
            foreach ($params as $param) {
                codecept_debug($param);
            }
        }
    }

    /**
     * Make sure binary is executable
     * @param string $path
     */
    public static function ensure_permissions($path)
    {
        if (PHP_OS_FAMILY === 'Windows') {
            return;
        }

        $permissions = substr(sprintf('%o', fileperms($path)), -4);
        if ($permissions !== '0755') {
            chmod($path, 0755);
        }
    }
}
