<?php
/**
 * Very small .env loader (no dependencies).
 * Supports lines like KEY=value, ignores comments and empty lines.
 */

if (!function_exists('loadEnv')) {
    function loadEnv(string $rootPath, string $fileName = '.env'): void
    {
        $path = rtrim($rootPath, '/').'/'.$fileName;
        if (!is_file($path) || !is_readable($path)) {
            return;
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if (!$lines) return;

        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || substr($line, 0, 1) === "#") continue;

            // KEY=VALUE
            $pos = strpos($line, '=');
            if ($pos === false) continue;

            $key = trim(substr($line, 0, $pos));
            $val = trim(substr($line, $pos + 1));

            // Strip surrounding quotes
            if ((str_starts_with($val, '"') && str_ends_with($val, '"')) || (substr($val, 0, 1) === "'" && substr($val, -1) === "'")) {
                $val = substr($val, 1, -1);
            }

            if ($key === '') continue;

            // Don't override existing env
            if (getenv($key) === false) {
                putenv($key.'='.$val);
                $_ENV[$key] = $val;
            }
        }
    }
}
