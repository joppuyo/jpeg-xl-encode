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
        $binary = \Joppuyo\JpegXlEncode\Method\CjxlBinaryMethod::getBinaryPath();
        \Joppuyo\JpegXlEncode\Method\CjxlBinaryMethod::ensurePermissions($binary);
    }

    protected function _after()
    {
    }

    public function testCantSetInvalidQuality()
    {
        $this->tester->expectThrowable(\Joppuyo\JpegXlEncode\Exception\InvalidArgumentException::class, function () {
            $source = __DIR__ . '/../_data/jpeg-xl-logo.png';
            $destination = __DIR__ . '/../_output/testCantSetInvalidQuality.jxl';
            $options = [
                'encoding' => 'lossy',
                'quality' => 'asdf',
            ];
            \Joppuyo\JpegXlEncode\Encoder::encode($source, $destination, $options);
        });

        $this->tester->expectThrowable(\Joppuyo\JpegXlEncode\Exception\InvalidArgumentException::class, function () {
            $source = __DIR__ . '/../_data/jpeg-xl-logo.png';
            $destination = __DIR__ . '/../_output/testCantSetInvalidQuality.jxl';
            $options = [
                'encoding' => 'lossy',
                'quality' => 123,
            ];
            \Joppuyo\JpegXlEncode\Encoder::encode($source, $destination, $options);
        });

        $this->tester->expectThrowable(\Joppuyo\JpegXlEncode\Exception\InvalidArgumentException::class, function () {
            $source = __DIR__ . '/../_data/jpeg-xl-logo.png';
            $destination = __DIR__ . '/../_output/testCantSetInvalidQuality.jxl';
            $options = [
                'encoding' => 'lossy',
                'quality' => -1,
            ];
            \Joppuyo\JpegXlEncode\Encoder::encode($source, $destination, $options);
        });
    }

    public function testCantSetInvalidEncoding()
    {
        $this->tester->expectThrowable(\Joppuyo\JpegXlEncode\Exception\InvalidArgumentException::class, function () {
            $source = __DIR__ . '/../_data/jpeg-xl-logo.png';
            $destination = __DIR__ . '/../_output/testCantSetInvalidQuality.jxl';
            $options = [
                'encoding' => 'asdf',
            ];
            \Joppuyo\JpegXlEncode\Encoder::encode($source, $destination, $options);
        });
    }

    public function testCantSetInvalidProgressive()
    {
        $this->tester->expectThrowable(\Joppuyo\JpegXlEncode\Exception\InvalidArgumentException::class, function () {
            $source = __DIR__ . '/../_data/jpeg-xl-logo.png';
            $destination = __DIR__ . '/../_output/testCantSetInvalidQuality.jxl';
            $options = [
                'progressive' => 'asdf',
            ];
            \Joppuyo\JpegXlEncode\Encoder::encode($source, $destination, $options);
        });

        $this->tester->expectThrowable(\Joppuyo\JpegXlEncode\Exception\InvalidArgumentException::class, function () {
            $source = __DIR__ . '/../_data/jpeg-xl-logo.png';
            $destination = __DIR__ . '/../_output/testCantSetInvalidQuality.jxl';
            $options = [
                'progressive' => 1,
            ];
            \Joppuyo\JpegXlEncode\Encoder::encode($source, $destination, $options);
        });

        $this->tester->expectThrowable(\Joppuyo\JpegXlEncode\Exception\InvalidArgumentException::class, function () {
            $source = __DIR__ . '/../_data/jpeg-xl-logo.png';
            $destination = __DIR__ . '/../_output/testCantSetInvalidQuality.jxl';
            $options = [
                'progressive' => 123,
            ];
            \Joppuyo\JpegXlEncode\Encoder::encode($source, $destination, $options);
        });
    }

    public function testCantSetInvalidEffort()
    {
        $this->tester->expectThrowable(\Joppuyo\JpegXlEncode\Exception\InvalidArgumentException::class, function () {
            $source = __DIR__ . '/../_data/jpeg-xl-logo.png';
            $destination = __DIR__ . '/../_output/testCantSetInvalidQuality.jxl';
            $options = [
                'effort' => 'asdf',
            ];
            \Joppuyo\JpegXlEncode\Encoder::encode($source, $destination, $options);
        });
        $this->tester->expectThrowable(\Joppuyo\JpegXlEncode\Exception\InvalidArgumentException::class, function () {
            $source = __DIR__ . '/../_data/jpeg-xl-logo.png';
            $destination = __DIR__ . '/../_output/testCantSetInvalidQuality.jxl';
            $options = [
                'effort' => 10,
            ];
            \Joppuyo\JpegXlEncode\Encoder::encode($source, $destination, $options);
        });
        $this->tester->expectThrowable(\Joppuyo\JpegXlEncode\Exception\InvalidArgumentException::class, function () {
            $source = __DIR__ . '/../_data/jpeg-xl-logo.png';
            $destination = __DIR__ . '/../_output/testCantSetInvalidQuality.jxl';
            $options = [
                'effort' => -1,
            ];
            \Joppuyo\JpegXlEncode\Encoder::encode($source, $destination, $options);
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
                \Joppuyo\JpegXlEncode\Encoder::isAbsolutePath(
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
                \Joppuyo\JpegXlEncode\Encoder::isAbsolutePath(
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
                \Joppuyo\JpegXlEncode\Encoder::isAbsolutePath(
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
                \Joppuyo\JpegXlEncode\Encoder::isAbsolutePath(
                    $path,
                    'Linux'
                ),
                false
            );
        }

    }
}
