<?php

namespace App\Services\Auth;

use Exception;
use Illuminate\Support\Facades\Log;

class JwtService
{
    protected string $secret;

    public function __construct()
    {
        $this->secret = config('auth.jwt_secret') ?? config('app.key');
    }

    /**
     * Generate a JWT for a user.
     *
     * @param array $payload
     * @param int|null $expiration Minutes (null uses default 480 / 8h)
     * @return string
     */
    public function generate(array $payload, ?int $expiration = null): string
    {
        $expiration = $expiration ?? 480;
        $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);
        
        $now = time();
        $payload['exp'] = $now + ($expiration * 60);
        $payload['iat'] = $now;
        $payload['nbf'] = $now;
        $payloadJson = json_encode($payload);

        $base64UrlHeader = $this->base64UrlEncode($header);
        $base64UrlPayload = $this->base64UrlEncode($payloadJson);

        $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, $this->secret, true);
        $base64UrlSignature = $this->base64UrlEncode($signature);

        return $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;
    }

    /**
     * Validate and decode a JWT.
     *
     * @param string $token
     * @return array|null
     */
    public function decode(string $token): ?array
    {
        $parts = explode('.', $token);
        if (count($parts) !== 3) {
            return null;
        }

        [$header, $payload, $signature] = $parts;

        // Verify Signature
        $validSignature = hash_hmac('sha256', $header . "." . $payload, $this->secret, true);
        if (!$this->hashEquals($this->base64UrlEncode($validSignature), $signature)) {
            Log::warning("JWT Signature invalid");
            return null;
        }

        $decodedPayload = json_decode($this->base64UrlDecode($payload), true);
        if (!$decodedPayload) {
            return null;
        }

        $now = time();

        // Check Expiration
        if (isset($decodedPayload['exp']) && $decodedPayload['exp'] < $now) {
            Log::info("JWT Expired");
            return null;
        }

        // Check Not Before (nbf)
        if (isset($decodedPayload['nbf']) && $decodedPayload['nbf'] > ($now + 60)) {
            Log::warning("JWT Not active yet (nbf in future)");
            return null;
        }

        // Reject token issued in the far future (clock drift protection > 60s)
        if (isset($decodedPayload['iat']) && $decodedPayload['iat'] > ($now + 60)) {
            Log::warning("JWT Issued in future (iat drift)");
            return null;
        }

        return $decodedPayload;
    }

    /**
     * Base64Url Encoding.
     */
    protected function base64UrlEncode(string $data): string
    {
        return str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($data));
    }

    /**
     * Base64Url Decoding.
     */
    protected function base64UrlDecode(string $data): string
    {
        return base64_decode(str_replace(['-', '_'], ['+', '/'], $data));
    }

    /**
     * Timing attack safe comparison.
     */
    protected function hashEquals(string $known, string $user): bool
    {
        return hash_equals($known, $user);
    }
}
