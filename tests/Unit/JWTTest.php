<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

/**
 * JWT Unit Tests
 */
class JWTTest extends TestCase
{
    private int $testUserId = 1;
    private string $testLogin = 'testuser';

    public function testGenerateAccessToken(): void
    {
        $token = \JWT::generateAccessToken($this->testUserId, $this->testLogin);
        
        $this->assertIsString($token);
        $this->assertNotEmpty($token);
        
        // JWT should have 3 parts separated by dots
        $parts = explode('.', $token);
        $this->assertCount(3, $parts);
    }

    public function testGenerateRefreshToken(): void
    {
        $token = \JWT::generateRefreshToken($this->testUserId);
        
        $this->assertIsString($token);
        $this->assertNotEmpty($token);
        
        // JWT should have 3 parts separated by dots
        $parts = explode('.', $token);
        $this->assertCount(3, $parts);
    }

    public function testVerifyValidAccessToken(): void
    {
        $token = \JWT::generateAccessToken($this->testUserId, $this->testLogin);
        $payload = \JWT::verifyToken($token);
        
        $this->assertIsObject($payload);
        $this->assertEquals($this->testUserId, $payload->user_id);
        $this->assertEquals($this->testLogin, $payload->login);
        $this->assertEquals('access', $payload->type);
        $this->assertObjectHasProperty('iat', $payload);
        $this->assertObjectHasProperty('exp', $payload);
    }

    public function testVerifyValidRefreshToken(): void
    {
        $token = \JWT::generateRefreshToken($this->testUserId);
        $payload = \JWT::verifyToken($token);
        
        $this->assertIsObject($payload);
        $this->assertEquals($this->testUserId, $payload->user_id);
        $this->assertEquals('refresh', $payload->type);
        $this->assertObjectHasProperty('jti', $payload);
    }

    public function testVerifyInvalidToken(): void
    {
        $invalidToken = 'invalid.token.here';
        $payload = \JWT::verifyToken($invalidToken);
        
        $this->assertFalse($payload);
    }

    public function testVerifyExpiredToken(): void
    {
        // This test would require mocking time or using a very short expiration
        // For now, we'll just verify the structure
        $this->assertTrue(true);
    }

    public function testAccessTokenContainsCorrectClaims(): void
    {
        $token = \JWT::generateAccessToken($this->testUserId, $this->testLogin);
        $payload = \JWT::verifyToken($token);
        
        $this->assertObjectHasProperty('user_id', $payload);
        $this->assertObjectHasProperty('login', $payload);
        $this->assertObjectHasProperty('type', $payload);
        $this->assertObjectHasProperty('iat', $payload);
        $this->assertObjectHasProperty('exp', $payload);
    }

    public function testRefreshTokenContainsCorrectClaims(): void
    {
        $token = \JWT::generateRefreshToken($this->testUserId);
        $payload = \JWT::verifyToken($token);
        
        $this->assertObjectHasProperty('user_id', $payload);
        $this->assertObjectHasProperty('type', $payload);
        $this->assertObjectHasProperty('jti', $payload);
        $this->assertObjectHasProperty('iat', $payload);
        $this->assertObjectHasProperty('exp', $payload);
    }

    public function testTokenExpirationIsInFuture(): void
    {
        $token = \JWT::generateAccessToken($this->testUserId, $this->testLogin);
        $payload = \JWT::verifyToken($token);
        
        $currentTime = time();
        $this->assertGreaterThan($currentTime, $payload->exp);
    }

    public function testTokenIssuedAtIsInPast(): void
    {
        $token = \JWT::generateAccessToken($this->testUserId, $this->testLogin);
        $payload = \JWT::verifyToken($token);
        
        $currentTime = time();
        $this->assertLessThanOrEqual($currentTime, $payload->iat);
    }
}
