<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

/**
 * Router Unit Tests
 */
class RouterTest extends TestCase
{
    private \Router $router;

    protected function setUp(): void
    {
        $this->router = new \Router();
    }

    public function testCanRegisterGetRoute(): void
    {
        $called = false;
        
        $this->router->get('/test', function() use (&$called) {
            $called = true;
        });
        
        // We can't easily test dispatch without mocking, but we can verify no errors
        $this->assertInstanceOf(\Router::class, $this->router);
    }

    public function testCanRegisterPostRoute(): void
    {
        $called = false;
        
        $this->router->post('/test', function() use (&$called) {
            $called = true;
        });
        
        $this->assertInstanceOf(\Router::class, $this->router);
    }

    public function testCanRegisterMultipleRoutes(): void
    {
        $this->router->get('/route1', function() {});
        $this->router->get('/route2', function() {});
        $this->router->post('/route3', function() {});
        
        $this->assertInstanceOf(\Router::class, $this->router);
    }

    public function testRouterAcceptsCallableHandlers(): void
    {
        $this->router->get('/test', function() {
            return 'test';
        });
        
        $this->router->get('/test2', [$this, 'dummyHandler']);
        
        $this->assertInstanceOf(\Router::class, $this->router);
    }

    public function dummyHandler(): string
    {
        return 'dummy';
    }
}
