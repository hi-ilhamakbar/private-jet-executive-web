<?php

declare(strict_types=1);

/*
 * Copy to config/local.php only when environment variables are not available.
 * Keep that file outside version control and, in production, outside public_html.
 */
return [
    'app_url' => 'https://privatejetexecutive.com',
    'timezone' => 'Asia/Jakarta',
    'mail' => [
        'host' => 'mail.jasatiketmurah.com',
        'port' => 465,
        'username' => 'charter@privatejetexecutive.com',
        'password' => 'change-me',
        'encryption' => 'ssl',
    ],
];
