<?php

namespace Joppuyo\JpegXlEncode\Method;

class DummyThrowsExceptionMethod implements Method
{
    public static function isAvailable()
    {
        return true;
    }

    public static function getSupportedFeatures(){}

    public static function encode(string $source, string $destination, array $options = [])
    {
        throw new \Exception('Something went wrong');
    }

}