<?php
namespace PhpGuru;

class Router
{
    private array $routes = [];

    public function get(string $path, array $handler): void
    {
        $this->routes['GET'][$path] = $handler;
    }

    public function post(string $path, array $handler): void
    {
        $this->routes['POST'][$path] = $handler;
    }

    public function dispatch(string $method, string $uri): void
    {
        $uri = parse_url($uri, PHP_URL_PATH);

        if (isset($this->routes[$method][$uri])) {
            $handler = $this->routes[$method][$uri];
            [$controller, $action] = $handler;
            $controller->$action();
        } else {
            http_response_code(404);
            echo '<h1>404 — Page Not Found</h1>';
        }
    }
}
