<?php

/**
 * Session Management Utility
 * 
 * Handles secure session operations with httponly, secure, and samesite flags
 */
class Session
{
    /**
     * Start session with secure configuration
     */
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start([
                'cookie_httponly' => true,
                'cookie_secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on',
                'cookie_samesite' => 'Strict'
            ]);
        }
    }

    /**
     * Set session value
     * 
     * @param string $key
     * @param mixed $value
     */
    public static function set(string $key, mixed $value): void
    {
        self::start();
        $_SESSION[$key] = $value;
    }

    /**
     * Get session value
     * 
     * @param string $key
     * @return mixed
     */
    public static function get(string $key): mixed
    {
        self::start();
        return $_SESSION[$key] ?? null;
    }

    /**
     * Check if session key exists
     * 
     * @param string $key
     * @return bool
     */
    public static function has(string $key): bool
    {
        self::start();
        return isset($_SESSION[$key]);
    }

    /**
     * Destroy session
     */
    public static function destroy(): void
    {
        self::start();
        $_SESSION = [];
        
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }
        
        session_destroy();
    }

    /**
     * Check if user is authenticated
     * 
     * @return bool
     */
    public static function isAuthenticated(): bool
    {
        return self::has('user_id');
    }

    /**
     * Get authenticated user ID
     * 
     * @return int|null
     */
    public static function getUserId(): ?int
    {
        $userId = self::get('user_id');
        return $userId !== null ? (int)$userId : null;
    }
}
