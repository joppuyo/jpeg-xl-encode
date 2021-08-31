<?php

class ExampleTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;
    
    protected function _before()
    {
    }

    protected function _after()
    {
    }

    public function testDefaultSettingsJpeg()
    {
        $source = __DIR__ . '/../_data/broadway-tower-edit.jpg';
        $destination = __DIR__ . '/../_output/testDefaultSettingsJpeg.jxl';
        $result = __DIR__ . '/../_data/broadway-tower-edit-linux-v0-5-0-quality-85-mode-vardct-effort-7-progressive-true.jxl';
        \Joppuyo\JpegXlEncode\Encoder::encode($source, $destination);
        $this->assertEquals(md5_file($destination), md5_file($result));
    }

    public function testDefaultSettingsPng()
    {
        $source = __DIR__ . '/../_data/jpeg-xl-logo.png';
        $destination = __DIR__ . '/../_output/testDefaultSettingsPng.jxl';
        $result = __DIR__ . '/../_data/jpeg-xl-logo-mac-v0-5-0-mode-modular-effort-7-progressive-false.jxl';
        \Joppuyo\JpegXlEncode\Encoder::encode($source, $destination);
        $this->assertEquals(md5_file($destination), md5_file($result));
    }

    public function test90SettingJpeg()
    {
        $source = __DIR__ . '/../_data/broadway-tower-edit.jpg';
        $destination = __DIR__ . '/../_output/test90SettingJpeg.jxl';
        $result = __DIR__ . '/../_data/broadway-tower-edit-linux-v0-5-0-quality-90-mode-vardct-effort-7-progressive-true.jxl';
        \Joppuyo\JpegXlEncode\Encoder::encode($source, $destination, [
            'quality' => 90,
        ]);
        $this->assertEquals(md5_file($destination), md5_file($result));
    }

    /*public function testFormatOptionsOverrideDefaultOptions()
    {
        $source = __DIR__ . '/../_data/broadway-tower-edit.jpg';
        $destination = __DIR__ . '/../_output/testFormatOptionsOverrideDefaultOptions.jxl';
        $result = __DIR__ . '/../_data/broadway-tower-edit-mac-v0-5-0-quality-90-mode-vardct-effort-7-progressive-true.jxl';
        \Joppuyo\JpegXlEncode\Encoder::encode($source, $destination, [
            'quality' => 10,
            'lossy' => [
                'quality' => 90,
            ]
        ]);
        $this->assertEquals(md5_file($destination), md5_file($result));
    }*/

    public function testNonImageFile()
    {
        $this->tester->expectThrowable(new \Exception('Invalid MIME type. Must be one of the following: image/jpeg, image/png.'), function () {
            $source = __DIR__ . '/../_data/text.txt';
            $destination = __DIR__ . '/../_output/testNonImageFile.jxl';
            \Joppuyo\JpegXlEncode\Encoder::encode($source, $destination);
        });
    }

    public function testNonExistingImageFile()
    {
        $this->tester->expectThrowable(new \Exception('File does not exist.'), function () {
            $source = __DIR__ . '/../_data/asdfg.qwerty';
            $destination = __DIR__ . '/../_output/testNonExistingImageFile.jxl';
            \Joppuyo\JpegXlEncode\Encoder::encode($source, $destination);
        });
    }

    public function testPngToLossy()
    {
        $source = __DIR__ . '/../_data/jpeg-xl-logo.png';
        $destination = __DIR__ . '/../_output/testPngToLossy.jxl';
        $result = __DIR__ . '/../_data/jpeg-xl-logo-mac-v0-5-0-quality-85-mode-vardct-effort-7-progressive-true.jxl';
        $options = [
            'encoding' => 'lossy',
        ];
        \Joppuyo\JpegXlEncode\Encoder::encode($source, $destination, $options);
        $this->assertEquals(md5_file($destination), md5_file($result));
    }

    public function testCantSetQualityInModular()
    {
        $source = __DIR__ . '/../_data/jpeg-xl-logo.png';
        $destination = __DIR__ . '/../_output/testDefaultSettingsPng.jxl';
        $result = __DIR__ . '/../_data/jpeg-xl-logo-mac-v0-5-0-mode-modular-effort-7-progressive-false.jxl';

        $options = [
            'encoding' => 'lossless',
            'quality' => 50,
        ];

        \Joppuyo\JpegXlEncode\Encoder::encode($source, $destination, $options);
        $this->assertEquals(md5_file($destination), md5_file($result));
    }
}
