<?php
 
declare (strict_types = 1);

namespace WondPHP\Exceptions;

use Exception;

/**
 * HTTP异常
 */
class HttpException extends \RuntimeException
{
    private $statusCode;
    private $headers;

    public function __construct(int $statusCode, string $message = '', \Throwable $previous = null, array $headers = [], $code = 0)
    {
        $this->statusCode = $statusCode;
        $this->headers    = $headers;
        http_response_code($statusCode);

        parent::__construct($message, $code, $previous);
    }

    public function getStatusCode()
    {
        return $this->statusCode;
    }

    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * Set response headers.
     *
     * @param array $headers Response headers
     */
    public function setHeaders(array $headers)
    {
        $this->headers = $headers;
    }
}
