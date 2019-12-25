<?php declare(strict_types=1);

use Serhii\TinyLogger\Logger;

if (!function_exists('tiny_log')) {

    /**
     * @param $message
     * @param string|null $log_type
     * @param string|null $file_path
     */
    function tiny_log($message, ?string $log_type = 'error', ?string $file_path = null): void
    {
        $logger = Logger::new();

        if ($file_path) {
            $logger->setPath($file_path);
        }

        $logger->write($message, $log_type);
    }

}