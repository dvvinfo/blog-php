<?php

namespace Tests\Unit\Models;

use PHPUnit\Framework\TestCase;

/**
 * User Model Unit Tests
 * 
 * Note: These tests require database connection
 * Run with: docker exec personal_blog_web vendor/bin/phpunit
 */
class UserTest extends TestCase
{
    public function testHashPassword(): void
    {
        $password = 'testpassword123';
        $hash = \User::hashPassword($password);
        
        $this->assertIsString($hash);
        $this->assertNotEquals($password, $hash);
        $this->assertGreaterThan(50, strlen($hash)); // bcrypt hashes are long
    }

    public function testHashPasswordProducesDifferentHashes(): void
    {
        $password = 'testpassword123';
        $hash1 = \User::hashPassword($password);
        $hash2 = \User::hashPassword($password);
        
        // Same password should produce different hashes (due to salt)
        $this->assertNotEquals($hash1, $hash2);
    }

    public function testPasswordVerification(): void
    {
        $password = 'testpassword123';
        $hash = \User::hashPassword($password);
        
        // Verify correct password
        $this->assertTrue(password_verify($password, $hash));
        
        // Verify incorrect password
        $this->assertFalse(password_verify('wrongpassword', $hash));
    }
}
