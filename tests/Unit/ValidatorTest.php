<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

/**
 * Validator Unit Tests
 */
class ValidatorTest extends TestCase
{
    // Login validation tests
    
    public function testValidateLoginSuccess(): void
    {
        $errors = \Validator::validateLogin('testuser');
        $this->assertEmpty($errors);
    }

    public function testValidateLoginEmpty(): void
    {
        $errors = \Validator::validateLogin('');
        $this->assertNotEmpty($errors);
        $this->assertContains('Логин не может быть пустым', $errors);
    }

    public function testValidateLoginTooShort(): void
    {
        $errors = \Validator::validateLogin('ab');
        $this->assertNotEmpty($errors);
        $this->assertContains('Логин должен содержать минимум 3 символа', $errors);
    }

    public function testValidateLoginTooLong(): void
    {
        $errors = \Validator::validateLogin(str_repeat('a', 51));
        $this->assertNotEmpty($errors);
        $this->assertContains('Логин не может превышать 50 символов', $errors);
    }

    public function testValidateLoginInvalidCharacters(): void
    {
        $errors = \Validator::validateLogin('test user!');
        $this->assertNotEmpty($errors);
        $this->assertContains('Логин может содержать только буквы, цифры и подчеркивание', $errors);
    }

    public function testValidateLoginValidCharacters(): void
    {
        $errors = \Validator::validateLogin('test_user123');
        $this->assertEmpty($errors);
    }

    // Password validation tests

    public function testValidatePasswordSuccess(): void
    {
        $errors = \Validator::validatePassword('password123');
        $this->assertEmpty($errors);
    }

    public function testValidatePasswordEmpty(): void
    {
        $errors = \Validator::validatePassword('');
        $this->assertNotEmpty($errors);
        $this->assertContains('Пароль не может быть пустым', $errors);
    }

    public function testValidatePasswordTooShort(): void
    {
        $errors = \Validator::validatePassword('12345');
        $this->assertNotEmpty($errors);
        $this->assertContains('Пароль должен содержать минимум 6 символов', $errors);
    }

    public function testValidatePasswordTooLong(): void
    {
        $errors = \Validator::validatePassword(str_repeat('a', 256));
        $this->assertNotEmpty($errors);
        $this->assertContains('Пароль слишком длинный', $errors);
    }

    // Post title validation tests

    public function testValidatePostTitleSuccess(): void
    {
        $errors = \Validator::validatePostTitle('Test Post Title');
        $this->assertEmpty($errors);
    }

    public function testValidatePostTitleEmpty(): void
    {
        $errors = \Validator::validatePostTitle('');
        $this->assertNotEmpty($errors);
        $this->assertContains('Заголовок не может быть пустым', $errors);
    }

    public function testValidatePostTitleTooLong(): void
    {
        $errors = \Validator::validatePostTitle(str_repeat('a', 256));
        $this->assertNotEmpty($errors);
        $this->assertContains('Заголовок не может превышать 255 символов', $errors);
    }

    // Post content validation tests

    public function testValidatePostContentSuccess(): void
    {
        $errors = \Validator::validatePostContent('This is a valid post content with more than 10 characters.');
        $this->assertEmpty($errors);
    }

    public function testValidatePostContentEmpty(): void
    {
        $errors = \Validator::validatePostContent('');
        $this->assertNotEmpty($errors);
        $this->assertContains('Содержимое не может быть пустым', $errors);
    }

    public function testValidatePostContentTooShort(): void
    {
        $errors = \Validator::validatePostContent('Short');
        $this->assertNotEmpty($errors);
        $this->assertContains('Содержимое должно содержать минимум 10 символов', $errors);
    }

    // Comment text validation tests

    public function testValidateCommentTextSuccess(): void
    {
        $errors = \Validator::validateCommentText('Valid comment');
        $this->assertEmpty($errors);
    }

    public function testValidateCommentTextEmpty(): void
    {
        $errors = \Validator::validateCommentText('');
        $this->assertNotEmpty($errors);
        $this->assertContains('Комментарий не может быть пустым', $errors);
    }

    public function testValidateCommentTextTooShort(): void
    {
        $errors = \Validator::validateCommentText('a');
        $this->assertNotEmpty($errors);
        $this->assertContains('Комментарий должен содержать минимум 2 символа', $errors);
    }

    public function testValidateCommentTextTooLong(): void
    {
        $errors = \Validator::validateCommentText(str_repeat('a', 1001));
        $this->assertNotEmpty($errors);
        $this->assertContains('Комментарий не может превышать 1000 символов', $errors);
    }

    // Sanitize tests

    public function testSanitizeRemovesHtmlTags(): void
    {
        $input = '<script>alert("XSS")</script>Hello';
        $sanitized = \Validator::sanitize($input);
        $this->assertStringNotContainsString('<script>', $sanitized);
        $this->assertStringContainsString('&lt;script&gt;', $sanitized);
    }

    public function testSanitizeTrimsWhitespace(): void
    {
        $input = '  Hello World  ';
        $sanitized = \Validator::sanitize($input);
        $this->assertEquals('Hello World', $sanitized);
    }

    public function testSanitizeHandlesQuotes(): void
    {
        $input = 'Hello "World" and \'Test\'';
        $sanitized = \Validator::sanitize($input);
        $this->assertStringContainsString('&quot;', $sanitized);
        $this->assertStringContainsString('&#039;', $sanitized);
    }
}
