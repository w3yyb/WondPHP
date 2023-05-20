<?php

namespace WondPHP\Http\Protocols;

use WondPHP\Http\Protocols\Session;
use WondPHP\Http\Protocols;

// use Symfony\Component\HttpFoundation\ParameterBag;
use WondPHP\Http\ParameterBag;
// use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

/**
 * Class Request
 *
 */
class Request //extends SymfonyRequest 此性能有问题
{
    /**
     * Connection.
     *
     * @var TcpConnection
     */
    public $connection = null;

    protected static $trustedProxies = [];
    /**
     * Session instance.
     *
     * @var Session
     */
    public $session = null;

    /**
     * Properties.
     *
     * @var array
     */
    public $properties = array();

    /**
     * Http buffer.
     *
     * @var string
     */
    protected $_buffer = null;

    /**
     * Request data.
     *
     * @var array
     */
    protected $_data = null;

    /**
     * Header cache.
     *
     * @var array
     */
    protected static $_headerCache = array();

    /**
     * Get cache.
     *
     * @var array
     */
    protected static $_getCache = array();

    /**
     * Post cache.
     *
     * @var array
     */
    protected static $_postCache = array();

    /**
     * Enable cache.
     *
     * @var bool
     */
    protected static $_enableCache = true;


    /**
     * Request constructor.
     *
     * @param $buffer
     */
    public function __construct($buffer='')
    {
        // parent::__construct();
        $buffer=get_http_raw();
        $this->_buffer = $buffer;
    }

    //修改请求内容
    public function modify($type='get', $name=null, $value=null)
    {
        $this->_data[$type][$name] =$value;
    }

    /**
     * $_GET.
     *
     * @param null $name
     * @param null $default
     * @return mixed|null
     */
    public function get($name = null, $default = null)
    {
        if (!isset($this->_data['get'])) {
            $this->parseGet();
        }
        if (null === $name) {
            return $this->_data['get'];
        }
        return isset($this->_data['get'][$name]) ? $this->_data['get'][$name] : $default;
    }

    /**
     * $_POST.
     *
     * @param $name
     * @param null $default
     * @return mixed|null
     */
    public function post($name = null, $default = null)
    {
        if (!isset($this->_data['post'])) {
            $this->parsePost();
            if(empty($this->_data['post']) && $_POST){
                $this->_data['post'] = $_POST;
            }
        }
        if (null === $name) {
            return $this->_data['post'];
        }
        return isset($this->_data['post'][$name]) ? $this->_data['post'][$name] : $default;
    }

    /**
     * Get header item by name.
     *
     * @param null $name
     * @param null $default
     * @return string|null
     */
    public function header($name = null, $default = null)
    {
        if (!isset($this->_data['headers'])) {
            $this->parseHeaders();
        }
        if (null === $name) {
            return $this->_data['headers'];
        }
        $name = \strtolower($name);
        return isset($this->_data['headers'][$name]) ? $this->_data['headers'][$name] : $default;
    }

    /**
     * Get cookie item by name.
     *
     * @param null $name
     * @param null $default
     * @return string|null
     */
    public function cookie($name = null, $default = null)
    {
        if (!isset($this->_data['cookie'])) {
            \parse_str(\str_replace('; ', '&', $this->header('cookie')), $this->_data['cookie']);
        }
        if ($name === null) {
            return $this->_data['cookie'];
        }
        return isset($this->_data['cookie'][$name]) ? $this->_data['cookie'][$name] : $default;
    }

    /**
     * Get upload files.
     *
     * @param null $name
     * @return array|null
     */
    public function file($name = null)
    {
        if (!isset($this->_data['files'])) {
            $this->parsePost();
        }
        if (null === $name) {
            return $this->_data['files'];
        }
        return isset($this->_data['files'][$name]) ? $this->_data['files'][$name] : null;
    }

    /**
     * Get method.
     *
     * @return string
     */
    public function method()
    {
        if (!isset($this->_data['method'])) {
            $this->parseHeadFirstLine();
        }
        return $this->_data['method'];
    }

    /**
     * Get http protocol version.
     *
     * @return string.
     */
    public function protocolVersion()
    {
        if (!isset($this->_data['protocolVersion'])) {
            $this->parseProtocolVersion();
        }
        return $this->_data['protocolVersion'];
    }

    /**
     * Get host.
     *
     * @param bool $without_port
     * @return string
     */
    public function host($without_port = false)
    {
        $host = $this->header('host');
        if ($without_port && $pos = \strpos($host, ':')) {
            return \substr($host, 0, $pos);
        }
        return $host;
    }

    /**
     * Get uri.
     *
     * @return mixed
     */
    public function uri()
    {
        if (!isset($this->_data['uri'])) {
            $this->parseHeadFirstLine();
        }
        return $this->_data['uri'];
    }

    /**
     * Get path.
     *
     * @return mixed
     */
    public function path()
    {
        if (!isset($this->_data['path'])) {
            $this->_data['path'] = \parse_url($this->uri(), PHP_URL_PATH);
        }
        return $this->_data['path'];
    }

    /**
     * Get query string.
     *
     * @return mixed
     */
    public function queryString()
    {
        if (!isset($this->_data['query_string'])) {
            $this->_data['query_string'] = \parse_url($this->uri(), PHP_URL_QUERY);
        }
        return $this->_data['query_string'];
    }

    /**
     * Get session.
     *
     * @return bool
     */
    public function session()
    {
        if ($this->session === null) {
            $session_id = $this->sessionId();
            if ($session_id === false) {
                return false;
            }
            $this->session = new Session($session_id);
        }
        return $this->session;
    }

    /**
     * Get session id.
     *
     * @return bool|mixed
     */
    public function sessionId()
    {
        if (!isset($this->_data['sid'])) {
            $session_name = Http::sessionName();
            $sid = $this->cookie($session_name);
            if ($sid === '' || $sid === null) {
                if (0) {
                    echo('Request->session() fail, header already send');
                    return false;
                }
                $sid = static::createSessionId();
                $cookie_params = \session_get_cookie_params();
                $lifetime =config('session.lifetime') ??$cookie_params['lifetime'] ;

                $header= array($session_name . '=' . $sid
                    . (empty($cookie_params['domain']) ? '' : '; Domain=' . $cookie_params['domain'])
                    . (empty($lifetime) ? '' : '; Max-Age=' . ($lifetime *60))
                    . (empty($cookie_params['path']) ? '' : '; Path=' . $cookie_params['path'])
                    . (empty($cookie_params['samesite']) ? '' : '; SameSite=' . $cookie_params['samesite'])
                    . (!$cookie_params['secure'] ? '' : '; Secure')
                    . (!$cookie_params['httponly'] ? '' : '; HttpOnly'));
                header("Set-Cookie: $header[0]");
            }
            $this->_data['sid'] = $sid;
        }
        return $this->_data['sid'];
    }

    /**
     * Get http raw head.
     *
     * @return string
     */
    public function rawHead()
    {
        if (!isset($this->_data['head'])) {
            $this->_data['head'] = \strstr($this->_buffer, "\r\n\r\n", true);
        }
        return $this->_data['head'];
    }

    /**
     * Get http raw body.
     *
     * @return string
     */
    public function rawBody()
    {
        return \substr($this->_buffer, \strpos($this->_buffer, "\r\n\r\n") + 4);
    }

    /**
     * Get raw buffer.
     *
     * @return string
     */
    public function rawBuffer()
    {
        return $this->_buffer;
    }

    /**
     * Enable or disable cache.
     *
     * @param $value
     */
    public static function enableCache($value)
    {
        static::$_enableCache = (bool)$value;
    }

    /**
     * Parse first line of http header buffer.
     *
     * @return void
     */
    protected function parseHeadFirstLine()
    {
        $first_line = \strstr($this->_buffer, "\r\n", true);
        $tmp = \explode(' ', $first_line, 3);
        $this->_data['method'] = $tmp[0];
        $this->_data['uri'] = isset($tmp[1]) ? $tmp[1] : '/';
    }

    /**
     * Parse protocol version.
     *
     * @return void
     */
    protected function parseProtocolVersion()
    {
        $first_line = \strstr($this->_buffer, "\r\n", true);
        $protoco_version = substr(\strstr($first_line, 'HTTP/'), 5);
        $this->_data['protocolVersion'] = $protoco_version ? $protoco_version : '1.0';
    }

    /**
     * Parse headers.
     *
     * @return void
     */
    protected function parseHeaders()
    {
        $this->_data['headers'] = array();
        $raw_head = $this->rawHead();
        $head_buffer = \substr($raw_head, \strpos($raw_head, "\r\n") + 2);
        $cacheable = static::$_enableCache && !isset($head_buffer[2048]);
        if ($cacheable && isset(static::$_headerCache[$head_buffer])) {
            $this->_data['headers'] = static::$_headerCache[$head_buffer];
            return;
        }
        $head_data = \explode("\r\n", $head_buffer);
        foreach ($head_data as $content) {
            if (false !== \strpos($content, ':')) {
                list($key, $value) = \explode(':', $content, 2);
                $this->_data['headers'][\strtolower($key)] = \ltrim($value);
            } else {
                $this->_data['headers'][\strtolower($content)] = '';
            }
        }
        if ($cacheable) {
            static::$_headerCache[$head_buffer] = $this->_data['headers'];
            if (\count(static::$_headerCache) > 128) {
                unset(static::$_headerCache[key(static::$_headerCache)]);
            }
        }
    }

    /**
     * Parse head.
     *
     * @return void
     */
    protected function parseGet()
    {
        $query_string = $this->queryString();
        $this->_data['get'] = array();
        if ($query_string === '') {
            return;
        }
        $cacheable = static::$_enableCache && !isset($query_string[1024]);
        if ($cacheable && isset(static::$_getCache[$query_string])) {
            $this->_data['get'] = static::$_getCache[$query_string];
            return;
        }
        \parse_str($query_string, $this->_data['get']);

        if ($cacheable) {
            static::$_getCache[$query_string] = $this->_data['get'];
            if (\count(static::$_getCache) > 256) {
                unset(static::$_getCache[key(static::$_getCache)]);
            }
        }
    }

    /**
     * Parse post.
     *
     * @return void
     */
    protected function parsePost()
    {

        $body_buffer = $this->rawBody();

        $this->_data['post'] = $this->_data['files'] = array();
        if ($body_buffer === '') {
            return;
        }
        $cacheable = static::$_enableCache && !isset($body_buffer[1024]);
        if ($cacheable && isset(static::$_postCache[$body_buffer])) {
            $this->_data['post'] = static::$_postCache[$body_buffer];
            return;
        }
        $content_type = $this->header('content-type', '');
        if (\preg_match('/boundary="?(\S+)"?/', $content_type, $match)) {
            $http_post_boundary = '--' . $match[1];
            $this->parseUploadFiles($http_post_boundary);
            return;
        }
        if (\preg_match('/\bjson\b/i', $content_type)) {
            $this->_data['post'] = (array) json_decode($body_buffer, true);
        } else {
            \parse_str($body_buffer, $this->_data['post']);
        }
        if ($cacheable) {
            static::$_postCache[$body_buffer] = $this->_data['post'];
            if (\count(static::$_postCache) > 256) {
                unset(static::$_postCache[key(static::$_postCache)]);
            }
        }
    }

    /**
     * Parse upload files.
     *
     * @param $http_post_boundary
     * @return void
     */
    protected function parseUploadFiles($http_post_boundary)
    {
        $http_body = $this->rawBody();
        $http_body = \substr($http_body, 0, \strlen($http_body) - (\strlen($http_post_boundary) + 4));
        $boundary_data_array = \explode($http_post_boundary . "\r\n", $http_body);
        if ($boundary_data_array[0] === '') {
            unset($boundary_data_array[0]);
        }
        $key = -1;
        $files = array();
        foreach ($boundary_data_array as $boundary_data_buffer) {
            list($boundary_header_buffer, $boundary_value) = \explode("\r\n\r\n", $boundary_data_buffer, 2);
            // Remove \r\n from the end of buffer.
            $boundary_value = \substr($boundary_value, 0, -2);
            $key++;
            foreach (\explode("\r\n", $boundary_header_buffer) as $item) {
                list($header_key, $header_value) = \explode(": ", $item);
                $header_key = \strtolower($header_key);
                switch ($header_key) {
                    case "content-disposition":
                        // Is file data.
                        if (\preg_match('/name="(.*?)"; filename="(.*?)"$/i', $header_value, $match)) {
                            $error = 0;
                            $tmp_file = '';
                            $size = \strlen($boundary_value);
                            $tmp_upload_dir = HTTP::uploadTmpDir();
                            if (!$tmp_upload_dir) {
                                $error = UPLOAD_ERR_NO_TMP_DIR;
                            } else {
                                $tmp_file = \tempnam($tmp_upload_dir, 'wondphp.upload.');
                                if ($tmp_file === false || false == \file_put_contents($tmp_file, $boundary_value)) {
                                    $error = UPLOAD_ERR_CANT_WRITE;
                                }
                            }
                            // Parse upload files.
                            $files[$key] = array(
                                'key' => $match[1],
                                'name' => $match[2],
                                'tmp_name' => $tmp_file,
                                'size' => $size,
                                'error' => $error
                            );
                            break;
                        } // Is post field.
                        else {
                            // Parse $_POST.
                            if (\preg_match('/name="(.*?)"$/', $header_value, $match)) {
                                $this->_data['post'][$match[1]] = $boundary_value;
                            }
                        }
                        break;
                    case "content-type":
                        // add file_type
                        $files[$key]['type'] = \trim($header_value);
                        break;
                }
            }
        }

        foreach ($files as $file) {
            $key = $file['key'];
            unset($file['key']);
            $this->_data['files'][$key] = $file;
        }
    }

    /**
     * Create session id.
     *
     * @return string
     */
    protected static function createSessionId()
    {
        return \bin2hex(\pack('d', \microtime(true)) . \pack('N', \mt_rand()));
    }


    /**
    * Checks whether the request is secure or not.
    *
    * This method can read the client protocol from the "X-Forwarded-Proto" header
    * when trusted proxies were set via "setTrustedProxies()".
    *
    * The "X-Forwarded-Proto" header must contain the protocol: "https" or "http".
    *
    * @return bool
    */
    public function isSecure()
    {
        // if ($this->isFromTrustedProxy() && $proto = $this->getTrustedValues(self::HEADER_X_FORWARDED_PROTO)) {
        //     return \in_array(strtolower($proto[0]), ['https', 'on', 'ssl', '1'], true);
        // }

        $https = $_SERVER['HTTPS'] ?? '';

        return !empty($https) && 'off' !== strtolower($https);
    }

    /**
     * Indicates whether this request originated from a trusted proxy.
     *
     * This can be useful to determine whether or not to trust the
     * contents of a proxy-specific header.
     *
     * @return bool true if the request came from a trusted proxy, false otherwise
     */
    public function isFromTrustedProxy()//todo
    {
        return self::$trustedProxies && IpUtils::checkIp($_SERVER['REMOTE_ADDR'], self::$trustedProxies);
    }
    /**
     * Setter.
     *
     * @param $name
     * @param $value
     * @return void
     */
    public function __set($name, $value)
    {
        $this->properties[$name] = $value;
    }

    /**
     * Getter.
     *
     * @param $name
     * @return mixed|null
     */
    public function __get($name)
    {
        return isset($this->properties[$name]) ? $this->properties[$name] : null;
    }

    /**
     * Isset.
     *
     * @param $name
     * @return bool
     */
    public function __isset($name)
    {
        return isset($this->properties[$name]);
    }

    /**
     * Unset.
     *
     * @param $name
     * @return void
     */
    public function __unset($name)
    {
        unset($this->properties[$name]);
    }

    /**
     * __destruct.
     *
     * @return void
     */
    public function __destruct()
    {
        if (isset($this->_data['files'])) {
            \clearstatcache();
            foreach ($this->_data['files'] as $item) {
                if (\is_file($item['tmp_name'])) {
                    \unlink($item['tmp_name']);
                }
            }
        }
    }

    public function __toString()
    {
        return $this->rawHead() ."\r\n" .$this->rawBody();
    }
}
