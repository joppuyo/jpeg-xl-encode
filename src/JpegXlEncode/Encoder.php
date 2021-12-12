<?php

// SPDX-FileCopyrightText: 2021 Johannes Siipola
// SPDX-License-Identifier: MIT

// SPDX-FileCopyrightText: Copyright (c) 2005-2008, eZ Systems A.S.
// SPDX-License-Identifier: BSD-3-Clause

namespace Joppuyo\JpegXlEncode;

use ImageMimeTypeGuesser\ImageMimeTypeGuesser;
use Joppuyo\JpegXlEncode\Exception\BinaryValidationException;
use Joppuyo\JpegXlEncode\Exception\InvalidArgumentException;
use Symfony\Component\Process\Process;
use Respect\Validation\Validator as v;

class Encoder {

    /**
     * @var bool
     */
    private static $binaryValidated = false;

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
            ],
            '_methods' => [
                'cjxlBinary',
                'vipsExtension',
                'imagickExtension',
            ],
        ];

        if (!self::isAbsolutePath($source)) {
            throw new InvalidArgumentException('Source path must be an absolute path.');
        }

        if (!self::isAbsolutePath($destination)) {
            throw new InvalidArgumentException('Destination path must be an absolute path.');
        }

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

        self::validateOptions($options);

        $flags = [];
        
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
        self::validateBinary($binary_path);
        self::ensurePermissions($binary_path);

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
            return realpath(__DIR__ . '/../../bin/cjxl-v0-5-0-macos-x64-static');
        }
        if (PHP_OS_FAMILY === 'Linux') {
            return realpath(__DIR__ . '/../../bin/cjxl-v0-6-1-linux-x64-static');
        }
        if (PHP_OS_FAMILY === 'Windows') {
            return realpath(__DIR__ . '/../../bin/cjxl-v0-5-0-windows-x64-static.exe');
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
     */
    public static function ensurePermissions(string $path)
    {
        if (PHP_OS_FAMILY === 'Windows') {
            return;
        }

        $permissions = substr(sprintf('%o', fileperms($path)), -4);
        if ($permissions !== '0755') {
            chmod($path, 0755);
        }
    }

    private static function validateOptions(array $options)
    {
        $optionValidator = v::key('quality', v::intType()->between(1, 100))
            ->key('effort', v::intType()->between(1, 9))
            ->key('progressive', v::boolType())
            ->key('encoding', v::stringType()->in(['lossless', 'lossy']))
            ->key('_methods', v::arrayType()->each(v::stringVal()));

        try {
            $optionValidator->check($options);
        } catch (\Exception $exception) {
            throw new InvalidArgumentException($exception->getMessage());
        }

    }

    private static function validateBinary($binaryPath) {
        if(self::$binaryValidated) {
            // We validate binary only once per request to improve performance
            self::debug('Binary already validated.');
            return;
        }
        self::debug("Binary hasn't been validated yet. Validating...");
        $comparisonHash = self::getHash();
        $binaryHash = hash_file('sha256', $binaryPath);
        if(!hash_equals($binaryHash, $comparisonHash)) {
            self::debug('Hash does not match.');
            throw new BinaryValidationException("Binary hash check failed.");
        }
        self::debug('Binary hash matches. Caching result of hash comparison to speed up further conversions.');
        self::$binaryValidated = true;
    }

    private static function getHash()
    {
        if (PHP_OS_FAMILY === 'Darwin') {
            // https://github.com/joppuyo/jpeg-xl-static-mac/releases/tag/v0.5.0-static-2
            return '292927130b4a83c639df6ba573916c2205234ca85f68a1e1357201e5b33b1904';
        }
        if (PHP_OS_FAMILY === 'Linux') {
            // https://github.com/libjxl/libjxl/releases/tag/v0.6.1
            return '07bfb1902ef8eab8b5266ad884c2638fd17552a7e053ea0d65aa606cf7fcce48';
        }
        if (PHP_OS_FAMILY === 'Windows') {
            // https://github.com/joppuyo/jpeg-xl-static/releases/tag/v0.5.0-static
            return 'b78ec5a1b48c48c1e0dbb47865f7af8057a92291c65581a59e744a3dac6d5490';
        }
    }

    /**
     * Returns whether the passed $path is an absolute path.
     * Based on code from EzComponents, licensed under New BSD Licence
     * Copyright (c) 2005-2008, eZ Systems A.S.
     *
     * @param string $path
     * @return bool
     */
    public static function isAbsolutePath($path, $os = PHP_OS_FAMILY)
    {
        if ($os === 'Windows') {
            // Sanitize the paths to use the correct directory separator for the platform
            $path = strtr($path, '\\/', '\\\\');

            // Absolute paths with drive letter: X:\
            if (preg_match('@^[A-Z]:\\\\@i', $path)) {
                return true;
            }

            // Absolute paths with network paths: \\server\share\
            if (preg_match('@^\\\\\\\\[A-Z]+\\\\[^\\\\]@i', $path)) {
                return true;
            }
        } else {
            // Sanitize the paths to use the correct directory separator for the platform
            $path = strtr($path, '\\/', '//');

            if ($path[0] == '/') {
                return true;
            }
        }
        return false;
    }
}
