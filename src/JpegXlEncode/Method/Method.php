<?php

// SPDX-FileCopyrightText: 2021 Johannes Siipola
// SPDX-License-Identifier: MIT

namespace NPX\JpegXlEncode\Method;

interface Method {
    public static function isAvailable();
    public static function encode(string $source, string $destination, array $options = []);
}
