<?php

/**
 * Simple Router
 * 
 * Handles GET and POST route registration and dispatching
 */
class Router
{
    private array $routes = [];

    /**
     * Register GET route
     * 
     * @param string $path
     * @param callable $handler
     */
    public function get(string $path, callable $handler): void
    {
        $this->routes['GET'][$path] = $handler;
    }

    /**
     * Register POST route
     * 
     * @param string $path
     * @param callable $handler
     */
    public function post(string $path, callable $handler): void
    {
        $this->routes['POST'][$path] = $handler;
    }

    /**
     * Dispatch request to appropriate handler
     * 
     * @param string $method
     * @param string $uri
     */
    public function dispatch(string $method, string $uri): void
    {
        // Remove query string
        $uri = strtok($uri, '?');
        
        // Remove trailing slash except for root
        if ($uri !== '/' && str_ends_with($uri, '/')) {
            $uri = rtrim($uri, '/');
        }

        // Check for exact match
        if (isset($this->routes[$method][$uri])) {
            call_user_func($this->routes[$method][$uri]);
            return;
        }

        // Check for dynamic routes (e.g., /posts/{id})
        foreach ($this->routes[$method] ?? [] as $route => $handler) {
            $pattern = $this->convertRouteToRegex($route);
            
            if (preg_match($pattern, $uri, $matches)) {
                array_shift($matches); // Remove full match
                call_user_func_array($handler, $matches);
                return;
            }
        }

        // No route found
        $this->notFound();
    }

    /**
     * Convert route pattern to regex
     * 
     * @param string $route
     * @return string
     */
    private function convertRouteToRegex(string $route): string
    {
        // Replace {id} with regex pattern for numbers
        $pattern = preg_replace('/\{id\}/', '(\d+)', $route);
        $pattern = preg_replace('/\{[a-zA-Z_]+\}/', '([^/]+)', $pattern);
        return '#^' . $pattern . '$#';
    }

    /**
     * Handle 404 Not Found
     */
    private function notFound(): void
    {
        http_response_code(404);
        echo '404 - Page Not Found';
    }
}
