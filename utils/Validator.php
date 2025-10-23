<?php

/**
 * Input Validation Utility
 * 
 * Provides validation methods for user input with error messages
 */
class Validator
{
    /**
     * Validate login
     * 
     * @param string $login
     * @return array Array of error messages (empty if valid)
     */
    public static function validateLogin(string $login): array
    {
        $errors = [];

        if (empty(trim($login))) {
            $errors[] = 'Логин не может быть пустым';
        } elseif (strlen($login) < 3) {
            $errors[] = 'Логин должен содержать минимум 3 символа';
        } elseif (strlen($login) > 50) {
            $errors[] = 'Логин не может превышать 50 символов';
        } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $login)) {
            $errors[] = 'Логин может содержать только буквы, цифры и подчеркивание';
        }

        return $errors;
    }

    /**
     * Validate password
     * 
     * @param string $password
     * @return array Array of error messages (empty if valid)
     */
    public static function validatePassword(string $password): array
    {
        $errors = [];

        if (empty($password)) {
            $errors[] = 'Пароль не может быть пустым';
        } elseif (strlen($password) < 6) {
            $errors[] = 'Пароль должен содержать минимум 6 символов';
        } elseif (strlen($password) > 255) {
            $errors[] = 'Пароль слишком длинный';
        }

        return $errors;
    }

    /**
     * Validate post title
     * 
     * @param string $title
     * @return array Array of error messages (empty if valid)
     */
    public static function validatePostTitle(string $title): array
    {
        $errors = [];

        if (empty(trim($title))) {
            $errors[] = 'Заголовок не может быть пустым';
        } elseif (strlen($title) > 255) {
            $errors[] = 'Заголовок не может превышать 255 символов';
        }

        return $errors;
    }

    /**
     * Validate post content
     * 
     * @param string $content
     * @return array Array of error messages (empty if valid)
     */
    public static function validatePostContent(string $content): array
    {
        $errors = [];

        if (empty(trim($content))) {
            $errors[] = 'Содержимое не может быть пустым';
        } elseif (strlen($content) < 10) {
            $errors[] = 'Содержимое должно содержать минимум 10 символов';
        }

        return $errors;
    }

    /**
     * Validate comment text
     * 
     * @param string $text
     * @return array Array of error messages (empty if valid)
     */
    public static function validateCommentText(string $text): array
    {
        $errors = [];

        if (empty(trim($text))) {
            $errors[] = 'Комментарий не может быть пустым';
        } elseif (strlen($text) < 2) {
            $errors[] = 'Комментарий должен содержать минимум 2 символа';
        } elseif (strlen($text) > 1000) {
            $errors[] = 'Комментарий не может превышать 1000 символов';
        }

        return $errors;
    }

    /**
     * Sanitize input string
     * 
     * @param string $input
     * @return string
     */
    public static function sanitize(string $input): string
    {
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }
}
