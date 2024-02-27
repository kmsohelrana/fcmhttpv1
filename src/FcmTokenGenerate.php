<?php

namespace Kmsohelrana\Fcmhttpv1;

use GuzzleHttp\Client;
class FcmTokenGenerate
{

    // Helpers

    const CLOUD_PLATFORM = "https://www.googleapis.com/auth/firebase.messaging";
    public static function generateAccessToken()
    {
        $data["access_token"] = null;

        $fcm_json = file_get_contents(config('fcm_config.fcm_json_path'));

        $authConfig = json_decode($fcm_json, true);

        $secret = openssl_get_privatekey($authConfig['private_key']);

        $header = json_encode([
            'typ' => 'JWT',
            'alg' => 'RS256'
        ]);

        $timestamp = time();

        $payload = json_encode([
            "iss" => $authConfig['client_email'],
            "scope" => self::CLOUD_PLATFORM,
            "aud" => $authConfig['token_uri'],
            "exp" => $timestamp + 3600,
            "iat" => $timestamp
        ]);

        $base64UrlHeader = self::base64UrlEncode($header);
        $base64UrlPayload = self::base64UrlEncode($payload);

        openssl_sign($base64UrlHeader . "." . $base64UrlPayload, $signature, $secret, OPENSSL_ALGO_SHA256);

        $base64UrlSignature = self::base64UrlEncode($signature);

        $jwt = $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;

        $client = new Client();

        $response = $client->post($authConfig['token_uri'], [
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/x-www-form-urlencoded',
            ],
            'form_params' => [
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion' => $jwt,
            ],
        ]);

        if ($response->getStatusCode() == 200) {
            $data["access_token"] = json_decode($response->getBody(), true)['access_token'];
        }

        return $data["access_token"];
    }

    protected static function base64UrlEncode(string $data): string
    {
        $base64Url = strtr(base64_encode($data), '+/', '-_');

        return rtrim($base64Url, '=');
    }

    protected static function base64UrlDecode(string $base64Url): string
    {
        return base64_decode(strtr($base64Url, '-_', '+/'));
    }
}
