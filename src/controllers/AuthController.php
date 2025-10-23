<?php

require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../../utils/JWT.php';
require_once __DIR__ . '/../middleware/JWTMiddleware.php';
require_once __DIR__ . '/../../utils/Validator.php';

/**
 * Authentication Controller
 * 
 * Handles user registration, login, and logout with JWT
 */
class AuthController
{
    /**
     * Show registration form
     */
    public static function showRegisterForm(): void
    {
        JWTMiddleware::requireGuest();
        require __DIR__ . '/../../views/auth/register.php';
    }

    /**
     * Handle registration
     */
    public static function register(): void
    {
        $errors = [];
        $login = $_POST['login'] ?? '';
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        // Validate login
        $loginErrors = Validator::validateLogin($login);
        if (!empty($loginErrors)) {
            $errors = array_merge($errors, $loginErrors);
        }

        // Validate password
        $passwordErrors = Validator::validatePassword($password);
        if (!empty($passwordErrors)) {
            $errors = array_merge($errors, $passwordErrors);
        }

        // Check password confirmation
        if ($password !== $confirmPassword) {
            $errors[] = 'Пароли не совпадают';
        }

        // Check if login already exists
        if (empty($errors)) {
            $existingUser = User::findByLogin($login);
            if ($existingUser) {
                $errors[] = 'Пользователь с таким логином уже существует';
            }
        }

        // If validation fails, show form with errors
        if (!empty($errors)) {
            require __DIR__ . '/../../views/auth/register.php';
            return;
        }

        // Create user
        $user = User::create($login, $password);

        if ($user === false) {
            $errors[] = 'Ошибка при создании пользователя';
            require __DIR__ . '/../../views/auth/register.php';
            return;
        }

        // Generate JWT tokens
        $accessToken = JWT::generateAccessToken($user->id, $user->login);
        $refreshToken = JWT::generateRefreshToken($user->id);

        // Set tokens in cookies
        JWT::setTokenCookie('access_token', $accessToken, 900); // 15 minutes
        JWT::setTokenCookie('refresh_token', $refreshToken, 604800); // 7 days

        // Redirect to home
        header('Location: /');
        exit;
    }

    /**
     * Show login form
     */
    public static function showLoginForm(): void
    {
        JWTMiddleware::requireGuest();
        require __DIR__ . '/../../views/auth/login.php';
    }

    /**
     * Handle login
     */
    public static function login(): void
    {
        $errors = [];
        $login = $_POST['login'] ?? '';
        $password = $_POST['password'] ?? '';

        // Basic validation
        if (empty($login) || empty($password)) {
            $errors[] = 'Заполните все поля';
            require __DIR__ . '/../../views/auth/login.php';
            return;
        }

        // Find user
        $user = User::findByLogin($login);

        // Verify credentials
        if (!$user || !$user->verifyPassword($password)) {
            $errors[] = 'Неверный логин или пароль';
            require __DIR__ . '/../../views/auth/login.php';
            return;
        }

        // Generate JWT tokens
        $accessToken = JWT::generateAccessToken($user->id, $user->login);
        $refreshToken = JWT::generateRefreshToken($user->id);

        // Set tokens in cookies
        JWT::setTokenCookie('access_token', $accessToken, 900); // 15 minutes
        JWT::setTokenCookie('refresh_token', $refreshToken, 604800); // 7 days

        // Redirect to home
        header('Location: /');
        exit;
    }

    /**
     * Handle logout
     */
    public static function logout(): void
    {
        // Get refresh token and revoke it
        $refreshToken = JWT::getTokenFromCookie('refresh_token');
        if ($refreshToken) {
            JWT::revokeRefreshToken($refreshToken);
        }

        // Delete token cookies
        JWT::deleteTokenCookie('access_token');
        JWT::deleteTokenCookie('refresh_token');

        header('Location: /login');
        exit;
    }
}
