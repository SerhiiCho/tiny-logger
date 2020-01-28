<?php declare(strict_types=1);

namespace Serhii\TinyLogger;

use Exception;
use Psr\Log\LoggerInterface;

final class Logger implements LoggerInterface
{
    /** @var string|null */
    private $file_path;

    /** @var self|null */
    private static $instance;

    private function __construct() {}

    private function __clone() {}

    private function __wakeup() {}

    /**
     * Get singleton instance of the class.
     *
     * @see https://en.wikipedia.org/wiki/Singleton_pattern
     * @return self
     */
    public static function new(): self
    {
        return static::$instance ?? (static::$instance = new static());
    }

    /**
     * Set path for logging output, if file doesn't exists it will be created.
     * It will not create directories, so make sure you have directories in
     * the provided path.
     *
     * @param string $path Absolute or relative path to a directory where log file
     * will be created. Sprintf syntax is allowed for this method like so:
     * setPath('%s/storage/logs/logs.log', '/var/www/html')
     *
     * @param array $params
     * @return $this
     */
    public function setPath(string $path, ...$params): self
    {
        $this->file_path = sprintf($path, ...$params);
        return $this;
    }

    /**
     * You can pass almost any type as the first argument and method will
     * figure out what it needs to do with this data in order to save it
     * into a file.
     *
     * @param mixed $text Text that will be written as a context. Can be any type.
     * If Throwable object is passed, it will be logged with whole stack trace,
     * error message and line number.
     * @param string|null $options Options can be log type like "error",
     * "debug", "warning" etc... Also you can pass option "pos".
     * To pass both option and log type separate them with pipe character
     * like that: "pos|info".
     *
     * @throws \Exception Throws if file path wasn't wasn't provided by setPath()
     * method. Make sure that setPath() is called before the logging happens.
     */
    public function write($text, ?string $options = 'error'): void
    {
        $this->createFileIfNotExist();
        $input = $this->prepareTextForLogging(new Text($text), new Option($options));
        file_put_contents($this->file_path, $input, FILE_APPEND);
    }

    /**
     * @throws \Exception Throws if file path wasn't wasn't provided by setPath() method.
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

    private function prepareTextForLogging(Text $text, Option $option): string
    {
        $text->prepare();
        $option->prepare();

        $result = "{$text->getDateBlock()} {$option->getErrorType()}: {$text->getPreparedText()}" . PHP_EOL;

        if ($option->has('pos')) {
            return $result . $text->getTraceLine();
        }

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function emergency($message, array $context = [])
    {
        $this->write($message, 'emergency');
    }

    /**
     * @inheritDoc
     */
    public function alert($message, array $context = [])
    {
        $this->write($message, 'alert');
    }

    /**
     * @inheritDoc
     */
    public function critical($message, array $context = [])
    {
        $this->write($message, 'critical');
    }

    /**
     * @inheritDoc
     */
    public function error($message, array $context = [])
    {
        $this->write($message, 'error');
    }

    /**
     * @inheritDoc
     */
    public function warning($message, array $context = [])
    {
        $this->write($message, 'warning');
    }

    /**
     * @inheritDoc
     */
    public function notice($message, array $context = [])
    {
        $this->write($message, 'notice');
    }

    /**
     * @inheritDoc
     */
    public function info($message, array $context = [])
    {
        $this->write($message, 'info');
    }

    /**
     * @inheritDoc
     */
    public function debug($message, array $context = [])
    {
        $this->write($message, 'degug');
    }

    /**
     * @inheritDoc
     */
    public function log($level, $message, array $context = [])
    {
        $this->write($message, 'log');
    }
}
