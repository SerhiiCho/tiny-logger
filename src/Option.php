<?php declare(strict_types=1);

namespace Serhii\TinyLogger;

use stdClass;

final class Option
{
    const AVAILABLE_OPTIONS = ['pos'];

    /**
     * @var string
     */
    private $input_options;

    /**
     * @var array
     */
    private $converted_options = [];

    /**
     * @var string
     */
    private $error_type;

    /**
     * Option constructor.
     *
     * @param string $input_options
     */
    public function __construct(string $input_options)
    {
        $this->input_options = $input_options;
    }

    /**
     * @return string
     */
    public function getErrorType(): string
    {
        return $this->error_type;
    }

    /**
     * @return self
     */
    public function prepare(): self
    {
        foreach (explode('|', $this->input_options) as $option) {
            if (in_array($option, self::AVAILABLE_OPTIONS)) {
                $this->converted_options[] = $option;
                continue;
            }

            $this->error_type = $option;
        }

        return $this;
    }

    /**
     * Check if option exists in the list of options.
     *
     * @param string $option_name
     * @return bool
     */
    public function has(string $option_name): bool
    {
        return in_array($option_name, $this->converted_options);
    }
}

