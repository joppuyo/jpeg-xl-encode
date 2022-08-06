<?php

// SPDX-FileCopyrightText: 2021 Johannes Siipola
// SPDX-License-Identifier: MIT

class ValidationTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    protected function _before()
    {
        $binary = \NPX\JpegXlEncode\Method\CjxlBinaryMethod::getBinaryPath();
        \NPX\JpegXlEncode\Method\CjxlBinaryMethod::ensurePermissions($binary);
    }

    protected function _after()
    {
    }

    public function testCantSetInvalidQuality()
    {
        $this->tester->expectThrowable(\NPX\JpegXlEncode\Exception\InvalidArgumentException::class, function () {
            $source = __DIR__ . '/../_data/jpeg-xl-logo/jpeg-xl-logo.png';
            $destination = __DIR__ . '/../_output/testCantSetInvalidQuality.jxl';
            $options = [
                'encoding' => 'lossy',
                'quality' => 'asdf',
            ];
            \NPX\JpegXlEncode\Encoder::encode($source, $destination, $options);
        });

        $this->tester->expectThrowable(\NPX\JpegXlEncode\Exception\InvalidArgumentException::class, function () {
            $source = __DIR__ . '/../_data/jpeg-xl-logo/jpeg-xl-logo.png';
            $destination = __DIR__ . '/../_output/testCantSetInvalidQuality.jxl';
            $options = [
                'encoding' => 'lossy',
                'quality' => 123,
            ];
            \NPX\JpegXlEncode\Encoder::encode($source, $destination, $options);
        });

        $this->tester->expectThrowable(\NPX\JpegXlEncode\Exception\InvalidArgumentException::class, function () {
            $source = __DIR__ . '/../_data/jpeg-xl-logo/jpeg-xl-logo.png';
            $destination = __DIR__ . '/../_output/testCantSetInvalidQuality.jxl';
            $options = [
                'encoding' => 'lossy',
                'quality' => -1,
            ];
            \NPX\JpegXlEncode\Encoder::encode($source, $destination, $options);
        });
    }

    public function testCantSetInvalidEncoding()
    {
        $this->tester->expectThrowable(\NPX\JpegXlEncode\Exception\InvalidArgumentException::class, function () {
            $source = __DIR__ . '/../_data/jpeg-xl-logo/jpeg-xl-logo.png';
            $destination = __DIR__ . '/../_output/testCantSetInvalidQuality.jxl';
            $options = [
                'encoding' => 'asdf',
            ];
            \NPX\JpegXlEncode\Encoder::encode($source, $destination, $options);
        });
    }

    public function testCantSetInvalidProgressive()
    {
        $this->tester->expectThrowable(\NPX\JpegXlEncode\Exception\InvalidArgumentException::class, function () {
            $source = __DIR__ . '/../_data/jpeg-xl-logo/jpeg-xl-logo.png';
            $destination = __DIR__ . '/../_output/testCantSetInvalidQuality.jxl';
            $options = [
                'progressive' => 'asdf',
            ];
            \NPX\JpegXlEncode\Encoder::encode($source, $destination, $options);
        });

        $this->tester->expectThrowable(\NPX\JpegXlEncode\Exception\InvalidArgumentException::class, function () {
            $source = __DIR__ . '/../_data/jpeg-xl-logo/jpeg-xl-logo.png';
            $destination = __DIR__ . '/../_output/testCantSetInvalidQuality.jxl';
            $options = [
                'progressive' => 1,
            ];
            \NPX\JpegXlEncode\Encoder::encode($source, $destination, $options);
        });

        $this->tester->expectThrowable(\NPX\JpegXlEncode\Exception\InvalidArgumentException::class, function () {
            $source = __DIR__ . '/../_data/jpeg-xl-logo/jpeg-xl-logo.png';
            $destination = __DIR__ . '/../_output/testCantSetInvalidQuality.jxl';
            $options = [
                'progressive' => 123,
            ];
            \NPX\JpegXlEncode\Encoder::encode($source, $destination, $options);
        });
    }

    public function testCantSetInvalidEffort()
    {
        $this->tester->expectThrowable(\NPX\JpegXlEncode\Exception\InvalidArgumentException::class, function () {
            $source = __DIR__ . '/../_data/jpeg-xl-logo/jpeg-xl-logo.png';
            $destination = __DIR__ . '/../_output/testCantSetInvalidQuality.jxl';
            $options = [
                'effort' => 'asdf',
            ];
            \NPX\JpegXlEncode\Encoder::encode($source, $destination, $options);
        });
        $this->tester->expectThrowable(\NPX\JpegXlEncode\Exception\InvalidArgumentException::class, function () {
            $source = __DIR__ . '/../_data/jpeg-xl-logo/jpeg-xl-logo.png';
            $destination = __DIR__ . '/../_output/testCantSetInvalidQuality.jxl';
            $options = [
                'effort' => 10,
            ];
            \NPX\JpegXlEncode\Encoder::encode($source, $destination, $options);
        });
        $this->tester->expectThrowable(\NPX\JpegXlEncode\Exception\InvalidArgumentException::class, function () {
            $source = __DIR__ . '/../_data/jpeg-xl-logo/jpeg-xl-logo.png';
            $destination = __DIR__ . '/../_output/testCantSetInvalidQuality.jxl';
            $options = [
                'effort' => -1,
            ];
            \NPX\JpegXlEncode\Encoder::encode($source, $destination, $options);
        });
    }

    public function testAbsolutePathValidation()
    {
        $absolute = [
            '//server/file',
            '\\\\server\\file',
            'C:/Users/',
            'C:\\Users\\',
        ];

        foreach ($absolute as $path) {

            codecept_debug($path);

            $this->tester->assertEquals(
                \NPX\JpegXlEncode\Encoder::isAbsolutePath(
                    $path,
                    'Windows'
                ),
                true
            );
        }

        $invalid = [
            'C:cwd/another',
            'C:cwd\\another',
            'directory/directory',
            'directory\\directory',
        ];

        foreach ($invalid as $path) {

            codecept_debug($path);

            $this->tester->assertEquals(
                \NPX\JpegXlEncode\Encoder::isAbsolutePath(
                    $path,
                    'Windows'
                ),
                false
            );
        }

        $absolute = [
            '/home/foo',
            '/home/foo/..',
        ];

        foreach ($absolute as $path) {

            codecept_debug($path);

            $this->tester->assertEquals(
                \NPX\JpegXlEncode\Encoder::isAbsolutePath(
                    $path,
                    'Linux'
                ),
                true
            );
        }

        $invalid = [
            'C:cwd/another',
            'C:cwd\\another',
            'directory/directory',
            'directory\\directory',
        ];

        foreach ($invalid as $path) {

            codecept_debug($path);

            $this->tester->assertEquals(
                \NPX\JpegXlEncode\Encoder::isAbsolutePath(
                    $path,
                    'Linux'
                ),
                false
            );
        }

    }
}
