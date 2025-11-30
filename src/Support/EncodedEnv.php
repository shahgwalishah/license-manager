<?php

namespace Illuminates\Framework\Support;

class EncodedEnv
{
    public static function get($encoded)
    {
        $key = base64_decode($encoded);
        return $_ENV[$key] ?? getenv($key) ?? null;
    }
}
