<?php

declare(strict_types=1);

use App\Core\Router;
use App\Core\View;

require dirname(__DIR__) . '/app/Core/Router.php';
require dirname(__DIR__) . '/app/Core/View.php';

date_default_timezone_set('Asia/Jakarta');

header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('Permissions-Policy: camera=(), geolocation=(), microphone=()');
header("Content-Security-Policy: default-src 'self'; img-src 'self' data:; style-src 'self' https://fonts.googleapis.com; font-src 'self' https://fonts.gstatic.com; script-src 'self'; base-uri 'self'; form-action 'self'; frame-ancestors 'none'");

$router = new Router([
    '/' => 'home',
    '/private-charter' => 'coming-soon',
    '/services' => 'coming-soon',
    '/destinations' => 'coming-soon',
    '/about' => 'coming-soon',
    '/contact' => 'coming-soon',
]);

$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$page = $router->resolve($path);

if ($page === 'not-found') {
    http_response_code(404);
}

$pageData = match ($page) {
    'home' => [
        'pageTitle' => 'Private Aviation, Redefined',
        'metaDescription' => 'Private charter solutions from Indonesia to destinations worldwide.',
        'canonicalPath' => '/',
    ],
    'not-found' => [
        'pageTitle' => 'Page Not Found',
        'metaDescription' => 'The requested page could not be found.',
        'canonicalPath' => $path,
    ],
    default => [
        'pageTitle' => 'PrivateJetExecutive.com',
        'metaDescription' => 'Private charter solutions from Indonesia to destinations worldwide.',
        'canonicalPath' => $path,
    ],
};

View::render($page, $pageData);
