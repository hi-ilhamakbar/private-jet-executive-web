<?php

declare(strict_types=1);

use App\Core\Router;
use App\Core\View;
use App\Forms\InquiryForms;
use App\Forms\InquiryReference;
use App\Mail\InquiryMailer;
use App\Core\Environment;

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
require $projectRoot . '/app/Core/Environment.php';
require $projectRoot . '/app/Forms/InquiryForms.php';
require $projectRoot . '/app/Forms/InquiryReference.php';
require $projectRoot . '/app/Mail/InquiryMailer.php';

date_default_timezone_set('Asia/Jakarta');
Environment::load($projectRoot);

header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('Permissions-Policy: camera=(), geolocation=(), microphone=()');
header("Content-Security-Policy: default-src 'self'; img-src 'self' data: https://flagcdn.com; style-src 'self' https://fonts.googleapis.com; font-src 'self' https://fonts.gstatic.com; script-src 'self'; base-uri 'self'; form-action 'self'; frame-ancestors 'none'");

$router = new Router([
    '/' => 'home',
    '/private-charter' => 'private-charter',
    '/services' => 'services',
    '/service' => 'services',
    '/destinations' => 'destinations',
    '/about' => 'about',
    '/contact' => 'contact',
]);

$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$page = $router->resolve($path);

$formName = match ($page) {
    'private-charter' => 'charter',
    'contact' => 'contact',
    default => null,
};
$formState = ['errors' => [], 'old' => [], 'notice' => null, 'submitted' => false, 'ready' => false];
$csrfToken = '';
$captchaQuestion = '';

if ($formName !== null) {
    InquiryForms::startSession();
    $csrfToken = InquiryForms::csrfToken();

    if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
        $formState = InquiryForms::process($formName, $_POST);

        if ($formState['ready'] === true) {
            if (InquiryForms::isDuplicateSubmission($formName, $formState['old'])) {
                $formState['notice'] = 'We have already received this enquiry. Our team will be in touch shortly.';
            } else {
                try {
                    $reference = InquiryReference::generate($formName);
                    InquiryMailer::send($formName, $formState['old'], $reference);
                    InquiryForms::rememberSubmission($formName, $formState['old']);
                    InquiryForms::flashSuccess($formName, $reference);
                    header('Location: ' . $path, true, 303);
                    exit;
                } catch (Throwable $exception) {
                    error_log('Inquiry email delivery failed: ' . $exception->getMessage());
                    $formState['errors']['_form'] = 'We could not send your enquiry at this time. Please email charter@privatejetexecutive.com directly.';
                }
            }
        }
    } else {
        $reference = InquiryForms::consumeFlash($formName);
        if ($reference !== null) {
            $label = $formName === 'charter' ? 'charter inquiry' : 'message';
            $formState['notice'] = 'Your ' . $label . ' has been received. Reference: ' . $reference . '. A confirmation has been sent to your email address.';
        }
    }

    $captchaQuestion = InquiryForms::captcha($formName)['question'];
}

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
    'private-charter' => [
        'pageTitle' => 'Private Charter',
        'metaDescription' => 'Tailored private aircraft charter solutions from Indonesia to destinations worldwide.',
        'canonicalPath' => '/private-charter',
    ],
    'services' => [
        'pageTitle' => 'Aviation Services',
        'metaDescription' => 'Private charter solutions for leisure, corporate, group, medical and specialised travel requirements.',
        'canonicalPath' => '/services',
    ],
    'destinations' => [
        'pageTitle' => 'Global Destinations',
        'metaDescription' => 'Private charter solutions connecting Indonesia with destinations across the world.',
        'canonicalPath' => '/destinations',
    ],
    'about' => [
        'pageTitle' => 'About Us',
        'metaDescription' => 'An Indonesia-based private aviation service with a global outlook and personal approach.',
        'canonicalPath' => '/about',
    ],
    'contact' => [
        'pageTitle' => 'Contact Our Team',
        'metaDescription' => 'Speak with the Private Jet Executive team about your private charter requirements.',
        'canonicalPath' => '/contact',
    ],
    default => [
        'pageTitle' => 'Private Jet Executive',
        'metaDescription' => 'Private charter solutions from Indonesia to destinations worldwide.',
        'canonicalPath' => $path,
    ],
};

View::render($page, $pageData + [
    'formState' => $formState,
    'csrfToken' => $csrfToken,
    'captchaQuestion' => $captchaQuestion,
]);
