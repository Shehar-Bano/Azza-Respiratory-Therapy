<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FirebaseService
{
    private ?string $serverKey;
    private ?string $projectId;
    private ?string $credentialsFile;

    public function __construct()
    {
        $this->serverKey = config('services.firebase.server_key');
        $this->projectId = config('services.firebase.project_id');
        $this->credentialsFile = config('services.firebase.credentials_file');
    }

    /**
     * Send Push Notification to a single FCM device token.
     */
    public function sendToToken(string $token, string $title, string $body, array $data = []): bool
    {
        if (empty($token)) {
            return false;
        }

        // 1. If legacy FCM server key is set
        if (!empty($this->serverKey)) {
            return $this->sendViaLegacyApi($token, $title, $body, $data);
        }

        // 2. If credentials JSON file exists or project ID is set, use HTTP v1 API
        if ($this->hasCredentialsFile() || !empty($this->projectId)) {
            return $this->sendViaHttpV1Api($token, $title, $body, $data);
        }

        Log::info('FCM Push Notification Simulated (No FCM Server Key or JSON Credentials configured)', [
            'token' => $token,
            'title' => $title,
            'body' => $body,
            'data' => $data,
        ]);

        return true;
    }

    /**
     * Check if JSON credentials file exists.
     */
    private function hasCredentialsFile(): bool
    {
        if (empty($this->credentialsFile)) {
            return false;
        }

        $path = base_path($this->credentialsFile);
        return file_exists($path) || file_exists($this->credentialsFile);
    }

    /**
     * Get absolute path of credentials file.
     */
    private function getCredentialsFilePath(): ?string
    {
        if (empty($this->credentialsFile)) {
            return null;
        }

        if (file_exists($this->credentialsFile)) {
            return $this->credentialsFile;
        }

        $basePath = base_path($this->credentialsFile);
        if (file_exists($basePath)) {
            return $basePath;
        }

        return null;
    }

    /**
     * Send via Legacy FCM HTTP API.
     */
    private function sendViaLegacyApi(string $token, string $title, string $body, array $data): bool
    {
        $response = Http::withHeaders([
            'Authorization' => 'key=' . $this->serverKey,
            'Content-Type' => 'application/json',
        ])->post('https://fcm.googleapis.com/fcm/send', [
            'to' => $token,
            'notification' => [
                'title' => $title,
                'body' => $body,
                'sound' => 'default',
            ],
            'data' => $data,
            'priority' => 'high',
        ]);

        if ($response->failed()) {
            Log::error('FCM Legacy API Request Failed', [
                'status' => $response->status(),
                'response' => $response->body(),
            ]);
            return false;
        }

        return true;
    }

    /**
     * Send via FCM HTTP v1 API.
     */
    private function sendViaHttpV1Api(string $token, string $title, string $body, array $data): bool
    {
        $credentials = $this->parseCredentialsFile();
        $projectId = $this->projectId 
            ?: ($credentials['project_id'] ?? null) 
            ?: ($credentials['project_info']['project_id'] ?? null);

        if (!$projectId) {
            Log::error('FCM HTTP v1: Missing project_id in .env or firebase_credentials.json.');
            return false;
        }

        $accessToken = $this->getAccessToken($credentials);
        if (!$accessToken) {
            Log::error('FCM HTTP v1: Failed to retrieve OAuth2 access token.');
            return false;
        }

        $url = sprintf('https://fcm.googleapis.com/v1/projects/%s/messages:send', $projectId);

        $response = Http::withToken($accessToken)
            ->withHeaders(['Content-Type' => 'application/json'])
            ->post($url, [
                'message' => [
                    'token' => $token,
                    'notification' => [
                        'title' => $title,
                        'body' => $body,
                    ],
                    'data' => array_map('strval', $data),
                ],
            ]);

        if ($response->failed()) {
            Log::error('FCM HTTP v1 Request Failed', [
                'status' => $response->status(),
                'response' => $response->body(),
            ]);
            return false;
        }

        return true;
    }

    /**
     * Parse credentials JSON file.
     */
    private function parseCredentialsFile(): ?array
    {
        $path = $this->getCredentialsFilePath();
        if (!$path) {
            return null;
        }

        try {
            $content = file_get_contents($path);
            return json_decode($content, true);
        } catch (\Throwable $e) {
            Log::error('FCM Credentials file read error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Obtain cached or fresh Google OAuth2 Access Token using JWT Service Account credentials.
     */
    private function getAccessToken(?array $credentials): ?string
    {
        if (!$credentials || !isset($credentials['private_key'], $credentials['client_email'])) {
            Log::error('FCM HTTP v1: The file in storage/app/firebase_credentials.json is a client (android google-services.json) file instead of a Firebase Service Account Key. Please download the Service Account Private Key JSON from Firebase Console > Project Settings > Service Accounts.');
            return null;
        }

        $cacheKey = 'fcm_google_access_token_' . md5($credentials['client_email']);

        return Cache::remember($cacheKey, 3300, function () use ($credentials) {
            $jwt = $this->createGoogleJwt($credentials['client_email'], $credentials['private_key']);
            if (!$jwt) {
                return null;
            }

            $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion' => $jwt,
            ]);

            if ($response->successful()) {
                return $response->json('access_token');
            }

            Log::error('Google OAuth2 Token Request Failed', [
                'status' => $response->status(),
                'response' => $response->body(),
            ]);

            return null;
        });
    }

    /**
     * Create JWT assertion signed with private key for Google OAuth2.
     */
    private function createGoogleJwt(string $clientEmail, string $privateKey): ?string
    {
        $header = json_encode(['alg' => 'RS256', 'typ' => 'JWT']);
        $now = time();
        $payload = json_encode([
            'iss' => $clientEmail,
            'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
            'aud' => 'https://oauth2.googleapis.com/token',
            'exp' => $now + 3600,
            'iat' => $now,
        ]);

        $base64UrlHeader = $this->base64UrlEncode($header);
        $base64UrlPayload = $this->base64UrlEncode($payload);

        $signatureInput = $base64UrlHeader . "." . $base64UrlPayload;

        $signature = '';
        $success = openssl_sign($signatureInput, $signature, $privateKey, 'sha256WithRSAEncryption');

        if (!$success) {
            Log::error('OpenSSL JWT signing failed for Firebase credentials.');
            return null;
        }

        $base64UrlSignature = $this->base64UrlEncode($signature);

        return $signatureInput . "." . $base64UrlSignature;
    }

    /**
     * Helper to base64url encode strings.
     */
    private function base64UrlEncode(string $data): string
    {
        return rtrim(str_replace(['+', '/'], ['-', '_'], base64_encode($data)), '=');
    }
}
