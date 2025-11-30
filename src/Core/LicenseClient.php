<?php

namespace Wijhat\LicenseManager\Core;

use Wijhat\LicenseManager\Helpers\Fingerprint;

class LicenseClient
{
    public static function postVerify($server, $license, $domain)
    {
        $server = rtrim($server, '/');

        $payload = json_encode([
            'license_key' => $license,
            'domain'      => $domain,
            'fingerprint' => Fingerprint::generate(),
        ]);

        $ctx = stream_context_create([
            'http' => [
                'method'  => 'POST',
                'header'  => "Content-Type: application/json\r\n",
                'content' => $payload,
                'timeout' => 6,
            ]
        ]);

        $response = @file_get_contents("{$server}/verify-license", false, $ctx);

        if (!$response) {
            return "License server unreachable.";
        }

        $json = json_decode($response, true);

        return ($json['status'] ?? false)
            ? true
            : ($json['message'] ?? "Invalid license.");
    }

    public static function quickVerify($server, $license, $domain)
    {
        $server = rtrim($server, '/');

        $fp = Fingerprint::generate();

        $url = "{$server}/quick-verify?license_key={$license}&domain={$domain}&fingerprint={$fp}";

        $response = @file_get_contents($url);

        if (!$response) return "License server unreachable.";

        return trim($response) === "VALID" ? true : "Invalid quick check.";
    }
}
