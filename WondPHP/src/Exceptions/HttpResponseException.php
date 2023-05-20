<?php
 
declare (strict_types = 1);

namespace WondPHP\Exceptions;

use WondPHP\Response;

/**
 * HTTP响应异常
 */
class HttpResponseException extends \RuntimeException
{
    /**
     * @var Response
     */
    protected $response;

    public function __construct(Response $response)
    {
        $this->response = $response;
    }

    public function getResponse()
    {
        return $this->response;
    }

}
