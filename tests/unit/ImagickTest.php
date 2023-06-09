<?php

// SPDX-FileCopyrightText: 2021 Johannes Siipola
// SPDX-License-Identifier: MIT

class ImagickTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    protected function _before()
    {
        if (!\NPX\JpegXlEncode\Method\ImagickMethod::isAvailable()) {
            $this->markTestSkipped('Method not available');
        }
        $binary = \NPX\JpegXlEncode\Method\CjxlBinaryMethod::getBinaryPath();
        \NPX\JpegXlEncode\Method\CjxlBinaryMethod::ensurePermissions($binary);
    }

    protected function _after()
    {
    }

    public function test90SettingJpeg()
    {
        $source = __DIR__ . '/../_data/broadway-tower-edit/broadway-tower-edit.jpg';
        $destination = __DIR__ . '/../_output/test90SettingJpegImagick.jxl';

        $comparison_image = __DIR__ . '/../_output/broadway-tower-edit-imagick-quality-90-mode-vardct-effort-7-progressive-false.jxl';

        $this->tester->runShellCommand("convert -quality 90 -set jxl:effort 7 \"$source\" \"$comparison_image\"");

        \NPX\JpegXlEncode\Encoder::encode(
            $source,
            $destination,
            [
                'encoding' => 'lossy',
                'quality' => 90,
                'effort' => 7,
                '_methods' => ['imagick'],
            ]
        );

        $this->assertEquals(md5_file($destination), md5_file($comparison_image));
    }

    public function testDefaultSettingsPng()
    {
        $source = __DIR__ . '/../_data/jpeg-xl-logo/jpeg-xl-logo.png';

        $destination = __DIR__ . '/../_output/testDefaultSettingsPngImagick.jxl';

        $comparison_image = __DIR__ . '/../_output/jpeg-xl-logo-imagick-mode-modular-quality-100-effort-7-progressive-false.jxl';

        $binary = \NPX\JpegXlEncode\Method\CjxlBinaryMethod::getBinaryPath();

        $this->tester->runShellCommand("convert -quality 100 -set jxl:effort 7 \"$source\" \"$comparison_image\"");

        \NPX\JpegXlEncode\Encoder::encode(
            $source,
            $destination,
            [
                '_methods' => [
                    'imagick'
                ]
            ]
        );

        $this->assertEquals(md5_file($destination), md5_file($comparison_image));
    }

}
