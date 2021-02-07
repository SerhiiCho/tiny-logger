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
    public $file_path = 'logs-test.log';

    public function setUp(): void
    {
        Logger::setPath($this->file_path);
    }

    public function tearDown(): void
    {
        $this->removeFile($this->file_path);
        Logger::disablePostRequest();
    }

    private function removeFile(string $file_path): void
    {
        \file_exists($file_path) ? \unlink($file_path) : null;
    }

    /** @test */
    public function write_method_creates_log_file(): void
    {
        $this->assertFileNotExists($this->file_path);
        Logger::new()->write('Some log message goes here');
        $this->assertFileExists($this->file_path);
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
        Logger::new()->write('Nice text is here');
        Logger::new()->write(null);
        $log_file_content = \file_get_contents($this->file_path);
        $this->assertStringContainsString('] error: Nice text is here', $log_file_content);
        $this->assertStringContainsString('null', $log_file_content);
    }

    /** @test */
    public function write_method_writes_given_text_to_a_log_file_with_different_type(): void
    {
        Logger::new()->write('Nice text is here', 'debug');
        $log_file_content = \file_get_contents($this->file_path);
        $this->assertStringContainsString('] debug: Nice text is here', $log_file_content);
    }

    /** @test */
    public function write_method_can_except_array(): void
    {
        $array = ['hello' => 'world'];
        Logger::new()->write($array, 'info');

        $log_file_content = \file_get_contents($this->file_path);

        $json = \json_encode($array, JSON_PRETTY_PRINT);
        $this->assertStringContainsString($json, $log_file_content);
    }

    /** @test */
    public function write_method_can_except_object(): void
    {
        $obj = (object) ['hello' => 'world'];
        Logger::new()->write($obj, 'info');

        $log_file_content = \file_get_contents($this->file_path);

        $json = \json_encode($obj, JSON_PRETTY_PRINT);
        $this->assertStringContainsString($json, $log_file_content);
    }

    /** @test */
    public function function_creates_log_file(): void
    {
        $this->assertFileNotExists($this->file_path);
        tiny_log('Some log message goes here');
        $this->assertFileExists($this->file_path);
    }

    /** @test */
    public function Logger_has_method_helpers(): void
    {
        $this->assertFileNotExists($this->file_path);
        Logger::new()->error('Some log message goes here');
        $this->assertFileExists($this->file_path);

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
            Logger::new()->write($e);
        }

        $log_file_content = \file_get_contents($this->file_path);

        $this->assertStringContainsString('This is an exception', $log_file_content);
    }

    /** @test */
    public function write_method_can_except_error(): void
    {
        try {
            throw new Error('This is an error');
        } catch (Error $e) {
            Logger::new()->write($e);
        }

        $log_file_content = \file_get_contents($this->file_path);

        $this->assertStringContainsString('This is an error', $log_file_content);
    }

    /** @test */
    public function write_method_can_except_parse_error(): void
    {
        try {
            throw new ParseError('This is a parse error');
        } catch (Error $e) {
            Logger::new()->write($e);
        }

        $this->assertStringContainsString('This is a parse error', \file_get_contents($this->file_path));
    }

    /** @test */
    public function write_method_can_except_type_error(): void
    {
        try {
            throw new TypeError('This is a type error');
        } catch (Error $e) {
            Logger::new()->write($e);
        }

        $this->assertStringContainsString('This is a type error', \file_get_contents($this->file_path));
    }

    /** @test */
    public function write_method_can_except_boolean_true(): void
    {
        Logger::new()->write(true, 'info');
        $this->assertStringContainsString('true', \file_get_contents($this->file_path));
    }

    /** @test */
    public function write_method_can_except_boolean_false(): void
    {
        Logger::new()->write(false, 'info');
        $log_file_content = \file_get_contents($this->file_path);
        $this->assertStringContainsString('false', $log_file_content);
    }

    /** @test */
    public function write_method_can_except_null(): void
    {
        Logger::new()->write(null, 'info');
        $log_file_content = \file_get_contents($this->file_path);
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
    
    /** @test */
    public function you_can_pass_file_path_as_the_third_argument_in_function_and_it_will_not_change_global_file_path(): void
    {
        $file_path = 'nice.log';

        tiny_log('Text', 'info', $file_path);
        $this->assertSame($this->file_path, Logger::getPath());

        $this->removeFile($file_path);
    }

    /** @test */
    public function you_can_pass_file_path_as_the_third_argument_in_write_method_and_it_will_not_change_global_file_path(): void
    {
        $file_path = 'nice.log';

        Logger::new()->write('Text', 'info', $file_path);
        $this->assertSame($this->file_path, Logger::getPath());

        $this->removeFile($file_path);
    }

    /** @test */
    public function getPath_method_returns_file_path(): void
    {
        $this->assertSame($this->file_path, Logger::getPath());
    }
}
