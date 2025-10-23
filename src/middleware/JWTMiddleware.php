<?php

require_once __DIR__ . '/../../utils/JWT.php';
require_once __DIR__ . '/../models/User.php';

/**
 * JWT Middleware
 * 
 * Handles JWT authentication and authorization
 */
class JWTMiddleware
{
    /**
     * Require valid JWT token
     * Redirects to login if token is invalid or missing
     * 
     * @return object|null Decoded token payload
     */
    public static function requireAuth(): ?object
    {
        $accessToken = JWT::getTokenFromCookie('access_token');
        
        if (!$accessToken) {
            // Try to refresh token
            if (self::tryRefreshToken()) {
                $accessToken = JWT::getTokenFromCookie('access_token');
            } else {
                header('Location: /login');
                exit;
            }
        }

        $payload = JWT::verifyToken($accessToken);
        
        if (!$payload || $payload->type !== 'access') {
            // Try to refresh token
            if (self::tryRefreshToken()) {
                $accessToken = JWT::getTokenFromCookie('access_token');
                $payload = JWT::verifyToken($accessToken);
            } else {
                header('Location: /login');
                exit;
            }
        }

        // Store user info for easy access
        $_SERVER['JWT_USER_ID'] = $payload->user_id;
        $_SERVER['JWT_USER_LOGIN'] = $payload->login;
        
        return $payload;
    }

    /**
     * Try to refresh access token using refresh token
     * 
     * @return bool
     */
    private static function tryRefreshToken(): bool
    {
        $refreshToken = JWT::getTokenFromCookie('refresh_token');
        
        if (!$refreshToken) {
            return false;
        }

        // Verify refresh token
        $payload = JWT::verifyToken($refreshToken);
        
        if (!$payload || $payload->type !== 'refresh') {
            return false;
        }

        // Check if refresh token exists in database
        if (!JWT::verifyRefreshTokenInDB($refreshToken)) {
            return false;
        }

        // Get user
        $user = User::findById($payload->user_id);
        
        if (!$user) {
            return false;
        }

        // Generate new access token
        $newAccessToken = JWT::generateAccessToken($user->id, $user->login);
        JWT::setTokenCookie('access_token', $newAccessToken, 900); // 15 minutes
        
        return true;
    }

    /**
     * Require guest (non-authenticated user)
     * Redirects to home if authenticated
     */
    public static function requireGuest(): void
    {
        // Clean up old session cookies if they exist
        if (isset($_COOKIE['PHPSESSID'])) {
            setcookie('PHPSESSID', '', time() - 3600, '/');
        }
        
        $accessToken = JWT::getTokenFromCookie('access_token');
        
        if (!$accessToken) {
            return; // No token, user is guest
        }
        
        $payload = JWT::verifyToken($accessToken);
        
        if ($payload && $payload->type === 'access') {
            header('Location: /');
            exit;
        }
        
        // Token is invalid, delete it
        JWT::deleteTokenCookie('access_token');
        JWT::deleteTokenCookie('refresh_token');
    }

    /**
     * Require post owner
     * Verifies that the authenticated user owns the specified post
     * 
     * @param int $post_id
     */
    public static function requirePostOwner(int $post_id): void
    {
        $payload = self::requireAuth();
        
        require_once __DIR__ . '/../models/Post.php';
        $post = Post::findById($post_id);

        if (!$post) {
            http_response_code(404);
            echo '404 - Пост не найден';
            exit;
        }

        if ($post->user_id !== $payload->user_id) {
            http_response_code(403);
            echo '403 - Доступ запрещен. Вы не являетесь автором этого поста.';
            exit;
        }
    }

    /**
     * Get current user ID from JWT
     * 
     * @return int|null
     */
    public static function getUserId(): ?int
    {
        return $_SERVER['JWT_USER_ID'] ?? null;
    }

    /**
     * Get current user login from JWT
     * 
     * @return string|null
     */
    public static function getUserLogin(): ?string
    {
        return $_SERVER['JWT_USER_LOGIN'] ?? null;
    }

    /**
     * Check if user is authenticated
     * 
     * @return bool
     */
    public static function isAuthenticated(): bool
    {
        $accessToken = JWT::getTokenFromCookie('access_token');
        
        if (!$accessToken) {
            // Try to refresh
            if (self::tryRefreshToken()) {
                $accessToken = JWT::getTokenFromCookie('access_token');
                if ($accessToken) {
                    $payload = JWT::verifyToken($accessToken);
                    if ($payload && $payload->type === 'access') {
                        $_SERVER['JWT_USER_ID'] = $payload->user_id;
                        $_SERVER['JWT_USER_LOGIN'] = $payload->login;
                        return true;
                    }
                }
            }
            return false;
        }

        $payload = JWT::verifyToken($accessToken);
        
        if ($payload && $payload->type === 'access') {
            $_SERVER['JWT_USER_ID'] = $payload->user_id;
            $_SERVER['JWT_USER_LOGIN'] = $payload->login;
            return true;
        }
        
        return false;
    }
}
