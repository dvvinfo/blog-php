<?php

/**
 * Application Entry Point
 * 
 * Main router and application bootstrap with JWT authentication
 */

// Error reporting for development
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Autoload Composer dependencies
require_once __DIR__ . '/../vendor/autoload.php';

// Autoload application dependencies
require_once __DIR__ . '/../utils/Router.php';
require_once __DIR__ . '/../utils/JWT.php';
require_once __DIR__ . '/../src/middleware/JWTMiddleware.php';
require_once __DIR__ . '/../src/controllers/AuthController.php';
require_once __DIR__ . '/../src/controllers/PostController.php';
require_once __DIR__ . '/../src/controllers/CommentController.php';
require_once __DIR__ . '/../src/controllers/ProfileController.php';

// Create router instance
$router = new Router();

// Authentication routes
$router->get('/register', [AuthController::class, 'showRegisterForm']);
$router->post('/register', [AuthController::class, 'register']);
$router->get('/login', [AuthController::class, 'showLoginForm']);
$router->post('/login', [AuthController::class, 'login']);
$router->get('/logout', [AuthController::class, 'logout']);

// Post routes
$router->get('/', [PostController::class, 'index']);
$router->get('/posts', [PostController::class, 'index']);
$router->get('/posts/{id}', [PostController::class, 'show']);
$router->get('/posts/create', [PostController::class, 'showCreateForm']);
$router->post('/posts/create', [PostController::class, 'create']);
$router->get('/posts/{id}/edit', [PostController::class, 'showEditForm']);
$router->post('/posts/{id}/edit', [PostController::class, 'edit']);
$router->post('/posts/{id}/delete', [PostController::class, 'delete']);
$router->post('/posts/{id}/like', [PostController::class, 'like']);
$router->post('/posts/{id}/dislike', [PostController::class, 'dislike']);

// Comment routes
$router->post('/posts/{id}/comments', [CommentController::class, 'create']);

// Profile routes
$router->get('/profile/analytics', [ProfileController::class, 'analytics']);

// Get request method and URI
$method = $_SERVER['REQUEST_METHOD'];
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Dispatch request
$router->dispatch($method, $uri);
