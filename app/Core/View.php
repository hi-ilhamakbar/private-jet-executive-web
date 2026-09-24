<?php

declare(strict_types=1);

namespace App\Core;

final class View
{
    /** @param array<string, mixed> $data */
    public static function render(string $template, array $data = []): void
    {
        $templatePath = dirname(__DIR__, 2) . '/templates/pages/' . $template . '.php';

        if (!is_file($templatePath)) {
            http_response_code(500);
            $templatePath = dirname(__DIR__, 2) . '/templates/pages/error.php';
            $data = ['pageTitle' => 'Service Unavailable'];
        }

        extract($data, EXTR_SKIP);
        ob_start();
        require $templatePath;
        $content = (string) ob_get_clean();
        require dirname(__DIR__, 2) . '/templates/layouts/base.php';
    }
}
