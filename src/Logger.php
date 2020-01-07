<?php declare(strict_types=1);

namespace Serhii\TinyLogger;

use Exception;

final class Logger
{
    /**
     * @var string|null
     */
    private $file_path;

    /**
     * @var self|null
     */
    private static $instance;

    private function __construct() {}
    private function __clone() {}
    private function __wakeup() {}

    /**
     * Get class instance
     *
     * @see https://en.wikipedia.org/wiki/Singleton_pattern
     * @return self
     */
    public static function new(): self
    {
        return static::$instance ?? (static::$instance = new static());
    }

    /**
     * @param string $path
     * @return $this
     */
    public function setPath(string $path): self
    {
        $this->file_path = $path;
        return $this;
    }

    /**
     * @param array|object|string|bool|float|int|null mixed $text
     * @param string|null $log_type
     */
    public function write($text, ?string $log_type = 'error'): void
    {
        if (is_float($text) || is_int($text)) {
            $text = strval($text);
        }

        if (is_array($text) || is_object($text)) {
            $text = json_encode($text, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);
        }

        $timestamp = date('Y-m-d H:i:s');
        $insert = "[$timestamp] {$log_type}: {$text}\n";

        $this->createFileIfNotExist();

        file_put_contents($this->file_path, $insert, FILE_APPEND);
    }

    private function createFileIfNotExist(): void
    {
        if ($this->file_path === null) {
            throw new Exception('File path for logging output is not specified');
        }

        if (!file_exists($this->file_path)) {
            file_put_contents($this->file_path, '');
        }
    }
}

