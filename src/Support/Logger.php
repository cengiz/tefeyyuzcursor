<?php
declare(strict_types=1);

namespace Tefeyyuz\Support;

final class Logger
{
    public function __construct(private readonly string $path)
    {
        $dir = dirname($this->path);
        if (!is_dir($dir)) {
            mkdir($dir, 0775, true);
        }
    }

    public function info(string $message, array $context = []): void
    {
        $this->write('INFO', $message, $context);
    }

    public function warning(string $message, array $context = []): void
    {
        $this->write('WARNING', $message, $context);
    }

    public function error(string $message, array $context = []): void
    {
        $this->write('ERROR', $message, $context);
    }

    private function write(string $level, string $message, array $context): void
    {
        $line = sprintf(
            "[%s] %s %s %s%s",
            date('c'),
            $level,
            $message,
            $context !== [] ? json_encode($context, JSON_UNESCAPED_UNICODE) : '',
            PHP_EOL
        );
        file_put_contents($this->path, $line, FILE_APPEND | LOCK_EX);
    }
}
