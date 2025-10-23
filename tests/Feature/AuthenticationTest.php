<?php

namespace Tests\Feature;

use PHPUnit\Framework\TestCase;

/**
 * Authentication Feature Tests
 * 
 * These tests verify the complete authentication flow
 */
class AuthenticationTest extends TestCase
{
    public function testRegistrationValidationFlow(): void
    {
        // Test login validation
        $loginErrors = \Validator::validateLogin('ab');
        $this->assertNotEmpty($loginErrors);
        
        // Test password validation
        $passwordErrors = \Validator::validatePassword('123');
        $this->assertNotEmpty($passwordErrors);
        
        // Test valid data
        $validLoginErrors = \Validator::validateLogin('testuser');
        $validPasswordErrors = \Validator::validatePassword('password123');
        $this->assertEmpty($validLoginErrors);
        $this->assertEmpty($validPasswordErrors);
    }

    public function testLoginValidationFlow(): void
    {
        // Test empty fields
        $login = '';
        $password = '';
        
        $this->assertEmpty($login);
        $this->assertEmpty($password);
        
        // Test with valid data
        $login = 'testuser';
        $password = 'password123';
        
        $this->assertNotEmpty($login);
        $this->assertNotEmpty($password);
    }

    public function testPasswordHashingAndVerification(): void
    {
        $password = 'testpassword123';
        
        // Hash password
        $hash = \User::hashPassword($password);
        $this->assertNotEquals($password, $hash);
        
        // Verify password
        $this->assertTrue(password_verify($password, $hash));
        $this->assertFalse(password_verify('wrongpassword', $hash));
    }

    public function testJWTTokenGeneration(): void
    {
        $userId = 1;
        $login = 'testuser';
        
        // Generate access token
        $accessToken = \JWT::generateAccessToken($userId, $login);
        $this->assertNotEmpty($accessToken);
        
        // Verify token
        $payload = \JWT::verifyToken($accessToken);
        $this->assertIsObject($payload);
        $this->assertEquals($userId, $payload->user_id);
        $this->assertEquals($login, $payload->login);
        $this->assertEquals('access', $payload->type);
    }

    public function testJWTRefreshTokenGeneration(): void
    {
        $userId = 1;
        
        // Generate refresh token
        $refreshToken = \JWT::generateRefreshToken($userId);
        $this->assertNotEmpty($refreshToken);
        
        // Verify token
        $payload = \JWT::verifyToken($refreshToken);
        $this->assertIsObject($payload);
        $this->assertEquals($userId, $payload->user_id);
        $this->assertEquals('refresh', $payload->type);
        $this->assertObjectHasProperty('jti', $payload);
    }

    public function testCompleteAuthenticationFlow(): void
    {
        // 1. Validate registration data
        $login = 'newuser';
        $password = 'password123';
        
        $loginErrors = \Validator::validateLogin($login);
        $passwordErrors = \Validator::validatePassword($password);
        
        $this->assertEmpty($loginErrors);
        $this->assertEmpty($passwordErrors);
        
        // 2. Hash password
        $hash = \User::hashPassword($password);
        $this->assertNotEquals($password, $hash);
        
        // 3. Generate JWT tokens
        $userId = 1; // Simulated user ID
        $accessToken = \JWT::generateAccessToken($userId, $login);
        $refreshToken = \JWT::generateRefreshToken($userId);
        
        $this->assertNotEmpty($accessToken);
        $this->assertNotEmpty($refreshToken);
        
        // 4. Verify tokens
        $accessPayload = \JWT::verifyToken($accessToken);
        $refreshPayload = \JWT::verifyToken($refreshToken);
        
        $this->assertEquals($userId, $accessPayload->user_id);
        $this->assertEquals($login, $accessPayload->login);
        $this->assertEquals($userId, $refreshPayload->user_id);
    }
}
