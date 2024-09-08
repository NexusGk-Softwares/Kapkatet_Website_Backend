<?php
// Load environment variables
require 'vendor/autoload.php'; // Use this to load .env via vlucas/phpdotenv if you're using it
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JWTHandler {

    // Secret key for signing the JWT (you can set this in your .env file)
    private static $secret_key;
    private static $algorithm = 'HS256';

    // Load JWT secret key from environment variables
    public static function init() {
        self::$secret_key = getenv('JWT_SECRET');  // Fetch from .env file
        if (!self::$secret_key) {
            throw new Exception("JWT secret key is not set in .env");
        }
    }

    /**
     * Generate a JWT token.
     * @param array $payload - The data to encode in the JWT (user info, issued time, expiration time, etc.).
     * @return string - Encoded JWT token.
     */
    public static function encode($payload) {
        return JWT::encode($payload, self::$secret_key, self::$algorithm);
    }

    /**
     * Decode a JWT token.
     * @param string $jwt - The JWT token to decode.
     * @return object|bool - Decoded data if successful, false if failed.
     */
    public static function decode($jwt) {
        try {
            $decoded = JWT::decode($jwt, new Key(self::$secret_key, self::$algorithm));
            return $decoded;
        } catch (Exception $e) {
            return false;  // Invalid token
        }
    }

    /**
     * Validate JWT token and return payload.
     * @param string $token - The JWT token to validate.
     * @return mixed - Decoded payload if valid, false otherwise.
     */
    public static function validate($token) {
        $decoded = self::decode($token);
        if ($decoded) {
            $now = new DateTimeImmutable();
            // Check if token has expired
            if ($decoded->exp < $now->getTimestamp()) {
                return false; // Token expired
            }
            return $decoded;
        }
        return false; // Invalid token
    }

    /**
     * Generate a payload for the JWT token.
     * @param int $userId - The user ID to include in the payload.
     * @param string $email - The user's email to include in the payload.
     * @return array - Payload with the user ID, email, and expiration.
     */
    public static function generatePayload($userId, $email) {
        $issuedAt = new DateTimeImmutable();
        $expiration = $issuedAt->modify('+1 hour')->getTimestamp(); // Token expires in 1 hour

        return [
            'iat' => $issuedAt->getTimestamp(),  // Issued at time
            'exp' => $expiration,                // Expiration time
            'user_id' => $userId,                // Include user ID
            'email' => $email                    // Include user email
        ];
    }
}

// Initialize the JWT Handler
JWTHandler::init();
?>
