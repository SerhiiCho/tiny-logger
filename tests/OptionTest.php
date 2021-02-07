<?php

declare(strict_types=1);

namespace Serhii\Tests;

use PHPUnit\Framework\TestCase;
use Serhii\TinyLogger\Logger;

class OptionTest extends TestCase
{
    public $file_name = 'logs-test.log';

    public function setUp(): void
    {
        Logger::setPath($this->file_name);
    }

    public function tearDown(): void
    {
        \file_exists($this->file_name) ? \unlink($this->file_name) : null;
    }

    /** @test */
    public function pos_option_adds_trace_line_to_log_file(): void
    {
        Logger::new()->write('Nice text is here', 'pos|debug');
        $line_number = __LINE__ - 1;

        $log_file_content = \file_get_contents($this->file_name);

        $regex = \sprintf('!>>> %s on line: %d!', __FILE__, $line_number);
        $this->assertRegExp($regex, $log_file_content);
    }

    /** @test */
    public function if_pos_option_not_provided_trace_line_is_not_added(): void
    {
        Logger::new()->write('Nice text is here', 'debug');
        $line_number = __LINE__ - 1;

        $log_file_content = \file_get_contents($this->file_name);
        $regex = \sprintf('!>>> %s on line: %d!', __FILE__, $line_number);

        $this->assertNotRegExp($regex, $log_file_content);
        $this->assertNotRegExp('!>>>!', $log_file_content);
    }

    /** @test */
    public function pos_option_adds_trace_line_to_log_file_when_using_function(): void
    {
        tiny_log('Nice text is here', 'pos|debug');
        $line_number = __LINE__ - 1;

        $log_file_content = \file_get_contents($this->file_name);

        $regex = \sprintf('!>>> %s on line: %d!', __FILE__, $line_number);
        $this->assertRegExp($regex, $log_file_content);
    }

    /** @test */
    public function if_pos_option_not_provided_trace_line_is_not_added_when_using_function(): void
    {
        tiny_log('Nice text is here', 'debug');
        $line_number = __LINE__ - 1;

        $log_file_content = \file_get_contents($this->file_name);
        $regex = \sprintf('!>>> %s on line: %d!', __FILE__, $line_number);

        $this->assertNotRegExp($regex, $log_file_content);
        $this->assertNotRegExp('!>>>!', $log_file_content);
    }

    /** @test */
    public function you_can_pass_option_without_error_type(): void
    {
        tiny_log('Nice text is here', 'pos');
        $line_number = __LINE__ - 1;

        $log_file_content = \file_get_contents($this->file_name);

        $regex = \sprintf('!>>> %s on line: %d!', __FILE__, $line_number);
        $this->assertRegExp($regex, $log_file_content);
    }
}