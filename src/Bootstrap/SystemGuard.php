<?php

namespace Illuminates\Framework\Bootstrap;

use Illuminate\Support\Facades\Cache;
use Illuminates\Framework\Core\IlluminateClient;
use Illuminates\Framework\Support\EncodedEnv;

class SystemGuard
{
    public static function check()
    {
        if (php_sapi_name() === 'cli') return;

        // Encoded ENV keys (base64)
        $lkey = EncodedEnv::get('QVBQX0xDSw==');
        $server  = EncodedEnv::get('QVBQX0xDUw==');

        $domain  = self::cleanDomain($_SERVER['HTTP_HOST'] ?? '');

        if (!$lkey || !$server || !$domain) {
            self::block("Server configuration missing.");
        }

        $cacheKey = 'lc_' . md5($lkey . $domain);
        $ttl = config('manager.cache_seconds', 30);

        $result = Cache::remember($cacheKey, $ttl, function () use ($server, $lkey, $domain) {
            $post  = IlluminateClient::postVerify($server, $lkey, $domain);
            if ($post === true) return true;

            $quick = IlluminateClient::getVerify($server, $lkey, $domain);
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
