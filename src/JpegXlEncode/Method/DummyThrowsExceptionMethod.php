<?php

// SPDX-FileCopyrightText: 2021 Johannes Siipola
// SPDX-License-Identifier: MIT

namespace NPX\JpegXlEncode\Method;

class DummyThrowsExceptionMethod implements Method
{
    public static function isAvailable()
    {
        return true;
    }

    public static function encode(string $source, string $destination, array $options = [])
    {
        throw new \Exception('Something went wrong');
    }

}