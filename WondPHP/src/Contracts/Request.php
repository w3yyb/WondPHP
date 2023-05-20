<?php
namespace WondPHP\Contracts;

interface Request
{
    
    public function all();


    public function input($name, $default = null);
    public function only(array $keys);
    public function except(array $keys);
    public function file($name = null);
    public function getRemoteIp();
    public function getRemotePort();
    public function getLocalIp();
    public function getLocalPort();
    public function getRealIp($safe_mode = true);
    public function url();
    public function fullUrl();
    public function isAjax();
    public function isPjax();
    public function expectsJson();
    // public function acceptsAnyContentType();
    // public function getAcceptableContentTypes();
    // public function wantsJson();
    // public function acceptJson();
    public function controller();
    public function action();
    // public function accepts($contentTypes);
    // public static function matchesType($actual, $type);
    // public static function isIntranetIp($ip);

    // public function modify($type='get', $name=null, $value=null);
    public function get($name = null, $default = null);
    public function post($name = null, $default = null);
    public function header($name = null, $default = null);
    public function cookie($name = null, $default = null);
    public function method();
    public function protocolVersion();
    public function host($without_port = false);
    public function uri();
    public function path();
    public function queryString();
    // public function session();
    public function sessionId();
    // public function rawHead();
    public function rawBody();
    // public function rawBuffer();
    // public static function enableCache($value);
    // public function parseHeadFirstLine();
    // public function parseProtocolVersion();
    // public function parseHeaders();
    // public function parseGet();
    // public function parsePost();
    // public function parseUploadFiles($http_post_boundary);
    // public static function createSessionId();
    // public function isSecure();
    // public function isFromTrustedProxy();//todo
    // public function __set($name, $value);
    // public function __get($name);
    // public function __isset($name);
    // public function __unset($name);
    // public function __toString();



















}