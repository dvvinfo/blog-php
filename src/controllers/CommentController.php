<?php

require_once __DIR__ . '/../models/Comment.php';
require_once __DIR__ . '/../models/Post.php';
require_once __DIR__ . '/../middleware/JWTMiddleware.php';
require_once __DIR__ . '/../../utils/Validator.php';

/**
 * Comment Controller
 * 
 * Handles comment operations
 */
class CommentController
{
    /**
     * Handle comment creation
     * 
     * @param int $post_id
     */
    public static function create(int $post_id): void
    {
        JWTMiddleware::requireAuth();

        // Check if post exists
        $post = Post::findById($post_id);
        if (!$post) {
            http_response_code(404);
            echo '404 - Пост не найден';
            return;
        }

        $errors = [];
        $text = $_POST['text'] ?? '';

        // Validate comment text
        $textErrors = Validator::validateCommentText($text);
        if (!empty($textErrors)) {
            $errors = array_merge($errors, $textErrors);
        }

        // If validation fails, redirect back with error
        if (!empty($errors)) {
            // Store errors in cookie for display
            setcookie('comment_errors', json_encode($errors), time() + 60, '/');
            header('Location: /posts/' . $post_id);
            exit;
        }

        // Create comment
        $userId = JWTMiddleware::getUserId();
        $comment = Comment::create($post_id, $userId, $text);

        if ($comment === false) {
            setcookie('comment_errors', json_encode(['Ошибка при создании комментария']), time() + 60, '/');
        }

        // Redirect back to post
        header('Location: /posts/' . $post_id);
        exit;
    }
}
