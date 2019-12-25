<?php declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\TestCase;
use Serhii\TinyLogger\Logger;

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
    public function write_method_writes_given_text_to_a_log_file(): void
    {
        Logger::new()->write('Nice text is here', 'info');
        $log_file_content = file_get_contents($this->file_name);
        $this->assertTrue(boolval(preg_match("/Nice text is here/", $log_file_content)));
    }

    /** @test */
    public function write_method_can_except_array(): void
    {
        $array = ['hello' => 'world'];
        Logger::new()->write($array, 'info');

        $log_file_content = file_get_contents($this->file_name);

        $json = json_encode($array, JSON_PRETTY_PRINT);
        $this->assertTrue(boolval(preg_match("/$json/", $log_file_content)));
    }

    /** @test */
    public function write_method_can_except_object(): void
    {
        $obj = (object) ['hello' => 'world'];
        Logger::new()->write($obj, 'info');

        $log_file_content = file_get_contents($this->file_name);

        $json = json_encode($obj, JSON_PRETTY_PRINT);
        $this->assertTrue(!! preg_match("/$json/", $log_file_content));
    }

    /** @test */
    public function function_creates_log_file(): void
    {
        $this->assertFileNotExists($this->file_name);
        tiny_log('Some log message goes here');
        $this->assertFileExists($this->file_name);
    }
}