<?php

class VipsTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    protected function _before()
    {
        if (!\Joppuyo\JpegXlEncode\Method\VipsMethod::isAvailable()) {
            $this->markTestSkipped('Method not available');
        }
        $binary = \Joppuyo\JpegXlEncode\Encoder::getBinaryPath();
        \Joppuyo\JpegXlEncode\Encoder::ensurePermissions($binary);
    }

    protected function _after()
    {
    }

    public function test90SettingJpeg()
    {
        $source = __DIR__ . '/../_data/broadway-tower-edit.jpg';
        $destination = __DIR__ . '/../_output/test90SettingJpegVips.jxl';

        $comparison_image = __DIR__ . '/../_output/broadway-tower-edit-linux-v0-5-0-quality-90-mode-vardct-effort-7-progressive-false.jxl';

        $binary = \Joppuyo\JpegXlEncode\Encoder::getBinaryPath();

        $this->tester->runShellCommand("vips jxlsave \"$source\" \"$comparison_image\" --Q 90 --effort=7");

        \Joppuyo\JpegXlEncode\Method\VipsMethod::encode(
            $source,
            $destination,
            [
                'encoding' => 'lossy',
                'quality' => 90,
            ]
        );

        $this->assertEquals(md5_file($destination), md5_file($comparison_image));
    }

    public function testDefaultSettingsPng()
    {
        $source = __DIR__ . '/../_data/jpeg-xl-logo.png';

        $destination = __DIR__ . '/../_output/testDefaultSettingsPngVips.jxl';

        $comparison_image = __DIR__ . '/../_output/jpeg-xl-logo-v0-5-0-mode-modular-quality-100-effort-7-progressive-false.jxl';

        $binary = \Joppuyo\JpegXlEncode\Encoder::getBinaryPath();

        $this->tester->runShellCommand("vips jxlsave \"$source\" \"$comparison_image\" --lossless --effort=7");

        \Joppuyo\JpegXlEncode\Method\VipsMethod::encode(
            $source,
            $destination,
            [
                'encoding' => 'lossless',
                'quality' => 100,
            ]
        );

        $this->assertEquals(md5_file($destination), md5_file($comparison_image));
    }

}