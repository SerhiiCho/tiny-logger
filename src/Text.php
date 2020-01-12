<?php declare(strict_types=1);

namespace Serhii\TinyLogger;

final class Text
{
    /**
     * @var mixed
     */
    private $input_text;

    /**
     * @var string
     */
    private $prepared_text;

    /**
     * Text constructor.
     *
     * @param mixed $input_text
     */
    public function __construct($input_text)
    {
        $this->input_text = $input_text;
    }

    /**
     * @return self
     */
    public function prepare(): self
    {
        if (is_float($this->input_text) || is_int($this->input_text)) {
            $this->prepared_text = (string)$this->input_text;
            return $this;
        }

        if (is_array($this->input_text) || is_object($this->input_text)) {
            $this->prepared_text = json_encode($this->input_text, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            return $this;
        }

        $this->prepared_text = $this->input_text;

        return $this;
    }

    /**
     * @return string
     */
    public function getPreparedText(): string
    {
        return $this->prepared_text;
    }
}

