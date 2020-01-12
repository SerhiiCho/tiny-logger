<?php declare(strict_types=1);

namespace Serhii\Tests;

use PHPUnit\Framework\TestCase;
use Serhii\TinyLogger\Logger;

class OptionTest extends TestCase
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
    public function pos_option_adds_file_path_namespace_and_line_number(): void
    {
        Logger::new()->write('Nice text is here', 'pos|debug');
        $line_number = __LINE__ - 1;

        $log_file_content = file_get_contents($this->file_name);

        $regex = sprintf('!>>> %s on line: %d!', __FILE__, $line_number);
        $this->assertRegExp("$regex", $log_file_content);
    }
}