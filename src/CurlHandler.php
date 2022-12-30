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
     * @var array|null
     */
    private $json;

    /**
     * @var Text
     */
    private $text;

    /**
     * @var Option
     */
    private $option;

    /**
     * @var Curl
     */
    private $curl;

    /**
     * CurlHandler constructor.
     *
     * @param string $url
     * @param array|null $json
     * @param Text $text
     * @param Option $option
     * @param Curl $curl
     */
    public function __construct(string $url, ?array $json, Text $text, Option $option, Curl $curl)
    {
        $this->url = $url;
        $this->json = $json;
        $this->text = $text;
        $this->option = $option;
        $this->curl = $curl;
    }

    public function makeRequest(): void
    {
        $json = $this->json ? $this->createCustomJson() : $this->createDefaultJson();

        $this->curl->setHeader('Content-Type', 'application/json');
        $this->curl->post($this->url, $json, true);
    }

    /**
     * @return string[]
     */
    private function createDefaultJson(): array
    {
        return [
            'timestamp' => $this->text->getDateBlock(true),
            'message' => $this->text->getPreparedText(),
            'type' => $this->option->getErrorType(),
        ];
    }

    /**
     * @return string[]
     */
    private function createCustomJson(): array
    {
        $search = [JsonFieldValue::TIMESTAMP, JsonFieldValue::MESSAGE, JsonFieldValue::ERROR_TYPE];
        $replace = [$this->text->getDateBlock(true), $this->text->getPreparedText(), $this->option->getErrorType()];

        return str_replace($search, $replace, $this->json ?? []);
    }
}
