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
     * You can pass almost any type as the first argument and method will
     * figure out what it needs to do with this data in order to save it
     * into a file.
     *
     * @param array|object|string|bool|float|int $text
     * @param string|null $log_type
     * @throws \Exception
     */
    public function write($text, ?string $log_type = 'error'): void
    {
        if (is_float($text) || is_int($text)) {
            $text = (string) $text;
        }

        if (is_array($text) || is_object($text)) {
            $text = json_encode($text, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);
        }

        $insert = sprintf('[%s] %s: %s%s', date('Y-m-d H:i:s'), $log_type, $text, PHP_EOL);

        $this->createFileIfNotExist();

        file_put_contents($this->file_path, $insert, FILE_APPEND);
    }

    /**
     * @throws \Exception
     */
    private function createFileIfNotExist(): void
    {
        if (is_null($this->file_path)) {
            throw new Exception('File path for logging output is not specified');
        }

        if (!file_exists($this->file_path)) {
            file_put_contents($this->file_path, '');
        }
    }

    /**
     * In this context passing by reference is not critical. There is no point to
     * create new variable in memory for that simple task.
     *
     * @param array|object|string|bool|float|int $text
     * @return void
     */
    private function prepareTextForLogging(&$text): void
    {
        if (is_float($text) || is_int($text)) {
            $text = (string) $text;
        }

        if (is_array($text) || is_object($text)) {
            $text = json_encode($text, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);
        }
    }
}

