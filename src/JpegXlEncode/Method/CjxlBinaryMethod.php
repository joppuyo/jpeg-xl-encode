<?php

namespace Joppuyo\JpegXlEncode\Method;

use Jcupitt\Vips\Config;
use Jcupitt\Vips\Image;
use Joppuyo\JpegXlEncode\Exception\BinaryValidationException;
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
            return realpath(__DIR__ . '/../../../bin/cjxl-v0-5-0-macos-x64-static');
        }
        if (PHP_OS_FAMILY === 'Linux') {
            return realpath(__DIR__ . '/../../../bin/cjxl-v0-6-1-linux-x64-static');
        }
        if (PHP_OS_FAMILY === 'Windows') {
            return realpath(__DIR__ . '/../../../bin/cjxl-v0-5-0-windows-x64-static.exe');
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