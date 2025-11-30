<?php

namespace Illuminates\Framework\Core;

use Illuminates\Framework\Helpers\FP;

class IlluminateClient
{
    public static function postVerify($server, $lkey, $domain)
    {
        $server = rtrim($server, '/');

        $payload = json_encode([
            'lkey' => $lkey,
            'domain'      => $domain,
            'fp' => FP::generate(),
        ]);

        $ctx = stream_context_create([
            'http' => [
                'method'  => 'POST',
                'header'  => "Content-Type: application/json\r\n",
                'content' => $payload,
                'timeout' => 6,
            ]
        ]);

        $response = @file_get_contents("{$server}/verify", false, $ctx);

        if (!$response) {
            return $response;
        }

        $json = json_decode($response, true);

        return ($json['status'] ?? false)
            ? true
            : ($json['message'] ?? "");
    }

    public static function getVerify($server, $lkey, $domain)
    {
        $server = rtrim($server, '/');

        $fp = FP::generate();

        $url = "{$server}/get-verify?lkey={$lkey}&domain={$domain}&fp={$fp}";

        $response = @file_get_contents($url);

        if (!$response) return $response;

        return trim($response) === "VALID" ? true : "Invalid quick check.";
    }
}
