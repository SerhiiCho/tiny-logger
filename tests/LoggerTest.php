<?php

declare(strict_types=1);

namespace Serhii\Tests;

use Curl\Curl;
use Error;
use Exception;
use ParseError;
use PHPUnit\Framework\TestCase;
use Serhii\TinyLogger\Logger;
use Serhii\TinyLogger\Option;
use Serhii\TinyLogger\Text;
use TypeError;

use function SandFox\Debug\call_private_method;

class LoggerTest extends TestCase
{
    public $file_name = 'logs-test.log';

    public function setUp(): void
    {
        Logger::setPath($this->file_name);
    }

    public function tearDown(): void
    {
        \file_exists($this->file_name) ? \unlink($this->file_name) : null;
        Logger::disablePostRequest();
    }

    /** @test */
    public function write_method_creates_log_file(): void
    {
        $this->assertFileNotExists($this->file_name);
        Logger::write('Some log message goes here');
        $this->assertFileExists($this->file_name);
    }

    /** @test */
    public function setPath_changes_file_path(): void
    {
        Logger::setPath('different.log')->write('Some log message goes here');
        $this->assertFileExists('different.log');
        \file_exists('different.log') ? \unlink('different.log') : null;
    }

    /** @test */
    public function you_can_use_sprintf_syntax_int_insetPath_method(): void
    {
        Logger::setPath('%serent%s', 'diff', '.log')->write('Some log message goes here');
        $this->assertFileExists('different.log');
        \file_exists('different.log') ? \unlink('different.log') : null;
    }

    /** @test */
    public function write_method_writes_given_text_to_a_log_file(): void
    {
        Logger::write('Nice text is here');
        Logger::write(null);
        $log_file_content = \file_get_contents($this->file_name);
        $this->assertStringContainsString('] error: Nice text is here', $log_file_content);
        $this->assertStringContainsString('null', $log_file_content);
    }

    /** @test */
    public function write_method_writes_given_text_to_a_log_file_with_different_type(): void
    {
        Logger::write('Nice text is here', 'debug');
        $log_file_content = \file_get_contents($this->file_name);
        $this->assertStringContainsString('] debug: Nice text is here', $log_file_content);
    }

    /** @test */
    public function write_method_can_except_array(): void
    {
        $array = ['hello' => 'world'];
        Logger::write($array, 'info');

        $log_file_content = \file_get_contents($this->file_name);

        $json = \json_encode($array, JSON_PRETTY_PRINT);
        $this->assertStringContainsString($json, $log_file_content);
    }

    /** @test */
    public function write_method_can_except_object(): void
    {
        $obj = (object) ['hello' => 'world'];
        Logger::write($obj, 'info');

        $log_file_content = \file_get_contents($this->file_name);

        $json = \json_encode($obj, JSON_PRETTY_PRINT);
        $this->assertStringContainsString($json, $log_file_content);
    }

    /** @test */
    public function function_creates_log_file(): void
    {
        $this->assertFileNotExists($this->file_name);
        tiny_log('Some log message goes here');
        $this->assertFileExists($this->file_name);
    }

    /** @test */
    public function Logger_has_method_helpers(): void
    {
        $this->assertFileNotExists($this->file_name);
        Logger::error('Some log message goes here');
        $this->assertFileExists($this->file_name);

        $instance = Logger::new();

        $this->assertTrue(method_exists($instance, 'emergency'));
        $this->assertTrue(method_exists($instance, 'alert'));
        $this->assertTrue(method_exists($instance, 'critical'));
        $this->assertTrue(method_exists($instance, 'error'));
        $this->assertTrue(method_exists($instance, 'warning'));
        $this->assertTrue(method_exists($instance, 'notice'));
        $this->assertTrue(method_exists($instance, 'info'));
        $this->assertTrue(method_exists($instance, 'debug'));
    }

    /** @test */
    public function write_method_can_except_exception(): void
    {
        try {
            throw new Exception('This is an exception');
        } catch (Exception $e) {
            Logger::write($e);
        }

        $log_file_content = \file_get_contents($this->file_name);

        $this->assertStringContainsString('This is an exception', $log_file_content);
    }

    /** @test */
    public function write_method_can_except_error(): void
    {
        try {
            throw new Error('This is an error');
        } catch (Error $e) {
            Logger::write($e);
        }

        $log_file_content = \file_get_contents($this->file_name);

        $this->assertStringContainsString('This is an error', $log_file_content);
    }

    /** @test */
    public function write_method_can_except_parse_error(): void
    {
        try {
            throw new ParseError('This is a parse error');
        } catch (Error $e) {
            Logger::write($e);
        }

        $this->assertStringContainsString('This is a parse error', \file_get_contents($this->file_name));
    }

    /** @test */
    public function write_method_can_except_type_error(): void
    {
        try {
            throw new TypeError('This is a type error');
        } catch (Error $e) {
            Logger::write($e);
        }

        $this->assertStringContainsString('This is a type error', \file_get_contents($this->file_name));
    }

    /** @test */
    public function write_method_can_except_boolean_true(): void
    {
        Logger::write(true, 'info');
        $this->assertStringContainsString('true', \file_get_contents($this->file_name));
    }

    /** @test */
    public function write_method_can_except_boolean_false(): void
    {
        Logger::write(false, 'info');
        $log_file_content = \file_get_contents($this->file_name);
        $this->assertStringContainsString('false', $log_file_content);
    }

    /** @test */
    public function write_method_can_except_null(): void
    {
        Logger::write(null, 'info');
        $log_file_content = \file_get_contents($this->file_name);
        $this->assertStringContainsString('null', $log_file_content);
    }

    /** @test */
    public function makePostRequestIfOptionIsEnabled_makes_request_if_option_is_enabled(): void
    {
        $text = new Text('Error message');
        $option = new Option('info');

        $curl = $this->createMock(Curl::class);

        $curl->expects($this->once())->method('setHeader');
        $curl->expects($this->once())->method('post');

        Logger::enablePostRequest('http://my-site.io/web-hook');

        call_private_method(Logger::new(), 'makePostRequestIfOptionIsEnabled', $text, $option, $curl);
    }

    /** @test */
    public function makePostRequestIfOptionIsEnabled_doesnt_make_request_if_option_is_not_enabled(): void
    {
        $text = new Text('My error message');
        $option = new Option('error');

        $curl = $this->createMock(Curl::class);

        $curl->expects($this->never())->method('setHeader');
        $curl->expects($this->never())->method('post');

        call_private_method(Logger::new(), 'makePostRequestIfOptionIsEnabled', $text, $option, $curl);
    }

    /** @test */
    public function disablePostRequest_disables_post_request_after_option_has_been_enabled_enabled(): void
    {
        $text = new Text('Some error message');
        $option = new Option('debug');

        $curl = $this->createMock(Curl::class);

        $curl->expects($this->never())->method('setHeader');
        $curl->expects($this->never())->method('post');

        Logger::enablePostRequest('http://site.io/web-hook');
        Logger::disablePostRequest();

        call_private_method(Logger::new(), 'makePostRequestIfOptionIsEnabled', $text, $option, $curl);
    }
}
