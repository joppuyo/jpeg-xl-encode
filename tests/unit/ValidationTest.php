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
        $binary = \Joppuyo\JpegXlEncode\Encoder::getBinaryPath();
        \Joppuyo\JpegXlEncode\Encoder::ensure_permissions($binary);
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
}
