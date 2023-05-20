<?php
namespace WondPHP\Http;

use WondPHP\Http\UploadFile;
use Symfony\Component\HttpFoundation\AcceptHeader;
use Illuminate\Support\Str;
/**
 * Class Request
 *
 */
class Request extends \WondPHP\Http\Protocols\Request
{

    public const HEADER_FORWARDED = 0b000001; // When using RFC 7239
    public const HEADER_X_FORWARDED_FOR = 0b000010;
    public const HEADER_X_FORWARDED_HOST = 0b000100;
    public const HEADER_X_FORWARDED_PROTO = 0b001000;
    public const HEADER_X_FORWARDED_PORT = 0b010000;
    public const HEADER_X_FORWARDED_PREFIX = 0b100000;

    /** @deprecated since Symfony 5.2, use either "HEADER_X_FORWARDED_FOR | HEADER_X_FORWARDED_HOST | HEADER_X_FORWARDED_PORT | HEADER_X_FORWARDED_PROTO" or "HEADER_X_FORWARDED_AWS_ELB" or "HEADER_X_FORWARDED_TRAEFIK" constants instead. */
    public const HEADER_X_FORWARDED_ALL = 0b1011110; // All "X-Forwarded-*" headers sent by "usual" reverse proxy
    public const HEADER_X_FORWARDED_AWS_ELB = 0b0011010; // AWS ELB doesn't send X-Forwarded-Host
    public const HEADER_X_FORWARDED_TRAEFIK = 0b0111110; // All "X-Forwarded-*" headers sent by Traefik reverse proxy

    public const METHOD_HEAD = 'HEAD';
    public const METHOD_GET = 'GET';
    public const METHOD_POST = 'POST';
    public const METHOD_PUT = 'PUT';
    public const METHOD_PATCH = 'PATCH';
    public const METHOD_DELETE = 'DELETE';
    public const METHOD_PURGE = 'PURGE';
    public const METHOD_OPTIONS = 'OPTIONS';
    public const METHOD_TRACE = 'TRACE';
    public const METHOD_CONNECT = 'CONNECT';
    private static $trustedHeaderSet = -1;

    /**
     * @var string
     */
    public $app = null;

    /**
     * @var string
     */
    public $controller = null;

    /**
     * @var string
     */
    public $action = null;

    /**
     * @return mixed|null
     */
    public function all()
    {
        return $this->post() + $this->get();
    }

    /**
     * @param $name
     * @param null $default
     * @return null
     */
    public function input($name, $default = null)
    {
        $post = $this->post();
        if (isset($post[$name])) {
            return $post[$name];
        }
        $get = $this->get();
        return isset($get[$name]) ? $get[$name] : $default;
    }

    /**
     * @param array $keys
     * @return array
     */
    public function only(array $keys)
    {
        $all = $this->all();
        $result = [];
        foreach ($keys as $key) {
            if (isset($all[$key])) {
                $result[$key] = $all[$key];
            }
        }
        return $result;
    }

    /**
     * @param array $keys
     * @return mixed|null
     */
    public function except(array $keys)
    {
        $all = $this->all();
        foreach ($keys as $key) {
            unset($all[$key]);
        }
        return $all;
    }

    /**
     * @param null $name
     * @return null| array | UploadFile
     */
    public function file($name = null)
    {
        $files = $_FILES;//;parent::file($name);
        if (null === $files) {
            return $name === null ? [] : null;
        }
        if ($name !== null) {
            $files=array_values($files)[0] ?? '';
            if (!empty($files)) {
                return new UploadFile($files['tmp_name'], $files['name'], $files['type'], $files['error']);
            } else {
                return '';
            }
        }
        $upload_files = [];
        foreach ($files as $name => $file) {
            $upload_files[$name] = new UploadFile($file['tmp_name'], $file['name'], $file['type'], $file['error']);
        }
        return $upload_files;
    }

    /**
     * @return string
     */
    public function getRemoteIp()
    {
        return $_SERVER['REMOTE_ADDR'];
    }

    /**
     * @return int
     */
    public function getRemotePort()
    {
        return $_SERVER['REMOTE_PORT']	;
    }

    /**
     * @return string
     */
    public function getLocalIp()
    {
        return gethostbyname($_SERVER["SERVER_NAME"]);
    }

    /**
     * @return int
     */
    public function getLocalPort()
    {
        return $_SERVER['SERVER_PORT'];
    }

    /**
     * @param bool $safe_mode
     * @return string
     */
    public function getRealIp($safe_mode = true)
    {
        $remote_ip = $this->getRemoteIp();
        if ($safe_mode && !static::isIntranetIp($remote_ip)) {
            return $remote_ip;
        }
        return $this->header('client-ip', $this->header(
            'x-forwarded-for',
            $this->header('x-real-ip', $this->header(
                'x-client-ip',
                $this->header('via', $remote_ip)
            ))
        ));
    }

    /**
     * @return string
     */
    public function url()
    {
        return '//' . $this->host() . $this->path();
    }

    /**
     * @return string
     */
    public function fullUrl()
    {
        return '//' . $this->host() . $this->uri();
    }
     /**
     * Determine if the current request URL and query string matches a pattern.
     *
     * @param  mixed  ...$patterns
     * @return bool
     */
    public function fullUrlIs(...$patterns)
    {
        $url = $this->fullUrl();

        foreach ($patterns as $pattern) {

            if (Str::is($pattern, $url)) {
                return true;
            }
        }

        return false;
    }


    /**
     * Determine if the current request URI matches a pattern.
     *
     * @param  mixed  ...$patterns
     * @return bool
     */
    public function is(...$patterns)
    {
        $path = $this->decodedPath();
        foreach ($patterns as $pattern) {
        $path=    ltrim ($path,'/');
            if (Str::is($pattern, $path)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get the current decoded path info for the request.
     *
     * @return string
     */
    public function decodedPath()
    {
        return rawurldecode($this->path());
    }


    /**
     * @return bool
     */
    public function isAjax()
    {
        return $this->header('X-Requested-With') === 'XMLHttpRequest';
    }

     /**
     * Determine if the request is the result of an AJAX call.
     *
     * @return bool
     */
    public function ajax()
    {
        return $this->isXmlHttpRequest();
    }

     /**
     * Returns true if the request is an XMLHttpRequest.
     *
     * It works if your JavaScript library sets an X-Requested-With HTTP header.
     * It is known to work with common JavaScript frameworks:
     *
     * @see https://wikipedia.org/wiki/List_of_Ajax_frameworks#JavaScript
     *
     * @return bool true if the request is an XMLHttpRequest, false otherwise
     */
    public function isXmlHttpRequest()
    {
        return 'XMLHttpRequest' == $this->header('X-Requested-With');
    }

    /**
     * Determine if the request is the result of a prefetch call.
     *
     * @return bool
     */
    public function prefetch()
    {
        return strcasecmp($_SERVER['HTTP_X_MOZ'] ?? '', 'prefetch') === 0 ||
               strcasecmp($this->header('Purpose') ?? '', 'prefetch') === 0;
    }

    /**
     * @return bool
     */
    public function isPjax()
    {
        return (bool)$this->header('X-PJAX');
    }

    /**
     * @return bool
     */
    public function expectsJson()
    {
        return ($this->isAjax() && ! $this->isPjax() && $this->acceptsAnyContentType()) || $this->wantsJson(); 

    }


    /**
     * Determine if the current request accepts any content type.
     *
     * @return bool
     */
    public function acceptsAnyContentType()
    {
        $acceptable = $this->getAcceptableContentTypes();

        return \count($acceptable) === 0 || (
            isset($acceptable[0]) && ($acceptable[0] === '*/*' || $acceptable[0] === '*')
        );
    }

    public function getAcceptableContentTypes()
    {
        $accept =$this->header('accept') ?? [];
        $accepts =  array_keys(AcceptHeader::fromString($accept)->all());
        return $accepts;
    }

    /**
     * Determine if the current request is asking for JSON.
     *
     * @return bool
     */
    public function wantsJson()
    {
        $acceptable = $this->getAcceptableContentTypes();

        return isset($acceptable[0]) &&  (strstr($acceptable[0], '/json') ||  strstr($acceptable[0], '+json'));
    }

    public function isJson()
    {
        return Str::contains($this->header('content-type'), ['/json', '+json']);
    }

    public function isJson2()
    {
        return $this->hasHeader('Content-Type') &&
               Str::contains($this->header('Content-Type')[0], 'json');
    }
     


    /**
     * @return bool
     */
    public function acceptJson()
    {
        return $this->accepts('application/json');

    }

    public function controller()
    {
        return $this->controller;//$GLOBALS['classname'];
    }

    public function action()
    {
        return $this->action;
        // return $GLOBALS['actionname'];
    }
    /**
     * Determines whether the current requests accepts a given content type.
     *
     * @param  string|array  $contentTypes
     * @return bool
     */
    public function accepts($contentTypes)
    {
        $accepts = $this->getAcceptableContentTypes();

        if (\count($accepts) === 0) {
            return true;
        }

        $types = (array) $contentTypes;

        foreach ($accepts as $accept) {
            if ($accept === '*/*' || $accept === '*') {
                return true;
            }

            foreach ($types as $type) {
                if ($this->matchesType($accept, $type) || $accept === strtok($type, '/').'/*') {
                    return true;
                }
            }
        }

        return false;
    }


    /**
     * Determine if the given content types match.
     *
     * @param  string  $actual
     * @param  string  $type
     * @return bool
     */
    public static function matchesType($actual, $type)
    {
        if ($actual === $type) {
            return true;
        }

        $split = explode('/', $actual);

        return isset($split[1]) && preg_match('#'.preg_quote($split[0], '#').'/.+\+'.preg_quote($split[1], '#').'#', $type);
    }

    /**
     * @param string $ip
     * @return bool
     */
    public static function isIntranetIp($ip)
    {
        $reserved_ips = [
            '167772160'  => 184549375,  /*    10.0.0.0 -  10.255.255.255 */
            '3232235520' => 3232301055, /* 192.168.0.0 - 192.168.255.255 */
            '2130706432' => 2147483647, /*   127.0.0.0 - 127.255.255.255 */
            '2886729728' => 2887778303, /*  172.16.0.0 -  172.31.255.255 */
        ];

        $ip_long = ip2long($ip);

        foreach ($reserved_ips as $ip_start => $ip_end) {
            if (($ip_long >= $ip_start) && ($ip_long <= $ip_end)) {
                return true;
            }
        }
        return false;
    }


    /**
     * Retrieve an old input item.
     *
     * @param  string|null  $key
     * @param  string|array|null  $default
     * @return string|array
     */
    public function old($key = null, $default = null)
    {
        return $this->session()->getOldInput($key, $default) ?? $default;
        // return $this->hasSession() ? $this->session()->getOldInput($key, $default) : $default;
    }

    /**
     * Flash the input for the current request to the session.
     *
     * @return void
     */
    public function flash()
    {
        $this->session()->flashInput($this->all());
    }

    /**
     * Flash only some of the input to the session.
     *
     * @param  array|mixed  $keys
     * @return void
     */
    public function flashOnly($keys)
    {
        $this->session()->flashInput(
            $this->only(is_array($keys) ? $keys : func_get_args())
        );
    }

    /**
     * Flash only some of the input to the session.
     *
     * @param  array|mixed  $keys
     * @return void
     */
    public function flashExcept($keys)
    {
        $this->session()->flashInput(
            $this->except(is_array($keys) ? $keys : func_get_args())
        );
    }

    /**
     * Flush all of the old input from the session.
     *
     * @return void
     */
    public function flush()
    {
        $this->session()->flashInput([]);
    }

    public function hasSession()
    {
        return null !== $this->session;
    }

    public function isMethodCacheable()
    {
        return \in_array($this->getMethod(), ['GET', 'HEAD']);
    }

    /**
     * Gets the request "intended" method.
     *
     * If the X-HTTP-Method-Override header is set, and if the method is a POST,
     * then it is used to determine the "real" intended HTTP method.
     *
     * The _method request parameter can also be used to determine the HTTP method,
     * but only if enableHttpMethodParameterOverride() has been called.
     *
     * The method is always an uppercased string.
     *
     * @return string The request method
     *
     * @see getRealMethod()
     */
    public function getMethod()
    {
        if (null !== $this->method) {
            return $this->method;
        }

        $this->method = strtoupper($_SERVER['REQUEST_METHOD'] ??  'GET');

        if ('POST' !== $this->method) {
            return $this->method;
        }

        $method = $this->headers->get('X-HTTP-METHOD-OVERRIDE');

        if (!$method && self::$httpMethodParameterOverride) {
            $method = $this->request->get('_method', $this->query->get('_method', 'POST'));
        }

        if (!\is_string($method)) {
            return $this->method;
        }

        $method = strtoupper($method);

        if (\in_array($method, ['GET', 'HEAD', 'POST', 'PUT', 'DELETE', 'CONNECT', 'OPTIONS', 'PATCH', 'PURGE', 'TRACE'], true)) {
            return $this->method = $method;
        }

        if (!preg_match('/^[A-Z]++$/D', $method)) {
            throw new SuspiciousOperationException(sprintf('Invalid method override "%s".', $method));
        }

        return $this->method = $method;
    }


    /**
     * Gets the Etags.
     *
     * @return array The entity tags
     */
    public function getETags()
    {
        return preg_split('/\s*,\s*/', $this->header('if_none_match', ''), -1, \PREG_SPLIT_NO_EMPTY);
    }

      /**
     * Sets a list of trusted proxies.
     *
     * You should only list the reverse proxies that you manage directly.
     *
     * @param array $proxies          A list of trusted proxies, the string 'REMOTE_ADDR' will be replaced with $_SERVER['REMOTE_ADDR']
     * @param int   $trustedHeaderSet A bit field of Request::HEADER_*, to set which headers to trust from your proxies
     */
    public static function setTrustedProxies(array $proxies, int $trustedHeaderSet)
    {
        if (self::HEADER_X_FORWARDED_ALL === $trustedHeaderSet) {
            trigger_deprecation('symfony/http-foundation', '5.2', 'The "HEADER_X_FORWARDED_ALL" constant is deprecated, use either "HEADER_X_FORWARDED_FOR | HEADER_X_FORWARDED_HOST | HEADER_X_FORWARDED_PORT | HEADER_X_FORWARDED_PROTO" or "HEADER_X_FORWARDED_AWS_ELB" or "HEADER_X_FORWARDED_TRAEFIK" constants instead.');
        }
        self::$trustedProxies = array_reduce($proxies, function ($proxies, $proxy) {
            if ('REMOTE_ADDR' !== $proxy) {
                $proxies[] = $proxy;
            } elseif (isset($_SERVER['REMOTE_ADDR'])) {
                $proxies[] = $_SERVER['REMOTE_ADDR'];
            }

            return $proxies;
        }, []);
        self::$trustedHeaderSet = $trustedHeaderSet;
    }

}
