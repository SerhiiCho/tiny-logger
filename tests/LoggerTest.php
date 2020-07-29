<?php declare(strict_types=1);

namespace Serhii\Tests;

use Error;
use Exception;
use TypeError;
use ParseError;
use Serhii\TinyLogger\Logger;
use PHPUnit\Framework\TestCase;

class LoggerTest extends TestCase
{
    public $file_name = 'logs-test.log';

    public function setUp(): void
    {
        Logger::new()->setPath($this->file_name);
    }

    public function tearDown(): void
    {
        @unlink($this->file_name);
    }

    /** @test */
    public function write_method_creates_log_file(): void
    {
        $this->assertFileNotExists($this->file_name);
        Logger::new()->write('Some log message goes here');
        $this->assertFileExists($this->file_name);
    }

    /** @test */
    public function setPath_changes_file_path(): void
    {
        Logger::new()->setPath('different.log')->write('Some log message goes here');
        $this->assertFileExists('different.log');
        @unlink('different.log');
    }

    /** @test */
    public function you_can_use_sprintf_syntax_int_insetPath_method(): void
    {
        Logger::new()->setPath('%serent%s', 'diff', '.log')->write('Some log message goes here');
        $this->assertFileExists('different.log');
        @unlink('different.log');
    }

    /** @test */
    public function write_method_writes_given_text_to_a_log_file(): void
    {
        Logger::new()->write('Nice text is here');
        Logger::new()->write(null);
        $log_file_content = file_get_contents($this->file_name);
        $this->assertRegExp('/] error: Nice text is here/', $log_file_content);
        $this->assertRegExp('/null/', $log_file_content);
    }

    /** @test */
    public function write_method_writes_given_text_to_a_log_file_with_different_type(): void
    {
        Logger::new()->write('Nice text is here', 'debug');
        $log_file_content = file_get_contents($this->file_name);
        $this->assertRegExp('/] debug: Nice text is here/', $log_file_content);
    }

    /** @test */
    public function write_method_can_except_array(): void
    {
        $array = ['hello' => 'world'];
        Logger::new()->write($array, 'info');

        $log_file_content = file_get_contents($this->file_name);

        $json = json_encode($array, JSON_PRETTY_PRINT);
        $this->assertRegExp("/$json/", $log_file_content);
    }

    /** @test */
    public function write_method_can_except_object(): void
    {
        $obj = (object) ['hello' => 'world'];
        Logger::new()->write($obj, 'info');

        $log_file_content = file_get_contents($this->file_name);

        $json = json_encode($obj, JSON_PRETTY_PRINT);
        $this->assertRegExp("/$json/", $log_file_content);
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
        Logger::new()->error('Some log message goes here');
        $this->assertFileExists($this->file_name);

        $this->assertTrue(method_exists(Logger::new(), 'emergency'));
        $this->assertTrue(method_exists(Logger::new(), 'alert'));
        $this->assertTrue(method_exists(Logger::new(), 'critical'));
        $this->assertTrue(method_exists(Logger::new(), 'error'));
        $this->assertTrue(method_exists(Logger::new(), 'warning'));
        $this->assertTrue(method_exists(Logger::new(), 'notice'));
        $this->assertTrue(method_exists(Logger::new(), 'info'));
        $this->assertTrue(method_exists(Logger::new(), 'debug'));
    }

    /** @test */
    public function write_method_can_except_exception(): void
    {
        try {
            throw new Exception('This is an exception');
        } catch (Exception $e) {
            Logger::new()->write($e);
        }

        $log_file_content = file_get_contents($this->file_name);

        $this->assertRegExp("/This is an exception/", $log_file_content);
    }

    /** @test */
    public function write_method_can_except_error(): void
    {
        try {
            throw new Error('This is an error');
        } catch (Error $e) {
            Logger::new()->write($e);
        }

        $log_file_content = file_get_contents($this->file_name);

        $this->assertRegExp("/This is an error/", $log_file_content);
    }

    /** @test */
    public function write_method_can_except_parse_error(): void
    {
        try {
            throw new ParseError('This is a parse error');
        } catch (Error $e) {
            Logger::new()->write($e);
        }

        $log_file_content = file_get_contents($this->file_name);

        $this->assertRegExp("/This is a parse error/", $log_file_content);
    }

    /** @test */
    public function write_method_can_except_type_error(): void
    {
        try {
            throw new TypeError('This is a type error');
        } catch (Error $e) {
            Logger::new()->write($e);
        }

        $log_file_content = file_get_contents($this->file_name);

        $this->assertRegExp("/This is a type error/", $log_file_content);
    }

    /** @test */
    public function write_method_can_except_boolean_true(): void
    {
        Logger::new()->write(true, 'info');
        $log_file_content = file_get_contents($this->file_name);
        $this->assertRegExp("/true/", $log_file_content);
    }

    /** @test */
    public function write_method_can_except_boolean_false(): void
    {
        Logger::new()->write(false, 'info');
        $log_file_content = file_get_contents($this->file_name);
        $this->assertRegExp("/false/", $log_file_content);
    }

    /** @test */
    public function write_method_can_except_null(): void
    {
        Logger::new()->write(null, 'info');
        $log_file_content = file_get_contents($this->file_name);
        $this->assertRegExp("/null/", $log_file_content);
    }
}