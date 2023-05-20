<?php
namespace WondPHP\Http\Protocols;

use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use WondPHP\Request;

/**
 * Class Response
 *
 */
class Response
{
    use \Illuminate\Support\Traits\Macroable;//如果你想要定义一个自定义的可以在多个路由和控制器中复用的响应，可以使用 Response 门面上的 macro 方法

    /**
     * Header data.
     *
     * @var array
     */
    protected $_header = null;

    /**
     * Http status.
     *
     * @var int
     */
    protected $_status = null;

    /**
     * Http reason.
     *
     * @var string
     */
    protected $_reason = null;

    /**
     * Http version.
     *
     * @var string
     */
    protected $_version = '1.1';

    /**
     * Http body.
     *
     * @var string
     */
    protected $_body = null;

    /**
     * Mine type map.
     * @var array
     */
    protected static $_mimeTypeMap = null;

    /**
     * Phrases.
     *
     * @var array
     */
    protected static $_phrases = array(
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing',
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-status',
        208 => 'Already Reported',
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        306 => 'Switch Proxy',
        307 => 'Temporary Redirect',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Time-out',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Large',
        415 => 'Unsupported Media Type',
        416 => 'Requested range not satisfiable',
        417 => 'Expectation Failed',
        418 => 'I\'m a teapot',
        422 => 'Unprocessable Entity',
        423 => 'Locked',
        424 => 'Failed Dependency',
        425 => 'Unordered Collection',
        426 => 'Upgrade Required',
        428 => 'Precondition Required',
        429 => 'Too Many Requests',
        431 => 'Request Header Fields Too Large',
        451 => 'Unavailable For Legal Reasons',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Time-out',
        505 => 'HTTP Version not supported',
        506 => 'Variant Also Negotiates',
        507 => 'Insufficient Storage',
        508 => 'Loop Detected',
        511 => 'Network Authentication Required',
    );

    /**
     * Init.
     *
     * @return void
     */
    public static function init()
    {
        static::initMimeTypeMap();
    }

    /**
     * Response constructor.
     *
     * @param int $status
     * @param array $headers
     * @param string $body
     */
    public function __construct(
        $status = 200,
        $headers = array(),
        $body = ''
    ) {
        if (is_numeric($body)) {
            $body= (string) $body;
        }
        if (\is_object($body)) {
            $body= (array) $body;
        }
        if (\is_bool($body) || \is_null($body)) {
            $body= '';
        }
        if ($status !==http_response_code()) {
            $this->_status = http_response_code();
        }else{
            $this->_status = $status;

        }
        $this->_header = $headers;
        $this->_body = $body;
        if (!\is_string($body)) {
            // return $this->toResponse(Request::getInstance(), $body);
            return $this->toResponse(app('request'), $body);
        }
    }


    //修改响应
    public function modify($type='header', $name=null, $value=null)
    {
        if ($type==='header') {
            $this->_header[$name]=$value;
        }
        if ($type==='status') {
            $this->_status[$name]=$value;
        }
        if ($type==='body') {
            $this->_body[$name]=$value;
        }
        return $this;
    }
    /**
     * Set header.
     *
     * @param $name
     * @param $value
     * @return $this
     */
    public function header($name, $value)
    {
        if (empty($this->_header)) {
            $this->_header=[];
        }
        if ($name ==='Cache-Control') {
            $this->_header[$name][] =    $value;

        }else{
        $this->_header[$name] = $value;

        }
        return $this;
    }

    /**
     * Set header.
     *
     * @param $name
     * @param $value
     * @return Response
     */
    public function withHeader($name, $value)
    {
        return $this->header($name, $value);
    }

    /**
     * Set headers.
     *
     * @param $headers
     * @return $this
     */
    public function withHeaders($headers)
    {
        $this->_header = \array_merge($this->_header, $headers);
        return $this;
    }
    
    /**
     * Remove header.
     *
     * @param $name
     * @return $this
     */
    public function withoutHeader($name)
    {
        unset($this->_header[$name]);
        return $this;
    }

    /**
     * Get header.
     *
     * @param $name
     * @return null|array|string
     */
    public function getHeader($name)
    {
        if (!isset($this->_header[$name])) {
            return null;
        }
        return $this->_header[$name];
    }

    /**
     * Get headers.
     *
     * @return array
     */
    public function getHeaders()
    {
        return $this->_header;
    }

     /**
     * Set the exception to attach to the response.
     *
     * @param  \Throwable  $e
     * @return $this
     */
    public function withException(\Throwable $e)
    {
        $this->exception = $e;

        return $this;
    }


    /**
     * Set status.
     *
     * @param $code
     * @param null $reason_phrase
     * @return $this
     */
    public function withStatus($code, $reason_phrase = null)
    {
        $this->_status = $code;
        $this->_reason = $reason_phrase;
        return $this;
    }

    public function getStatusCode()
    {
        return $this->_status;
    }
    /**
     * Set protocol version.
     *
     * @param $version
     * @return $this
     */
    public function withProtocolVersion($version)
    {
        $this->_version = $version;
        return $this;
    }

    /**
     * Set http body.
     *
     * @param $body
     * @return $this
     */
    public function withBody($body)
    {
        $this->_body = $body;
        return $this;
    }

    /**
     * Get http raw body.
     */
    public function rawBody()
    {
        $body =ob_get_contents();
        return $body. $this->_body;
    }


    /**
         * Sends content for the current web response.
         *
         * @return $this
         */
    public function sendContent()
    {
        echo $this->_body;

        return $this;
    }

    /**
     * Sends HTTP headers and content.
     *
     * @return $this
     */
    public function send()
    {

        $this->sendHeaders();
        $this->sendContent();
        if (\function_exists('fastcgi_finish_request')) {
            fastcgi_finish_request();
        } elseif (!\in_array(\PHP_SAPI, ['cli', 'phpdbg'], true)) {
            static::closeOutputBuffers(0, true);
        }

        return $this;
    }

    /**
     * Sends HTTP headers.
     *
     * @return $this
     */
    public function sendHeaders()
    {
        // headers have already been sent by the developer
        if (headers_sent()) {
            return $this;
        }
        // headers
        // foreach ($this->_header->allPreserveCaseWithoutCookies() as $name => $values) {
        foreach ($this->_header  as $name => $values) {
            $replace = 0 === strcasecmp($name, 'Content-Type');

            if ($name==='Set-Cookie') {
                continue;
            }
            if ($name ==='Cache-Control') {
                foreach ($values as $value) {
                header($name.': '.$value, $replace, $this->_status);
            }
            }elseif($name!=="Location"){ // }else{   yuan
                header($name.': '.$values, $replace, $this->_status);
            }
            // foreach ($values as $value) {
            //     header($name.': '.$value, $replace, $this->_status);
            // }
        }

        // // cookies
        if (isset($this->_header['Set-Cookie'])) {
            foreach ($this->_header['Set-Cookie'] as $cookie) {
                header('Set-Cookie: '.$cookie, false, $this->_status);
            }
        }

        // // status
        $reason = $this->_reason ? $this->_reason : static::$_phrases[$this->_status];

        header(sprintf('HTTP/%s %s %s', $this->_version, $this->_status, $reason), true, $this->_status);

        return $this;
    }


    /**
     * Send file.
     *
     * @param $file
     * @param int $offset
     * @param int $length
     * @return $this
     */
    public function withFile($file, $offset = 0, $length = 0)
    {
        if (!\is_file($file)) {
            return $this->withStatus(404)->withBody('<h3>404 Not Found</h3>');
        }
        $this->file = array('file' => $file, 'offset' => $offset, 'length' => $length);
        return $this;
    }

    /**
     * Set cookie.
     *
     * @param $name
     * @param string $value
     * @param int $maxage
     * @param string $path
     * @param string $domain
     * @param bool $secure
     * @param bool $http_only
     * @return $this
     */
    public function cookie($name, $value = '', $max_age = 0, $path = '', $domain = '', $secure = false, $http_only = false)
    {
        if (!headers_sent()) {
            setcookie($name, $value, $max_age +time(), $path, $domain, $secure, $http_only);
        }
        $this->_header['Set-Cookie'][] = $name . '=' . \rawurlencode($value)
            . (empty($domain) ? '' : '; Domain=' . $domain)
            . (empty($max_age) ? '' : '; Max-Age=' . $max_age)
            . (empty($path) ? '' : '; Path=' . $path)
            . (!$secure ? '' : '; Secure')
            . (!$http_only ? '' : '; HttpOnly');
        return $this;
    }

    /**
     * Create header for file.
     *
     * @param $file
     * @return string
     */
    protected function createHeadForFile($file_info)
    {
        $file = $file_info['file'];
        $reason = $this->_reason ? $this->_reason : static::$_phrases[$this->_status];
        $head = "HTTP/{$this->_version} {$this->_status} $reason\r\n";
        $headers = $this->_header;
        if (!isset($headers['Server'])) {
            $head .= "Server: WondPHP\r\n";
        }
        foreach ($headers as $name => $value) {
            if (\is_array($value)) {
                foreach ($value as $item) {
                    $head .= "$name: $item\r\n";
                }
                continue;
            }
            $head .= "$name: $value\r\n";
        }

        if (!isset($headers['Connection'])) {
            $head .= "Connection: keep-alive\r\n";
        }

        $file_info = \pathinfo($file);
        $extension = isset($file_info['extension']) ? $file_info['extension'] : '';
        $mime_type=mime_content_type($file);
        $base_name =  $this->down_name?? $file_info['basename'];// isset($file_info['basename']) ? $file_info['basename'] : 'unknown';
        if (!isset($headers['Content-Type'])) {
            if (isset(self::$_mimeTypeMap[$extension])) {
                // $head .= "Content-Type: " . self::$_mimeTypeMap[$extension] . "\r\n";
                $head .= "Content-Type: " . $mime_type . "\r\n";
                $head .= "Content-Length: " . filesize($file) . "\r\n";
            } else {
                $head .= "Content-Type: application/octet-stream\r\n";
            }
        }

        // if (!isset($headers['Content-Disposition']) && isset(self::$_mimeTypeMap[$extension])) {
        if (!isset($headers['Content-Disposition']) && empty($this->echofile)) {
            $head .= "Content-Disposition: attachment; filename=\"$base_name\"\r\n";
        }

        if (!isset($headers['Last-Modified'])) {
            if ($mtime = \filemtime($file)) {
                $head .= 'Last-Modified: '.\date('D, d M Y H:i:s', $mtime) . ' ' . \date_default_timezone_get() ."\r\n";
            }
        }

        $head_array=explode("\r\n", $head);

        foreach ($head_array as $key => $value) {
            header($value);
        }

        // header('Content-Description: File Transfer');
        // header('Content-Type: application/octet-stream');
        // header("Cache-Control: no-cache, must-revalidate");
        // header("Expires: 0");
        // header('Content-Disposition: attachment; filename="'.basename($base_name).'"');
        // header('Content-Length: ' . filesize($file));
        // header('Pragma: public');
        if (isset($this->echofile)) {
            echo file_get_contents($file);
        } else {
            readfile($file);
        }
        exit;

        return "{$head}\r\n";
    }

    /**
     * __toString.
     *
     * @return string
     */
    public function __toString()
    {
        if (isset($this->file)) {
            return $this->createHeadForFile($this->file);
        }

        $reason = $this->_reason ? $this->_reason : static::$_phrases[$this->_status];
        $body_len = \strlen($this->_body);
        if (empty($this->_header)) {
            // return "HTTP/{$this->_version} {$this->_status} $reason\r\nServer: WondPHP\r\nContent-Type: text/html;charset=utf-8\r\nContent-Length: $body_len\r\nConnection: keep-alive\r\n\r\n{$this->_body}";
        }

        $head = "HTTP/{$this->_version} {$this->_status} $reason\r\n";
        $headers = $this->_header;
        if (!isset($headers['Server'])) {
            $head .= "Server: WondPHP\r\n";
        }
        foreach ($headers as $name => $value) {
            if (\is_array($value)) {
                foreach ($value as $item) {
                    $head .= "$name: $item\r\n";
                }
                continue;
            }
            $head .= "$name: $value\r\n";
        }

        if (!isset($headers['Connection'])) {
            $head .= "Connection: keep-alive\r\n";
        }

        if (!isset($headers['Content-Type'])) {
            $head .= "Content-Type: text/html;charset=utf-8\r\n";
        } elseif ($headers['Content-Type'] === 'text/event-stream') {//lenix 这里 对么？ todo
            return $head . $this->_body;
        }

        if (!isset($headers['Transfer-Encoding'])) {
            if (!isset($headers['Set-Cookie'])) {
                // $head .= "Content-Length: $body_len\r\n\r\n";  //这里经常有问题，先去了
            }
        } else {
            return "$head\r\n".dechex($body_len)."\r\n{$this->_body}\r\n";
        }

        $head_array=explode("\r\n", $head);

        foreach ($head_array as $key => $value) {
            if (strstr($value, 'Set-Cookie')) {
            } else {
            }
        }
         
        // The whole http package
        return  $this->_body ??'';
    }

    /**
     * Init mime map.
     *
     * @return void
     */
    public static function initMimeTypeMap()
    {
        $mime_file = __DIR__ . '/mime.types';
        $items = \file($mime_file, \FILE_IGNORE_NEW_LINES | \FILE_SKIP_EMPTY_LINES);
        foreach ($items as $content) {
            if (\preg_match("/\s*(\S+)\s+(\S.+)/", $content, $match)) {
                $mime_type       = $match[1];
                $extension_var   = $match[2];
                $extension_array = \explode(' ', \substr($extension_var, 0, -1));
                foreach ($extension_array as $file_extension) {
                    static::$_mimeTypeMap[$file_extension] = $mime_type;
                }
            }
        }
    }

    /**
     * Cleans or flushes output buffers up to target level.
     *
     * Resulting level can be greater than target level if a non-removable buffer has been encountered.
     *
     * @final
     */
    public static function closeOutputBuffers(int $targetLevel, bool $flush): void
    {
        $status = ob_get_status(true);
        $level = \count($status);
        $flags = \PHP_OUTPUT_HANDLER_REMOVABLE | ($flush ? \PHP_OUTPUT_HANDLER_FLUSHABLE : \PHP_OUTPUT_HANDLER_CLEANABLE);

        while ($level-- > $targetLevel && ($s = $status[$level]) && (!isset($s['del']) ? !isset($s['flags']) || ($s['flags'] & $flags) === $flags : $s['del'])) {
            if ($flush) {
                ob_end_flush();
            } else {
                ob_end_clean();
            }
        }
    }

}
