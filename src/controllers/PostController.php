<?php

require_once __DIR__ . '/../models/Post.php';
require_once __DIR__ . '/../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../../utils/Session.php';
require_once __DIR__ . '/../../utils/Validator.php';

/**
 * Post Controller
 * 
 * Handles post management operations
 */
class PostController
{
    /**
     * Display all posts
     */
    public static function index(): void
    {
        Session::start();
        $posts = Post::findAll();
        require __DIR__ . '/../../views/posts/index.php';
    }

    /**
     * Display single post
     * 
     * @param int $id
     */
    public static function show(int $id): void
    {
        Session::start();
        
        $post = Post::findById($id);

        if (!$post) {
            http_response_code(404);
            echo '404 - Пост не найден';
            return;
        }

        // Increment views
        $post->incrementViews();

        // Get author and comments
        $author = $post->getAuthor();
        $comments = $post->getComments();

        require __DIR__ . '/../../views/posts/show.php';
    }

    /**
     * Show create post form
     */
    public static function showCreateForm(): void
    {
        AuthMiddleware::requireAuth();
        Session::start();
        require __DIR__ . '/../../views/posts/create.php';
    }

    /**
     * Handle post creation
     */
    public static function create(): void
    {
        AuthMiddleware::requireAuth();
        Session::start();

        $errors = [];
        $title = $_POST['title'] ?? '';
        $content = $_POST['content'] ?? '';

        // Validate title
        $titleErrors = Validator::validatePostTitle($title);
        if (!empty($titleErrors)) {
            $errors = array_merge($errors, $titleErrors);
        }

        // Validate content
        $contentErrors = Validator::validatePostContent($content);
        if (!empty($contentErrors)) {
            $errors = array_merge($errors, $contentErrors);
        }

        // If validation fails, show form with errors
        if (!empty($errors)) {
            require __DIR__ . '/../../views/posts/create.php';
            return;
        }

        // Create post
        $userId = Session::getUserId();
        $post = Post::create($userId, $title, $content);

        if ($post === false) {
            $errors[] = 'Ошибка при создании поста';
            require __DIR__ . '/../../views/posts/create.php';
            return;
        }

        // Redirect to post view
        header('Location: /posts/' . $post->id);
        exit;
    }

    /**
     * Show edit post form
     * 
     * @param int $id
     */
    public static function showEditForm(int $id): void
    {
        AuthMiddleware::requirePostOwner($id);
        Session::start();

        $post = Post::findById($id);
        require __DIR__ . '/../../views/posts/edit.php';
    }

    /**
     * Handle post update
     * 
     * @param int $id
     */
    public static function edit(int $id): void
    {
        AuthMiddleware::requirePostOwner($id);
        Session::start();

        $post = Post::findById($id);
        $errors = [];
        $title = $_POST['title'] ?? '';
        $content = $_POST['content'] ?? '';

        // Validate title
        $titleErrors = Validator::validatePostTitle($title);
        if (!empty($titleErrors)) {
            $errors = array_merge($errors, $titleErrors);
        }

        // Validate content
        $contentErrors = Validator::validatePostContent($content);
        if (!empty($contentErrors)) {
            $errors = array_merge($errors, $contentErrors);
        }

        // If validation fails, show form with errors
        if (!empty($errors)) {
            require __DIR__ . '/../../views/posts/edit.php';
            return;
        }

        // Update post
        $result = $post->update($title, $content);

        if (!$result) {
            $errors[] = 'Ошибка при обновлении поста';
            require __DIR__ . '/../../views/posts/edit.php';
            return;
        }

        // Redirect to post view
        header('Location: /posts/' . $post->id);
        exit;
    }

    /**
     * Handle post deletion
     * 
     * @param int $id
     */
    public static function delete(int $id): void
    {
        AuthMiddleware::requirePostOwner($id);

        $post = Post::findById($id);
        $post->delete();

        header('Location: /');
        exit;
    }

    /**
     * Handle like action
     * 
     * @param int $id
     */
    public static function like(int $id): void
    {
        AuthMiddleware::requireAuth();

        $post = Post::findById($id);

        if (!$post) {
            http_response_code(404);
            echo '404 - Пост не найден';
            return;
        }

        $post->incrementLikes();

        header('Location: /posts/' . $id);
        exit;
    }

    /**
     * Handle dislike action
     * 
     * @param int $id
     */
    public static function dislike(int $id): void
    {
        AuthMiddleware::requireAuth();

        $post = Post::findById($id);

        if (!$post) {
            http_response_code(404);
            echo '404 - Пост не найден';
            return;
        }

        $post->incrementDislikes();

        header('Location: /posts/' . $id);
        exit;
    }
}
