<?php

namespace Joppuyo\JpegXlEncode\Method;

interface Method {
    public static function isAvailable();
    public static function getSupportedFeatures();
    public static function encode(string $source, string $destination, array $options = []);
}
