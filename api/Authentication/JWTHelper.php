<?php
/**
 * Pure-PHP HS256 JWT helper — no Composer dependency, PHP 7.2+ compatible.
 */
class JWTHelper {

    public static function generate(array $claims, $ttl = 86400) {
        $now     = time();
        $payload = array_merge($claims, ['iat' => $now, 'exp' => $now + $ttl]);

        $header  = self::b64url(json_encode(['typ' => 'JWT', 'alg' => 'HS256']));
        $payload = self::b64url(json_encode($payload));
        $sig     = self::b64url(hash_hmac('SHA256', "$header.$payload", JWT_SECRET, true));

        return "$header.$payload.$sig";
    }

    /**
     * @return array|false
     */
    public static function validate($token) {
        $parts = explode('.', $token);
        if (count($parts) !== 3) {
            return false;
        }

        list($b64_header, $b64_payload, $b64_sig) = $parts;

        $sig      = base64_decode(strtr($b64_sig, '-_', '+/'));
        $expected = hash_hmac('SHA256', "$b64_header.$b64_payload", JWT_SECRET, true);

        if (!hash_equals($expected, $sig)) {
            return false;
        }

        $payload = json_decode(base64_decode(strtr($b64_payload, '-_', '+/')), true);
        if (!is_array($payload)) {
            return false;
        }
        if (isset($payload['exp']) && $payload['exp'] < time()) {
            return false;
        }

        return $payload;
    }

    private static function b64url($data) {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
}
