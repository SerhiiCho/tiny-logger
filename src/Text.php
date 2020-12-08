<?php declare(strict_types=1);

namespace Serhii\TinyLogger;

use Throwable;

final class Text
{
    /** @var mixed */
    private $input_text;

    /** @var string */
    private $prepared_text;

    public function __construct($input_text)
    {
        $this->input_text = $input_text;
    }

    public function prepare(): self
    {
        if ($this->input_text instanceof Throwable) {
            $e = $this->input_text;
            $this->prepared_text = "{$e->getMessage()} in {$e->getFile()} at line: {$e->getLine()}\n{$e->getTraceAsString()}";
            return $this;
        }

        if (\is_float($this->input_text) || \is_int($this->input_text)) {
            $this->prepared_text = (string)$this->input_text;
            return $this;
        }

        if (\is_array($this->input_text) || \is_object($this->input_text)) {
            $this->prepared_text = \json_encode($this->input_text, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            return $this;
        }

        $this->prepared_text = $this->input_text;

        return $this;
    }

    public function getPreparedText(): string
    {
        if ($this->prepared_text === true) {
            return 'true';
        }

        if ($this->prepared_text === false) {
            return 'false';
        }

        return $this->prepared_text ?? 'null';
    }

    public function getTraceLine(): string
    {
        $trace = \debug_backtrace()[2];

        if (!!\preg_match('!/logger\.php!', $trace['file'])) {
            $trace = \debug_backtrace()[3];
        }

        return ">>> {$trace['file']} on line: {$trace['line']}" . PHP_EOL;
    }

    public function getDateBlock(): string
    {
        return '[' . \date('Y-m-d H:i:s') . ']';
    }
}

