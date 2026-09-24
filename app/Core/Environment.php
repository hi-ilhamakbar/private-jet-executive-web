<?php

declare(strict_types=1);

namespace App\Core;

final class Environment
{
    /** @var array<string, string> */
    private static array $values = [];

    public static function load(string $projectRoot): void
    {
        $configuredPath = getenv('PJE_ENV_PATH');
        $accountRoot = dirname(dirname($projectRoot));
        $paths = array_filter([
            is_string($configuredPath) ? $configuredPath : null,
            $accountRoot . '/privatejetexecutive-config/.env',
            $projectRoot . '/.env',
        ]);

        foreach ($paths as $path) {
            if (is_file($path)) {
                self::parseFile($path);
                return;
            }
        }
    }

    public static function get(string $key, string $default = ''): string
    {
        return self::$values[$key] ?? (string) (getenv($key) ?: $default);
    }

    private static function parseFile(string $path): void
    {
        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        if ($lines === false) {
            return;
        }

        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) {
                continue;
            }

            [$key, $value] = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);

            if (!preg_match('/^[A-Z][A-Z0-9_]*$/', $key)) {
                continue;
            }

            self::$values[$key] = trim($value, "\"'");
        }
    }
}
