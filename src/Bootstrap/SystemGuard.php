<?php

namespace Wijhat\LicenseManager\Bootstrap;

use Illuminate\Support\Facades\Cache;
use Wijhat\LicenseManager\Core\LicenseClient;
use Wijhat\LicenseManager\Support\EncodedEnv;

class SystemGuard
{
    public static function check()
    {
        if (php_sapi_name() === 'cli') return;

        // Encoded ENV keys (base64)
        $license = EncodedEnv::get('QVBQX0xDSw==');
        $server  = EncodedEnv::get('QVBQX0xDUw==');

        $domain  = self::cleanDomain($_SERVER['HTTP_HOST'] ?? '');

        if (!$license || !$server || !$domain) {
            self::block("License configuration missing.");
        }

        $cacheKey = 'lc_' . md5($license . $domain);
        $ttl = config('license-manager.cache_seconds', 30);

        $result = Cache::remember($cacheKey, $ttl, function () use ($server, $license, $domain) {
            $post  = LicenseClient::postVerify($server, $license, $domain);
            if ($post === true) return true;

            $quick = LicenseClient::quickVerify($server, $license, $domain);
            if ($quick === true) return true;

            return $post ?: $quick;
        });

        if ($result !== true) {
            self::block($result);
        }
    }

    private static function cleanDomain($host)
    {
        return parse_url("http://{$host}", PHP_URL_HOST);
    }

    private static function block($msg)
    {
        http_response_code(403);

        echo "<h2 style='font-family:Arial;color:#c00;text-align:center;margin-top:60px;'>License Error</h2>";
        echo "<p style='text-align:center;font-family:Arial;color:#555;'>{$msg}</p>";

        die();
    }
}
