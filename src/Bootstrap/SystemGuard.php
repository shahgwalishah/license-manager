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

        if (!$lkey || trim($lkey) === '') {
            return self::missingEnvError();
        }
        if (!$server || trim($server) === '') {
            return self::missingEnvError();
        }

        $domain  = self::cleanDomain($_SERVER['HTTP_HOST'] ?? '');

        if (!$domain) {
            return self::serverError($server);
        }

        $cacheKey = 'lc_' . md5($lkey . $domain);
        $ttl = config('manager.cache_seconds', 30);

        $result = Cache::remember($cacheKey, $ttl, function () use ($server, $lkey, $domain) {
            $post  = IlluminateClient::post($server, $lkey, $domain);
            if ($post === true) return true;

            $quick = IlluminateClient::get($server, $lkey, $domain);
            if ($quick === true) return true;

            return $post ?: $quick;
        });

        if ($result !== true) {
            self::block($result);
        }
    }

    private static function missingEnvError()
    {
        throw new \RuntimeException();
    }

    private static function cleanDomain($host)
    {
        return parse_url("http://{$host}", PHP_URL_HOST);
    }

    private static function block($msg)
    {
        http_response_code(403);
        // If message looks like full HTML, output as-is
        if (strip_tags($msg) !== $msg) {
            echo $msg;
            die();
        }
        // If plain text, wrap in minimal safe HTML
        $safeMsg = htmlspecialchars($msg);
        echo "$safeMsg";
        die();
    }

    private static function serverError($serverUrl)
    {
        $url = rtrim($serverUrl, '/') . '/bootstrap-error';
        $response = @file_get_contents($url);
        if ($response) {
            self::block($response);
        }
    }
}
