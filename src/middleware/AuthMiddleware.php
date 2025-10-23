<?php

require_once __DIR__ . '/../../utils/Session.php';
require_once __DIR__ . '/../models/Post.php';

/**
 * Authentication Middleware
 * 
 * Handles authentication and authorization checks
 */
class AuthMiddleware
{
    /**
     * Require authenticated user
     * Redirects to login page if not authenticated
     */
    public static function requireAuth(): void
    {
        Session::start();

        if (!Session::isAuthenticated()) {
            header('Location: /login');
            exit;
        }
    }

    /**
     * Require guest (non-authenticated user)
     * Redirects to home page if authenticated
     */
    public static function requireGuest(): void
    {
        Session::start();

        if (Session::isAuthenticated()) {
            header('Location: /');
            exit;
        }
    }

    /**
     * Require post owner
     * Verifies that the authenticated user owns the specified post
     * 
     * @param int $post_id
     */
    public static function requirePostOwner(int $post_id): void
    {
        self::requireAuth();

        $post = Post::findById($post_id);

        if (!$post) {
            http_response_code(404);
            echo '404 - Пост не найден';
            exit;
        }

        $userId = Session::getUserId();

        if ($post->user_id !== $userId) {
            http_response_code(403);
            echo '403 - Доступ запрещен. Вы не являетесь автором этого поста.';
            exit;
        }
    }
}
