<?php

declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
header('X-Content-Type-Options: nosniff');

$query = trim((string) ($_GET['q'] ?? ''));

if (mb_strlen($query) < 2 || mb_strlen($query) > 80 || !preg_match('/^[\p{L}\p{N}\s,\.\-()]+$/u', $query)) {
    http_response_code(422);
    echo json_encode(['error' => 'Enter a valid airport, city, or IATA search term.']);
    exit;
}

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_name('pje_session');
    session_start();
}

$now = time();
$requests = $_SESSION['airport_search_requests'] ?? [];
$requests = array_values(array_filter($requests, static fn (mixed $time): bool => is_int($time) && $time > $now - 600));

if (count($requests) >= 40) {
    $_SESSION['airport_search_requests'] = $requests;
    http_response_code(429);
    echo json_encode(['error' => 'Too many airport searches. Please wait a moment and try again.']);
    exit;
}

$requests[] = $now;
$_SESSION['airport_search_requests'] = $requests;

if (!function_exists('curl_init')) {
    http_response_code(503);
    echo json_encode(['error' => 'Airport search is temporarily unavailable.']);
    exit;
}

$url = 'https://www.ratehawk.com/air/api/regions/?query=' . rawurlencode($query) . '&locale=en';
$curl = curl_init($url);

curl_setopt_array($curl, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_CONNECTTIMEOUT => 3,
    CURLOPT_TIMEOUT => 6,
    CURLOPT_FOLLOWLOCATION => false,
    CURLOPT_HTTPHEADER => ['Accept: application/json'],
    CURLOPT_USERAGENT => 'Private Jet Executive airport search/1.0',
]);

$body = curl_exec($curl);
$status = (int) curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
$error = curl_error($curl);
curl_close($curl);

if ($body === false || $status < 200 || $status >= 300) {
    error_log('RateHawk airport search failed: ' . ($error !== '' ? $error : 'HTTP ' . $status));
    http_response_code(502);
    echo json_encode(['error' => 'Airport search is temporarily unavailable.']);
    exit;
}

$decoded = json_decode($body, true);

if (!is_array($decoded)) {
    http_response_code(502);
    echo json_encode(['error' => 'Airport search returned an unexpected response.']);
    exit;
}

echo json_encode($decoded, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
