<?php

declare(strict_types=1);

namespace Serhii\Tests;

use Curl\Curl;
use PHPUnit\Framework\TestCase;
use Serhii\TinyLogger\CurlHandler;
use Serhii\TinyLogger\JsonFieldValue;
use Serhii\TinyLogger\Option;
use Serhii\TinyLogger\Text;

class CurlHandlerTest extends TestCase
{
    /** @test */
    public function makeRequest_method_makes_request_with_default_json(): void
    {
        $text = new Text('This is my error message');
        $option = new Option('error');
        $url = 'http://test-url.com';

        $expected_json = [
            'timestamp' => $text->getDateBlock(true),
            'message' => $text->getPreparedText(),
            'type' => $option->getErrorType(),
        ];

        $curl = $this->createMock(Curl::class);
        $curl->expects($this->once())->method('post')->with($url, $expected_json, true);

        $curl_handler = $this->getMockBuilder(CurlHandler::class)
            ->setMethodsExcept(['makeRequest'])
            ->setConstructorArgs([$url, null, $text, $option])
            ->getMock();

        $curl_handler->expects($this->once())->method('getCurl')->willReturn($curl);
        $curl_handler->makeRequest();
    }

    /** @test */
    public function makeRequest_method_makes_request_with_custom_json(): void
    {
        $text = new Text('This is my error message');
        $option = new Option('error');
        $url = 'http://test-url.com';

        $input_json = [
            'time' => JsonFieldValue::TIMESTAMP,
            'errorMessage' => 'Error: ' . JsonFieldValue::MESSAGE . '!!!',
            'errorType' => JsonFieldValue::ERROR_TYPE,
            'token' => 12345,
        ];

        $expected_json = [
            'time' => $text->getDateBlock(true),
            'errorMessage' => "Error: {$text->getPreparedText()}!!!",
            'errorType' => $option->getErrorType(),
            'token' => 12345,
        ];

        $curl = $this->createMock(Curl::class);
        $curl->expects($this->once())->method('post')->with($url, $expected_json, true);

        $curl_handler = $this->getMockBuilder(CurlHandler::class)
            ->setMethodsExcept(['makeRequest'])
            ->setConstructorArgs([$url, $input_json, $text, $option])
            ->getMock();

        $curl_handler->expects($this->once())->method('getCurl')->willReturn($curl);
        $curl_handler->makeRequest();
    }
}
