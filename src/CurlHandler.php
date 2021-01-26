<?php

declare(strict_types=1);

namespace Serhii\TinyLogger;

use Curl\Curl;

final class CurlHandler
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

    public function __construct(string $url, ?array $json, Text $text)
    {
        $this->url = $url;
        $this->json = $json;
        $this->text = $text;
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
            'timestamp' => time(),
            'message' => $this->text->getPreparedText(),
            'type' => $this->text->getPreparedText(),
        ];
    }

    public function getCurl(): Curl
    {
        return new Curl();
    }
}
