<?php

namespace Illuminates\Framework\Helpers;

class FP
{
    public static function generate()
    {
        $vendorPath = base_path('vendor/illuminates/framework/runtime');

        if (!is_dir($vendorPath)) {
            @mkdir($vendorPath, 0755, true);
        }

        $fpFile = $vendorPath . '/.fp';

        if (!file_exists($fpFile)) {
            file_put_contents($fpFile, bin2hex(random_bytes(32)));
        }

        $seed = trim(file_get_contents($fpFile));

        $domain = self::cleanDomain($_SERVER['HTTP_HOST'] ?? '');
        $appKey = config('app.key');
        $root   = base_path();
        $php    = PHP_VERSION;

        return hash('sha256', implode('|', [$domain, $appKey, $root, $php, $seed]));
    }

    private static function cleanDomain($host)
    {
        return parse_url("http://{$host}", PHP_URL_HOST);
    }
}
