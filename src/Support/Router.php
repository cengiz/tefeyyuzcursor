<?php
declare(strict_types=1);

namespace Tefeyyuz\Support;

final class Router
{
    /** @var list<array{method:string,pattern:string,handler:callable}> */
    private array $routes = [];

    public function get(string $pattern, callable $handler): void
    {
        $this->add('GET', $pattern, $handler);
    }

    public function post(string $pattern, callable $handler): void
    {
        $this->add('POST', $pattern, $handler);
    }

    private function add(string $method, string $pattern, callable $handler): void
    {
        $this->routes[] = compact('method', 'pattern', 'handler');
    }

    public function dispatch(string $method, string $path): mixed
    {
        $path = '/' . trim($path, '/');
        if ($path !== '/') {
            $path .= '/';
        }
        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }
            $regex = '#^' . $route['pattern'] . '$#u';
            if (preg_match($regex, $path, $matches)) {
                $args = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                return ($route['handler'])(...array_values($args));
            }
        }
        http_response_code(404);
        return null;
    }
}
