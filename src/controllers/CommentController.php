<?php

require_once __DIR__ . '/../models/Comment.php';
require_once __DIR__ . '/../models/Post.php';
require_once __DIR__ . '/../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../../utils/Session.php';
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
        AuthMiddleware::requireAuth();
        Session::start();

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
            // Store errors in session for display
            Session::set('comment_errors', $errors);
            header('Location: /posts/' . $post_id);
            exit;
        }

        // Create comment
        $userId = Session::getUserId();
        $comment = Comment::create($post_id, $userId, $text);

        if ($comment === false) {
            Session::set('comment_errors', ['Ошибка при создании комментария']);
        }

        // Redirect back to post
        header('Location: /posts/' . $post_id);
        exit;
    }
}
