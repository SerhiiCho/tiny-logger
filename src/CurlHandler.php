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

        $curl->setHeader('Content-Type', 'application/json');
        $curl->post($this->url, $this->json ?? $this->createDefaultJson(), true);
    }

    private function createDefaultJson(): array
    {
        return [
            'timestamp' => $this->text->getTimestamp(),
            'message' => $this->text->getPreparedText(),
            'type' => $this->option->getErrorType(),
        ];
    }

    public function getCurl(): Curl
    {
        return new Curl();
    }
}
