<?php

// SPDX-FileCopyrightText: 2021 Johannes Siipola
// SPDX-License-Identifier: MIT

// SPDX-FileCopyrightText: Copyright (c) 2005-2008, eZ Systems A.S.
// SPDX-License-Identifier: BSD-3-Clause

namespace NPX\JpegXlEncode;

use ImageMimeTypeGuesser\ImageMimeTypeGuesser;
use NPX\JpegXlEncode\Exception\BinaryValidationException;
use NPX\JpegXlEncode\Exception\InvalidArgumentException;
use NPX\JpegXlEncode\Exception\MethodUnavailableException;
use NPX\JpegXlEncode\Method\CjxlBinaryMethod;
use NPX\JpegXlEncode\Method\CjxlSystemBinaryMethod;
use NPX\JpegXlEncode\Method\DummyThrowsExceptionMethod;
use NPX\JpegXlEncode\Method\ImagickMethod;
use NPX\JpegXlEncode\Method\VipsMethod;
use Symfony\Component\Process\Process;
use Respect\Validation\Validator as v;

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
            ],
            '_methods' => [
                'cjxl_binary',
                'cjxl_system_binary',
                'vips',
                'imagick',
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

        //self::debug($options);

        self::validateOptions($options);

        $success = false;

        foreach ($options['_methods'] as $methodName) {
            try {
                $methodClass = self::getClassForMethodName($methodName);
                if (!$methodClass::isAvailable()) {
                    throw new MethodUnavailableException();
                }
                // Try encoding using method
                $methodClass::encode($source, $destination, $options);
                $success = true;
            } catch (\Exception $exception) {
                // If failed, try next method
                continue;
            }
            // If success, break out of look
            break;
        }

        if (!$success) {
            // TODO: show better message from method
            throw new \Exception('None of the methods succeeded');
        }
    }

    public static function debug(...$params) {
        if (function_exists('codecept_debug')) {
            foreach ($params as $param) {
                codecept_debug($param);
            }
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

    /**
     * @param $methodName
     * @return Method\Method
     * @throws \Exception
     */
    private static function getClassForMethodName($methodName)
    {
        if ($methodName === 'cjxl_binary') {
            return new CjxlBinaryMethod();
        }
        if ($methodName === 'cjxl_system_binary') {
            return new CjxlSystemBinaryMethod();
        }
        if ($methodName === 'imagick') {
            return new ImagickMethod();
        }
        if ($methodName === 'vips') {
            return new VipsMethod();
        }
        if ($methodName === 'dummy_not_available') {
            return new DummyThrowsExceptionMethod();
        }
        if ($methodName === 'dummy_throws_exception') {
            return new DummyThrowsExceptionMethod();
        }
        throw new \Exception("Could not find class for method $methodName");
    }

}
