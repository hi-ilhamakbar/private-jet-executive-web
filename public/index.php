<?php

declare(strict_types=1);

use App\Core\Router;
use App\Core\View;

/**
 * Resolve the non-public application directory.
 *
 * Local development uses the repository parent of /public. On cPanel, copy
 * only /public into the domain document root and provide the repository path
 * through public/runtime-config.php or the PJE_APP_ROOT environment variable.
 */
function projectRoot(): string
{
    $candidates = [dirname(__DIR__)];
    $runtimeConfig = __DIR__ . '/runtime-config.php';

    if (is_file($runtimeConfig)) {
        $config = require $runtimeConfig;

        if (is_array($config) && is_string($config['app_root'] ?? null)) {
            $candidates[] = $config['app_root'];
        }
    }

    $environmentRoot = getenv('PJE_APP_ROOT');
    if (is_string($environmentRoot) && $environmentRoot !== '') {
        $candidates[] = $environmentRoot;
    }

    $documentRoot = $_SERVER['DOCUMENT_ROOT'] ?? '';
    if (is_string($documentRoot) && $documentRoot !== '') {
        // Standard cPanel layout: /home/ACCOUNT/public_html/DOMAIN.
        $accountRoot = dirname(dirname($documentRoot));
        $candidates[] = $accountRoot . '/repositories/private-jet-executive-web';
    }

    foreach ($candidates as $candidate) {
        $resolved = realpath($candidate);

        if ($resolved !== false && is_file($resolved . '/app/Core/Router.php')) {
            return $resolved;
        }
    }

    throw new RuntimeException('Application root could not be resolved.');
}

try {
    $projectRoot = projectRoot();
} catch (RuntimeException $exception) {
    error_log('PrivateJetExecutive application root is unavailable.');
    http_response_code(500);
    header('Content-Type: text/html; charset=utf-8');
    exit('Service temporarily unavailable.');
}

require $projectRoot . '/app/Core/Router.php';
require $projectRoot . '/app/Core/View.php';

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
