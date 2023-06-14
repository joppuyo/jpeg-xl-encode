<?php

// SPDX-FileCopyrightText: 2021 Johannes Siipola
// SPDX-License-Identifier: MIT

namespace NPX\JpegXlEncode\Method;

use Jcupitt\Vips\Config;
use Jcupitt\Vips\Image;
use NPX\JpegXlEncode\Exception\BinaryValidationException;
use PHPUnit\Exception;
use Symfony\Component\Process\Process;

class CjxlBinaryMethod implements Method
{
    /**
     * @var bool
     */
    private static $binaryValidated = false;

    public static function isAvailable()
    {
        if (!function_exists('proc_open')) {
            return false;
        }

        try {
            $binary_path = self::getBinaryPath();

            self::debug('binary path', $binary_path);

            self::validateBinary($binary_path);
            self::ensurePermissions($binary_path);
            $process = new Process([$binary_path, '--version']);
            $process->run();

            self::debug('result', $process->getOutput());

            if (stripos($process->getOutput(), 'Copyright (c) the JPEG XL Project') !== false) {
                return true;
            }

        } catch (\Exception $exception) {
            self::debug('Ran into exception while executing cjxl binary', $exception->getMessage());
            return false;
        }

        return false;
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
            return realpath(__DIR__ . '/../../../bin/cjxl-v0-8-1-macos-x64-static');
        }
        if (PHP_OS_FAMILY === 'Linux') {
            return realpath(__DIR__ . '/../../../bin/cjxl-v0-8-1-linux-x64-static');
        }
        if (PHP_OS_FAMILY === 'Windows') {
            return realpath(__DIR__ . '/../../../bin/cjxl-v0-8-1-windows-x64-static/cjxl.exe');
        }
        throw new \Exception('Could not find binary suitable for the current operating system.');
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
            // https://github.com/joppuyo/libjxl-0.8.1-mac-static/releases/tag/v0.8.1-mac
            return '1096361e774f4f400c06e87df65821d0e8f8c8224007d85c0ad034a9051d8fe4';
        }
        if (PHP_OS_FAMILY === 'Linux') {
            // https://github.com/libjxl/libjxl/releases/tag/v0.8.1
            return 'bb0dd640f120771d699931935970333569673a5a83edd471ac07068285f5d6c1';
        }
        if (PHP_OS_FAMILY === 'Windows') {
            // https://github.com/libjxl/libjxl/releases/tag/v0.8.1
            return '90291a0ccc3cbcc5626e45ff3ddc155d79469322384d710c70a41f917b45c2e8';
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

    private static function debug(...$params) {
        if (function_exists('codecept_debug')) {
            foreach ($params as $param) {
                codecept_debug($param);
            }
        }
    }

}