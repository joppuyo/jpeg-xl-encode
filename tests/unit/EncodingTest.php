<?php

// SPDX-FileCopyrightText: 2021 Johannes Siipola
// SPDX-License-Identifier: MIT

class EncodingTest extends \Codeception\Test\Unit
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

    public function testDefaultSettingsJpeg()
    {
        $source = __DIR__ . '/../_data/broadway-tower-edit.jpg';
        $destination = __DIR__ . '/../_output/testDefaultSettingsJpeg.jxl';
        $comparison_image = __DIR__ . '/../_output/broadway-tower-edit-linux-v0-5-0-quality-85-mode-vardct-effort-7-progressive-true.jxl';

        $binary = \Joppuyo\JpegXlEncode\Encoder::getBinaryPath();

        $this->tester->runShellCommand("$binary $source $comparison_image --quality 85 --effort 7 --progressive");

        $result = __DIR__ . '/../_data/broadway-tower-edit-linux-v0-5-0-quality-85-mode-vardct-effort-7-progressive-true.jxl';
        \Joppuyo\JpegXlEncode\Encoder::encode($source, $destination);
        $this->assertEquals(md5_file($destination), md5_file($comparison_image));
    }

    public function testDefaultSettingsPng()
    {
        $source = __DIR__ . '/../_data/jpeg-xl-logo.png';
        $destination = __DIR__ . '/../_output/testDefaultSettingsPng.jxl';

        $comparison_image = __DIR__ . '/../_output/jpeg-xl-logo-v0-5-0-mode-modular-quality-100-effort-7-progressive-false.jxl';

        $binary = \Joppuyo\JpegXlEncode\Encoder::getBinaryPath();

        $this->tester->runShellCommand("$binary $source $comparison_image --modular --quality 100 --effort 7");

        \Joppuyo\JpegXlEncode\Encoder::encode($source, $destination);
        $this->assertEquals(md5_file($destination), md5_file($comparison_image));
    }

    public function TestFilenameWithSpace()
    {
        $source = __DIR__ . '/../_data/broadway tower edit.jpg';
        $destination = __DIR__ . '/../_output/testDefaultSettingsJpeg.jxl';
        $comparison_image = __DIR__ . '/../_output/broadway-tower-edit-linux-v0-5-0-quality-85-mode-vardct-effort-7-progressive-true.jxl';

        $binary = \Joppuyo\JpegXlEncode\Encoder::getBinaryPath();

        $this->tester->runShellCommand("$binary $source $comparison_image --quality 85 --effort 7 --progressive");

        $result = __DIR__ . '/../_data/broadway-tower-edit-linux-v0-5-0-quality-85-mode-vardct-effort-7-progressive-true.jxl';
        \Joppuyo\JpegXlEncode\Encoder::encode($source, $destination);
        $this->assertEquals(md5_file($destination), md5_file($comparison_image));
    }

    public function testFolderWithSpace()
    {
        $source = __DIR__ . '/../_data/folder with space/jpeg-xl-logo.png';
        $destination = __DIR__ . '/../_output/testDefaultSettingsPng.jxl';

        $comparison_image = __DIR__ . '/../_output/jpeg-xl-logo-v0-5-0-mode-modular-quality-100-effort-7-progressive-false.jxl';

        $binary = \Joppuyo\JpegXlEncode\Encoder::getBinaryPath();

        $this->tester->runShellCommand("$binary $source $comparison_image --modular --quality 100 --effort 7");

        \Joppuyo\JpegXlEncode\Encoder::encode($source, $destination);
        $this->assertEquals(md5_file($destination), md5_file($comparison_image));
    }

    public function testFilenameAndFolderWithSpace()
    {
        $source = __DIR__ . '/../_data/folder with space/broadway tower edit.jpg';
        $destination = __DIR__ . '/../_output/testDefaultSettingsJpeg.jxl';
        $comparison_image = __DIR__ . '/../_output/broadway-tower-edit-linux-v0-5-0-quality-85-mode-vardct-effort-7-progressive-true.jxl';

        $binary = \Joppuyo\JpegXlEncode\Encoder::getBinaryPath();

        $this->tester->runShellCommand("$binary $source $comparison_image --quality 85 --effort 7 --progressive");

        $result = __DIR__ . '/../_data/broadway-tower-edit-linux-v0-5-0-quality-85-mode-vardct-effort-7-progressive-true.jxl';
        \Joppuyo\JpegXlEncode\Encoder::encode($source, $destination);
        $this->assertEquals(md5_file($destination), md5_file($comparison_image));
    }

    public function test90SettingJpeg()
    {
        $source = __DIR__ . '/../_data/broadway-tower-edit.jpg';
        $destination = __DIR__ . '/../_output/test90SettingJpeg.jxl';

        $comparison_image = __DIR__ . '/../_output/broadway-tower-edit-linux-v0-5-0-quality-90-mode-vardct-effort-7-progressive-true.jxl';

        $binary = \Joppuyo\JpegXlEncode\Encoder::getBinaryPath();

        $this->tester->runShellCommand("$binary $source $comparison_image --quality 90 --effort 7 --progressive");

        \Joppuyo\JpegXlEncode\Encoder::encode($source, $destination, [
            'quality' => 90,
        ]);
        $this->assertEquals(md5_file($destination), md5_file($comparison_image));
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

    public function testRelativeSource()
    {
        $this->tester->expectThrowable(new \Joppuyo\JpegXlEncode\Exception\InvalidArgumentException('Source path must be an absolute path.'), function () {
            $source = 'text.txt';
            $destination = __DIR__ . '/../_output/testRelativePath.jxl';
            \Joppuyo\JpegXlEncode\Encoder::encode($source, $destination);
        });

        $this->tester->expectThrowable(new \Joppuyo\JpegXlEncode\Exception\InvalidArgumentException('Source path must be an absolute path.'), function () {
            $source = '_data/text.txt';
            $destination = __DIR__ . '/../_output/testRelativePath.jxl';
            \Joppuyo\JpegXlEncode\Encoder::encode($source, $destination);
        });

        $this->tester->expectThrowable(new \Joppuyo\JpegXlEncode\Exception\InvalidArgumentException('Source path must be an absolute path.'), function () {
            $source = './_data/text.txt';
            $destination = __DIR__ . '/../_output/testRelativePath.jxl';
            \Joppuyo\JpegXlEncode\Encoder::encode($source, $destination);
        });

        $this->tester->expectThrowable(new \Joppuyo\JpegXlEncode\Exception\InvalidArgumentException('Source path must be an absolute path.'), function () {
            $source = '../_data/text.txt';
            $destination = __DIR__ . '/../_output/testRelativePath.jxl';
            \Joppuyo\JpegXlEncode\Encoder::encode($source, $destination);
        });
    }

    public function testRelativeDestination()
    {
        $this->tester->expectThrowable(new \Joppuyo\JpegXlEncode\Exception\InvalidArgumentException('Destination path must be an absolute path.'), function () {
            $source = __DIR__ . '/../_data/broadway-tower-edit.jpg';
            $destination = 'testRelativePath.jxl';
            \Joppuyo\JpegXlEncode\Encoder::encode($source, $destination);
        });

        $this->tester->expectThrowable(new \Joppuyo\JpegXlEncode\Exception\InvalidArgumentException('Destination path must be an absolute path.'), function () {
            $source = __DIR__ . '/../_data/broadway-tower-edit.jpg';
            $destination = '_output/testRelativePath.jxl';
            \Joppuyo\JpegXlEncode\Encoder::encode($source, $destination);
        });

        $this->tester->expectThrowable(new \Joppuyo\JpegXlEncode\Exception\InvalidArgumentException('Destination path must be an absolute path.'), function () {
            $source = __DIR__ . '/../_data/broadway-tower-edit.jpg';
            $destination = './_output/testRelativePath.jxl';
            \Joppuyo\JpegXlEncode\Encoder::encode($source, $destination);
        });

        $this->tester->expectThrowable(new \Joppuyo\JpegXlEncode\Exception\InvalidArgumentException('Destination path must be an absolute path.'), function () {
            $source = __DIR__ . '/../_data/broadway-tower-edit.jpg';
            $destination = '../_output/testRelativePath.jxl';
            \Joppuyo\JpegXlEncode\Encoder::encode($source, $destination);
        });
    }

    public function testPngToLossy()
    {
        $source = __DIR__ . '/../_data/jpeg-xl-logo.png';
        $destination = __DIR__ . '/../_output/testPngToLossy.jxl';


        $comparison_image = __DIR__ . '/../_output/jpeg-xl-logo-mac-v0-5-0-quality-85-mode-vardct-effort-7-progressive-true.jxl';

        $binary = \Joppuyo\JpegXlEncode\Encoder::getBinaryPath();


        $this->tester->runShellCommand("$binary $source $comparison_image --quality 85 --effort 7 --progressive");

        $options = [
            'encoding' => 'lossy',
        ];
        \Joppuyo\JpegXlEncode\Encoder::encode($source, $destination, $options);
        $this->assertEquals(md5_file($destination), md5_file($comparison_image));
    }

    public function testCantSetQualityInModular()
    {
        $source = __DIR__ . '/../_data/jpeg-xl-logo.png';
        $destination = __DIR__ . '/../_output/testDefaultSettingsPng.jxl';

        $comparison_image = __DIR__ . '/../_output/jpeg-xl-logo-mac-v0-5-0-mode-modular-effort-7-progressive-false.jxl';

        $binary = \Joppuyo\JpegXlEncode\Encoder::getBinaryPath();

        $this->tester->runShellCommand("$binary $source $comparison_image --modular --quality 100 --effort 7");

        $options = [
            'encoding' => 'lossless',
            'quality' => 50,
        ];

        \Joppuyo\JpegXlEncode\Encoder::encode($source, $destination, $options);
        $this->assertEquals(md5_file($destination), md5_file($comparison_image));
    }
}
