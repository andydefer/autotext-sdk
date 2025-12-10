<?php

namespace Andydefer\AutotextSdk\Services;

class FirebaseAuthProvider
{
    /**
     * Retourne un access token OAuth2 pour Firebase
     */
    public function getAccessToken(array $config): string
    {
        $header = base64_encode(json_encode(['alg' => 'RS256', 'typ' => 'JWT']));
        $now = time();
        $claimSet = base64_encode(json_encode([
            'iss' => $config['client_email'],
            'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
            'aud' => $config['token_uri'],
            'iat' => $now,
            'exp' => $now + 3600,
        ]));

        $signature = '';
        openssl_sign($header . '.' . $claimSet, $signature, $config['private_key'], 'SHA256');
        $jwt = $header . '.' . $claimSet . '.' . base64_encode($signature);

        $client = new \GuzzleHttp\Client();
        $res = $client->post($config['token_uri'], [
            'form_params' => [
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion' => $jwt,
            ],
        ]);

        $resData = json_decode($res->getBody()->getContents(), true);

        return $resData['access_token'] ?? '';
    }
}
