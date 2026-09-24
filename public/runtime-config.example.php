<?php

declare(strict_types=1);

/*
 * cPanel deployment only:
 * Copy this file as runtime-config.php in the domain document root, then set
 * app_root to the absolute repository path outside public_html.
 * This file is ignored by Git and denied by public/.htaccess.
 */
return [
    'app_root' => '/home/ACCOUNT/repositories/private-jet-executive-web',
];
