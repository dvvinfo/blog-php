<?php

namespace Tests\Feature;

use PHPUnit\Framework\TestCase;

/**
 * Comment Validation Feature Tests
 */
class CommentValidationTest extends TestCase
{
    public function testCompleteCommentValidation(): void
    {
        // Test invalid data
        $text = 'a'; // Too short
        $errors = \Validator::validateCommentText($text);
        $this->assertNotEmpty($errors);
        
        // Test valid data
        $validText = 'This is a valid comment';
        $errors = \Validator::validateCommentText($validText);
        $this->assertEmpty($errors);
    }

    public function testCommentTextEdgeCases(): void
    {
        // Empty comment
        $errors = \Validator::validateCommentText('');
        $this->assertNotEmpty($errors);
        
        // 1 character (too short)
        $errors = \Validator::validateCommentText('a');
        $this->assertNotEmpty($errors);
        
        // 2 characters (minimum)
        $errors = \Validator::validateCommentText('ab');
        $this->assertEmpty($errors);
        
        // 1000 characters (maximum)
        $maxComment = str_repeat('a', 1000);
        $errors = \Validator::validateCommentText($maxComment);
        $this->assertEmpty($errors);
        
        // 1001 characters (too long)
        $tooLongComment = str_repeat('a', 1001);
        $errors = \Validator::validateCommentText($tooLongComment);
        $this->assertNotEmpty($errors);
    }

    public function testCommentXSSProtection(): void
    {
        $maliciousComment = '<script>alert("XSS")</script>Nice comment!';
        
        // Validation should pass
        $errors = \Validator::validateCommentText($maliciousComment);
        $this->assertEmpty($errors);
        
        // But sanitization should escape HTML
        $sanitized = \Validator::sanitize($maliciousComment);
        $this->assertStringNotContainsString('<script>', $sanitized);
        $this->assertStringContainsString('&lt;script&gt;', $sanitized);
    }

    public function testCommentWithSpecialCharacters(): void
    {
        $comment = 'Comment with "quotes" and \'apostrophes\' and <tags>';
        
        $errors = \Validator::validateCommentText($comment);
        $this->assertEmpty($errors);
        
        $sanitized = \Validator::sanitize($comment);
        $this->assertStringContainsString('&quot;', $sanitized);
        $this->assertStringContainsString('&#039;', $sanitized);
        $this->assertStringContainsString('&lt;', $sanitized);
    }
}
