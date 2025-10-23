<?php

require_once __DIR__ . '/../models/Post.php';
require_once __DIR__ . '/../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../../utils/Session.php';

/**
 * Profile Controller
 * 
 * Handles user profile and analytics
 */
class ProfileController
{
    /**
     * Display user analytics
     */
    public static function analytics(): void
    {
        AuthMiddleware::requireAuth();
        Session::start();

        $userId = Session::getUserId();
        
        // Get user posts
        $userPosts = Post::findByUserId($userId);
        
        // Calculate statistics
        $totalPosts = count($userPosts);
        $totalViews = 0;
        $totalLikes = 0;
        $totalDislikes = 0;
        $totalComments = 0;

        foreach ($userPosts as $post) {
            $totalViews += $post->views;
            $totalLikes += $post->likes;
            $totalDislikes += $post->dislikes;
            $totalComments += $post->getCommentsCount();
        }

        require __DIR__ . '/../../views/profile/analytics.php';
    }
}
