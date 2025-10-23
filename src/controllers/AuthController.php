<?php

require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../../utils/Session.php';
require_once __DIR__ . '/../../utils/Validator.php';

/**
 * Authentication Controller
 * 
 * Handles user registration, login, and logout
 */
class AuthController
{
    /**
     * Show registration form
     */
    public static function showRegisterForm(): void
    {
        Session::start();
        
        // Redirect if already authenticated
        if (Session::isAuthenticated()) {
            header('Location: /');
            exit;
        }

        require __DIR__ . '/../../views/auth/register.php';
    }

    /**
     * Handle registration
     */
    public static function register(): void
    {
        Session::start();

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

        // Create session
        Session::set('user_id', $user->id);
        Session::set('user_login', $user->login);

        // Redirect to home
        header('Location: /');
        exit;
    }

    /**
     * Show login form
     */
    public static function showLoginForm(): void
    {
        Session::start();
        
        // Redirect if already authenticated
        if (Session::isAuthenticated()) {
            header('Location: /');
            exit;
        }

        require __DIR__ . '/../../views/auth/login.php';
    }

    /**
     * Handle login
     */
    public static function login(): void
    {
        Session::start();

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

        // Create session
        Session::set('user_id', $user->id);
        Session::set('user_login', $user->login);

        // Redirect to home
        header('Location: /');
        exit;
    }

    /**
     * Handle logout
     */
    public static function logout(): void
    {
        Session::destroy();
        header('Location: /login');
        exit;
    }
}
