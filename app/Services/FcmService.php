<?php

namespace App\Services;

use Google\Client as GoogleClient;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FcmService
{
    public static function send($token, $title, $body, $data = [])
    {
        if (empty($token)) {
            Log::warning('FCM token missing, notification skipped');
            return null;
        }

        try {
            $client = new GoogleClient();
            $client->setAuthConfig(config('services.fcm.credentials'));
            $client->addScope('https://www.googleapis.com/auth/firebase.messaging');

            $tokenData = $client->fetchAccessTokenWithAssertion();

            if (isset($tokenData['error'])) {
                throw new \Exception(json_encode($tokenData));
            }

            $projectId = config('services.fcm.project_id');

            $url = "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send";

            $response = Http::withToken($tokenData['access_token'])
                ->post($url, [
                    'message' => [
                        'token' => $token,
                        'notification' => [
                            'title' => $title,
                            'body'  => $body,
                        ],
                        'data' => array_map('strval', $data),
                    ],
                ]);

            if ($response->failed()) {
                Log::error('FCM SEND FAILED', [
                    'response' => $response->body(),
                ]);
            }

            return $response->json();

        } catch (\Throwable $e) {
            Log::error('FCM EXCEPTION', [
                'error' => $e->getMessage(),
            ]);
            Log::info('FCM PROJECT CHECK', [
    'config_project_id' => config('services.fcm.project_id'),
    'json_project_id' => json_decode(
        file_get_contents(config('services.fcm.credentials')),
        true
    )['project_id'] ?? null,
]);

        }
    }
}
