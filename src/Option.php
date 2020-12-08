<?php

declare(strict_types=1);

namespace Serhii\TinyLogger;

final class Option
{
    public const AVAILABLE_OPTIONS = ['pos'];

    /**
     * @var string
     */
    private $input_options;

    /**
     * @var string[]
     */
    private $prepared_options = [];

    /**
     * @var string|null
     */
    private $error_type;

    public function __construct(string $input_options)
    {
        $this->input_options = $input_options;
    }

    public function getErrorType(): string
    {
        return $this->error_type ?? 'error';
    }

    public function prepare(): self
    {
        foreach (\explode('|', $this->input_options) as $option) {
            if (\in_array($option, self::AVAILABLE_OPTIONS, true)) {
                $this->prepared_options[] = $option;
                continue;
            }

            $this->error_type = $option;
        }

        return $this;
    }

    public function has(string $option_name): bool
    {
        return \in_array($option_name, $this->prepared_options, true);
    }
}
