<?php

declare(strict_types=1);

$siteName = 'PrivateJetExecutive.com';
$pageTitle = $pageTitle ?? $siteName;
$metaDescription = $metaDescription ?? 'Private charter solutions from Indonesia to destinations worldwide.';
$canonicalPath = $canonicalPath ?? '/';
$appUrl = rtrim((string) (getenv('APP_URL') ?: 'https://privatejetexecutive.com'), '/');
$canonicalUrl = $appUrl . ($canonicalPath === '/' ? '/' : $canonicalPath);
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="<?= htmlspecialchars($metaDescription, ENT_QUOTES, 'UTF-8') ?>">
    <meta name="robots" content="index,follow">
    <link rel="canonical" href="<?= htmlspecialchars($canonicalUrl, ENT_QUOTES, 'UTF-8') ?>">
    <link rel="icon" href="/assets/images/favicon.ico" sizes="any">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="<?= $siteName ?>">
    <meta property="og:title" content="<?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?> | <?= $siteName ?>">
    <meta property="og:description" content="<?= htmlspecialchars($metaDescription, ENT_QUOTES, 'UTF-8') ?>">
    <meta property="og:url" content="<?= htmlspecialchars($canonicalUrl, ENT_QUOTES, 'UTF-8') ?>">
    <meta name="twitter:card" content="summary">
    <title><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?> | <?= $siteName ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;500;600&family=Manrope:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/main.css">
</head>
<body>
    <a class="skip-link" href="#main-content">Skip to content</a>
    <?php require dirname(__DIR__) . '/partials/header.php'; ?>
    <main id="main-content" tabindex="-1"><?= $content ?></main>
    <?php require dirname(__DIR__) . '/partials/footer.php'; ?>
    <script src="/assets/js/main.js" defer></script>
</body>
</html>
