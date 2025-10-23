<?php

use Firebase\JWT\JWT as FirebaseJWT;
use Firebase\JWT\Key;

/**
 * JWT Utility
 * 
 * Handles JWT token generation and validation
 */
class JWT
{
    // Secret key for signing tokens (should be in environment variables in production)
    private static string $secretKey = 'your-secret-key-change-this-in-production-min-256-bits';
    private static string $algorithm = 'HS256';
    
    // Token expiration times
    private static int $accessTokenExpiration = 900; // 15 minutes
    private static int $refreshTokenExpiration = 604800; // 7 days

    /**
     * Generate access token
     * 
     * @param int $userId
     * @param string $login
     * @return string
     */
    public static function generateAccessToken(int $userId, string $login): string
    {
        $issuedAt = time();
        $expire = $issuedAt + self::$accessTokenExpiration;

        $payload = [
            'iat' => $issuedAt,
            'exp' => $expire,
            'user_id' => $userId,
            'login' => $login,
            'type' => 'access'
        ];

        return FirebaseJWT::encode($payload, self::$secretKey, self::$algorithm);
    }

    /**
     * Generate refresh token
     * 
     * @param int $userId
     * @return string
     */
    public static function generateRefreshToken(int $userId): string
    {
        $issuedAt = time();
        $expire = $issuedAt + self::$refreshTokenExpiration;

        $payload = [
            'iat' => $issuedAt,
            'exp' => $expire,
            'user_id' => $userId,
            'type' => 'refresh',
            'jti' => bin2hex(random_bytes(16)) // Unique token ID
        ];

        $token = FirebaseJWT::encode($payload, self::$secretKey, self::$algorithm);
        
        // Store refresh token in database
        self::storeRefreshToken($userId, $token, $expire);
        
        return $token;
    }

    /**
     * Verify and decode token
     * 
     * @param string $token
     * @return object|false
     */
    public static function verifyToken(string $token): object|false
    {
        try {
            $decoded = FirebaseJWT::decode($token, new Key(self::$secretKey, self::$algorithm));
            return $decoded;
        } catch (Exception $e) {
            error_log('JWT verification failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Store refresh token in database
     * 
     * @param int $userId
     * @param string $token
     * @param int $expiresAt
     * @return bool
     */
    private static function storeRefreshToken(int $userId, string $token, int $expiresAt): bool
    {
        try {
            require_once __DIR__ . '/../src/Database.php';
            $db = Database::getConnection();
            
            $stmt = $db->prepare(
                'INSERT INTO refresh_tokens (user_id, token, expires_at) VALUES (:user_id, :token, :expires_at)'
            );
            
            return $stmt->execute([
                'user_id' => $userId,
                'token' => $token,
                'expires_at' => date('Y-m-d H:i:s', $expiresAt)
            ]);
        } catch (PDOException $e) {
            error_log('Failed to store refresh token: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Verify refresh token exists in database
     * 
     * @param string $token
     * @return bool
     */
    public static function verifyRefreshTokenInDB(string $token): bool
    {
        try {
            require_once __DIR__ . '/../src/Database.php';
            $db = Database::getConnection();
            
            $stmt = $db->prepare(
                'SELECT id FROM refresh_tokens WHERE token = :token AND expires_at > NOW()'
            );
            $stmt->execute(['token' => $token]);
            
            return $stmt->fetch() !== false;
        } catch (PDOException $e) {
            error_log('Failed to verify refresh token: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Revoke refresh token
     * 
     * @param string $token
     * @return bool
     */
    public static function revokeRefreshToken(string $token): bool
    {
        try {
            require_once __DIR__ . '/../src/Database.php';
            $db = Database::getConnection();
            
            $stmt = $db->prepare('DELETE FROM refresh_tokens WHERE token = :token');
            return $stmt->execute(['token' => $token]);
        } catch (PDOException $e) {
            error_log('Failed to revoke refresh token: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Revoke all user refresh tokens
     * 
     * @param int $userId
     * @return bool
     */
    public static function revokeAllUserTokens(int $userId): bool
    {
        try {
            require_once __DIR__ . '/../src/Database.php';
            $db = Database::getConnection();
            
            $stmt = $db->prepare('DELETE FROM refresh_tokens WHERE user_id = :user_id');
            return $stmt->execute(['user_id' => $userId]);
        } catch (PDOException $e) {
            error_log('Failed to revoke user tokens: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Clean expired tokens
     * 
     * @return bool
     */
    public static function cleanExpiredTokens(): bool
    {
        try {
            require_once __DIR__ . '/../src/Database.php';
            $db = Database::getConnection();
            
            $stmt = $db->prepare('DELETE FROM refresh_tokens WHERE expires_at < NOW()');
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log('Failed to clean expired tokens: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get token from cookie
     * 
     * @param string $name
     * @return string|null
     */
    public static function getTokenFromCookie(string $name): ?string
    {
        return $_COOKIE[$name] ?? null;
    }

    /**
     * Set token in cookie
     * 
     * @param string $name
     * @param string $token
     * @param int $expiration
     * @return bool
     */
    public static function setTokenCookie(string $name, string $token, int $expiration): bool
    {
        return setcookie(
            $name,
            $token,
            [
                'expires' => time() + $expiration,
                'path' => '/',
                'httponly' => true,
                'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on',
                'samesite' => 'Strict'
            ]
        );
    }

    /**
     * Delete token cookie
     * 
     * @param string $name
     * @return bool
     */
    public static function deleteTokenCookie(string $name): bool
    {
        return setcookie(
            $name,
            '',
            [
                'expires' => time() - 3600,
                'path' => '/',
                'httponly' => true,
                'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on',
                'samesite' => 'Strict'
            ]
        );
    }
}
