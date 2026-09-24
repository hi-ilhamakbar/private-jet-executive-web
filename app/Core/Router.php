<?php

declare(strict_types=1);

namespace App\Core;

final class Router
{
    /** @var array<string, string> */
    private array $routes;

    /** @param array<string, string> $routes */
    public function __construct(array $routes)
    {
        $this->routes = $routes;
    }

    public function resolve(string $path): string
    {
        $normalisedPath = '/' . trim(rawurldecode($path), '/');

        return $this->routes[$normalisedPath] ?? 'not-found';
    }
}
