<?php

namespace Tests\Feature;

use PHPUnit\Framework\TestCase;

/**
 * Post Validation Feature Tests
 */
class PostValidationTest extends TestCase
{
    public function testCompletePostCreationValidation(): void
    {
        // Test invalid data
        $title = '';
        $content = 'Short';
        
        $titleErrors = \Validator::validatePostTitle($title);
        $contentErrors = \Validator::validatePostContent($content);
        
        $this->assertNotEmpty($titleErrors);
        $this->assertNotEmpty($contentErrors);
        
        // Test valid data
        $validTitle = 'My Test Post Title';
        $validContent = 'This is a valid post content with more than 10 characters.';
        
        $validTitleErrors = \Validator::validatePostTitle($validTitle);
        $validContentErrors = \Validator::validatePostContent($validContent);
        
        $this->assertEmpty($validTitleErrors);
        $this->assertEmpty($validContentErrors);
    }

    public function testPostTitleEdgeCases(): void
    {
        // Empty title
        $errors = \Validator::validatePostTitle('');
        $this->assertNotEmpty($errors);
        
        // Whitespace only
        $errors = \Validator::validatePostTitle('   ');
        $this->assertNotEmpty($errors);
        
        // Maximum length (255 chars)
        $maxTitle = str_repeat('a', 255);
        $errors = \Validator::validatePostTitle($maxTitle);
        $this->assertEmpty($errors);
        
        // Over maximum length
        $tooLongTitle = str_repeat('a', 256);
        $errors = \Validator::validatePostTitle($tooLongTitle);
        $this->assertNotEmpty($errors);
    }

    public function testPostContentEdgeCases(): void
    {
        // Empty content
        $errors = \Validator::validatePostContent('');
        $this->assertNotEmpty($errors);
        
        // Too short (less than 10 chars)
        $errors = \Validator::validatePostContent('Short');
        $this->assertNotEmpty($errors);
        
        // Exactly 10 chars
        $errors = \Validator::validatePostContent('1234567890');
        $this->assertEmpty($errors);
        
        // Valid long content
        $longContent = str_repeat('This is a test. ', 100);
        $errors = \Validator::validatePostContent($longContent);
        $this->assertEmpty($errors);
    }

    public function testXSSProtectionInPosts(): void
    {
        $maliciousTitle = '<script>alert("XSS")</script>Title';
        $maliciousContent = '<img src=x onerror="alert(1)">Content here with enough characters.';
        
        // Validation should pass (we validate length, not content)
        $titleErrors = \Validator::validatePostTitle($maliciousTitle);
        $contentErrors = \Validator::validatePostContent($maliciousContent);
        
        $this->assertEmpty($titleErrors);
        $this->assertEmpty($contentErrors);
        
        // But sanitization should escape HTML
        $sanitizedTitle = \Validator::sanitize($maliciousTitle);
        $sanitizedContent = \Validator::sanitize($maliciousContent);
        
        $this->assertStringNotContainsString('<script>', $sanitizedTitle);
        $this->assertStringNotContainsString('<img', $sanitizedContent);
        $this->assertStringContainsString('&lt;', $sanitizedTitle);
        $this->assertStringContainsString('&lt;', $sanitizedContent);
    }
}
