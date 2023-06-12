<?php

// SPDX-FileCopyrightText: 2021 Johannes Siipola
// SPDX-License-Identifier: MIT

namespace unit;
class SystemBinaryTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    protected function _before()
    {
        if (!\NPX\JpegXlEncode\Method\CjxlSystemBinaryMethod::isAvailable()) {
            $this->markTestSkipped('Method not available');
        }
    }

    protected function _after()
    {
    }

    public function testDefaultSettingsJpeg()
    {
        $source = __DIR__ . '/../_data/broadway-tower-edit/broadway-tower-edit.jpg';
        $destination = __DIR__ . '/../_output/testDefaultSettingsJpegSystem3.jxl';
        $comparison_image = __DIR__ . '/../_output/broadway-tower-edit-linux-v0-5-0-quality-85-mode-vardct-effort-7-progressive-true-system3.jxl';

        $binary = \NPX\JpegXlEncode\Method\CjxlSystemBinaryMethod::getBinaryPath();

        codecept_debug("$binary $source $comparison_image --quality 85 --effort 7 --progressive --lossless_jpeg=0");

        $this->tester->runShellCommand("$binary $source $comparison_image --quality 85 --effort 7 --progressive --lossless_jpeg=0");

        $result = __DIR__ . '/../_data/broadway-tower-edit-linux-v0-5-0-quality-85-mode-vardct-effort-7-progressive-true-system3.jxl';
        \NPX\JpegXlEncode\Encoder::encode($source, $destination,
        [
            '_methods' => ['cjxl_system_binary']
        ]);
        $this->assertEquals(md5_file($destination), md5_file($comparison_image));
    }

    public function testDefaultSettingsPng()
    {
        $source = __DIR__ . '/../_data/jpeg-xl-logo/jpeg-xl-logo.png';
        $destination = __DIR__ . '/../_output/testDefaultSettingsPngSystem3.jxl';

        $comparison_image = __DIR__ . '/../_output/jpeg-xl-logo-v0-5-0-mode-modular-quality-100-effort-7-progressive-false-system3.jxl';

        $binary = \NPX\JpegXlEncode\Method\CjxlSystemBinaryMethod::getBinaryPath();

        $this->tester->runShellCommand("$binary $source $comparison_image --modular=1 --quality 100 --effort 7");

        \NPX\JpegXlEncode\Encoder::encode($source, $destination, [
            '_methods' => ['cjxl_system_binary']
        ]);
        $this->assertEquals(md5_file($destination), md5_file($comparison_image));
    }

    public function test90SettingJpeg()
    {
        $source = __DIR__ . '/../_data/broadway-tower-edit/broadway-tower-edit.jpg';
        $destination = __DIR__ . '/../_output/test90SettingJpegSystem3.jxl';

        $comparison_image = __DIR__ . '/../_output/broadway-tower-edit-linux-v0-5-0-quality-90-mode-vardct-effort-7-progressive-true-system3.jxl';

        $binary = \NPX\JpegXlEncode\Method\CjxlSystemBinaryMethod::getBinaryPath();

        $this->tester->runShellCommand("$binary \"$source\" \"$comparison_image\" --quality 90 --effort 7 --progressive --lossless_jpeg=0");

        \NPX\JpegXlEncode\Encoder::encode($source, $destination, [
            'quality' => 90,
            '_methods' => ['cjxl_system_binary']
        ]);
        $this->assertEquals(md5_file($destination), md5_file($comparison_image));
    }

    public function testNonImageFile()
    {
        $this->tester->expectThrowable(new \Exception('Invalid MIME type. Must be one of the following: image/jpeg, image/png.'), function () {
            $source = __DIR__ . '/../_data/text.txt';
            $destination = __DIR__ . '/../_output/testNonImageFile.jxl';
            \NPX\JpegXlEncode\Encoder::encode($source, $destination);
        });
    }

    public function testNonExistingImageFile()
    {
        $this->tester->expectThrowable(new \Exception('File does not exist.'), function () {
            $source = __DIR__ . '/../_data/asdfg.qwerty';
            $destination = __DIR__ . '/../_output/testNonExistingImageFile.jxl';
            \NPX\JpegXlEncode\Encoder::encode($source, $destination);
        });
    }

    public function testPngToLossy()
    {
        $source = __DIR__ . '/../_data/jpeg-xl-logo/jpeg-xl-logo.png';
        $destination = __DIR__ . '/../_output/testPngToLossySystem3.jxl';


        $comparison_image = __DIR__ . '/../_output/jpeg-xl-logo-mac-v0-5-0-quality-85-mode-vardct-effort-7-progressive-true-system3.jxl';

        $binary = \NPX\JpegXlEncode\Method\CjxlBinaryMethod::getBinaryPath();


        $this->tester->runShellCommand("$binary $source $comparison_image --quality 85 --effort 7 --progressive");

        $options = [
            'encoding' => 'lossy',
            '_methods' => ['cjxl_system_binary']
        ];
        \NPX\JpegXlEncode\Encoder::encode($source, $destination, $options);
        $this->assertEquals(md5_file($destination), md5_file($comparison_image));
    }

    public function testCantSetQualityInModular()
    {
        $source = __DIR__ . '/../_data/jpeg-xl-logo/jpeg-xl-logo.png';
        $destination = __DIR__ . '/../_output/testDefaultSettingsPngSystem3.jxl';

        $comparison_image = __DIR__ . '/../_output/jpeg-xl-logo-mac-v0-5-0-mode-modular-effort-7-progressive-false-system3.jxl';

        $binary = \NPX\JpegXlEncode\Method\CjxlBinaryMethod::getBinaryPath();

        $this->tester->runShellCommand("$binary $source $comparison_image --modular=1 --quality 100 --effort 7");

        $options = [
            'encoding' => 'lossless',
            'quality' => 50,
            '_methods' => ['cjxl_system_binary']
        ];

        \NPX\JpegXlEncode\Encoder::encode($source, $destination, $options);
        $this->assertEquals(md5_file($destination), md5_file($comparison_image));
    }

}
