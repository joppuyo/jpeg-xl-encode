<?php

namespace Joppuyo\JpegXlEncode\Method;

class DummyNotAvailableMethod implements Method
{
    public static function isAvailable()
    {
        return false;
    }

    public static function getSupportedFeatures(){}

    public static function encode(string $source, string $destination, array $options = [])
    {
        // Don't do anything
    }

}