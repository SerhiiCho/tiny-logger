<?php

declare(strict_types=1);

namespace Serhii\TinyLogger;

use Curl\Curl;

class CurlHandler
{
    /**
     * @var string
     */
    private $url;

    /**
     * @var mixed[]|null
     */
    private $json;

    /**
     * @var \Serhii\TinyLogger\Text
     */
    private $text;

    /**
     * @var \Serhii\TinyLogger\Option
     */
    private $option;

    public function __construct(string $url, ?array $json, Text $text, Option $option)
    {
        $this->url = $url;
        $this->json = $json;
        $this->text = $text;
        $this->option = $option;
    }

    public function makeRequest(): void
    {
        $curl = $this->getCurl();
        $json = $this->json ? $this->createCustomJson() : $this->createDefaultJson();

        $curl->setHeader('Content-Type', 'application/json');
        $curl->post($this->url, $json, true);
    }

    public function getCurl(): Curl
    {
        return new Curl();
    }

    private function createDefaultJson(): array
    {
        return [
            'timestamp' => $this->text->getDateBlock(true),
            'message' => $this->text->getPreparedText(),
            'type' => $this->option->getErrorType(),
        ];
    }

    private function createCustomJson(): array
    {
        $search = [JsonFieldValue::TIMESTAMP, JsonFieldValue::MESSAGE, JsonFieldValue::ERROR_TYPE];
        $replace = [$this->text->getDateBlock(true), $this->text->getPreparedText(), $this->option->getErrorType()];
        return str_replace($search, $replace, $this->json);
    }
}
